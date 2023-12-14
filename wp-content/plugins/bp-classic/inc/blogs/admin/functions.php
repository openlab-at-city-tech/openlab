<?php
/**
 * BP Classic Blogs Admin functions.
 *
 * @package bp-classic\inc\blogs\admin
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the Blogs directory states.
 *
 * @since 1.0.0
 *
 * @param string[] $states An array of post display states.
 * @param WP_Post  $post   The current post object.
 * @return array           The Blogs component's directory states.
 */
function bp_classic_blogs_admin_display_directory_states( $states = array(), $post = null ) {
	$bp = buddypress();

	if ( isset( $bp->pages->blogs->id ) && (int) $bp->pages->blogs->id === (int) $post->ID ) {
		$states['page_for_sites_directory'] = _x( 'BP Sites Page', 'page label', 'bp-classic' );
	}

	return $states;
}
add_filter( 'bp_classic_admin_display_directory_states', 'bp_classic_blogs_admin_display_directory_states', 10, 2 );
