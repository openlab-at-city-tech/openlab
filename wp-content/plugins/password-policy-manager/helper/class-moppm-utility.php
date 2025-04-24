<?php
/**
 * File contains utility functions.
 *
 * @package    password-policy-manager/helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Moppm_Utility' ) ) {
	/**
	 * Utility class
	 */
	class Moppm_Utility {

		/**
		 * Function to validate password
		 *
		 * @param string $password user password.
		 * @return string message
		 */
		public static function validate_password( $password ) {
			$length_pass = strlen( $password );
			if ( ( get_site_option( 'moppm_Numeric_digit' ) === '1' ) && ( ! preg_match( '#[0-9]+#', $password ) ) ) {
				return 'New password must contain numeric value.';
			}
			if ( ( get_site_option( 'moppm_letter' ) === '1' ) && ( ! preg_match( '/[a-z]/', $password ) ) ) {
				return 'New password must contain lower case letter.';
			}
			if ( ( get_site_option( 'moppm_letter' ) === '1' ) && ( ! preg_match( '/[A-Z]/', $password ) ) ) {
				return 'New password must contain upper case letter.';
			}

			if ( ( get_site_option( 'moppm_special_char' ) === '1' ) && ( ! preg_match( "/[@#$\%&\!*?()_+{:;'\><,.}]/", $password ) ) ) {
				return 'New password must contain special character.';
			}
			if ( $length_pass < get_site_option( 'moppm_digit' ) ) {
				return 'New password must contain at least ' . get_site_option( 'moppm_digit' ) . ' characters.';
			}
			return 'VALID';
		}

		/**
		 * Function to return strength of the given password.
		 *
		 * @param string $password user password.
		 * @return int
		 */
		public static function check_password_score( $password ) {
			$score = 0;
			if ( strlen( $password ) > 7 ) {
				$score = $score + 2;
			}
			if ( preg_match( '/[a-z]/', $password ) ) {
				$score++;
			}
			if ( preg_match( '/[A-Z]/', $password ) ) {
				$score++;
			}
			if ( preg_match( '#[0-9]+#', $password ) ) {
				$score++;
			}
			if ( preg_match( "/[@#$!\%&\*()_+{:;'\><,.}]/", $password ) ) {
				$score++;
			}
			if ( strlen( $password ) > 12 ) {
				$score = $score + 2;
			}
			if ( strlen( $password ) > 17 ) {
				$score = $score + 2;
			}
			return $score;
		}
		/**
		 * Function to send plugin configuration.
		 *
		 * @param boolean $send_all_configuration whether to send configuration or not.
		 * @return string
		 */
		public static function moppm_send_configuration( $send_all_configuration = false ) {
			$user_object          = wp_get_current_user();
			$key                  = get_site_option( 'moppm_customerKey' );
			$space                = '<span>&nbsp;&nbsp;&nbsp;</span>';
			$specific_plugins     = array(
				'UM_Functions'   => 'Ultimate Member',
				'wc_get_product' => 'WooCommerce',
				'pmpro_gateways' => 'Paid MemberShip Pro',
			);
			$plugin_configuration = '<br><br><I>Plugin Configuration :-</I>' . $space . ( is_multisite() ? 'Multisite : Yes' : 'Single-site : Yes' ) . $space . ( $key ? 'Key : ' . $key : '' ) . $space;
			foreach ( $specific_plugins as $class_name => $plugin_name ) {
				if ( class_exists( $class_name ) || function_exists( $class_name ) ) {
					$plugin_configuration = $plugin_configuration . $space . 'Installed Plugins :' . $plugin_name;
				}
			}
			if ( get_site_option( 'no_of_of_attempt' ) ) {
				$plugin_configuration = $plugin_configuration . $space . '1 Click Reset : ' . get_site_option( 'no_of_of_attempt' );
			}
			if ( time() - get_site_option( 'moppm_pricing_page_visitor' ) < 2592000 && ( get_site_option( 'moppm_plantype' ) || get_site_option( 'moppm_plantype' ) ) ) {
				$plugin_configuration = $plugin_configuration . $space . "Checked plans : '";
				if ( get_site_option( 'moppm_plantype' ) ) {
					$plugin_configuration = $plugin_configuration . get_site_option( 'moppm_plantype' ) . "'";
				}
			}
			$plugin_configuration = $plugin_configuration . $space . 'PHP_version : ' . phpversion();
			if ( ! $send_all_configuration ) {
				return $plugin_configuration;
			}

			return $plugin_configuration;
		}
	}
}
