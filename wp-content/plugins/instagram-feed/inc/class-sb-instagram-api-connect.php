<?php

if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Class SB_Instagram_API_Connect
 *
 * Connect to the Instagram API and return the results. It's possible
 * to build the url from a connected account (includes access token,
 * account id, account type), endpoint and parameters (hashtag, etc..)
 * as well as a full url such as from the pagination data from some Instagram API requests.
 *
 * Errors from either the Instagram API or from the HTTP request are detected
 * and can be handled.
 *
 * Primarily used in the SB_Instagram_Feed class to collect posts and data for
 * the header. Can also be used for comments in the Pro version
 *
 * @since 2.0/5.0
 */
class SB_Instagram_API_Connect
{
	/** Response from the API request
	 *
	 * @var object
	 */
	protected $response;
	/** If the server is unable to connect to the url, returns true
	 *
	 * @var bool
	 */
	protected $encryption_error;
	/** URL for the API request
	 *
	 * @var string
	 */
	private $url;

	/**
	 * SB_Instagram_API_Connect constructor.
	 *
	 * @param mixed  $connected_account_or_url either the connected account
	 *   data for this request or the complete url for the request.
	 * @param string $endpoint (optional) is optional only if the complete url is provided
	 *     otherwise is they key for the endpoint needed for the request (ex. "header").
	 * @param array  $params (optional) used with the connected account and endpoint to add
	 *    additional query parameters to the url if needed.
	 *
	 * @since 2.0/5.0
	 */
	public function __construct($connected_account_or_url, $endpoint = '', $params = array())
	{
		if (is_array($connected_account_or_url) && isset($connected_account_or_url['access_token'])) {
			$this->set_url($connected_account_or_url, $endpoint, $params);
		} elseif (!is_array($connected_account_or_url) && strpos($connected_account_or_url, 'https') !== false) {
			$this->url = $connected_account_or_url;
		} else {
			$this->url = '';
		}
	}

	/**
	 * Determines how and where to record an error from Instagram's API response
	 *
	 * @param array  $response response from the API request.
	 * @param array  $error_connected_account the connected account that is associated
	 *    with the error.
	 * @param string $request_type key used to determine the endpoint (ex. "header").
	 *
	 * @since 2.0/5.0
	 */
	public static function handle_instagram_error($response, $error_connected_account, $request_type)
	{
		global $sb_instagram_posts_manager;
		delete_option('sbi_dismiss_critical_notice');

		$type = isset($response['error']['code']) && (int)$response['error']['code'] === 18 ? 'hashtag_limit' : 'api';

		$sb_instagram_posts_manager->add_error($type, $response, $error_connected_account);

		if ($type === 'hashtag_limit') {
			$sb_instagram_posts_manager->maybe_set_display_error($type, $response);
		}
	}

	/**
	 * Determines how and where to record an error connecting to a specified url
	 *
	 * @param $response
	 *
	 * @since 2.0/5.0
	 */
	public static function handle_wp_remote_get_error($response)
	{
		global $sb_instagram_posts_manager;
		delete_option('sbi_dismiss_critical_notice');

		$sb_instagram_posts_manager->add_error('wp_remote_get', $response);
	}

	/**
	 * Returns the response from Instagram
	 *
	 * @return array|object
	 *
	 * @since 2.0/5.0
	 */
	public function get_data()
	{
		if ($this->is_wp_error()) {
			return array();
		}
		if (!empty($this->response['data'])) {
			return $this->response['data'];
		} else {
			return $this->response;
		}
	}

	/**
	 * If the server is unable to connect to the url, returns true
	 *
	 * @return bool
	 *
	 * @since 2.0/5.0
	 */
	public function is_wp_error()
	{
		return is_wp_error($this->response);
	}

	/**
	 * Returns the error response and the url that was trying to be connected to
	 * or false if no error
	 *
	 * @return array|bool
	 *
	 * @since 2.0/5.0
	 */
	public function get_wp_error()
	{
		if ($this->is_wp_error()) {
			return array('response' => $this->response, 'url' => $this->url);
		} else {
			return false;
		}
	}

	/**
	 * Returns the full url for the next page of the API request
	 *
	 * @param $type
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public function get_next_page($type = '')
	{
		if (!empty($this->response['pagination']['next_url'])) {
			return $this->response['pagination']['next_url'];
		}

		if (!empty($this->response['paging']['next'])) {
			return $this->response['paging']['next'];
		}

		if (isset($this->response['paging']['cursors']['after']) && $this->type_allows_after_paging($type)) {
			return $this->response['paging']['cursors']['after'];
		}

		return '';
	}

	/**
	 * Certain endpoints don't include the "next" URL so
	 * this method allows using the "cursors->after" data instead
	 *
	 * @param $type
	 *
	 * @return bool
	 *
	 * @since 2.2.2/5.3.3
	 */
	public function type_allows_after_paging($type)
	{
		return false;
	}

	/**
	 * If url needs to be generated from the connected account, endpoint,
	 * and params, this function is used to do so.
	 *
	 * @param $url
	 */
	public function set_url_from_args($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public function get_url()
	{
		return $this->url;
	}

	/**
	 * Sets the url for the API request based on the account information,
	 * type of data needed, and additional parameters.
	 *
	 * Overwritten in the Pro version.
	 *
	 * @param array  $connected_account Connected account to be used in the request.
	 * @param string $endpoint_slug Header or user.
	 * @param array  $params Additional params related to the request.
	 *
	 * @since 2.0/5.0
	 * @since 2.2/5.3 added endpoints for the basic display API
	 */
	protected function set_url($connected_account, $endpoint_slug, $params)
	{
		$account_type = !empty($connected_account['type']) ? $connected_account['type'] : 'personal';
		$connect_type = isset($connected_account['connect_type']) ? $connected_account['connect_type'] : 'personal';

		$access_token = sbi_maybe_clean($connected_account['access_token']);
		if ($this->isInvalidAccessToken($access_token, $account_type, $connect_type)) {
			$this->encryption_error = true;
			$url = '';
		} else {
			$url = $this->buildUrl($connected_account, $endpoint_slug, $account_type, $connect_type, $params, $access_token);
		}

		$this->set_url_from_args($url);
	}

	/**
	 * If the server can connect but Instagram returns an error, returns true
	 *
	 * @return bool
	 *
	 * @since 2.0/5.0
	 */
	public function is_instagram_error($response = false)
	{

		if (!$response) {
			$response = $this->response;
		}

		return isset($response['error']);
	}

	/**
	 * Connect to the Instagram API and record the response
	 *
	 * @since 2.0/5.0
	 */
	public function connect()
	{
		if (empty($this->url)) {
			$this->response = array();
			return;
		}
		$args = array(
			'timeout' => 20
		);
		$response = wp_safe_remote_get($this->url, $args);

		/**
		 * Api response for instagram connection
		 *
		 * @since 6.0.6
		 */
		do_action('sbi_api_connect_response', $response, $this->url);

		if (!is_wp_error($response)) {
			// certain ways of representing the html for double quotes causes errors so replaced here.
			$response = json_decode(str_replace('%22', '&rdquo;', $response['body']), true);

			if (empty($response)) {
				$response = array(
					'error' => array(
						'code' => 'unknown',
						'message' => __("An unknown error occurred when trying to connect to Instagram's API.", 'instagram-feed')
					)
				);
			}
		}

		$this->response = $response;
	}

	/**
	 * Determines how and where to record an error connecting to a specified url
	 *
	 * @since 2.0/5.0
	 */
	public function has_encryption_error()
	{
		return isset($this->encryption_error) && $this->encryption_error;
	}

	/**
	 * Checks if the provided access token is invalid.
	 *
	 * @param string $access_token The access token to be validated.
	 * @param string $account_type The type of account associated with the access token.
	 * @param string $connect_type The type of connection being used.
	 * @return bool Returns true if the access token is invalid, false otherwise.
	 */
	protected function isInvalidAccessToken($access_token, $account_type, $connect_type)
	{
		if (
			$account_type === 'basic' || ($account_type === 'personal'
			&& ($connect_type === 'business_basic' || $connect_type === 'personal'))
		) {
			return strpos($access_token, 'IG') !== 0;
		} else {
			return strpos($access_token, 'EA') !== 0;
		}
	}

	/**
	 * Builds the URL for connecting to the Instagram API.
	 *
	 * @param array  $connected_account The connected Instagram account.
	 * @param string $endpoint The API endpoint to connect to.
	 * @param string $account_type The type of Instagram account (e.g., personal, business).
	 * @param string $connect_type The type of connection (e.g., user, page).
	 * @param array  $params Additional params related to the request.
	 * @param string $access_token The access token for authenticating the API request.
	 *
	 * @return string The constructed URL for the API request.
	 */
	protected function buildUrl($connected_account, $endpoint, $account_type, $connect_type, $params, $access_token)
	{
		if ($account_type === 'basic' || $account_type === 'personal') {
			return $this->buildInstagramUrl($connected_account, $endpoint, $connect_type, $params, $access_token);
		} else {
			return $this->buildFacebookUrl($connected_account, $endpoint, $params, $access_token);
		}
	}

	/**
	 * Builds the Instagram URL for API requests.
	 *
	 * @param array  $connected_account The connected Instagram account.
	 * @param string $endpoint The API endpoint to connect to.
	 * @param string $connect_type The type of connection (e.g., 'user', 'hashtag').
	 * @param array  $params Additional params related to the request.
	 * @param string $access_token The access token for authentication.
	 *
	 * @return string The constructed Instagram API URL.
	 */
	protected function buildInstagramUrl($connected_account, $endpoint, $connect_type, $params, $access_token)
	{
		$num = !empty($params['num']) ? (int)$params['num'] : 33;

		$fields = ($connect_type === 'business_basic') ? 'user_id,username,name,account_type,profile_picture_url,followers_count,follows_count,media_count,biography' : 'id,username,media_count,account_type';
		$media_fields = ($connect_type === 'business_basic') ? 'media_url,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink,children%7Bmedia_url,id,media_type,timestamp,permalink,thumbnail_url%7D' : 'media_url,thumbnail_url,caption,id,media_type,timestamp,username,permalink,children%7Bmedia_url,id,media_type,timestamp,permalink,thumbnail_url%7D';

		$base_url = 'https://graph.instagram.com/';

		switch ($endpoint) {
			case 'access_token':
				return $base_url . 'refresh_access_token?grant_type=ig_refresh_token&access_token=' . $access_token;
			case 'header':
				return $base_url . 'me?fields=' . $fields . '&access_token=' . $access_token;
			case 'media':
				// Single media endpoint for fetching individual post details
				$media_id = !empty($params['media_id']) ? $params['media_id'] : '';
				$media_fields = !empty($params['fields']) ? $params['fields'] : 'thumbnail_url,media_url,media_type';
				return $base_url . $media_id . '?fields=' . $media_fields . '&access_token=' . $access_token;
			default:
				$num = min($num, 200);
				return $base_url . $connected_account['user_id'] . '/media?fields=' . $media_fields . '&limit=' . $num . '&access_token=' . $access_token;
		}
	}

	/**
	 * Builds the Facebook API URL for the given connected account.
	 *
	 * @param array  $connected_account The connected account identifier.
	 * @param string $endpoint The API endpoint to connect to.
	 * @param array  $params Additional params related to the request.
	 * @param string $access_token The access token for authentication.
	 *
	 * @return string The constructed Facebook API URL.
	 */
	protected function buildFacebookUrl($connected_account, $endpoint, $params, $access_token)
	{
		$num = !empty($params['num']) ? (int)$params['num'] : 33;

		$header_fields = 'biography,id,username,website,followers_count,media_count,profile_picture_url,name';
		$media_fields = 'media_url,media_product_type,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink,children%7Bmedia_url,id,media_type,timestamp,permalink,thumbnail_url%7D';

		$base_url = 'https://graph.facebook.com/';

		switch ($endpoint) {
			case 'header':
				return $base_url . $connected_account['user_id'] . '?fields=' . $header_fields . '&access_token=' . $access_token;
			case 'media':
				// Single media endpoint for fetching individual post details
				$media_id = !empty($params['media_id']) ? $params['media_id'] : '';
				$media_fields_single = !empty($params['fields']) ? $params['fields'] : 'thumbnail_url,media_url,media_type';
				return $base_url . $media_id . '?fields=' . $media_fields_single . '&access_token=' . $access_token;
			default:
				$num = min($num, 200);
				return $base_url . $connected_account['user_id'] . '/media?fields=' . $media_fields . '&limit=' . $num . '&access_token=' . $access_token;
		}
	}
}
