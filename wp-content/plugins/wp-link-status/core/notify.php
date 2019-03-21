<?php

/**
 * Notify class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Notify {



	/**
	 * Send a notification of scan completed
	 */
	public static function completed($scan) {

		// Get current notifications
		$notifications = self::get_notifications();
		if (!in_array($scan->id, $notifications)) {

			// Add scan to notifications
			$notifications[] = $scan->id;
			self::set_notifications($notifications);

			// Spawn admin ajax URL
			wplnst_require('core', 'curl');
			WPLNST_Core_CURL::spawn(array('CURLOPT_URL' => add_query_arg(array(
				'wplnst_notify_email' => 'on',
				'wplnst_notify_nonce' => WPLNST_Core_Alive::get_notify_nonce(),
			), rtrim(admin_url('admin-ajax.php'), '/'))));
		}
	}



	/**
	 * Check for pending notifications
	 */
	public static function check() {

		// Retrieve notifications
		$notifications = self::get_notifications();
		if (empty($notifications)) {
			return;
		}

		// Extract first element
		$scan_id = (int) array_shift($notifications);
		self::set_notifications($notifications);

		// Check identifier
		if (!$scan_id > 0) {
			return;
		}

		// Check scan
		global $wpdb;
		$scan_row = $wpdb->get_row($wpdb->prepare('SELECT name, config FROM '.$wpdb->prefix.'wplnst_scans WHERE scan_id = %d', $scan_id));
		if (empty($scan_row) || !is_object($scan_row)) {
			return;
		}

		// Extract configuration
		$config = @json_decode($scan_row->config, true);
		if (empty($config) || !is_array($config)) {
			return;
		}

		// Initialize
		$emails = array();

		// Default e-mail
		if (isset($config['notify_default']) && true === $config['notify_default']) {
			$admin_email = get_option('admin_email');
			if (!empty($admin_email) && is_email($admin_email)) {
				$emails[] = $admin_email;
			}
		}

		// New addresses
		if (isset($config['notify_address']) && true === $config['notify_address'] && !empty($config['notify_address_email'])) {
			$addresses = str_replace(',', ';', $config['notify_address_email']);
			$addresses = explode(';', $addresses);
			foreach ($addresses as $address) {
				if (!empty($address) && is_email($address)) {
					$emails[] = $address;
				}
			}
		}

		// Check collection
		if (empty($emails)) {
			return;
		}

		// Send
		$result = wp_mail(array_unique($emails), __('WP Link Status scan completed', 'wplnst'),
sprintf(__('

Your scan is completed, you can see the results here:

%s
%s

', 'wplnst'), empty($scan_row->name)? __('(no name)', 'wplnst') : $scan_row->name, WPLNST_Core_Plugin::get_url_scans_results($scan_id)));
	}



	/**
	 * Retrieve option with scans to notify
	 */
	private static function get_notifications() {
		$notifications = @json_decode(get_option('wplnst_notifications'), true);
		return (empty($notifications) || !is_array($notifications))? array() : $notifications;
	}



	/**
	 * Save notifications
	 */
	private static function set_notifications($notifications) {
		return update_option('wplnst_notifications', @json_encode($notifications));
	}



}