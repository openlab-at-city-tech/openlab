<?php

/**
 * Scheme class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Scheme {



	/**
	 * Tables used by this plugin
	 */
	public static function get_tables() {
		return array(
			'urls',
			'urls_locations',
			'urls_locations_att',
			'urls_status',
			'scans',
			'scans_objects',
		);
	}



	/**
	 * Remove all plugin tables
	 */
	public static function drop_tables() {

		// Globals
		global $wpdb;

		// Plugin tables
		$tables = self::get_tables();

		// Remove each one
		foreach ($tables as $name) {
			$wpdb->query('DROP TABLE '.$wpdb->prefix.'wplnst_'.esc_sql($name));
		}
	}



	/**
	 * Check plugin custom tables
	 */
	public static function check_tables() {

		// Globals
		global $wpdb;

		// Initialize
		$create = array();

		// Plugin tables
		$tables = self::get_tables();

		// Check each table
		foreach ($tables as $name) {
			$result = $wpdb->get_var('SHOW TABLES LIKE "'.$wpdb->prefix.'wplnst_'.esc_sql($name).'"');
			if (empty($result)) {
				$create[] = $name;
			}
		}

		// Done
		return empty($create)? false : $create;
	}



	/**
	 * Create custom plugin tables
	 */
	public static function create_tables($tables = array()) {

		// Globals
		global $wpdb;

		// Compose charset
		$charset = (empty($wpdb->charset)? '' : ' DEFAULT CHARACTER SET '.$wpdb->charset).(empty($wpdb->collate)? '' : ' COLLATE '.$wpdb->collate);

		// URLs table
		if (in_array('urls', $tables)) {
			$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'wplnst_urls` (
				`url_id` 				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`url` 					TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT "",
				`hash`					VARCHAR(64) NOT NULL DEFAULT "",
				`scheme`				VARCHAR(20) NOT NULL DEFAULT "",
				`host`					VARCHAR(255) NOT NULL DEFAULT "",
				`path`					VARCHAR(255) NOT NULL DEFAULT "",
				`query`					VARCHAR(255) NOT NULL DEFAULT "",
				`scope`					VARCHAR(10) NOT NULL DEFAULT "",
				`created_at` 			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`last_scan_id` 			BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`last_status_level`		VARCHAR(1) NOT NULL DEFAULT "",
				`last_status_code`		VARCHAR(3) NOT NULL DEFAULT "",
				`last_curl_errno`		INT(3) UNSIGNED NOT NULL DEFAULT 0,
				`last_request_at` 		DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				PRIMARY KEY				(`url_id`),
				KEY `url` 				(`url`(255)),
				UNIQUE KEY `hash`		(`hash`),
				KEY `scheme` 			(`scheme`),
				KEY `host` 				(`host`),
				KEY `path` 				(`path`),
				KEY `query` 			(`query`),
				KEY `scope` 			(`scope`),
				KEY `last_scan_id`		(`last_scan_id`),
				KEY `last_status_level` (`last_status_level`),
				KEY `last_status_code` 	(`last_status_code`),
				KEY `last_curl_errno` 	(`last_curl_errno`),
				KEY `last_request_at` 	(`last_request_at`)
			)'.$charset);
		}

		// URLs and locations relationship table
		if (in_array('urls_locations', $tables)) {
			$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'wplnst_urls_locations` (
				`loc_id` 				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`url_id` 				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`scan_id` 				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`link_type`				VARCHAR(25) NOT NULL DEFAULT "",
				`object_id`				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`object_type`			VARCHAR(50) NOT NULL DEFAULT "",
				`object_post_type`		VARCHAR(20) NOT NULL DEFAULT "",
				`object_field`			VARCHAR(100) NOT NULL DEFAULT "",
				`object_date_gmt`		DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`detected_at` 			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`chunk`					TEXT NOT NULL DEFAULT "",
				`anchor`				TEXT NOT NULL DEFAULT "",
				`raw_url` 				TEXT NOT NULL DEFAULT "",
				`fragment`				TEXT NOT NULL DEFAULT "",
				`spaced`				TINYINT(1) NOT NULL DEFAULT 0,
				`malformed` 			TINYINT(1) NOT NULL DEFAULT 0,
				`absolute` 				TINYINT(1) NOT NULL DEFAULT 0,
				`protorel`				TINYINT(1) NOT NULL DEFAULT 0,
				`relative` 				TINYINT(1) NOT NULL DEFAULT 0,
				`nofollow`				TINYINT(1) NOT NULL DEFAULT 0,
				`ignored` 				TINYINT(1) NOT NULL DEFAULT 0,
				`unlinked` 				TINYINT(1) NOT NULL DEFAULT 0,
				`modified` 				TINYINT(1) NOT NULL DEFAULT 0,
				`anchored`				TINYINT(1) NOT NULL DEFAULT 0,
				`attributed`			TINYINT(1) NOT NULL DEFAULT 0,
				PRIMARY KEY	 			(`loc_id`),
				KEY `url_id` 			(`url_id`),
				KEY `scan_id` 			(`scan_id`),
				KEY `link_type` 		(`link_type`),
				KEY `object_id` 		(`object_id`),
				KEY `object_type` 		(`object_type`),
				KEY `object_date_gmt` 	(`object_date_gmt`),
				KEY `anchor`			(`anchor`(255)),
				KEY `spaced`			(`spaced`),
				KEY `malformed` 		(`malformed`),
				KEY `absolute` 			(`absolute`),
				KEY `protorel`			(`protorel`),
				KEY `relative`			(`relative`),
				KEY `nofollow`			(`nofollow`),
				KEY `ignored`			(`ignored`),
				KEY `unlinked`			(`unlinked`),
				KEY `modified`			(`modified`),
				KEY `anchored`			(`anchored`),
				KEY `attributed`		(`attributed`)
			)'.$charset);
		}

		// URLs locations and attributes relationship table
		if (in_array('urls_locations_att', $tables)) {
			$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'wplnst_urls_locations_att` (
				`att_id` 				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`loc_id` 				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`scan_id` 				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`attribute`				VARCHAR(255) NOT NULL DEFAULT "",
				`value`					VARCHAR(255) NOT NULL DEFAULT "",
				PRIMARY KEY	 			(`att_id`),
				KEY `loc_id` 			(`loc_id`),
				KEY `scan_id` 			(`scan_id`),
				KEY `attribute`			(`attribute`),
				KEY `value`				(`value`)
			)'.$charset);
		}

		// URLs status and scans relationship table
		if (in_array('urls_status', $tables)) {
			$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'wplnst_urls_status` (
				`url_id` 				BIGINT(20) UNSIGNED NOT NULL,
				`scan_id` 				BIGINT(20) UNSIGNED NOT NULL,
				`status_level`			VARCHAR(1) NOT NULL DEFAULT "",
				`status_code`			VARCHAR(3) NOT NULL DEFAULT "",
				`curl_errno`			INT(3) UNSIGNED NOT NULL DEFAULT 0,
				`redirect_url`			TEXT NOT NULL DEFAULT "",
				`redirect_steps`		TEXT NOT NULL DEFAULT "",
				`redirect_url_id`		BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`redirect_url_status`	VARCHAR(3) NOT NULL DEFAULT "",
				`redirect_curl_errno`	INT(3) UNSIGNED NOT NULL DEFAULT 0,
				`headers`				TEXT NOT NULL DEFAULT "",
				`headers_request`		TEXT NOT NULL DEFAULT "",
				`body`					TEXT NOT NULL DEFAULT "",
				`phase`					VARCHAR(20) NOT NULL DEFAULT "",
				`created_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`started_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`request_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`total_objects`			INT(9) UNSIGNED NOT NULL DEFAULT 0,
				`total_posts`			INT(9) UNSIGNED NOT NULL DEFAULT 0,
				`total_comments`		INT(9) UNSIGNED NOT NULL DEFAULT 0,
				`total_blogroll`		INT(9) UNSIGNED NOT NULL DEFAULT 0,
				`total_time`			DECIMAL(3,3) UNSIGNED NOT NULL DEFAULT 0,
				`total_bytes`			BIGINT(20) UNSIGNED NOT NULL,
				`requests`				INT(4) UNSIGNED NOT NULL DEFAULT 0,
				`rechecked`				TINYINT(1) NOT NULL DEFAULT 0,
				PRIMARY KEY				(`url_id`, `scan_id`),
				KEY `url_id`			(`url_id`),
				KEY `scan_id`			(`scan_id`),
				KEY `status_level` 		(`status_level`),
				KEY `status_code` 		(`status_code`),
				KEY `curl_errno` 		(`curl_errno`),
				KEY `redirect_url_id` 	(`redirect_url_id`),
				KEY `redirect_url_status` (`redirect_url_status`),
				KEY `phase`				(`phase`),
				KEY `total_objects`		(`total_objects`),
				KEY `total_posts`		(`total_posts`),
				KEY `total_comments` 	(`total_comments`),
				KEY `total_blogroll` 	(`total_blogroll`),
				KEY `total_time`		(`total_time`),
				KEY `total_bytes`		(`total_bytes`),
				KEY `rechecked`			(`rechecked`)
			)'.$charset);
		}

		// Scans table
		if (in_array('scans', $tables)) {
			$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'wplnst_scans` (
				`scan_id` 				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`type`					VARCHAR(20)  NOT NULL DEFAULT "scan",
				`name`					VARCHAR(255) NOT NULL DEFAULT "",
				`status`				VARCHAR(20)  NOT NULL DEFAULT "",
				`ready` 				TINYINT(1) NOT NULL DEFAULT 0,
				`hash` 					VARCHAR(32) NOT NULL DEFAULT "",
				`created_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`modified_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`modified_by`			BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`started_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`enqueued_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`stopped_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`continued_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`finished_at`			DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				`config`				TEXT NOT NULL DEFAULT "",
				`summary`				TEXT NOT NULL DEFAULT "",
				`trace`					TEXT NOT NULL DEFAULT "",
				`threads`				TEXT NOT NULL DEFAULT "",
				`max_threads`			INT(3) UNSIGNED NOT NULL DEFAULT 0,
				`connect_timeout`		INT(4) UNSIGNED NOT NULL DEFAULT 0,
				`request_timeout`		INT(4) UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY	 			(`scan_id`),
				KEY `type`				(`type`),
				KEY `name` 				(`name`),
				KEY `status`			(`status`),
				UNIQUE KEY `hash`		(`hash`),
				KEY `config`			(`config`(255))
			)'.$charset);
		}

		// Scans objects
		if (in_array('scans_objects', $tables)) {
			$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'wplnst_scans_objects` (
				`scan_id` 				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`object_id`				BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`object_type`			VARCHAR(50) NOT NULL DEFAULT "",
				`object_date_gmt`		DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
				PRIMARY KEY	 			(`scan_id`, `object_id`, `object_type`),
				KEY `scan_id` 			(`scan_id`),
				KEY `object_type` 		(`object_type`),
				KEY `object_date_gmt`	(`object_date_gmt`)
			)'.$charset);
		}
	}



	/**
	 * Upgrade table schemes
	 */
	public static function upgrade() {}



}