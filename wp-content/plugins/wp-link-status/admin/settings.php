<?php

/**
 * Admin Settings class
 *
 * @package WP Link Status
 * @subpackage Admin
 */
class WPLNST_Admin_Settings {



	/**
	 * Constructor
	 */
	public function __construct(&$admin) {

		// Initialize
		$notice_success = $notice_error = false;

		// Check submit
		if (isset($_POST['settings_nonce'])) {

			// Check nonce
			if (!wp_verify_nonce($_POST['settings_nonce'], WPLNST_Core_Plugin::slug.'-settings')) {
				return $admin->screen_invalid_nonce(WPLNST_Admin::get_text('settings'));
			}

			// General update
			update_option('wplnst_max_threads', 		(int) $_POST['tx-max-threads']);
			update_option('wplnst_max_scans', 			(int) $_POST['tx-max-scans']);
			update_option('wplnst_max_pack',			(int) $_POST['tx-max-pack']);
			update_option('wplnst_max_requests', 		(int) $_POST['tx-max-requests']);
			update_option('wplnst_max_redirs', 			(int) $_POST['tx-max-redirs']);
			update_option('wplnst_max_download', 		(int) $_POST['tx-max-download']);
			update_option('wplnst_user_agent', 			stripslashes($_POST['tx-user-agent']));

			// Timeouts update
			update_option('wplnst_connect_timeout', 	(int) $_POST['tx-connect-timeout']);
			update_option('wplnst_request_timeout', 	(int) $_POST['tx-request-timeout']);
			update_option('wplnst_extra_timeout', 		(int) $_POST['tx-extra-timeout']);
			update_option('wplnst_crawler_alive', 		(int) $_POST['tx-crawler-alive']);
			update_option('wplnst_total_objects', 		(int) $_POST['tx-total-objects']);
			update_option('wplnst_summary_status', 		(int) $_POST['tx-summary-status']);
			update_option('wplnst_summary_phases', 		(int) $_POST['tx-summary-phases']);
			update_option('wplnst_summary_objects', 	(int) $_POST['tx-summary-objects']);

			// Advanced update
			update_option('wplnst_recursion_limit', 	(int) $_POST['tx-recursion-limit']);
			update_option('wplnst_mysql_calc_rows', 	empty($_POST['ck-mysql-calc-rows'])? 'off' : 'on');
			update_option('wplnst_uninstall_data', 		empty($_POST['ck-uninstall-data'])?  'off' : 'on');

			// Update notice
			$notice_success = __('Settings updated', 'wplnst');
		}

		// Custom action view
		add_action('wplnst_view_settings', array(&$this, 'view_settings'));

		// Show settings screen
		$admin->screen_view(array(
			'title' 			=> WPLNST_Admin::get_text('settings'),
			'wp_action'			=> 'wplnst_view_settings',
			'action'			=> WPLNST_Core_Plugin::get_url_settings(),
			'nonce'				=> wp_create_nonce(WPLNST_Core_Plugin::slug.'-settings'),
			'notice_success' 	=> $notice_success,
			'notice_error' 		=> $notice_error,
		));
	}



	/**
	 * Extension view for settings page
	 */
	public function view_settings($args) {
		wplnst_require('views', 'settings');
		WPLNST_Views_Settings::view($args);
	}



}