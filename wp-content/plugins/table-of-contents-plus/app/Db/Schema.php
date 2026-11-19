<?php
namespace AIOSEO\TableOfContents\Db;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database Schema for Table of Contents Plus tables.
 *
 * This class defines the complete, current state of all database tables
 * used by the Table of Contents Plus addon. These schemas are used with WordPress's
 * dbDelta() function to automatically create tables and add missing columns.
 *
 * @since 202607
 */
class Schema {
	/**
	 * Get all table schemas for Table of Contents Plus tables.
	 *
	 * Returns an array of CREATE TABLE statements for all tables
	 * used by Table of Contents Plus. These will be processed by dbDelta() to ensure
	 * the database schema matches the definitions.
	 *
	 * @since 202607
	 *
	 * @return array Array of SQL CREATE TABLE statements.
	 */
	public function getSchema() {
		return [
			$this->getNotificationsTableSchema(),
			$this->getCacheTableSchema()
		];
	}

	/**
	 * Get the schema for aioseo_table_of_contents_notifications table.
	 *
	 * @since 202607
	 *
	 * @return string SQL CREATE TABLE statement.
	 */
	private function getNotificationsTableSchema() {
		$tableName      = aioseoTableOfContents()->core->db->db->prefix . 'aioseo_table_of_contents_notifications';
		$charsetCollate = aioseoTableOfContents()->core->db->db->get_charset_collate();

		return "CREATE TABLE {$tableName} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			notification_id bigint(20) unsigned DEFAULT NULL,
			notification_name varchar(255) DEFAULT NULL,
			slug varchar(13) NOT NULL,
			title text NOT NULL,
			content longtext NOT NULL,
			type varchar(64) NOT NULL,
			level text NOT NULL,
			start datetime DEFAULT NULL,
			end datetime DEFAULT NULL,
			button1_label varchar(255) DEFAULT NULL,
			button1_action varchar(255) DEFAULT NULL,
			button2_label varchar(255) DEFAULT NULL,
			button2_action varchar(255) DEFAULT NULL,
			dismissed tinyint(1) NOT NULL DEFAULT 0,
			new tinyint(1) NOT NULL DEFAULT 1,
			created datetime NOT NULL,
			updated datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY ndx__aioseo_table_of_contents_notifications_slug (slug),
			KEY ndx__aioseo_table_of_contents_notifications_dates (start, end),
			KEY ndx__aioseo_table_of_contents_notifications_type (type),
			KEY ndx__aioseo_table_of_contents_notifications_dismissed (dismissed)
		) {$charsetCollate};";
	}

	/**
	 * Get the schema for aioseo_table_of_contents_cache table.
	 *
	 * @since 202607
	 *
	 * @return string SQL CREATE TABLE statement.
	 */
	public function getCacheTableSchema() {
		$tableName      = aioseoTableOfContents()->core->db->db->prefix . 'aioseo_table_of_contents_cache';
		$charsetCollate = aioseoTableOfContents()->core->db->db->get_charset_collate();

		return "CREATE TABLE {$tableName} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(80) NOT NULL,
			value longtext NOT NULL,
			is_object TINYINT(1) DEFAULT 0,
			expiration datetime DEFAULT NULL,
			created datetime NOT NULL,
			updated datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY ndx_aioseo_table_of_contents_cache_name (name),
			KEY ndx_aioseo_table_of_contents_cache_expiration (expiration)
		) {$charsetCollate};";
	}
}