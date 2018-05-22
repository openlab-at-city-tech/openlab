<?php
/**
 * Plugin Name: Download Media Library
 * Plugin URI: https://github.com/marcelotorres/download-media-library/
 * Description: Download the files from the Media Library in ZIP format, <strong>organized by post type > post name > media type > file extension</strong>.
 * Author: marcelotorres
 * Author URI: http://marcelotorresweb.com/
 * Version: 0.2.1
 * License: GPLv2 or later
 * Text Domain: mtdml
 * Domain Path: /languages/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Enable allow_url_fopen
if( !(ini_get('allow_url_fopen') )) {
   ini_set(allow_url_fopen, 'On');
} 

// Sets the plugin path/url.
define( 'MTDML_PATH', plugin_dir_path( __FILE__ ) );

//Add custom meta links for plugins page
add_filter( 'plugin_row_meta', 'mtdml_custom_plugin_row_meta', 10, 2 );
function mtdml_custom_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'download-media-library.php' ) !== false ) {
		$new_links = array(
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G85Z9XFXWWHCY" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" alt="PayPal - The safer, easier way to pay online!" border="0"></a>'
			);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}

//enqueue scripts and styles
function mtdml_scripts() {
	//wp_enqueue_style( 'style-name', get_stylesheet_uri() );
	wp_register_script( 'mtdml-admin', plugins_url( '/assets/js/admin.js', __FILE__ ) );
	wp_enqueue_script( 'mtdml-admin', plugins_url( '/assets/js/admin.js', __FILE__ ) );
}
add_action( 'admin_init', 'mtdml_scripts' );

// Load textdomain.
load_plugin_textdomain( 'mtdml', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

// Include admin settings
require_once(MTDML_PATH.'download-media-library-admin.php');