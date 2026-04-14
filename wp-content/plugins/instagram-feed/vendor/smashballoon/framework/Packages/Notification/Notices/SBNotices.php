<?php

/**
 * Notices class
 *
 * @package Notices
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Notification\Notices;

use InstagramFeed\Vendor\Smashballoon\Framework\Packages\Notification\Notices\AdminNotice;
use function InstagramFeed\Vendor\Smashballoon\Framework\sb_map_notice_hooks;
use function InstagramFeed\Vendor\Smashballoon\Framework\sb_get_plugin_type;
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Get all notices and display error warning or success notices
 */
class SBNotices
{
    /**
     * Notices
     *
     * @var array
     */
    private $notices = [];
    /**
     * Group notices
     *
     * @var array
     */
    private $group_notices = [];
    /**
     * Current screen.
     *
     * @var string
     */
    private $screen;
    /**
     * Plugin - Example: instagram_feed
     *
     * @var string
     */
    private $plugin;
    /**
     * Plugin slug name - Example: instagram-feed
     *
     * @var string
     */
    private $plugin_slug;
    /**
     * Plugin type - Example: free or pro
     *
     * @var string
     */
    private $plugin_type;
    /**
     * Notice options
     *
     * @var string
     */
    private $notice_option;
    private $group_notice_option;
    /**
     * The single instance of the class.
     *
     * @var SBNotices
     */
    protected static $instance = null;
    /**
     * Main SBNotices Instance.
     *
     * Ensures only one instance of SBNotices is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return SBNotices - Main instance.
     */
    public static function instance($plugin_slug = '')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($plugin_slug);
        }
        return self::$instance;
    }
    /**
     * Constructor
     */
    public function __construct($plugin_slug = '')
    {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_type = sb_get_plugin_type($this->plugin_slug);
        $plugin_slug = str_replace('-', '_', $plugin_slug);
        $this->plugin = str_replace('_pro', '', $plugin_slug);
        $this->notice_option = sanitize_key('sb_' . $this->plugin . '_notices');
        $this->group_notice_option = sanitize_key('sb_' . $this->plugin . '_group_notices');
        $this->notices = get_option($this->notice_option, []);
        $this->screen = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        $this->group_notices = get_option($this->group_notice_option, []);
        $notice_hook = sb_map_notice_hooks($this->plugin_slug);
        add_action('admin_notices', [$this, 'display_notices']);
        add_action($notice_hook, [$this, 'display_notices']);
        add_action('admin_init', [$this, 'dismiss_notices'], 20);
        add_filter('safe_style_css', function ($styles) {
            $styles[] = 'display';
            return $styles;
        });
    }
    /**
     * Get all notices
     *
     * @return array
     */
    public function get_notices()
    {
        return $this->notices;
    }
    /**
     * Set notices
     *
     * @param array $notices Notices.
     *
     * @return void
     */
    public function set_notices($notices)
    {
        $this->notices = $notices;
    }
    /**
     * Get group notices
     *
     * @return array
     */
    public function get_group_notices()
    {
        return $this->group_notices;
    }
    /**
     * Set group notices
     *
     * @param array $group_notices Group notices.
     *
     * @return void
     */
    public function set_group_notices($group_notices)
    {
        $this->group_notices = $group_notices;
    }
    /**
     * Display notices
     *
     * @return void
     */
    public function display_notices()
    {
        // Validate notifications.
        $this->validate_notices();
        if ($this->notices) {
            foreach ($this->notices as $notice) {
                // Check notice type.
                switch ($notice['type']) {
                    // Admin notice.
                    case 'error':
                    case 'warning':
                    case 'information':
                    default:
                        $error = new AdminNotice($notice);
                        $error->display();
                        break;
                }
            }
        }
    }
    /**
     * Validate notices
     *
     * @return void
     */
    private function validate_notices()
    {
        $notices = $this->get_notices();
        $has_admin_errors = apply_filters('sb_' . $this->plugin . '_has_admin_errors', \false);
        if ($notices) {
            foreach ($notices as $key => $notice) {
                if (!isset($notice['type']) || !isset($notice['message'])) {
                    unset($notices[$key]);
                }
                // Check if critical error is present then unset 'information' notices.
                if ($has_admin_errors && in_array($notice['type'], ['information', 'warning'], \true)) {
                    unset($notices[$key]);
                }
                // Check start and end date, and unset if expired.
                if ($notice['start_date'] && $notice['end_date']) {
                    if (strtotime($notice['start_date']) > time() || strtotime($notice['end_date']) < time()) {
                        unset($notices[$key]);
                    }
                }
                // Check page and unset if not match.
                if (isset($notice['page']) && !empty($notice['page'])) {
                    $page = $notice['page'];
                    if (!is_array($page)) {
                        $page = [$page];
                    }
                    if (!in_array($this->screen, $page, \true)) {
                        unset($notices[$key]);
                    }
                }
                // If page has exclude then unset if match.
                if (isset($notice['page_exclude']) && !empty($notice['page_exclude'])) {
                    $page_exclude = $notice['page_exclude'];
                    if (!is_array($page_exclude)) {
                        $page_exclude = [$page_exclude];
                    }
                    if (in_array($this->screen, $page_exclude, \true)) {
                        unset($notices[$key]);
                    }
                }
                // Check capability and unset if not match.
                if (isset($notice['capability']) && !empty($notice['capability'])) {
                    $capability = $notice['capability'];
                    if (!is_array($capability)) {
                        $capability = [$capability];
                    }
                    if (!current_user_can($capability[0])) {
                        unset($notices[$key]);
                    }
                }
                // Check if notice is for free or pro version and unset if not match.
                if (isset($notice['version']) && !empty($notice['version'])) {
                    if ($this->plugin_type !== $notice['version']) {
                        unset($notices[$key]);
                    }
                }
            }
            // Notices are duplicate so unset them.
            $notices = array_unique($notices, \SORT_REGULAR);
            // Sort notices as per priority value.
            uasort($notices, function ($a, $b) {
                if (isset($a['priority']) && isset($b['priority'])) {
                    return $a['priority'] - $b['priority'];
                }
                return 255;
            });
            $notices = apply_filters('sb_' . $this->plugin . '_admin_notices', $notices);
            $this->set_notices($notices);
        }
    }
    /**
     * Get notice by id
     *
     * @param string $id
     *
     * @return array|boolean
     */
    public function get_notice($id)
    {
        $notices = $this->get_notices();
        return isset($notices[$id]) ? $notices[$id] : \false;
    }
    /**
     * Add notice
     *
     * @param string $id
     * @param string $type
     * @param array  $args
     * @param string $group
     *
     * @return void
     */
    public function add_notice($id, $type, $args, $group = \false)
    {
        if (empty($id) || empty($args['title']) && empty($args['message'])) {
            return;
        }
        $type = in_array($type, ['error', 'warning', 'information'], \true) ? $type : 'error';
        $notices = $this->get_notices();
        // Check if notice already exists.
        if (isset($notices[$id])) {
            return;
        }
        // Merge with defaults.
        $notice = wp_parse_args($args, ['id' => $id, 'type' => $type, 'message' => '', 'title' => '', 'icon' => '', 'class' => '', 'dismissible' => \false, 'priority' => 255, 'start_date' => \false, 'end_date' => \false]);
        // Add notice to notices array.
        $notices[$id] = $notice;
        if ($group) {
            // Add notice to group.
            $notices[$id]['group'] = $group;
        }
        // Update notices.
        $this->set_notices($notices);
        update_option($this->notice_option, $notices);
        // Handle group notices.
        if ($group) {
            $group_notices = $this->get_group_notices();
            if (!isset($group_notices[$group])) {
                $group_notices[$group] = [];
            }
            $group_notices[$group][] = $id;
            $this->set_group_notices($group_notices);
            update_option($this->group_notice_option, $group_notices);
        }
    }
    /**
     * Remove notice by id
     *
     * @param string $id
     *
     * @return void
     */
    public function remove_notice($id)
    {
        $notices = $this->get_notices();
        if (isset($notices[$id])) {
            // Handle group notices.
            $group_notices = $this->get_group_notices();
            $is_group_notice = isset($notices[$id]['group']) ? $notices[$id]['group'] : \false;
            if ($is_group_notice) {
                $group_id = $notices[$id]['group'];
                if (isset($group_notices[$group_id])) {
                    $group_notices[$group_id] = array_diff($group_notices[$group_id], [$id]);
                    $this->set_group_notices($group_notices);
                    update_option($this->group_notice_option, $group_notices);
                }
            }
            unset($notices[$id]);
            $this->set_notices($notices);
            update_option($this->notice_option, $notices);
        }
    }
    /**
     * Remove all notices
     *
     * @return void
     */
    public function remove_all_notices()
    {
        $this->set_notices([]);
        $this->set_group_notices([]);
        delete_option($this->notice_option);
        delete_option($this->group_notice_option);
    }
    /**
     * Dismiss notices if the GET param is set.
     *
     * @return void
     */
    public function dismiss_notices()
    {
        if (isset($_GET['sb-dismiss-notice']) && isset($_GET['_sb_notice_nonce'])) {
            if (!wp_verify_nonce(wp_unslash($_GET['_sb_notice_nonce']), 'sb_dismiss_notice_nonce')) {
                wp_die(esc_html__('Action failed. Please refresh the page and retry.', 'sb-notices'));
            }
            $notice_id = sanitize_text_field(wp_unslash($_GET['sb-dismiss-notice']));
            $notices = $this->get_notices();
            if (isset($notices[$notice_id])) {
                $notice = $notices[$notice_id];
                if (!$notice['dismissible']) {
                    wp_die(esc_html__('Notice cannot be dismissed.', 'sb-notices'));
                }
                if (isset($notice['capability']) && !empty($notice['capability'])) {
                    $capability = $notice['capability'];
                    if (!is_array($capability)) {
                        $capability = [$capability];
                    }
                    if (!current_user_can($capability[0])) {
                        wp_die(esc_html__('You do not have permission to dismiss the notice.', 'sb-notices'));
                    }
                }
                $this->remove_notice($notice_id);
                update_user_meta(get_current_user_id(), 'sb_notice_' . $notice_id . '_dismissed', \true);
                do_action('sb_notice_' . $notice_id . '_dismissed', $notice_id);
            }
        }
    }
}
