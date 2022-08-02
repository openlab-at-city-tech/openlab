<?php
/**
 * Webfonts API functions.
 *
 * @package    WordPress
 * @subpackage WebFonts
 * @since      6.0.0
 */

if ( ! function_exists( 'wp_webfonts' ) ) {
	/**
	 * Instantiates the webfonts controller, if not already set, and returns it.
	 *
	 * @since 6.0.0
	 *
	 * @return WP_Webfonts Instance of the controller.
	 */
	function wp_webfonts() {
		global $wp_webfonts;

		if ( ! $wp_webfonts instanceof WP_Webfonts ) {
			$wp_webfonts = new WP_Webfonts();
			$wp_webfonts->init();
		}

		return $wp_webfonts;
	}
}

if ( ! function_exists( 'wp_register_webfonts' ) ) {
	/**
	 * Registers a collection of webfonts.
	 *
	 * Example of how to register Source Serif Pro font with font-weight range of 200-900
	 * and font-style of normal and italic:
	 *
	 * If the font files are contained within the theme:
	 * <code>
	 * wp_register_webfonts(
	 *      array(
	 *          array(
	 *              'provider'    => 'local',
	 *              'font-family' => 'Source Serif Pro',
	 *              'font-weight' => '200 900',
	 *              'font-style'  => 'normal',
	 *              'src'         => get_theme_file_uri( 'assets/fonts/source-serif-pro/SourceSerif4Variable-Roman.ttf.woff2' ),
	 *          ),
	 *          array(
	 *              'provider'    => 'local',
	 *              'font-family' => 'Source Serif Pro',
	 *              'font-weight' => '200 900',
	 *              'font-style'  => 'italic',
	 *              'src'         => get_theme_file_uri( 'assets/fonts/source-serif-pro/SourceSerif4Variable-Italic.ttf.woff2' ),
	 *          ),
	 *      )
	 * );
	 * </code>
	 *
	 * Webfonts should be registered in the `after_setup_theme` hook.
	 *
	 * @since 6.0.0
	 *
	 * @param array[] $webfonts Webfonts to be registered.
	 *                        This contains an array of webfonts to be registered.
	 *                        Each webfont is an array.
	 * @return string[] The font family slug of the registered webfonts.
	 */
	function wp_register_webfonts( array $webfonts ) {
		$registered_webfont_slugs = array();

		foreach ( $webfonts as $webfont ) {
			$slug = wp_register_webfont( $webfont );

			if ( is_string( $slug ) ) {
				$registered_webfont_slugs[ $slug ] = true;
			}
		}

		return array_keys( $registered_webfont_slugs );
	}
}

if ( ! function_exists( 'wp_register_webfont' ) ) {
	/**
	 * Registers a single webfont.
	 *
	 * Example of how to register Source Serif Pro font with font-weight range of 200-900:
	 *
	 * If the font file is contained within the theme:
	 *
	 * <code>
	 * wp_register_webfont(
	 *      array(
	 *          'provider'    => 'local',
	 *          'font-family' => 'Source Serif Pro',
	 *          'font-weight' => '200 900',
	 *          'font-style'  => 'normal',
	 *          'src'         => get_theme_file_uri( 'assets/fonts/source-serif-pro/SourceSerif4Variable-Roman.ttf.woff2' ),
	 *      )
	 * );
	 * </code>
	 *
	 * @since 6.0.0
	 *
	 * @param array $webfont Webfont to be registered.
	 * @return string|false The font family slug if successfully registered, else false.
	 */
	function wp_register_webfont( array $webfont ) {
		return wp_webfonts()->register_webfont( $webfont );
	}
}

if ( ! function_exists( 'wp_enqueue_webfonts' ) ) {
	/**
	 * Enqueues a collection of font families.
	 *
	 * Example of how to enqueue Source Serif Pro and Roboto font families, both registered beforehand.
	 *
	 * <code>
	 * wp_enqueue_webfonts(
	 *  'Roboto',
	 *  'Sans Serif Pro'
	 * );
	 * </code>
	 *
	 * Font families should be enqueued from the `init` hook or later.
	 *
	 * @since 6.0.0
	 *
	 * @param string[] $webfonts Font families to be enqueued.
	 */
	function wp_enqueue_webfonts( array $webfonts ) {
		foreach ( $webfonts as $webfont ) {
			wp_enqueue_webfont( $webfont );
		}
	}
}

if ( ! function_exists( 'wp_enqueue_webfont' ) ) {
	/**
	 * Enqueue a single font family that has been registered beforehand.
	 *
	 * Example of how to enqueue Source Serif Pro font:
	 *
	 * <code>
	 * wp_enqueue_webfont( 'Source Serif Pro' );
	 * </code>
	 *
	 * Font families should be enqueued from the `init` hook or later.
	 *
	 * @since 6.0.0
	 *
	 * @param string $font_family_name The font family name to be enqueued.
	 * @return bool True if successfully enqueued, else false.
	 */
	function wp_enqueue_webfont( $font_family_name ) {
		return wp_webfonts()->enqueue_webfont( $font_family_name );
	}
}

if ( ! function_exists( 'wp_register_webfont_provider' ) ) {
	/**
	 * Registers a custom font service provider.
	 *
	 * A webfont provider contains the business logic for how to
	 * interact with a remote font service and how to generate
	 * the `@font-face` styles for that remote service.
	 *
	 * How to register a custom font service provider:
	 *    1. Load its class file into memory before registration.
	 *    2. Pass the class' name to this function.
	 *
	 * For example, for a class named `My_Custom_Font_Service_Provider`:
	 * ```
	 *    wp_register_webfont_provider( My_Custom_Font_Service_Provider::class );
	 * ```
	 *
	 * @since 6.0.0
	 *
	 * @param string $name      The provider's name.
	 * @param string $classname The provider's class name.
	 *                          The class should be a child of `WP_Webfonts_Provider`.
	 *                          See {@see WP_Webfonts_Provider}.
	 *
	 * @return bool True if successfully registered, else false.
	 */
	function wp_register_webfont_provider( $name, $classname ) {
		return wp_webfonts()->register_provider( $name, $classname );
	}
}

if ( ! function_exists( 'wp_get_webfont_providers' ) ) {
	/**
	 * Gets all registered providers.
	 *
	 * Return an array of providers, each keyed by their unique
	 * ID (i.e. the `$id` property in the provider's object) with
	 * an instance of the provider (object):
	 *     ID => provider instance
	 *
	 * Each provider contains the business logic for how to
	 * process its specific font service (i.e. local or remote)
	 * and how to generate the `@font-face` styles for its service.
	 *
	 * @since 6.0.0
	 *
	 * @return WP_Webfonts_Provider[] All registered providers, each keyed by their unique ID.
	 */
	function wp_get_webfont_providers() {
		return wp_webfonts()->get_providers();
	}
}

/**
 * Add webfonts mime types.
 */
add_filter(
	'mime_types',
	function( $mime_types ) {
		// Webfonts formats.
		$mime_types['woff2'] = 'font/woff2';
		$mime_types['woff']  = 'font/woff';
		$mime_types['ttf']   = 'font/ttf';
		$mime_types['eot']   = 'application/vnd.ms-fontobject';
		$mime_types['otf']   = 'application/x-font-opentype';

		return $mime_types;
	}
);
