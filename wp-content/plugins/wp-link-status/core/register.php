<?php

/**
 * Register class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Register {



	/**
	 * Plugin activation process
	 */
	public static function activation() {

		// Check salt file
		WPLNST_Core_Nonce::check_salt_file();

		// Load scheme library
		wplnst_require('core', 'scheme');

		// Check plugins tables
		if (false !== ($tables = WPLNST_Core_Scheme::check_tables())) {
			WPLNST_Core_Scheme::create_tables($tables);
		}

		// Check scheme upgrade
		WPLNST_Core_Scheme::upgrade();
	}



	/**
	 * Plugin deactivation process
	 */
	public static function deactivation() {
		WPLNST_Core_Settings::delete_crawler_options();
	}



	/**
	 * Uninstall plugin data
	 */
	public static function uninstall() {

		// Uninstall info first
		if (!wplnst_get_bsetting('uninstall_data')) {
			return;
		}

		// Remove salt file
		WPLNST_Core_Nonce::remove_salt_file();

		// Remove user meta
		$user_id = get_current_user_id();
		delete_user_meta($user_id, 'wplnst_advanced_search');
		delete_user_meta($user_id, 'wplnst_scans_per_page');
		delete_user_meta($user_id, 'wplnst_scan_results_per_page');

		// Remove plugin options
		WPLNST_Core_Settings::delete_all_options();

		// Remove scheme tables
		wplnst_require('core', 'scheme');
		WPLNST_Core_Scheme::drop_tables();
	}



}