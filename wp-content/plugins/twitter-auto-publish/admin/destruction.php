<?php
if( !defined('ABSPATH') ){ exit();}
function twap_free_network_destroy($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				twap_free_destroy();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	twap_free_destroy();
}

function twap_free_destroy()
{
	global $wpdb;
	
	if(get_option('xyz_credit_link')=="twap")
	{
		update_option("xyz_credit_link", '0');
	}
	
		
	delete_option('xyz_twap_twconsumer_secret');
	delete_option('xyz_twap_twconsumer_id');
	delete_option('xyz_twap_tw_id');
	delete_option('xyz_twap_current_twappln_token');
	delete_option('xyz_twap_twpost_permission');
	delete_option('xyz_twap_twpost_image_permission');
	delete_option('xyz_twap_twaccestok_secret');
	delete_option('xyz_twap_twmessage');
	delete_option('xyz_twap_future_to_publish');
	delete_option('xyz_twap_apply_filters');
	
	delete_option('xyz_twap_free_version');
	
	delete_option('xyz_twap_include_pages');
	delete_option('xyz_twap_include_posts');
	delete_option('xyz_twap_include_categories');
	delete_option('xyz_twap_include_customposttypes');
	delete_option('xyz_twap_peer_verification');
	delete_option('xyz_twap_post_logs');
	delete_option('xyz_twap_premium_version_ads');
	delete_option('xyz_twap_default_selection_edit');
	delete_option('twap_installed_date');
	delete_option('xyz_twap_dnt_shw_notice');
	delete_option('xyz_twap_tw_char_limit');
	delete_option('xyz_twap_credit_dismiss');
}

register_uninstall_hook(XYZ_TWAP_PLUGIN_FILE,'twap_free_network_destroy');


?>