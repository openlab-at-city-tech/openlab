<?php

/*-----------------------------------------------------------------------------------*/
/* WooThemes Framework Version & Theme Version */
/*-----------------------------------------------------------------------------------*/
function woo_version_init(){

    $woo_framework_version = '4.1.0';

    if ( get_option( 'woo_framework_version') <> $woo_framework_version )
    	update_option( 'woo_framework_version', $woo_framework_version);

}
add_action( 'init', 'woo_version_init' );

function woo_version(){

    $theme_data = get_theme_data( get_template_directory() . '/style.css' );
    $theme_version = $theme_data['Version'];
    $woo_framework_version = get_option( 'woo_framework_version' );

	echo "\n<!-- Theme version -->\n";
    echo '<meta name="generator" content="'. get_option( 'woo_themename').' '. $theme_version .'" />' ."\n";
    echo '<meta name="generator" content="WooFramework '. $woo_framework_version .'" />' ."\n";

}
// Add or remove Generator meta tags
if ( get_option( 'framework_woo_disable_generator') == "true" )
	remove_action( 'wp_head',  'wp_generator' );
else
	add_action( 'wp_head', 'woo_version' );

/*-----------------------------------------------------------------------------------*/
/* Load the required Framework Files */
/*-----------------------------------------------------------------------------------*/

$functions_path = get_template_directory() . '/functions/';

require_once ( $functions_path . 'admin-functions.php' );				// Custom functions and plugins
require_once ( $functions_path . 'admin-setup.php' );					// Options panel variables and functions
require_once ( $functions_path . 'admin-custom.php' );				// Custom fields
require_once ( $functions_path . 'admin-interface.php' );				// Admin Interfaces (options,framework, seo)
require_once ( $functions_path . 'admin-framework-settings.php' );	// Framework Settings
require_once ( $functions_path . 'admin-seo.php' );					// Framework SEO controls
require_once ( $functions_path . 'admin-sbm.php' ); 					// Framework Sidebar Manager
require_once ( $functions_path . 'admin-medialibrary-uploader.php' ); // Framework Media Library Uploader Functions // 2010-11-05.
require_once ( $functions_path . 'admin-hooks.php' );					// Definition of WooHooks
if (get_option( 'framework_woo_woonav') == "true")
	require_once ($functions_path . 'admin-custom-nav.php' );		// Woo Custom Navigation
require_once ( $functions_path . 'admin-shortcodes.php' );			// Woo Shortcodes
require_once ( $functions_path . 'admin-shortcode-generator.php' ); 	// Framework Shortcode generator // 2011-01-21.
?>