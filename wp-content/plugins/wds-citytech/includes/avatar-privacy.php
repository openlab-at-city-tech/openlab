<?php
/**
 * Avatar Privacy functionality.
 *
 * Allows users to control who can see their avatar.
 */

/**
 * Determines the avatar visibility level for the user.
 *
 * @param int $user_id ID of the user.
 * @return string
 */
function openlab_get_user_avatar_visibility( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = bp_displayed_user_id();
	}

	$visibility = bp_get_user_meta( $user_id, 'avatar_visibility', true );
	if ( ! $visibility ) {
		$visibility = 'public';
	}

	return $visibility;
}

/**
 * Gets avatar visibility levels for OpenLab.
 *
 * @return array
 */
function openlab_get_avatar_visibility_levels() {
	if ( function_exists( 'openlab_get_xprofile_visibility_levels' ) ) {
		return openlab_get_xprofile_visibility_levels();
	}

	$visibility_levels = bp_xprofile_get_visibility_levels();

	if ( isset( $visibility_levels['loggedin'] ) ) {
		$visibility_levels['loggedin']['label'] = 'OpenLab Members';
	}

	if ( isset( $visibility_levels['friends'] ) ) {
		$visibility_levels['friends']['label'] = 'Friends';
	}

	return $visibility_levels;
}

/**
 * Sanitizes avatar visibility.
 *
 * @param string $visibility Avatar visibility level.
 * @return string
 */
function openlab_sanitize_avatar_visibility( $visibility ) {
	$visibility_levels = openlab_get_avatar_visibility_levels();

	if ( ! isset( $visibility_levels[ $visibility ] ) ) {
		return 'public';
	}

	return $visibility;
}

/**
 * Gets the avatar visibility currently submitted on the registration form.
 *
 * @return string
 */
function openlab_get_signup_avatar_visibility_value() {
	if ( empty( $_POST['avatar_visibility'] ) ) {
		return 'public';
	}

	return openlab_sanitize_avatar_visibility( sanitize_text_field( wp_unslash( $_POST['avatar_visibility'] ) ) );
}

/**
 * AJAX callback for updating avatar privacy.
 *
 * @return void
 */
function openlab_ajax_update_avatar_privacy() {
	check_ajax_referer( 'openlab_avatar_privacy', 'nonce' );

	if ( ! isset( $_POST['user_id'] ) || ! isset( $_POST['visibility'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request.', 'openlab' ) ) );
	}

	$user_id    = (int) $_POST['user_id'];
	$visibility = sanitize_text_field( wp_unslash( $_POST['visibility'] ) );

	$visibility_levels = openlab_get_avatar_visibility_levels();
	if ( ! isset( $visibility_levels[ $visibility ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid visibility level.', 'openlab' ) ) );
	}

	$is_my_profile = bp_loggedin_user_id() === $user_id;
	if ( ! $is_my_profile && ! current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to do this.', 'openlab' ) ) );
	}

	bp_update_user_meta( $user_id, 'avatar_visibility', $visibility );

	wp_send_json_success();
}
add_action( 'wp_ajax_openlab_avatar_privacy', 'openlab_ajax_update_avatar_privacy' );

/**
 * Saves avatar visibility at registration.
 *
 * @param array $usermeta Signup metadata.
 * @return array
 */
function openlab_save_avatar_visibility_at_registration( $usermeta ) {
	if ( ! isset( $_POST['avatar_visibility'] ) ) {
		return $usermeta;
	}

	$usermeta['avatar_visibility'] = openlab_get_signup_avatar_visibility_value();

	return $usermeta;
}
add_filter( 'bp_signup_usermeta', 'openlab_save_avatar_visibility_at_registration' );

/**
 * Processes avatar visibility on account activation.
 *
 * @param int    $user_id User ID.
 * @param string $key     Activation key.
 * @param array  $data    Signup data.
 * @return void
 */
function openlab_process_avatar_visibility_at_activation( $user_id, $key, $data ) {
	if ( empty( $data['meta']['avatar_visibility'] ) ) {
		return;
	}

	bp_update_user_meta(
		$user_id,
		'avatar_visibility',
		openlab_sanitize_avatar_visibility( $data['meta']['avatar_visibility'] )
	);
}
add_action( 'bp_core_activated_user', 'openlab_process_avatar_visibility_at_activation', 10, 3 );

/**
 * Enforces avatar privacy settings.
 *
 * @param string $avatar_url Avatar URL.
 * @param array  $args       Arguments passed to the avatar filter.
 * @return string
 */
function openlab_filter_avatar_url_for_privacy( $avatar_url, $args ) {
	// BP only technically supports 'user' but we also sniff for 'member'.
	if ( 'user' !== $args['object'] && 'member' !== $args['object'] ) {
		return $avatar_url;
	}

	$visibility = openlab_get_user_avatar_visibility( $args['item_id'] );

	if ( 'public' === $visibility ) {
		return $avatar_url;
	}

	// Users can always see their own avatar.
	if ( bp_loggedin_user_id() === (int) $args['item_id'] ) {
		return $avatar_url;
	}

	// Admins can always see all avatars.
	if ( current_user_can( 'bp_moderate' ) ) {
		return $avatar_url;
	}

	switch ( $visibility ) {
		case 'loggedin':
			// Logged-in users can see all avatars.
			if ( is_user_logged_in() ) {
				return $avatar_url;
			}
			break;

		case 'friends':
			// Friends can see each other's avatars.
			if ( function_exists( 'friends_check_friendship' ) && friends_check_friendship( bp_loggedin_user_id(), $args['item_id'] ) ) {
				return $avatar_url;
			}
			break;

		case 'adminsonly':
		default:
			// No one can see this avatar (except admins, handled above).
			break;
	}

	$avatar_url = openlab_get_default_avatar_uri();

	return $avatar_url;
}
add_filter( 'bp_core_fetch_avatar_url', 'openlab_filter_avatar_url_for_privacy', 20, 2 );

/**
 * Filters avatar img markup for privacy.
 *
 * @param string $html Avatar img markup.
 * @param array  $args Arguments passed to the avatar filter.
 * @return string
 */
function openlab_filter_avatar_html_for_privacy( $html, $args ) {
	// Get the avatar src from the img tag.
	$src = '';
	if ( preg_match( '/src="([^"]+)"/', $html, $matches ) ) {
		$src = $matches[1];
	}

	if ( ! $src ) {
		return $html;
	}

	$avatar_url = openlab_filter_avatar_url_for_privacy( $src, $args );

	// Replace the src in the img tag.
	$html = preg_replace( '/src="([^"]+)"/', 'src="' . esc_url( $avatar_url ) . '"', $html );

	return $html;
}
add_filter( 'bp_core_fetch_avatar', 'openlab_filter_avatar_html_for_privacy', 20, 2 );
