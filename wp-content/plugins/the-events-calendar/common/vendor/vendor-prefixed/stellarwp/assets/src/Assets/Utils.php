<?php

namespace TEC\Common\StellarWP\Assets;

class Utils {
	/**
	 * Stores all the Bases for the request.
	 *
	 * @since 1.2.3
	 *
	 * @var array
	 */
	protected static array $bases = [];

	/**
	 * Determines if the provided value should be regarded as 'true'.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $var
	 *
	 * @return bool
	 */
	public static function is_truthy( $var ) : bool {
		if ( is_bool( $var ) ) {
			return $var;
		}

		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Provides an opportunity to modify strings that will be
		 * deemed to evaluate to true.
		 *
		 * @param array $truthy_strings
		 */
		$truthy_strings = (array) apply_filters( "stellarwp/assets/{$hook_prefix}/is_truthy_strings", [
			'1',
			'enable',
			'enabled',
			'on',
			'y',
			'yes',
			'true',
		] );

		// Makes sure we are dealing with lowercase for testing
		if ( is_string( $var ) ) {
			$var = strtolower( $var );
		}

		// If $var is a string, it is only true if it is contained in the above array
		if ( in_array( $var, $truthy_strings, true ) ) {
			return true;
		}

		// All other strings will be treated as false
		if ( is_string( $var ) ) {
			return false;
		}

		// For other types (ints, floats etc) cast to bool
		return (bool) $var;
	}

	/**
	 * Gets the asset bases for the request, both directory and URL.
	 *
	 * @since 1.2.3
	 *
	 * @return array
	 */
	public static function get_bases(): array {
		$key = self::get_runtime_cache_key();

		if ( empty( static::$bases[ $key ] ) ) {
			static::$bases[ $key ] = [
				'wpmu_plugin' => [
					'base_dir' => wp_normalize_path( WPMU_PLUGIN_DIR ),
					'base_url' => set_url_scheme( WPMU_PLUGIN_URL ),
				],
				'wp_plugin'   => [
					'base_dir' => wp_normalize_path( WP_PLUGIN_DIR ),
					'base_url' => set_url_scheme( WP_PLUGIN_URL ),
				],
				'wp_content'  => [
					'base_dir' => wp_normalize_path( WP_CONTENT_DIR ),
					'base_url' => set_url_scheme( WP_CONTENT_URL ),
				],
				'plugins'     => [
					'base_dir' => wp_normalize_path( WP_PLUGIN_DIR ),
					'base_url' => plugins_url(),
				],
				'stylesheet'  => [
					'base_dir' => wp_normalize_path( get_stylesheet_directory() ),
					'base_url' => get_stylesheet_directory_uri(),
				],
			];
		}

		return static::$bases[ $key ];
	}

	/**
	 * Clears the runtime cache.
	 *
	 * @since 1.4.1
	 *
	 * @return void
	 */
	public static function clear_runtime_cache() {
		static::$bases = [];
	}

	/**
	 * Get the runtime cache key.
	 *
	 * @since 1.2.3
	 *
	 * @param array $extra Extra data to include in the cache key.
	 *
	 * @return string
	 */
	public static function get_runtime_cache_key( array $extra = [] ): string {
		return md5(
			serialize(
				array_merge(
					[
						WPMU_PLUGIN_DIR,
						WPMU_PLUGIN_URL,
						WP_PLUGIN_DIR,
						WP_PLUGIN_URL,
						WP_CONTENT_DIR,
						WP_CONTENT_URL,
						plugins_url(),
						get_stylesheet_directory(),
						get_stylesheet_directory_uri(),
					],
					$extra
				)
			)
		);
	}
}
