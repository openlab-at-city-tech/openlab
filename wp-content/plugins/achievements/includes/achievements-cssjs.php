<?php
/**
 * Set up and enqueue all CS and JS files.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements 
 * @subpackage cssjs
 *
 * $Id: achievements-cssjs.php 1002 2011-10-04 20:13:34Z DJPaul $
 */

/**
 * Enqueues CSS files.
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 */
function dpa_add_css() {
	global $bp;

 	if ( is_active_widget( false, false, 'achievements-sitewide' ) || is_active_widget( false, false, 'achievements-available-achievements' ) || is_active_widget( false, false, 'achievements-member-achievements' ) || is_active_widget( false, false, 'achievements-featured-achievement' ) || is_active_widget( false, false, 'achievements-member-achievements-available' ) || is_active_widget( false, false, 'achievements-member-points' ) )
		wp_enqueue_style( 'achievements-widget', plugins_url( '/css/widget.css', __FILE__ ), array(), ACHIEVEMENTS_VERSION );

	if ( !bp_is_current_component( $bp->achievements->slug ) ) {
		if ( bp_is_active( 'activity' ) && bp_is_activity_component() && !bp_is_blog_page() || ( bp_is_component_front_page( 'activity' ) && bp_is_front_page() ) )
			wp_enqueue_style( 'achievements-directory', plugins_url( '/css/directory.css', __FILE__ ), array(), ACHIEVEMENTS_VERSION );

		wp_print_styles();
		return;
	}

	if ( ( DPA_SLUG_CREATE == $bp->current_action && dpa_permission_can_user_create() ) || ( DPA_SLUG_ACHIEVEMENT_EDIT == $bp->current_action && dpa_permission_can_user_edit() ) || ( DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE == $bp->current_action && dpa_permission_can_user_change_picture() ) || ( DPA_SLUG_ACHIEVEMENT_GRANT == $bp->current_action && dpa_permission_can_user_grant() ) )
		wp_enqueue_style( 'achievements-admin', plugins_url( '/css/admin.css', __FILE__ ), array(), ACHIEVEMENTS_VERSION );

	if ( $bp->is_single_item )
		wp_enqueue_style( 'achievements-detail', plugins_url( '/css/detail.css', __FILE__ ), array(), ACHIEVEMENTS_VERSION );
	else
		wp_enqueue_style( 'achievements-directory', plugins_url( '/css/directory.css', __FILE__ ), array(), ACHIEVEMENTS_VERSION );

	wp_print_styles();
}
add_action( 'wp_head', 'dpa_add_css' );

/**
 * Enqueues JavaScript files.
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 */
function dpa_add_js() {
	global $bp;

	if ( !bp_is_current_component( $bp->achievements->slug ) )
		return;

	if ( ( DPA_SLUG_CREATE == $bp->current_action && dpa_permission_can_user_create() ) || ( DPA_SLUG_ACHIEVEMENT_EDIT == $bp->current_action && dpa_permission_can_user_edit() ) || ( DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE == $bp->current_action && dpa_permission_can_user_change_picture() ) || ( DPA_SLUG_ACHIEVEMENT_GRANT == $bp->current_action && dpa_permission_can_user_grant() ) )
		wp_enqueue_script( 'achievements-admin-js', plugins_url( '/js/admin.js', __FILE__ ), array(), ACHIEVEMENTS_VERSION );

	if ( $bp->is_single_item )
		wp_enqueue_script( 'achievements-detail-js', plugins_url( '/js/detail.js', __FILE__ ), array(), ACHIEVEMENTS_VERSION );
	else
		wp_enqueue_script( 'achievements-directory-js', plugins_url( '/js/directory.js', __FILE__ ), array(), ACHIEVEMENTS_VERSION );
}
add_action( 'wp_head', 'dpa_add_js', 1 );
?>