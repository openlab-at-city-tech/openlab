<?php
/**
 * Theme options RGBA setup component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.5
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class RGBA implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Filters

				add_filter( 'michelle/customize/options/get', __CLASS__ . '::customize_preview', 15 );

				add_filter( 'michelle/customize/rgba/get_alphas', __CLASS__ . '::alphas' );

				add_filter( 'michelle/customize/css_variables/get_array/partial', __CLASS__ . '::css_variable', 10, 3 );

	} // /init

	/**
	 * Get alpha values (%) for CSS rgba() colors.
	 *
	 * @since  1.0.0
	 *
	 * @return  array
	 */
	public static function get_alphas(): array {

		// Output

			/**
			 * Sets an alpha value (%) for CSS rgba() color of a specific theme options.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $alphas  Array of theme option id and alpha value in percent pairs.
			 */
			return (array) apply_filters( 'michelle/customize/rgba/get_alphas', array() );

	} // /get_alphas

	/**
	 * Sets alpha values (%) for CSS rgba() colors.
	 *
	 * @since    1.0.0
	 * @version  1.0.5
	 *
	 * @param  array $alphas
	 *
	 * @return  array
	 */
	public static function alphas( array $alphas ): array {

		// Processing

			$alphas['color_body_text'] = array(
				'css_var_name' => '--color_body_border',
				'alpha'        => 'var(--border_opacity)',
			);


		// Output

			return (array) $alphas;

	} // /alphas

	/**
	 * Customize preview RGBA colors.
	 *
	 * @since    1.0.0
	 * @version  1.0.5
	 *
	 * @param  array $options
	 *
	 * @return  array
	 */
	public static function customize_preview( array $options ): array {

		// Variables

			$alphas = self::get_alphas();


		// Processing

			foreach ( $options as $key => $option ) {
				if (
					isset( $option['css_var'] )
					&& isset( $alphas[ $option['id'] ] )
				) {
					$args = $alphas[ $option['id'] ];

					$alpha = $args['alpha'];
					if ( is_integer( $alpha ) ) {
						$alpha = (float) ( absint( $alpha ) / 100 );
					}

					$options[ $key ]['preview_js']['css'][':root'][] = array(
						'property'         => $args['css_var_name'],
						'prefix'           => 'rgba(',
						'suffix'           => ',' . $alpha . ')',
						'process_callback' => 'michelle.Customize.hexToRgb',
					);
				}
			}


		// Output

			return (array) $options;

	} // /customize_preview

	/**
	 * Adding RGBA CSS variables.
	 *
	 * @since    1.0.0
	 * @version  1.0.5
	 *
	 * @param  array  $css_vars
	 * @param  array  $option
	 * @param  string $value
	 *
	 * @return  array
	 */
	public static function css_variable( array $css_vars = array(), array $option = array(), string $value = '' ): array {

		// Variables

			$alphas = self::get_alphas();


		// Processing

			if (
				isset( $option['id'] )
				&& isset( $alphas[ $option['id'] ] )
			) {
				$args = $alphas[ $option['id'] ];
				$css_vars[ $args['css_var_name'] ] = esc_attr( self::color_hex_to_rgba( (string) $value, $args['alpha'] ) );
			}


		// Output

			return (array) $css_vars;

	} // /css_variable

	/**
	 * Get rgb() or rgba() from hex color.
	 *
	 * @since    1.0.0
	 * @version  1.0.5
	 *
	 * @link  http://php.net/manual/en/function.hexdec.php
	 *
	 * @param  string     $hex
	 * @param  string|int $alpha  [0-100] or CSS variable.
	 *
	 * @return  string
	 */
	public static function color_hex_to_rgba( string $hex, $alpha = 100 ): string {

		// Variables

			$output = 'rgba(';

			$rgb = array();

			$hex = preg_replace( '/[^0-9A-Fa-f]/', '', (string) $hex );
			$hex = substr( $hex, 0, 6 );


		// Processing

			// Converting hex color into rgb.
			$color    = (int) hexdec( $hex );
			$rgb['r'] = (int) 0xFF & ( $color >> 0x10 );
			$rgb['g'] = (int) 0xFF & ( $color >> 0x8 );
			$rgb['b'] = (int) 0xFF & $color;
			$output  .= implode( ',', $rgb );

			// Using alpha (rgba)?
			if ( is_integer( $alpha ) ) {
				$output .= ',' . ( $alpha / 100 );
			} else {
				$output .= ',' . $alpha;
			}

			// Closing opening bracket.
			$output .= ')';


		// Output

			return (string) $output;

	} // /color_hex_to_rgba

}
