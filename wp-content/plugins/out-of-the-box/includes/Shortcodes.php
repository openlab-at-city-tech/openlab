<?php

namespace TheLion\OutoftheBox;

class Shortcodes
{
    /**
     *  @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;

    /**
     * Contains the location to the cache file.
     *
     * @var string
     */
    private $_cache_location;

    /**
     * Contains the file handle in case the plugin has to work
     * with a file for unlocking/locking.
     *
     * @var type
     */
    private $_cache_file_handle;

    /**
     * $_shortcodes contains all the cached shortcodes that are present
     * in the Cache File.
     *
     * @var array
     */
    private $_shortcodes = [];

    /**
     * Is set to true when a change has been made in the cache.
     * Forcing the plugin to save the cache when needed.
     *
     * @var bool
     */
    private $_updated = false;

    public function __construct(Processor $processor)
    {
        $this->_processor = $processor;

        $this->_cache_name = get_current_blog_id();
        if ($this->_processor->is_network_authorized()) {
            $this->_cache_name = 'network';
        }
        $this->_cache_name .= '.shortcodes';

        $this->_cache_location = OUTOFTHEBOX_CACHEDIR.$this->_cache_name;

        // Load Cache
        $this->load_cache();
    }

    public function __destruct()
    {
        $this->update_cache();
    }

    public function remove_shortcode($token)
    {
        if (isset($this->_shortcodes[$token])) {
            return false;
        }

        unset($this->_shortcodes[$token]);
        $this->set_updated();

        return true;
    }

    public function get_shortcode_by_id($token)
    {
        if (!isset($this->_shortcodes[$token])) {
            return false;
        }

        // Delete the removal flag when the shortcode as the shortcode is still in use
        if (isset($this->_shortcodes[$token]['remove'])) {
            unset($this->_shortcodes[$token]['remove']);
            $this->_shortcodes[$token]['expire'] = strtotime('+1 weeks');
            $this->set_updated();
        }

        return $this->_shortcodes[$token];
    }

    public function has_shortcodes()
    {
        return count($this->_shortcodes) > 0;
    }

    public function get_all_shortcodes()
    {
        return $this->_shortcodes;
    }

    public function set_shortcode($token, $shortcode)
    {
        $this->_shortcodes[$token] = $shortcode;
        $this->set_updated();

        return $this->_shortcodes[$token];
    }

    public function is_updated()
    {
        return $this->_updated;
    }

    public function set_updated($value = true)
    {
        $this->_updated = (bool) $value;

        return $this->_updated;
    }

    public function get_cache_name()
    {
        return $this->_cache_name;
    }

    public function get_cache_location()
    {
        return $this->_cache_location;
    }

    public function reset_cache()
    {
        $this->_nodes = [];
        $this->set_last_check_for_update();
        $this->set_last_check_token(null);
        $this->update_cache();
    }

    public function update_cache()
    {
        if ($this->is_updated()) {
            $saved = $this->_save_local_cache();
            $this->set_updated(false);
        }
    }

    public function load_cache()
    {
        $cache = $this->_read_local_cache('close');

        if (!empty($cache) && !is_array($cache)) {
            $this->_unserialize_cache($cache);
        }
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    protected function _set_cache_file_handle($handle)
    {
        return $this->_cache_file_handle = $handle;
    }

    protected function _get_cache_file_handle()
    {
        return $this->_cache_file_handle;
    }

    protected function _unlock_local_cache()
    {
        $handle = $this->_get_cache_file_handle();
        if (!empty($handle)) {
            flock($this->_get_cache_file_handle(), LOCK_UN);
            fclose($this->_get_cache_file_handle());
            $this->_set_cache_file_handle(null);
        }

        clearstatcache();

        return true;
    }

    protected function _read_local_cache($close = false)
    {
        $handle = $this->_get_cache_file_handle();
        if (empty($handle)) {
            $this->_create_local_lock(LOCK_SH);
        }

        clearstatcache();
        rewind($this->_get_cache_file_handle());

        $data = null;
        if (filesize($this->get_cache_location()) > 0) {
            $data = fread($this->_get_cache_file_handle(), filesize($this->get_cache_location()));
        }

        if (false !== $close) {
            $this->_unlock_local_cache();
        }

        return $data;
    }

    protected function _create_local_lock($type)
    {
        // Check if file exists
        $file = $this->get_cache_location();

        if (!file_exists($file)) {
            @file_put_contents($file, $this->_serialize_cache());

            if (!is_writable($file)) {
                error_log('[Out-of-the-Box message]: '.sprintf('Shortcode file (%s) is not writable', $file));
                die(sprintf('Shortcode file (%s) is not writable', $file));
            }
        }

        // Check if the file is more than 1 minute old.
        $requires_unlock = ((filemtime($file) + 60) < (time()));

        // Temporarily workaround when flock is disabled. Can cause problems when plugin is used in multiple processes
        if (false !== strpos(ini_get('disable_functions'), 'flock')) {
            $requires_unlock = false;
        }

        // Check if file is already opened and locked in this process
        $handle = $this->_get_cache_file_handle();
        if (empty($handle)) {
            $handle = fopen($file, 'c+');
            if (!is_resource($handle)) {
                error_log('[Out-of-the-Box message]: '.sprintf('Shortcode file (%s) is not writable', $file));
                die(sprintf('Shortcode file (%s) is not writable', $file));
            }
            $this->_set_cache_file_handle($handle);
        }

        @set_time_limit(60);

        if (!flock($this->_get_cache_file_handle(), $type)) {
            /*
             * If the file cannot be unlocked and the last time
             * it was modified was 1 minute, assume that
             * the previous process died and unlock the file manually
             */
            if ($requires_unlock) {
                $this->_unlock_local_cache();
                $handle = fopen($file, 'c+');
                $this->_set_cache_file_handle($handle);
            }
            // Try to lock the file again
            flock($this->_get_cache_file_handle(), LOCK_EX);
        }
        @set_time_limit(60);

        return true;
    }

    protected function _save_local_cache()
    {
        if (!$this->_create_local_lock(LOCK_EX)) {
            return false;
        }

        $data = $this->_serialize_cache($this);

        ftruncate($this->_get_cache_file_handle(), 0);
        rewind($this->_get_cache_file_handle());

        $result = fwrite($this->_get_cache_file_handle(), $data);

        $this->_unlock_local_cache();
        $this->set_updated(false);

        return true;
    }

    private function _serialize_cache()
    {
        $now = time();
        foreach ($this->_shortcodes as $token => $shortcode) {
            if (!isset($shortcode['expire']) || ($shortcode['expire']) < $now) {
                // Only delete the shortcode once it is marked to prevent issues with multiple shortcodes on the same page
                if (isset($shortcode['remove']) && true === $shortcode['remove']) {
                    unset($this->_shortcodes[$token]);
                } else {
                    $this->_shortcodes[$token]['remove'] = true;
                    $this->_shortcodes[$token]['expire'] = strtotime('+1 weeks');
                }
            }
        }

        $data = [
            '_shortcodes' => $this->_shortcodes,
        ];

        return serialize($data);
    }

    private function _unserialize_cache($data)
    {
        $values = unserialize($data);
        if (false !== $values) {
            foreach ($values as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}
