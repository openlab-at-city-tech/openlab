<?php
/*
Plugin Name: 	Gravity Forms Directory
Plugin URI: 	https://katz.co/gravity-forms-addons/
Description: 	Turn <a href="https://katz.si/gravityforms">Gravity Forms</a> into a great WordPress directory...and more!
Author: 		Katz Web Services, Inc.
Version: 		4.2
Author URI:		https://gravityview.co
Text Domain:    gravity-forms-addons
License:		GPLv2 or later
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html

Copyright 2020 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
if ( ! defined( 'GF_DIRECTORY_VERSION' ) ) {
	define( 'GF_DIRECTORY_VERSION', '4.1.3' );
}
if ( ! defined( 'GF_DIRECTORY_URL' ) ) {
	define( 'GF_DIRECTORY_URL', plugins_url( '/', __FILE__ ) );
}
if ( ! defined( 'GF_DIRECTORY_PATH' ) ) {
	define( 'GF_DIRECTORY_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'GF_DIRECTORY_PLUGIN_BASENAME' ) ) {
	define( 'GF_DIRECTORY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'GF_DIRECTORY_FILE' ) ) {
	define( 'GF_DIRECTORY_FILE', __FILE__ );
}

define( 'GF_DIRECTORY_MIN_GF_VERSION', '2.4' );

if ( ! gf_directory_check_dependancy() ) {
	return;
}

/**
* Check if Gravity Forms is installed.
*
* @return void
*/
function gf_directory_check_dependancy() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	// check if dependency is met.
	if ( ! is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		unset( $_GET['activate'] );
		add_action( 'admin_notices', 'gf_directory_dependancy_notice' );
		return false;
	}
	return true;
}

/**
* Outputs a loader warning notice.
*
* @return void
*/
function gf_directory_dependancy_notice() {
	echo sprintf( '<div class="error"><p>%s</p></div>', __( 'Plugin deactivated - To make <strong>Gravity Forms Directory</strong> plugin work, you need to install and activate Gravity Forms plugin first.', 'gravity-forms-addons' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gf-directory-activator.php
 */
function activate_gf_directory() {
	require_once GF_DIRECTORY_PATH . 'includes/class-gf-directory-activator.php';
	GFDirectory_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gf-directory-deactivator.php
 */
function deactivate_gf_directory() {
	require_once GF_DIRECTORY_PATH . 'includes/class-gf-directory-deactivator.php';
	GFDirectory_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gf_directory' );
register_deactivation_hook( __FILE__, 'deactivate_gf_directory' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gf-directory.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-gf-directory-shortcode.php';


/**
 * Main instance of GFDirectory.
 *
 * Returns the main instance of the class Instance.
 *
 * @since  3.0.0
 *
 * @return object GFDirectory
 */
function gfdirectory_class_instance() {
	return GFDirectory::get_instance();
}

$gf_directory = gfdirectory_class_instance();
GFDirectory_Shortcode::get_instance();

/* Ending ?> left out intentionally */
