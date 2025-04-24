<?php
/**
 * This file is related to user account functions
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_utility,$moppm_dirname,$moppm_db_queries;

if ( current_user_can( 'manage_options' ) && isset( $_POST['option'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- used nonce verification in each functions
	$option = trim( sanitize_text_field( wp_unslash( $_POST['option'] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing -- used nonce verification in each functions
	switch ( $option ) {
		case 'moppm_register_user':
			moppm_register_user();
			break;
		case 'moppm_verify_user':
			moppm_verify_user();
			break;
		case 'moppm_cancel':
			moppm_revert_back_registration();
			break;
		case 'moppm_reset_password':
			moppm_reset_password();
			break;
		case 'moppm_goto_verifyuser':
			moppm_goto_sign_in_page();
			break;
	}
}

	$user = wp_get_current_user();

if ( get_site_option( 'moppm_verify_customer' ) === 'true' ) {

	$admin_email = get_site_option( 'moppm_email' ) ? get_site_option( 'moppm_email' ) : '';
	include $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'account' . DIRECTORY_SEPARATOR . 'login.php';
} elseif ( ! moppm_icr() ) {

	include $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'account' . DIRECTORY_SEPARATOR . 'register.php';
} else {
	$email = get_site_option( 'moppm_email' );
	$key   = get_site_option( 'moppm_customerKey' );
	$api   = get_site_option( 'moppm_api_key' );
	$token = get_site_option( 'moppm_customer_token' );
	include $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'account' . DIRECTORY_SEPARATOR . 'profile.php';
}

/**
 * Function for registration of user
 *
 * @return void
 */
function moppm_register_user() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'moppm-account-nonce' ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
		return;
	}
	$company         = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
	$email           = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password        = isset( $_POST['password'] ) ? $_POST['password'] : '';//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash , WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize and unslash password
	$confirmpassword = isset( $_POST['confirmPassword'] ) ? $_POST['confirmPassword'] : '';//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash , WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize and unslash password
	if ( strlen( $password ) < 6 || strlen( $confirmpassword ) < 6 ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'PASS_LENGTH' ), 'ERROR' );
		return;
	}
	if ( $password !== $confirmpassword ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'PASS_MISMATCH' ), 'ERROR' );
		return;
	}
	if ( moppm_check_empty_or_null( $email ) || moppm_check_empty_or_null( $password ) || moppm_check_empty_or_null( $confirmpassword ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'REQUIRED_FIELDS' ), 'ERROR' );
		return;
	}
		update_site_option( 'moppm_email', $email );
		update_site_option( 'company', $company );
		update_site_option( 'password', $password );
		$user    = new MOPPM_Api();
		$content = json_decode( $user->check_user( $email ), true );
	switch ( $content['status'] ) {
		case 'CUSTOMER_NOT_FOUND':
			$user_key = json_decode( $user->create_user( $email, $company, $password, $phone = '', $first_name = '', $last_name = '' ), true );

			if ( strcasecmp( $user_key['status'], 'SUCCESS' ) === 0 ) {
				moppm_save_success_user_config( $email, $user_key['id'], $user_key['apiKey'], $user_key['token'] );
				moppm_get_current_user( $email, $password );
			}

			break;
		default:
			moppm_get_current_user( $email, $password );
			break;
	}
}

/**
 * Function to go to sign in page
 *
 * @return void
 */
function moppm_goto_sign_in_page() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'moppm-account-nonce' ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
		return;
	}
	update_site_option( 'moppm_verify_customer', 'true' );

}

/**
 * Function to revert back on registration page
 *
 * @return void
 */
function moppm_revert_back_registration() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'moppm-account-nonce' ) ) {
			do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
		return;
	}
	delete_site_option( 'moppm_email' );
	delete_site_option( 'moppm_verify_customer' );
}

/**
 * Function to reset password
 *
 * @return void
 */
function moppm_reset_password() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'moppm-account-nonce' ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
		return;
	}
	$user                     = new MOPPM_Api();
	$forgot_password_response = json_decode( $user->forgot_password() );
	if ( 'SUCCESS' === $forgot_password_response->status ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'RESET_PASS' ), 'SUCCESS' );
	}
}

/**
 * Function to verify user
 *
 * @return void
 */
function moppm_verify_user() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'moppm-account-nonce' ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
		return;
	}
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password = isset( $_POST['password'] ) ? $_POST['password'] : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash , WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize and unslash password
	if ( moppm_check_empty_or_null( $email ) || moppm_check_empty_or_null( $password ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'REQUIRED_FIELDS' ), 'ERROR' );
		return;
	}
	moppm_get_current_user( $email, $password );
}


/**
 * Function to get current user details
 *
 * @param string $email email of user.
 * @param string $password password of user.
 * @return void
 */
function moppm_get_current_user( $email, $password ) {
	global $moppm_db_queries;
	$user     = wp_get_current_user();
	$user     = new MOPPM_Api();
	$content  = $user->get_user_key( $email, $password );
	$user_key = json_decode( $content, true );
	if ( json_last_error() === JSON_ERROR_NONE ) {
		if ( isset( $user_key['phone'] ) ) {
			update_site_option( 'moppm_admin_phone', $user_key['phone'] );
		}

		update_site_option( 'moppm_email', $email );
		moppm_save_success_user_config( $email, $user_key['id'], $user_key['apiKey'], $user_key['token'] );
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'REG_SUCCESS' ), 'SUCCESS' );
		return;
	} else {
		update_site_option( 'moppm_verify_customer', 'true' );
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ACCOUNT_EXISTS' ), 'ERROR' );
	}
}

/**
 * Function to save user configurations
 *
 * @param string $email email of user.
 * @param string $id user key.
 * @param string $apikey apikey.
 * @param string $token token.
 * @return void
 */
function moppm_save_success_user_config( $email, $id, $apikey, $token ) {
	update_site_option( 'moppm_customerKey', $id );
	update_site_option( 'moppm_api_key', $apikey );
	update_site_option( 'moppm_customer_token', $token );
	update_site_option( 'moppm_registration_status', 'SUCCESS' );
	delete_site_option( 'moppm_verify_customer' );
	$mo2f_customer_selected_plan = get_option( 'moppm_planname' );
	if ( ! empty( $mo2f_customer_selected_plan ) ) {
		delete_option( 'moppm_planname' );
		?><script>window.location.href="admin.php?page=moppm_upgrade";</script>
		<?php
	}
}

/**

 * Function to find that user is registered
 *
 * @return int
 */
function moppm_icr() {
	$email    = get_site_option( 'moppm_email' );
	$user_key = get_site_option( 'moppm_customerKey' );
	if ( ! $email || ! $user_key || ! is_numeric( trim( $user_key ) ) ) {
		return 0;
	} else {
		return 1;
	}
}

/**
 * Function to check if the value is empty or not
 *
 * @param string $value value.
 * @return boolean
 */
function moppm_check_empty_or_null( $value ) {
	if ( ! isset( $value ) || empty( $value ) ) {
		return true;
	}
	return false;
}
