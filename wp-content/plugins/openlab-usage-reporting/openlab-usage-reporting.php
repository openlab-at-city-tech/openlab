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
	require( OLUR_PLUGIN_DIR . 'includes/report.php' );
	require( OLUR_PLUGIN_DIR . 'includes/admin.php' );
}
add_action( 'init', 'olur_init', 100 );
