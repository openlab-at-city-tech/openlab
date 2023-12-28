<?php
/**
 * Customized styles component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.12
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Assets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Styles implements Component_Interface {

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

				add_action( 'wp_enqueue_scripts', __CLASS__ . '::inline_styles', MICHELLE_ENQUEUE_PRIORITY + 9 );

				add_action( 'customize_save_after', __CLASS__ . '::customize_timestamp' );

			// Filters

				add_filter( 'tiny_mce_before_init', __CLASS__ . '::inline_styles_classic_editor' );

	} // /init

	/**
	 * Get custom CSS.
	 *
	 * @since  1.0.0
	 *
	 * @return  string
	 */
	public static function get_css(): string {

		// Output

			/**
			 * Filters PHP generated CSS.
			 *
			 * @since  1.0.0
			 *
			 * @param  string $css  CSS code.
			 */
			return (string) apply_filters( 'michelle/customize/styles/get_css', '' );

	} // /get_css

	/**
	 * Get processed CSS variables string.
	 *
	 * @since  1.0.0
	 *
	 * @return  string
	 */
	public static function get_css_variables(): string {

		// Variables

			$css_vars  = '--background_color:' . maybe_hash_hex_color( get_background_color() ) . ';';
			$css_vars .= CSS_Variables::get_string();


		// Processing

			if ( ! empty( $css_vars ) ) {
				$css_vars =
					'/* START CSS variables */'
					. PHP_EOL
					. ':root { '
					. PHP_EOL
					. $css_vars
					. PHP_EOL
					. '}'
					. PHP_EOL
					. '/* END CSS variables */';
			}


		// Output

			return (string) $css_vars;

	} // /get_css_variables

	/**
	 * Enqueue HTML head inline styles.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function inline_styles() {

		// Variables

			$css  = (string) self::get_css_variables();
			$css .= (string) self::get_css();


		// Processing

			if ( ! empty( $css ) ) {
				wp_add_inline_style(
					'michelle',
					(string) Assets\Factory::esc_css( $css, 'customize-styles' )
				);
			}

	} // /inline_styles

	/**
	 * Enqueue inline styles for classic editor.
	 *
	 * Adds styles to the head of the TinyMCE iframe.
	 * Kudos to @Otto42 for the original solution.
	 *
	 * Can not use `esc_js()` below as it uses `_wp_specialchars()` which
	 * converts CSS safe characters into unusable string.
	 *
	 * @since  1.0.0
	 *
	 * @param array $mce_init TinyMCE styles.
	 *
	 * @return  array
	 */
	public static function inline_styles_classic_editor( array $mce_init ): array {

		// Variables

			$css = (string) self::get_css_variables();


		// Processing

			if ( $css ) {
				$css = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $css ) );
				$css = str_replace( "\r", '', $css );
				$css = str_replace( "\n", '\\n', addslashes( $css ) );

				if ( ! isset( $mce_init['content_style'] ) ) {
					$mce_init['content_style'] = $css . ' ';
				} else {
					$mce_init['content_style'] .= ' ' . $css . ' ';
				}
			}


		// Output

			return $mce_init;

	} // /inline_styles_classic_editor

	/**
	 * Customizer save action timestamp.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function customize_timestamp() {

		// Output

			set_theme_mod( '__customize_timestamp', esc_attr( gmdate( 'ymdHis' ) ) );

	} // /customize_timestamp

}
