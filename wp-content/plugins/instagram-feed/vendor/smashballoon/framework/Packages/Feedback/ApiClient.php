<?php

/**
 * API Client - HTTP client for sending feedback data.
 *
 * @package Feedback
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Feedback;

if (!defined('ABSPATH')) {
    exit;
}
/**
 * Handles HTTP communication with the feedback API endpoint.
 * Fire-and-forget: never blocks plugin deactivation.
 */
class ApiClient
{
    /**
     * Request timeout in seconds.
     *
     * @var int
     */
    const TIMEOUT = 5;
    /**
     * Default API endpoint for Smash Balloon plugins.
     *
     * @var string
     */
    const DEFAULT_ENDPOINT = 'https://smashballoon.com/wp-json/sb/v1/feedback';
    /**
     * WPChat API endpoint.
     *
     * @var string
     */
    const WPCHAT_ENDPOINT = 'https://wpchat.com/wp-json/sb/v1/feedback';
    /**
     * Staging API endpoint for Smash Balloon plugins.
     *
     * @var string
     */
    const STAGING_DEFAULT_ENDPOINT = 'https://staging.smashballoon.com/wp-json/sb/v1/feedback';
    /**
     * Staging WPChat API endpoint.
     *
     * @var string
     */
    const STAGING_WPCHAT_ENDPOINT = 'https://staging.wpchat.com/wp-json/sb/v1/feedback';
    /**
     * Plugin slugs that should use the WPChat endpoint.
     *
     * @var array
     */
    const WPCHAT_SLUGS = ['wpchat', 'wpchat-pro'];
    /**
     * Check if the current environment is a development/staging environment.
     *
     * @return bool True if in a dev environment.
     */
    public static function is_dev_environment()
    {
        // Allow explicit override via constant.
        if (defined('SB_FEEDBACK_USE_STAGING') && SB_FEEDBACK_USE_STAGING) {
            return \true;
        }
        // Check WordPress environment type (WP 5.5+).
        if (function_exists('wp_get_environment_type')) {
            $env = wp_get_environment_type();
            if (in_array($env, ['local', 'development', 'staging'], \true)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Get the API endpoint for a given plugin slug.
     *
     * @param string $plugin_slug Plugin slug.
     *
     * @return string API endpoint URL.
     */
    public static function get_endpoint_for_slug($plugin_slug)
    {
        $is_dev = self::is_dev_environment();
        $is_wpchat = in_array($plugin_slug, self::WPCHAT_SLUGS, \true);
        if ($is_wpchat) {
            return $is_dev ? self::STAGING_WPCHAT_ENDPOINT : self::WPCHAT_ENDPOINT;
        }
        return $is_dev ? self::STAGING_DEFAULT_ENDPOINT : self::DEFAULT_ENDPOINT;
    }
    /**
     * Send feedback data to the API endpoint.
     *
     * Fire-and-forget: errors are silently ignored so that
     * plugin deactivation always proceeds.
     *
     * @param string $endpoint API endpoint URL.
     * @param array  $data     Feedback data to send.
     *
     * @return bool True if request was sent (regardless of response), false on failure.
     */
    public static function send($endpoint, array $data)
    {
        if (empty($endpoint)) {
            return \false;
        }
        $response = wp_remote_post($endpoint, ['timeout' => self::TIMEOUT, 'blocking' => \false, 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'], 'body' => wp_json_encode($data), 'sslverify' => \true]);
        return !is_wp_error($response);
    }
}
