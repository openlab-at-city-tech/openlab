<?php
/**
 * BP Classic Activity Admin functions.
 *
 * @package bp-classic\inc\activity\admin
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the Activity directory states.
 *
 * @since 1.0.0
 *
 * @param string[] $states An array of post display states.
 * @param WP_Post  $post   The current post object.
 * @return array           The Activity component's directory states.
 */
function bp_classic_activity_admin_display_directory_states( $states = array(), $post = null ) {
	$bp = buddypress();

	if ( isset( $bp->pages->activity->id ) && (int) $bp->pages->activity->id === (int) $post->ID ) {
		$states['page_for_activity_directory'] = _x( 'BP Activity Page', 'page label', 'bp-classic' );
	}

	return $states;
}
add_filter( 'bp_classic_admin_display_directory_states', 'bp_classic_activity_admin_display_directory_states', 10, 2 );
