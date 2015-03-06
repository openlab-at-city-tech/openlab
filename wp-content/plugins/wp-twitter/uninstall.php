<?php
/**
 * Code used when the plugin is removed (not just deactivated but actively deleted through the WordPress Admin).
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

//get_option('fdx_updater_options');

//Settings
delete_option('fdx1_settings');


//Sharethis Button integration
delete_option('wp_twitter_fdx_tweet_button_display_single');
delete_option('wp_twitter_fdx_tweet_button_display_page');
delete_option('wp_twitter_fdx_tweet_button_display_home');
delete_option('wp_twitter_fdx_tweet_button_display_arquive');
delete_option('wp_twitter_copynshare');
delete_option('wp_twitter_fdx_tweet_button_place');
delete_option('wp_twitter_fdx_tweet_button_style');
delete_option('wp_twitter_fdx_tweet_button_choose');
delete_option('wp_twitter_fdx_tweet_button_container');
delete_option('wp_twitter_fdx_tweet_button_twitter_username');
delete_option('wp_twitter_fdx_logo_top');
delete_option('wp_twitter_fdx_tweet_button_style2');
delete_option('wp_twitter_fdx_tweet_button_style3');
delete_option('wp_twitter_fdx_services');

//widgets
delete_option('wp_twitter_fdx_widget_title');
delete_option('wp_twitter_fdx_username');
delete_option('wp_twitter_fdx_height');
delete_option('wp_twitter_fdx_width');
delete_option('wp_twitter_fdx_shell_bg');
delete_option('wp_twitter_fdx_shell_text');
delete_option('wp_twitter_fdx_tweet_bg');
delete_option('wp_twitter_fdx_tweet_text');
delete_option('wp_twitter_fdx_links');

delete_option('wp_twitter_fdx_search_widget_sidebar_title');
delete_option('wp_twitter_fdx_widget_search_title');
delete_option('wp_twitter_fdx_widget_search_caption');
delete_option('wp_twitter_fdx_search_height');
delete_option('wp_twitter_fdx_search_width');
delete_option('wp_twitter_fdx_search_shell_bg');
delete_option('wp_twitter_fdx_search_tweet_bg');
delete_option('wp_twitter_fdx_search_tweet_text');
delete_option('wp_twitter_fdx_search_links');