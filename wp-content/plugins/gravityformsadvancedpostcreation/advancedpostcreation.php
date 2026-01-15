<?php
/**
Plugin Name: Gravity Forms Advanced Post Creation Add-On
Plugin URI: https://gravityforms.com
Description: Allows you to create new posts through Gravity Forms.
Version: 1.5.0
Author: Gravity Forms
Author URI: https://gravityforms.com
Text Domain: gravityformsadvancedpostcreation
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2009 - 2025 Rocketgenius, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 **/

defined( 'ABSPATH' ) || die();

define( 'GF_ADVANCEDPOSTCREATION_VERSION', '1.5.0' );

// If Gravity Forms is loaded, bootstrap the Advanced Post Creation Add-On.
add_action( 'gform_loaded', array( 'GF_PostCreation_Bootstrap', 'load' ), 5 );

/**
 * Class GF_PostCreation_Bootstrap
 *
 * Handles the loading of the Advanced Post Creation Add-On and registers with the Add-On framework.
 */
class GF_PostCreation_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Advanced Post Creation Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-advancedpostcreation.php' );

		GFAddOn::register( 'GF_Advanced_Post_Creation' );

	}

}

/**
 * Returns an instance of the GF_Advanced_Post_Creation class
 *
 * @see    GF_Advanced_Post_Creation::get_instance()
 *
 * @return GF_Advanced_Post_Creation
 */
function gf_advancedpostcreation() {
	return GF_Advanced_Post_Creation::get_instance();
}
