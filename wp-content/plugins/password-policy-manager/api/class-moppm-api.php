<?php
/**
 * This file contains class related to api functions.
 *
 * @package password-policy-manager/api
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MOPPM_Api' ) ) {
	/**
	 * Class to call all APIs
	 */
	class MOPPM_Api {

		/**
		 * Function for wp_remote_post
		 *
		 * @param string $url url.
		 * @param array  $args argument to wp_remote_post.
		 * @return string
		 */
		public static function wp_remote_post( $url, $args = array() ) {
			$response = wp_remote_post( $url, $args );
			if ( ! is_wp_error( $response ) ) {
				return $response['body'];
			} else {
				$message = 'Please enable curl extension.';

				return wp_json_encode(
					array(
						'status'  => 'ERROR',
						'message' => $message,
					)
				);
			}
		}

		/**
		 * Function to make wp_remote_post call
		 *
		 * @param string $url url.
		 * @param string $fields fields for wp_remote_post call.
		 * @param array  $http_header_array header for the request.
		 * @return string
		 */
		public static function make_curl_call( $url, $fields, $http_header_array = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF-8',
			'Authorization' => 'Basic',
		) ) {
			if ( gettype( $fields ) !== 'string' ) {
				$fields = wp_json_encode( $fields );
			}

			$args = array(
				'method'      => 'POST',
				'body'        => $fields,
				'timeout'     => '20',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $http_header_array,
			);

			$response = self::wp_remote_post( $url, $args );
			return $response;
		}

		/**
		 * Function to get key of user
		 *
		 * @param string $email email of user.
		 * @param string $password password of user.
		 * @return string
		 */
		public static function get_user_key( $email, $password ) {
			$url      = MOPPM_Constants::HOST_NAME . '/moas/rest/customer/key';
			$fields   = array(
				'email'    => $email,
				'password' => $password,
			);
			$json     = wp_json_encode( $fields );
			$response = self::make_curl_call( $url, $json );
			return $response;
		}

		/**
		 * Function to handle forgot password of miniorange account
		 *
		 * @return string
		 */
		public static function forgot_password() {
			$url        = MOPPM_Constants::HOST_NAME . '/moas/rest/customer/password-reset';
			$email      = get_site_option( 'moppm_email' );
			$key        = get_site_option( 'moppm_customerKey' );
			$api        = get_site_option( 'moppm_api_key' );
			$token      = get_site_option( 'moppm_customer_token' );
			$fields     = array( 'email' => $email );
			$json       = wp_json_encode( $fields );
			$authheader = self::createauthheader( $key, $api );
			$response   = self::make_curl_call( $url, $json, $authheader );
			return $response;
		}

		/**
		 * Function to check if user exists or not
		 *
		 * @param string $email email of user.
		 * @return string
		 */
		public static function check_user( $email ) {
			$url      = MOPPM_Constants::HOST_NAME . '/moas/rest/customer/check-if-exists';
			$fields   = array(
				'email' => $email,
			);
			$json     = wp_json_encode( $fields );
			$response = self::make_curl_call( $url, $json );
			return $response;
		}

		/**
		 * Function to create user
		 *
		 * @param string $email email of user.
		 * @param string $company company of user.
		 * @param string $password password of user.
		 * @param string $phone phone number of user.
		 * @param string $first_name first name of user.
		 * @param string $last_name last name of user.
		 * @return string
		 */
		public static function create_user( $email, $company, $password, $phone = '', $first_name = '', $last_name = '' ) {
			$url      = MOPPM_Constants::HOST_NAME . '/moas/rest/customer/add';
			$fields   = array(
				'companyName'    => $company,
				'areaOfInterest' => 'WordPress Password Policy Plugin',
				'firstname'      => $first_name,
				'lastname'       => $last_name,
				'email'          => $email,
				'phone'          => $phone,
				'password'       => $password,
			);
			$json     = wp_json_encode( $fields );
			$response = self::make_curl_call( $url, $json );
			return $response;
		}

		/**
		 * Function to submit contact us form
		 *
		 * @param string $q_email email of user.
		 * @param string $q_phone phone number of user.
		 * @param string $query query of user.
		 * @return string
		 */
		public static function submit_contact_us( $q_email, $q_phone, $query ) {
			$current_user = wp_get_current_user();
			$url          = MOPPM_Constants::HOST_NAME . '/moas/rest/customer/contact-us';
			$query        = '[miniOrange password policy | Setting -V ' . MOPPM_VERSION . ']: ' . esc_html( $query );
			$company      = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$fields       = array(
				'firstName' => $current_user->user_firstname,
				'lastName'  => $current_user->user_lastname,
				'company'   => $company,
				'email'     => $q_email,
				'ccEmail'   => 'mfasupport@xecurify.com',
				'phone'     => $q_phone,
				'query'     => $query,
			);
			$field_string = wp_json_encode( $fields );
			$response     = self::make_curl_call( $url, $field_string );
			return $response;
		}

		/**
		 * Function to send feedback email
		 *
		 * @param string $email email of user.
		 * @param string $message feedback message.
		 * @param string $feedback_option feedback option.
		 * @return string
		 */
		public static function send_email_alert( $email, $message, $feedback_option ) {
			global $user;
			$company   = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$url       = MOPPM_Constants::HOST_NAME . '/moas/api/notify/send';
			$user_key  = MOPPM_Constants::DEFAULT_CUSTOMER_KEY;
			$apikey    = MOPPM_Constants::DEFAULT_API_KEY;
			$fromemail = 'no-reply@xecurify.com';
			if ( 'moppm_skip_feedback' === $feedback_option ) {
				$subject = 'Deactivate [Feedback Skipped]: miniOrange password policy setting';
			} elseif ( 'moppm_feedback' === $feedback_option ) {
				$subject = 'Feedback: miniOrange password policy setting - ' . sanitize_email( $email );
			}
			$user    = wp_get_current_user();
			$query   = '[miniOrange password policy setting: - V ' . MOPPM_VERSION . ']: ' . $message;
			$content = '<div >Hello, <br><br>First Name :' . sanitize_text_field( $user->user_firstname ) . '<br><br>Last  Name :' . sanitize_text_field( $user->user_lastname ) . '   <br><br>Company :<a href="' . $company . '" target="_blank" >' . $company . '</a><br><br>Email :<a href="mailto:' . sanitize_email( $email ) . '" target="_blank">' . sanitize_email( $email ) . '</a><br><br>Query :' . $query . '</div>';
			$fields  = array(
				'customerKey' => $user_key,
				'sendEmail'   => true,
				'email'       => array(
					'customerKey' => $user_key,
					'fromEmail'   => $fromemail,
					'fromName'    => 'Xecurify',
					'toEmail'     => 'mfasupport@xecurify.com',
					'toName'      => 'mfasupport@xecurify.com',
					'subject'     => $subject,
					'content'     => $content,
				),
			);

			$field_string = wp_json_encode( $fields );
			$authheader   = self::createauthheader( $user_key, $apikey );
			$response     = self::make_curl_call( $url, $field_string, $authheader );
			return $response;
		}
		/**
		 * Function to create auth header
		 *
		 * @param string $user_key key of user.
		 * @param string $apikey user api key.
		 * @return string
		 */
		public static function createauthheader( $user_key, $apikey ) {
			$currenttimestampinmillis = round( microtime( true ) * 1000 );
			$currenttimestampinmillis = number_format( $currenttimestampinmillis, 0, '', '' );
			$stringtohash             = $user_key . $currenttimestampinmillis . $apikey;
			$hashvalue                = hash( 'sha512', $stringtohash );
			$headers                  = array(
				'Content-Type'  => 'application/json',
				'Customer-Key'  => $user_key,
				'Timestamp'     => $currenttimestampinmillis,
				'Authorization' => $hashvalue,
			);

			return $headers;
		}
	}
}
