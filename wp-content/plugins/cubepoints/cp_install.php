<?php
/**
 * CubePoints installation script
 */

 function cp_install() {

	// set default values
	add_option('cp_auth_key', substr(md5(uniqid()),3,10));
	add_option('cp_comment_points', 5);
	add_option('cp_reg_points', 100);
	add_option('cp_del_comment_points', 5);
	add_option('cp_post_points', 20);
	add_option('cp_prefix', '$');
	add_option('cp_suffix', '');
	add_option('cp_about_posts', true);
	add_option('cp_about_comments', true);
	add_option('cp_topfilter', array());
	add_option('cp_ver', CP_VER);

	// create database
	global $wpdb;
	if($wpdb->get_var("SHOW TABLES LIKE '".CP_DB."'") != CP_DB || (int) get_option('cp_db_version') < 1.3) {
		$sql = "CREATE TABLE " . CP_DB . " (
			  id bigint(20) NOT NULL AUTO_INCREMENT,
			  uid bigint(20) NOT NULL,
			  type VARCHAR(256) NOT NULL,
			  data TEXT NOT NULL,
			  points bigint(20) NOT NULL,
			  timestamp bigint(20) NOT NULL,
			  UNIQUE KEY id (id)
			);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option("cp_db_version", 1.3);
	}

}
 
?>