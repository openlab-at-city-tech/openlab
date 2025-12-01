<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName

/**
 * Editoria11y Accessibility Checker
 *
 * Plugin Name:       Editoria11y Accessibility Checker
 * Plugin URI:        https://wordpress.org/plugins/editoria11y-accessibility-checker/
 * Version:           2.1.7
 * Requires PHP:      7.2
 * Requires at least: 6.0
 * Tested up to:      6.9
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
 * @copyright       2025 The Trustees of Princeton University
 * @license         GPL v2 or later
 */

/**
 * Class Editoria11y
 *
 * @package Editoria11y
 */
class Editoria11y {
	// Library version; used as cache buster.
	const ED11Y_VERSION = '2.1.7';

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
	public static function create_database(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_urls       = $wpdb->prefix . 'ed11y_urls';
		$table_results    = $wpdb->prefix . 'ed11y_results';
		$table_dismissals = $wpdb->prefix . 'ed11y_dismissals';

		// Initial table creation

		$sql_urls = "CREATE TABLE $table_urls (
			pid int(9) unsigned AUTO_INCREMENT NOT NULL,
			post_id int(9) unsigned NOT NULL default '0',
			page_url varchar(190) NOT NULL,
			entity_type varchar(255) NOT NULL,
			page_title varchar(1024) NOT NULL,
			page_total smallint(4) unsigned NOT NULL,
			PRIMARY KEY pid (pid),
			KEY page_url (page_url),
			KEY post_id (post_id)
			) $charset_collate;";

		$sql_results = "CREATE TABLE $table_results (
			pid int(9) unsigned NOT NULL,
			result_key varchar(32) NOT NULL,
			result_count smallint(4) NOT NULL,
			created datetime DEFAULT current_timestamp NOT NULL,
			updated datetime DEFAULT current_timestamp NOT NULL,
			PRIMARY KEY (pid, result_key),
			FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE
			) $charset_collate;";

		$sql_dismissals = "CREATE TABLE $table_dismissals (
			id int(9) unsigned AUTO_INCREMENT NOT NULL,
			pid int(9) unsigned NOT NULL,
			result_key varchar(32) NOT NULL,
			user smallint(6) unsigned NOT NULL,
			element_id varchar(2048)  NOT NULL,
			dismissal_status varchar(64) NOT NULL,
			created datetime DEFAULT current_timestamp NOT NULL,
			updated datetime DEFAULT current_timestamp NOT NULL,
			stale tinyint(1) NOT NULL default '0',
			PRIMARY KEY (id),
			KEY page_url (pid),
			KEY user (user),
			KEY dismissal_status (dismissal_status),
			FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE
			) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		maybe_create_table( $table_urls, $sql_urls );
		maybe_create_table( $table_results, $sql_results );
		maybe_create_table( $table_dismissals, $sql_dismissals );

		// Add post_id column for db versions < 1.0
		$url_columns = $wpdb->get_results( "DESC $table_urls" );
		if( count($url_columns) !== 6) {
			$wpdb->query("ALTER TABLE $table_urls
	        ADD post_id int(9) unsigned NOT NULL default 0,
	        DROP PRIMARY KEY, ADD PRIMARY KEY pid ( pid ),
	        ADD KEY post_id (post_id)
	      ;");
		}

		// Add foreign keys not reliably handled by maybe_create_table
		$results_create_table_sql_row = $wpdb->get_row( "SHOW CREATE TABLE $table_results" );
		if ( $results_create_table_sql_row ) {
			$results_create_table_sql = $results_create_table_sql_row->{'Create Table'};
			$results_constraint       = $wpdb->prefix . 'ed11y_results_pid';

			$result_constraint_matches = preg_match( '/CONSTRAINT `(.+?)` FOREIGN KEY \(`pid`\)/', $results_create_table_sql, $result_matches );

			$result_foreign_key = null;
			if ( $result_constraint_matches ) {
				$result_foreign_key = $result_matches[1];
			}

			if ( $result_foreign_key ) {
				try {
					// MySQL syntax
					$wpdb->get_var( // phpcs:ignore
						$wpdb->prepare( "ALTER TABLE $table_results
					DROP FOREIGN KEY %1s;", array( $result_foreign_key ) )
					);
				} catch (Exception $e) {
					// MariaDB syntax
					$wpdb->get_var( // phpcs:ignore
						$wpdb->prepare( "ALTER TABLE $table_results
					DROP CONSTRAINT %1s;", array( $result_foreign_key ) )
					);
				} finally {
					// Add replacement constraint
					$wpdb->get_var( // phpcs:ignore
						$wpdb->prepare( "ALTER TABLE $table_results
					ADD CONSTRAINT %1s FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE", $results_constraint)
					);
				}
			} else {
				// Add new constraint
				$wpdb->get_var( // phpcs:ignore
					$wpdb->prepare( "ALTER TABLE $table_results
					ADD CONSTRAINT %1s FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE", $results_constraint)
				);
			}
		}

		$dismissal_create_table_sql_row = $wpdb->get_row( "SHOW CREATE TABLE $table_dismissals" );
		if ( $dismissal_create_table_sql_row ) {
			$dismissal_create_table_sql = $dismissal_create_table_sql_row->{'Create Table'};
			$dismissal_constraint       = $wpdb->prefix . 'ed11y_dismissal_pid';

			$dissmissal_constraint_matches = preg_match( '/CONSTRAINT `(.+?)` FOREIGN KEY \(`pid`\)/', $dismissal_create_table_sql, $dismissal_matches );

			$dismissal_key = null;
			if ( $dissmissal_constraint_matches ) {
				$dismissal_key = $dismissal_matches[1];
			}

			if ( $dismissal_key ) {
				try {
					// MySQL syntax
					$wpdb->get_var( // phpcs:ignore
						$wpdb->prepare( "ALTER TABLE $table_dismissals
						DROP FOREIGN KEY %1s;", array( $dismissal_key ) )
					);
				} catch (Exception $e) {
					// MariaDB syntax
					$wpdb->get_var( // phpcs:ignore
						$wpdb->prepare( "ALTER TABLE $table_dismissals
						DROP CONSTRAINT %1s;", array( $dismissal_key ) )
					);
				} finally {
					// Add new constraint
					$wpdb->get_var( // phpcs:ignore
						$wpdb->prepare( "ALTER TABLE $table_dismissals
						ADD CONSTRAINT %1s FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE", $dismissal_constraint)
					);
				}
			} else {
				$wpdb->get_var( // phpcs:ignore
					$wpdb->prepare( "ALTER TABLE $table_dismissals
						ADD CONSTRAINT %1s FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE", $dismissal_constraint)
				);
			}
		}
	}

	/**
	 * Make sure tables are in place and up to date.
	 */
	public static function check_tables(): bool {
		// Lazy DB creation

		$tableCheck = get_option( "editoria11y_db_version" );

		if ( '-failed' === substr( $tableCheck, -7 ) ) {
			// Tables are broken, don't try again until next release
			return false;
		}

		if ( version_compare( $tableCheck, '1.2', '>=' ) ) {
			// Tables are up to date
			return true;
		}

		// Create DB and set option based on success
		update_option( 'editoria11y_db_version', '1.2-failed' );
		self::create_database();
		update_option( 'editoria11y_db_version', '1.2' );

		return true;
	}

	/**
	 * Plugin Activation
	 */
	public static function activate() {
		// No action needed.
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
		delete_option( 'editoria11y_db_version' );
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
