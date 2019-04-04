<?php

/**
 * Status class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Status {



	/**
	 * Input data
	 */
	public $data;



	/**
	 * Pairs name/value
	 */
	public $headers = array();
	public $headers_request = array();



	/**
	 * Status code, description and level
	 */
	public $code = 0;
	public $code_desc = '';
	public $level = 0;



	/**
	 * Redirect URL
	 */
	public $redirect_url = '';
	public $redirect_url_id = 0;
	public $redirect_url_level = 0;
	public $redirect_url_status = '';
	public $redirect_url_status_desc = '';
	public $redirect_curl_errno = 0;
	public $redirect_curl_err_title = '';
	public $redirect_curl_err_desc = '';
	public $redirect_steps = '';



	/**
	 * cURL error number and desc
	 */
	public $curl_errno = 0;
	public $curl_err_title = '';
	public $curl_err_desc = '';



	/**
	 * Exact request timestamp
	 */
	public $timestamp = 0;



	/**
	 * Timestamp to datetime
	 */
	public $request_at = '0000-00-00 00:00:00';



	/**
	 * Total request time
	 */
	public $total_time = 0;



	/**
	 * Total bytes and size
	 */
	public $total_size = '';
	public $total_bytes = 0;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($data = null) {
		if (!empty($data) && is_array($data)) {
			$this->process_data($data);
		}
	}



	// Data extract and validation
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Process request data
	 */
	public function process_data($data) {

		// Copy var
		$this->data = $data;

		// Process headers
		if (!empty($data['headers'])) {
			$this->extract_headers($data['headers']);
		}

		// Process request headers
		if (!empty($data['headers_request'])) {
			$this->extract_headers_request($data['headers_request']);
		}

		// Check cURL error
		if (!empty($data['curl_errno'])) {
			$this->curl_errno = (int) $data['curl_errno'];
		}

		// Timestamp and DateTime
		if (!empty($data['timestamp'])) {
			$this->timestamp = (int) $data['timestamp'];
			if (!empty($this->timestamp) && false !== ($datetime = @gmdate('Y-m-d H:i:s', $this->timestamp))) {
				$this->request_at = $datetime;
			}
		}

		// Total time
		if (!empty($data['total_time'])) {
			$this->total_time = (float) $data['total_time'];
		}

		// Total bytes
		if (!empty($data['total_bytes'])) {
			$this->total_bytes = (int) $data['total_bytes'];
		}
	}



	/**
	 * Parse and extract headers data
	 */
	private function extract_headers($headers) {

		// Parse headers
		$headers_raw = explode("\n", str_replace("\n\r", "\n", $headers));
		foreach ($headers_raw as $header) {

			// Clean line
			$header = trim($header);
			if (empty($header)) {
				continue;
			}

			// Check redirection in status code
			if (!isset($status_code) && 0 === stripos($header, 'HTTP/')) {
				$line = trim(preg_replace('/\s+/', ' ', $header));
				$line = explode(' ', $line);
				if (count($line) > 1) {
					$status_code = (int) mb_substr(trim($line[1]), 0, 3);
					$this->headers['status'] = $header;
				}

			// Parse other headers
			} elseif (false !== ($pos = strpos($header, ':')) && $pos > 0) {
				$name = trim(mb_substr($header, 0, $pos));
				$this->headers[$name] = trim(mb_substr($header, $pos + 1));
				if ('location' == strtolower($name)) {
					$this->redirect_url = $this->headers[$name];
				}
			}
		}

		// Check status
		if (isset($status_code)) {
			$this->code = $status_code;
			$this->level = (int) mb_substr($status_code, 0 , 1);
		}
	}



	/**
	 * Parse and extract request headers data
	 */
	private function extract_headers_request($headers) {

		// Parse headers
		$headers_raw = explode("\n", str_replace("\n\r", "\n", $headers));
		foreach ($headers_raw as $header) {

			// Check GET method
			if (0 === strpos($header, 'GET ')) {
				$this->headers_request['GET'] = mb_substr($header, 4);

			// Check property
			} elseif ((false !== ($pos = strpos($header, ':')) && $pos > 0)) {
				$name = trim(mb_substr($header, 0, $pos));
				$this->headers_request[$name] = trim(mb_substr($header, $pos + 1));
			}
		}
	}



}