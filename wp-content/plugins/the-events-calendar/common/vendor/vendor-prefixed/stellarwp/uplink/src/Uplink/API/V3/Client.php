<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3;

use TEC\Common\StellarWP\Uplink\API\V3\Contracts\Client_V3;
use WP_Error;
use WP_Http;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * The Version 3 client for the licensing server.
 *
 * @see \TEC\Common\StellarWP\Uplink\API\V3\Provider::register()
 */
final class Client implements Client_V3 {

	/**
	 * API base endpoint.
	 *
	 * @var string
	 */
	private $api_root;

	/**
	 * Base URL for the license key server.
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * The default request arguments to send with WP_Http
	 *
	 * @var array<string, mixed>
	 */
	private $request_args;

	/**
	 * @var WP_Http
	 */
	private $wp_http;

	/**
	 * @param  string  $api_root
	 * @param  string  $base_url
	 * @param  array<string, mixed>  $request_args
	 * @param  WP_Http  $wp_http
	 */
	public function __construct( string $api_root, string $base_url, array $request_args, WP_Http $wp_http ) {
		$this->api_root     = $api_root;
		$this->base_url     = $base_url;
		$this->request_args = $request_args;
		$this->wp_http      = $wp_http;
	}

	/**
	 * Perform a GET request.
	 *
	 * @param  string  $endpoint
	 * @param  array<string, mixed>  $params
	 *
	 * @return WP_Error|array{
	 *       'body' : array<string, mixed>,
	 *       'headers' : CaseInsensitiveDictionary,
	 *       'response' : array{
	 *           'code' : int,
	 *           'message' : string,
	 *       },
	 *       'cookies' : array<int, \WP_Http_Cookie>,
	 *       'filename' : string|null,
	 *       'http_response' : \WP_HTTP_Requests_Response
	 *   }
	 */
	public function get( string $endpoint, array $params = [] ) {
		$args = array_merge( $this->request_args, [
			'body' => $params,
		] );

		return $this->request( $endpoint, 'GET', $args );
	}

	/**
	 * Perform a POST request.
	 *
	 * @param  string  $endpoint
	 * @param  array<string, mixed>  $params
	 *
	 * @return WP_Error|array{
	 *       'body' : array<string, mixed>,
	 *       'headers' : CaseInsensitiveDictionary,
	 *       'response' : array{
	 *           'code' : int,
	 *           'message' : string,
	 *       },
	 *       'cookies' : array<int, \WP_Http_Cookie>,
	 *       'filename' : string|null,
	 *       'http_response' : \WP_HTTP_Requests_Response
	 *   }
	 */
	public function post( string $endpoint, array $params = [] ) {
		$args = array_merge( $this->request_args, [
			'body' => $params,
		] );

		return $this->request( $endpoint, 'POST', $args );
	}

	/**
	 * Perform any other request.
	 *
	 * @param  string  $endpoint
	 * @param  string  $method
	 * @param  array<string, mixed>  $params
	 *
	 * @return WP_Error|array{
	 *       'body' : array<string, mixed>,
	 *       'headers' : CaseInsensitiveDictionary,
	 *       'response' : array{
	 *           'code' : int,
	 *           'message' : string,
	 *       },
	 *       'cookies' : array<int, \WP_Http_Cookie>,
	 *       'filename' : string|null,
	 *       'http_response' : \WP_HTTP_Requests_Response
	 *   }
	 */
	public function request( string $endpoint, string $method = 'GET', array $params = [] ) {
		$url = $this->build_url( $endpoint );

		$args = array_merge( $this->request_args, [
			'method' => strtoupper( $method ),
		], $params );

		$response = $this->wp_http->request( $url, $args );

		if ( $response instanceof WP_Error ) {
			return $response;
		}

		$response['body'] = json_decode( $response['body'], true );

		return $response;
	}

	/**
	 * Build the complete URL from a provided endpoint.
	 *
	 * @param  string  $endpoint  The relative endpoint.
	 *
	 * @return string
	 */
	private function build_url( string $endpoint ): string {
		return rtrim( $this->base_url, '/' ) . trailingslashit( $this->api_root ) . ltrim( $endpoint, '/' );
	}

}
