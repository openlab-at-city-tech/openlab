<?php
/**
 * Theme components initialization.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Variables

			$components = array(
				'Accessibility',
				'Assets',
				'Content',
				'Customize',
				'Entry',
				'Footer',
				'Header',
				'Loop',
				'Menu',
				'Plugin',
				'Setup',
				'Theme_Hook_Alliance',
				'Tool',
				'Welcome',
			);


		// Processing

			// Loading theme components.
			foreach ( $components as $component ) {
				call_user_func( __NAMESPACE__ . "\\{$component}\\Component::init" );
			}

	} // /init

}
