<?php
if ( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
	global $wpdb;

	$sql = "delete from $wpdb->postmeta where left(meta_value, 6) = 'wpAjax'";
	$wpdb->query($sql);
	
	$sql = "delete from $wpdb->posts where post_type = 'ajax_edit_comments'";
	$wpdb->query($sql);
	
	delete_option('WPAjaxEditAuthoruserOptions');
	delete_option( 'WPAjaxEditAuthoruser_options' );
	delete_option('WPAjaxEditComments20');
	delete_site_option( 'WPAjaxEditAuthoruserOptions' );
	delete_site_option( 'WPAjaxEditAuthoruser_options' );
	delete_site_option( 'WPAjaxEditComments20' );
?>
