<?php
/**
 * Broken Link Checker
 *
 * @link              https://wordpress.org/plugins/broken-link-checker/
 * @since             1.0.0
 * @package           broken-link-checker
 *
 * @wordpress-plugin
 * Plugin Name:       Broken Link Checker
 * Plugin URI:        https://wordpress.org/plugins/broken-link-checker/
 * Description:       Checks your blog for broken links and notifies you on the dashboard if any are found.
 * Version:           2.2.4
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            WPMU DEV
 * Author URI:        https://wpmudev.com/
 * Text Domain:       broken-link-checker
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
Broken Link Checker is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Broken Link Checker is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Broken Link Checker. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

namespace WPMUDEV_BLC;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

// Plugin version.
if ( ! defined( 'WPMUDEV_BLC_VERSION' ) ) {
	define( 'WPMUDEV_BLC_VERSION', '2.2.4' );
}

// Define WPMUDEV_BLC_PLUGIN_FILE.
if ( ! defined( 'WPMUDEV_BLC_PLUGIN_FILE' ) ) {
	define( 'WPMUDEV_BLC_PLUGIN_FILE', __FILE__ );
}

// Plugin basename.
if ( ! defined( 'WPMUDEV_BLC_BASENAME' ) ) {
	define( 'WPMUDEV_BLC_BASENAME', plugin_basename( __FILE__ ) );
}

// Plugin directory.
if ( ! defined( 'WPMUDEV_BLC_DIR' ) ) {
	define( 'WPMUDEV_BLC_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin url.
if ( ! defined( 'WPMUDEV_BLC_URL' ) ) {
	define( 'WPMUDEV_BLC_URL', plugin_dir_url( __FILE__ ) );
}
// Assets url.
if ( ! defined( 'WPMUDEV_BLC_ASSETS_URL' ) ) {
	define( 'WPMUDEV_BLC_ASSETS_URL', plugin_dir_url( __FILE__ ) . trailingslashit( 'assets' ) );
}

// Scripts version.
if ( ! defined( 'WPMUDEV_BLC_SCIPTS_VERSION' ) ) {
	define( 'WPMUDEV_BLC_SCIPTS_VERSION', '2.2.4' );
}

// SUI version number used in BLC_SHARED_UI_VERSION and enqueues.
if ( ! defined( 'BLC_SHARED_UI_VERSION_NUMBER' ) ) {
	define( 'BLC_SHARED_UI_VERSION_NUMBER', '2-12-23' );
}

// SUI version used in admin body class.
if ( ! defined( 'BLC_SHARED_UI_VERSION' ) ) {
	define( 'BLC_SHARED_UI_VERSION', 'sui-' . BLC_SHARED_UI_VERSION_NUMBER );
}

// Path to the plugin's legacy directory.
if ( ! defined( 'BLC_DIRECTORY_LEGACY' ) ) {
	define( 'BLC_DIRECTORY_LEGACY', WPMUDEV_BLC_DIR . '/legacy' );
}

// Path to legacy file.
if ( ! defined( 'BLC_PLUGIN_FILE_LEGACY' ) ) {
	//define( 'BLC_PLUGIN_FILE_LEGACY', BLC_DIRECTORY_LEGACY . '/init.php' );
	define( 'BLC_PLUGIN_FILE_LEGACY', BLC_DIRECTORY_LEGACY . '/init.php' );
}

// Autoloader.
require_once plugin_dir_path( __FILE__ ) . 'core/utils/autoloader.php';

/**
 * Run plugin activation hook to setup plugin.
 *
 * @since 2.0.0
 */

// Make sure wpmudev_blc_instance is not already defined.
if ( ! function_exists( 'wpmudev_blc_instance' ) && ! function_exists( 'WPMUDEV_BLC\wpmudev_blc_instance' ) ) {
	/**
	 * Main instance of plugin.
	 *
	 * Returns the main instance of WPMUDEV_BLC to prevent the need to use globals
	 * and to maintain a single copy of the plugin object.
	 * You can simply call WPMUDEV_BLC\instance() to access the object.
	 *
	 * @since  2.0.0
	 *
	 * @return object WPMUDEV_BLC\Core\Loader
	 */
	function wpmudev_blc_instance() {
		return Core\Loader::instance();
	}

	// Init the plugin and load the plugin instance for the first time.
	add_action( 'plugins_loaded', 'WPMUDEV_BLC\\wpmudev_blc_instance' );

	if ( isset( $_GET['wpmudev-hub'] ) && empty( get_option( 'blc_settings' ) ) ) {
		wpmudev_blc_instance();
		do_action( 'wpmudev_blc_plugin_activated' );
	}

	register_activation_hook(
		__FILE__,
		function() {
			Core\Activation::instance();
		}
	);

	register_deactivation_hook(
		__FILE__,
		function() {
			Core\Deactivation::instance();
		}
	);
}

// Load the legacy plugin.
add_action(
	'plugins_loaded',
	function() {
		require 'legacy/init.php';
	},
	11
);
