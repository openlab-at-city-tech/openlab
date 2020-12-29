<?php
/**
 * BuddyPress signup integration.
 */

/**
 * Checks non-CUNY validation codes
 */
function cac_check_signup_validation_code( $result ) {

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
add_filter( 'bp_core_validate_user_signup', 'cac_check_signup_validation_code', 5 );

/**
 * At user signup, grab the code details and stash in wp_signup usermeta
 *
 * This will then have to be loaded at activation time and recorded in wp_usermeta
 */
function cac_ncs_signup_meta( $usermeta ) {
	if ( isset( $_POST['signup_validation_code'] ) ) {
		$data = OpenLab\SignupCodes\Schema::get_code_data( $_POST['signup_validation_code'] );
		$usermeta['cac_signup_code'] = $data;
	}

	return $usermeta;
}
add_action( 'bp_signup_usermeta', 'cac_ncs_signup_meta' );

/**
 * At user activation, grabs CAC signup code data and moves to usermeta
 */
function cac_ncs_activation_meta( $user_id, $key, $user ) {

	if ( isset( $user['meta']['cac_signup_code'] ) ) {
		update_user_meta( $user_id, 'cac_signup_code_data', $user['meta']['cac_signup_code'] );
		update_user_meta( $user_id, 'cac_signup_code', $user['meta']['cac_signup_code']->ID );

		if ( isset( $user['meta']['cac_signup_code']->groups ) ) {
			foreach ( (array) $user['meta']['cac_signup_code']->groups as $group_id ) {
				groups_join_group( $group_id, $user_id );
			}
		}
	}
}
add_action( 'bp_core_activated_user', 'cac_ncs_activation_meta', 10, 3 );
