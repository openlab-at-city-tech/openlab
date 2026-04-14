<?php

/**
 * SBI_Support_Tool.
 *
 * @since 6.4
 *
 * @package instagram-feed-pro
 */

namespace InstagramFeed\Admin;

use SB_Instagram_Connected_Account;
use WP_Error;
use WP_User_Query;

use function sbi_encrypt_decrypt;
use function time;
use function wp_delete_user;
use function wp_insert_user;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Create a temporary page and user that support can use to troubleshoot feed issues
 */
class SBI_Support_Tool
{
	/**
	 * Plugin name for identifying which plugin this is for
	 *
	 * @var string
	 */
	public static $plugin_name = 'SmashBalloon Instagram';

	/**
	 * Slug for identifying which plugin this is for
	 *
	 * @var string
	 */
	public static $plugin = 'smash_sbi';

	/**
	 * Temp User Name
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $name = 'SmashBalloon';

	/**
	 * Temp Last Name
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $last_name = 'Support';


	/**
	 * Temp Login UserName
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $username = 'SmashBalloon_SBISupport';

	/**
	 * Cron Job Name
	 *
	 * @access public
	 *
	 * @var string
	 */
	public static $cron_event_name = 'smash_sbi_delete_expired_user';

	/**
	 * Temp User Role
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $role = '_support_role';

	/**
	 * Instagram Basic Display API URL
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $basic_display_api = 'https://graph.instagram.com/';

	/**
	 * Instagram Graph API URL
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $graph_api = 'https://graph.facebook.com/';

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * SBI_Support_Tool initializer.
	 *
	 * @since 6.3
	 */
	public function init()
	{
		$this->init_temp_login();

		if (!is_admin()) {
			return;
		}

		$this->ini_ajax_calls();
		add_action('admin_menu', array($this, 'register_menu'));
		add_action('admin_footer', array('\InstagramFeed\Admin\SBI_Support_Tool', 'delete_expired_users'));
	}

	/**
	 * Init Login
	 *
	 * @since 6.3
	 */
	public function init_temp_login()
	{

		$attr = self::$plugin . '_token';
        if (empty($_GET[$attr])) { // phpcs:ignore
			return;
		}

        $token = sanitize_key($_GET[$attr]);  // phpcs:ignore
		$temp_user = self::get_temporary_user_by_token($token);
		if (!$temp_user) {
			wp_die(esc_attr__('You cannot connect this user', 'instagram-feed'));
		}

		$user_id = $temp_user->ID;
		$should_login = (is_user_logged_in() && $user_id !== get_current_user_id()) || !is_user_logged_in();

		if ($should_login) {
			if ($user_id !== get_current_user_id()) {
				wp_logout();
			}

			$user_login = $temp_user->user_login;

			wp_set_current_user($user_id, $user_login);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $user_login, $temp_user);
			$redirect_page = 'admin.php?page=' . self::$plugin . '_tool';
			wp_safe_redirect(admin_url($redirect_page));
			exit();
		}
	}

	/**
	 * Get User By Token.
	 *
	 * @param string $token Token to connect with.
	 *
	 * @since 6.3
	 */
	public static function get_temporary_user_by_token($token = '')
	{
		if (empty($token)) {
			return false;
		}

		$args = array(
			'fields' => 'all',
			'meta_query' => array(
				array(
					'key' => self::$plugin . '_token',
					'value' => sanitize_text_field($token),
					'compare' => '=',
				),
			),
		);

		$users = new WP_User_Query($args);
		$users_result = $users->get_results();

		if (empty($users_result)) {
			return false;
		}

		return $users_result[0];
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @return void
	 * @since 6.3
	 */
	public function ini_ajax_calls()
	{
		add_action('wp_ajax_sbi_create_temp_user', array($this, 'create_temp_user_ajax_call'));
		add_action('wp_ajax_sbi_delete_temp_user', array($this, 'delete_temp_user_ajax_call'));
		add_action('wp_ajax_sbi_get_api_calls_handler', array($this, 'get_api_calls_handler'));
	}

	/**
	 * Check & Delete Expired Users
	 *
	 * @since 6.3
	 */
	public static function delete_expired_users()
	{
		$existing_user = self::check_temporary_user_exists();
		if ($existing_user === null) {
			return false;
		}
		$is_expired = intval($existing_user['expires']) - time() <= 0;
		if (!$is_expired) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user($existing_user['id']);
	}

	/**
	 * Check Temporary User Created
	 *
	 * @since 6.3
	 */
	public static function check_temporary_user_exists()
	{
		$args = array(
			'fields' => 'all',
			'meta_query' => array(
				array(
					'key' => self::$plugin . '_token',
					'value' => null,
					'compare' => '!=',
				),
			),
		);
		$users = new WP_User_Query($args);
		$users_result = $users->get_results();
		if (empty($users_result)) {
			return null;
		}
		return self::get_user_meta_data($users_result[0]->ID);
	}

	/**
	 * Delete Temp User
	 *
	 * @since 6.3
	 */
	public static function delete_temp_user()
	{
		$existing_user = self::check_temporary_user_exists();
		if ($existing_user === null) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user($existing_user['id']);
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 */
	public function delete_temp_user_ajax_call()
	{
		check_ajax_referer('sbi-admin', 'nonce');
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}

		if (!isset($_POST['userId'])) {
			wp_send_json_error();
		}

		$user_id = absint($_POST['userId']);
		$return = self::delete_temporary_user($user_id);
		echo wp_json_encode($return);
		wp_die();
	}

	/**
	 * Delete Temp User.
	 *
	 * @param int $user_id User ID to delete.
	 *
	 * @return array
	 *
	 * @since 6.3
	 */
	public static function delete_temporary_user($user_id)
	{
		require_once ABSPATH . 'wp-admin/includes/user.php';

		if (!current_user_can('delete_users')) {
			return array(
				'success' => false,
				'message' => __('You don\'t have enough permission to delete users'),
			);
		}
		if (!wp_delete_user($user_id)) {
			return array(
				'success' => false,
				'message' => __('Cannot delete this user'),
			);
		}

		return array(
			'success' => true,
			'message' => __('User Deleted'),
		);
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 */
	public function create_temp_user_ajax_call()
	{
		check_ajax_referer('sbi-admin', 'nonce');
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}
		$return = self::create_temporary_user();
		echo wp_json_encode($return);
		wp_die();
	}

	/**
	 * Create New User.
	 *
	 * @return array
	 *
	 * @since 6.3
	 */
	public static function create_temporary_user()
	{
		if (!current_user_can('create_users')) {
			return array(
				'success' => false,
				'message' => __('You don\'t have enough permission to create users'),
			);
		}
		$domain = str_replace(
			array(
				'http://',
				'https://',
				'http://www.',
				'https://www.',
				'www.',
			),
			'',
			site_url()
		);

		$email = self::$username . '@' . $domain;
		$temp_user_args = array(
			'user_email' => $email,
			'user_pass' => self::generate_temp_password(),
			'first_name' => self::$name,
			'last_name' => self::$last_name,
			'user_login' => self::$username,
			'role' => self::$plugin . self::$role,
		);

		$temp_user_id = wp_insert_user($temp_user_args);
		if (is_wp_error($temp_user_id)) {
			$result = array(
				'success' => false,
				'message' => __('Cannot create user'),
			);
		} else {
			$creation_time = time();
			$expires = strtotime('+15 days', $creation_time);
			$token = str_replace(array('=', '&', '"', "'"), '', sbi_encrypt_decrypt('encrypt', self::generate_temp_password(35)));

			update_user_meta($temp_user_id, self::$plugin . '_user', $temp_user_id);
			update_user_meta($temp_user_id, self::$plugin . '_token', $token);
			update_user_meta($temp_user_id, self::$plugin . '_create_time', $creation_time);
			update_user_meta($temp_user_id, self::$plugin . '_expires', $expires);

			$result = array(
				'success' => true,
				'message' => __('Temporary user created successfully'),
				'user' => self::get_user_meta_data($temp_user_id),
			);
		}
		return $result;
	}

	/**
	 * Generate Temp User Password
	 *
	 * @param int $length Length of password.
	 *
	 * @return string
	 * @since 6.3
	 */
	public static function generate_temp_password($length = 20)
	{
		return wp_generate_password($length, true, true);
	}

	/**
	 * Get User Meta
	 *
	 * @param int $user_id User ID to retrieve metadata for.
	 *
	 * @return array|bool
	 *
	 * @since 6.3
	 */
	public static function get_user_meta_data($user_id)
	{
		$user = get_user_meta($user_id, self::$plugin . '_user');
		if (!$user) {
			return false;
		}
		$token = get_user_meta($user_id, self::$plugin . '_token');
		$creation_time = get_user_meta($user_id, self::$plugin . '_create_time');
		$expires = get_user_meta($user_id, self::$plugin . '_expires');

		$url = self::$plugin . '_token=' . $token[0];
		return array(
			'id' => $user_id,
			'token' => $token[0],
			'creation_time' => $creation_time[0],
			'expires' => $expires[0],
			'expires_date' => self::get_expires_days($expires[0]),
			'url' => admin_url('/?' . $url),
		);
	}

	/**
	 * Get UDays before Expiring Token
	 *
	 * @param string $expires Unix timestamp of when the token expires.
	 *
	 * @since 6.3
	 */
	public static function get_expires_days($expires)
	{
		return ceil(($expires - time()) / 60 / 60 / 24);
	}

	/**
	 * Register Menu.
	 *
	 * @since 6.0
	 */
	public function register_menu()
	{
		$role_id = self::$plugin . self::$role;
		$cap = $role_id;
		$cap = apply_filters('sbi_settings_pages_capability', $cap);

		add_submenu_page(
			'sb-instagram-feed',
			__('Support API tool', 'instagram-feed'),
			__('Support API tool', 'instagram-feed'),
			$cap,
			self::$plugin . '_tool',
			array($this, 'render'),
			5
		);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	/**
	 * Enqueue Scripts.
	 *
	 * @since 6.2.9
	 */
	public function enqueue_scripts()
	{
		$screen = get_current_screen();
		if (strpos($screen->id, self::$plugin . '_tool') === false) {
			return;
		}

		wp_enqueue_script('sbi-support-tool', SBI_PLUGIN_URL . 'admin/assets/js/support-tool.js', array('jquery'), SBIVER, true);
		wp_localize_script(
			'sbi-support-tool',
			'sbi_support_tool',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('sbi-admin'),
			)
		);

		wp_enqueue_style('sbi-support-tool', SBI_PLUGIN_URL . 'admin/assets/css/support-tool.css', array(), SBIVER);
	}

	/**
	 * Render the Api Tools Page
	 *
	 * @since 6.3
	 */
	public function render()
	{
		include_once SBI_PLUGIN_DIR . 'admin/views/support/support-tools.php';
	}

	/**
	 * Get ajax handers for API calls
	 *
	 * @since 6.2.9
	 */
	public function get_api_calls_handler()
	{
		check_ajax_referer('sbi-admin', 'nonce');

		$user_role = self::$plugin . self::$role;
		if (!sbi_current_user_can($user_role)) {
			wp_send_json_error(__('You don\'t have enough permission to perform this API call.', 'instagram-feed'));
		}

		$user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : false;
		$ajax_action = isset($_POST['ajax_action']) ? sanitize_text_field($_POST['ajax_action']) : 'user_info';
		$account_type = isset($_POST['account_type']) ? sanitize_text_field($_POST['account_type']) : 'basic';
		$connect_type = isset($_POST['connect_type']) ? sanitize_text_field($_POST['connect_type']) : 'personal';

		if (!$user_id) {
			wp_send_json_error(__('User ID is required', 'instagram-feed'));
		}

		$connected_accounts = SB_Instagram_Connected_Account::get_all_connected_accounts();

		$access_token = '';
		foreach ($connected_accounts as $connected_account) {
			if ((string)$connected_account['id'] === $user_id) {
				$access_token = $connected_account['access_token'];
				break;
			}
		}

		if (empty($access_token)) {
			wp_send_json_error(__('Access Token is required', 'instagram-feed'));
		}

		switch ($ajax_action) {
			case 'user_info':
				$api_response = $this->get_account_info(array(
					'user_id' => $user_id,
					'access_token' => $access_token,
					'account_type' => $account_type,
					'connect_type' => $connect_type,
				));
				break;

			case 'media':
				$media_fields = isset($_POST['media_fields']) ? sanitize_text_field($_POST['media_fields']) : 'media_url,thumbnail_url,caption,id,media_type,timestamp,username,permalink';
				$post_limit = isset($_POST['post_limit']) ? absint($_POST['post_limit']) : 10;
				$api_response = $this->get_media(array(
					'user_id' => $user_id,
					'access_token' => $access_token,
					'account_type' => $account_type,
					'media_fields' => $media_fields,
					'post_limit' => $post_limit,
				));
				break;

			default:
				wp_send_json_error(__('Invalid API action', 'instagram-feed'));
		}

		if (is_wp_error($api_response)) {
			wp_send_json_error($api_response);
		} else {
			$api_response = sanitize_text_field(wp_remote_retrieve_body($api_response));
			$api_response = json_decode($api_response, true);

			if (isset($api_response['error'])) {
				wp_send_json_error($api_response['error']);
			}

			// responses have next pagination data that includes access token so we need to remove it.
			if (isset($api_response['paging']['next'])) {
				$api_response['paging']['next'] = !empty($api_response['paging']['next']) ? true : false;
			}

			wp_send_json_success([
				'api_response' => $api_response,
				'user_id' => $user_id,
			]);
		}
	}

	/**
	 * Get Account Info
	 *
	 * @param array $args Arguments for the API call.
	 *
	 * @return object
	 *
	 * @since 6.2.9
	 */
	public function get_account_info($args)
	{
		$user_id = isset($args['user_id']) ? sanitize_text_field($args['user_id']) : false;
		$access_token = isset($args['access_token']) ? sanitize_text_field($args['access_token']) : false;
		$account_type = isset($args['account_type']) ? sanitize_text_field($args['account_type']) : 'basic';
		$connect_type = isset($args['connect_type']) ? sanitize_text_field($args['connect_type']) : 'personal';

		if (!$user_id || !$access_token) {
			return new WP_Error('missing_params', __('User ID and Access Token are required', 'instagram-feed'));
		}

		if ($account_type === 'basic' || $account_type === 'personal' && ($connect_type === 'business_basic' || $connect_type === 'personal')) {
			$fields = ($connect_type === 'business_basic') ? 'user_id,username,name,account_type,profile_picture_url,followers_count,follows_count,media_count,biography' : 'id,username,media_count,account_type';

			$me_endpoint_url = self::$basic_display_api . $user_id . '?fields=' . $fields . '&access_token=' . $access_token;
		} else {
			$me_endpoint_url = self::$graph_api . $user_id . '?fields=biography,id,username,website,followers_count,media_count,profile_picture_url,name&access_token=' . $access_token;
		}

		return wp_safe_remote_get($me_endpoint_url);
	}

	/**
	 * Get Media
	 *
	 * @param array $args Arguments for the API call.
	 *
	 * @return object
	 *
	 * @since 6.2.9
	 */
	public function get_media($args)
	{
		$user_id = isset($args['user_id']) ? sanitize_text_field($args['user_id']) : false;
		$access_token = isset($args['access_token']) ? sanitize_text_field($args['access_token']) : false;
		$account_type = isset($args['account_type']) ? sanitize_text_field($args['account_type']) : 'basic';
		$media_fields = isset($args['media_fields']) ? sanitize_text_field($args['media_fields']) : 'media_url,thumbnail_url,caption,id,media_type,timestamp,username,permalink';
		$post_limit = isset($args['post_limit']) ? absint($args['post_limit']) : 10;

		if (!$user_id || !$access_token) {
			return new WP_Error('missing_params', __('User ID and Access Token are required', 'instagram-feed'));
		}

		if (strpos($media_fields, 'children') !== false) {
			$media_fields .= '%7Bmedia_url,id,media_type,timestamp,permalink,thumbnail_url%7D';
		}

		if ($account_type === 'basic' || $account_type === 'personal') {
			$api_url = self::$basic_display_api . $user_id . '/media?fields=' . $media_fields . '&limit=' . $post_limit . '&access_token=' . $access_token;
		} else {
			$api_url = self::$graph_api . $user_id . '/media?fields=' . $media_fields . '&limit=' . $post_limit . '&access_token=' . $access_token;
		}

		return wp_safe_remote_get($api_url, array('timeout' => 120));
	}
}
