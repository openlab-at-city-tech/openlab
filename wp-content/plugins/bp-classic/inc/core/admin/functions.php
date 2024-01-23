<?php
/**
 * BP Classic Admin functions.
 *
 * @package bp-classic\inc\core\admin
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add notice if no rewrite rules are enabled.
 *
 * @since 1.0.0
 */
function bp_classic_admin_permalink_notice() {
	if ( ! bp_has_pretty_urls() ) {
		bp_core_add_admin_notice(
			sprintf(
				// Translators: %s is the url to the permalink settings.
				__( '<strong>BuddyPress is almost ready</strong>. You must <a href="%s">update your permalink structure</a> to something other than the default for it to work.', 'bp-classic' ),
				admin_url( 'options-permalink.php' )
			),
			'error'
		);
	}
}
add_action( 'bp_admin_init', 'bp_classic_admin_permalink_notice', 1010 );

/**
 * Dedicated filter to inform about BP components directory page states.
 *
 * @since 1.0.0
 *
 * @param string[] $post_states An array of post display states.
 * @param WP_Post  $post        The current post object.
 */
function bp_classic_admin_display_directory_states( $post_states = array(), $post = null ) {
	/**
	 * Filter here to add states to BP Directory pages.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $value An empty array.
	 * @param WP_Post  $post  The current post object.
	 */
	$directory_page_states = apply_filters( 'bp_classic_admin_display_directory_states', array(), $post );

	if ( $directory_page_states ) {
		$post_states = array_merge( $post_states, $directory_page_states );
	}

	return $post_states;
}
add_filter( 'display_post_states', 'bp_classic_admin_display_directory_states', 10, 2 );
