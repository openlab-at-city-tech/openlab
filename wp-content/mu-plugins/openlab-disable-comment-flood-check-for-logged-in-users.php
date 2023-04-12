<?php

/**
 * Disable comment flood checks for logged-in users.
 *
 * See #1738.
 */
function openlab_disable_comment_flood_check_for_logged_in_users( $block ) {
	if ( is_user_logged_in() ) {
		remove_filter( 'comment_flood_filter', 'wp_throttle_comment_flood', 10 );
	}

	return $block;
}
add_filter( 'comment_flood_filter', 'openlab_disable_comment_flood_check_for_logged_in_users', 0 );
