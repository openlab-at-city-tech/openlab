<?php
/**
 * The Http Request for syncing plugin with BLC Api.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Models
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Http_Requests\Sync_Scan_Results;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_HTTP_Response;
use WPMUDEV_BLC\App\Emails\Scan_Report\Controller as ReportMailer;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Models\Http_Request;
use WPMUDEV_BLC\Core\Traits\Sanitize;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;
use WPMUDEV_BLC\App\Scan_Models\Scan_Data;
use WPMUDEV_Dashboard;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Http_Requests\Sync_Scan_Results
 */
class Controller extends Base {
	/**
	 * Use Dashboard api trait.
	 */
	use Dashboard_API;

	/**
	 * Use Sanitize Trait.
	 *
	 * @since 2.0.0
	 */
	use Sanitize;

	/**
	 * Response
	 *
	 * @var null|array
	 */
	private $response = array(
		'code'        => 404,
		'status'      => null,
		'scan_status' => '',
		'message'     => '',
		'data'        => array(
			'broken_links'  => null,
			'success_urls'  => null,
			'unique_urls'   => null,
			'start_time'    => null,
			'end_time'      => null,
			'scan_duration' => null,
		),
	);

	/**
	 * The BLC remote api full url.
	 *
	 * @var string
	 */
	private $url = null;

	/**
	 * The BLC api key.
	 *
	 * @var string
	 */
	private $api_key = null;

	/**
	 * Token
	 *
	 * @var string
	 */
	private $token = null;
	/**
	 *
	 * Null or Object of eiter Http_Request or WP_Error.
	 *
	 * @var object
	 */
	private $request_api = null;

	/**
	 * Starts the BLC scan. Request:
	 * curl --location --request POST 'https://new-dev-hub-blc.staging.wpmudev.com/api/blc/v1/scan?domain=https://site.com' --header 'Authorization: {DASH_API_KEY}'
	 *
	 * @return WP_HTTP_Response|null|WP_Error
	 */
	public function start() {
		Utilities::log( 'Sync request started.' );
		Settings::instance()->init();

		$request_status = $this->can_do_request();
		// $previous_status = Settings::instance()->get( 'scan_status' );

		if ( is_wp_error( $request_status ) ) {
			$this->set_response_code( 500 );
			$this->set_response_message( esc_html( $request_status->get_error_message() ) );

			return $this->get_response();
		}

		$this->prepare_request();

		/*
		 * Request to get scan results.
		 */
		$args = array(
			'method'  => 'GET',
			'url'     => $this->get_request_url(),
			'headers' => array(
				'Authorization' => $this->api_key,
			),
		);

		$api_request   = $this->request_api->request( $args );
		$error_message = $this->error_messages( 'request_error' );

		if ( ! $this->request_api->get_response() instanceof WP_HTTP_Response ) {
			$this->set_response_code( 500 );
			$this->set_response_message( $error_message );

			return $this->get_response();
		}

		$response_data = json_decode( $this->request_api->get_data(), true );

		if ( 200 !== $api_request->get_status() ) {
			$error_message = isset( $response_data['message'] ) ? esc_html( $response_data['message'] ) : $error_message;

			$this->set_response_code( 500 );
			$this->set_response_message( $error_message );

			return $this->get_response();
		}

		// Send scan in progress response.
		if ( ! empty( $response_data['scanning']['is_running'] ) ) {
			$this->set_response_scan_status( 'in_progress' );
			Settings::instance()->set( array( 'scan_status' => 'in_progress' ) );
			Settings::instance()->save();
			$this->set_response_code( 500 );
			$this->set_response_message( $this->error_messages( 'scan_in_progress' ) );

			return $this->get_response();
		}

		if ( empty( $response_data['last_result'] ) ) {
			$this->set_response_code( 500 );
			$this->set_response_message( $this->error_messages( 'missing_results' ) );
			Utilities::log( $this->error_messages( 'missing_results' ) );

			return $this->get_response();
		}

		$scan_result = $response_data['last_result'];

		// Initial data values.
		$this->set_response_data(
			array(
				'broken_links'       => 0,
				'success_urls'       => 0,
				'total_urls'         => 0,
				'unique_urls'        => 0,
				'start_time'         => '-',
				'end_time'           => '-',
				'cooldown_remaining' => 0,
				'scan_duration'      => 0,
			)
		);

		// No scan data. Probably no scan ever ran for this site.
		if ( ! isset( $scan_result['start_unix_time_utc'] ) || 0 === $scan_result['start_unix_time_utc'] ) {
			$this->set_response_code( 449 );
			$this->set_response_message( $this->error_messages( 'scan_has_no_start_time' ) );
			Utilities::log( $this->error_messages( 'scan_has_no_start_time' ) );
			/*
			$this->set_response_message(
				esc_html__(
					'There seem to be no scan records. Please try running a new scan.',
					'broken-link-checker'
				)
			);
			*/
			$this->set_response_message( '' );

			return $this->get_response();
		}

		// Scan_Data::instance()->set() expects a json string with a `params` index/key.
		$json_to_import = json_encode( array( 'params' => $response_data ) );

		if ( ! Scan_Data::instance()->set( $json_to_import ) ) {
			// This doesn't mean that there was an error. There was probably no change in data that's why
			// update_option returns false. So we're just logging a message.
			Utilities::log( $this->error_messages( 'error_on_save' ) );
		}

		if ( Settings::instance()->get( 'blc_schedule_scan_in_progress' ) ) {
			// TODO Ensure that notification is sent after schedule scan completed even if HUB response fails.
			// Currently we need to remove this notification due to possible duplicates: BLC-392.
			// ReportMailer::instance()->init();
			// ReportMailer::instance()->send_email();
			Settings::instance()->set( array( 'blc_schedule_scan_in_progress' => false ) );
		}

		Settings::instance()->set( array( 'scan_status' => 'completed' ) );
		Settings::instance()->save();

		$this->set_response_scan_status( 'completed' );
		$this->set_response_code( $api_request->get_status() );

		$scan_results = Settings::instance()->get( 'scan_results' );
		$end_time     = ! empty( $scan_results['end_time'] ) ? intval( $scan_results['end_time'] ) : 0;
		$date_utc     = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$diff         = round( ( $date_utc->getTimestamp() - $end_time ) / 60 );
		$remaining    = intval( 15 - $diff );

		$this->set_response_data(
			array(
				'broken_links'       => isset( $scan_result['num_broken_links'] ) ? intval( $scan_result['num_broken_links'] ) : null,
				'success_urls'       => isset( $scan_result['num_successful_links'] ) ? intval( $scan_result['num_successful_links'] ) : null,
				'total_urls'         => isset( $scan_result['num_found_links'] ) ? intval( $scan_result['num_found_links'] ) : null,
				'unique_urls'        => isset( $scan_result['num_site_unique_links'] ) ? intval( $scan_result['num_site_unique_links'] ) : null,
				'start_time'         => ! empty( $scan_result['start_unix_time_utc'] ) ?
					Utilities::timestamp_to_formatted_date( intval( $scan_result['start_unix_time_utc'] ), true ) : '-',
				'end_time'           => ! empty( $scan_result['ended_unix_time_utc'] ) ?
					Utilities::timestamp_to_formatted_date( intval( $scan_result['ended_unix_time_utc'] ), true ) : '-',
				'cooldown_remaining' => $remaining,
				'scan_duration'      => isset( $scan_result['scan_duration'] ) ? Utilities::normalize_seconds_format( floatval( $scan_result['scan_duration'] ) ) : '',
				'scan_duration_sec'  => isset( $scan_result['scan_duration'] ) ? floatval( $scan_result['scan_duration'] ) : '',
			)
		);

		if ( 0 < $remaining && 15 >= $remaining && 'completed' === Settings::instance()->get( 'scan_status' ) ) {
			$this->set_response_message(
				esc_html__(
					'Scan completed successfully.',
					'broken-link-checker'
				)
			);
		} else {
			$this->set_response_message(
				esc_html__(
					'Data successfully updated.',
					'broken-link-checker'
				)
			);
		}

		return $this->get_response();
	}

	/**
	 * Checks if the request can be carried out.
	 *
	 * @return bool|WP_Error
	 */
	private function can_do_request() {
		if ( Utilities::is_localhost() ) {
			return new WP_Error(
				'blc-api-request-failled',
				esc_html__(
					'Scan could not be started because it seems you are on localhost. Broken Links Checker API can not reach sites on local hosts',
					'broken-link-checker'
				)
			);
		}

		if ( ! (bool) self::site_connected() ||
			Settings::instance()->get( 'use_legacy_blc_version' ) ||
			! class_exists( '\WPMUDEV_Dashboard' ) ||
			! WPMUDEV_Dashboard::$api->has_key() ) {
			return new WP_Error(
				'blc-api-request-failled',
				esc_html__(
					esc_html__( 'Can not make request.', 'broken-link-checker' ),
					'broken-link-checker'
				)
			);
		}

		return true;
	}

	public function set_response_code( int $code = 200 ) {
		$this->response['status'] = absint( $code );
	}

	public function set_response_message( string $data = '' ) {
		$this->response['message'] = $data;
	}

	/**
	 * Returns the response.
	 *
	 * @return WP_Http_Response|array
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Prepares some params.
	 *
	 * @return void
	 */
	private function prepare_request() {
		if ( is_null( $this->request_api ) ) {
			$this->request_api = Http_Request::instance();
		}

		// WPMUDEV_Dashboard class has been checked already in `$this->can_do_request()`.
		$this->api_key = WPMUDEV_Dashboard::$api->get_key();
	}

	/**
	 * Returns full request url.
	 *
	 * @return string
	 */
	public function get_request_url() {
		if ( is_null( $this->url ) ) {
			$this->url = Utilities::hub_api_sync_url();
		}

		return $this->url;
	}

	protected function error_messages( string $message_code = '' ) {
		$error_messages = array(
			'request_error'          => esc_html__( 'Something went wrong with request.', 'broken-link-checker' ),
			'scan_in_progress'       => esc_html__( 'Scan is currently in progress. Please try again in a few minutes.', 'broken-link-checker' ),
			'error_on_save'          => esc_html__( 'Something went wrong while storing new data to DB. More likely there is nothing to update', 'broken-link-checker' ),
			'scan_has_no_start_time' => esc_html__( 'The start time is missing. Aborting importing data.', 'broken-link-checker' ),
			'missing_results'        => esc_html__( 'Results are missing. Aborting importing data.', 'broken-link-checker' ),

		);

		if ( ! empty( $message_code ) ) {
			return isset( $error_messages[ $message_code ] ) ? $error_messages[ $message_code ] : '';
		}

		return $error_messages;
	}

	/**
	 * Sets the response status in global array.
	 *
	 * @param string $status
	 *
	 * @return void
	 */
	public function set_response_scan_status( string $status = 'completed' ) {
		if ( ! in_array( $status, array( 'completed', 'in_progress' ) ) ) {
			$status = 'completed';
		}
		$this->response['scan_status'] = $status;
	}

	public function get_error_message() {
		return $this->response['message'];
	}

	public function set_response_data( $data = '' ) {
		$this->response['data'] = $data;
	}

	public function get_response_code() {
		return $this->response['status'];
	}

	public function get_response_message() {
		return $this->response['message'];
	}

	public function get_response_data() {
		return $this->response['data'];
	}

	public function get_response_scan_status() {
		return $this->response['scan_status'];
	}
}
