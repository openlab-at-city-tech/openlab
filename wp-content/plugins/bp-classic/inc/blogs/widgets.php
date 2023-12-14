<?php
/**
 * BP Classic Blogs Widget Functions.
 *
 * @package bp-classic\inc\blogs
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Recent Posts Legacy Widget.
 *
 * @since 1.0.0
 */
function bp_classic_blogs_register_recent_posts_widget() {
	register_widget( 'BP_Classic_Blogs_Recent_Posts_Widget' );
}

/**
 * Register the widgets for the Blogs component.
 *
 * @since 1.0.0
 */
function bp_classic_blogs_register_widgets() {
	global $wpdb;

	if ( is_multisite() && bp_is_active( 'activity' ) && bp_is_root_blog( $wpdb->blogid ) ) {
		add_action( 'widgets_init', 'bp_classic_blogs_register_recent_posts_widget' );
	}
}
add_action( 'bp_register_widgets', 'bp_classic_blogs_register_widgets' );
