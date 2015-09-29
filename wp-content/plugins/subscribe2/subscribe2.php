<?php
/*
Plugin Name: Subscribe2
Plugin URI: http://subscribe2.wordpress.com
Description: Notifies an email list when new entries are posted.
Version: 10.20.8
Author: Matthew Robinson, Tanay Lakhani
Author URI: http://subscribe2.wordpress.com
Licence: GPL3
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=2387904
Text Domain: subscribe2
*/

/*
Copyright (C) 2006-14 Matthew Robinson
Based on the Original Subscribe2 plugin by
Copyright (C) 2005 Scott Merrill (skippy@skippy.net)

This file is part of Subscribe2.

Subscribe2 is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Subscribe2 is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Subscribe2. If not, see <http://www.gnu.org/licenses/>.
*/

if ( version_compare($GLOBALS['wp_version'], '3.3', '<') || !function_exists( 'add_action' ) ) {
	if ( !function_exists( 'add_action' ) ) {
		$exit_msg = __("I'm just a plugin, please don't call me directly", 'subscribe2');
	} else {
		// Subscribe2 needs WordPress 3.3 or above, exit if not on a compatible version
		$exit_msg = sprintf(__('This version of Subscribe2 requires WordPress 3.3 or greater. Please update %1$s or use an older version of %2$s.', 'subscribe2'), '<a href="http://codex.wordpress.org/Updating_WordPress">Wordpress</a>', '<a href="http://wordpress.org/extend/plugins/subscribe2/download/">Subscribe2</a>');
	}
	exit($exit_msg);
}

// stop Subscribe2 being activated site wide on Multisite installs
if ( !function_exists( 'is_plugin_active_for_network' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( is_plugin_active_for_network(plugin_basename(__FILE__)) ) {
	deactivate_plugins( plugin_basename(__FILE__) );
	$exit_msg = __('Subscribe2 cannot be activated as a network plugin. Please activate it on a site level', 'subscribe2');
	exit($exit_msg);
}

// our version number. Don't touch this or any line below
// unless you know exactly what you are doing
define( 'S2VERSION', '10.20.8' );
define( 'S2PATH', trailingslashit(dirname(__FILE__)) );
define( 'S2DIR', trailingslashit(dirname(plugin_basename(__FILE__))) );
define( 'S2URL', plugin_dir_url(dirname(__FILE__)) . S2DIR );

// Set maximum execution time to 5 minutes - won't affect safe mode
$safe_mode = array('On', 'ON', 'on', 1);
if ( !in_array(ini_get('safe_mode'), $safe_mode) && ini_get('max_execution_time') < 300 ) {
	@ini_set('max_execution_time', 300);
}

require_once( S2PATH . 'classes/class-s2-core.php' );
if ( is_admin() ) {
	require_once( S2PATH . 'classes/class-s2-admin.php' );
	global $mysubscribe2;
	$mysubscribe2 = new s2_admin;
	$mysubscribe2->s2init();
} else {
	require_once( S2PATH . 'classes/class-s2-frontend.php' );
	global $mysubscribe2;
	$mysubscribe2 = new s2_frontend;
	$mysubscribe2->s2init();
}

function s2_install() {
	add_option('readygraph_tutorial', 'true');
	add_option('rg_s2_plugin_do_activation_redirect', true);
}
if( file_exists(plugin_dir_path( __FILE__ ).'/readygraph-extension.php' )) {
if (get_option('readygraph_deleted') && get_option('readygraph_deleted') == 'true'){}
else{
include "readygraph-extension.php";
}
if(get_option('readygraph_application_id') && strlen(get_option('readygraph_application_id')) > 0){
register_deactivation_hook( __FILE__, 's2_readygraph_plugin_deactivate' );
}
function s2_readygraph_plugin_deactivate(){
	$app_id = get_option('readygraph_application_id');
	update_option('readygraph_deleted', 'false');
	wp_remote_get( "http://readygraph.com/api/v1/tracking?event=subscribe2_plugin_uninstall&app_id=$app_id" );
	s2_delete_rg_options();
}
}
else {

}
register_activation_hook(__FILE__, 's2_install');

function s2_rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") 
           s2_rrmdir($dir."/".$object); 
        else unlink   ($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
  }
  $del_url = plugin_dir_path( __FILE__ );
  unlink($del_url.'/readygraph-extension.php');
 $setting_url="admin.php?page=s2";
  echo'<script> window.location="'.admin_url($setting_url).'"; </script> ';
}
function s2_delete_rg_options() {
delete_option('readygraph_access_token');
delete_option('readygraph_application_id');
delete_option('readygraph_refresh_token');
delete_option('readygraph_email');
delete_option('readygraph_settings');
delete_option('readygraph_delay');
delete_option('readygraph_enable_sidebar');
delete_option('readygraph_auto_select_all');
delete_option('readygraph_enable_notification');
delete_option('readygraph_enable_popup');
delete_option('readygraph_enable_branding');
delete_option('readygraph_send_blog_updates');
delete_option('readygraph_send_real_time_post_updates');
delete_option('readygraph_popup_template');
delete_option('readygraph_upgrade_notice');
delete_option('readygraph_adsoptimal_secret');
delete_option('readygraph_adsoptimal_id');
delete_option('readygraph_connect_anonymous');
delete_option('readygraph_connect_anonymous_app_secret');
delete_option('readygraph_tutorial');
delete_option('readygraph_site_url');
delete_option('readygraph_enable_monetize');
delete_option('readygraph_monetize_email');
delete_option('readygraph_plan');
}

?>