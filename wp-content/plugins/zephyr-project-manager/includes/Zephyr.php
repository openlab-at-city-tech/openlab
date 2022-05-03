<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Base\BaseController;

class Zephyr {

	public function __construct() {

	}

	public static function isPro() {
		if (class_exists('Inc\\ZephyrProjectManager\\Plugin')) {
			return true;
		}

		return false;
	}

	public static function getPluginData() {
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data( ZPM_PLUGIN_PATH . '/zephyr-project-manager.php' );
		return $plugin_data;
	}

	// Returns the data for the Pro Add On
	public static function getProPluginData() {

		if (!Zephyr::isPro()) {
			return false;
		}

		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( ZEPHYR_PRO_PLUGIN_PATH . '/zephyr-project-manager-pro.php' );
		return $plugin_data;
	}

	// Returns the version of the basic plugin
	public static function getPluginVersion() {
		$plugin_data = Zephyr::getPluginData();
		return $plugin_data['Version'];
	}

	// Returns the version of the pro add on
	public static function getProPluginVersion() {

		if (!Zephyr::isPro()) {
			return false;
		}

		$plugin_data = Zephyr::getProPluginData();
		return $plugin_data['Version'];
	}

	public static function proRequiredLabel() {
		ob_start();
		if (!Zephyr::isPro()) {
			?>
			<span class="zpm-pro-required-label"><?php _e( 'Pro', 'zephyr-project-manager' ); ?></span>
			<?php
		}
		return ob_get_clean();
	}

	public static function getSettingsPages() {
		$pages = [];
		$settingsPages = apply_filters( 'zpm_settings_sections', $pages );
		return $settingsPages;
	}

}