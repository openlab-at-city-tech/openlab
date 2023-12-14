<?php
/**
 * BP Classic Members Functions.
 *
 * @package bp-classic\inc\members
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the link for the logged-in user's profile.
 *
 * @since 1.0.0
 *
 * @return string The link for the logged-in user's profile.
 */
function bp_get_loggedin_user_link() {
	$url = bp_loggedin_user_url();

	/**
	 * Filters the link for the logged-in user's profile.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Link for the logged-in user's profile.
	 */
	return apply_filters( 'bp_get_loggedin_user_link', $url );
}

/**
 * Get the link for the displayed user's profile.
 *
 * @since 1.0.0
 *
 * @return string The link for the displayed user's profile.
 */
function bp_get_displayed_user_link() {
	$url = bp_displayed_user_url();

	/**
	 * Filters the link for the displayed user's profile.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Link for the displayed user's profile.
	 */
	return apply_filters( 'bp_get_displayed_user_link', $url );
}

/**
 * Alias of {@link bp_displayed_user_domain()}.
 *
 * @since 1.0.0
 */
function bp_user_link() {
	bp_displayed_user_url();
}
