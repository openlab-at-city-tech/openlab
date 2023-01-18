<?php
/**
 * BuddyPress signup integration.
 */

namespace OpenLab\SignupCodes;

use OpenLab\SignupCodes\Schema;

/**
 * Checks non-CUNY validation codes
 */
function check_signup_validation_code( $result ) {

	foreach( $result['errors']->errors as $error_key => $error ) {
		if ( in_array( 'Sorry, that email address is not allowed!', $error ) ) {
			// Check for a validation code
			if ( isset( $_POST['signup_validation_code'] ) ) {
				$vcode = $_POST['signup_validation_code'];

				if ( cac_ncs_validate_code( $vcode ) ) {
					unset( $result['errors']->errors['user_email'] );
				} else {

					// Otherwise we will have to add a new error and hook something into

					// the registration template. See how BP does this.

					$result['errors']->errors['user_email'][0] = 'Non-CUNY registrations are only permitted with a specially provided signup code.';
				}
			}
		}
	}

	return $result;
}
add_filter( 'bp_core_validate_user_signup', __NAMESPACE__ . '\\check_signup_validation_code', 5 );

/**
 * At user signup, grab the code details and stash in wp_signup usermeta
 *
 * This will then have to be loaded at activation time and recorded in wp_usermeta
 */
function signup_meta( $usermeta ) {
	if ( isset( $_POST['signup_validation_code'] ) ) {
		$data = Schema::get_code_data( $_POST['signup_validation_code'] );
		$usermeta['cac_signup_code'] = $data;
	}

	return $usermeta;
}
add_action( 'bp_signup_usermeta', __NAMESPACE__ . '\\signup_meta' );

/**
 * At user activation, grabs CAC signup code data and moves to usermeta
 */
function activation_meta( $user_id, $key, $user ) {
	$data = isset( $user['meta']['cac_signup_code'] ) ? $user['meta']['cac_signup_code'] : null;

	if ( ! $data ) {
		return;
	}

	update_user_meta( $user_id, 'cac_signup_code_data', $data );
	update_user_meta( $user_id, 'cac_signup_code', $data->ID );

	if ( isset( $data->groups ) ) {
		foreach ( (array) $data->groups as $group_id ) {
			groups_join_group( $group_id, $user_id );
		}
	}

	if ( empty( $data->account_type ) ) {
		// The name is sent, and we have to find the corresponding slug.
		$mt_slug      = '';
		$member_types = openlab_get_member_types();
		foreach ( $member_types as $member_type ) {
			if ( $data->account_type === $member_type->name ) {
				$mt_slug = $member_type->slug;
				break;
			}
		}
		openlab_set_user_member_type( $user_id, $mt_slug );
	}
}
add_action( 'bp_core_activated_user', __NAMESPACE__ . '\\activation_meta', 10, 3 );
