<?php

/**
 * Tools related to password resets in August 2022.
 */

// Don't send admin notifications of password resets.
remove_action( 'after_password_reset', 'wp_password_change_notification' );

/**
 * Modifies the content of the password reset email.
 */
add_filter(
	'retrieve_password_message',
	function( $message, $key, $user_login, $user_data ) {
		$locale = get_user_locale( $user_data );

		$message = 'A password reset has been requested for your City Tech OpenLab account.' . "\r\n\r\n";

		/* translators: %s: User login. */
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";

		$message .= 'Visit the following address to choose a new password:' . "\r\n\r\n";

		$url = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . '&wp_lang=' . $locale;

		$message .= '<a href="' . $url . '">' . $url . '</a>' . "\r\n\r\n";

		$message .= __( 'If this was a mistake, ignore this email and nothing will happen.' ) . "\r\n\r\n";

		if ( ! is_user_logged_in() ) {
			$requester_ip = $_SERVER['REMOTE_ADDR'];
			if ( $requester_ip ) {
				$message .= sprintf(
					/* translators: %s: IP address of password reset requester. */
					__( 'This password reset request originated from the IP address %s.' ),
					$requester_ip
				) . "\r\n";
			}
		}

		return $message;
	},
	10,
	4
);

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

		$password_error_messages = $errors->get_error_messages( 'incorrect_password' );
		if ( ! $password_error_messages ) {
			return $errors;
		}

		$password_error_message = preg_replace( '|\s*<a href="([^"]+)">Lost your password\?</a>|', ' If you have forgotten your password, <a href="\1">please reset it now</a>.', $password_error_messages[0] );

		$errors->remove( 'incorrect_password' );

		$errors->add( 'incorrect_password', $password_error_message );

		return $errors;
	}
);
