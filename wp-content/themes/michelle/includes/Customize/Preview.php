<?php
/**
 * Customize preview class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Assets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Preview implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'customize_preview_init', __CLASS__ . '::assets' );

	} // /init

	/**
	 * Customizer preview assets enqueue.
	 *
	 * @since    1.0.0
	 * @version  1.2.0
	 *
	 * @return  void
	 */
	public static function assets() {

		// Processing

			// Using `wp_enqueue_scripts` action for late enqueue.
			add_action( 'wp_enqueue_scripts', function() {
				Assets\Factory::style_enqueue( array(
					'handle'   => 'michelle-customize-preview',
					'src'      => get_theme_file_uri( 'assets/css/customize-preview.css' ),
					'inline'   => '/* JS CSS */', // Produces `#michelle-customize-preview-inline-css`.
					'add_data' => array(
						'precache' => true,
					),
				) );
			}, 9999 );

			Assets\Factory::script_enqueue( array(
				'handle'   => 'michelle-customize-preview',
				'src'      => get_theme_file_uri( 'assets/js/customize-preview.min.js' ),
				'deps'     => array( 'jquery', 'customize-preview' ),
				'add_data' => array(
					'precache' => true,
				),
			) );

	} // /assets

	/**
	 * Returns customizer JavaScript.
	 *
	 * This function automatically outputs theme customizer preview JavaScript for each theme option,
	 * where the `preview_js` property is set.
	 * Generated CSS styles are added to `#michelle-customize-preview-inline-css` inline style tag.
	 * It is possible to set up a custom JS action, not just CSS styles change. That can be used
	 * to trigger a class on an element, for example.
	 * If `preview_js => false` is set, the change of the theme option won't trigger the customizer
	 * preview refresh. This is useful for theme options that are not front-end related.
	 *
	 * The actual JavaScript is outputted in the footer of the page.
	 *
	 * @example
	 *   'preview_js' => array(
	 *
	 *     // Setting CSS styles:
	 *     'css' => array(
	 *
	 *       // CSS variables (the `[[id]]` gets replaced with option ID)
	 *       ':root' => array(
	 *         '--[[id]]',
	 *       ),
	 *       ':root' => array(
	 *         array(
	 *           'property' => '--[[id]]',
	 *           'suffix'   => 'px',
	 *         ),
	 *       ),
	 *
	 *       // Sets the whole value to the `css-property-name` of the `selector`
	 *       'selector' => array(
	 *         'background-color',...
	 *       ),
	 *
	 *       // Sets the `css-property-name` of the `selector` with specific settings
	 *       'selector' => array(
	 *         array(
	 *           'property'         => 'text-shadow',
	 *           'prefix'           => '0 1px 1px rgba(',
	 *           'suffix'           => ', .5)',
	 *           'process_callback' => 'michelle.Customize.hexToRgb',
	 *           'custom'           => '0 0 0 1em [[value]] ), 0 0 0 2em transparent, 0 0 0 3em [[value]]',
	 *         ),...
	 *       ),
	 *
	 *       // Replaces "@" in `selector` for `selector-replace-value` (such as "@ h2, @ h3" to ".footer h2, .footer h3")
	 *       'selector' => array(
	 *         'selector_replace' => 'selector-replace-value',
	 *         'selector_before'  => '@media (min-width: 80em) {',
	 *         'selector_after'   => '}',
	 *         'background-color',...
	 *       ),
	 *
	 *     ),
	 *
	 *     // And/or setting custom JavaScript:
	 *     'custom' => 'JavaScript here', // Such as "$( '.site-header' ).toggleClass( 'sticky' );"
	 *
	 *   );
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function get_js() {

		// Pre

			/**
			 * Bypass filter for Michelle_Customize::preview_scripts().
			 *
			 * Returning a non-false value will short-circuit the method,
			 * returning the passed value instead.
			 *
			 * @since  1.0.0
			 *
			 * @param  mixed $pre  Default: false. If not false, method returns this value as string.
			 */
			$pre = apply_filters( 'pre/michelle/customize/preview/get_js', false );

			if ( false !== $pre ) {
				return (string) $pre;
			}


		// Variables

			$options = Options::get();

			ksort( $options );

			$output = $output_single = '';


		// Processing

			if ( is_array( $options ) && ! empty( $options ) ) {
				foreach ( $options as $option ) {
					if ( isset( $option['preview_js'] ) && is_array( $option['preview_js'] ) ) {
						$option_id = sanitize_title( $option['id'] );

						$output_single  = "wp.customize("  . PHP_EOL;
						$output_single .= "\t" . "'" . $option_id . "',"  . PHP_EOL;
						$output_single .= "\t" . "function( value ) {"  . PHP_EOL;
						$output_single .= "\t\t" . 'value.bind( function( to, from ) {' . PHP_EOL;

						// CSS.
						if ( isset( $option['preview_js']['css'] ) ) {

							$output_single .= "\t\t\t" . "var newCSS = '';" . PHP_EOL;

							// Empty color value fix.
							if ( 'color' === $option['type'] ) {
								$output_single .= "\t\t\t" . "if ( '' === to ) to = 'transparent';" . PHP_EOL;
							}

							foreach ( $option['preview_js']['css'] as $selector => $properties ) {
								if ( is_array( $properties ) ) {
									$output_single_css = $selector_before = $selector_after = '';

									foreach ( $properties as $key => $property ) {

										// Selector setup:

											if ( 'selector_replace' === $key ) {
												if ( is_array( $property ) ) {
													$selector_replaced = array();
													foreach ( $property as $replace ) {
														$selector_replaced[] = str_replace( '@', (string) $replace, $selector );
													}
													$selector = implode( ', ', $selector_replaced );
												} else {
													$selector = str_replace( '@', (string) $property, $selector );
												}
												continue;
											}

											if ( 'selector_before' === $key ) {
												$selector_before = $property;
												continue;
											}

											if ( 'selector_after' === $key ) {
												$selector_after = $property;
												continue;
											}

										// CSS properties setup:

											if ( ! is_array( $property ) ) {
												$property = array( 'property' => (string) $property );
											}

											$property = wp_parse_args( (array) $property, array(
												'custom'           => '',
												'prefix'           => '',
												'process_callback' => '',
												'property'         => '',
												'suffix'           => '',
											) );

											// Replace `[[id]]` placeholder with an option ID.
											$property['property'] = str_replace(
												'[[id]]',
												$option_id,
												$property['property']
											);

											$value = ( empty( $property['process_callback'] ) ) ? ( 'to' ) : ( trim( $property['process_callback'] ) . '( to )' );

											if ( empty( $property['custom'] ) ) {
												$output_single_css .= $property['property'] . ": " . $property['prefix'] . "' + " . esc_attr( $value ) . " + '" . $property['suffix'] . "; ";
											} else {
												$output_single_css .= $property['property'] . ": " . str_replace( '[[value]]', "' + " . esc_attr( $value ) . " + '", $property['custom'] ) . "; ";
											}

									}

									$output_single .= "\t\t\t" . "newCSS += '" . $selector_before . $selector . " { " . $output_single_css . "}" . $selector_after . " ';" . PHP_EOL;
								}
							}

							$output_single .= PHP_EOL . "\t\t\t" . "michellePreviewStyleTag.append( newCSS );" . PHP_EOL;
						}

						// Custom JS.
						if ( isset( $option['preview_js']['custom'] ) ) {
							$output_single .= "\t\t" . $option['preview_js']['custom'] . PHP_EOL;
						}

						$output_single .= "\t\t" . '} );' . PHP_EOL;
						$output_single .= "\t" . '}'. PHP_EOL;
						$output_single .= ');'. PHP_EOL;

						/**
						 * Filters single customizer theme option preview JavaScript code.
						 *
						 * The dynamic portion of the hook name, `$option_id`, refers to the single theme
						 * option ID. For example, 'color_accent', 'color_accent_text', and so on depending
						 * on the theme options.
						 *
						 * @since  1.0.0
						 *
						 * @param  string $output_single
						 */
						$output .= (string) apply_filters( "michelle/customize/preview/js/option_{$option_id}", $output_single );
					}
				}
			}

			$output = trim( $output );


		// Output

			if ( $output ) {
				/**
				 * Filters final output of customizer theme options preview JavaScript code.
				 *
				 * @since  1.0.0
				 *
				 * @param  string $output
				 */
				return (string) apply_filters( 'michelle/customize/preview/js',
					'( function( $ ) {' . PHP_EOL.PHP_EOL
					. '"use strict";' . PHP_EOL.PHP_EOL
					. 'var michellePreviewStyleTag = $( "#michelle-customize-preview-inline-css" );' . PHP_EOL.PHP_EOL
					. trim( $output ) . PHP_EOL.PHP_EOL // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					. '} )( jQuery );'
				);
			}

	} // /get_js

}
