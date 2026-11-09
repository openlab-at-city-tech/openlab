<?php

/**
 * The Recipients model.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Users\Recipients;
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Users\Recipients;

// Abort if called directly.
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

defined( 'WPINC' ) || die;

/**
 * Class Settings
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Dashboard
 */
class Model extends Base {
	/**
	 * Common key to be used in user meta and options tables.
	 *
	 * @var string
	 */
	public static $review_flag_key = 'wpmudev_blc_reviewed';

	/**
	 * Returns a boolean indicating if user can review on wp org or not. This is based on user role (administrators only) and specific meta that is used as a flag `wpmudev_blc_reviewed` in case already reviewed.
	 *
	 * @param int|null $user_id
	 *
	 * @return mixed
	 */
	/*public static function can_review( int $user_id = null ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		return apply_filters(
			'wpmudev_blc_users_recipient_can_review',
			user_can( $user_id, 'manage_options' ) && empty( get_option( self::$review_flag_key ) ) && empty( get_user_meta( $user_id, self::$review_flag_key, true ) )
		);
	}
	*/

	/**
	 * Returns true if user has reviewd, else false.
	 *
	 * @param int|null $user_id
	 *
	 * @return bool
	 */
	public static function recipient_has_reviewed( int $user_id = null ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		return apply_filters(
			'wpmudev_blc_users_recipient_has_reviewed',
			! empty( get_user_meta( $user_id, self::$review_flag_key, true ) )
		);
	}

	/**
	 * Set the reviewed flag to true if user can review.
	 *
	 * @param int|null $user_id
	 *
	 * @return bool
	 */
	public static function flag_recipient_reviewed(  int $user_id = null  ) {
		return update_user_meta( $user_id, self::$review_flag_key, true ) && update_option( self::$review_flag_key, true, false );
	}

	public static function has_been_reviewed() {
		return ! empty( get_option( self::$review_flag_key ) );
	}

	public static function get_recipient_by_key( string $key = null ) {
		if ( empty( $key ) ) {
			return false;
		}

		/*
		 * Split into two parts only. A recipient key may itself contain an underscore,
		 * and splitting on every one of them would truncate it before it is compared.
		 */
		$key_parts = explode( '_', base64_decode( $key ), 2 );

		if ( count( $key_parts ) < 2 ) {
			return array();
		}

		$hashed_email  = $key_parts[0];
		$recipient_key = sanitize_text_field( $key_parts[1] );
		$schedule      = Settings::instance()->get( 'schedule' );

		if ( '' === $recipient_key ) {
			return array();
		}

		// First go through unregistered recipients (that were added by email).
		if ( ! empty( $schedule['emailrecipients'] ) ) {
			$recipient = array_filter(
				$schedule['emailrecipients'],
				function ( $recipient_data ) use ( $recipient_key, $hashed_email ) {
					if ( ! isset( $recipient_data['key'], $recipient_data['email'] ) ) {
						return false;
					}

					return hash_equals( (string) $recipient_data['key'], $recipient_key ) &&
					       hash_equals( md5( $recipient_data['email'] ), $hashed_email );
				}
			);

			if ( ! empty( $recipient ) ) {
				return array_values( $recipient )[0];
			}
		}

		/*
		 * Registered recipients. The token has to carry the whole stored key, which is
		 * random and specific to the recipient. Matching on md5( email ) alone would
		 * authenticate anyone who knows the address, and an address is not a secret.
		 */
		if ( ! empty( $schedule['registered_recipients_data'] ) ) {
			foreach ( $schedule['registered_recipients_data'] as $user_data ) {
				if ( ! isset( $user_data['key'], $user_data['email'] ) ) {
					continue;
				}

				if ( ! hash_equals( (string) $user_data['key'], $recipient_key ) ) {
					continue;
				}

				if ( ! hash_equals( md5( $user_data['email'] ), $hashed_email ) ) {
					continue;
				}

				$user = get_user_by( 'email', sanitize_email( $user_data['email'] ) );

				if ( $user instanceof \WP_User ) {
					return array(
						'key'     => $user_data['key'],
						'name'    => $user->display_name,
						'email'   => $user->user_email,
						'user_id' => $user->ID,
					);
				}
			}
		}

		return array();
	}
}