<?php
/*
 Plugin Name: WP Twitter Auto Publish
Plugin URI: https://xyzscripts.com/wordpress-plugins/twitter-auto-publish/
Description:   Publish posts automatically from your blog to Twitter social media. You can publish your posts to Twitter as simple text message or as text message with image. The plugin supports filtering posts by post-types and categories.
Version: 1.4.1
Author: xyzscripts.com
Author URI: https://xyzscripts.com/
License: GPLv2 or later
*/

/*
 This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if( !defined('ABSPATH') ){ exit();}
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}
//ob_start();
//error_reporting(E_ALL);
define('XYZ_TWAP_PLUGIN_FILE',__FILE__);
global $wpdb;
//$wpdb->query('SET SQL_MODE=""');

require_once( dirname( __FILE__ ) . '/admin/install.php' );
require_once( dirname( __FILE__ ) . '/xyz-functions.php' );
require_once( dirname( __FILE__ ) . '/admin/menu.php' );
require_once( dirname( __FILE__ ) . '/admin/destruction.php' );

require_once( dirname( __FILE__ ) . '/api/twitteroauth.php' );
require_once( dirname( __FILE__ ) . '/admin/ajax-backlink.php' );
require_once( dirname( __FILE__ ) . '/admin/metabox.php' );
require_once( dirname( __FILE__ ) . '/admin/publish.php' );
require_once( dirname( __FILE__ ) . '/admin/admin-notices.php' );
if(isset($_GET['page']) && ($_GET['page']=='twitter-auto-publish-suggest-features')){
	ob_start();
}
if(get_option('xyz_credit_link')=="twap"){

	add_action('wp_footer', 'xyz_twap_credit');

}
function xyz_twap_credit() {
	$content = '<div style="clear:both;width:100%;text-align:center; font-size:11px; "><a target="_blank" title="WP Twitter Auto Publish" href="https://xyzscripts.com/wordpress-plugins/twitter-auto-publish/details" >WP Twitter Auto Publish</a> Powered By : <a target="_blank" title="PHP Scripts & Programs" href="http://www.xyzscripts.com" >XYZScripts.com</a></div>';
	echo $content;
}
if(!function_exists('get_post_thumbnail_id'))
	add_theme_support( 'post-thumbnails' );
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'xyz_twap_add_action_links' );
function xyz_twap_add_action_links( $links ) {
	$xyz_twap_links = array(
			'<a href="' . admin_url( 'admin.php?page=twitter-auto-publish-settings' ) . '">Settings</a>',
	);
	return array_merge( $links, $xyz_twap_links);
}
