<?php
/**
Plugin Name: Gravity Forms Dropbox Add-On
Plugin URI: https://gravityforms.com
Description: Integrates Gravity Forms with Dropbox, enabling end users to upload files to Dropbox through Gravity Forms.
Version: 3.0.1
Author: Gravity Forms
Author URI: https://gravityforms.com
License: GPL-2.0+
Text Domain: gravityformsdropbox
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2012-2021 Rocketgenius Inc.

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

define( 'GF_DROPBOX_VERSION', '3.0.1' );

/**
 * Path to Dropbox add-on root folder.
 *
 * @since 2.9
 */
define( 'GF_DROPBOX_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// If Gravity Forms is loaded, bootstrap the Dropbox Add-On.
add_action( 'gform_loaded', array( 'GF_Dropbox_Bootstrap', 'load' ), 5 );

/**
 * Class GF_Dropbox_Bootstrap
 *
 * Handles the loading of the Dropbox Add-On and registers with the Add-On Framework.
 */
class GF_Dropbox_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Dropbox Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-dropbox.php' );

		GFAddOn::register( 'GF_Dropbox' );

	}

}

/**
 * Returns an instance of the GF_Dropbox class
 *
 * @see    GF_Dropbox::get_instance()
 * @return GF_Dropbox
 */
function gf_dropbox() {
	return GF_Dropbox::get_instance();
}
