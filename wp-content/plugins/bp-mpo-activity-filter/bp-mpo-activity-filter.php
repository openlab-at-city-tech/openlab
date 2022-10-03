<?php
/*
Plugin Name: BP MPO Activity Filter
Plugin URI: http://github.com/boonebgorges/bp-mpo-activity-filter
Description: When using More Privacy Options, this plugin removes items from BP activity streams according to user roles
Version: 1.3.3
Author: Boone Gorges
Author URI: http://boone.gorg.es
*/

/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_mpo_activity_filter_init() {
	require( dirname( __FILE__ ) . '/bp-mpo-activity-filter-bp-functions.php' );
}
add_action( 'bp_include', 'bp_mpo_activity_filter_init' );
