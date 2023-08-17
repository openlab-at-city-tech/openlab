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

namespace WPMUDEV_BLC\Core\Models;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_HTTP_Response;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use function is_wp_error;


/**
 * Class Installer
 *
 * @package WPMUDEV_BLC\Core\Models
 */
class Http_Request extends Base {
	/**
	 * Response
	 *
	 * @var null|WP_HTTP_Response
	 */
	private $response = null;

	/**
	 * Sets up the request.
	 *
	 * @param array $args
	 *
	 * @return WP_HTTP_Response
	 */
	public function request( array $args = array() ) {
		$this->init_response();

		$url     = $args['url'] ?? null;
		$headers = $args['headers'] ?? array();
		$body    = $args['body'] ?? array();
		$method  = $args['method'] ?? 'GET';

		$response = $this->process_request( $url, $headers, $body, $method );

		if ( is_wp_error( $response ) ) {
			$this->response->set_status( 500 );
			$this->response->set_data( $response->get_error_message() );
		} else {
			$this->response->set_status( $response['code'] ?? 500 );
			$this->response->set_data( $response['body'] ?? null );
		}

		return $this->get_response();
	}

	/**
	 * Initializes the response property to WP_HTTP_Response object.
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	protected function init_response( array $args = array() ) {
		$this->response = new WP_HTTP_Response();
	}

	/**
	 * @param string $url
	 * @param array $headers
	 * @param array $body
	 * @param string $method
	 *
	 * @return array|WP_Error
	 */
	private function process_request(
		string $url = null,
		array $headers = array(),
		array $body = array(),
		string $method = 'GET'
	) {
		if ( empty( $url ) ) {
			return new WP_Error( 'invalid_request_data', __( 'Missing url.', 'broken-link-checker' ) );
		}

		if ( ! in_array( $method, array( 'GET', 'POST', 'PATCH' ) ) ) {
			$method = 'GET';
		}

		$response = null;
		$args     = apply_filters(
			'wpmudev_blc_http_request_args',
			array(
				'headers' => $headers,
				'body'    => wp_json_encode( is_array( $body ) ? $body : array() ),
			),
			$url,
			$headers,
			$body,
			$method
		);

		// Logging request args. Only if wp debugging is active.
		Utilities::log( "Processing request with following params : \nURL: {$url} \nArgs:" . var_export( $args, true
			) );

		switch ( $method ) {
			case 'POST' :
				$response = wp_remote_post(
					$url,
					$args
				);
				break;

			case 'PATCH':
				$response = wp_remote_request(
					$url,
					$args
				);
				break;

			case 'GET':
				$response = wp_remote_get(
					$url,
					array(
						'headers' => $headers,
					)
				);
				break;
		}

		return array(
			'code' => wp_remote_retrieve_response_code( $response ),
			'body' => wp_remote_retrieve_body( $response ),
		);
	}

	/**
	 * Returns the response param.
	 *
	 * @return WP_HTTP_Response
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Returns the response status.
	 *
	 * @return int
	 */
	public function get_status() {
		return $this->response->get_status();
	}

	/**
	 * Returns the response data.
	 *
	 * @return mixed Response data
	 */
	public function get_data() {
		return $this->response->get_data();
	}

	public function set_status( int $code = 200 ) {
		$this->response->set_status( absint( $code ) );
	}

	public function set_data( $data = null ) {
		$this->response->set_status( $data );
	}
}
