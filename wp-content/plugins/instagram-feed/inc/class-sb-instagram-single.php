<?php

if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Class SB_Instagram_Single
 *
 * Uses Media API to get data about single Instagram posts
 *
 * @since 2.5.3/5.8.3
 *
 * @package Instagram Feed
 */
class SB_Instagram_Single // phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
{
	/**
	 * Object that has of several encryption features.
	 *
	 * @var object|SB_Instagram_Data_Encryption
	 *
	 * @since 5.14.5
	 */
	protected $encryption;
	/**
	 * The permalink for the post used to get Media API data.
	 *
	 * @var string
	 */
	private $permalink;
	/**
	 * The parsed ID found in the permalink URL.
	 *
	 * @var string
	 */
	private $permalink_id;
	/**
	 * Data related to the post.
	 *
	 * @var array
	 */
	private $post;
	/**
	 * Error data from retrieving the Media API.
	 *
	 * @var array
	 */
	private $error;

	/**
	 * Media ID for direct Graph API access.
	 *
	 * @var string
	 */
	private $media_id;

	/**
	 * Post data from original source (contains username for account matching).
	 *
	 * @var array
	 */
	private $post_data;

	/**
	 * Cached connected accounts mapped by username for efficient lookup.
	 *
	 * @var array|null
	 */
	private static $connected_accounts_cache = null;

	/**
	 * SB_Instagram_Single constructor.
	 *
	 * @param string $permalink_or_permalink_id Either a link to the post or the ID embedded in it.
	 * @param array  $post_data Optional. Original post data containing media_id and username.
	 */
	public function __construct($permalink_or_permalink_id, $post_data = array())
	{
		if (strpos($permalink_or_permalink_id, 'http') !== false) {
			$this->permalink = $permalink_or_permalink_id;
			$exploded_permalink = explode('/', $permalink_or_permalink_id);
			$permalink_id = $exploded_permalink[4];

			$this->permalink_id = $permalink_id;
		} else {
			$this->permalink_id = $permalink_or_permalink_id;
			$this->permalink = 'https://www.instagram.com/p/' . $this->permalink_id;
		}
		$this->error = false;
		$this->post_data = $post_data;

		// Extract media_id from post_data if available
		$this->media_id = !empty($post_data['id']) ? $post_data['id'] : '';

		$this->encryption = new SB_Instagram_Data_Encryption();
	}

	/**
	 * Sets post data from cache or fetches new data
	 * if it doesn't exist or hasn't been updated recently
	 *
	 * @since 2.5.3/5.8.3
	 */
	public function init()
	{
		$this->post = $this->maybe_saved_data();

		if ((empty($this->post) || !$this->was_recently_updated()) && !$this->should_delay_fetch_request()) {
			$data = $this->fetch();
			if (!empty($data)) {
				$data = $this->parse_and_restructure($data);
				$this->post = $data;
				$this->update_last_update_timestamp();
				$this->update_single_cache();
			} elseif ($data === false) {
				$this->add_fetch_request_delay();
			}
		}
	}

	/**
	 * Returns whatever data exists or empty array
	 *
	 * @return array
	 *
	 * @since 2.5.3/5.8.3
	 */
	private function maybe_saved_data()
	{
		$stored_option = get_option('sbi_single_cache', array());
		if (!is_array($stored_option)) {
			$stored_option = json_decode($this->encryption->decrypt($stored_option), true);
		}
		$data = array();
		if (!empty($stored_option[$this->permalink_id])) {
			return $stored_option[$this->permalink_id];
		} else {
			$settings = get_option('sb_instagram_settings', array());
			$resize_disabled = false;
			if (isset($settings['sb_instagram_disable_resize']) && $settings['sb_instagram_disable_resize'] === 'on') {
				$resize_disabled = true;
			}

			if (!$resize_disabled) {
				global $wpdb;

				$posts_table_name = $wpdb->prefix . SBI_INSTAGRAM_POSTS_TYPE;
                // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$results = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT json_data FROM $posts_table_name
						WHERE instagram_id = %s
						LIMIT 1",
						$this->permalink_id
					)
				);
                // phpcs:enable
				if (isset($results[0])) {
					$data = json_decode($this->encryption->decrypt($results[0]), true);
				}
			}
		}

		return $data;
	}

	/**
	 * Image URLs expire so this will compare when the data
	 * was last updated from the API
	 *
	 * @return bool
	 *
	 * @since 2.5.3/5.8.3
	 */
	public function was_recently_updated()
	{
		if (!isset($this->post['last_update'])) {
			return false;
		}

		return (time() - 14 * DAY_IN_SECONDS) < $this->post['last_update'];
	}

	/**
	 * If there was a problem with the last fetch request, the plugin
	 * waits 5 minutes to try again to prevent excessive API calls
	 * and Instagram throttling
	 *
	 * @return bool
	 *
	 * @since 2.5.3/5.8.3
	 */
	public function should_delay_fetch_request()
	{
		return get_transient('sbi_delay_fetch_' . $this->permalink_id) !== false;
	}

	/**
	 * Makes an HTTP request for fresh data from Media API.
	 *
	 * Media API - For owned posts (user posts with media_id and username)
	 *
	 * @return bool|mixed|null
	 *
	 * @since 2.5.3/5.8.3
	 * @since 6.10.0 Added Media API support for future-proofing.
	 */
	public function fetch()
	{
		// Try Media API if media_id is available
		if (!empty($this->media_id)) {
			$data = $this->fetch_from_media_api();
			return $data;
		}

		return false;
	}

	/**
	 * Fetches data from Instagram Media API using media ID.
	 *
	 * Uses SB_Instagram_API_Connect class to handle proper authentication,
	 * token validation, and host selection (graph.instagram.com vs graph.facebook.com).
	 *
	 * Note: Media API only works for posts owned by the connected account.
	 *
	 * @return bool|array False on failure, array of data on success
	 *
	 * @since 6.10.0
	 */
	private function fetch_from_media_api()
	{
		// Get connected account for this post
		$connected_account = $this->get_connected_account_for_post();

		if (empty($connected_account)) {
			return false;
		}

		// Verify this post belongs to the connected account
		if (!empty($this->post_data['username']) && !empty($connected_account['username'])) {
			if ($this->post_data['username'] !== $connected_account['username']) {
				return false;
			}
		}

		// Use API Connect class to build URL with proper authentication
		$params = array(
			'media_id' => $this->media_id,
			'fields' => 'thumbnail_url,media_url,media_type'
		);

		$api_connect = new SB_Instagram_API_Connect($connected_account, 'media', $params);

		// Check for encryption/token errors before connecting
		if ($api_connect->has_encryption_error()) {
			return false;
		}

		// Make the API request
		$api_connect->connect();

		// Handle errors
		if ($api_connect->is_wp_error()) {
			return false;
		}

		$data = $api_connect->get_data();

		if ($api_connect->is_instagram_error($data)) {
			$this->add_fetch_request_delay();
			// Fail silently
			return false;
		}

		return $data;
	}

	/**
	 * Gets the connected account for the current post.
	 *
	 * Uses username from post_data to match with connected accounts.
	 * Implements static caching to avoid repeated database queries.
	 *
	 * @return array|bool Connected account array or false if not found
	 *
	 * @since 6.10.0
	 */
	private function get_connected_account_for_post()
	{
		// Load and cache all connected accounts once per request
		if (self::$connected_accounts_cache === null) {
			self::$connected_accounts_cache = array();

			if (class_exists('SB_Instagram_Connected_Account')) {
				$all_accounts = SB_Instagram_Connected_Account::get_all_connected_accounts();

				// Build username => account map for O(1) lookup
				foreach ($all_accounts as $account) {
					if (!empty($account['username'])) {
						self::$connected_accounts_cache[$account['username']] = $account;
					}
				}
			}
		}

		// Try to get username from post_data
		$username = '';
		if (!empty($this->post_data['username'])) {
			$username = $this->post_data['username'];
		}

		// Direct lookup by username (O(1))
		if (!empty($username) && isset(self::$connected_accounts_cache[$username])) {
			return self::$connected_accounts_cache[$username];
		}

		// Fallback: return first available account
		if (!empty(self::$connected_accounts_cache)) {
			return reset(self::$connected_accounts_cache);
		}

		return false;
	}

	/**
	 * If there's an error, fetch requests are delayed 5 minutes
	 * for the specific permalink/post to prevent excessive requests
	 *
	 * @since 2.5.3/5.8.3
	 */
	public function add_fetch_request_delay()
	{
		set_transient('sbi_delay_fetch_' . $this->permalink_id, true, 300);
	}

	/**
	 * Data is restructured to look like regular API data
	 * for ease of use with other plugin features
	 *
	 * @param array $data Raw data from the Media API.
	 *
	 * @return array
	 *
	 * @since 2.5.3/5.8.3
	 * @since 6.10.0 Added Media API support.
	 */
	private function parse_and_restructure($data)
	{
		$return = array(
			'thumbnail_url' => '',
			'media_url' => '',
			'id' => $this->permalink_id,
			'media_type' => isset($data['media_type']) ? $data['media_type'] : 'IMAGE',
		);

		if (!empty($data['thumbnail_url'])) {
			$return['thumbnail_url'] = $data['thumbnail_url'];
		}

		if (!empty($data['media_url'])) {
			$return['media_url'] = $data['media_url'];
		}

		/**
		 * Filter the restructured post data from Media API
		 *
		 * Allows developers to modify or add alternative thumbnail sources
		 * when Instagram's Media API doesn't return thumbnail_url
		 *
		 * @param array  $return       The restructured post data
		 * @param array  $data         Raw API response data
		 * @param string $permalink    Post permalink
		 * @param string $permalink_id Post shortcode/ID
		 *
		 * @since 6.10.0
		 */
		$return = apply_filters('sbi_single_parse_and_restructure', $return, $data, $this->permalink, $this->permalink_id);

		return $return;
	}

	/**
	 * Track last API request due to some data expiring and
	 * needing to be refreshed
	 *
	 * @since 2.5.3/5.8.3
	 */
	private function update_last_update_timestamp()
	{
		$this->post['last_update'] = time();
	}

	/**
	 * Data retrieved with this method has its own cache
	 *
	 * @since 2.5.3/5.8.3
	 */
	private function update_single_cache()
	{
		$stored_option = get_option('sbi_single_cache', array());
		if (!is_array($stored_option)) {
			$stored_option = json_decode($this->encryption->decrypt($stored_option), true);
		}
		$new = array($this->permalink_id => $this->post);
		$stored_option = array_merge($new, (array)$stored_option);
		// only latest 400 posts to prevent a crazy amount of these.
		$stored_option = array_slice($stored_option, 0, 400);

		update_option('sbi_single_cache', $this->encryption->encrypt(sbi_json_encode($stored_option)), false);
	}

	/**
	 * Get the data related to the Instagram post.
	 *
	 * @return array
	 *
	 * @since 2.5.3/5.8.3
	 */
	public function get_post()
	{
		return $this->post;
	}

	/**
	 * Get error that occurred when retrieving data
	 *
	 * @return array|false
	 */
	public function get_error()
	{
		return $this->error;
	}
}
