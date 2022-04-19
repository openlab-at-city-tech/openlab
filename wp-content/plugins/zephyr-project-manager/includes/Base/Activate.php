<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;
if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
use Inc\Core\Projects;
use Inc\Core\Utillities;

class Activate {

	public static function activate(){
		global $wpdb;
		flush_rewrite_rules();
		Activate::installTables();
	}

	public static function installTables() {
		global $wpdb;
		$projects_table = $wpdb->prefix . 'zpm_projects';
		$tasks_table = $wpdb->prefix . 'zpm_tasks';
		$categories_table = $wpdb->prefix . 'zpm_categories';
		$activity_table = $wpdb->prefix . 'zpm_activity';
		$messages_table = $wpdb->prefix . 'zpm_messages';

		Activate::installTasksTable();
		Projects::createProjectsTable();

		$table_name = $categories_table;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  name text NOT NULL,
			  description text NOT NULL,
			  color text NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = $messages_table;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			subject VARCHAR(255) NOT NULL,
			subject_id mediumint(9) NOT NULL,
			parent_id mediumint(9) NOT NULL,
			message text NOT NULL,
			type VARCHAR(255) NOT NULL,
			date_created TIMESTAMP NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$table_name = $activity_table;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				subject_id mediumint(9) NOT NULL,
				subject_name text NOT NULL,
				old_name text NOT NULL,
				subject text NOT NULL,
				action text NOT NULL,
				date_done TIMESTAMP NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = $tasks_table;
		if (!Utillities::table_column_exists( $table_name, 'other_data' )) {
			$wpdb->query("ALTER TABLE $table_name ADD other_data TEXT");
		}

		Activate::zephyr_check_new_tables();
		$version = Zephyr::getPluginVersion();
		update_option( 'zpm_database_version', $version );
	}

	public static function zephyr_check_new_tables() {
		global $wpdb;
		$projects_table = $wpdb->prefix . 'zpm_projects';
		$tasks_table = $wpdb->prefix . 'zpm_tasks';
		$categories_table = $wpdb->prefix . 'zpm_categories';
		$activity_table = $wpdb->prefix . 'zpm_activity';
		$messages_table = $wpdb->prefix . 'zpm_messages';

		$table_name = $tasks_table;

		if (!Utillities::table_column_exists($table_name, 'priority')) {
			$wpdb->query("ALTER TABLE $table_name ADD priority varchar(255)");
		}

		if (!Utillities::table_column_exists($table_name, 'status')) {
			$wpdb->query("ALTER TABLE $table_name ADD status varchar(255)");
		}

		if (!Utillities::table_column_exists($table_name, 'team')) {
			$wpdb->query("ALTER TABLE $table_name ADD team TEXT NOT NULL");
		}

		if (!Utillities::table_column_exists( $table_name, 'other_data' )) {
			$wpdb->query("ALTER TABLE $table_name ADD other_data TEXT");
		}

		$query = "SHOW FIELDS FROM {$table_name}";
		$values = $wpdb->get_results( $query );

		foreach ($values as $value) {
			if ($value->Field == 'assignee') {
				//echo 'I am assignee field!1';
				if ( strtolower( $value->Type ) !== 'varchar(255)' ) {
					$wpdb->query("ALTER TABLE $table_name MODIFY assignee VARCHAR(255) NOT NULL");
				}
			}
		}

		$table_name = $projects_table;
		if (!Utillities::table_column_exists($table_name, 'priority')) {
			$wpdb->query("ALTER TABLE $table_name ADD priority varchar(255)");
		}

		$query = "SHOW FIELDS FROM {$table_name}";
		$values = $wpdb->get_results( $query );

		foreach ($values as $value) {
			if ($value->Field == 'team') {
				if ( strtolower( $value->Type ) !== 'text' ) {
					$wpdb->query("ALTER TABLE $table_name MODIFY team TEXT NOT NULL");
				}
			}
		}
	}

	public static function installTasksTable() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'zpm_tasks';
		$columnSql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
			parent_id mediumint(9) NOT NULL DEFAULT '-1',
			user_id mediumint(9) NOT NULL,
			project mediumint(9) NOT NULL,
			assignee varchar(255) NOT NULL,
			name text NOT NULL,
			description text NOT NULL,
			categories varchar(100) NOT NULL,
			completed boolean NOT NULL,
			date_created TIMESTAMP NOT NULL,
			date_start TIMESTAMP NOT NULL,
			date_due TIMESTAMP NOT NULL,
			date_completed TIMESTAMP NOT NULL,
			priority varchar(255),
			status varchar(255),
			other_data TEXT,
			team TEXT NOT NULL,
			type varchar(255) DEFAULT 'normal',
			archived BOOLEAN DEFAULT 0";

		$columnSql = apply_filters( 'zpm_task_table_columns_sql', $columnSql );

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			$columnSql,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}