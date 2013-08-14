<?php

/**
 * Reset the group creation cookies when visiting first page of group creation
 */
function openlab_clear_group_creation_cookies() {
	global $bp;

	// Huge hack. I can't figure any other way to clear it early enough
	if ( empty( $_POST ) && 0 === strpos( $_SERVER['REQUEST_URI'], '/groups/create/' ) && false !== strpos( $_SERVER['REQUEST_URI'], 'group-details' ) ) {
		unset( $bp->groups->current_create_step );
		unset( $bp->groups->completed_create_steps );

		setcookie( 'bp_new_group_id', false, time() - 1000, COOKIEPATH );
		setcookie( 'bp_completed_create_steps', false, time() - 1000, COOKIEPATH );
	}
}
add_action( 'bp_init', 'openlab_clear_group_creation_cookies', 20 );
