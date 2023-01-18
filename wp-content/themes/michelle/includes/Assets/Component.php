<?php
/**
 * Assets component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Assets;

use WebManDesign\Michelle\Component_Interface;

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

			// Theme assets.
			Styles::init();
			Scripts::init();
			// Post editor assets.
			Editor::init();

			// Filters

				add_filter( 'michelle/assets/esc_css', 'wp_strip_all_tags' );

	} // /init

}
