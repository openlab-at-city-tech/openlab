<?php

/**
 * Customizations related to user passwords.
 */

namespace OpenLab\Passwords;

/**
 * Gets the password expiration interval.
 *
 * @return int The interval in seconds.
 */
function get_password_expiration_interval() {
	return 180 * DAY_IN_SECONDS;
}

/**
 * Gets the warning interval for password expiration.
 *
 * @return int The interval in seconds.
 */
function get_password_expiration_warning_interval() {
	return 14 * DAY_IN_SECONDS;
}

/**
 * Sets a user's password expiration date.
 *
 * @param int $user_id The user ID.
 * @param int $expiration The expiration timestamp.
 */
function set_password_expiration( $user_id, $expiration ) {
	update_user_meta( $user_id, 'password_expiration', $expiration );
}

/**
 * Gets a user's password expiration date.
 *
 * @param int $user_id The user ID.
 * @return int|null The expiration timestamp. Null if not set.
 */
function get_password_expiration( $user_id ) {
	$saved = get_user_meta( $user_id, 'password_expiration', true );

	if ( ! $saved ) {
		return null;
	}

	return (int) $saved;
}

/**
 * Adds a 'Password Expiration' section to user-edit.php.
 *
 * @param WP_User $user The user object.
 */
function user_edit_form( $user ) {
	wp_enqueue_script(
		'passwords-admin',
		plugins_url() . '/wds-citytech/assets/js/passwords-admin.js',
		array( 'jquery' ),
		null,
		true
	);

	$expiration = get_password_expiration( $user->ID );

	?>

	<h2>Password Expiration</h2>

	<?php if ( ! $expiration ) : ?>
		<p>This user's password does not currently have an expiration date.</p>
	<?php elseif ( $expiration > time() ) : ?>
		<p>This user's password <strong>will expire</strong> on <?php echo date( 'F j, Y', $expiration ); ?> at <?php echo date( 'g:i a', $expiration ); ?> (UTC).</p>
	<?php else : ?>
		<p>This user's password <strong>expired</strong> on <?php echo date( 'F j, Y', $expiration ); ?> at <?php echo date( 'g:i a', $expiration ); ?> (UTC).</p>
	<?php endif; ?>

	<table class="form-table">
		<tr>
			<th><label for="password_expiration">Set Password Expiration (UTC)</label></th>
			<td>
				<input type="datetime-local" id="password-expiration" name="password_expiration" value="<?php echo $expiration ? date( 'Y-m-d H:i:s', $expiration ) : ''; ?>" />
				<button class="button" id="set-password-expiration">Set to Now</button>
				<button class="button" id="clear-password-expiration">Clear</button>

				<?php wp_nonce_field( 'set_password_expiration', 'set-password-expiration-nonce', false ); ?>
			</td>
		</tr>
	</table>

	<?php
}
add_action( 'edit_user_profile', __NAMESPACE__ . '\user_edit_form' );

/**
 * Saves the password expiration date when a user is updated.
 *
 * @param int $user_id The user ID.
 */
function save_user( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( ! isset( $_POST['set-password-expiration-nonce'] ) || ! wp_verify_nonce( $_POST['set-password-expiration-nonce'], 'set_password_expiration' ) ) {
		return;
	}

	$expiration = strtotime( $_POST['password_expiration'] );

	if ( ! $expiration ) {
		delete_user_meta( $user_id, 'password_expiration' );
	} else {
		set_password_expiration( $user_id, $expiration );
	}
}
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\save_user' );

/**
 * Hook into the login process to check for password expiration.
 *
 * @param \WP_User|\WP_Error $user The WP_User object or WP_Error.
 * @param string $username The username.
 * @param string $password The password.
 * @return WP_User|WP_Error The WP_User object or WP_Error.
 */
function check_password_expiration( $user, $username, $password ) {
	if ( \is_wp_error( $user ) ) {
		return $user;
	}

	$user_obj = get_user_by( 'login', $username );
	if ( ! $user_obj ) {
		return $user;
	}

	$expiration = get_password_expiration( $user_obj->ID );

	if ( ! $expiration ) {
		return $user;
	}

	if ( $expiration < time() ) {
		return new \WP_Error(
			'password_expired',
			'Your password has expired. Please reset it.' ,
			[
				'username' => $username,
			]
		);
	}

	return $user;
}
add_filter( 'authenticate', __NAMESPACE__ . '\check_password_expiration', 999, 3 );

/**
 * Perform a redirect when a user's password has expired.
 *
 * We use the 'login_redirect' filter because it occurs after the user has been authenticated.
 *
 * @param string             $redirect_to           The redirect URL.
 * @param string             $requested_redirect_to The requested redirect URL.
 * @param \WP_User|\WP_Error $user                  The WP_User object.
 * @return string The redirect URL.
 */
function login_redirect( $redirect_to, $requested_redirect_to, $user ) {
	if ( \is_wp_error( $user ) && 'password_expired' === $user->get_error_code() ) {
		$redirect_url = add_query_arg(
			[
				'action'           => 'lostpassword',
				'password_expired' => 'true',
				'user_login'       => $user->get_error_data()['username'],
			],
			wp_login_url()
		);

		wp_safe_redirect( $redirect_url );
		die;
	}

	return $redirect_to;
}
add_filter( 'login_redirect', __NAMESPACE__ . '\login_redirect', 50, 3 );

/**
 * Filters the message to display when a user's password has expired.
 *
 * @param string $message The message.
 * @return string The message.
 */
function password_expired_message( $message ) {
	if ( empty( $_GET['action'] ) || 'lostpassword' !== $_GET['action'] ) {
		return $message;
	}

	if ( empty( $_GET['password_expired'] ) || 'true' !== $_GET['password_expired'] ) {
		return $message;
	}

	$message = wp_get_admin_notice(
		'Your password has expired. To reset it, please enter your username or email address below. You will receive an email with instructions on how to proceed.',
		[
			'type'               => 'error',
			'additional_classes' => [ 'password-expired' ],
		]
	);

	return $message;
}
add_filter( 'login_message', __NAMESPACE__ . '\password_expired_message' );

/**
 * Sets the user's password expiration date when they reset their password.
 *
 * @param \WP_User $user The user object.
 * @return void
 */
function set_password_expiration_on_password_reset( $user ) {
	$expiration = time() + get_password_expiration_interval();
	set_password_expiration( $user->ID, $expiration );
}
add_action( 'after_password_reset', __NAMESPACE__ . '\set_password_expiration_on_password_reset' );

/**
 * Sets the user's password expiration date when they change their password via Dashboard > Users > Profile.
 *
 * This is necessary because the 'after_password_reset' hook does not fire when
 * a user changes their password via the Dashboard.
 *
 * @param int      $user_id       The user ID.
 * @param \WP_User $old_user_data The old user data.
 * @param array    $userdata      The new user data.
 * @return void
 */
function set_password_expiration_on_password_change( $user_id, $old_user_data, $userdata ) {
	if ( ! isset( $userdata['user_pass'] ) ) {
		return;
	}

	if ( $userdata['user_pass'] === $old_user_data->user_pass ) {
		return;
	}

	$expiration = time() + get_password_expiration_interval();
	set_password_expiration( $user_id, $expiration );
}
add_action( 'profile_update', __NAMESPACE__ . '\set_password_expiration_on_password_change', 10, 3 );

/**
 * Set password expiration for newly activated users.
 *
 * @param int $user_id The user ID.
 * @return void
 */
function set_password_expiration_on_user_activation( $user_id ) {
	$expiration = time() + get_password_expiration_interval();
	set_password_expiration( $user_id, $expiration );
}
add_action( 'user_register', __NAMESPACE__ . '\set_password_expiration_on_user_activation' );

/**
 * Hide the 'Confirm weak password' link on the password reset dialog.
 *
 * This forces users to choose a strong password.
 */
function hide_weak_password() {
  // remove the 'confirm weak password' link from the password reset dialog ?>
  <script>
    document.addEventListener("DOMContentLoaded", function(event) {
      var elements = document.getElementsByClassName('pw-weak');
      var requiredElement = elements[0];
      if(requiredElement){
        requiredElement.remove();
      }
    });
</script>
<?php }
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\hide_weak_password' );

/**
 * Enqueues the JS for generating the expiration warning notice.
 */
function enqueue_expiration_warning_notice_js() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_expiration = get_password_expiration( get_current_user_id() );
	if ( ! $user_expiration || $user_expiration < time() ) {
		return;
	}

	if ( time() + get_password_expiration_warning_interval() < $user_expiration ) {
		return;
	}

	// Check whether the user has dismissed this notice.
	$user_dismissed_notices = get_user_meta( get_current_user_id(), 'password_expiration_warning_dismissed', true );
	if ( is_array( $user_dismissed_notices ) && in_array( $user_expiration, $user_dismissed_notices, true ) ) {
		return;
	}

	wp_enqueue_script(
		'password-expiration-warning-notice',
		plugins_url() . '/wds-citytech/assets/js/password-expiration-warning-notice.js',
		array( 'jquery' ),
		null,
		true
	);

	wp_localize_script(
		'password-expiration-warning-notice',
		'passwordExpirationWarningNotice',
		[
			'dismissUrl' => wp_nonce_url( admin_url( 'admin-ajax.php?action=dismiss_password_expiration_warning_notice' ), 'dismiss_password_expiration_warning_notice' ),
			'expiration' => $user_expiration,
			'resetUrl'   => add_query_arg(
				[
					'action'           => 'lostpassword',
					'password_expired' => 'warning',
					'user_login'       => wp_get_current_user()->user_login,
				],
				wp_login_url()
			),
		]
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_expiration_warning_notice_js' );

/**
 * AJAX handler for dismissing the password expiration warning notice.
 */
function dismiss_password_expiration_warning_notice() {
	check_ajax_referer( 'dismiss_password_expiration_warning_notice' );

	$user_expiration = get_password_expiration( get_current_user_id() );

	$user_dismissed_notices = get_user_meta( get_current_user_id(), 'password_expiration_warning_dismissed', true );

	if ( ! is_array( $user_dismissed_notices ) ) {
		$user_dismissed_notices = [];
	}

	$user_dismissed_notices[] = $user_expiration;

	update_user_meta( get_current_user_id(), 'password_expiration_warning_dismissed', $user_dismissed_notices );
}
add_action( 'wp_ajax_dismiss_password_expiration_warning_notice', __NAMESPACE__ . '\dismiss_password_expiration_warning_notice' );

/**
 * When logging in, delete all instances of 'password_expiration_warning_dismissed_' usermeta.
 *
 * @param string  $user_login The user's login.
 * @param WP_User $user       The user object.
 */
function delete_password_expiration_warning_dismissed_usermeta( $user_login, $user ) {
	delete_user_meta( $user->ID, 'password_expiration_warning_dismissed' );
}
add_action( 'wp_login', __NAMESPACE__ . '\delete_password_expiration_warning_dismissed_usermeta', 10, 2 );

/**
 * Don't show password reset fields on Dashboard > Profile.
 */
add_filter( 'show_password_fields', function() {
	return is_super_admin();
} );
