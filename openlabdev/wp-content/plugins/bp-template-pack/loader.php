<?php 

/*
Plugin Name: BuddyPress Template Pack
Plugin URI: http://wordpress.org/extend/plugins/bp-template-pack/
Description: Add support for BuddyPress to your existing WordPress theme. This plugin will guide you through the process step by step.
Author: apeatling, boonebgorges
Version: 1.1.3
Author URI: http://buddypress.org
*/

/*****
 * Initialize the plugin once BuddyPress has initialized.
 */
function bp_tpack_loader() {
	include( dirname( __FILE__ ) . '/bp-template-pack.php' );
}
add_action( 'bp_include', 'bp_tpack_loader' );

?>
