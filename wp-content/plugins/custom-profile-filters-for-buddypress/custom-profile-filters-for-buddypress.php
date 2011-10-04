<?php

/*
Plugin Name: Custom Profile Filters for BuddyPress
Plugin URI: http://dev.commons.gc.cuny.edu
Description: Changes the way that profile data fields get filtered into clickable URLs.
Version: 0.3.1
Author: Boone Gorges
Author URI: http://teleogistic.net
*/

$no_link_fields = array( // Enter the field ID of any field that you want to appear as plain, non-clickable text. Don't forget to separate with commas.

	'Skype ID ' 	,
	'Phone' 	,
	'IM'		
	
	);

$social_networking_fields = array( // Enter the field ID of any field that prompts for the username to a social networking site, followed by the URL format for profiles on that site, with *** in place of the user name. Thus, since the URL for the profile of awesometwitteruser is twitter.com/awesometwitteruser, you should enter 'Twitter' => 'twitter.com/***'. Don't forget: 1) Leave out the 'http://', 2) Separate items with commas

	'Twitter' =>'twitter.com/***' ,
	'Delicious ID' => 'delicious.com/***' ,
	'YouTube ID ' => 'youtube.com/***' ,
	'Flickr ID ' =>'flickr.com/***' ,
	'FriendFeed ID' => 'friendfeed.com/***'

	);


/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function custom_profile_filters_for_buddypress_init() {
	require( dirname( __FILE__ ) . '/custom-profile-filters-for-buddypress-bp-functions.php' );
}
add_action( 'bp_init', 'custom_profile_filters_for_buddypress_init' );

?>