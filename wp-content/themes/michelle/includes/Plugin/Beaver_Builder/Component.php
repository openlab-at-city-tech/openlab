<?php
/**
 * Beaver Builder integration component.
 *
 * @link  https://wordpress.org/plugins/beaver-builder-lite-version/
 * @link  https://www.wpbeaverbuilder.com/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Plugin\Beaver_Builder;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize\Colors;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

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

				add_filter( 'fl_builder_upgrade_url', __CLASS__ . '::upgrade_url' );

				add_filter( 'fl_builder_settings_form_defaults', __CLASS__ . '::settings_form_defaults', 10, 2 );

				add_filter( 'fl_builder_color_presets', __CLASS__ . '::color_presets' );
				add_filter( 'pre_update_option__fl_builder_color_presets', __CLASS__ . '::color_presets_save' );

	} // /init

	/**
	 * Upgrade URL.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $url
	 *
	 * @return  string
	 */
	public static function upgrade_url( string $url ): string {

		// Output

			return esc_url( add_query_arg( 'fla', '67', (string) $url ) );

	} // /upgrade_url

	/**
	 * Global settings defaults.
	 *
	 * @since  1.0.0
	 *
	 * @param  $defaults
	 * @param  string $form_type
	 *
	 * @return  object
	 */
	public static function settings_form_defaults( $defaults, string $form_type ) {

		// Processing

			if ( 'global' === $form_type ) {
				$defaults->show_default_heading     = '1';
				$defaults->default_heading_selector = implode( ', ', array(
					'.fl-builder .page-header',
					'.fl-theme-builder-404 .page-header',
					'.fl-theme-builder-archive .page-header',
					'.fl-theme-builder-singular .page-header',
				) );

				$defaults->row_width = $GLOBALS['content_width'];

				$defaults->medium_breakpoint     = 1280;
				$defaults->responsive_breakpoint = 880;
			}


		// Output

			return $defaults;

	} // /settings_form_defaults

	/**
	 * Converts theme colors to Beaver Builder color presets format.
	 *
	 * @uses  WebManDesign\Michelle\Customize\Colors::get()
	 *
	 * @since  1.0.0
	 *
	 * @return  array
	 */
	public static function get_colors(): array {

		// Variables

			$theme_colors = (array) Colors::get();


		// Processing

			$theme_colors = array_column( $theme_colors, 'color' );
			$theme_colors = array_values( $theme_colors );
			$theme_colors = array_unique( $theme_colors );
			asort( $theme_colors );


		// Output

			return $theme_colors;

	} // /get_colors

	/**
	 * Adds color presets generated from theme colors.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $color_presets
	 *
	 * @return  array
	 */
	public static function color_presets( array $color_presets = array() ): array {

		// Variables

			$theme_colors = self::get_colors();


		// Processing

			$color_presets = array_map(
				'sanitize_hex_color_no_hash',
				array_unique( array_merge( (array) $color_presets, $theme_colors ) )
			);
			asort( $color_presets );


		// Output

			return array_values( array_filter( $color_presets ) );

	} // /color_presets

	/**
	 * Prevents issues when saving color presets.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $color_presets
	 *
	 * @return  array
	 */
	public static function color_presets_save( array $color_presets = array() ): array {

		// Output

			return array_values( array_diff( $color_presets, self::get_colors() ) );

	} // /color_presets_save

}
