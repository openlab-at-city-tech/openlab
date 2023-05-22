<?php
/**
 * The Http Request model.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Models
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Http_Requests\Scan;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_HTTP_Response;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Scan_Models\Scan_Data;
use WPMUDEV_BLC\Core\Models\Http_Request;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;

use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Http_Requests\Scan
 */
class Controller extends Base {
	/**
	 * Use Dashboard_API Trait.
	 *
	 * @since 2.0.0
	 */
	use Dashboard_API;

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
			'broken_links' => null,
			'succeses'     => null,
			'unique_urls'  => null,
			'total_urls'   => null,
			'start_time'   => null,
			'end_time'     => null,
			'duration'     => null,
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
	 *
	 * @return WP_HTTP_Response|null|WP_Error
	 */
	public function start() {
		$request_status = $this->can_do_request();

		if ( is_wp_error( $request_status ) ) {
			$this->set_response_code( 500 );
			$this->set_response_message( esc_html( $request_status->get_error_message() ) );

			return $this->get_response();
		}

		$this->prepare_request();

		/*
		 * Request to start scan.
		 */
		$args = array(
			'method'  => 'POST',
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

		$json_to_import = json_encode( array( 'params' => $response_data ) );

		if ( ! Scan_Data::instance()->set( $json_to_import ) ) {
			// This doesn't mean that there was an error. There was probably no change in data that's why
			// update_option returns false. So we're just logging a message.
			Utilities::log( $this->error_messages( 'error_on_save' ) );
		}

		$this->set_response_scan_status( 'completed' );
		Settings::instance()->set( array( 'scan_status' => 'completed' ) );
		Settings::instance()->save();

		$this->set_response_code( $api_request->get_status() );
		$this->set_response_data( $api_request->get_data() );
		$this->set_response_message(
			esc_html__(
				'Scan for Broken Links is in progress. Please wait a few minutes until scan completes.',
				'broken-link-checker'
			)
		);

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
		     ! \WPMUDEV_Dashboard::$api->has_key() ) {
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
		$this->api_key = \WPMUDEV_Dashboard::$api->get_key();
	}

	/**
	 * Returns full request url.
	 *
	 * @return string
	 */
	public function get_request_url() {
		if ( is_null( $this->url ) ) {
			$this->url = Utilities::hub_api_scan_url();
		}

		return $this->url;
	}

	protected function error_messages( string $message_code = '' ) {
		$error_messages = array(
			'request_error'    => esc_html__( 'Something went wrong with request.', 'broken-link-checker' ),
			'scan_in_progress' => esc_html__( 'Scan is currently in progress. Please try again in 15 minutes.', 'broken-link-checker' ),
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

	public function set_response_data( $data = '' ) {
		$this->response['data'] = $data;
	}

	public function get_error_message() {
		return $this->response['message'];
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
