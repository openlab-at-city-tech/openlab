<?php
/**
 * An endpoint where Hub can send requests and retrieve scan schedule data.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Hub_Endpoints\Scan_Data
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Hub_Endpoints\Scan_Data;

// Abort if called directly.
defined( 'WPINC' ) || die;

use DateTime;
use DateTimeZone;
use WPMUDEV_BLC\Core\Controllers\Hub_Endpoint;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use function boolval;
use function gmdate;
use function intval;
use function wp_next_scheduled;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Hub_Endpoint\Scan_Data
 */
class Controller extends Hub_Endpoint {
	/**
	 * Returns a json string with schedule data.
	 */
	public function get_schedule_data() {
		$schedule_data = array(
			'active'          => false,
			'recipients'      => array(),
			'emailRecipients' => array(),
			'frequency'       => 'daily',
			'days'            => array(),
			'time'            => '00:00',
			'nextScanData'    => array(),
		);
		$schedule      = $this->get_schedule();

		if ( ! empty( $schedule ) ) {
			$schedule_data['active'] = isset( $schedule['active'] ) ? boolval( $schedule['active'] ) : false;

			if ( ! $schedule_data['active'] ) {
				$this->output_formatted_response( $schedule_data );
			}

			$schedule_data['recipients']      = $schedule['recipients'] ?? $schedule_data['recipients'];
			$schedule_data['emailRecipients'] = Settings::instance()->get_scan_active_email_recipients() ?? $schedule_data['emailRecipients'];
			$schedule_data['frequency']       = $schedule['frequency'] ?? $schedule_data['frequency'];
			$schedule_data['days']            = ( 'monthly' === $schedule['frequency'] ) ?
				$schedule['monthdays'] :
				( 'weekly' === $schedule['monthdays'] ? $schedule['days'] : array() );
			$schedule_data['time']            = $schedule['time'] ?? $schedule_data['time'];
			$schedule_data['nextScanData']    = $this->get_next_scan_data();
		}

		$this->output_formatted_response( $schedule_data );
	}

	/**
	 * Returns the schedule from settings, or if a key is set, it returns that key's value
	 *
	 * @param string $key .
	 *
	 * @return array|mixed|null
	 */
	private function get_schedule( string $key = '' ) {
		static $schedule = null;

		if ( is_null( $schedule ) ) {
			$schedule = Settings::instance()->get( 'schedule' );
		}

		if ( ! empty( $key ) && is_array( $schedule ) ) {
			return $schedule[ $key ] ?? null;
		}

		return $schedule;
	}

	/**
	 * Returns an array which contains next blc schedule data.
	 *
	 * @return array.
	 */
	private function get_next_scan_data() {
		$next_scan_data = array(
			'siteZone'              => '',
			'timestampSiteZone'     => '',
			'timestampUTC'          => '',
			'formattedDateSiteZone' => '',
			'formattedDateUTC'      => '',
		);
		$schedule_cron  = new \WPMUDEV_BLC\App\Scheduled_Events\Scan\Controller();

		$schedule_cron->init();

		//$next_scan_timestamp_utc = wp_next_scheduled( $schedule_cron->cron_hook );
		$next_scan_timestamp_utc = $this->get_next_schedule_timestamp( $schedule_cron->get_hook_name() );
		$date_format             = Utilities::get_date_format();
		$time_format             = Utilities::get_time_format();

		if ( $next_scan_timestamp_utc ) {
			$next_scan_timestamp_utc = intval( $next_scan_timestamp_utc );
			$next_scan_date          = gmdate( 'Y-m-d H:i:s', $next_scan_timestamp_utc );
			$next_scan_datetime      = new DateTime( $next_scan_date, new DateTimeZone( 'UTC' ) );

			$next_scan_datetime->setTimezone( new DateTimeZone( Utilities::get_timezone_string( true ) ) );

			$next_scan_timestamp                     = strtotime( $next_scan_datetime->format( 'Y-m-d H:i:s' ) );
			$next_scan_data['siteZone']              = Utilities::get_timezone_string( true );
			$next_scan_data['timestampSiteZone']     = $next_scan_timestamp;
			$next_scan_data['timestampUTC']          = $next_scan_timestamp_utc;
			$next_scan_data['formattedDateSiteZone'] = gmdate( "{$date_format} {$time_format}", $next_scan_timestamp );
			$next_scan_data['formattedDateUTC']      = gmdate( "{$date_format} {$time_format}", $next_scan_timestamp_utc );
		}

		return $next_scan_data;
	}

	/**
	 * Calculates next `blc_schedule_scan` cron timestamp.
	 *
	 * @return int|bool.
	 */
	private function get_next_schedule_timestamp( $cron_hook ) {
		$next_timestamp = wp_next_scheduled( $cron_hook );

		if ( ! $next_timestamp ) {
			$schedule_cron = new \WPMUDEV_BLC\App\Scheduled_Events\Scan\Controller();
			$schedule_cron->set_scan_schedule();

			$next_timestamp = wp_next_scheduled( $cron_hook );
		}

		return $next_timestamp;
	}

	/**
	 * Sets the endpoint's action vars to be used by Dash plugin.
	 */
	protected function setup_action_vars() {
		$this->endpoint_action_name     = 'blc_get_data';
		$this->endpoint_action_callback = 'get_schedule_data';
	}
}
