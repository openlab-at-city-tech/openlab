<?php
/*
Plugin Name: BP Group Management
Plugin URI: http://teleogistic.net/code/buddypress/bp-group-management
Description: Allows site administrators to manage BuddyPress group membership
Version: 0.4.3
Author: Boone Gorges
Author URI: http://teleogistic.net
*/

/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_group_management_init() {
	require( dirname( __FILE__ ) . '/bp-group-management-bp-functions.php' );
}
add_action( 'bp_init', 'bp_group_management_init' );

function bp_group_management_admin_init() {
	
	wp_register_style( 'bp-group-management-css', WP_PLUGIN_URL . '/bp-group-management/bp-group-management-css.css' );
}
add_action( 'admin_init', 'bp_group_management_admin_init' );

function bp_group_management_locale_init () {
	$plugin_dir = basename(dirname(__FILE__));
	$locale = get_locale();
	$mofile = WP_PLUGIN_DIR . "/bp-group-management/languages/bp-group-management-$locale.mo";
      
      if ( file_exists( $mofile ) )
      		load_textdomain( 'bp-group-management', $mofile );
}
add_action ('plugins_loaded', 'bp_group_management_locale_init');

?>
