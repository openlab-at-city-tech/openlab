<?php
/**
 * Utilities class contains a list of common useful helper methods.
 *
 * @link    https://wpmudev.com/
 * @since   1.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV/Plugin_Cross_Sell
 *
 * @copyright (c) 2025, Incsub (http://incsub.com)
 */

namespace WPMUDEV\Modules\Plugin_Cross_Sell;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * A list of general purpose utility methods.
 */
class Utilities {
	/**
	 * Returns the list of all plugins.
	 *
	 * @return array
	 */
	public function get_plugins_list() {
		static $plugins_list = null;

		if ( is_null( $plugins_list ) ) {
			$plugins_file = untrailingslashit( WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR ) . '/core/plugins-list.php';
			if ( file_exists( $plugins_file ) ) {
				$plugins_list = require_once $plugins_file;
			}
		}

		return is_array( $plugins_list ) ? $plugins_list : array();
	}

	/**
	 * Returns the list of free plugins.
	 *
	 * @return array
	 */
	public function get_free_plugins(): array {
		$plugins = $this->get_plugins_list();
		return is_array( $plugins ) && ! empty( $plugins['free-plugins'] ) ? $plugins['free-plugins'] : array();
	}

	/**
	 * Returns the list of pro plugins.
	 *
	 * @return array
	 */
	public function get_pro_plugins(): array {
		$plugins = $this->get_plugins_list();
		return is_array( $plugins ) && ! empty( $plugins['pro-plugins'] ) ? $plugins['pro-plugins'] : array();
	}

	/**
	 * Retrieves the path of a plugin by its slug.
	 *
	 * @param string $plugin_slug The plugin slug.
	 * @return string
	 */
	public function get_plugin_path_by_slug( string $plugin_slug = '' ): string {
		$free_plugins = $this->get_free_plugins();
		$free_plugins = is_array( $free_plugins ) && ! empty( $free_plugins['free-plugins'] ) ? $free_plugins['free-plugins'] : $free_plugins;

		return $this->get_value_from_associative_array( 'path', $free_plugins, array( 'slug' => $plugin_slug ) );
	}

	/**
	 * Extracts the value of a key from the first associative array in an array that matches the given criteria.
	 *
	 * @param string $key         The key whose value you want to extract.
	 * @param array  $input_list  The array of associative arrays to filter.
	 * @param array  $args        The criteria for filtering (key-value pairs).
	 * @param string $operator    How to combine the criteria ('AND' or 'OR').
	 * @return mixed              The value for the specified key if found, or null.
	 */
	public function get_value_from_associative_array( string $key = '', array $input_list = array(), array $args = array(), string $operator = 'AND' ) {
		if ( empty( $key ) || empty( $input_list ) ) {
			return null;
		}

		$filtered = wp_list_filter( $input_list, $args, $operator );

		if ( ! empty( $filtered ) ) {
			$first_item = current( $filtered );

			// Check if $first_item is an array.
			if ( is_array( $first_item ) ) {
				return isset( $first_item[ $key ] ) ? $first_item[ $key ] : '';
			}

			// Check if $first_item is an object.
			if ( is_object( $first_item ) ) {
				return isset( $first_item->$key ) ? $first_item->$key : '';
			}
		}

		return null;
	}

	/**
	 * Checks if a plugin is installed.
	 *
	 * @param string $file The plugin file path.
	 * @return bool
	 */
	public function is_plugin_installed( string $file = '' ): bool {
		// Include necessary plugin functions.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();

		return is_array( $all_plugins ) ? isset( $all_plugins[ $file ] ) : false;
	}

	/**
	 * Get plugin statistics from WordPress.org API
	 *
	 * @param string $plugin_slug The plugin slug from wordpress.org.
	 * @param bool   $force_refresh Optional. Whether to force a refresh of the data from the API.
	 * @return array|false Plugin data or false on failure.
	 */
	public function get_plugin_stats( string $plugin_slug = '', bool $force_refresh = false ): mixed {
		if ( empty( $plugin_slug ) ) {
			return false;
		}

		// Set cache key - add a prefix for easy identification/deletion.
		$transient_key = 'wpmudev_pcs_plugin_stats_' . sanitize_key( $plugin_slug );

		// Try to get cached data first (unless force refresh is requested).
		if ( ! $force_refresh ) {
			$cached_data = get_transient( $transient_key );
			if ( false !== $cached_data ) {
				return $cached_data;
			}
		}

		// If not in cache or force refresh, fetch from API.
		$url = "https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&slug={$plugin_slug}";

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15, // Increased timeout for potentially slow API responses.
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status_code ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}

		// Format the data (with fallbacks for each field).
		$plugin_data = array(
			'name'            => $data['name'] ?? '',
			'version'         => $data['version'] ?? '',
			'active_installs' => $data['active_installs'] ?? 0,
			'rating'          => isset( $data['rating'] ) ? round( $data['rating'] / 20, 1 ) : 0, // Convert to 5-star scale.
			'num_ratings'     => $data['num_ratings'] ?? 0,
			'last_updated'    => $data['last_updated'] ?? '',
			'requires_wp'     => $data['requires'] ?? '',
			'tested_wp'       => $data['tested'] ?? '',
			'author'          => isset( $data['author'] ) ? wp_strip_all_tags( $data['author'] ) : '',
			'homepage'        => $data['homepage'] ?? '',
		);

		// Cache the data.
		$expiration = DAY_IN_SECONDS * 3;

		// Store in transient.
		set_transient( $transient_key, $plugin_data, $expiration );

		return $plugin_data;
	}

	/**
	 * Clear plugin stats cache for a specific plugin or all plugin stats
	 *
	 * @param string $plugin_slug Optional. Clear cache for specific plugin. If empty, clears all plugin stats.
	 * @return bool True on success
	 */
	public function clear_plugin_stats_cache( string $plugin_slug = '' ): bool {
		global $wpdb;

		// If plugin slug provided, clear only that plugin's cache.
		if ( ! empty( $plugin_slug ) ) {
			$transient_key = 'wpmudev_pcs_plugin_stats_' . sanitize_key( $plugin_slug );
			return delete_transient( $transient_key );
		}

		// Otherwise clear all plugin stats transients.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wpmudev_pcs_plugin_stats_%'" );
		return true;
	}

	/**
	 * Validate a value against a given schema.
	 *
	 * @param mixed $value   The value to check.
	 * @param mixed $schema  The schema that defines the expected type or structure.
	 * @param bool  $strict  If true, value must not have extra keys/properties.
	 * @return bool
	 */
	public function validate_schema( $value, $schema, bool $strict = false ): bool {
		// If schema is a simple type (string), perform a type check.
		if ( is_string( $schema ) ) {
			return $this->validate_type( $value, $schema );
		}

		// If schema is an array, we expect the value to be an array or object.
		if ( is_array( $schema ) ) {
			if ( ! is_array( $value ) && ! is_object( $value ) ) {
				return false;
			}

			// Convert objects to an associative array of properties.
			$value_array = is_object( $value ) ? get_object_vars( $value ) : $value;

			// Check each key defined in the schema.
			foreach ( $schema as $key => $expected_type ) {
				if ( $strict && ! array_key_exists( $key, $value_array ) ) {
					// In strict mode, all keys in the schema must be present.
					return false;
				}
				if ( array_key_exists( $key, $value_array ) ) {
					// Recursively validate the value for this key.
					if ( ! $this->validate_schema( $value_array[ $key ], $expected_type, $strict ) ) {
						return false;
					}
				}
			}

			// In strict mode, also ensure that there are no extra keys.
			if ( $strict && count( $value_array ) !== count( $schema ) ) {
				return false;
			}
			return true;
		}

		// If the schema is neither a string nor an array, we don't know how to validate.
		return false;
	}

	/**
	 * Check if a value is of the expected type.
	 *
	 * @param mixed  $value The value to check.
	 * @param string $type  The expected type (e.g., 'int', 'string', 'bool', 'array', 'object').
	 * @return bool
	 */
	public function validate_type( $value, string $type ): bool {
		switch ( $type ) {
			case 'int':
			case 'integer':
				return is_int( $value );
			case 'string':
				return is_string( $value );
			case 'bool':
			case 'boolean':
				return is_bool( $value );
			case 'float':
			case 'double':
				return is_float( $value );
			case 'array':
				return is_array( $value );
			case 'object':
				return is_object( $value );
			default:
				// If $type is a class name, check if $value is an instance of that class.
				if ( class_exists( $type ) ) {
					return $value instanceof $type;
				}
				// Unknown type.
				return false;
		}
	}
}
