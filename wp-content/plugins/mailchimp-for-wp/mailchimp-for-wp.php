<?php
/*
Plugin Name: MC4WP: Mailchimp for WordPress
Plugin URI: https://www.mc4wp.com/#utm_source=wp-plugin&utm_medium=mailchimp-for-wp&utm_campaign=plugins-page
Description: Mailchimp for WordPress by ibericode. Adds various highly effective sign-up methods to your site.
Version: 4.8.10
Author: ibericode
Author URI: https://ibericode.com/
Text Domain: mailchimp-for-wp
Domain Path: /languages
License: GPL v3

Mailchimp for WordPress
Copyright (C) 2012-2022, Danny van Kooten, hi@dannyvankooten.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Prevent direct file access
defined( 'ABSPATH' ) or exit;

/** @ignore */
function _mc4wp_load_plugin() {
	 global $mc4wp;

	// don't run if Mailchimp for WP Pro 2.x is activated
	if ( defined( 'MC4WP_VERSION' ) ) {
		return;
	}

	// don't run if PHP version is lower than 5.3
	if ( ! function_exists( 'array_replace' ) ) {
		return;
	}

	// bootstrap the core plugin
	define( 'MC4WP_VERSION', '4.8.10' );
	define( 'MC4WP_PLUGIN_DIR', __DIR__ );
	define( 'MC4WP_PLUGIN_FILE', __FILE__ );

	// load autoloader if function not yet exists (for compat with sitewide autoloader)
	if ( ! function_exists( 'mc4wp' ) ) {
		require_once MC4WP_PLUGIN_DIR . '/vendor/autoload.php';
	}

	require MC4WP_PLUGIN_DIR . '/includes/default-actions.php';
	require MC4WP_PLUGIN_DIR . '/includes/default-filters.php';

	// require API class manually because Composer's classloader is case-sensitive
	// but we need it to pass class_exists condition
	require MC4WP_PLUGIN_DIR . '/includes/api/class-api-v3.php';

	/**
	 * @global MC4WP_Container $GLOBALS['mc4wp']
	 * @name $mc4wp
	 */
	$mc4wp = mc4wp();
	$mc4wp['api'] = 'mc4wp_get_api_v3';
	$mc4wp['log'] = 'mc4wp_get_debug_log';

	// forms
	$mc4wp['forms'] = new MC4WP_Form_Manager();
	$mc4wp['forms']->add_hooks();

	// integration core
	$mc4wp['integrations'] = new MC4WP_Integration_Manager();
	$mc4wp['integrations']->add_hooks();

	// Doing cron? Load Usage Tracking class.
	if ( isset( $_GET['doing_wp_cron'] ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		MC4WP_Usage_Tracking::instance()->add_hooks();
	}

	// Initialize admin section of plugin
	if ( is_admin() ) {
		$admin_tools = new MC4WP_Admin_Tools();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$ajax = new MC4WP_Admin_Ajax( $admin_tools );
			$ajax->add_hooks();
		} else {
			$messages = new MC4WP_Admin_Messages();
			$mc4wp['admin.messages'] = $messages;

			$admin = new MC4WP_Admin( $admin_tools, $messages );
			$admin->add_hooks();

			$forms_admin = new MC4WP_Forms_Admin( $messages );
			$forms_admin->add_hooks();

			$integrations_admin = new MC4WP_Integration_Admin( $mc4wp['integrations'], $messages );
			$integrations_admin->add_hooks();
		}
	}
}

function _mc4wp_on_plugin_activation() {
	// schedule the action hook to refresh the stored Mailchimp lists on a daily basis
	$time_string = sprintf( 'tomorrow %d:%d%d am', rand( 0, 7 ), rand( 0, 5 ), rand( 0, 9 ) );
	wp_schedule_event( strtotime( $time_string ), 'daily', 'mc4wp_refresh_mailchimp_lists' );
}

// bootstrap custom integrations
function _mc4wp_bootstrap_integrations() {
	require_once MC4WP_PLUGIN_DIR . '/integrations/bootstrap.php';
}

add_action( 'plugins_loaded', '_mc4wp_load_plugin', 8 );
add_action( 'plugins_loaded', '_mc4wp_bootstrap_integrations', 90 );
register_activation_hook( __FILE__, '_mc4wp_on_plugin_activation' );
