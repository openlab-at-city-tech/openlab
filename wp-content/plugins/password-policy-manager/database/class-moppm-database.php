<?php
/**
 * File containing database functions
 *
 * @package password-policy-manager/database
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MOPPM_DATABASE' ) ) {
	/**
	 * Class for database related functions
	 */
	class MOPPM_DATABASE {
		/**
		 * Variable to hold moppm_user_report_table database table
		 *
		 * @var string
		 */
		private $report_table;
		/**
		 * Constructor function
		 */
		public function __construct() {
			global $wpdb;
			$this->report_table = $wpdb->base_prefix . 'moppm_user_report_table';
		}

		/**
		 * This function runs plugin activation
		 *
		 * @return void
		 */
		public function plugin_activate() {
			add_site_option( 'moppm_activated_time', time() );
			add_site_option( 'Moppm_enable_disable_ppm', 'on' );
			global $wpdb;
			if ( ! get_site_option( 'moppm_dbversion' ) ) {
				update_site_option( 'moppm_dbversion', MOPPM_Constants::DB_VERSION );
				$this->generate_tables();
			} else {
				$current_db_version = get_site_option( 'moppm_dbversion' );
				if ( $current_db_version < MOPPM_Constants::DB_VERSION ) {

					update_site_option( 'moppm_dbversion', MOPPM_Constants::DB_VERSION );
					$this->generate_tables();
				}
			}
		}

		/**
		 * Function to generate tables
		 *
		 * @return void
		 */
		public function generate_tables() {
			global $wpdb;
			$tablename = $this->report_table;
			if ( $wpdb->get_var( $wpdb->prepare( 'show tables like %s', array( $tablename ) ) ) !== $tablename ) { //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder -- complex placeholder is required for database tablename, wpdb required here and catching is not required here.
				$sql = 'CREATE TABLE ' . $tablename . ' (`id` int NOT NULL AUTO_INCREMENT, `user_email` mediumtext NOT NULL, `Login_time` mediumtext,`Logout_time` mediumtext, UNIQUE KEY id (id) );'; //phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange -- Creating a database table 
				dbDelta( $sql );
			}

		}

		/**
		 * Function to get whole report list
		 *
		 * @return string
		 */
		public function moppm_get_report_list() {
			global $wpdb;
			$tablename = $this->report_table;
			return $wpdb->get_results( $wpdb->prepare( 'SELECT id,user_email,Login_time,Logout_time FROM %1s', array( $tablename ) ) );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder -- complex placeholder is required for database tablename, wpdb required here and catching is not required here.
		}

		/**
		 * Function to insert entry into report list table
		 *
		 * @param int    $user_id id of user.
		 * @param string $email email of user.
		 * @param string $log_time login time.
		 * @param string $log_out_time password change time.
		 * @return void
		 */
		public function insert_report_list( $user_id, $email, $log_time, $log_out_time ) {
			global $wpdb;
			$table_name = $this->report_table;
			$wpdb->query( $wpdb->prepare( 'INSERT INTO %1s (id,user_email,Login_time,Logout_time) VALUES(%d,%s,%s,%s) ON DUPLICATE KEY UPDATE Login_time=%s', array( $table_name, $user_id, $email, $log_time, $log_out_time, $log_time ) ) );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder -- complex placeholder is required for database tablename, wpdb required here and catching is not required here.
		}

		/**
		 * Function to delete one row from table
		 *
		 * @param int $user_id user id of user.
		 * @return void
		 */
		public function delete_report_list( $user_id ) {
			global $wpdb;
			$table_name = $this->report_table;
			$wpdb->query( $wpdb->prepare( 'DELETE FROM %1s WHERE id = %d', array( $table_name, $user_id ) ) );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder -- complex placeholder is required for database tablename, wpdb required here and catching is not required here.
		}

		/**
		 * Function to delete all entries from report list table
		 *
		 * @return void
		 */
		public function clear_report_list() {
			global $wpdb;
			$table_name = $this->report_table;
			$wpdb->query( $wpdb->prepare( 'DELETE FROM %1s', array( $table_name ) ) );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder -- complex placeholder is required for database tablename, wpdb required here and catching is not required here.
		}

		/**
		 * Function to update password change time
		 *
		 * @param int    $user_id user id of user.
		 * @param string $log_out_time password change time of user.
		 * @return void
		 */
		public function update_report_list( $user_id, $log_out_time ) {
			global $wpdb;
			$table_name = $this->report_table;
			$wpdb->query( $wpdb->prepare( 'UPDATE %1s SET Logout_time= %s  WHERE id = %d ', array( $table_name, $log_out_time, $user_id ) ) );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder -- complex placeholder is required for database tablename, wpdb required here and catching is not required here.
		}

	}
}
