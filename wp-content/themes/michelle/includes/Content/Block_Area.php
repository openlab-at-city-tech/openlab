<?php
/**
 * Block area component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.6
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Block_Area implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.6
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'michelle/content/404', __CLASS__ . '::error_404' );

				add_action( 'tha_footer_top', __CLASS__ . '::footer' );

	} // /init

	/**
	 * Get block area post type key.
	 *
	 * @since  1.3.0
	 *
	 * @return  string
	 */
	public static function get_post_type(): string {

		// Output

			/**
			 * Filters block area post type key.
			 *
			 * @since  1.3.0
			 *
			 * @param  string $post_type
			 */
			return (string) apply_filters( 'michelle/content/block_area/get_post_type', 'wp_block' );

	} // /get_post_type

	/**
	 * Site footer content.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function footer() {

		// Output

			get_template_part( 'templates/parts/block/area/footer' );

	} // /footer

	/**
	 * Error 404 page content.
	 *
	 * @since    1.0.6
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function error_404() {

		// Output

			get_template_part( 'templates/parts/block/area/404' );

	} // /error_404

}
