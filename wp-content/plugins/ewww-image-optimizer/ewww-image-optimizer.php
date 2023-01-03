<?php
/**
 * Loader for Standard EWWW IO plugin.
 *
 * This file bootstraps the rest of the EWWW IO plugin after some basic checks.
 *
 * @link https://ewww.io
 * @package EWWW_Image_Optimizer
 */

/*
Plugin Name: EWWW Image Optimizer
Plugin URI: https://wordpress.org/plugins/ewww-image-optimizer/
Description: Smaller Images, Faster Sites, Happier Visitors. Comprehensive image optimization that doesn't require a degree in rocket science.
Author: Exactly WWW
Version: 6.9.3
Requires at least: 5.8
Requires PHP: 7.2
Author URI: https://ewww.io/
License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EWWW_IO_CLOUD_PLUGIN' ) ) {
	define( 'EWWW_IO_CLOUD_PLUGIN', false );
}

// Check the PHP version.
if ( ! defined( 'PHP_VERSION_ID' ) || PHP_VERSION_ID < 70200 ) {
	add_action( 'network_admin_notices', 'ewww_image_optimizer_unsupported_php' );
	add_action( 'admin_notices', 'ewww_image_optimizer_unsupported_php' );
} elseif ( defined( 'EWWW_IMAGE_OPTIMIZER_VERSION' ) ) {
	// Prevent loading both EWWW IO plugins.
	add_action( 'network_admin_notices', 'ewww_image_optimizer_dual_plugin' );
	add_action( 'admin_notices', 'ewww_image_optimizer_dual_plugin' );
} elseif ( false === strpos( add_query_arg( null, null ), 'ewwwio_disable=1' ) ) {
	/**
	 * The full path of the plugin file (this file).
	 *
	 * @var string EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE
	 */
	define( 'EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE', __FILE__ );
	/**
	 * The path of the plugin file relative to the plugins/ folder.
	 *
	 * @var string EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE_REL
	 */
	define( 'EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE_REL', plugin_basename( __FILE__ ) );
	/**
	 * This is the full system path to the plugin folder.
	 *
	 * @var string EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH
	 */
	define( 'EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	/**
	 * This is the full system path to the bundled binaries.
	 *
	 * @var string EWWW_IMAGE_OPTIMIZER_BINARY_PATH
	 */
	define( 'EWWW_IMAGE_OPTIMIZER_BINARY_PATH', plugin_dir_path( __FILE__ ) . 'binaries/' );
	/**
	 * This is the full system path to the plugin images for testing.
	 *
	 * @var string EWWW_IMAGE_OPTIMIZER_IMAGES_PATH
	 */
	define( 'EWWW_IMAGE_OPTIMIZER_IMAGES_PATH', plugin_dir_path( __FILE__ ) . 'images/' );
	if ( ! defined( 'EWWW_IMAGE_OPTIMIZER_TOOL_PATH' ) ) {
		if ( ! defined( 'EWWWIO_CONTENT_DIR' ) ) {
			$ewwwio_content_dir = trailingslashit( WP_CONTENT_DIR ) . trailingslashit( 'ewww' );
			if ( ! is_writable( WP_CONTENT_DIR ) || ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
				$upload_dir = wp_get_upload_dir();
				if ( false === strpos( $upload_dir['basedir'], '://' ) && is_writable( $upload_dir['basedir'] ) ) {
					$ewwwio_content_dir = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( 'ewww' );
				}
			}
			/**
			 * The folder where we store debug logs (among other things) - MUST have a trailing slash.
			 *
			 * @var string EWWWIO_CONTENT_DIR
			 */
			define( 'EWWWIO_CONTENT_DIR', $ewwwio_content_dir );
		}
		/**
		 * The folder where we install optimization tools - MUST have a trailing slash.
		 *
		 * @var string EWWW_IMAGE_OPTIMIZER_TOOL_PATH
		 */
		define( 'EWWW_IMAGE_OPTIMIZER_TOOL_PATH', EWWWIO_CONTENT_DIR );
	} elseif ( ! defined( 'EWWWIO_CONTENT_DIR' ) ) {
		define( 'EWWWIO_CONTENT_DIR', EWWW_IMAGE_OPTIMIZER_TOOL_PATH );
	}

	/**
	 * All the 'unique' functions for the core EWWW IO plugin.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'unique.php' );
	/**
	 * All the 'common' functions for both EWWW IO plugins.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'common.php' );
	/**
	 * All the base functions for our plugins.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-eio-base.php' );
	/**
	 * The various class extensions for background optimization.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-ewwwio-media-background-process.php' );
	/**
	 * EWWW_Image class for working with queued images and image records from the database.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-ewww-image.php' );
	/**
	 * EIO_Backup class for managing image backups.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-eio-backup.php' );
	/**
	 * EWWWIO_Tracking class for reporting anonymous site data.
	 */
	require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-ewwwio-tracking.php' );
	/**
	 * The main function to return a single EIO_Base object to functions elsewhere.
	 *
	 * @return object object|EIO_Base The one true EIO_Base instance.
	 */
	function eio_plugin() {
		// TODO: create an intermediary EIO_Plugin class that inherits from EIO_Base. This
		// can be used for things like defining constants, including other files, and adding hooks.
		if ( method_exists( 'EIO_Base', 'instance' ) ) {
			return EIO_Base::instance();
		}
		return new EIO_Base();
	}
} // End if().

if ( ! function_exists( 'ewww_image_optimizer_unsupported_php' ) ) {
	/**
	 * Display a notice that the PHP version is too old.
	 */
	function ewww_image_optimizer_unsupported_php() {
		echo '<div id="ewww-image-optimizer-warning-php" class="error"><p><a href="https://docs.ewww.io/article/55-upgrading-php" target="_blank" data-beacon-article="5ab2baa6042863478ea7c2ae">' . esc_html__( 'EWWW Image Optimizer requires PHP 7.2 or greater. Newer versions of PHP are significantly faster and much more secure. If you are unsure how to upgrade to a supported version, ask your webhost for instructions.', 'ewww-image-optimizer' ) . '</a></p></div>';
	}

	/**
	 * Display a notice when both the standard and cloud plugins are active.
	 */
	function ewww_image_optimizer_dual_plugin() {
		echo "<div id='ewww-image-optimizer-warning-double-plugin' class='error'><p><strong>" . esc_html__( 'Only one version of the EWWW Image Optimizer can be active at a time. Please deactivate other copies of the plugin.', 'ewww-image-optimizer' ) . '</strong></p></div>';
	}
}
