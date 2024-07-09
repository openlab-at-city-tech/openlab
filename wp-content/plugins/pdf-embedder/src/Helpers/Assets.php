<?php

namespace PDFEmbedder\Helpers;

/**
 * Class Assets to help manage assets.
 *
 * @since 4.7.0
 */
class Assets {

	/**
	 * Plugin version.
	 *
	 * @since 4.8.0
	 *
	 * @var string
	 */
	public static $base_ver = PDFEMB_VERSION;

	/**
	 * Path to the main plugin file.
	 *
	 * @since 4.8.0
	 *
	 * @var string
	 */
	public static $base_file = PDFEMB_PLUGIN_FILE;

	/**
	 * Path to the plugin directory.
	 *
	 * @since 4.8.0
	 *
	 * @var string
	 */
	public static $base_dir = PDFEMB_PLUGIN_DIR;

	/**
	 * Based on the SCRIPT_DEBUG const add or not the `.min` to the file name.
	 * Usage: `Assets::min( 'file.js' );`.
	 *
	 * @since 4.7.0
	 *
	 * @param string $file Filename: alpine.js or tailwind.css, or jquery.plugin.js.
	 */
	public static function min( string $file ): string {

		$chunks = explode( '.', $file );
		$ext    = (array) array_pop( $chunks );
		$min    = Check::is_script_debug() ? [] : [ 'min' ];

		return implode( '.', array_merge( $chunks, $min, $ext ) );
	}

	/**
	 * Define the version of an asset: either the provided version, PDFEMB_VERSION if not provided, or time() when in SCRIPT_DEBUG mode.
	 *
	 * @since 4.7.0
	 *
	 * @param string $current Default value.
	 */
	public static function ver( string $current = '' ): string {

		if ( empty( $current ) ) {
			$current = static::$base_ver;
		}

		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $current;
	}

	/**
	 * Get the URL to a file by its name.
	 *
	 * @since 4.7.0
	 *
	 * @param string $file   File name relative to /assets/ directory in the plugin.
	 * @param bool   $minify Whether the file URL should lead to a minified file.
	 */
	public static function url( string $file, bool $minify = true ): string {

		$file = trim( $file, '/\\' );

		if ( $minify ) {
			$file = self::min( $file );
		}

		return plugins_url( '/assets/' . $file, static::$base_file );
	}

	/**
	 * Get the content of the SVG file.
	 *
	 * @since 4.7.0
	 *
	 * @param string $file SVG file content to retrieve.
	 */
	public static function svg( string $file ): string {

		$file = sanitize_file_name( $file );

		if ( substr( $file, -4, 4 ) !== '.svg' ) {
			return '';
		}

		$path = static::$base_dir . 'assets/' . ltrim( $file, '/\\' );

		if ( is_readable( $path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			return (string) file_get_contents( $path );
		}

		return '';
	}
}
