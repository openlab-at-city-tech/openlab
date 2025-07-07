<?php
/**
 * This file contains functions related to ajax calls
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MOPPM_Ajax' ) ) {
	/**
	 * Class for ajax related functions
	 */
	class MOPPM_Ajax {
		/**
		 * Constructor function
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'moppm_ajax_fun' ) );
		}

		/**
		 * Main ajax function
		 *
		 * @return void
		 */
		public function moppm_ajax_fun() {
			if ( isset( $_POST['moppm_user_password'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is used in each functions separately
				$username = isset( $_POST['moppm_user_name'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_user_name'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is used in each functions separately
				$password = $_POST['moppm_user_password']; //phpcs:ignore WordPress.Security.NonceVerification.Missing , WordPress.Security.ValidatedSanitizedInput.MissingUnslash , WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce is used in each functions separately and do not sanitize or unsplash password
				do_action( 'authenticate', null, $username, $password );

			}
			add_action( 'wp_ajax_moppm_ajax', array( $this, 'moppm_ajax' ) );
			add_action( 'wp_ajax_moppm_login', array( $this, 'moppm_login' ) );
			add_action( 'wp_ajax_nopriv_moppm_login', array( $this, 'moppm_login' ) );
		}

		/**
		 * Function to handle ajax function calls
		 *
		 * @return void
		 */
		public function moppm_ajax() {
			global $moppm_db_queries;
			if ( isset( $_POST['option'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is used in each functions separately
				$option = sanitize_text_field( wp_unslash( $_POST['option'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is used in each functions separately
				switch ( $option ) {
					case 'moppm_setting_enable_disable':
						$this->moppm_setting_enable_disable();
						break;
					case 'moppm_log_out_form':
						$this->moppm_log_out_form();
						break;
					case 'moppm_setting_enable_disable_form':
						$this->moppm_setting_enable_disable_form();
						break;
					case 'moppm_reset_button':
						$this->moppm_reset_button_submit();
						break;
					case 'moppm_enable_disable_report':
						$this->moppm_enable_disable_report();
						break;
					case 'moppm_report_remove':
						$this->moppm_report_remove();
						break;
					case 'moppm_clear_button':
						$this->moppm_clear_button();
						break;
					case 'moppm_update_plan':
						$this->moppm_update_plan();
						break;
					case 'moppm_black_friday_remove':
						$this->moppm_black_friday_remove();
						break;

				}
			}
		}

		/**
		 * Function to remove black friday sale offer baner
		 *
		 * @return void
		 */
		public function moppm_black_friday_remove() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm-remove-offer-banner' ) ) {
				wp_send_json( 'ERROR' );
				return;
			} else {
				update_site_option( 'moppm_remove_offer_banner', true );
				wp_send_json( 'SUCCESS' );
			}

		}

		/**
		 * Function to call new password submit function
		 *
		 * @return void
		 */
		public function moppm_login() {
			$option = isset( $_POST['option'] ) ? sanitize_text_field( wp_unslash( $_POST['option'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is used in each functions separately.
			if ( 'moppm_submit_new_pass' === $option ) {
				$this->moppm_submit_new_pass();
			}
		}

		/**
		 * Function to handle log out form of miniorange account
		 *
		 * @return void
		 */
		public function moppm_log_out_form() {
			delete_site_option( 'moppm_email' );
			delete_site_option( 'moppm_customerKey' );
			delete_site_option( 'moppm_api_key' );
			delete_site_option( 'moppm_customer_token' );
			delete_site_option( 'moppm_registration_status' );
		}

		/**
		 * Function for handling update plan
		 *
		 * @return void
		 */
		public function moppm_update_plan() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm_update_plan' ) ) {
				wp_send_json( 'ERROR' );
				return;
			}
			$moppm_all_plannames = isset( $_POST['planname'] ) ? sanitize_text_field( wp_unslash( $_POST['planname'] ) ) : '';
			$moppm_plan_type     = isset( $_POST['plantype'] ) ? sanitize_text_field( wp_unslash( $_POST['plantype'] ) ) : '';
			update_site_option( 'moppm_planname', $moppm_all_plannames );
			update_site_option( 'moppm_plantype', $moppm_plan_type );

		}

		/**
		 * Function to handle enable disable report
		 *
		 * @return void
		 */
		public function moppm_enable_disable_report() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm_enable_disable_report' ) ) {
				wp_send_json( 'ERROR' );
				return;
			}
			$moppm_enable_disable_ppm = isset( $_POST['moppm_enable_disable_report'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_enable_disable_report'] ) ) : '';
			update_site_option( 'moppm_enable_disable_report', $moppm_enable_disable_ppm );
			if ( 'on' === $moppm_enable_disable_ppm ) {
				wp_send_json( 'true' );
			} elseif ( '' === $moppm_enable_disable_ppm ) {
				wp_send_json( 'false' );
			}

		}

		/**
		 * Function to clear report table
		 */
		public function moppm_clear_button() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm_clear_nonce' ) ) {
				wp_send_json( 'ERROR' );
				return;
			} else {
				global $wpdb;
				global $moppm_db_queries;
				$moppm_db_queries->clear_report_list();
				return;
			}
		}

		/**
		 * Function to remove report of a particular user
		 *
		 * @return void
		 */
		public function moppm_report_remove() {
			global $moppm_db_queries;
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm_remove_Nonce' ) ) {
				wp_send_json( 'ERROR' );
				return;
			}
			if ( isset( $_POST['user_value'] ) ) {
				$user_id = sanitize_text_field( wp_unslash( $_POST['user_value'] ) );
				$moppm_db_queries->delete_report_list( $user_id );
			}
		}

		/**
		 * Function to handle one click reset password
		 *
		 * @return void
		 */
		public function moppm_reset_button_submit() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppm_reset_nonce' ) ) {
				wp_send_json( 'ERROR' );
				return;
			}
			$users         = get_users();
			$no_of_attempt = get_site_option( 'no_of_of_attempt' );
			if ( ! $no_of_attempt ) {
				$no_of_attempt = 1;
			}
			$no_of_attempt = $no_of_attempt++;
			update_site_option( 'no_of_of_attempt', $no_of_attempt );
			$message = 'Dear customer,<br>
            You have successfully reset the password for all your users. They will be asked to reset their password the next time they login';
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$email   = get_site_option( 'admin_email' );
			$result  = wp_mail( $email, 'Reset All Password - WordPress', $message, $headers );
			if ( ! $result ) {
				wp_send_json( 'SMTP_NOT_SET' );
				return;
			}
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					add_user_meta( $user->id, 'moppm_points', '1' );
					$sessions = WP_Session_Tokens::get_instance( $user->id );
					$sessions->destroy_all();
				}
			}

		}

		/**
		 * Function to handle unable disable of password policies
		 *
		 * @return void
		 */
		public function moppm_setting_enable_disable() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'PPMsettingNonce' ) ) {
				wp_send_json( 'ERROR' );
				return;
			}
			if ( isset( $_POST['moppm_enable_ppm'] ) ) {
				$moppm_enable_disable_ppm = sanitize_text_field( wp_unslash( $_POST['moppm_enable_ppm'] ) );
				update_site_option( 'Moppm_enable_disable_ppm', $moppm_enable_disable_ppm );
			} else {
				$moppm_enable_disable_ppm = '';
				update_site_option( 'Moppm_enable_disable_ppm', $moppm_enable_disable_ppm );
			}
			if ( 'on' === $moppm_enable_disable_ppm ) {
				wp_send_json( 'true' );
			} elseif ( '' === $moppm_enable_disable_ppm ) {
				wp_send_json( 'false' );
			}
		}

		/**
		 * Function to handle different password policies for all users.
		 *
		 * @return void
		 */
		public function moppm_setting_enable_disable_form() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'PPMsettingNonce' ) ) {
				wp_send_json( 'ERROR' );
				return;
			}
			$moppm_numeric_digit         = isset( $_POST['moppm_numeric_digit'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_numeric_digit'] ) ) : '';
			$moppm_enable_disable_expiry = isset( $_POST['moppm_enable_disable_expiry'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_enable_disable_expiry'] ) ) : '';
			$moppm_letter                = isset( $_POST['moppm_letter'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_letter'] ) ) : '';
			$moppm_first_reset           = isset( $_POST['moppm_first_reset'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_first_reset'] ) ) : '';
			$moppm_digit                 = isset( $_POST['moppm_digit'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['moppm_digit'] ) ) ) : '';
			$moppm_special_char          = isset( $_POST['moppm_special_char'] ) ? sanitize_text_field( wp_unslash( $_POST['moppm_special_char'] ) ) : '';
			'true' === $moppm_letter ? update_site_option( 'moppm_letter', 1 ) : update_site_option( 'moppm_letter', 0 );
			'true' === $moppm_first_reset ? update_site_option( 'moppm_first_reset', 1 ) : update_site_option( 'moppm_first_reset', 0 );
			'true' === $moppm_numeric_digit ? update_site_option( 'moppm_Numeric_digit', 1 ) : update_site_option( 'moppm_Numeric_digit', 0 );
			'true' === $moppm_enable_disable_expiry ? update_site_option( 'moppm_enable_disable_expiry', 1 ) : update_site_option( 'moppm_enable_disable_expiry', 0 );
			'true' === $moppm_special_char ? update_site_option( 'moppm_special_char', 1 ) : update_site_option( 'moppm_special_char', 0 );
			if ( $moppm_digit > 7 && $moppm_digit < 26 ) {
				update_site_option( 'moppm_digit', $moppm_digit );
			} else {
				wp_send_json( 'Digit_Invalid' );
			}
			wp_send_json( 'true' );
		}

		/**
		 * Function to handle new password submission
		 *
		 * @return void
		 */
		public function moppm_submit_new_pass() {
			global $moppm_db_queries;
			$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'moppmresetformnonce' ) ) {
				wp_send_json_error( MOPPM_Messages::SOMETHING_WENT_WRONG );
			}
			$session_id = isset( $_POST['session_id'] ) ? sanitize_text_field( wp_unslash( $_POST['session_id'] ) ) : '';
			if ( isset( $_POST['moppm_save_pass'] ) ) {
				$moppm_submit_new_pass = sanitize_text_field( wp_unslash( $_POST['moppm_save_pass'] ) );
				update_site_option( 'moppm_save_pass', $moppm_submit_new_pass );
			}
			$newpass  = isset( $_POST['newpass'] ) ? $_POST['newpass'] : '';//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash ,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize and unslash password.
			$newpass2 = isset( $_POST['newpass2'] ) ? $_POST['newpass2'] : '';//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash ,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize and unslash password.
			$oldpass  = isset( $_POST['oldpass'] ) ? $_POST['oldpass'] : '';//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash ,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize and unslash password.
			$userdata = get_transient( $session_id );
			$user_id  = $userdata['moppm_user_id'];
			if ( ! $user_id ) {
				wp_send_json_error( MOPPM_Messages::SESSION_TIMEOUT );
			}
			$pattern = ' ';
			if ( strpos( $newpass2, $pattern ) ) {
				wp_send_json_error( MOPPM_Messages::BLANK_PASSWORD );
			}
			$user      = get_user_by( 'ID', $user_id );
			$user_pass = $user->data->user_pass;
			$user_name = $user->data->user_login;
			if ( ! wp_check_password( $oldpass, $user_pass, $user_id ) ) {
				wp_send_json_error( MOPPM_Messages::WRONG_CURRENT_PASS );
			} elseif ( $newpass !== $newpass2 ) {
				wp_send_json_error( MOPPM_Messages::MISMATCH_PASSWORDS );
			}
			if ( $newpass === $newpass2 ) {
				$result = Moppm_Utility::validate_password( $newpass2 );
				if ( 'VALID' !== $result ) {
					wp_send_json_error( $result );
				}
				$moppm_count = Moppm_Utility::check_password_score( $newpass2 );
				update_user_meta( $user_id, 'moppm_pass_score', $moppm_count );
				$log_out_time = gmdate( 'M j, Y, g:i:s a' );
				if ( get_site_option( 'moppm_enable_disable_report' ) === 'on' ) {
					$moppm_db_queries->update_report_list( $user_id, $log_out_time );
				}
				wp_set_password( $newpass, $user_id );
				$meta_key = 'moppm_last_pass_timestmp';
				update_user_meta( $user_id, $meta_key, time() );
				update_user_meta( $user_id, 'moppm_first_reset', '2' );
				$info                  = array();
				$info['user_login']    = $user_name;
				$info['user_password'] = $newpass;
				$info['remember']      = true;
				$response = array( 'message' => MOPPM_Messages::PASSWORD_SAVED, 'user_id' => $user_id );
				wp_send_json_success($response);
			}
		
		}
	}
}
new MOPPM_Ajax();
