<?php
	namespace TFN;

	class DPLA
	{
		/**
		 * The base URL.
		 *
		 * @var string
		 */
		const API_BASE_URL = 'http://api.dp.la/v2/';

		/**
		 * The number of seconds to wait for an API response.
		 *
		 * @var int
		 */
		const API_TIMEOUT = 60;

		/**
		 * The user's API key.
		 *
		 * @var string
		 */
		protected $_api_key = '';

		/**
		 * Constructor. Requires an API key.
		 *
		 * @param string $api_key
		 */
		public function __construct($api_key)
		{
			$this->_api_key = $api_key;
		}

		/**
		 * Create a new search query.
		 *
		 * @param array $options An initial set of search options.
		 * @return DPLA_SearchQuery
		 */
		public function createSearchQuery($options = array())
		{
			if (!class_exists('\TFN\DPLA_SearchQuery')) {
				require dirname(__FILE__).'/dpla/searchquery.php';
			}
			return new DPLA_SearchQuery($this, $options);
		}

		/**
		 * Call the API. This is used by other classes to access the API.
		 *
		 * @param string $url     The URL to request, relative to the base URL.
		 * @param array  $params  Optional array of parameters to send with the request.
		 * @return mixed          The decoded or raw response.
		 * @throws Exception If an error occurs.
		 */
		public function callAPI($url, $params = array())
		{
			// Strip the initial / if present and prepend the base URL.
			if ($url[0] == '/') {
				$url = substr($url, 1);
			}
			$url = self::API_BASE_URL.$url;

			// Add the query string if necessary.
			$params['api_key'] = $this->_api_key;
			$url .= (strpos($url, '?') === false ? '?' : '&').http_build_query($params);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, self::API_TIMEOUT);

			// Get rid of Expect issues.
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: '));

			$response = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			if ($info['http_code'] < 200 || $info['http_code'] > 299) {
				throw new Exception('\TFN\DPLA::callAPI: Request failed with status ['.$info['http_code'].']');
			}

			if (stripos($info['content_type'], 'application/json') !== false) {
				// Decode the JSON response.
				$decoded = json_decode($response, true);
				if (!is_array($decoded)) {
					throw new Exception('\TFN\DPLA::callAPI: Failed to decode JSON response: '.$response);
				}
				$response = $decoded;
			}

			return $response;
		}
	}
