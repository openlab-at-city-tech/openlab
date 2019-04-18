<?php

// Check existing class to avoid conflicts
if (!class_exists('WPLNST_Core_HTTP_Request') && !function_exists('wplnst_http_read_stream')) :

/**
 * WP Link Status Core HTTP request class
 *
 * @package WP Link Status
 * @subpackage WP Link Status Core
 */
class WPLNST_Core_HTTP_Request {



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Start request process
	 */
	public static function start() {



		/* Initialization */

		// Debug point
		self::debug('start');

		// Default timezone
		date_default_timezone_set('UTC');

		// Check basic arguments
		if (!empty($_GET) || empty($_POST) || empty($_POST['url']) || empty($_POST['url_id']) || empty($_POST['connect_timeout']) || empty($_POST['request_timeout']) || empty($_POST['max_download']) || empty($_POST['nonce']) || empty($_POST['hash'])) {
			self::debug('error args');
			self::terminate();
		}

		// Check URL
		$url = $_POST['url'];
		if (empty($url) || (0 !== stripos($url, 'http://') && 0 !== stripos($url, 'https://') && 0 !== stripos($url, 'ftp://'))) {
			self::debug('error protocol');
			self::terminate();
		}

		// Check URL id
		$url_id = (int) $_POST['url_id'];
		if (empty($url_id)) {
			self::debug('error url_id');
			self::terminate();
		}

		// Check back URL
		$back_url = empty($_POST['back_url'])? false : trim($_POST['back_url']);
		if (!empty($back_url) && (0 !== stripos($back_url, 'http://') && 0 !== stripos($back_url, 'https://'))) {
			self::debug('error back_url: '.$back_url);
			self::terminate();
		}

		// Check connect timeout
		$connect_timeout = (int) $_POST['connect_timeout'];
		if (empty($connect_timeout)) {
			self::debug('error connect timeout');
			self::terminate();
		}

		// Check request timeout
		$request_timeout = (int) $_POST['request_timeout'];
		if (empty($request_timeout)) {
			self::debug('error request timeout');
			self::terminate();
		}

		// Check max download
		global $max_download, $max_download_done;
		$max_download = (int) $_POST['max_download'];
		if (empty($max_download)) {
			self::debug('error max download');
			self::terminate();
		}

		// Check only headers
		global $only_headers, $only_headers_done;
		$only_headers = (isset($_POST['only_headers']) && '1' == $_POST['only_headers']);

		// Load nonce library
		require(dirname(dirname(__FILE__)).'/nonce/nonce.php');

		// Verify nonce for this URL hash
		if (!WPLNST_Core_Nonce::verify_nonce($_POST['nonce'], $_POST['hash'])) {
			self::debug('error nonce hash');
			self::terminate();
		}

		// Load cURL wrapper library
		require(dirname(dirname(__FILE__)).'/curl.php');

		// User Agent string
		$user_agent = empty($_POST['user_agent'])? '' : $_POST['user_agent'];



		/* URL HTTP status */

		// No timeout
		set_time_limit(0);

		// Initialize globals
		global $wplnst_http_response;
		$wplnst_http_response = '';

		// Prepare CURL options
		$curlopts = array(
			'CURLOPT_URL' 				=> $url,
			'CURLOPT_HEADER' 			=> true,
			'CURLINFO_HEADER_OUT'		=> true,
			'CURLOPT_NOBODY' 			=> false,
			'CURLOPT_HTTPHEADER' 		=> array('Expect:'),
			'CURLOPT_FOLLOWLOCATION' 	=> false,
			'CURLOPT_RETURNTRANSFER'	=> false,
			'CURLOPT_FRESH_CONNECT'		=> true,
			'CURLOPT_CONNECTTIMEOUT'	=> $connect_timeout,
			'CURLOPT_TIMEOUT'			=> $request_timeout,
			'CURLOPT_USERAGENT'			=> $user_agent,
			'CURLOPT_WRITEFUNCTION'		=> 'wplnst_http_read_stream',
		);

		// Debug point
		self::debug('before curl request');

		// Do the request
		$response = WPLNST_Core_CURL::request($curlopts, array('CURLINFO_HEADER_OUT', 'CURLINFO_TOTAL_TIME', 'CURLINFO_SIZE_DOWNLOAD', 'CURLINFO_HEADER_SIZE'));

		// Debug point
		self::debug('after curl request');

		// Check request headers
		$headers_request = isset($response['info']['CURLINFO_HEADER_OUT'])? $response['info']['CURLINFO_HEADER_OUT'] : '';

		// Check total time
		$total_time = isset($response['info']['CURLINFO_TOTAL_TIME'])? (int) $response['info']['CURLINFO_TOTAL_TIME'] : false;
		if (empty($total_time)) {
			$total_time = $response['time'];
		}

		// Total size
		$total_bytes  = isset($response['info']['CURLINFO_SIZE_DOWNLOAD'])? (int) $response['info']['CURLINFO_SIZE_DOWNLOAD'] : false;
		if (empty($total_bytes)) {
			$total_bytes = strlen($wplnst_http_response);
		}

		// Headers size
		$headers_size = isset($response['info']['CURLINFO_HEADER_SIZE'])? 	(int) $response['info']['CURLINFO_HEADER_SIZE']   : false;
		if (empty($headers_size)) {
			$headers_pos = strpos($wplnst_http_response, "\r\n\r\n");
			$headers_size = (false === $headers_pos)? $total_bytes : $headers_pos + 4;
		}

		// Extract headers (trim to avoid conflicts with this script in API mode)
		$wplnst_http_headers = trim(substr($wplnst_http_response, 0, $headers_size));

		// Check errno exception when aborted with this plugin
		if ('23' == $response['errno'] && (!empty($max_download_done) || !empty($only_headers_done))) {
			$response['errno'] = 0;
		}


		/* Back to WP */

		// Populate POST fields
		$postfields = array(
			'url_id'  			=> $url_id,
			'headers' 			=> $wplnst_http_headers,
			'headers_request' 	=> $headers_request,
			'total_time' 		=> $total_time,
			'total_bytes' 		=> $total_bytes,
			'headers_size' 		=> $headers_size,
			'curl_errno' 		=> $response['errno'],
			'timestamp' 		=> $response['timestamp'],
		);

		// Aditional body field
		if (!empty($_POST['return_body']) && '1' == $_POST['return_body']) {
			$postfields['body'] = substr($wplnst_http_response, $headers_size);
		}

		// Check back URL
		if (!empty($back_url)) {

			// Debug point
			self::debug('back to '.$back_url);

			// Spawn back URL call
			$response = WPLNST_Core_CURL::spawn(array(
				'CURLOPT_URL' 				=> $back_url,
				'CURLOPT_USERAGENT' 		=> 'WPLNST HTTP Requests script',
				'CURLOPT_POST' 				=> true,
				'CURLOPT_POSTFIELDS' 		=> http_build_query($postfields, null, '&'),
				'CURLOPT_HTTPHEADER' 		=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'),
				'CURLOPT_CONNECTTIMEOUT' 	=> 5,
				'CURLOPT_TIMEOUT' 			=> 5,
			));

			// Debug point
			self::debug('back to finished, response: '.preg_replace('/\s+/', ' ', str_replace("\n", ' ', print_r($response, true))));

			// End
			self::terminate();
		}

		// Debug point
		self::debug('end script');

		// End with post data
		self::terminate($postfields);
	}



	// Response
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Ends execution
	 */
	private static function terminate($output = array()) {

		// Initialize
		$dump = '';

		// Check array output
		if (!empty($output) && is_array($output)) {

			// JSON response
			@header('Content-Type: application/json');

			// Dump data
			$dump = self::json_encode(array(
				'status' => 'ok',
				'data' => $output,
			));
		}

		// No cache headers
		@header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
		@header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		@header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		@header('Cache-Control: post-check=0, pre-check=0', false);
		@header('Pragma: no-cache');

		// Avoid indexation
		@header('X-Robots-Tag: noindex');

		// Debug point
		self::debug('script terminate');

		// End
		die($dump);
	}



	/**
	 * A driver or wrapper for JSON encode
	 */
	private static function json_encode($string) {

		// Check native function
		if (function_exists('json_encode')) {
			return @json_encode($string);

		// Other
		} else {

			// Globals
			global $wp_json;

			// Check current instance
			if (empty($wp_json) || !($wp_json instanceof Services_JSON)) {

				// Include file
				@require_once(dirname(__FILE__).'/class-json.php');

				// Create new object
				if (class_exists('Services_JSON')) {
					$wp_json = new Services_JSON();
				}
			}

			// Check object
			return (!empty($wp_json) && $wp_json instanceof Services_JSON)? $wp_json->encodeUnsafe($string) : false;
		}
	}



	/**
	 * Debug output
	 */
	private static function debug($message) {

		// Dependencies
		static $started;
		if (!isset($started)) {
			$started = true;
			require_once dirname(dirname(__FILE__)).'/debug.php';
		}

		// Debug function call
		wplnst_debug($message, 'HTTP');
	}



}



// Callback functions
// ---------------------------------------------------------------------------------------------------



/**
 * Response callback function
 */
function wplnst_http_read_stream($ch, $line) {

	// Globals
	global $wplnst_http_response, $max_download, $max_download_done, $only_headers, $only_headers_done;

	// Current lengths
	$line_length = strlen($line);
	$total_length = strlen($wplnst_http_response);

	// Check overflow
	if (($total_length + $line_length) > $max_download) {

		// Check available size
		$available = $max_download - $total_length;
		if ($available > 0) {
			$wplnst_http_response .= substr($line, 0, $available);
		}

		// Max download achieved
		$max_download_done = true;

		// End
		return 0;
	}

	// Add new chunk
	$wplnst_http_response .= $line;

	// Check only headers
	if ($only_headers && false !== strpos($wplnst_http_response, "\r\n\r\n")) {

		// Headers achieved
		$only_headers_done = true;

		// End
		return 0;
	}

	// Done
	return strlen($line);
}


// Run
WPLNST_Core_HTTP_Request::start();


// End check
endif;