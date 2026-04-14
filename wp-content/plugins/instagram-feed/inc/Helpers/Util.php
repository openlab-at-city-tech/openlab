<?php

namespace InstagramFeed\Helpers;

use DateTime;

/**
 * @since 6.1.2
 */
class Util
{
	/**
	 * Returns the enabled debugging flag state.
	 *
	 * @return bool
	 */
	public static function isDebugging()
	{
		return (defined('SBI_DEBUG') && SBI_DEBUG === true) || isset($_GET['sbi_debug']) || isset($_GET['sb_debug']);
	}

	public static function isIFPage()
	{
		return get_current_screen() !== null && !empty($_GET['page']) && strpos($_GET['page'], 'sbi-') !== false;
	}

	/**
	 * Returns the script debug state.
	 *
	 * @return bool
	 */
	public static function is_script_debug()
	{
		return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG === true;
	}


	/**
	 * Get other active plugins of Smash Balloon
	 *
	 * @since 6.2.0
	 */
	public static function get_sb_active_plugins_info()
	{
		// Get the WordPress's core list of installed plugins.
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();

		$plugins = array(
			'instagram' => array(
				'free' => 'instagram-feed/instagram-feed.php',
				'pro' => 'instagram-feed-pro/instagram-feed.php',
			),
			'facebook' => array(
				'free' => 'custom-facebook-feed/custom-facebook-feed.php',
				'pro' => 'custom-facebook-feed-pro/custom-facebook-feed.php',
			),
			'twitter' => array(
				'free' => 'custom-twitter-feeds/custom-twitter-feed.php',
				'pro' => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
			),
			'youtube' => array(
				'free' => 'feeds-for-youtube/feeds-for-youtube.php',
				'pro' => 'youtube-feed-pro/youtube-feed-pro.php',
			),
			'tiktok' => array(
				'free' => 'feeds-for-tiktok/feeds-for-tiktok.php',
				'pro' => 'tiktok-feeds-pro/tiktok-feeds-pro.php',
			),
			'reviews' => array(
				'free' => 'reviews-feed/sb-reviews.php',
				'pro' => 'reviews-feed-pro/sb-reviews-pro.php',
			),
			'social_wall' => array(
				'free' => 'social-wall/social-wall.php',
			),
			'feed_analytics' => array(
				'free' => 'sb-analytics/sb-analytics-pro.php',
			)
		);

		$active_plugins_info = array();

		foreach ($plugins as $key => $plugin_files) {
			$active_plugins_info[$key . '_plugin'] = $plugin_files['free'];
			$active_plugins_info['is_' . $key . '_installed'] = false;

			if (isset($plugin_files['pro']) && isset($installed_plugins[$plugin_files['pro']])) {
				$active_plugins_info[$key . '_plugin'] = $plugin_files['pro'];
				$active_plugins_info['is_' . $key . '_installed'] = true;
			} elseif (isset($installed_plugins[$plugin_files['free']])) {
				$active_plugins_info['is_' . $key . '_installed'] = true;
			}
		}

		$active_plugins_info['installed_plugins'] = $installed_plugins;

		return $active_plugins_info;
	}

	/**
	 * Get a valid timestamp to avoid Year 2038 problem.
	 *
	 * @param mixed $timestamp
	 * @return int
	 */
	public static function get_valid_timestamp($timestamp)
	{
		// check if timestamp is negative and set to maximum value
		if ($timestamp < 0) {
			$timestamp = 2147483647;
		}

		if (is_numeric($timestamp)) {
			$timestamp = (int)$timestamp;
			return $timestamp;
		}

		$new_timestamp = new DateTime($timestamp);
		$year = $new_timestamp->format('Y');
		if ((int)$year >= 2038) {
			$new_timestamp->setDate(2037, 12, 30)->setTime(00, 00, 00);
			$timestamp = $new_timestamp->getTimestamp();
		} else {
			$timestamp = strtotime($timestamp);
		}

		return $timestamp;
	}

	/**
	 * Checks if the user has custom templates, CSS or JS added and if they have dismissed the notice
	 *
	 * @return bool
	 * @since 6.3
	 */
	public static function sbi_show_legacy_css_settings()
	{
		$show_legacy_css_settings = false;
		$sbi_statuses = get_option('sbi_statuses', array());

		if (
			(isset($sbi_statuses['custom_templates_notice'])
				&& self::sbi_has_custom_templates())
			|| self::sbi_legacy_css_enabled()
		) {
			$show_legacy_css_settings = true;
		}

		$show_legacy_css_settings = apply_filters('sbi_show_legacy_css_settings', $show_legacy_css_settings);

		return $show_legacy_css_settings;
	}

	/**
	 * Checks if the user has custom templates, CSS or JS added
	 *
	 * @return bool
	 * @since 6.3
	 */
	public static function sbi_has_custom_templates()
	{
		// Check if the user has sbi custom templates in their theme
		$templates = array(
			'feed.php',
			'footer.php',
			'header.php',
			'item.php',
		);
		foreach ($templates as $template) {
			if (locate_template('sbi/' . $template)) {
				return true;
			}
		}

		// Check if the user has custom CSS and or JS added in the settings
		$settings = get_option('sb_instagram_settings', array());
		if (isset($settings['sb_instagram_custom_css']) && !empty($settings['sb_instagram_custom_css'])) {
			return true;
		}
		if (isset($settings['sb_instagram_custom_js']) && !empty($settings['sb_instagram_custom_js'])) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the user has legacy CSS enabled
	 *
	 * @return bool
	 * @since 6.3
	 */
	public static function sbi_legacy_css_enabled()
	{
		$legacy_css_enabled = false;
		$settings = get_option('sb_instagram_settings', array());
		if (
			isset($settings['enqueue_legacy_css'])
			&& $settings['enqueue_legacy_css']
		) {
			$legacy_css_enabled = true;
		}

		$legacy_css_enabled = apply_filters('sbi_legacy_css_enabled', $legacy_css_enabled);

		return $legacy_css_enabled;
	}

	/**
	 * Checks if sb_instagram_posts_manager errors exists.
	 *
	 * @return bool
	 */
	public static function sbi_has_admin_errors()
	{
		global $sb_instagram_posts_manager;
		$are_critical_errors = $sb_instagram_posts_manager->are_critical_errors();

		if ($are_critical_errors) {
			return true;
		}

		$errors = $sb_instagram_posts_manager->get_errors();
		if (!empty($errors)) {
			foreach ($errors as $type => $error) {
				if (
					in_array($type, array('database_create', 'upload_dir', 'unused_feed', 'platform_data_deleted', 'database_error'))
					&& !empty($error)
				) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Safely unserialize data
	 *
	 * Uses native PHP unserialize with allowed_classes option for security.
	 * This prevents object injection attacks by disallowing class instantiation.
	 *
	 * @param mixed $data Data to unserialize
	 * @return mixed Unserialized data
	 * @since 6.1.2
	 */
	public static function safe_unserialize($data)
	{
		if (!is_string($data)) {
			return $data;
		}

		// Use native PHP 7.0+ unserialize with allowed_classes option
		// This is safe since minimum PHP version is 7.4
		$data = unserialize($data, ['allowed_classes' => false]);
		return $data;
	}
}
