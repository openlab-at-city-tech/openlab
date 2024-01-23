<?php

/**
 * BuddyPress 12.0 URL polyfills - Activity component.
 */

if ( ! function_exists( 'bp_activity_comment_cancel_url' ) ) :
/**
 * Outputs the URL to cancel a comment.
 *
 * @return void
 */
function bp_activity_comment_cancel_url() {
	echo esc_url( bp_get_activity_comment_cancel_url() );
}
endif;

if ( ! function_exists( 'bp_get_activity_comment_cancel_url' ) ) :
/**
 * Returns the URL to cancel a comment.
 *
 * @return string The URL to cancel a comment.
 */
function bp_get_activity_comment_cancel_url() {
	$query_vars = array();

	if ( isset( $_GET['acpage'] ) ) {
		$query_vars['acpage'] = (int) wp_unslash( $_GET['acpage'] );
	}

	if ( isset( $_GET['offset_lower'] ) ) {
		$query_vars['offset_lower'] = (int) wp_unslash( $_GET['offset_lower'] );
	}

	$url = add_query_arg( $query_vars, bp_get_activity_directory_permalink() );

	/**
	 * Filters the cancel comment link for the current activity comment.
	 *
	 * @since 12.0.0
	 *
	 * @param string $url Constructed URL parameters with activity IDs.
	 */
	return apply_filters( 'bp_get_activity_comment_cancel_url', $url );
}
endif;
