<?php

namespace OpenLab\Favorites;

class Schema {
	public static function get_table_name() {
		global $wpdb;

		$prefix = $wpdb->get_blog_prefix( get_main_site_id() );

		return "{$wpdb->prefix}openlab_favorites";
	}

	public static function install_table() {
		$sql = self::get_schema();

		if ( ! function_exists( 'dbDelta' ) ) {
			require ABSPATH . '/wp-admin/includes/upgrade.php';
		}

		$installed = dbDelta( $sql );
	}

	public static function get_schema() {
		global $wpdb;

		$sql = array();
		$charset_collate = $wpdb->get_charset_collate();

		$table_name = self::get_table_name();

		$sql[] = "CREATE TABLE {$table_name} (
					id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id bigint(20) NOT NULL,
					group_id bigint(20) NOT NULL,
					date_created datetime NOT NULL,
					KEY user_id (user_id),
					KEY group_id (group_id)
				) {$charset_collate};";

		return $sql;
	}
}
