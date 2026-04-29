<?php

namespace Imagely\NGG\Display;

/**
 * Static Assets management class for NextGEN Gallery.
 *
 * Handles static asset URL and path resolution with override support.
 */
class StaticAssets {

	/**
	 * Default plugin root directory.
	 *
	 * @var string
	 */
	public static $default_plugin_root = NGG_PLUGIN_DIR;

	/**
	 * New override path name for static assets.
	 *
	 * @var string
	 */
	public static $new_override_path_name = 'nextgen-gallery-static-overrides';

	/**
	 * Gets the URL for a static asset file.
	 *
	 * @param string       $filename         The filename to get URL for.
	 * @param false|string $legacy_module_id Legacy module ID for compatibility.
	 * @return string The URL to the static asset.
	 */
	public static function get_url( $filename, $legacy_module_id = false ) {
		$retval = self::get_abspath( $filename, $legacy_module_id );

		// Allow for overrides from WP_CONTENT/ngg/.
		if ( \strpos( $retval, \path_join( WP_CONTENT_DIR, 'ngg' ) ) !== false ) {
			$retval = \str_replace( \wp_normalize_path( WP_CONTENT_DIR ), WP_CONTENT_URL, $retval );
		}

		// Normal plugin distributed files.
		$retval = \str_replace( \wp_normalize_path( WP_PLUGIN_DIR ), WP_PLUGIN_URL, $retval );

		return \is_ssl() ? \str_replace( 'http:', 'https:', $retval ) : $retval;
	}

	/**
	 * Gets the absolute path for a static asset file with caching.
	 *
	 * @param string       $filename         The filename to get path for.
	 * @param false|string $legacy_module_id Legacy module ID for compatibility.
	 * @return string The absolute path to the static asset.
	 */
	public static function get_abspath( $filename, $legacy_module_id = false ) {
		static $cache = [];

		$key = $filename . (string) $legacy_module_id;

		if ( ! isset( $cache[ $key ] ) ) {
			$cache[ $key ] = static::get_computed_abspath( $filename, $legacy_module_id );
		}

		return $cache[ $key ];
	}

	/**
	 * Computes the absolute path for a static asset file.
	 *
	 * @param string       $filename         The filename to get path for.
	 * @param false|string $legacy_module_id Legacy module ID for compatibility.
	 * @return string The computed absolute path to the static asset.
	 */
	public static function get_computed_abspath( $filename, $legacy_module_id = false ) {
		$files = [
			'new_paths' => \path_join( WP_CONTENT_DIR, static::$new_override_path_name . DIRECTORY_SEPARATOR . $filename ),
			'default'   => \path_join( static::$default_plugin_root, 'static' . DIRECTORY_SEPARATOR . $filename ),
		];

		if ( ! empty( $legacy_module_id ) ) {
			$files['legacy'] = StaticPopeAssets::get_computed_abspath( $filename, $legacy_module_id );
		}

		$retval = '';

		foreach ( $files as $label => $filename ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( @\stream_resolve_include_path( $filename ) ) {
				$retval = $filename;
				break;
			}
		}

		if ( is_string( $retval ) ) {
			// Adjust for windows paths.
			return \wp_normalize_path( $retval );
		} else {
			return $retval;
		}
	}
}
