<?php
if ( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
	global $wpdb;

	$sql = "delete from $wpdb->postmeta where left(meta_value, 6) = 'wpAjax'";
	$wpdb->query($wpdb->prepare($sql));
	
	$sql = "delete from $wpdb->posts where post_type = 'ajax_edit_comments'";
	$wpdb->query($wpdb->prepare($sql));
	
	delete_option('WPAjaxEditAuthoruserOptions');
	delete_option('WPAjaxEditComments20');
?>