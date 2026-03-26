<?php

namespace InstagramFeed\Builder;

use InstagramFeed\Helpers\Util;
use SB_Instagram_API_Connect;
use SB_Instagram_Connected_Account;
use SB_Instagram_Data_Encryption;
use SB_Instagram_Data_Manager;
use SB_Instagram_Parse;

/**
 * Instagram Feed Source
 *
 * @since 6.0
 */
class SBI_Source
{
	protected const BATCH_SIZE = 10;

	/**
	 * AJAX hooks for various feed data related functionality
	 *
	 * @since 6.0
	 */
	public static function hooks()
	{
		add_action('wp_ajax_sbi_source_builder_update', array('InstagramFeed\Builder\SBI_Source', 'builder_update'));
		add_action('wp_ajax_sbi_source_builder_update_multiple', array('InstagramFeed\Builder\SBI_Source', 'builder_update_multiple'));
		add_action('wp_ajax_sbi_source_get_page', array('InstagramFeed\Builder\SBI_Source', 'get_page'));
		add_action('admin_init', array('InstagramFeed\Builder\SBI_Source', 'batch_process_legacy_source_queue'));
	}

	/**
	 * Used in an AJAX call to update sources based on selections or
	 * input from a user. Makes an API request to add additiona info
	 * about the connected source.
	 *
	 * @since 6.0
	 */
	public static function builder_update()
	{
		if (!check_ajax_referer('sbi-admin', 'nonce', false)) {
			wp_send_json_error();
		}
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}

		$source_data = array(
			'access_token' => sanitize_text_field($_POST['access_token']),
			'id' => sanitize_text_field($_POST['id']),
			'type' => sanitize_text_field($_POST['type']),
			'username' => isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '',
		);

		$return = sbi_connect_new_account($source_data['access_token'], $source_data['id']);

		if (empty($return)) {
			$return = array(
				'error' => '<div class="sbi-connect-actions sb-alerts-wrap"><div class="sb-alert">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.99935 0.666504C4.39935 0.666504 0.666016 4.39984 0.666016 8.99984C0.666016 13.5998 4.39935 17.3332 8.99935 17.3332C13.5993 17.3332 17.3327 13.5998 17.3327 8.99984C17.3327 4.39984 13.5993 0.666504 8.99935 0.666504ZM9.83268 13.1665H8.16602V11.4998H9.83268V13.1665ZM9.83268 9.83317H8.16602V4.83317H9.83268V9.83317Z" fill="#995C00"/>
                            </svg>
                            <span><strong>' . esc_html__('Something went wrong. Please make sure the ID and access token are correct.', 'instagram-feed') . '</strong></span><br>
                            ' . '' . '
                        </div></div>',
			);
		}

		if (empty($return['error'])) {
			wp_send_json_success(SBI_Feed_Builder::get_source_list());
		}

		wp_send_json_error(array('message' => $return['error']));
	}

	/**
	 * Used in an AJAX call to update Multiple sources based on selections or
	 * input from a user. Makes an API request to add additiona info
	 * about the connected source.
	 *
	 * @since 6.0
	 */
	public static function builder_update_multiple()
	{
		if (!check_ajax_referer('sbi-admin', 'nonce', false)) {
			wp_send_json_error();
		}
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['sourcesList']) && !empty($_POST['sourcesList']) && is_array($_POST['sourcesList'])) {
			foreach ($_POST['sourcesList'] as $single_source) :
				$source_data = array(
					'access_token' => sanitize_text_field($single_source['access_token']),
					'id' => sanitize_text_field($single_source['id']),
					'type' => sanitize_text_field($single_source['type']),
					'username' => isset($single_source['username']) ? sanitize_text_field($single_source['username']) : '',
					'privilege' => ''
				);

				if ($single_source['type'] === 'business') {
					$source_data['privilege'] = 'tagged';
				}

				if (!empty($single_source['name'])) {
					$source_data['name'] = sanitize_text_field($single_source['name']);
				}

				self::process_connecting_source_data($source_data);
			endforeach;
		}
		echo sbi_json_encode(SBI_Feed_Builder::get_source_list());
		wp_die();
	}

	/**
	 * Add our update a source from raw API data.
	 *
	 * @param $source_data
	 *
	 * @return string
	 */
	public static function process_connecting_source_data($source_data)
	{
		$connected_account = array(
			'id' => $source_data['id'],
			'user_id' => $source_data['id'],
			'type' => $source_data['type'],
			'account_type' => $source_data['type'],
			'username' => $source_data['username'],
			'access_token' => $source_data['access_token'],
			'privilege' => $source_data['privilege'],
		);
		$single_source = self::update_single_source($connected_account, false);

		if (!empty($single_source['error'])) {
			$message = !empty($single_source['error']['error']['message']) ? esc_html($single_source['error']['error']['message']) : '';
			$code = !empty($single_source['error']['error']['code']) ? esc_html($single_source['error']['error']['code']) : '';
			if (isset($single_source['error']['response']) && is_wp_error($single_source['error']['response'])) {
				$response = $single_source['error']['response'];
				$message = sprintf(__('Error connecting to %s.', 'instagram-feed'), $single_source['error']['url']);
				if (isset($response) && isset($response->errors)) {
					foreach ($response->errors as $key => $item) {
						$code = $key;
						$message .= $item[0];
					}
				}
			}
			$message .= ' <a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __('Directions on how to resolve this issue', 'instagram-feed') . '</a>';

			$return_html = '<div class="sb-alerts-wrap"><div class="sb-alert">';
			$return_html .= '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">';
			$return_html .= '<path d="M8.99935 0.666504C4.39935 0.666504 0.666016 4.39984 0.666016 8.99984C0.666016 13.5998 4.39935 17.3332 8.99935 17.3332C13.5993 17.3332 17.3327 13.5998 17.3327 8.99984C17.3327 4.39984 13.5993 0.666504 8.99935 0.666504ZM9.83268 13.1665H8.16602V11.4998H9.83268V13.1665ZM9.83268 9.83317H8.16602V4.83317H9.83268V9.83317Z" fill="#995C00"/>';
			$return_html .= '</svg>';
			$return_html .= '<span><strong>' . sprintf(__('Connection Error: %s ', 'instagram-feed'), $code) . '</strong></span><br>' . $message . '</div></div>';

			$return = array(
				'success' => false,
				'message' => $return_html,
			);
			return sbi_json_encode($return);
		} else {
			global $sb_instagram_posts_manager;
			$sb_instagram_posts_manager->remove_error('connection');
		}

		$manager = new SB_Instagram_Data_Manager();
		$manager->update_last_used();

		return sbi_json_encode(SBI_Feed_Builder::get_source_list());
	}

	/**
	 * Transfer data from a connected account to the sources table
	 * after it's been validated with an API call
	 *
	 * @param array $connected_account
	 * @param bool  $connect_if_error
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function update_single_source($connected_account, $connect_if_error = true)
	{
		$account_type = isset($connected_account['account_type']) ? $connected_account['account_type'] : 'business';
		$connect_type = isset($connected_account['connect_type']) ? $connected_account['connect_type'] : 'personal';

		$connection = new SB_Instagram_API_Connect($connected_account, 'header', array());

		$connection->connect();
		if (isset($connected_account['privilege']) && $connected_account['privilege'] === 'tagged') {
			$connected_account['use_tagged'] = true;
		}

		$source_data = array(
			'access_token' => $connected_account['access_token'],
			'id' => $connected_account['user_id'],
			'type' => $account_type,
			'username' => $connected_account['username'],
			'privilege' => !empty($connected_account['use_tagged']) ? 'tagged' : '',
			'connect_type' => $connect_type,
		);

		if (!empty($connected_account['expires_timestamp'])) {
			$source_data['expires'] = date('Y-m-d H:i:s', $connected_account['expires_timestamp']);
		}

		if (isset($connected_account['local_avatar']) && $connected_account['local_avatar']) {
			SB_Instagram_Connected_Account::update_local_avatar_status($connected_account['username'], true);
		}

		$header_details = '{}';
		$source_data['error'] = '';
		if (!$connection->is_wp_error() && !$connection->is_instagram_error()) {
			$header_array = $connection->get_data();
			$header_array = self::merge_account_details($header_array, $connected_account);

			$cdn_avatar_url = SB_Instagram_Parse::get_avatar($header_array, array(), true);
			if (!empty($cdn_avatar_url)) {
				$created = SB_Instagram_Connected_Account::create_local_avatar($header_array['username'], $cdn_avatar_url);
				SB_Instagram_Connected_Account::update_local_avatar_status($header_array['username'], $created);

				if ($created) {
					$header_array['local_avatar_url'] = SB_Instagram_Connected_Account::get_local_avatar_url($header_array['username']);
					$header_array['local_avatar'] = SB_Instagram_Connected_Account::get_local_avatar_url($header_array['username']);
				}
			} else {
				SB_Instagram_Connected_Account::delete_local_avatar($header_array['username']);
				$header_array['local_avatar'] = false;
			}
			$source_data['username'] = $header_array['username'];
			$header_details = sbi_json_encode($header_array);
			$source_data['local_avatar'] = $header_array['local_avatar'];
			$source_data['local_avatar_url'] = $header_array['local_avatar'];
		} else {
			$source_data['error'] = $connection;
			if ($connection->is_wp_error()) {
				$source_data['error'] = $connection->get_wp_error();
			} else {
				$source_data['error'] = $connection->get_data();
			}
		}

		$source_data['info'] = $header_details;

		if (!empty($connected_account['private'])) {
			$source_data['info']['private'] = $connected_account['private'];
		}

		if (empty($source_data['error']) || $connect_if_error) {
			$args = array(
				'username' => $source_data['username'],
			);
			$results = SBI_Db::source_query($args);
			$already_connected = isset($results[0]) && $results[0]['username'] === $source_data['username'];

			if ($already_connected) {
				SBI_Db::delete_source($results[0]['id']);
			}
			self::update_or_insert($source_data);
		}

		$source_data['record_id'] = 0;
		$source_data['account_id'] = $connected_account['user_id'];
		$source_data['account_type'] = $account_type;

		return $source_data;
	}

	public static function merge_account_details($header_details_array, $connected_account)
	{
		$header_details_array['local_avatar'] = !empty($connected_account['local_avatar']);
		$header_details_array['name'] = !empty($connected_account['name']) ? $connected_account['name'] : '{}';
		$header_details_array['page_access_token'] = !empty($connected_account['page_access_token']) ? $connected_account['page_access_token'] : '';

		return $header_details_array;
	}

	/**
	 * Used to update or insert connected accounts (sources)
	 *
	 * @param array $source_data
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function update_or_insert($source_data)
	{
		$result = false;

		if (isset($source_data['id'])) {
			if (isset($source_data['info']) && (is_object($source_data['info']) || is_array($source_data['info']))) {
				$source_data['info'] = sbi_json_encode($source_data['info']);
			}

			if (self::exists_in_database($source_data)) {
				$source_data['last_updated'] = date('Y-m-d H:i:s');
				$result = self::update($source_data, false) !== false;
			} elseif (isset($source_data['access_token'])) {
				$result = self::insert($source_data) !== false;
			}
		}

		return $result;
	}

	/**
	 * Whether or not the source exists in the database
	 *
	 * @param array $args
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function exists_in_database($args)
	{
		$results = SBI_Db::source_query($args);

		return isset($results[0]);
	}

	/**
	 * Update info in rows that match the source data
	 *
	 * @param array $source_data
	 *
	 * @return false|int
	 *
	 * @since 6.0
	 */
	public static function update($source_data, $where_privilige = true)
	{
		$where = array('id' => $source_data['id']);
		unset($source_data['id']);

		if ($where_privilige && isset($source_data['privilege'])) {
			$where['privilege'] = $source_data['privilege'];
		}

		// usernames are more common in the other plugins so
		// that is the name of the column that is used as the
		// page or group "name" data
		if (isset($source_data['name'])) {
			$source_data['username'] = $source_data['name'];
		}
		$data = $source_data;

		return SBI_Db::source_update($data, $where);
	}

	/**
	 * Add a new source as a row in the sbi_sources table
	 *
	 * @param array $source_data
	 *
	 * @return false|int
	 *
	 * @since 6.0
	 */
	public static function insert($source_data)
	{
		if (isset($source_data['name'])) {
			$source_data['username'] = $source_data['name'];
		}
		$data = $source_data;

		return SBI_Db::source_insert($data);
	}

	/**
	 * Get a list of sources with a limit and offset like a page
	 *
	 * @since 6.0
	 */
	public static function get_page()
	{
		if (!check_ajax_referer('sbi-admin', 'nonce', false)) {
			wp_send_json_error();
		}
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}

		$args = array('page' => $_POST['page']);
		$source_data = SBI_Db::source_query($args);

		echo sbi_json_encode($source_data);

		wp_die();
	}

	/**
	 * Connection URLs are based on the website connecting accounts so that is
	 * configured here and returned
	 *
	 * @param bool $is_settings
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_connection_urls($is_settings = false)
	{
		$urls = array();
		$admin_url_state = $is_settings ? admin_url('admin.php?page=sbi-settings') : admin_url('admin.php?page=sbi-feed-builder');
		$nonce = wp_create_nonce('sbi_con');

		// If the admin_url isn't returned correctly then use a fallback
		if (
			$admin_url_state === '/wp-admin/admin.php?page=sbi-feed-builder'
			|| $admin_url_state === '/wp-admin/admin.php?page=sbi-feed-builder&tab=configuration'
		) {
			$admin_url_state = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}

		$admin_email = get_option('admin_email', '');
		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$user_email = $current_user->user_email;
		}
		$user_email = isset($user_email) ? $user_email : $admin_email;

		$urls['personal'] = array(
			'connect' => SBI_CONNECT_URL,
			'wordpress_user' => sanitize_email($user_email),
			'v' => 'free',
			'vn' => SBIVER,
			'sbi_con' => $nonce,
		);

		$urls['business'] = array(
			'connect' => SBI_CONNECT_URL,
			'wordpress_user' => sanitize_email($user_email),
			'v' => 'free',
			'vn' => SBIVER,
			'sbi_con' => $nonce,
		);

		$urls['stateURL'] = $admin_url_state;

		return $urls;
	}

	/**
	 * Used as a listener for the account connection process. If
	 * data is returned from the account connection processed it's used
	 * to generate the list of possible sources to chose from.
	 *
	 * @return array|bool
	 *
	 * @since 6.0
	 */
	public static function maybe_source_connection_data()
	{
		$nonce = !empty($_GET['sbi_con']) ? sanitize_key($_GET['sbi_con']) : '';
		if (!wp_verify_nonce($nonce, 'sbi_con')) {
			return false;
		}
		if (isset($_GET['sbi_access_token']) && isset($_GET['sbi_graph_api'])) {
			return self::retrieve_available_advanced_business_accounts();
		} elseif (isset($_GET['sbi_access_token']) && isset($_GET['sbi_account_type'])) {
			return self::retrieve_available_basic_accounts();
		}

		return false;
	}

	/**
	 * Uses the Facebook API to retrieve a list of business accounts
	 *
	 * @return array|bool
	 *
	 * @since 6.0
	 */
	public static function retrieve_available_advanced_business_accounts()
	{

		$return = array(
			'type' => 'business',
			'unconnectedAccounts' => array(),
			'matchingExistingAccounts' => array(),
			'didQuickUpdate' => false,
		);

		$access_token = sbi_maybe_clean(urldecode($_GET['sbi_access_token']));
		if (empty($access_token)) {
			return array();
		}
		$url = 'https://graph.facebook.com/me/accounts?fields=instagram_business_account,access_token&limit=500&access_token=' . $access_token;

		$args = array(
			'timeout' => 60,
		);
		$result = wp_safe_remote_get($url, $args);
		$pages_data = '{}';
		if (!is_wp_error($result)) {
			$pages_data = $result['body'];
		} else {
			$page_error = $result;
		}

		if (isset($page_error) && isset($page_error->errors)) {
			$error_message = '';
			foreach ($page_error->errors as $key => $item) {
				$error_message .= $key . ': ' . $item[0] . ' ';
			}
			return array(
				'error' => array(
					'code' => 'HTTP Request',
					'message' => __('Your server could not complete a remote request to Facebook\'s API. Your host may be blocking access or there may be a problem with your server.', 'instagram-feed'),
					'details' => $error_message,
				),
			);
		}
		$pages_data_arr = json_decode($pages_data, true);

		if (empty($pages_data_arr['data'])) {
			return array(
				'error' => array(
					'code' => 'No Accounts Found',
					'message' => __('Couldn\'t find Business Profile', 'instagram-feed'),
					'details' => sprintf(__('Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the %1$sFacebook account%2$s in this browser which is associated with your Instagram Business Profile.', 'instagram-feed'), '<a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">', '</a>'),
				),
			);
		}

		$user_url = 'https://graph.facebook.com/me?fields=name,id,picture&access_token=' . $access_token;
		$args = array(
			'timeout' => 60,
		);
		$result = wp_safe_remote_get($user_url, $args);
		if (!is_wp_error($result)) {
			$user_data = $result['body'];
			$user_data_arr = json_decode($user_data, true);

			$return['user'] = $user_data_arr;
		}
		$return['numFound'] = count($pages_data_arr['data']);

		foreach ($pages_data_arr['data'] as $page_data) {
			if (isset($page_data['instagram_business_account'])) {
				$instagram_business_id = $page_data['instagram_business_account']['id'];
				$page_access_token = isset($page_data['access_token']) ? $page_data['access_token'] : '';
				$instagram_account_url = 'https://graph.facebook.com/' . $instagram_business_id . '?fields=name,username,profile_picture_url&access_token=' . $access_token;

				$args = array(
					'timeout' => 60,
				);
				$result = wp_safe_remote_get($instagram_account_url, $args);
				if (!is_wp_error($result)) {
					$instagram_account_info = $result['body'];

					$instagram_account_data = json_decode($instagram_account_info, true);

					$instagram_biz_img = isset($instagram_account_data['profile_picture_url']) ? $instagram_account_data['profile_picture_url'] : false;
					$instagram_account_data['connect_type'] = 'business_advanced';
					$source_data = array(
						'access_token' => $access_token,
						'id' => $instagram_business_id,
						'user_id' => $instagram_business_id,
						'type' => 'business',
						'username' => $instagram_account_data['username'],
						'avatar' => $instagram_biz_img,
						'privilege' => 'tagged',
						'connect_type' => 'business_advanced',
					);

					$source_data['info'] = sbi_json_encode($instagram_account_data);
					$return['unconnectedAccounts'][] = $source_data;

					$args = array(
						'username' => $instagram_account_data['username'],
					);
					$results = SBI_Db::source_query($args);
					$already_connected_as_business_account = isset($results[0]) && $results[0]['account_type'] === 'business';
					$matches_existing_personal = isset($results[0]) && in_array($results[0]['account_type'], array('personal', 'basic'));

					if ($already_connected_as_business_account) {
						SBI_Db::delete_source($results[0]['id']);
						self::update_or_insert($source_data);
					} elseif ($matches_existing_personal && $return['numFound'] === 1) {
						$return['didQuickUpdate'] = true;
						SBI_Db::delete_source($results[0]['id']);
						self::update_or_insert($source_data);
					}
				} else {
					$page_error = $result;
				}
			}
		}

		if (empty($return['unconnectedAccounts'])) {
			return array(
				'error' => array(
					'code' => 'No Accounts Found',
					'message' => __('Couldn\'t find Business Profile', 'instagram-feed'),
					'details' => sprintf(__('Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the %1$sFacebook account%2$s in this browser which is associated with your Instagram Business Profile. If you are, in fact, logged-in to the correct account please make sure you have Instagram accounts connected with your Facebook account by following %3$sthis FAQ%4$s', 'instagram-feed'), '<a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">', '</a>', '<a href="https://smashballoon.com/reconnecting-an-instagram-business-profile/" target="_blank" rel="noopener">', '</a>'),
				),
			);
		}

		return $return;
	}

	/**
	 * Uses the Instagram Basic Display API to get available personal
	 * accounts
	 *
	 * @return array|bool
	 *
	 * @since 6.0
	 */
	public static function retrieve_available_basic_accounts()
	{
		$encryption = new SB_Instagram_Data_Encryption();
		$return = array(
			'type' => 'personal',
			'unconnectedAccounts' => array(),
			'matchingExistingAccounts' => array(),
			'didQuickUpdate' => false,
		);

		$access_token = sanitize_text_field($_GET['sbi_access_token']);
		if (empty($access_token)) {
			return array();
		}
		$user_id = sanitize_text_field($_GET['sbi_id']);
		$user_name = sanitize_text_field($_GET['sbi_username']);
		$expires_in = (int)$_GET['sbi_expires_in'];
		$expires_timestamp = time() + $expires_in;
		$account_type = isset($_GET['sbi_account_type']) ? sanitize_text_field($_GET['sbi_account_type']) : 'personal';
		$connect_type = isset($_GET['sbi_connect_type']) ? sanitize_text_field($_GET['sbi_connect_type']) : 'personal';

		$source_data = array(
			'access_token' => $access_token,
			'id' => $user_id,
			'user_id' => $user_id,
			'type' => 'basic',
			'username' => $user_name,
			'privilege' => '',
			'expires' => date('Y-m-d H:i:s', $expires_timestamp),
			'connect_type' => $connect_type,
		);

		$connection = new SB_Instagram_API_Connect($source_data, 'header', array());
		$connection->connect();
		$header_details = '{}';
		$source_data['error'] = '';
		if (!$connection->is_wp_error() && !$connection->is_instagram_error()) {
			$header_details_array = $connection->get_data();

			$header_details_array = self::merge_account_details($header_details_array, $source_data);
			$source_data['username'] = $header_details_array['username'];
			$header_details_array['connect_type'] = $connect_type;
			$header_details = sbi_json_encode($header_details_array);
		} else {
			$source_data['error'] = $connection;
			if ($connection->is_wp_error()) {
				$page_error = $connection->get_wp_error();
				if (!empty($page_error) && isset($page_error['response']->errors)) {
					$error_message = '';
					foreach ($page_error['response']->errors as $key => $item) {
						$error_message .= $key . ': ' . $item[0] . ' ';
					}

					return array(
						'error' => array(
							'code' => 'HTTP Request',
							'message' => $error_message,
							'details' => $error_message,
						),
					);
				}
			} else {
				$error = $connection->get_data();
				return array(
					'error' => array(
						'code' => $error['error']['code'],
						'message' => $error['error']['message'],
						'details' => $error['error']['message'],
					),
				);
			}
		}

		$source_data['info'] = $header_details;
		$return['unconnectedAccounts'][] = $source_data;

		$args = array(
			'username' => $source_data['username'],
		);
		$results = SBI_Db::source_query($args);
		$already_connected_as_business_account = isset($results[0]) && $results[0]['account_type'] === 'business';
		$matches_existing_personal = isset($results[0]) && $results[0]['account_type'] !== 'business';

		if ($already_connected_as_business_account) {
			$return['matchingExistingAccounts'] = $results[0];
			$instagram_account_data = json_decode($encryption->decrypt($results[0]['info']), true);
			$return['matchingExistingAccounts']['avatar'] = isset($instagram_account_data['profile_picture_url']) ? $instagram_account_data['profile_picture_url'] : false;

			$return['notice'] = __('The Instagram account you are logged into is already connected as a "business" account. Remove the business account if you\'d like to connect as a basic account instead (not recommended).', 'instagram-feed');
		} else {
			if ($matches_existing_personal) {
				$return['matchingExistingAccounts'] = $results[0];
				SBI_Db::delete_source($results[0]['id']);
			}

			$update_result = self::update_or_insert($source_data);
			if ($update_result === false) {
				return array(
					'error' => array(
						'code' => 'Database Error',
						'message' => __('Failed to update or insert the source data.', 'instagram-feed'),
						'details' => __('Failed to update or insert the source data.', 'instagram-feed'),
					),
				);
			}

			$return['didQuickUpdate'] = true;
			$return['notice'] = isset($return['notice']) ? $return['notice'] : '';
		}

		$personal_accounts = SBI_Db::source_query(array('type' => 'basic'));
		if (empty($personal_accounts)) {
			global $sbi_notices;
			$personal_api_notice = $sbi_notices->get_notice('personal_api_deprecation');
			if (!empty($personal_api_notice)) {
				$sbi_notices->remove_notice('personal_api_deprecation');
			}
		}

		return $return;
	}

	/**
	 * Creates a queue of connected accounts that need to be added to
	 * the sources table
	 *
	 * @since 6.0
	 */
	public static function set_legacy_source_queue()
	{
		$sbi_statuses_option = get_option('sbi_statuses', array());
		$options = get_option('sb_instagram_settings', array());

		$connected_accounts = isset($options['connected_accounts']) ? $options['connected_accounts'] : array();

		$sbi_statuses_option['legacy_source_queue'] = array_chunk(array_keys($connected_accounts), self::BATCH_SIZE);

		update_option('sbi_statuses', $sbi_statuses_option);

		return $sbi_statuses_option['legacy_source_queue'];
	}

	/**
	 * Processes one set of connected accounts
	 *
	 * @since 6.0
	 */
	public static function batch_process_legacy_source_queue()
	{
		if (!self::should_do_source_updates()) {
			return;
		}

		$sbi_statuses_option = get_option('sbi_statuses', array());
		$batch = array_shift($sbi_statuses_option['legacy_source_queue']);
		update_option('sbi_statuses', $sbi_statuses_option); // updated early just in case there is a fatal error

		if (empty($batch)) {
			return;
		}
		$options = get_option('sb_instagram_settings', array());

		$connected_accounts = isset($options['connected_accounts']) ? $options['connected_accounts'] : array();

		foreach ($batch as $account_key) {
			$connected_account = isset($connected_accounts[$account_key]) ? $connected_accounts[$account_key] : false;

			if ($connected_account) {
				self::update_single_source($connected_account);
			}
		}

		return $sbi_statuses_option['legacy_source_queue'];
	}

	/**
	 * Whether or not there are still sources in the queue and
	 * this isn't disabled
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function should_do_source_updates()
	{
		$sbi_statuses_option = get_option('sbi_statuses', array());

		$should_do_source_updates = isset($sbi_statuses_option['legacy_source_queue']) ? !empty($sbi_statuses_option['legacy_source_queue']) : false;

		return apply_filters('should_do_source_updates', $should_do_source_updates);
	}

	/**
	 * Creates a source from the access token and
	 * source ID saved in 3.x settings
	 *
	 * @since 6.0
	 */
	public static function update_source_from_legacy_settings()
	{
		// not needed
	}

	/**
	 * If the plugin is still updating legacy sources this function
	 * can be used to udpate a single source if needed before
	 * the update is done.
	 *
	 * @param string $slug_or_id
	 *
	 * @return array|bool
	 */
	public static function maybe_one_off_connected_account_update($slug_or_id)
	{
		if (!self::should_do_source_updates()) {
			return false;
		}

		$connected_accounts = (array)json_decode(stripcslashes(get_option('sbi_connected_accounts')), true);
		$connected_account = isset($connected_accounts[$slug_or_id]) ? $connected_accounts[$slug_or_id] : false;

		if ($connected_account) {
			return self::update_single_source($connected_account);
		}

		return false;
	}

	/**
	 * Clears the "error" column in the sbi_sources table for a specific
	 * account
	 *
	 * @param string $account_id
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function clear_error($account_id)
	{
		$source_data = array(
			'id' => $account_id,
			'error' => '',
		);
		return self::update_or_insert($source_data);
	}

	/**
	 * Adds an error to the error table by account ID
	 *
	 * @param string              $account_id
	 * @param string|object|array $error
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function add_error($account_id, $error)
	{
		$source_data = array(
			'id' => $account_id,
			'error' => is_string($error) ? $error : sbi_json_encode($error),
		);
		return self::update_or_insert($source_data);
	}

	/**
	 * Uses query results from the sbi_sources table to convert them
	 * into connected account data and return them as a connected account
	 * array as would be used in versions 5.x and below
	 *
	 * @param array $source_data
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function convert_sources_to_connected_accounts($source_data)
	{
		$encryption = new SB_Instagram_Data_Encryption();

		$connected_accounts = array();

		foreach ($source_data as $source_datum) {
			$info = !empty($source_datum['info']) ? json_decode($encryption->decrypt($source_datum['info']), true) : array();
			$settings = array('gdpr' => 'no');
			$avatar = SB_Instagram_Parse::get_avatar($info, $settings, true);
			$connected_account = array(
				'id' => $source_datum['account_id'],
				'user_id' => $source_datum['account_id'],
				'type' => $source_datum['account_type'],
				'account_type' => $source_datum['account_type'],
				'username' => $source_datum['username'],
				'access_token' => sbi_maybe_clean($source_datum['access_token']),
				'privilege' => $source_datum['privilege'],
				'expires_timestamp' => Util::get_valid_timestamp($source_datum['expires']),
				'is_valid' => empty($source_datum['error']),
				'profile_picture' => $avatar,
				'last_checked' => isset($source_datum['last_updated']) ? strtotime($source_datum['last_updated']) : time(),
				'connect_type' => isset($source_datum['connect_type']) ? $source_datum['connect_type'] : 'personal',
			);
			if (!empty($info['private'])) {
				$connected_account['private'] = $info['private'];
			}

			if (!empty($info['biography'])) {
				$connected_account['bio'] = $info['biography'];
			}

			$connected_account['local_avatar_url'] = SB_Instagram_Connected_Account::maybe_local_avatar($source_datum['username'], $avatar);

			$connected_accounts[$source_datum['account_id']] = $connected_account;
		}

		return $connected_accounts;
	}

	/**
	 * Returns a batch of accounts that have expiring access tokens
	 *
	 * @return array|bool
	 *
	 * @since 6.0
	 */
	public static function get_expiring()
	{
		$args = array('expiring' => true);
		return SBI_Db::source_query($args);
	}

	/**
	 * Updates Personal Account Bio
	 *
	 * @return array|bool
	 *
	 * @since 6.0.8
	 */
	public static function update_personal_account_bio($account_id, $bio)
	{
		$source = SBI_Db::get_source_by_account_id($account_id);
		if (isset($source['info'])) {
			$encryption = new SB_Instagram_Data_Encryption();
			$info = json_decode($encryption->maybe_decrypt($source['info']), true);
			$info = array('biography' => $bio) + $info;
			$to_update = array();
			$to_update['info'] = json_encode($info);
			SBI_Db::source_update($to_update, array('id' => $account_id));
		}
	}
}
