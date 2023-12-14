<?php
/**
 * BP Classic Members Admin functions.
 *
 * @package bp-classic\inc\members\admin
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the Members directory states.
 *
 * @since 1.0.0
 *
 * @param string[] $states An array of post display states.
 * @param WP_Post  $post   The current post object.
 * @return array           The Members component's directory states.
 */
function bp_classic_members_admin_display_directory_states( $states = array(), $post = null ) {
	$bp = buddypress();

	if ( isset( $bp->pages->members->id ) && (int) $bp->pages->members->id === (int) $post->ID ) {
		$states['page_for_members_directory'] = _x( 'BP Members Page', 'page label', 'bp-classic' );
	}

	if ( bp_get_signup_allowed() || bp_get_members_invitations_allowed() ) {
		if ( isset( $bp->pages->register->id ) && (int) $bp->pages->register->id === (int) $post->ID ) {
			$states['page_for_bp_registration'] = _x( 'BP Registration Page', 'page label', 'bp-classic' );
		}

		if ( isset( $bp->pages->activate->id ) && (int) $bp->pages->activate->id === (int) $post->ID ) {
			$states['page_for_bp_activation'] = _x( 'BP Activation Page', 'page label', 'bp-classic' );
		}
	}

	return $states;
}
add_filter( 'bp_classic_admin_display_directory_states', 'bp_classic_members_admin_display_directory_states', 10, 2 );
