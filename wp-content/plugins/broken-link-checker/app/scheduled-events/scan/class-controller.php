<?php
/**
 * Scheduled event for BLC Scan.
 * A single scheduled event that gets triggered based on options set in "Schedule Scan"
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Schedule_Events\Scan
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Scheduled_Events\Scan;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Exception;
use WPMUDEV_BLC\App\Http_Requests\Scan\Controller as Scan_API;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Traits\Cron;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Schedule_Events\Scan
 */
class Controller extends Base {
	use Cron;

	/**
	 * WP Cron hook to execute when event is run.
	 *
	 * @var string
	 */
	public $cron_hook_name = 'blc_schedule_scan';

	/**
	 * BLC settings from options table.
	 *
	 * @var array
	 */
	private $settings = null;

	/**
	 * Init Schedule
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		//if ( wp_doing_ajax() || ! $this->get_schedule( 'active' ) ) {
		if ( wp_doing_ajax() ) {
			return;
		}

		Settings::instance()->init();

		//add_action( 'init', array( $this, 'load' ) );
		//add_action( 'wpmudev_blc_rest_enpoints_after_save_schedule_settings', array( $this, 'deactivate_cron' ), 10 );
		add_action( 'wpmudev_blc_rest_enpoints_after_save_schedule_settings', array( $this, 'set_scan_schedule' ) );

		add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'deactivate_cron' ) );
	}

	/**
	 * Starts the scheduled scan.
	 */
	public function process_scheduled_event() {
		if ( ! $this->get_schedule( 'active' ) || ! apply_filters( 'wpmudev_blc_can_run_scan_schedule', $this->can_run_schedule() ) ) {
			// Reset Schedule to make sure that it runs in time.
			$this->set_scan_schedule();
			return false;
		}

		// At his point we're setting the scan status flag to `in_progress`. So if it doesn't get `completed` there
		// will ba a sync request fired on page load.
		Settings::instance()->set( array( 'scan_status' => 'in_progress' ) );
		Settings::instance()->save();

		$scan = Scan_API::instance();

		// Setting scan schedule so that it doesn't run while scan still is running.
		$this->set_scan_schedule();
		$scan->start();
		$this->set_scan_schedule_flag();
		//$this->deactivate_cron();
		//$this->set_scan_schedule();
	}

	/**
	 * Returns the schedule from settings, or if a key is set, it returns that key's value
	 *
	 * @param string $key .
	 *
	 * @return array|mixed|null
	 */
	private function get_schedule( string $key = '' ) {
		if ( is_null( $this->settings ) ) {
			$this->settings = Settings::instance()->get( 'schedule' );
		}

		if ( ! empty( $key ) && is_array( $this->settings ) ) {
			return $this->settings[ $key ] ?? null;
		}

		return $this->settings;
	}

	/**
	 * Makes sure that the cron does not get triggered long before it's time.
	 *
	 * @throws Exception
	 * @return boolean
	 */
	protected function can_run_schedule() {
		// Better not run when membership is expired.
		if ( boolval( Utilities::membership_expired() ) ) {
			return false;
		}

		// For some reason this is called multiple times
		$cur_date           = new \DateTimeImmutable();
		$scan_results       = Settings::instance()->get( 'scan_results' );
		$last_timestamp     = $scan_results['end_time'] ?? null;
		$last_scan_date     = new \DateTimeImmutable( date( 'Y-m-d H:i:s', $last_timestamp ) );
		$interval_from_last = $cur_date->diff( $last_scan_date );

		if ( 'in_progress' === Settings::instance()->get( 'scan_status' ) || ( ! empty( $last_timestamp ) && abs( $interval_from_last->format( '%h' ) ) <= 5 ) ) {
			// Since we return `false` at this point, `$this->set_scan_schedule()` will be called from `process_scheduled_event()`. No need to run it here too.
			//$this->set_scan_schedule();
			return false;
		}

		$next_timestamp = wp_next_scheduled( $this->get_hook_name() );

		if ( empty( $next_timestamp ) ) {
			$next_timestamp = $this->get_timestamp();
		}

		$next_scan_date = new \DateTimeImmutable( date( 'Y-m-d H:i:s', $next_timestamp ) );
		$interval       = $next_scan_date->diff( $cur_date );
		// If for some reason the schedule gets triggered long before its time, let's make sure it doesn't run the callback.
		// We do allow some time span, by default 5 hours.
		return 5 >= abs( $interval->format( '%h' ) );
	}

	/**
	 * Returns the scheduled event's hook name.
	 * Overriding Trait's method.
	 */
	public function get_hook_name() {
		return $this->cron_hook_name;
	}

	/**
	 * Sets the scan flag to true. Useful when API sends the SET request, an email about the current schedule should
	 * be sent to schedule receivers.
	 */
	public function set_scan_schedule_flag( bool $flag = true ) {
		Settings::instance()->set( array( 'blc_schedule_scan_in_progress' => $flag ) );
		Settings::instance()->set( array( 'scan_status' => 'in_progress' ) );
		Settings::instance()->save();
	}

	/**
	 * Sets new scan schedule.
	 *
	 * @param array $settings The settings param from `wpmudev_blc_rest_enpoints_after_save_schedule_settings` action.
	 *
	 * @return bool
	 */
	public function set_scan_schedule( array $settings = array() ) {
		if ( ! $this->get_schedule( 'active' ) ) {
			return false;
		}

		$settings = empty( $settings ) ?? Settings::instance();

		// Deactivate cron if is already created, so we will replace it later on.
		$this->deactivate_cron();

		// As a single event it will be possible to set custom timestamps to run.
		$this->is_single_event = true;
		// Set the timestamp based on Schedule options.
		$this->timestamp = intval( $this->get_timestamp( $settings['schedule'] ?? array() ) );

		//$this->setup_cron();
		return $this->activate_cron();
	}

	/**
	 * Returns the timestamp of next scheduled scan.
	 *
	 * @param array $schedule
	 *
	 * @return false|int|null
	 */
	public function get_timestamp( array $schedule = array() ) {
		$schedule  = ! empty( $schedule['frequency'] ) ? $schedule : $this->get_schedule();
		$timestamp = null;

		if ( empty( $schedule['frequency'] ) || empty( $schedule['time'] ) ) {
			return $timestamp;
		}

		$schedule_time = $schedule['time'];
		// phpcs:ignore
		$current_day_num = 'monthly' === $schedule['frequency'] ? date( 'd' ) : date( 'w', time() );
		$schedule_days   = 'monthly' === $schedule['frequency'] ? $schedule['monthdays'] : $schedule['days'];

		if ( 'daily' !== $schedule['frequency'] && empty( $schedule_days ) ) {
			return $timestamp;
		}

		sort( $schedule_days );

		switch ( $schedule['frequency'] ) {
			case 'daily':
				if ( date_format( date_create( date_i18n( 'H:i' ) ), 'Hi' ) > date_format( date_create( $schedule_time ), 'Hi' ) ) {
					$timestamp = strtotime( "tomorrow {$schedule_time} " . wp_timezone_string() );
				} else {
					$timestamp = strtotime( "today {$schedule_time} " . wp_timezone_string() );
				}
				break;

			case 'weekly':
			case 'monthly':
				$next_day_num        = null;
				$move_to_next_period = false;

				if ( in_array( $current_day_num, $schedule_days, true ) ) {
					$day_key = array_keys( $schedule_days, $current_day_num, true )[0];

					if ( date_format( date_create( date_i18n( 'H:i' ) ), 'Hi' ) >= date_format( date_create( $schedule_time ), 'Hi' ) ) {
						$next_day_num = array_key_exists( ( $day_key + 1 ), $schedule_days ) ? $schedule_days[ ( $day_key + 1 ) ] : null;
					} else {
						$timestamp = strtotime( "today {$schedule_time}" . ' ' . wp_timezone_string() );
					}
				}

				if ( is_null( $next_day_num ) ) {
					foreach ( $schedule_days as $day_num ) {
						if ( intval( $day_num ) > intval( $current_day_num ) ) {
							$next_day_num = intval( $day_num );
							break;
						}
					}
				}

				if ( is_null( $next_day_num ) ) {
					$next_day_num        = intval( $schedule_days[0] );
					$move_to_next_period = true;
				}

				if ( is_null( $timestamp ) ) {
					if ( 'weekly' === $schedule['frequency'] ) {
						// phpcs:ignore
						$day_name  = date( 'l', strtotime( "Sunday +{$next_day_num} days" ) );
						$timestamp = strtotime( "next {$day_name} {$schedule_time}" . ' ' . wp_timezone_string() );
					} else {
						if ( $move_to_next_period ) {
							// As we're adding $next_day_num as additional days in next month, we need to deduct it by one.
							-- $next_day_num;
							$timestamp = strtotime( "+{$next_day_num} days {$schedule_time}" . ' ' . wp_timezone_string(), strtotime( 'first day of next month' ) );
						} else {
							$days_diff = $next_day_num - $current_day_num;
							$timestamp = strtotime( "+{$days_diff} days {$schedule_time}" . ' ' . wp_timezone_string() );
						}
					}
				}

				break;

			default:
				$timestamp = null;
		}

		return $timestamp;
	}
}
