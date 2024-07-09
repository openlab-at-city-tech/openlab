<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * A thin client to make requests to MT API using WP HTTP API.
 */
class TRP_MTAPI_Customer {


	private $url;

	public function __construct( $url ) {

		$this->url       = trailingslashit( $url );
	}

	public function lookup_site( $key, $url ) {

		return $this->request( 'POST', 'sites/lookup', [ 'key' => $key, 'url' => trailingslashit($url) ] );
	}

	public function lookup_license( $key ) {

		return $this->request( 'POST', 'licenses/lookup', [ 'key' => $key ] );
	}

	private function request( $method, $path, $data = null ) {

		$request_args = [
			'method'  => $method,
			'headers' => [
				'Content-Type' => 'application/json'
			],
		];

		if ( ! is_null( $data ) ) {
			$request_args['body'] = wp_json_encode( $data );
		}

		$response = wp_remote_request( $this->url . $path, $request_args );

		if ( is_wp_error( $response ) ) {
			$error_response = [ 'exception' => [] ];
			foreach ( $response->get_error_messages() as $message ) {
				// Emulates structure of MT API exception response for simplicity/consistency.
				$error_response['exception'][]['message'] = $message;
			}

			return $error_response;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
