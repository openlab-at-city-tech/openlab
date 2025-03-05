<?php
/**
 * @package WP Pointer Tour
 * @author Joachim Jensen <jv@intox.dk>
 * @license GPLv3
 * @copyright 2016 by Joachim Jensen
 */

if (!defined('ABSPATH')) {
    die();
}

class WP_Pointer_Tour
{
    const VERSION = '1.0';

    /**
     * Key to hold flag for user
     * @var string
     */
    private $_meta_key;

    /**
     * Pointers settings
     * @var array
     */
    private $_pointers;

    /**
     * Script enqueue flag
     * @var boolean
     */
    private $_enqueued = false;

    public function __construct($meta_key, $pointers = [])
    {
        //todo: consider singleton pattern or refactor script localize

        $this->_meta_key = $meta_key;
        $this->_pointers = $pointers;

        add_action(
            'wp_loaded',
            [$this,'initiate_tour']
        );
    }

    /**
     * Set tour callbacks if user should see it
     *
     * @since  1.0
     * @return void
     */
    public function initiate_tour()
    {
        if (!$this->user_has_finished_tour()) {
            add_action(
                'admin_enqueue_scripts',
                [$this,'enqueue_scripts']
            );
            add_action(
                'wp_ajax_cas_finish_tour',
                [$this,'finish_tour']
            );
        }
    }

    /**
     * Add pointer to tour
     *
     * @since 1.0
     * @param array  $pointer
     */
    public function add_pointer($pointer)
    {
        $this->_pointers[] = $pointer;
    }

    /**
     * Remove pointer from index
     *
     * @since  1.0
     * @param  int  $i
     * @return void
     */
    public function remove_pointer($i)
    {
        unset($this->_pointers[$i]);
    }

    /**
     * Set new pointers
     *
     * @since 1.0
     * @param array  $pointers
     */
    public function set_pointers($pointers)
    {
        $this->_pointers = $pointers;
    }

    /**
     * Get user option for tour
     *
     * @since  1.0
     * @param  int  $user
     * @return int|boolean
     */
    public function get_user_option($user = null)
    {
        if (!$user) {
            $user = get_current_user_id();
        }
        return get_user_option($this->_meta_key, $user);
    }

    /**
     * Has user finished tour
     *
     * @since  1.0
     * @return boolean
     */
    public function user_has_finished_tour()
    {
        return get_user_option($this->_meta_key) !== false;
    }

    /**
     * Set finish flag for user
     *
     * @since  1.0
     * @return void
     */
    public function finish_tour()
    {
        //Verify nonce
        if (!check_ajax_referer($this->_meta_key, 'nonce', false)) {
            wp_send_json_error('');
        }

        $time = time();
        $updated = update_user_option(
            get_current_user_id(),
            $this->_meta_key,
            $time
        );

        if ($updated) {
            wp_send_json_success($time);
        }

        wp_send_json_error('');
    }

    /**
     * Load scripts and styles
     *
     * @since  1.0
     * @param  string  $hook
     * @return void
     */
    public function enqueue_scripts($hook = '')
    {
        //scripts can be enqueued manually
        //so do not do it twice
        if ($this->_pointers && !$this->_enqueued) {
            $this->_enqueued = true;
            wp_enqueue_script('cas/pointers', plugins_url('assets/js/pointers.js', __FILE__), ['wp-pointer'], self::VERSION, true);
            wp_enqueue_style('wp-pointer');

            wp_localize_script('cas/pointers', 'WP_PT', [
                'pointers' => $this->_pointers,
                'nonce'    => wp_create_nonce($this->_meta_key),
                'close'    => __('Close', 'content-aware-sidebars'),
                'prev'     => __('Previous', 'content-aware-sidebars'),
                'next'     => __('Next', 'content-aware-sidebars')
            ]);
        }
    }
}

//eol
