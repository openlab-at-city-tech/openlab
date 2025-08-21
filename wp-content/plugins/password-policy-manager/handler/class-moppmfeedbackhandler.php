<?php
/**
 * This file is used to handle feedback on deactivation of the plugin.
 *
 * @package    password-policy-manager/handler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MOPPMFeedbackHandler' ) ) {
	/**
	 * Class to handle user feedback
	 */
	class MOPPMFeedbackHandler {
		
		/**
		 * Construct function.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'moppm_feedback_actions' ) );
			add_action( 'init', array( $this, 'moppm_pass2login_redirect' ) );
		}

        /**
		 * Logs in the users.
		 *
		 * @return void
		 */
		public function moppm_pass2login_redirect(){
			$nonce = isset( $_POST['moppm_login_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_login_nonce'] ) ) : null;
			if ( ! wp_verify_nonce( $nonce, 'moppm-login-nonce' ) ) {
				return;
			}
			$user_id         = isset( $_POST['mopppm_userid'] ) ? sanitize_text_field( wp_unslash( $_POST['mopppm_userid'] ) ) : '';
			$session_id      = isset( $_POST['moppm_session_id'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_session_id'] ) ) : '';
			$user_data       = get_transient( $session_id );
			if( empty( $user_data ) || empty( $user_id ) || (int) $user_id !== (int) $user_data['moppm_user_id'] ) {
				return;
			}
			$currentuser = get_user_by( 'id', $user_id );
			do_action( 'miniorange_post_authenticate_user_login', $currentuser, '', null );
			wp_set_current_user( $user_id, $currentuser->user_login );
			delete_expired_transients( true );
			wp_set_auth_cookie( $user_id, true );
			wp_safe_redirect( home_url());
			exit;
		}
		/**
		 * Function to handle feedback actions.
		 *
		 * @return void
		 */
		public function moppm_feedback_actions() {

			if ( current_user_can( 'manage_options' ) && isset( $_POST['option'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in below function
				if ( isset( $_POST['option'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in below function
					switch ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in below function
						case 'moppm_skip_feedback':
						case 'moppm_feedback':
							$this->handle_feedback();
							break;
					}
				}
			}
		}
		/**
		 * Function to handle feedback of user.
		 *
		 * @return void
		 */
		public function handle_feedback() {

			if ( MOPPM_TEST_MODE ) {
				deactivate_plugins( dirname( dirname( __FILE__ ) ) . '\\miniorange-password-policy-setting.php' );
				return;
			}
			$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_key( $_POST['_wpnonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm_feedback' ) ) {
				do_action( 'moppm_show_message', MOPPM_Messages::show_message( 'ERROR' ), 'ERROR' );
				return;
			}
			$user            = wp_get_current_user();
			$feedback_option = isset( $_POST['option'] ) ? sanitize_text_field( wp_unslash( $_POST['option'] ) ) : '';
			$message         = 'Plugin Deactivated ';

			$deactivation_reason = isset( $_POST['moppm_feedback'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_feedback'] ) ) : 'NA';

			if ( 'other' === $deactivation_reason || 'specific_feature' === $deactivation_reason ) {
				if ( isset( $_POST['moppm_query_feedback'] ) ) {
					$deactivate_reason_message = '[' . $deactivation_reason . ']-' . sanitize_text_field( wp_unslash( $_POST['moppm_query_feedback'] ) );
				}
			} elseif ( 'It_is_not_what_I_am_looking_for' === $deactivation_reason ) {
				if ( isset( $_POST['moppm_query_feedback_specific_feature'] ) ) {
					$deactivate_reason_message = '[It is not what I am looking for] -' . sanitize_text_field( wp_unslash( $_POST['moppm_query_feedback_specific_feature'] ) );
				}
			} else {
				$deactivate_reason_message = $deactivation_reason;
			}

			$activation_date = get_site_option( 'moppm_activated_time' );
			$current_date    = time();
			$diff            = $activation_date - $current_date;
			if ( false === $activation_date ) {
				$days = 'NA';
			} else {
				$days = abs( round( $diff / 86400 ) );
			}
			$reply_required = isset( $_POST['moppm_get_reply'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_get_reply'] ) ) : '';
			if ( ! empty( $reply_required ) ) {
				$message .= ' &nbsp; [Reply:<b style="color:red";>' . " don't reply  " . '</b> ';
			} else {
				$message .= ' [Reply: yes  ';
			}

			$message .= ' D:' . esc_html( $days );

			$message        .= '    Feedback : ' . esc_html( $deactivate_reason_message ) . '';
			$moppm_send_conf = isset( $_POST['moppm_send_conf'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_send_conf'] ) ) : '';
			if ( ! empty( $moppm_send_conf ) ) {

				$message .= Moppm_Utility::moppm_send_configuration( true );
			}

			$email = isset( $_POST['query_mail'] ) ? sanitize_email( wp_unslash( $_POST['query_mail'] ) ) : '';
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$email = esc_html( get_site_option( 'moppm_email' ) );
				if ( empty( $email ) ) {
					$email = $user->user_email;
				}
			}
			$feedback_reasons = new MOPPM_Api();
			if ( ! is_null( $feedback_reasons ) ) {
				$submited = json_decode( $feedback_reasons->send_email_alert( $email, $message, $feedback_option ), true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && 'ERROR' === $submited['status'] ) {
						do_action( 'moppm_show_message', $submited['message'], 'ERROR' );
					} else {
						if ( false === $submited ) {
							do_action( 'moppm_show_message', 'Error while submitting the query.', 'ERROR' );
						}
					}
				}
					deactivate_plugins( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'miniorange-password-policy-setting.php' );
					do_action( 'moppm_show_message', 'Thank you for the feedback.', 'SUCCESS' );
			}
		}
	}
}
new MOPPMFeedbackHandler();
