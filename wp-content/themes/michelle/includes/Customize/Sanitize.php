<?php
/**
 * Sanitization class.
 *
 * @link  https://github.com/WPTRT/code-examples/blob/master/customizer/sanitization-callbacks.php
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Tool\Google_Fonts;
use WP_Customize_Setting;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Sanitize {

	/**
	 * Sanitize checkbox.
	 *
	 * Sanitization callback for checkbox type controls.
	 * This callback sanitizes `$checked` as a boolean value, either TRUE or FALSE.
	 *
	 * @since  1.0.0
	 *
	 * @param  bool $value
	 *
	 * @return  bool
	 */
	public static function checkbox( bool $value ): bool {

		// Output

			return (bool) $value;

	} // /checkbox

	/**
	 * Sanitize select/radio.
	 *
	 * Sanitization callback for select and radio type controls.
	 * This callback sanitizes `$value` against provided array of `$choices`.
	 * The `$choices` has to be associated array!
	 *
	 * @since  1.0.0
	 *
	 * @param  string $value
	 * @param  mixed  $option
	 *
	 * @return  string
	 */
	public static function select( string $value, $option = null ): string {

		// Variables

			$option = self::get_option_args( $option );


		// Output

			return ( isset( $option['choices'][ $value ] ) ) ? ( esc_attr( $value ) ) : ( esc_attr( $option['default'] ) );

	} // /select

	/**
	 * Sanitize array.
	 *
	 * Sanitization callback for multiselect type controls.
	 * This callback sanitizes `$value` against provided array of `$choices`.
	 * The `$choices` has to be associated array!
	 * Returns an array of values.
	 *
	 * @since  1.0.0
	 *
	 * @param  string|array $value
	 * @param  mixed        $option
	 *
	 * @return  array
	 */
	public static function array_value( $value, $option = null ): array {

		// Variables

			$option = self::get_option_args( $option );

			/**
			 * If we get a string in `$value`,
			 * split it to array using `,` as delimiter.
			 */
			$value = ( is_string( $value ) ) ? ( explode( ',', (string) $value ) ) : ( (array) $value );


		// Requirements check

			if ( empty( $option['choices'] ) ) {
				return array();
			}


		// Processing

			foreach ( $value as $key => $single_value ) {
				if ( ! array_key_exists( $single_value, $option['choices'] ) ) {
					unset( $value[ $key ] );
					continue;
				}

				$value[ $key ] = esc_attr( $single_value );
			}


		// Output

			return $value;

	} // /array_value

	/**
	 * Sanitize fonts.
	 *
	 * Sanitization callback for `font-family` CSS property value.
	 * Allows only alphanumeric characters, spaces, commas, underscores,
	 * dashes, single/double quote inside the `$value`.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $value
	 * @param  mixed  $option
	 *
	 * @return  string
	 */
	public static function fonts( string $value, $option = null ): string {

		// Variables

			$option = self::get_option_args( $option );


		// Processing

			$value = trim( preg_replace( '/[^a-zA-Z0-9 ,_\-\'\"]+/', '', $value ) );


		// Output

			return ( $value ) ? ( $value ) : ( $option['default'] );

	} // /fonts

	/**
	 * Sanitize float.
	 *
	 * @since  1.0.0
	 *
	 * @param  float|int|string $value
	 *
	 * @return  float
	 */
	public static function float( $value ): float {

		// Output

			return (float) $value;

	} // /float

	/**
	 * CSS: Sanitize pixel value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_px( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, 'px', 'absint' );

	} // /css_px

	/**
	 * CSS: Sanitize percentage value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_percent( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, '%' );

	} // /css_percent

	/**
	 * CSS: Sanitize `rem` unit value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_rem( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, 'rem' );

	} // /css_rem

	/**
	 * CSS: Sanitize `em` unit value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_em( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, 'em' );

	} // /css_em

	/**
	 * CSS: Sanitize `vh` unit value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_vh( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, 'vh' );

	} // /css_vh

	/**
	 * CSS: Sanitize `vw` unit value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_vw( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, 'vw' );

	} // /css_vw

	/**
	 * CSS: Sanitize degrees value.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $value
	 *
	 * @return  string
	 */
	public static function css_deg( int $value ): string {

		// Output

			return self::get_number_with_suffix( $value, 'deg', 'absint' );

	} // /css_deg

	/**
	 * CSS: Sanitize fonts.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $fonts
	 *
	 * @return  string
	 */
	public static function css_fonts( string $fonts ): string {

		// Variables

			$css_comment  = '';
			$system_fonts = array(
				'-apple-system',
				'BlinkMacSystemFont',
				'"Segoe UI"',
				'Roboto',
				'Oxygen-Sans',
				'Ubuntu',
				'Cantarell',
				'"Helvetica Neue"',
				'sans-serif',
			);


		// Processing

			$fonts = explode( ',', (string) self::fonts( $fonts ) );

			foreach ( $fonts as $key => $family ) {
				$family = trim( $family, "\"' \t\n\r\0\x0B" );

				if ( 'system' === $family ) {
					$family = implode( ', ', $system_fonts );
				} elseif ( strpos( $family, ' ' ) ) {
					$family = '"' . $family . '"';
				}

				$fonts[ $key ] = $family;
			}

			$fonts = implode( ', ', $fonts );

			// Optional CSS debug comment at the end of font-family declaration.
			if (
				defined( 'WP_DEBUG' ) && WP_DEBUG
				&& ! empty( $css_comment )
			) {
				$fonts .= ' /* ' . $css_comment . ' */';
			}


		// Output

			return $fonts;

	} // /css_fonts

	/**
	 * CSS: Sanitize image URL.
	 *
	 * @since  1.0.0
	 *
	 * @param  array|int|string $image  Could be a URL, numeric image ID or an array with `id` image ID key.
	 *
	 * @return  string
	 */
	public static function css_image_url( $image ): string {

		// Variables

			$value = 'none';


		// Processing

			if ( is_array( $image ) && isset( $image['id'] ) ) {
				$image = absint( $image['id'] );
			}

			if ( is_numeric( $image ) ) {
				$image = wp_get_attachment_image_src( absint( $image ), 'full' );
				$image = $image[0];
			}

			if ( ! empty( $image ) ) {
				$value = 'url("' . esc_url_raw( $image ) . '")';
			}


		// Output

			return $value;

	} // /css_image_url

	/**
	 * CSS: Sanitize `background-repeat` checkbox.
	 *
	 * Available values:
	 * - TRUE: CSS value of `repeat`,
	 * - FALSE: CSS value of `no-repeat`.
	 *
	 * @since  1.0.0
	 *
	 * @param  bool|string $repeat
	 *
	 * @return  string
	 */
	public static function css_checkbox_background_repeat( $repeat ): string {

		// Processing

			if ( ! is_string( $repeat ) ) {
				$repeat = ( $repeat ) ? ( 'repeat' ) : ( 'no-repeat' );
			}


		// Output

			return (string) $repeat;

	} // /css_checkbox_background_repeat

	/**
	 * CSS: Sanitize `background-attachment` checkbox.
	 *
	 * Available values:
	 * - TRUE: CSS value of `fixed`,
	 * - FALSE: CSS value of `scroll`.
	 *
	 * @since  1.0.0
	 *
	 * @param  bool|string $attachment
	 *
	 * @return  string
	 */
	public static function css_checkbox_background_attachment( $attachment ): string {

		// Processing

			if ( ! is_string( $attachment ) ) {
				$attachment = ( $attachment ) ? ( 'fixed' ) : ( 'scroll' );
			}


		// Output

			return (string) $attachment;

	} // /css_checkbox_background_attachment



	// Getters:

		/**
		 * Option setup args parser.
		 *
		 * @since  1.0.0
		 *
		 * @param  array|WP_Customize_Setting $option
		 *
		 * @return  mixed
		 */
		public static function get_option_args( $option ) {

			// Variables

				$args = array(
					'default' => '',
					'choices' => array(),
				);


			// Processing

				if ( $option instanceof WP_Customize_Setting ) {
					$args['default'] = $option->default;
					$args['choices'] = $option->manager->get_control( $option->id )->choices;
				} elseif ( is_array( $option ) ) {
					$args = array_merge( $args, $option );
				}


			// Output

				return $args;

		} // /get_option_args

		/**
		 * CSS: Get numeric value with string suffix.
		 *
		 * @since  1.0.0
		 *
		 * @param  number $value
		 * @param  string $suffix
		 * @param  string $sanitize
		 *
		 * @return  string
		 */
		public static function get_number_with_suffix( $value, string $suffix = '%', string $sanitize = 'absint' ): string {

			// Output

				if ( is_callable( $sanitize ) ) {
					return call_user_func( $sanitize, $value ) . trim( $suffix );
				} else {
					return '';
				}

		} // /get_number_with_suffix

}
