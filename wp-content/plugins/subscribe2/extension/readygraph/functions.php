<?php
	if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   ReadyGraph
 * @author    dan@readygraph.com
 * @license   GPL-2.0+
 * @link      http://www.readygraph.com
 * @copyright 2014 Your Name or Company Name
 */
 
function s2_disconnectReadyGraph(){
	$app_id = get_option('readygraph_application_id');
	wp_remote_get( "http://readygraph.com/api/v1/tracking?event=disconnect_readygraph&app_id=$app_id" );
	s2_delete_rg_options();
	echo '<div class="updated"><p>We are sorry to see you go. ReadyGraph is now disconnected.</p></div>';
}
function s2_deleteReadyGraph(){
	$app_id = get_option('readygraph_application_id');
	update_option('readygraph_deleted', 'true');
	wp_remote_get( "http://readygraph.com/api/v1/tracking?event=uninstall_readygraph&app_id=$app_id" );
	s2_delete_rg_options();
	$dir = plugin_dir_path( __FILE__ );
	s2_rrmdir($dir);
}
function s2_readygraph_monetize_update(){
	$app_id = get_option('readygraph_application_id');
	$email = get_option('readygraph_monetize_email');
	$monetize = get_option('readygraph_enable_monetize');
	$url = 'https://readygraph.com/api/v1/wp-monetize/';
	$response = wp_remote_post($url, array( 'body' => array('app_id' => $app_id, 'monetize_email' => $email, 'monetize' => $monetize)));
	if ( is_wp_error( $response ) ) {
	} else {
   $json_decoded = json_decode($response['body'],true);
   if (array_key_exists('adsoptimal_id', $json_decoded['data'])) {
   update_option('readygraph_adsoptimal_id', $json_decoded['data']['adsoptimal_id']);
   }   if (array_key_exists('adsoptimal_secret', $json_decoded['data'])) {
   update_option('readygraph_adsoptimal_secret', $json_decoded['data']['adsoptimal_secret']);
   }
}
}
function s2_siteprofile_sync(){
	$app_id = get_option('readygraph_application_id');
	$email = get_option('readygraph_email');
	$site_name = get_option('readygraph_site_name');
	$site_url = get_option('readygraph_site_url');
	$site_description = get_option('readygraph_site_description');
	$site_category = get_option('readygraph_site_category');
	$site_language = get_option('readygraph_site_language');
	$url = 'https://readygraph.com/api/v1/wordpress-sync-siteprofile/';
	$response = wp_remote_post($url, array( 'body' => array('app_id' => $app_id, 'email' => $email, 'site_profile_name' => $site_name, 'site_profile_url' => $site_url, 'site_description' => $site_description, 'site_category' => $site_category, 'site_language' => $site_language)));
}

?>