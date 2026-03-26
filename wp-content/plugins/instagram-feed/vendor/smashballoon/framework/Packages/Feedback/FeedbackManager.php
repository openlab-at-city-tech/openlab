<?php

/**
 * Feedback Manager - Main entry point for the feedback library.
 *
 * @package Feedback
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Feedback;

if (!defined('ABSPATH')) {
    exit;
}
/**
 * Manages feedback collection across Smash Balloon plugins.
 * Initializes the deactivation survey and API client.
 */
class FeedbackManager
{
    /**
     * Plugin configuration.
     *
     * @var array
     */
    private static $configs = [];
    /**
     * Whether the manager has been bootstrapped.
     *
     * @var bool
     */
    private static $booted = \false;
    /**
     * Initialize the feedback system for a plugin.
     *
     * @param array $config {
     *     Plugin configuration.
     *
     *     @type string $plugin_slug    Required. Plugin slug (e.g. 'instagram-feed').
     *     @type string $plugin_name    Required. Display name (e.g. 'Smash Balloon Instagram Feed').
     *     @type string $plugin_version Required. Current plugin version.
     *     @type string $plugin_file    Required. Main plugin file path (__FILE__ from main plugin file).
     *     @type string $support_url    Optional. Support page URL. Default: 'https://smashballoon.com/support/'.
     *     @type string $api_endpoint   Optional. Feedback API endpoint URL.
     * }
     *
     * @return void
     */
    public static function init(array $config)
    {
        $defaults = ['plugin_slug' => '', 'plugin_name' => '', 'plugin_version' => '', 'plugin_file' => '', 'support_url' => 'https://smashballoon.com/support/', 'api_endpoint' => ''];
        $config = wp_parse_args($config, $defaults);
        if (empty($config['plugin_slug']) || empty($config['plugin_name']) || empty($config['plugin_file'])) {
            return;
        }
        $slug = sanitize_key($config['plugin_slug']);
        // Prevent duplicate registration.
        if (isset(self::$configs[$slug])) {
            return;
        }
        self::$configs[$slug] = $config;
        self::boot();
    }
    /**
     * Bootstrap the feedback system (once).
     *
     * @return void
     */
    private static function boot()
    {
        if (self::$booted) {
            return;
        }
        self::$booted = \true;
        // Hook directly — screen checks happen inside the callbacks.
        add_action('admin_enqueue_scripts', [__CLASS__, 'maybe_enqueue']);
        add_action('admin_footer', [__CLASS__, 'maybe_render']);
        add_action('wp_ajax_sb_deactivation_feedback', [DeactivationSurvey::class, 'handle_ajax']);
    }
    /**
     * Check if we're on the plugins page.
     *
     * @return bool
     */
    private static function is_plugins_page()
    {
        if (!current_user_can('activate_plugins')) {
            return \false;
        }
        global $pagenow;
        return 'plugins.php' === $pagenow;
    }
    /**
     * Enqueue assets if on plugins page.
     *
     * @return void
     */
    public static function maybe_enqueue()
    {
        if (!self::is_plugins_page()) {
            return;
        }
        $survey = new DeactivationSurvey(self::$configs);
        $survey->enqueue_assets();
    }
    /**
     * Render modal if on plugins page.
     *
     * @return void
     */
    public static function maybe_render()
    {
        if (!self::is_plugins_page()) {
            return;
        }
        $survey = new DeactivationSurvey(self::$configs);
        $survey->render_modal();
    }
    /**
     * Get configuration for a specific plugin.
     *
     * @param string $slug Plugin slug.
     * @return array|null
     */
    public static function get_config($slug)
    {
        return isset(self::$configs[$slug]) ? self::$configs[$slug] : null;
    }
    /**
     * Get all registered plugin configurations.
     *
     * @return array
     */
    public static function get_all_configs()
    {
        return self::$configs;
    }
    /**
     * Reset state (useful for testing).
     *
     * @return void
     */
    public static function reset()
    {
        self::$configs = [];
        self::$booted = \false;
    }
}
