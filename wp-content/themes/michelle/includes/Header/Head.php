<?php
/**
 * HTML head component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Header;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Head implements Component_Interface {

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

				add_action( 'wp_head', __CLASS__ . '::head', 0 );
				add_action( 'wp_head', __CLASS__ . '::link_pingback', 1 );

	} // /init

	/**
	 * HTML head.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function head() {

		// Processing

			get_template_part( 'templates/parts/header/head' );

	} // /head

	/**
	 * Add a pingback URL auto-discovery header for single posts, pages, or attachments.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function link_pingback() {

		// Output

			if (
				is_singular()
				&& pings_open()
			) {
				echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
			}

	} // /link_pingback

}
