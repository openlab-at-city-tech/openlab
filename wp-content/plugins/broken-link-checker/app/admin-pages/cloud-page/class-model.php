<?php
/**
 * The Cloud_Page model
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Page
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Pages\Cloud_Page;

// Abort if called directly.
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Options\Links_Queue\Model as Queue;
use WPMUDEV_BLC\Core\Utils\Utilities;

defined( 'WPINC' ) || die;

/**
 * Class Settings
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Page
 */
class Model {
	/**
	 * Holds an array with all schedule data.
	 *
	 * @since 2.0.0
	 * @var array $settings
	 */
	private static $settings = array();

	/**
	 * Holds an array with all schedule data.
	 *
	 * @since 2.0.0
	 * @var array $schedule
	 */
	private static $schedule = array();

	/**
	 * Returns an item from settings.
	 *
	 * @param string $item Settings key.
	 * @param string $fallback Default value if given key does not exist.
	 *
	 * @return array|string|null
	 */
	public static function get_settings_item( string $item = '', $fallback = null ) {
		return isset( self::get_settings()[ $item ] ) ? self::get_settings()[ $item ] : $fallback;
	}

	/**
	 * Returns DB Settings from options table.
	 *
	 * @return array
	 */
	public static function get_settings() {
		if ( empty( self::$settings ) ) {
			$settings       = new Settings();
			self::$settings = wp_parse_args( $settings::instance()->get(), $settings::instance()->default );
		}

		return self::$settings;
	}

	/**
	 * Checks if we are using Local (Legacy) mode.
	 *
	 * @return bool
	 */
	public static function use_legacy(): bool {
		return boolval( self::get_settings_item( 'use_legacy_blc_version' ) );
	}

	/**
	 * Returns true if scan is currently in progress else it returns false.
	 *
	 * @return bool
	 */
	public static function scan_in_progress() {
		return self::get_settings_item( 'scan_status' ) === 'in_progress';
	}

	/**
	 * Returns scan results stored in DB Settings option.
	 *
	 * @return array
	 */
	public static function get_scan_results() {
		$scan_results          = array();
		$scan_defaults         = isset( Settings::instance()->default['scan_results'] ) ?
			Settings::instance()->default['scan_results'] :
			array();
		$settings_scan_results = wp_parse_args( self::get_settings_item( 'scan_results' ), $scan_defaults );

		array_walk_recursive(
			$settings_scan_results,
			function ( $item, $key ) use ( &$scan_results ) {
				if ( ! \is_numeric( $item ) && ! $item ) {
					$item = '-';
				}

				if ( in_array( $key, array( 'start_time', 'end_time' ), true ) ) {
					if ( in_array( $item, array( '-', 0 ), true ) ) {
						$item = '-';
					} else {
						$item = Utilities::timestamp_to_formatted_date( \intval( $item ), true );
					}
				}

				if ( 'duration' === $key ) {
					$item = Utilities::normalize_seconds_format( ( \floatval( $item ) ) );
				}

				$scan_results[ $key ] = $item;
			}
		);

		if ( ! empty( $settings_scan_results['duration'] ) ) {
			$scan_results['duration_seconds'] = floatval( $settings_scan_results['duration'] );
		}

		return $scan_results;
	}

	/**
	 * Returns array with formatted hour list.
	 *
	 * @return array
	 */
	public static function get_hours_list(): array {
		$hour_list          = array();
		$hour_list_unsorted = Utilities::get_hour_list();

		array_walk_recursive(
			$hour_list_unsorted,
			function ( $item, $key ) use ( &$hour_list ) {
				$hour_list[] = array(
					'key'   => $key,
					'value' => $item,
				);
			}
		);

		return is_array( $hour_list ) ? $hour_list : array();
	}

	/**
	 * Returns array with formatted user data. Data includes id, name, avatar and roles.
	 *
	 * @return array
	 */
	public static function get_current_user_data_formated() {
		return self::format_recipient_user_data( get_current_user_id() );
	}

	/**
	 * Provides formation and includes specific data in user's data list.
	 *
	 * @param int|srting $input Main input which can be user id or email.
	 * @param string     $input_type Describes the input type. Valid options are `id` or `email`.
	 * @param array      $args An optional array than can contain user `key` and `display_name`. Used when no valid user found with given input value and type.
	 *
	 * @return array
	 */
	public static function format_recipient_user_data( $input = null, string $input_type = 'id', array $args = array() ) {
		$key          = null;
		$display_name = null;
		$roles        = array();
		$avatar       = '';
		$user         = null;

		$default = array(
			'id'        => 0,
			'avatar'    => '',
			'name'      => '',
			'roles'     => '',
			'validated' => '',
		);

		if ( ! in_array( $input_type, array( 'id', 'email' ), true ) ) {
			return $default;
		}

		if ( 'email' === $input_type ) {
			if ( ! is_email( $input ) ) {
				return $default;
			}

			$user = get_user_by( 'email', $input );
		} elseif ( 'id' === $input_type ) {
			$key  = intval( $input );
			$user = get_user_by( 'id', $key );

			if ( is_wp_error( $user ) ) {
				return $default;
			}
		}

		if ( $user instanceof \WP_User ) {
			$display_name = $user->display_name;
			$roles        = implode( ', ', Utilities::user_role_names( $user->ID ) );
			$avatar       = get_avatar_url( (int) $user->ID, array( 'size' => 30 ) );
		} else {
			$key          = isset( $args['key'] ) ? $args['key'] : sha1( $input );
			$roles        = '';
			$avatar       = get_avatar_url( $input, array( 'size' => 30 ) );
			$display_name = isset( $args['display_name'] ) ? $args['display_name'] : $input;
		}

		return wp_parse_args(
			array(
				'id'     => $key,
				'avatar' => $avatar,
				'name'   => $display_name,
				'roles'  => $roles,
			),
			$default
		);
	}

	/**
	 * Return registered recipients from db settings option.
	 *
	 * @return array
	 */
	public static function get_recipients() {
		$recipients_user_ids = self::get_schedule_item( 'recipients' );

		if ( empty( $recipients_user_ids ) ) {
			return array();
		}

		$recipients = array_map(
			function ( $recipient_id ) {
				return self::format_recipient_user_data( $recipient_id );
			},
			$recipients_user_ids
		);

		return $recipients;
	}

	/**
	 * Return registered email recipients from db settings option.
	 *
	 * @return array
	 */
	public static function get_email_recipients() {
		$email_recipients = self::get_schedule_item( 'emailrecipients' );

		$recipients = array_map(
			function ( $recipient ) {
				$recipient['avatar'] = get_avatar_url( $recipient['email'], array( 'size' => 30 ) );

				return $recipient;
			},
			$email_recipients
		);

		return $recipients;
	}

	/**
	 * Provides an item from the schedule data from DB settings option.The returned value is not escaped.
	 *
	 * @param string|null $item Schedule key.
	 * @return array|string
	 */
	public static function get_schedule_item( $item = null ) {
		if ( is_null( $item ) ) {
			return array();
		}

		// Important note. The returned value is not escaped here.
		return isset( self::get_schedule()[ $item ] ) ? self::get_schedule()[ $item ] : array();
	}

	/**
	 * Provides the schedule data from DB settings option
	 *
	 * @return array
	 */
	public static function get_schedule() {
		if ( ! empty( self::$schedule ) ) {
			return self::$schedule;
		}

		$schedule_defaults = isset( Settings::instance()->default['scan_results'] ) ?
			Settings::instance()->default['schedule'] :
			array();

		self::$schedule                    = wp_parse_args( self::get_settings_item( 'schedule' ), $schedule_defaults );
		self::$schedule['recipients']      = self::get_recipients();
		self::$schedule['emailRecipients'] = self::get_email_recipients();

		if ( empty( self::$schedule['days'] ) ) {
			self::$schedule['days'] = array( 0 );
		}

		if ( empty( self::$schedule['monthdays'] ) ) {
			self::$schedule['monthdays'] = array( 1 );
		}

		// Match system time format as set from General Settings.
		if ( Utilities::get_time_format() ) {
			self::$schedule['time'] = gmdate( Utilities::get_time_format(), strtotime( self::$schedule['time'] ) );
		}

		return self::$schedule;
	}

	/**
	 * Lists roles.
	 */
	public static function list_user_roles() {
		$recipients     = self::get_schedule_item( 'recipients' );
		$allowed_roles  = array_keys( Utilities::roles_names( array( 'manage_options', 'edit_posts' ) ) );
		$user_count     = count_users();
		$roles_list     = array();
		$rec_roles_list = array();

		array_walk_recursive(
			$recipients,
			function ( $value, $key ) use ( &$rec_roles_list ) {
				if ( 'roles' === $key ) {
					$roles = explode( ',', $value );

					if ( ! empty( $roles ) ) {
						foreach ( $roles as $role ) {
							$role = trim( $role );

							$rec_roles_list[ $role ] = array_key_exists( $role, $rec_roles_list ) ?
							intval( $rec_roles_list[ $role ] ) + 1 :
							1;
						}
					}
				}
			},
			$rec_roles_list
		);

		if ( ! empty( $user_count['avail_roles'] ) ) {
			foreach ( $user_count['avail_roles'] as $role_name => $user_count ) {
				if ( ! in_array( $role_name, $allowed_roles ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					continue;
				}

				if ( isset( $rec_roles_list[ ucfirst( $role_name ) ] ) ) {
					$user_count = $user_count - intval( $rec_roles_list[ ucfirst( $role_name ) ] );
				}

				$roles_list[] = array(
					'role_slug'  => $role_name,
					'role_name'  => ucfirst( $role_name ),
					'user_count' => $user_count >= 0 ? $user_count : 0,
				);
			}
		}

		return $roles_list;
	}

	/**
	 * Calculates remaining cooldown period if required.
	 */
	public static function get_cooldown_data() {
		$scan_results = self::get_settings_item( 'scan_results' );
		$end_time     = ! empty( $scan_results['end_time'] ) ? intval( $scan_results['end_time'] ) : 0;
		$date_utc     = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$diff         = round( ( $date_utc->getTimestamp() - $end_time ) / 60 );
		$remaining    = intval( 15 - $diff );

		return array(
			'cooling_down' => ( 0 < $end_time && 0 < $remaining ),
			'remaining'    => $remaining,
		);
	}

	/**
	 * Gives the status of links process. If there are links to be processed (edit/unlink/nofollow) returns true, else false.
	 *
	 * @return bool
	 */
	public static function links_process_status() {
		// If Queue is empty return false. If Queue is not empty it means there are Links to process so return true.
		return ! Queue::instance()->queue_is_empty();
	}
}
