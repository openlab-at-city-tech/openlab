<?php
/**
 * Holds all functions used in AJAX queries.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements
 * @subpackage ajax
 *
 * $Id: achievements-ajax.php 1008 2011-10-04 20:34:45Z DJPaul $
 */

/**
 * Implements the Achievements Directory 'order by' filter.
 *
 * @since 2.0
 */
function dpa_filter_directory_template_loader() {
	dpa_setup_globals();
	dpa_load_template( array( 'achievements/achievements-loop.php' ) );
}
add_action( 'wp_ajax_achievements_filter', 'dpa_filter_directory_template_loader' );

/**
 * Implements the Achievement's "Unlocked By" page pagination.
 *
 * @see dpa_filter_users_by_achievement() for the matching remove_filter calls to prevent conflict with regular use of the members template loop.
 * @since 2.0
 */
function dpa_filter_achievement_unlockedby_template_loader() {
	dpa_setup_globals();

	add_filter( 'bp_core_get_total_users_sql', 'dpa_filter_users_by_achievement', 10, 2 );
	add_filter( 'bp_core_get_paged_users_sql', 'dpa_filter_users_by_achievement', 10, 2 );
	add_filter( 'bp_member_last_active', 'dpa_filter_unlockedby_activity_timestamp' );
	add_action( 'bp_directory_members_actions', 'dpa_member_achievements_link' );
	add_action( 'bp_after_members_loop', 'dpa_remove_filters_after_members_loop' );

	locate_template( array( 'members/members-loop.php' ), true );
}
add_action( 'wp_ajax_unlockedby_filter', 'dpa_filter_achievement_unlockedby_template_loader' );

/**
 * Implements the Achievement's activity page filter.
 *
 * @since 2.0
 */
function dpa_dtheme_achievements_activity_template_loader() {
	dpa_setup_globals();
	dpa_achievement_activity_il8n_filter();

	$result = array();
	//$feed_url = dpa_get_achievement_activity_feed_link();

	// Buffer the loop in the template to a var for JS to spit out
	ob_start();
	locate_template( array( 'activity/activity-loop.php' ), true );
	$result['contents'] = str_replace( 'class="load-more"', 'class="achievements-load-more"', ob_get_contents() );
	//$result['feed_url'] = apply_filters( 'bp_dtheme_activity_feed_url', $feed_url, stripslashes( $_POST['scope'] ) );
	ob_end_clean();

	echo json_encode( $result );
}
add_action( 'wp_ajax_dpa_activity_get_older_updates', 'dpa_dtheme_achievements_activity_template_loader' );

/**
 * Returns specific user's avatar / name / last active time stamp in a <li> for the single Achievement "give" page.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_screen_achievement_grant_user_details() {
	global $bp;

	if ( empty( $_POST['member_id'] ) )
		return false;

	$member_id = (int)$_POST['member_id'];
	if ( !get_userdata( $member_id ) )
		return false;

	$user = new BP_Core_User( $member_id );

	echo '<li id="uid-' . $user->id . '">';
	echo $user->avatar_thumb;
	echo '<h4>' . $user->user_link . '</h4>';
	echo '<span class="activity">' . esc_attr( $user->last_active ) . '</span>';
	echo '</li>';
}
add_action( 'wp_ajax_dpa_grant_user_details', 'dpa_screen_achievement_grant_user_details' );
?>