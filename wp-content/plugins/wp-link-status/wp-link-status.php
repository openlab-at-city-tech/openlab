<?php
/*
Plugin Name: WP Broken Link Status Checker
Plugin URI: https://seedplugins.com/wp-link-status/
Description: Check and manage HTTP response codes of all your content site links and images.
Version: 1.0.6
Author: Pau Iglesias, SeedPlugins
License: GPLv2 or later
Text Domain: wplnst
Domain Path: /languages
*/

// Avoid script calls via plugin URL
if (!function_exists('add_action')) {
	die;
}

// Boot checks
require dirname(__FILE__).'/core/boot.php';

// This plugin constants
define('WPLNST_FILE', __FILE__);
define('WPLNST_PATH', dirname(WPLNST_FILE));
define('WPLNST_VERSION', '1.0.6');

// Check scan crawling action
require_once WPLNST_PATH.'/core/alive.php';
WPLNST_Core_Alive::check();

// Check admin area
if (is_admin()) {
	wplnst_require('admin', 'admin');
	WPLNST_Admin::instantiate();
}

/**
 * Plugin activation hook
 */
register_activation_hook(WPLNST_FILE, 'wplnst_plugin_activation');
if (!function_exists('wplnst_plugin_activation')) {

	function wplnst_plugin_activation($networkwide = false) {

		// Prevent network-wide activation
		if (is_multisite() && $networkwide) {
			deactivate_plugins(plugin_basename(WPLNST_FILE));
			wp_die('<p><strong>WP Link Status</strong> cannot be activated network-wide.</p>
			<p>Please activate it invididually per each site where you need it.</p>
			<p>Sorry for the inconvenience.</p>');
		}

		// Continues activation
		wplnst_require('core', 'register');
		WPLNST_Core_Register::activation();
	}
}

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(WPLNST_FILE, 'wplnst_plugin_deactivation');
if (!function_exists('wplnst_plugin_deactivation')) {

	function wplnst_plugin_deactivation() {
		wplnst_require('core', 'register');
		WPLNST_Core_Register::deactivation();
	}
}

/**
 * Plugin uninstall hook
 */
register_uninstall_hook(WPLNST_FILE, 'wplnst_plugin_uninstall');
if (!function_exists('wplnst_plugin_uninstall')) {

	function wplnst_plugin_uninstall() {
		wplnst_require('core', 'register');
		WPLNST_Core_Register::uninstall();
	}
}