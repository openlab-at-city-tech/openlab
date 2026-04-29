<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AN_GradeBook_Database {
	const DB_VERSION = 4.0;

	public static function maybe_setup() {
		if ( get_option( 'an_gradebook_db_version' ) != self::DB_VERSION ) {
			self::setup();
		}
	}

	public static function setup() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$table_gradebooks  = an_gradebook_table( 'an_gradebooks' );
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );
		$table_assignments = an_gradebook_table( 'an_assignments' );
		$table_assignment  = an_gradebook_table( 'an_assignment' );

		dbDelta( "CREATE TABLE {$table_gradebooks} (
			id int(11) NOT NULL AUTO_INCREMENT,
			name MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			school TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			semester TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			year int(11) NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};" );

		dbDelta( "CREATE TABLE {$table_gradebook} (
			id int(11) NOT NULL AUTO_INCREMENT,
			uid int(11) NOT NULL,
			gbid int(11) NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};" );

		dbDelta( "CREATE TABLE {$table_assignments} (
			id int(11) NOT NULL AUTO_INCREMENT,
			gbid int(11) NOT NULL,
			assign_order int(11) NOT NULL,
			assign_name mediumtext NOT NULL,
			assign_category mediumtext NOT NULL,
			assign_visibility VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Students',
			assign_date DATE NOT NULL DEFAULT '0000-00-00',
			assign_due DATE NOT NULL DEFAULT '0000-00-00',
			PRIMARY KEY  (id)
		) {$charset_collate};" );

		dbDelta( "CREATE TABLE {$table_assignment} (
			id int(11) NOT NULL AUTO_INCREMENT,
			uid int(11) NOT NULL,
			gbid int(11) NOT NULL,
			amid int(11) NOT NULL,
			assign_order int(11) NOT NULL,
			assign_points_earned decimal(7,2) NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};" );

		update_option( 'an_gradebook_db_version', self::DB_VERSION );
	}
}
