<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3\Contracts;

use WP_Error;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

interface Client_V3 {

	/**
	 * Perform a GET request.
	 *
	 * @param  string  $endpoint
	 * @param  array<string, mixed>  $params
	 *
	 * @return WP_Error|array{
	 *      'body' : array<string, mixed>,
	 *      'headers' : CaseInsensitiveDictionary,
	 *      'response' : array{
	 *          'code' : int,
	 *          'message' : string,
	 *      },
	 *      'cookies' : array<int, \WP_Http_Cookie>,
	 *      'filename' : string|null,
	 *      'http_response' : \WP_HTTP_Requests_Response
	 *  }
	 */
	public function get( string $endpoint, array $params = [] );


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
	public function post( string $endpoint, array $params = [] );

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
	public function request( string $endpoint, string $method = 'GET', array $params = [] );

}
