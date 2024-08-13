<?php
/**
 * BP Classic Compatibility Functions for bbPress.
 *
 * @package bp-classic\inc\forums
 * @since 1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adjusts the `WP_Query` paged var.
 *
 * @since 1.4.0
 *
 * @param WP_Query $query The WP Query pasded by reference.
 */
function bp_classic_setup_forums_pagination( &$query ) {
	if ( $query->get( 'paged' ) ) {
		return;
	}

	if ( ( bp_is_user() && bp_is_current_component( 'forums' ) ) || ( bp_is_group() && bp_is_current_action( 'forum' ) ) ) {
		$action_variables = (array) bp_action_variables();
		$is_paged         = array_search( bbp_get_paged_slug(), $action_variables, true );

		if ( false !== $is_paged ) {
			$query->set( 'paged', (int) bp_action_variable( $is_paged + 1 ) );
		}
	}
}
add_action( 'bp_members_parse_query', 'bp_classic_setup_forums_pagination' );
add_action( 'bp_groups_parse_query', 'bp_classic_setup_forums_pagination' );
