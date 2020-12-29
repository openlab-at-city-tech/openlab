<?php
/**
 * AJAX methods.
 */

namespace OpenLab\SignupCodes;

/**
 * AJAX handler for registration email check
 *
 * Return values:
 *   1: success
 *   2: no email provided
 *   3: not a valid email address
 *   4: unsafe
 *   5: not in domain whitelist
 *   6: email exists
 *   7: a known undergraduate student address
 */
function ajax_email_check() {
	$email = isset( $_POST['email'] ) ? $_POST['email'] : false;

	$retval = '1';

	if ( !$email ) {
		$retval = '2'; // no email
	} else {
		if ( ! is_email( $email ) ) {
			$retval = '3'; // Not an email address
		} else if ( function_exists( 'is_email_address_unsafe' ) && is_email_address_unsafe( $email ) ) {
			$retval = '4'; // Unsafe
		} else if ( ! cac_ncs_email_domain_is_in_whitelist( $email ) ) {
			$retval = '5';
		} else if ( email_exists( $email ) ) {
			$retval = '6';
		}
	}

	die( $retval );
}
add_action( 'wp_ajax_cac_ajax_email_check', __NAMESPACE__ . '\\ajax_email_check' );
add_action( 'wp_ajax_nopriv_cac_ajax_email_check', __NAMESPACE__ . '\\ajax_email_check' );

/**
 * AJAX handler for registration code check
 *
 * Return values:
 *   1: success
 *   0: failure
 */
function ajax_validate_code() {
	$vcode = isset( $_POST['code'] ) ? $_POST['code'] : '';

	$retval = cac_ncs_validate_code( $vcode ) ? '1' : '0';

	echo $retval;
	die();
}
add_action( 'wp_ajax_cac_ajax_vcode_check', __NAMESPACE__ . '\\ajax_validate_code' );
add_action( 'wp_ajax_nopriv_cac_ajax_vcode_check', __NAMESPACE__ . '\\ajax_validate_code' );
