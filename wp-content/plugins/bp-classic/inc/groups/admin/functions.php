<?php
/**
 * BP Classic Groups Admin functions.
 *
 * @package bp-classic\inc\groups\admin
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the Groups directory states.
 *
 * @since 1.0.0
 *
 * @param string[] $states An array of post display states.
 * @param WP_Post  $post   The current post object.
 * @return array           The Groups component's directory states.
 */
function bp_classic_groups_admin_display_directory_states( $states = array(), $post = null ) {
	$bp = buddypress();

	if ( isset( $bp->pages->groups->id ) && (int) $bp->pages->groups->id === (int) $post->ID ) {
		$states['page_for_groups_directory'] = _x( 'BP Groups Page', 'page label', 'bp-classic' );
	}

	return $states;
}
add_filter( 'bp_classic_admin_display_directory_states', 'bp_classic_groups_admin_display_directory_states', 10, 2 );
