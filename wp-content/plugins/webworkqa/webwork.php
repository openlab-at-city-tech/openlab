<?php
/*
Plugin Name: WeBWorK Q&A
Version: 1.0.0
Description: Integration between WeBWorK and WordPress
Author: OpenLab at City Tech
Author URI: https://openlab.citytech.cuny.edu
Plugin URI: https://openlab.citytech.cuny.edu/webwork-qa-plugin
Text Domain: webworkqa
Domain Path: /languages
*/

define( 'WEBWORK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WEBWORK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WEBWORK_PLUGIN_VER', '1.0.0' );

/**
 * Bootstrap.
 *
 * Loaded early to avoid race conditions with BuddyPress (bp_init).
 *
 * @since 1.0.0
 */
function webwork_init() {
	if ( version_compare( phpversion(), '5.3', '<' ) ) {
		add_action(
			'admin_notices',
			function() {
				echo '<div class=\"error\"><p>' . esc_html__( 'WeBWorK for WordPress requires PHP 5.3 to function properly. Please upgrade PHP or deactivate WeBWorK for WordPress.', 'webworkqa' ) . '</p></div>';
			}
		);
	}

	if ( version_compare( $GLOBALS['wp_version'], '4.4', '<' ) ) {
		add_action(
			'admin_notices',
			function() {
				echo '<div class=\"error\"><p>' . esc_html__( 'WeBWorK for WordPress requires WordPress 4.4 to function properly. Please upgrade WordPress or deactivate WeBWorK for WordPress.', 'webworkqa' ) . '</p></div>';
			}
		);
	}

	spl_autoload_register( 'webwork_autoload_register' );

	$GLOBALS['webwork'] = \WeBWorK\Loader::init();
}
add_action( 'init', 'webwork_init', 5 );

/**
 * Autoload logic.
 *
 * @since 1.0.0
 */
function webwork_autoload_register( $class ) {
	$prefix = 'WeBWorK\\';

	// Get the relative class name.
	$relative_class = substr( $class, strlen( $prefix ) );

	$base_dir = dirname( __FILE__ ) . '/classes/';

	$file = $base_dir . str_replace( '\\', '/', $relative_class . '.php' );

	if ( file_exists( $file ) ) {
		require $file;
	}
}

register_activation_hook( __FILE__, 'webwork_activation' );

/**
 * Activation routine.
 *
 * @since 1.0.0
 */
function webwork_activation() {
	spl_autoload_register( 'webwork_autoload_register' );

	$schema_obj = new \WeBWorK\Server\Schema();
	$schema     = $schema_obj->get_votes_schema();

	if ( ! function_exists( 'dbDelta' ) ) {
		require ABSPATH . '/wp-admin/includes/upgrade.php';
	}

	dbDelta( array( $schema ) );
}
