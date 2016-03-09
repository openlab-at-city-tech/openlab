<?php
/*
Plugin Name: OpenLab Usage Reporting
Version: 0.1-alpha
Description: Generate usage reports for the City Tech OpenLab
Author: Boone Gorges
Author URI: http://boone.gorg.es
Plugin URI: http://openlab.citytech.cuny.edu
Text Domain: openlab-usage-reporting
Domain Path: /languages
Network: true
*/

define( 'OLUR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OLUR_PLUGIN_URL', plugins_url( '', __FILE__ ) . '/' );

/**
 * Load the plugin files.
 */
function olur_init() {
	spl_autoload_register( 'olur_autoload_register' );

	require( OLUR_PLUGIN_DIR . 'includes/report.php' );
	require( OLUR_PLUGIN_DIR . 'includes/admin.php' );
}
add_action( 'init', 'olur_init', 100 );

/**
 * Autoload logic.
 *
 * @since 1.0.0
 */
function olur_autoload_register( $class ) {
	$prefix = 'OLUR\\';

	// Get the relative class name.
	$relative_class = substr( $class, strlen( $prefix ) );

	$base_dir = dirname( __FILE__ ) . '/classes/';

	$file = $base_dir . str_replace( '\\', '/', $relative_class . '.php' );

	if ( file_exists( $file ) ) {
		require $file;
	}
}
