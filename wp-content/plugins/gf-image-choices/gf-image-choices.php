<?php
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__( 'Gravity Forms Image Choices requires PHP 7.4 or higher. Please upgrade your PHP version.', 'gf_image_choices' );
        echo '</p></div>';
    } );
    return;
}
/*
Plugin Name: Gravity Forms Image Choices
Plugin URI: https://jetsloth.com/gravity-forms-image-choices/
Description: Easily add images as choices for Radio Buttons or Checkboxes fields in your Gravity Forms, including Survey, Quiz, Product and Option fields that have their field type set to Radio Buttons or Checkboxes
Author: JetSloth
Version: 1.6.16
Requires at least: 3.5
Tested up to: 6.8.1
Author URI: https://jetsloth.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
SPDX-License-Identifier: GPL-2.0-or-later
Text Domain: gf_image_choices
Requires PHP: 7.4
*/

/*
SPDX-License-Identifier: GPL-2.0-or-later
Copyright (c) 2025 JetSloth

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; see license.txt. If not, see https://www.gnu.org/licenses/.

Assets & trademarks: See readme.txt (“Assets & trademarks”) for details on non-GPL assets and trademarks.
*/

define('GFIC_VERSION', '1.6.16');
define('GFIC_HOME', 'https://jetsloth.com');
define('GFIC_NAME', 'Gravity Forms Image Choices');
define('GFIC_SLUG', 'gf-image-choices');
define('GFIC_AUTHOR', 'JetSloth');
define('GFIC_TIMEOUT', 20);
define('GFIC_SSL_VERIFY', false);

define('GFIC_SPLASH_ID', 'gfic_1_5_splash');
define('GFIC_SPLASH_URL', 'https://jetsloth.com/splash-page/image-choices-1-5/');

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

	if ( ! class_exists( 'Image_Choices_Plugin_Updater' ) ) {
		// load our custom updater if it doesn't already exist
		include_once( dirname( __FILE__ ) . '/edd/Image_Choices_Plugin_Updater.php' );
	}

	// retrieve the license key
	//$key = gf_image_choices()->get_plugin_setting( 'gf_image_choices_license_key' );
	$key = gf_image_choices()->get_license_key();
	$license_key = ( !empty($key) ) ? trim( $key ) : "";

	// Disable SSL verification in order to prevent download update failures
	add_filter('edd_sl_api_request_verify_ssl', '__return_false');

	// setup the updater
	$edd_updater = new Image_Choices_Plugin_Updater( GFIC_HOME, __FILE__, array(
			'version'   => GFIC_VERSION,
			'license'   => $license_key,
			'item_name' => GFIC_NAME,
			'author'    => 'JetSloth',
            'beta'      => false,
		)
	);

}
