<?php

if (defined('WP_UNINSTALL_PLUGIN')) {

	global $wpdb;

	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}oplb_gradebook_courses");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}oplb_gradebook_users");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}oplb_gradebook_assignments");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}oplb_gradebook_cells");
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM $wpdb->options
		 	WHERE option_name = %s",
			'oplb_gradebook_db_version'
		)
	);
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM $wpdb->options
		 	WHERE option_name = %s",
			'oplb_gradebook_settings'
		)
	);
	return false;
}

?>