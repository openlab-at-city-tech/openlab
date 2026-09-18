<?php
/**
 * Locks account email addresses so only network admins can change them.
 *
 * My Profile > Settings disables its email field on purpose, but wp-admin's
 * profile.php accepted a new address and applied it to the account. The
 * address is what ties a member to City Tech at registration, so members
 * must not be able to swap it out themselves.
 */

namespace OpenLab\UserEmailLock;

add_action( 'personal_options_update', __NAMESPACE__ . '\restore_posted_email', 1 );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\restore_posted_email', 1 );
add_action( 'user_profile_update_errors', __NAMESPACE__ . '\restore_user_email', 10, 3 );
add_action( 'load-profile.php', __NAMESPACE__ . '\discard_pending_email_change' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\lock_email_field' );
add_filter( 'rest_pre_insert_user', __NAMESPACE__ . '\strip_rest_email' );
add_action( 'bp_actions', __NAMESPACE__ . '\restore_bp_settings_email', 9 );

/**
 * Returns whether the current user may change email addresses.
 */
function current_user_can_change_email() {
	return current_user_can( 'manage_network_users' );
}

/**
 * Replaces a posted email with the stored one before wp-admin reads it.
 *
 * Runs at priority 1 so it precedes core's send_confirmation_on_profile_email(),
 * which reads $_POST['email'] directly and starts the confirmation flow.
 *
 * @param int $user_id The user being saved.
 */
function restore_posted_email( $user_id ) {
	if ( current_user_can_change_email() || ! isset( $_POST['email'] ) ) {
		return;
	}

	$user = get_userdata( $user_id );
	if ( $user ) {
		$_POST['email'] = $user->user_email;
	}
}

/**
 * Resets the email on the user object edit_user() is about to save.
 *
 * @param \WP_Error $errors Validation errors.
 * @param bool      $update Whether this is an update rather than a creation.
 * @param \stdClass $user   The user data about to be saved.
 */
function restore_user_email( $errors, $update, $user ) {
	if ( ! $update || current_user_can_change_email() || empty( $user->ID ) ) {
		return;
	}

	$stored = get_userdata( $user->ID );
	if ( $stored ) {
		$user->user_email = $stored->user_email;
	}
}

/**
 * Drops any email change still awaiting confirmation.
 *
 * profile.php completes a pending change from the ?newuseremail link. Clearing
 * the meta first stops changes that were started before the lock existed.
 */
function discard_pending_email_change() {
	if ( current_user_can_change_email() ) {
		return;
	}

	delete_user_meta( get_current_user_id(), '_new_email' );
}

/**
 * Makes the email field read-only, matching the username field.
 *
 * Core has no filter for this markup, so the field is adjusted in JS.
 *
 * @param string $hook_suffix The current admin page.
 */
function lock_email_field( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'profile.php', 'user-edit.php' ), true ) ) {
		return;
	}

	if ( current_user_can_change_email() ) {
		return;
	}

	wp_enqueue_script(
		'openlab-user-email-lock',
		plugins_url() . '/wds-citytech/assets/js/user-email-lock.js',
		array(),
		null,
		true
	);
}

/**
 * Strips the email from a REST user update.
 *
 * @param object $prepared_user The user data to pass to wp_update_user().
 * @return object
 */
function strip_rest_email( $prepared_user ) {
	if ( ! empty( $prepared_user->ID ) && ! current_user_can_change_email() ) {
		unset( $prepared_user->user_email );
	}

	return $prepared_user;
}

/**
 * Replaces a posted email with the stored one before BuddyPress saves settings.
 *
 * The General settings template posts the current address in a hidden field,
 * which is all that stops bp_settings_action_general() from changing it.
 */
function restore_bp_settings_email() {
	if ( ! bp_is_settings_component() || ! bp_is_current_action( 'general' ) ) {
		return;
	}

	if ( current_user_can_change_email() || ! isset( $_POST['email'] ) ) {
		return;
	}

	$user = get_userdata( bp_displayed_user_id() );
	if ( $user ) {
		$_POST['email'] = $user->user_email;
	}
}
