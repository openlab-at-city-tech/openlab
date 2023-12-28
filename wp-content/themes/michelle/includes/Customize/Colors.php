<?php
/**
 * Theme colors component.
 *
 * Theme colors data are being cached in transient as they are global for the website.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Colors implements Component_Interface {

	/**
	 * Name of cached data transient.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public static $transient_cache_colors = 'michelle_cache_theme_colors';

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

				add_action( 'customize_save_after', __CLASS__ . '::transient_cache_flush', 100 );

	} // /init

	/**
	 * Get all theme colors in array and cache them in transient.
	 *
	 * Each option with `palette` attribute is added to editor colors.
	 *
	 * @since  1.0.0
	 *
	 * @return  array
	 */
	public static function get(): array {

		// Requirements check

			// Get the data cached in transient first.
			$colors = array_filter( (array) get_transient( self::$transient_cache_colors ) );

			if ( ! empty( $colors ) ) {
				return $colors;
			}


		// Variables

			$mods          = (array) get_theme_mods();
			$theme_options = Options::get();


		// Processing

			foreach ( $theme_options as $option ) {
				if (
					isset( $option['palette'] )
					&& isset( $option['default'] )
				) {
					$color = ( isset( $mods[ $option['id'] ] ) ) ? ( $mods[ $option['id'] ] ) : ( $option['default'] );
					$colors[ $option['id'] ] = array(
						'id'    => $option['id'],
						'color' => sanitize_hex_color_no_hash( $color ),
						'name'  => ( isset( $option['palette']['name'] ) ) ? ( $option['palette']['name'] ) : ( $option['label'] ),
						'slug'  => ( isset( $option['palette']['slug'] ) ) ? ( $option['palette']['slug'] ) : ( $option['id'] ),
					);
				}
			}

			// Cache the data in transient.
			set_transient( self::$transient_cache_colors, array_filter( $colors ) );


		// Output

			return $colors;

	} // /get

	/**
	 * Flush theme colors array transient cache.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function transient_cache_flush() {

		// Processing

			delete_transient( self::$transient_cache_colors );

	} // /transient_cache_flush

}
