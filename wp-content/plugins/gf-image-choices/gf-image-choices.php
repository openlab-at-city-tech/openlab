<?php
/*
Plugin Name: Gravity Forms Image Choices
Plugin URI: https://jetsloth.com/gravity-forms-image-choices/
Description: Easily add images as choices for Radio Buttons or Checkboxes fields in your Gravity Forms, including Survey, Quiz, Product and Option fields that have their field type set to Radio Buttons or Checkboxes
Author: JetSloth
Version: 1.4.1
Requires at least: 3.5
Tested up to: 6.2
Author URI: https://jetsloth.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gf_image_choices
*/

/*
	Copyright 2017 JetSloth

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('GFIC_VERSION', '1.4.1');
define('GFIC_HOME', 'https://jetsloth.com');
define('GFIC_NAME', 'Gravity Forms Image Choices');
define('GFIC_SLUG', 'gf-image-choices');
define('GFIC_AUTHOR', 'JetSloth');
define('GFIC_TIMEOUT', 20);
define('GFIC_SSL_VERIFY', false);

define('GFIC_SPLASH_ID', 'gfic_1_4_splash');
define('GFIC_SPLASH_URL', 'https://jetsloth.com/splash-page/image-choices-1-4/');

add_action( 'gform_loaded', array( 'GF_Image_Choices_Bootstrap', 'load' ), 5 );

class GF_Image_Choices_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-image-choices.php' );

		GFAddOn::register( 'GFImageChoices' );
	}
}

function gf_image_choices() {
	if ( ! class_exists( 'GFImageChoices' ) ) {
		return false;
	}

	return GFImageChoices::get_instance();
}


add_action('init', 'gf_image_choices_plugin_updater', 0);
function gf_image_choices_plugin_updater() {

	if (gf_image_choices() === false) {
		return;
	}

	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// load our custom updater if it doesn't already exist
		include_once( dirname( __FILE__ ) . '/inc/EDD_SL_Plugin_Updater.php' );
	}

	// retrieve the license key
	$license_key = trim( gf_image_choices()->get_plugin_setting( 'gf_image_choices_license_key' ) );

	// Disable SSL verification in order to prevent download update failures
	add_filter('edd_sl_api_request_verify_ssl', '__return_false');

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( GFIC_HOME, __FILE__, array(
			'version'   => GFIC_VERSION,
			'license'   => $license_key,
			'item_name' => GFIC_NAME,
			'author'    => 'JetSloth',
            'beta'      => false,
		)
	);

}
