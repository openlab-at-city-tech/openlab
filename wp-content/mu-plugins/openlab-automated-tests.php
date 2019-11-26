<?php
/**
 * Plugin: OpenLab automated tests
 */

namespace OpenLab\AutomatedTests;

const TEST_EMAIL = 'tests@mail.citytech.cuny.edu';

/**
 * Bypasses user activation for a specific email.
 */
function bootstrap() {
	if ( ( ! defined( 'ENV_TYPE' ) || 'production' === ENV_TYPE ) ) {
		return false;
	}

	\add_filter( 'wpmu_signup_user_notification', __NAMESPACE__ . '\\auto_activate_user', 2, 3 );
}
\add_action( 'bp_loaded', __NAMESPACE__ . '\\bootstrap' );

/**
 * Autoactivate end-to-end test users.
 */
function auto_activate_user( $user, $user_email, $key ) {
	if ( $user_email !== TEST_EMAIL ) {
		return false;
	}

	\bp_core_activate_signup( $key );

	global $wpdb;

	/**
	 * Delete signup record. Allows re-using the same email.
	 * @see https://core.trac.wordpress.org/ticket/43232.
	 */
	$wpdb->delete( $wpdb->signups, [ 'user_email' => TEST_EMAIL ] );

	\add_filter( 'bp_registration_needs_activation', '__return_false' );
}
