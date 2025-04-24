<?php
/**
 * File to submit contact us form
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	global $moppm_dirname,$moppm_db_queries;

if ( current_user_can( 'manage_options' ) && isset( $_POST['option'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification.Missing -- have used nonce in function 
	$option = sanitize_text_field( wp_unslash( $_POST['option'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing -- have used nonce in function
	switch ( $option ) {
		case 'moppm_send_query':
			moppm_handle_support_form();
			break;
	}
}

	$current_user_var = wp_get_current_user();
	$phone            = get_site_option( 'moppm_admin_phone' );


if ( empty( $email ) ) {
	$email = $current_user_var->user_email;
}
	require_once $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'support.php';
/**
 * Function to handle support form
 *
 * @return void
 */
function moppm_handle_support_form() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'sendQueryNonce' ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
		return;
	}
	$email = isset( $_POST['query_email'] ) ? sanitize_email( wp_unslash( $_POST['query_email'] ) ) : '';
	$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
	$phone = isset( $_POST['query_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['query_phone'] ) ) : '';
	if ( empty( $email ) || empty( $query ) ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'SUPPORT_FORM_VALUES' ), 'ERROR' );
		return;
	}
	$contact_us = new MOPPM_Api();
	$contact_us->submit_contact_us( $email, $phone, $query );
	if ( json_last_error() === JSON_ERROR_NONE ) {
		do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'SUPPORT_FORM_SENT' ), 'SUCCESS' );
		return;
	}
	do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'SUPPORT_FORM_ERROR' ), 'ERROR' );
}
