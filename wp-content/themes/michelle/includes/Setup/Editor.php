<?php
/**
 * Post editor setup class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.5.1
 */

namespace WebManDesign\Michelle\Setup;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Editor implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				// Loading late enough so we can use `get_background_color()` below.
				add_action( 'after_setup_theme', __CLASS__ . '::after_setup_theme', 20 );

	} // /init

	/**
	 * After setup theme.
	 *
	 * @since    1.0.0
	 * @version  1.5.1
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {

		// Processing

			// Colors.
			add_theme_support( 'editor-color-palette', self::get_color_palette() );

			// Typography.
			add_theme_support( 'editor-font-sizes', self::get_font_sizes() );
			add_theme_support( 'custom-line-height' );

			// Alignment.
			add_theme_support( 'align-wide' );

			// Others.
			add_theme_support( 'custom-units' );
			add_theme_support( 'custom-spacing' );

			// Experimental
			/**
			 * Check `--wp--style--color--link` CSS variable.
			 * Does not work in WP 5.9 without `theme.json`,
			 * which on the other hand causes so much more issues...
			 */
			add_theme_support( 'link-color' );

			/**
			 * WP6.3
			 * @link  https://make.wordpress.org/core/2023/07/18/miscellaneous-developer-changes-in-wordpress-6-3/
			 */
			add_theme_support( 'link-color' );
			add_theme_support( 'border' );

	} // /after_setup_theme

	/**
	 * Get color palette setup array.
	 *
	 * Theme mod color classes:
	 * - .has-{$palette-slug}-color
	 * - .has-{$palette-slug}-background-color
	 *
	 * These should be styled in the theme stylesheet already,
	 * so no need to output any inline CSS code on front-end.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  array
	 */
	public static function get_color_palette(): array {

		// Variables

			$palette = array();


		// Processing

			foreach ( (array) Customize\Colors::get() as $color ) {
				$palette[] = array(
					'name'  => $color['name'],
					// (Though block editor automatically changes "_" to "-", we play safe here.)
					'slug'  => str_replace( '_', '-', $color['slug'] ),
					'color' => maybe_hash_hex_color( $color['color'] ),
				);
			}


		// Output

			/**
			 * Filters editor color palette setup array.
			 *
			 * @link  https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#block-color-palettes
			 *
			 * @since  1.0.0
			 *
			 * @param  array $palette
			 */
			return (array) apply_filters( 'michelle/setup/editor/get_color_palette', $palette );

	} // /get_color_palette

	/**
	 * Get font sizes setup array.
	 *
	 * These are set in `em` units within the theme stylesheet,
	 * so no need to output any inline CSS code on front-end.
	 *
	 * @since    1.0.0
	 * @version  1.3.11
	 *
	 * @return  array
	 */
	public static function get_font_sizes(): array {

		// Variables

			$base_font_size   = Customize\Mod::get( 'typography_size_html' );
			$typography_ratio = 1.333;


		// Output

			/**
			 * Filters editor font sizes setup array.
			 *
			 * @link  https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#block-font-sizes
			 *
			 * @since  1.0.0
			 *
			 * @param  array $sizes
			 */
			return (array) apply_filters( 'michelle/setup/editor/get_font_sizes', array(

				array(
					'name'      => esc_html_x( 'S (Small)', 'Font size label.', 'michelle' ),
					'shortName' => esc_html_x( 'S', 'Font size label abbreviation.', 'michelle' ),
					'size'      => round( $base_font_size * pow( $typography_ratio, -1 ) ),
					'slug'      => 'small',
				),

				array(
					'name'      => esc_html_x( 'M (Medium, normal)', 'Font size.', 'michelle' ),
					'shortName' => esc_html_x( 'M', 'Font size label abbreviation.', 'michelle' ),
					'size'      => $base_font_size,
					'slug'      => 'normal', // Can not use empty value here as that would cause inline styles being applied.
				),

				array(
					'name'      => esc_html_x( 'L (Large)', 'Font size.', 'michelle' ),
					'shortName' => esc_html_x( 'L', 'Font size label abbreviation.', 'michelle' ),
					'size'      => round( $base_font_size * $typography_ratio ),
					'slug'      => 'large',
				),

				array(
					'name'      => esc_html_x( 'XL (Extra Large)', 'Font size.', 'michelle' ),
					'shortName' => esc_html_x( 'XL', 'Font size label abbreviation.', 'michelle' ),
					'size'      => round( $base_font_size * pow( $typography_ratio, 2 ) ),
					'slug'      => 'extra-large',
				),

				array(
					'name' => esc_html_x( 'Huge', 'Font size.', 'michelle' ),
					'size' => round( $base_font_size * pow( $typography_ratio, 5 ) ),
					'slug' => 'huge',
				),

				array(
					'name' => esc_html_x( 'Gigantic', 'Font size.', 'michelle' ),
					'size' => round( $base_font_size * pow( $typography_ratio, 6 ) ),
					'slug' => 'gigantic',
				),

			) );

	} // /get_font_sizes

}
