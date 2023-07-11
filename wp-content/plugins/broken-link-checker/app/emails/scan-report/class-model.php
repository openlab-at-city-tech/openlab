<?php
/**
 * The Emails model for Scan report.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Emails\Scan_Report;
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Emails\Scan_Report;

// Abort if called directly.
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Webhooks\Recipient_Activation\Controller as Activation_Webhook;
use WPMUDEV_BLC\App\Webhooks\User_Review\Controller as Review_Webhook;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Users\Recipients\Model as Recipients;
use WPMUDEV_BLC\Core\Utils\Utilities;

defined( 'WPINC' ) || die;

/**
 * Class Settings
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Dashboard
 */
class Model extends Base {
	/**
	 * The scan results from DB.
	 *
	 * @var array
	 */
	private static $scan_results = array();

	private static $registered_users_recipients = null;

	/**
	 * Returns the header logo of the email.
	 *
	 * @return string
	 */
	public static function header_logo() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_header_logo',
			esc_url( WPMUDEV_BLC_ASSETS_URL . 'images/blc-logo-white-28x28.png' )
		);
	}

	/**
	 * Returns the BLC Title to be used in the email header.
	 *
	 * @return string
	 */
	public static function header_title() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_header_title',
			__( 'BLC Report', 'brocken-link-checker' )
		);
	}

	/**
	 * Returns home url.
	 *
	 * @retun string
	 */
	public static function get_hub_home_url() {
		return Utilities::hub_home_url();
	}

	/**
	 * Returns the header logo of the email.
	 *
	 * @return string
	 */
	public static function footer_logo() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_header_logo',
			esc_url( WPMUDEV_BLC_ASSETS_URL . 'images/wpmudev-logo-dark-30x30.png' )
		);
	}

	/**
	 * Returns social links info.
	 *
	 * @return array
	 */
	public static function social_links() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_social_data',
			array(
				'facebook'  => array(
					'icon' => WPMUDEV_BLC_ASSETS_URL . 'images/social/facebook-dark-7x14.png',
					'url'  => 'https://www.facebook.com/wpmudev',
				),
				'instagram' => array(
					'icon' => WPMUDEV_BLC_ASSETS_URL . 'images/social/instagram-dark-14x14.png',
					'url'  => 'https://www.instagram.com/wpmu_dev/',
				),
				'twitter'   => array(
					'icon' => WPMUDEV_BLC_ASSETS_URL . 'images/social/twitter-dark-13x11.png',
					'url'  => 'https://twitter.com/wpmudev/',
				),
			)
		);
	}

	/**
	 * Returns scan date.
	 */
	public static function scan_date() {
		$start_time = self::get_scan_results( 'start_time' );

		if ( ! empty( $start_time ) ) {
			$start_time = Utilities::microtime_to_date( intval( $start_time ), 'full_date', true );
		}

		return apply_filters(
			'wpmudev_blc_scan_report_email_scan_date',
			$start_time
		);
	}

	/**
	 * Returns scan results.
	 *
	 * @param string $key Optional scan results key.
	 */
	public static function get_scan_results( string $key = '' ) {
		if ( empty( self::$scan_results ) ) {
			$scan_results       = array();
			$scan_defaults      = Settings::instance()->default['scan_results'] ?? array();
			self::$scan_results = wp_parse_args( Settings::instance()->get( 'scan_results' ), $scan_defaults );
		}

		if ( ! empty( $key ) ) {
			return self::$scan_results[ $key ] ?? null;
		}

		return self::$scan_results;
	}

	/**
	 * Returns array recipients email address and names.
	 */
	public static function get_recipients() {
		return array_merge( self::get_registered_user_recipients(), self::get_email_recipients() );
	}

	public static function get_registered_user_recipients() {
		static $reviewers_ids = array();

		if ( is_null( self::$registered_users_recipients ) ) {
			self::$registered_users_recipients = array();
			$schedule                          = Settings::instance()->get( 'schedule' );
			$registered_recipients_data        = ! empty( $schedule['registered_recipients_data'] ) ? $schedule['registered_recipients_data'] : array();
			$has_been_reviewed                 = Recipients::has_been_reviewed();

			if ( ! empty( $registered_recipients_data ) ) {

				// Let's check which users will be allowed to review plugin.
				if ( empty( $reviewers_ids ) && ! $has_been_reviewed ) {
					$user_ids  = array_keys( $registered_recipients_data );
					$auth_user = Utilities::get_auth_user( true );

					if ( $auth_user instanceof \WP_User && in_array( $auth_user->ID, $user_ids ) ) {
						$reviewers_ids[] = $auth_user->ID;
					} else {
						$reviewers_ids = array_intersect( Utilities::get_dash_users(), $user_ids );
					}
				}

				array_walk(
					$registered_recipients_data,
					function ( $user_data, $user_id ) use ( $reviewers_ids, $has_been_reviewed ) {
						$review_link = '';
						$token       = base64_encode( md5( $user_data['email'] ) . '_' . $user_data['key'] );

						if ( ! $has_been_reviewed ) {

							// If still there is no reviewer, we can include any of the recipients that have an administrator role.
							if ( ( ! empty( $reviewers_ids ) && in_array( $user_id, $reviewers_ids ) ) || ( empty( $reviewers_ids ) && user_can( $user_id, 'manage_options' ) ) ) {
								$review_link = self::review_link( $user_id, $token );
							}
						}

						self::$registered_users_recipients[] = array(
							'name'             => $user_data['name'],
							'email'            => $user_data['email'],
							'key'              => $user_data['key'],
							'unsubscribe_link' => self::unsubscribe_link( $token ),
							'review_link'      => $review_link,
						);
					}
				);
			}

		}

		return self::$registered_users_recipients;
	}

	/**
	 * Returns a link where user can review. The link does not point directly to wp org.
	 * Instead, it first links to an endpoint so that we can flag that user has reviewed and then redirect to wp org.
	 * We need this flag so that we don't annoy user with every email to review.
	 *
	 * @param int|null $user_id
	 * @param string|null $token
	 *
	 * @return void|string
	 */
	private static function review_link( int $user_id = null, string $token = null ) {
		return add_query_arg(
			array(
				'token' => sanitize_text_field( $token ),
			),
			Review_Webhook::instance()->webhook_url()
		);
	}

	private static function unsubscribe_link( string $token = '' ) {
		return add_query_arg(
			array(
				'action'          => 'cancel',
				'activation_code' => sanitize_text_field( $token ),
			),
			Activation_Webhook::instance()->webhook_url()
		);
	}

	public static function get_email_recipients() {
		$email_recipients = Settings::instance()->get_scan_active_email_recipients();

		if ( ! empty( $email_recipients ) ) {
			array_walk(
				$email_recipients,
				function ( &$recipient ) {
					unset( $recipient['confirmed'] );
					$recipient['unsubscribe_link'] = self::unsubscribe_link( base64_encode( md5( $recipient['email'] ) . '_' . $recipient['key'] ) );
				}
			);
		}

		return $email_recipients;
	}
}
