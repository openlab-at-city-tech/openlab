<?php
/*
Plugin Name: Genesis Connect for BuddyPress
Plugin URI: http://www.studiopress.com/plugins/genesis-connect
Description: BuddyPress Theme Support for the Genesis Framework
Version: 1.2.1
Author: Copyblogger Media LLC
Author URI: http://www.copyblogger.com
*/

function genesisconnect_init() {

	if ( ! function_exists( 'bp_loaded' ) || ! function_exists( 'genesis_get_option' ) )
		return;
		
	define( 'GENESISCONNECT_VERSION', '1.2.1' );
	define( 'GENESISCONNECT_DIR', plugin_dir_path( __FILE__ ) );
	define( 'GENESISCONNECT_URL', plugin_dir_url( __FILE__ ) );
	load_plugin_textdomain( 'genesis-connect', false, '/genesis-connect/languages/' );
	require( GENESISCONNECT_DIR . 'lib/class.theme.php' );

}
add_action( 'genesis_setup', 'genesisconnect_init', 11 );

