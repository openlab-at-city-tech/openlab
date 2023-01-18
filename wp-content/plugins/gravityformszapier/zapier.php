<?php
/*
Plugin Name: Gravity Forms Zapier Add-On
Plugin URI: https://gravityforms.com
Description: Integrates Gravity Forms with Zapier, allowing form submissions to be automatically sent to your configured Zaps.
Version: 4.2
Author: Gravity Forms
Author URI: https://gravityforms.com
License: GPL-2.0+
Text Domain: gravityformszapier
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2009-2021 Rocketgenius, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.

*/

defined( 'ABSPATH' ) || die();

// Defines the current version of the Gravity Forms Zapier Add-On
define( 'GF_ZAPIER_VERSION', '4.2' );

// Defines the minimum version of Gravity Forms required to run this version of the add-on.
define( 'GF_ZAPIER_MIN_GF_VERSION', '2.4' );

// Defines the version of the Zapier App that this add-on is designed to support.
define( 'GF_ZAPIER_TARGET_ZAPIER_APP_VERSION', '2.2' );

// After GF is loaded, load the add-on
add_action( 'gform_loaded', array( 'GF_Zapier_Bootstrap', 'load_addon' ), 5 );


/**
 * Loads the Gravity Forms Zapier Add-On Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 4.0
 */
class GF_Zapier_Bootstrap {

	/**
	 * Loads the required files.
	 *
	 * @since 4.0
	 * @access public
	 * @static
	 */
	public static function load_addon() {

		// Requires the class file.
		require_once( plugin_dir_path( __FILE__ ) . '/class-gf-zapier.php' );

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'GF_Zapier' );
	}
}

/**
 * Returns an instance of the GF_Zapier class.
 *
 * @since 4.0
 * @return GF_Zapier An instance of the GF_Zapier class.
 */
function gf_zapier() {
	return GF_Zapier::get_instance();
}

