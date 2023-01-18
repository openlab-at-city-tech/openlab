<?php
/**
 * CSS variables generator component.
 *
 * Data are being cached in transient as they are global for the website.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.11
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Assets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class CSS_Variables implements Component_Interface {

	/**
	 * Name of cached data transient.
	 *
	 * @since    1.0.0
	 * @version  1.2.0
	 * @access   public
	 * @var      string
	 */
	public static $transient_cache_key = 'michelle_cache_css_vars';

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.0.12
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_inline_scrollbar_width', MICHELLE_ENQUEUE_PRIORITY );

				add_action( 'switch_theme',         __CLASS__ . '::transient_cache_flush' );
				add_action( 'customize_save_after', __CLASS__ . '::transient_cache_flush' );
				add_action( 'michelle/upgrade',     __CLASS__ . '::transient_cache_flush' );

	} // /init

	/**
	 * Get CSS variables from theme options in array.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  array
	 */
	public static function get_array(): array {

		// Variables

			$is_customize_preview = is_customize_preview();

			$css_vars = (array) get_transient( self::$transient_cache_key );
			$css_vars = array_filter( $css_vars, 'trim', ARRAY_FILTER_USE_KEY );


		// Requirements check

			if (
				! empty( $css_vars )
				&& ! $is_customize_preview
			) {
				// The filter is documented below.
				return (array) apply_filters( 'michelle/customize/css_variables/get_array', $css_vars );
			}


		// Processing

			foreach ( (array) Options::get() as $option ) {
				if ( ! isset( $option['css_var'] ) ) {
					continue;
				}

				if ( isset( $option['default'] ) ) {
					$value = $option['default'];
				} else {
					$value = '';
				}

				$mod = get_theme_mod( $option['id'] );
				if (
					isset( $option['sanitize_callback'] )
					&& is_callable( $option['sanitize_callback'] )
				) {
					$mod = call_user_func( $option['sanitize_callback'], $mod, $option );
				}
				if (
					! empty( $mod )
					|| 'checkbox' === $option['type']
					|| ( 'color' === $option['type'] && '' === $mod )
				) {
					if ( 'color' === $option['type'] ) {
						$value_check = maybe_hash_hex_color( $value );
						$mod         = maybe_hash_hex_color( $mod );
					} else {
						$value_check = $value;
					}
					// No need to output CSS var if it is the same as default.
					if ( $value_check === $mod ) {
						continue;
					}
					$value = $mod;
				} else {
					// No need to output CSS var if it was not changed in customizer.
					continue;
				}

				// Empty color value fix.
				if (
					'color' === $option['type']
					&& '' === $value
				) {
					$value             = 'transparent';
					$option['css_var'] = '[[value]]';
				}

				// Array value to string. Just in case.
				if ( is_array( $value ) ) {
					$value = (string) implode( ',', (array) $value );
				}

				if ( is_callable( $option['css_var'] ) ) {
					$value = call_user_func( $option['css_var'], $value );
				} else {
					$value = str_replace(
						'[[value]]',
						$value,
						(string) $option['css_var']
					);
				}

				// Do not apply `esc_attr()` as it will escape quote marks, such as in background image URL.
				$css_vars[ '--' . sanitize_title( $option['id'] ) ] = $value;

				/**
				 * Filters CSS variables output partially after each variable processing.
				 *
				 * Allows filtering the whole `$css_vars` array for each option individually.
				 * This way we can add an option related additional CSS variables.
				 *
				 * @since  1.0.0
				 *
				 * @param  string $css_vars  Array of CSS variable name and value pairs.
				 * @param  array  $option    Single theme option setup array.
				 * @param  string $value     Single CSS variable value.
				 */
				$css_vars = apply_filters( 'michelle/customize/css_variables/get_array/partial', $css_vars, $option, $value );
			}

			// Cache the results in transient.
			if ( ! $is_customize_preview ) {
				set_transient( self::$transient_cache_key, (array) $css_vars );
			}


		// Output

			/**
			 * Filters CSS variables output in array.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $css_vars  Array of CSS variable name and value pairs.
			 */
			return (array) apply_filters( 'michelle/customize/css_variables/get_array', $css_vars );

	} // /get_array

	/**
	 * Get CSS variables from theme options in string.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $separator
	 *
	 * @return  string
	 */
	public static function get_string( string $separator = ' ' ): string {

		// Variables

			$css_vars = (array) self::get_array();


		// Processing

			$css_vars = array_map(
				function( $variable, $value ) {
					// Actual CSS code declaring a variable.
					return (string) $variable . ': ' . (string) $value . ';';
				},
				array_keys( $css_vars ), // $variable
				$css_vars // $value
			);

			$css_vars = implode( (string) $separator, $css_vars );


		// Output

			/**
			 * Filters CSS variables output in string.
			 *
			 * @since  1.0.0
			 *
			 * @param  string $css_vars  String of CSS variable name and value pairs ready for CSS code output.
			 */
			return (string) apply_filters( 'michelle/customize/css_variables/get_string', trim( (string) $css_vars ) );

	} // /get_string

	/**
	 * Scrollbar width via JavaScript.
	 *
	 * @since    1.0.0
	 * @version  1.3.11
	 *
	 * @return  void
	 */
	public static function enqueue_inline_scrollbar_width() {

		// Processing

			wp_add_inline_script(
				'michelle-scripts-footer',
				Assets\Factory::strip( "
					( function() {
						'use strict';

						function michelleScrollbarWidth() {
							var scrollbar_width = window.innerWidth - document.documentElement.clientWidth;

							document.documentElement.style.setProperty(
								'--scrollbar_width',
								( 40 > scrollbar_width ) ? ( scrollbar_width + 'px' ) : ( '0px' )
							);
						}

						michelleScrollbarWidth();

						window.onresize = function() { michelleScrollbarWidth() };
					} )();
				" )
			);

	} // /enqueue_inline_scrollbar_width

	/**
	 * Flush the transient of cached CSS variables array.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function transient_cache_flush() {

		// Processing

			delete_transient( self::$transient_cache_key );

	} // /transient_cache_flush

}
