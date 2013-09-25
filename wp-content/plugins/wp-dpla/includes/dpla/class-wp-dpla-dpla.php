<?php

namespace TFN;

require_once __DIR__ . '/../../lib/php-dpla/tfn/dpla.php';

class WP_DPLA_DPLA extends DPLA {
	public function callAPI( $url, $params = array() ) {
		// Strip the initial / if present and prepend the base URL.
		if ($url[0] == '/') {
			$url = substr($url, 1);
		}
		$url = self::API_BASE_URL . $url;

		// Add the query string if necessary.
		$params['api_key'] = $this->_api_key;
		$url .= (strpos($url, '?') === false ? '?' : '&').http_build_query($params);

		$r = wp_remote_get( $url, array(
			'timeout' => self::API_TIMEOUT,
			'headers' => array(
				'Expect: ',
			),
		) );

		$http_code = wp_remote_retrieve_response_code( $r );
		$response = wp_remote_retrieve_body( $r );
		$content_type = wp_remote_retrieve_header( $r, 'content-type' );

		if ( $http_code < 200 ||  $http_code > 299 ) {
			throw new Exception( '\TFN\DPLA::callAPI: Request failed with status ['. $http_code .']' );
		}

		if ( stripos( $content_type, 'application/json') !== false ) {
			// Decode the JSON response.
			$decoded = json_decode( $response, true );
			if ( !is_array( $decoded ) ) {
				throw new Exception('\TFN\DPLA::callAPI: Failed to decode JSON response: '.$response);
			}
			$response = $decoded;
		}

		return $response;
	}
}
