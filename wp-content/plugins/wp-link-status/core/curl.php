<?php

/**
 * CURL class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_CURL {



	/**
	 * Spawn async request
	 */
	public static function spawn($setopts = array()) {
		return self::request(array_merge(array(
			'CURLOPT_HEADER' 			=> false,
			'CURLOPT_NOBODY' 			=> true,
			'CURLOPT_FOLLOWLOCATION' 	=> false,
			'CURLOPT_RETURNTRANSFER' 	=> false,
			'CURLOPT_FRESH_CONNECT' 	=> true,
			'CURLOPT_CONNECTTIMEOUT' 	=> 5,
			'CURLOPT_TIMEOUT' 			=> 5,
		), $setopts));
	}



	/**
	 * Submit a POST request
	 */
	public static function post($setopts = array(), $postfields = array()) {
		return self::request(array_merge(array(
			'CURLOPT_HEADER' 			=> false,
			'CURLOPT_NOBODY' 			=> false,
			'CURLOPT_FOLLOWLOCATION' 	=> false,
			'CURLOPT_RETURNTRANSFER' 	=> true,
			'CURLOPT_FRESH_CONNECT' 	=> true,
			'CURLOPT_POST'				=> true,
			'CURLOPT_POSTFIELDS' 		=> http_build_query($postfields, null, '&'),
			'CURLOPT_HTTPHEADER'		=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'),
		), $setopts));
	}



	/**
	 * Basic cURL wrapper
	 */
	public static function request($setopts = array(), $getinfo = array()) {

		// Default
		$response = array(
			'data'   => '',
			'info'	 => array(),
			'error'  => true,
			'errno'  => 0,
			'reason' => '',
			'time'	 => 0,
			'timestamp' => 0,
		);

		// Check cURL support
		if (function_exists('wplnst_is_curl_enabled') && !wplnst_is_curl_enabled()) {
			$response['reason'] = 'curl_disabled';
			return $response;
		}

		// cURL session
		if (false === ($ch = @curl_init())) {
			$response['reason'] = 'curl_init';
			return $response;
		}

		// IP resolve options
		if (!isset($setopts['CURLOPT_IPRESOLVE']) && defined('CURL_IPRESOLVE_V4')) {
			$setopts['CURLOPT_IPRESOLVE'] = CURL_IPRESOLVE_V4;
		}

		// HTTPS checks
		if (!empty($setopts['CURLOPT_URL']) && 0 === strpos($setopts['CURLOPT_URL'], 'https')) {
			if (!isset($setopts['CURLOPT_SSL_VERIFYHOST'])) {
				$setopts['CURLOPT_SSL_VERIFYHOST'] = false;
			}
			if (!isset($setopts['CURLOPT_SSL_VERIFYPEER'])) {
				$setopts['CURLOPT_SSL_VERIFYPEER'] = false;
			}
		}

		// Apply options
		foreach ($setopts as $name => $value) {
			if (defined($name)) {
				@curl_setopt($ch, constant($name), $value);
			}
		}

		// Now
		$response['timestamp'] = time();

		// Timer
		$time_start = microtime(true);

		// Retrieve response
		$response['data'] = @curl_exec($ch);

		// Time amount
		$response['time'] = microtime(true) - $time_start;

		// Copy error number
		$response['errno'] = (int) @curl_errno($ch);

		// Check info
		foreach ($getinfo as $info) {
			if (defined($info)) {
				$response['info'][$info] = @curl_getinfo($ch, constant($info));
			}
		}

		// Close connection
		@curl_close($ch);

		// Check error
		$response['error'] = ($response['errno'] > 0);

		// Done
		return $response;
	}



}