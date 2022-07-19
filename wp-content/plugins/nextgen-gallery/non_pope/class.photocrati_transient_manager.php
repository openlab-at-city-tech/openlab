<?php

class C_Photocrati_Transient_Manager
{
	private $_groups  = [];
	private $_tracker = [];

	/** @var null|C_Photocrati_Transient_Manager */
	private static $_instance = NULL;

	/**
	 * @return C_Photocrati_Transient_Manager
	 */
	static function get_instance()
	{
		if (!self::$_instance) {
			$klass = get_class();
			self::$_instance = new $klass;
		}
		return self::$_instance;
	}

	function __construct()
	{
		global $_wp_using_ext_object_cache;

		$this->_groups = get_option('ngg_transient_groups', array('__counter' => 1));
		if ($_wp_using_ext_object_cache)
		    $this->_tracker = get_option('photocrati_cache_tracker', array());

		// Ensure that _tracker remains an array or PHP 8 may generate fatal errors
		if (!is_array($this->_tracker))
		    $this->_tracker = [];

		if (!defined('NGG_DISABLE_PHOTOCRATI_CACHE_TRACKER') || !NGG_DISABLE_PHOTOCRATI_CACHE_TRACKER)
            register_shutdown_function(array(&$this, '_update_tracker'));
	}

	function delete_tracked($group=NULL)
	{
		global $_wp_using_ext_object_cache;
		if ($_wp_using_ext_object_cache) {
			if ($group) {
				if (is_array($this->_tracker) && isset($this->_tracker[$this->get_group_id($group)])) {
					foreach ($this->_tracker[$this->get_group_id($group)] as $key) {
						delete_transient($this->get_group_id($group).'__'.$key);
					}
					unset($this->_tracker[$this->get_group_id($group)]);
				}
			}
			else foreach($this->_groups as $group => $data) $this->delete_tracked($group);
		}
	}

	function _update_tracker()
	{
		global $_wp_using_ext_object_cache;
		if ($_wp_using_ext_object_cache)
		{
            $current_value = get_option('photocrati_cache_tracker', []);
            if ($current_value !== $this->_tracker)
                update_option('photocrati_cache_tracker', $this->_tracker, 'no');
		}
	}

	function add_group($group_or_groups)
	{
		$updated = FALSE;
		$groups = is_array($group_or_groups) ? $group_or_groups : array($group_or_groups);
		foreach ($groups as $group) {
			if (!isset($this->_groups[$group])) {
				$id = $this->_groups['__counter'] += 1;
				$this->_groups[$group] = array('id' => $id, 'enabled' => TRUE);
				$updated = TRUE;
			}
		}
		if ($updated) update_option('ngg_transient_groups', $this->_groups);

	}

	function get_group_id($group_name)
	{
		$this->add_group($group_name);

		return $this->_groups[$group_name]['id'];
	}

	function generate_key($group, $params=array())
	{
		if (is_object($params)) $params = (array) $params;
		if (is_array($params)) {
			foreach ($params as &$param) $param = @json_encode($param);
			$params = implode('', $params);
		}

		return $this->get_group_id($group).'__'.str_replace('-', '_', crc32($params));
	}

	function get($key, $default=NULL, $lookup=NULL)
	{
		$retval = $default;

		if (is_null($lookup)) {
			if (defined('PHOTOCRATI_CACHE')) {
				$lookup = PHOTOCRATI_CACHE;
			}
		}

		if ($lookup) {
			$retval = json_decode(get_transient($key));
			if (is_object($retval)) $retval = (array) $retval;
			if (is_null($retval)) $retval = $default;
		}

		return $retval;
	}

	function _track_key($key)
	{
		global $_wp_using_ext_object_cache;
		if ($_wp_using_ext_object_cache)
		{
		    if (!is_array($this->_tracker))
		        $this->_tracker = [];
			$parts = explode('__', $key);
			$group = $parts[0];
			$id = $parts[1];
			if (!isset($this->_tracker[$group]))
			    $this->_tracker[$group] = array();
			$this->_tracker[$group][] = $id;
		}
	}

    public function set($key, $value, $ttl=0)
	{
		$retval = FALSE;
		$enabled = TRUE;

		if (defined('PHOTOCRATI_CACHE'))
		    $enabled = PHOTOCRATI_CACHE;
		if (defined('PHOTOCRATI_CACHE_TTL')
            && !$ttl) $ttl = PHOTOCRATI_CACHE_TTL;

		if ($enabled)
		{
			$retval = set_transient($key, json_encode($value), $ttl);
			if ($retval)
			    $this->_track_key($key);
		}

		return $retval;
	}

    public function delete($key)
	{
		return delete_transient($key);
	}

    /**
     * Clears all (or only expired) transients managed by this utility
     *
     * @param string $group Group name to purge
     * @param bool $expired Whether to clear all transients (FALSE) or to clear expired transients (TRUE)
     */
    public function clear($group = NULL, $expired = FALSE)
	{
	    if ($group === '__counter')
	        return;

		if (is_string($group) && !empty($group))
        {
			global $wpdb;

			// A little query building is necessary here..
            // Clear transients for "the" site or for the current multisite instance
			$expired_sql = '';
            $params = array($wpdb->esc_like('_transient_') . '%',
                            '%' . $wpdb->esc_like("{$this->get_group_id($group)}__") . '%',
                            $wpdb->esc_like('_transient_timeout_') . '%');
			if ($expired)
            {
                $params[] = time();
                $expired_sql = $expired ? "AND b.option_value < %d" : '';
            }

            $sql = "DELETE a, b
                    FROM {$wpdb->options} a, {$wpdb->options} b
                    WHERE a.option_name LIKE %s
                    AND a.option_name LIKE %s
                    AND a.option_name NOT LIKE %s
                    AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
                    {$expired_sql}";

            $wpdb->query($wpdb->prepare($sql, $params));

            // Clear transients for the main site of a multisite network
            if (is_main_site() && is_main_network())
            {
                $expired_sql = '';
                $params = array($wpdb->esc_like('_site_transient_') . '%',
                                '%' . $wpdb->esc_like("{$this->get_group_id($group)}__") . '%',
                                $wpdb->esc_like('_site_transient_timeout_') . '%');
                if ($expired)
                {
                    $params[] = time();
                    $expired_sql = $expired ? "AND b.option_value < %d" : '';
                }
                $sql = "DELETE a, b
                        FROM {$wpdb->options} a, {$wpdb->options} b
                        WHERE a.option_name LIKE %s
                        AND a.option_name LIKE %s
                        AND a.option_name NOT LIKE %s
                        AND b.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(a.option_name, 17))
                        {$expired_sql}";
                $wpdb->query($wpdb->prepare($sql, $params));
            }

			if ($expired)
			    $this->delete_tracked($group);
		}
		else foreach ($this->_groups as $name => $params) {
			$this->clear($name, $expired);
		}
	}

    public static function update($key, $value, $ttl=NULL)
	{
		return self::get_instance()->set($key, $value, $ttl);
	}

    public static function fetch($key, $default=NULL)
	{
		return self::get_instance()->get($key, $default);
	}

	public static function flush($group = NULL)
	{
		self::get_instance()->clear($group);
	}

    public static function flush_expired($group = NULL)
    {
        self::get_instance()->clear($group, TRUE);
    }

	public static function create_key($group, $params=array())
	{
		return self::get_instance()->generate_key($group, $params);
	}
}