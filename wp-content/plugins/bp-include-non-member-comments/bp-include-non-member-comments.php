<?php
/*
Plugin Name: BP Include Non-member Comments
Plugin URI: http://teleogistic.net/code/buddypress/bp-include-non-member-comments
Description: Inserts blog comments from non-logged-in users into the activity stream
Version: 1.3
Author: Boone Gorges
Author URI: http://teleogistic.net
*/

/* Comment out the following two lines to run in BP 1.1.3 */
// add_action( 'comment_post', 'bp_blogs_record_nonmember_comment_old', 8, 2 );
// add_filter( 'bp_activity_content_filter', 'bp_nonmember_comment_content', 10, 4 );

/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_include_non_member_comments_init() {
	require( dirname( __FILE__ ) . '/bp-include-non-member-comments-bp-functions.php' );

	add_action( 'comment_post', 'bp_blogs_record_nonmember_comment', 8, 2 );

	add_action('wp_set_comment_status', 'bp_blogs_record_nonmember_comment_approved', 8, 2 );
	add_filter( 'bp_activity_content_filter', 'bp_nonmember_comment_content', 10, 4 );
}
add_action( 'bp_init', 'bp_include_non_member_comments_init' );


?>
