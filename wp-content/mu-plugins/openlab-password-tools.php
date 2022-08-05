<?php

/**
 * Tools related to password resets in August 2022.
 */

// @todo: modify text of outgoing email for users who have not yet changed their password?

/**
 * When a user changes their password for the first time after the resets, save a timestamp.
 */
add_action(
	'after_password_reset',
	function( $user ) {
		$timestamp = get_user_meta( $user->ID, 'user_has_reset_password_202208', true );
		if ( $timestamp ) {
			return;
		}

		// Date is in UTC.
		add_user_meta( $user->ID, 'user_has_reset_password_202208', gmdate( 'Y-m-d H:i:s' ) );
	}
);

/**
 * Modify login screen.
 *
 * - Change the language of the default 'error' message when you provide an incorrect password.
 * - Provide a second informational message about the issue.
 */
add_filter(
	'wp_login_errors',
	function( $errors ) {
		if ( ! ( $errors instanceof \WP_Error ) ) {
			$errors = new WP_Error;
		}

		$errors->add( 'password_reset_message', sprintf( '<strong>Please note</strong>: For the safety of your account, all City Tech OpenLab passwords were reset on August 4, 2022.  If you have not yet set a new password for your account, or if you have forgotten your password, <a href="%s">reset it now</a>.', esc_url( wp_lostpassword_url() ) ), 'message' );

		$password_error_messages = $errors->get_error_messages( 'incorrect_password' );
		if ( ! $password_error_messages ) {
			return $errors;
		}

		$password_error_message = preg_replace( '|\s*<a href="[^"]+">Lost your password\?</a>|', '', $password_error_messages[0] );

		$errors->remove( 'incorrect_password' );

		$errors->add( 'incorrect_password', $password_error_message );

		return $errors;
	}
);
