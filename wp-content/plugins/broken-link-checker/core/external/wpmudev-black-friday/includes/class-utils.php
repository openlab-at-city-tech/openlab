<?php
/**
 * WPMUDEV Black Friday Utils class
 *
 * Common utility methods for the Black Friday module.
 *
 * @since   2.0.0
 * @author  WPMUDEV
 * @package WPMUDEV\Modules\BlackFriday
 */

namespace WPMUDEV\Modules\BlackFriday;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Utils
 *
 * Common utility methods for the Black Friday module.
 *
 * @since 2.0.0
 */
class Utils {

	/**
	 * Gets the script data from assets php file.
	 *
	 * @since 2.0.0
	 *
	 * @param string $handle The script handle (e.g., 'banner').
	 *
	 * @return array The script data.
	 */
	public static function raw_script_data( string $handle = '' ): array {
		static $script_data = array();

		if ( empty( $handle ) ) {
			return array();
		}

		if ( isset( $script_data[ $handle ] ) ) {
			return $script_data[ $handle ];
		}

		$file_path = WPMUDEV_MODULE_BLACK_FRIDAY_DIR . 'build/' . $handle . '.min.asset.php';

		if ( file_exists( $file_path ) ) {
			$script_data[ $handle ] = include $file_path;
		} else {
			$script_data[ $handle ] = array();
		}

		return (array) $script_data[ $handle ];
	}

	/**
	 * Gets assets data for given key.
	 *
	 * @since 2.0.0
	 *
	 * @param string $handle The script handle (e.g., 'banner').
	 * @param string $key    The requested portion of data, an array key, usually version or dependencies.
	 *
	 * @return string|array The script data or specific key value.
	 */
	public static function script_data( string $handle = '', string $key = '' ) {
		$raw_script_data = self::raw_script_data( $handle );

		return ! empty( $key ) && ! empty( $raw_script_data[ $key ] ) ? $raw_script_data[ $key ] : $raw_script_data;
	}

	/**
	 * Get's all plugin data from plugins_list file.
	 *
	 * @since 2.0.0
	 *
	 * @return array The plugins list.
	 */
	public static function get_plugins_list(): array {
		static $plugins_list = null;

		if ( is_null( $plugins_list ) ) {
			$plugins_file = untrailingslashit( WPMUDEV_MODULE_BLACK_FRIDAY_DIR ) . '/includes/plugins-list.php';

			if ( file_exists( $plugins_file ) ) {
				$plugins_list = require_once $plugins_file;
			}
		}

		return is_array( $plugins_list ) ? $plugins_list : array();
	}

	/**
	 * Get's the admin screens of each WPMU DEV plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return array The plugin screen slugs.
	 */
	public static function get_plugin_screens(): array {
		static $plugins_screens = null;

		if ( is_null( $plugins_screens ) ) {
			$plugins_list    = self::get_plugins_list();
			$plugins_screens = array();

			// Extract admin_url_page from each plugin.
			$admin_pages = wp_list_pluck( $plugins_list, 'admin_url_page' );

			// Flatten in case admin_url_page is an array.
			foreach ( $admin_pages as $page ) {
				if ( is_array( $page ) ) {
					// If it's an array, add each item.
					$plugins_screens = array_merge( $plugins_screens, $page );
				} elseif ( ! empty( $page ) ) {
					// If it's a single value, add it directly.
					$plugins_screens[] = $page;
				}
			}

			// Remove duplicates and reindex.
			$plugins_screens = array_values( array_unique( $plugins_screens ) );
		}

		return $plugins_screens;
	}

	/**
	 * Get's the parent admin screens of each WPMU DEV plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return array The plugin parent screen slugs.
	 */
	public static function get_plugin_parent_screens(): array {
		$plugins_list = self::get_plugins_list();
		return array_values(
			wp_list_pluck( $plugins_list, 'admin_parent_page' )
		);
	}

	/**
	 * Get's the parent menu slugs of each WPMU DEV plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return array The parent menu slugs.
	 */
	public static function get_parent_menu_slugs(): array {
		return self::get_plugin_screens();
	}

	/**
	 * Polyfill for str_ends_with function for PHP versions < 8.0 and WP versions < 5.9. BLC supports from WP 5.2+.
	 *
	 * @since 2.0.0
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for.
	 *
	 * @return bool True if $haystack ends with $needle, false otherwise.
	 */
	public static function str_ends_with( string $haystack, string $needle ): bool {
		if ( function_exists( 'str_ends_with' ) ) {
			return str_ends_with( $haystack, $needle );
		}

		$length = strlen( $needle );
		if ( 0 === $length ) {
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}

	/**
	 * Get UTM source for given plugin key.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key The plugin key.
	 *
	 * @return string The UTM source.
	 */
	public static function get_utm_source( string $key = '' ): string {
		$plugins_list = self::get_plugins_list();
		$utm_source   = '';

		if ( isset( $plugins_list[ $key ] ) && isset( $plugins_list[ $key ]['utm_source'] ) ) {
			$utm_source = $plugins_list[ $key ]['utm_source'];
		}

		return $utm_source;
	}

	/**
	 * Get UTM source for the current admin page.
	 *
	 * @since 2.0.0
	 *
	 * @return string The UTM source.
	 */
	public static function get_current_page_utm_source(): string {
		$current_screen = get_current_screen();
		$plugins_list   = self::get_plugins_list();

		if ( empty( $current_screen ) || empty( $current_screen->id ) ) {
			return '';
		}

		$current_page = $current_screen->id;

		foreach ( $plugins_list as $plugin_key => $plugin_data ) {
			$admin_url_page = $plugin_data['admin_url_page'] ?? '';

			if ( is_string( $admin_url_page ) && str_contains( $current_page, $admin_url_page ) ) {
				return $plugin_data['utm_source'] ?? '';
			}
		}

		return '';
	}
}
