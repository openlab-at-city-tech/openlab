<?php
/**
 * Plugin compatibility/integration component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.5
 */

namespace WebManDesign\Michelle\Plugin;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.0.5
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Loading plugins:

				if ( class_exists( 'OCDI_Plugin' ) && is_admin() ) {
					One_Click_Demo_Import\Component::init();
				}

				if ( class_exists( 'FLBuilder' ) ) {
					Beaver_Builder\Component::init();
				}

	} // /init

}
