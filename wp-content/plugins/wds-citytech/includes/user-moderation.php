<?php

namespace OpenLab\UserModeration;

/**
 * User moderation tools.
 */

add_filter( 'authenticate', __NAMESPACE__ . '\\prevent_login', 50 );
add_action( 'show_user_profile', __NAMESPACE__ . '\\markup', 0 );
add_action( 'edit_user_profile', __NAMESPACE__ . '\\markup', 0 );
add_action( 'personal_options_update', __NAMESPACE__ . '\\save' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save' );

/**
 * Borrowed from bp-toolkit.
 *
 * @param int $member_id
 * @return bool
 */
function is_suspended( $member_id ) {
	// Using the same flag as bp-toolkit in case we ever want to integrate.
	$status = get_user_meta( $member_id, 'bptk_suspend', true );
	$status = (int) $status;

	return 1 === $status;
}

/**
 * Borrowed from bp-toolkit.
 *
 * @param int $member_id
 */
function suspend( $member_id ) {
	update_user_meta( $member_id, 'bptk_suspend', 1 );
	\WP_Session_Tokens::get_instance( $member_id )->destroy_all();
}

/**
 * Borrowed from bp-toolkit.
 */
function unsuspend( $member_id ) {
	update_user_meta( $member_id, 'bptk_suspend', 0 );
}

/**
 * Borrowed from bp-toolkit.
 */
function prevent_login( $member = null ) {
	// If login already failed, get out
	if ( is_wp_error( $member ) || empty( $member->ID ) ) {
		return $member;
	}

	// Set the user id.
	$member_id = (int) $member->ID;

	// If the user is blocked, set the wp-login.php error message.
	if ( is_suspended( $member_id ) ) {
		// Set the default message.
		$message = 'This account is not active. If you have questions, please <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us">contact us</a>.';
		// Set an error object to short-circuit the authentication process.
		$member = new \WP_Error( 'bptk_suspended_user', $message );
	}

	return $member;
}

/**
 * Admin markup.
 *
 * @param WP_User $user
 */
function markup( \WP_User $user ) {
	if ( ! current_user_can( 'edit_users' ) ) {
		return;
	}

	?>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label for="is-suspended">Suspend Account</label></th>
				<td>
					<input type="checkbox" <?php checked( is_suspended( $user->ID ) ); ?> value="1" name="is-suspended" id="is-suspended" />
					<p class="description">Suspended accounts cannot log into the OpenLab. User will be logged out of all locations.</p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php

	wp_nonce_field( 'openlab-is-suspended', 'openlab-is-suspended-nonce', false );
}

function save( $user_id ) {
	if ( ! current_user_can( 'edit_users' ) ) {
		return;
	}

	if ( ! isset( $_POST['openlab-is-suspended-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'openlab-is-suspended', 'openlab-is-suspended-nonce' );

	$new_is_suspended = ! empty( $_POST['is-suspended'] );
	$old_is_suspended = is_suspended( $user_id );

	if ( $new_is_suspended === $old_is_suspended ) {
		return;
	}

	if ( $new_is_suspended ) {
		suspend( $user_id );
	} else {
		unsuspend( $user_id );
	}
}
