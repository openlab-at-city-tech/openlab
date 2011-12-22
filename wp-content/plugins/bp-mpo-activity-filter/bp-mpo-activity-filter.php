<?php
/*
Plugin Name: BP MPO Activity Filter
Plugin URI: http://qwriting.org
Description: When using More Privacy Options, this plugin removes items from BP activity streams according to user roles
Version: 1.1.1
Author: Boone Gorges
Author URI: http://teleogistic.net
*/

/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_mpo_activity_filter_init() {
	require( dirname( __FILE__ ) . '/bp-mpo-activity-filter-bp-functions.php' );
}
add_action( 'bp_init', 'bp_mpo_activity_filter_init' );

?>
