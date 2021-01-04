<?php
if( !defined('ABSPATH') ){ exit();}
add_action('admin_menu', 'xyz_twap_menu');

function xyz_twap_add_admin_scripts()
{
	wp_enqueue_script('jquery');
	wp_register_script( 'xyz_notice_script_twap', plugins_url('twitter-auto-publish/js/notice.js') );
	wp_enqueue_script( 'xyz_notice_script_twap' );
	
	wp_register_style('xyz_twap_style', plugins_url('twitter-auto-publish/css/style.css'));
	wp_enqueue_style('xyz_twap_style');
}

add_action("admin_enqueue_scripts","xyz_twap_add_admin_scripts");

function xyz_twap_menu()
{
	add_menu_page('Twitter Auto Publish - Manage settings', 'WP Twitter Auto Publish', 'manage_options', 'twitter-auto-publish-settings', 'xyz_twap_settings',plugin_dir_url( XYZ_TWAP_PLUGIN_FILE ) . 'images/twap.png');
	$page=add_submenu_page('twitter-auto-publish-settings', 'Twitter Auto Publish - Manage settings', ' Settings', 'manage_options', 'twitter-auto-publish-settings' ,'xyz_twap_settings'); // 8 for admin
	add_submenu_page('twitter-auto-publish-settings', 'Twitter Auto Publish - Logs', 'Logs', 'manage_options', 'twitter-auto-publish-log' ,'xyz_twap_logs');
	add_submenu_page('twitter-auto-publish-settings', 'Twitter Auto Publish - About', 'About', 'manage_options', 'twitter-auto-publish-about' ,'xyz_twap_about'); // 8 for admin
	add_submenu_page('twitter-auto-publish-settings', 'Twitter Auto Publish - Suggest Feature', 'Suggest a Feature', 'manage_options', 'twitter-auto-publish-suggest-features' ,'xyz_twap_suggest_feature');
}


function xyz_twap_settings()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);	
	$_POST = xyz_trim_deep($_POST);
	$_GET = xyz_trim_deep($_GET);
	
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/settings.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}



function xyz_twap_about()
{
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/about.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}


function xyz_twap_suggest_feature()
{
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/suggest_feature.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}
function xyz_twap_logs()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$_POST = xyz_trim_deep($_POST);
	$_GET = xyz_trim_deep($_GET);

	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/logs.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}

?>