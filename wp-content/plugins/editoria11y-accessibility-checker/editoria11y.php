<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName

/**
 * Editoria11y Accessibility Checker
 *
 * Plugin Name:       Editoria11y Accessibility Checker
 * Plugin URI:        https://wordpress.org/plugins/editoria11y-accessibility-checker/
 * Version:           1.0.10
 * Requires PHP:      7.2
 * Requires at least: 6.0
 * Tested up to:      6.2
 * Author:            Princeton University, WDS
 * Author URI:        https://wds.princeton.edu/team
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       editoria11y
 * Domain Path:       /languages
 * Description:       User friendly content quality assurance. Checks automatically, highlights issues inline, and provides straightforward, easy-to-understand tips.
 *
 * @package         Editoria11y
 * @link            https://wordpress.org/plugins/editoria11y-accessibility-checker/
 * @author          John Jameson, Princeton University
 * @copyright       2023 The Trustees of Princeton University
 * @license         GPL v2 or later
 */

/**
 * Class Editoria11y
 *
 * @package Editoria11y
 */
class Editoria11y {
	const ED11Y_VERSION = '1.0.10';

	/**
	 * Attachs functions to loop.
	 */
	public function __construct() {

		// Set the constants needed by the plugin.
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		// Internationalize the text strings used. (Todo).
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		// Load the functions files.
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

		// Load the admin files.
		add_action( 'plugins_loaded', array( &$this, 'admin' ), 4 );

		// Load the API.
		add_action( 'plugins_loaded', array( &$this, 'api' ), 5 );

	}

	/**
	 * Defines file locations.
	 */
	public function constants() {
		global $wpdb;

		define( 'ED11Y_BASE', plugin_basename( __FILE__ ) );

		// Set constant path to the plugin directory.
		define( 'ED11Y_SRC', trailingslashit( plugin_dir_path( __FILE__ ) . 'src/' ) );

		// Set the constant path to the assets directory.
		define( 'ED11Y_ASSETS', trailingslashit( plugin_dir_url( __FILE__ ) . 'assets/' ) );

	}

	/**
	 * Loads translation files.
	 */
	public function i18n() {
		// Todo.
		load_plugin_textdomain( 'editoria11y', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Loads page functions.
	 */
	public function includes() {
		require_once ED11Y_SRC . 'functions.php';
	}

	/**
	 * Loads admin functions.
	 */
	public function admin() {
		if ( is_admin() ) {
			require_once ED11Y_SRC . 'functions.php';
			require_once ED11Y_SRC . 'admin.php';
		}
	}

	/**
	 * Creates API routes.
	 */
	public function api() {
		// Load the API.
		require_once ED11Y_SRC . 'controller/class-editoria11y-api-results.php';
		$ed11y_api_results = new Editoria11y_Api_Results();
		$ed11y_api_results->init();
		require_once ED11Y_SRC . 'controller/class-editoria11y-api-dismissals.php';
		$ed11y_api_dismissals = new Editoria11y_Api_Dismissals();
		$ed11y_api_dismissals->init();
	}

	/**
	 * Provides DB table schema.
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_urls       = $wpdb->prefix . 'ed11y_urls';
		$table_results    = $wpdb->prefix . 'ed11y_results';
		$table_dismissals = $wpdb->prefix . 'ed11y_dismissals';

		$sql_urls = "CREATE TABLE $table_urls (
			pid int(9) unsigned AUTO_INCREMENT NOT NULL,
			page_url varchar(225) NOT NULL,
			entity_type varchar(255) NOT NULL,
			page_title varchar(1024) NOT NULL,
			page_total smallint(4) unsigned NOT NULL,
			PRIMARY KEY page_url (page_url),
			KEY pid (pid)
			) $charset_collate;";

		$sql_results = "CREATE TABLE $table_results (
			pid int(9) unsigned NOT NULL,
			result_key varchar(32) NOT NULL,
			result_count smallint(4) NOT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			CONSTRAINT result PRIMARY KEY (pid, result_key),
			FOREIGN KEY (pid) REFERENCES $table_urls (pid) ON DELETE CASCADE
			) $charset_collate;";

		$sql_dismissals = "CREATE TABLE $table_dismissals (
			id int(9) unsigned AUTO_INCREMENT NOT NULL,
			pid int(9) unsigned NOT NULL,
			result_key varchar(32) NOT NULL,
			user smallint(6) unsigned NOT NULL,
			element_id varchar(2048)  NOT NULL,
			dismissal_status varchar(64) NOT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			stale tinyint(1) NOT NULL default '0',
			PRIMARY KEY (id),
			KEY page_url (pid),
			KEY user (user),
			KEY dismissal_status (dismissal_status),
			FOREIGN KEY (pid) REFERENCES $table_urls (pid) ON DELETE CASCADE
			) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		maybe_create_table( $table_urls, $sql_urls );
		maybe_create_table( $table_results, $sql_results );
		maybe_create_table( $table_dismissals, $sql_dismissals );
	}

	/**
	 * Remove DB tables on uninstall
	 */
	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		global $wpdb;

		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ed11y_dismissals" ); // phpcs:ignore
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ed11y_results" ); // phpcs:ignore
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ed11y_urls" ); // phpcs:ignore

		delete_option( 'ed11y_plugin_settings' );
		delete_site_option( 'ed11y_plugin_settings' );
		delete_site_transient( 'editoria11y_settings' );

	}

}

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Manage DB tables.
register_activation_hook( __FILE__, array( 'Editoria11y', 'activate' ) );

register_uninstall_hook( __FILE__, array( 'Editoria11y', 'uninstall' ) );

new Editoria11y();
