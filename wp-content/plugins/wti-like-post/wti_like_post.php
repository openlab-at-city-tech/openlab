<?php
/*
Plugin Name: WTI Like Post
Plugin URI: http://www.webtechideas.com/wti-like-post-plugin/
Description: WTI Like Post is a plugin for adding like (thumbs up) and unlike (thumbs down) functionality for posts/pages. On admin end alongwith handful of configuration settings, it will show a list of most liked posts/pages. If you have already liked a post/page and now you dislike it, then the old voting will be cancelled and vice-versa. You can reset the settings to default and the like/unlike counts for all/selected posts/pages as well. It comes with two widgets, one to display the most liked posts/pages for a given time range and another to show recently liked posts. Check out the <strong><a href="http://www.webtechideas.com/product/wti-like-post-pro/" target="_blank">powerful PRO version</a></strong> with lots of useful features.
Version: 1.4.2
Author: webtechideas
Author URI: http://www.webtechideas.com/
License: GPLv2 or later

Copyright 2014  Webtechideas  (email : support@webtechideas.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

#### INSTALLATION PROCESS ####
/*
1. Download the plugin and extract it
2. Upload the directory '/wti-like-post/' to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Click on 'WTI Like Post' link under Settings menu to access the admin section
*/

global $wti_like_post_db_version;
$wti_like_post_db_version = "1.4.2";

add_action('init', 'WtiLoadPluginTextdomain');
add_action('admin_init', 'WtiLikePostPluginUpdateMessage');

/**
 * Load the language files for this plugin
 * @param void
 * @return void
 */
function WtiLoadPluginTextdomain() {
     load_plugin_textdomain('wti-like-post', false, 'wti-like-post/lang');
}

/**
 * Hook the auto update message
 * @param void
 * @return void
 */
function WtiLikePostPluginUpdateMessage() {
    add_action( 'in_plugin_update_message-' . basename( dirname( __FILE__ ) ) . '/wti_like_post.php', 'WtiLikePostUpdateNotice' );
}

/**
 * Show additional message for plugin update
 * @param void
 * @return void
 */
function WtiLikePostUpdateNotice() {
    $info_title = __( 'In case there was any customization done with this plugin, then please take a backup first.', 'wti-like-post' );
    $info_text =  __( 'Check out the powerful PRO version with lots of useful features.', 'wti-like-post' );
    echo '<div style="border-top:1px solid #CCC; margin-top:3px; padding-top:3px; font-weight:normal;"><strong style="color:#CC0000">' . strip_tags( $info_title ) . '</strong> <strong><a href="http://www.webtechideas.com/product/wti-like-post-pro/" target="_blank">' . strip_tags( $info_text, '<br><a><strong><em><span>' ) . '</a></strong></div>';
}

add_filter('plugin_action_links', 'WtiLikePostPluginLinks', 10, 2);

/**
 * Create the settings link for this plugin
 * @param $links array
 * @param $file string
 * @return $links array
 */
function WtiLikePostPluginLinks($links, $file) {
     static $this_plugin;

     if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
     }

     if ($file == $this_plugin) {
		$settings_link = '<a href="' . admin_url('options-general.php?page=WtiLikePostAdminMenu') . '">' . __('Settings', 'wti-like-post') . '</a>';
		array_unshift($links, $settings_link);
     }

     return $links;
}

register_activation_hook(__FILE__, 'SetOptionsWtiLikePost');

/**
 * Basic options function for the plugin settings
 * @param no-param
 * @return void
 */
function SetOptionsWtiLikePost() {
     global $wpdb, $wti_like_post_db_version;

     // Creating the like post table on activating the plugin
     $wti_like_post_table_name = $wpdb->prefix . "wti_like_post";
	
     if ($wpdb->get_var("show tables like '$wti_like_post_table_name'") != $wti_like_post_table_name) {
		$sql = "CREATE TABLE " . $wti_like_post_table_name . " (
			`id` bigint(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			`value` int(2) NOT NULL,
			`date_time` datetime NOT NULL,
			`ip` varchar(40) NOT NULL,
			`user_id` int(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		)";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
     }
	
     // Adding options for the like post plugin
     add_option('wti_like_post_drop_settings_table', '0', '', 'yes');
     add_option('wti_like_post_voting_period', '0', '', 'yes');
     add_option('wti_like_post_voting_style', 'style1', '', 'yes');
     add_option('wti_like_post_alignment', 'left', '', 'yes');
     add_option('wti_like_post_position', 'bottom', '', 'yes');
     add_option('wti_like_post_login_required', '0', '', 'yes');
     add_option('wti_like_post_login_message', __('Please login to vote.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_thank_message', __('Thanks for your vote.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_voted_message', __('You have already voted.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_allowed_posts', '', '', 'yes');
     add_option('wti_like_post_excluded_posts', '', '', 'yes');
     add_option('wti_like_post_excluded_categories', '', '', 'yes');
     add_option('wti_like_post_excluded_sections', '', '', 'yes');
     add_option('wti_like_post_show_on_pages', '0', '', 'yes');
     add_option('wti_like_post_show_on_widget', '1', '', 'yes');
     add_option('wti_like_post_show_symbols', '1', '', 'yes');
     add_option('wti_like_post_show_dislike', '1', '', 'yes');
     add_option('wti_like_post_title_text', 'Like/Unlike', '', 'yes');
     add_option('wti_like_post_db_version', $wti_like_post_db_version, '', 'yes');
}

/**
 * For dropping the table and removing options
 * @param no-param
 * @return no-return
 */
function UnsetOptionsWtiLikePost() {
     global $wpdb;

	// Check the option whether to drop the table on plugin uninstall or not
	$drop_settings_table = get_option('wti_like_post_drop_settings_table');
	
	if ($drop_settings_table == 1) {
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wti_like_post");
	
		// Deleting the added options on plugin uninstall
		delete_option('wti_like_post_drop_settings_table');
		delete_option('wti_like_post_voting_period');
		delete_option('wti_like_post_voting_style');
		delete_option('wti_like_post_alignment');
		delete_option('wti_like_post_position');
		delete_option('wti_like_post_login_required');
		delete_option('wti_like_post_login_message');
		delete_option('wti_like_post_thank_message');
		delete_option('wti_like_post_voted_message');
		delete_option('wti_like_post_db_version');
		delete_option('wti_like_post_allowed_posts');
		delete_option('wti_like_post_excluded_posts');
		delete_option('wti_like_post_excluded_categories');
		delete_option('wti_like_post_excluded_sections');
		delete_option('wti_like_post_show_on_pages');
		delete_option('wti_like_post_show_on_widget');
		delete_option('wti_like_post_show_symbols');
		delete_option('wti_like_post_show_dislike');
		delete_option('wti_like_post_title_text');
	}
}

register_uninstall_hook(__FILE__, 'UnsetOptionsWtiLikePost');

function WtiLikePostAdminRegisterSettings() {
     // Registering the settings
     register_setting('wti_like_post_options', 'wti_like_post_drop_settings_table');
     register_setting('wti_like_post_options', 'wti_like_post_voting_period');
     register_setting('wti_like_post_options', 'wti_like_post_voting_style');
     register_setting('wti_like_post_options', 'wti_like_post_alignment');
     register_setting('wti_like_post_options', 'wti_like_post_position');
     register_setting('wti_like_post_options', 'wti_like_post_login_required');
     register_setting('wti_like_post_options', 'wti_like_post_login_message');
     register_setting('wti_like_post_options', 'wti_like_post_thank_message');
     register_setting('wti_like_post_options', 'wti_like_post_voted_message');
     register_setting('wti_like_post_options', 'wti_like_post_allowed_posts');
     register_setting('wti_like_post_options', 'wti_like_post_excluded_posts');
     register_setting('wti_like_post_options', 'wti_like_post_excluded_categories');
     register_setting('wti_like_post_options', 'wti_like_post_excluded_sections');
     register_setting('wti_like_post_options', 'wti_like_post_show_on_pages');
     register_setting('wti_like_post_options', 'wti_like_post_show_on_widget');
     register_setting('wti_like_post_options', 'wti_like_post_db_version');	
     register_setting('wti_like_post_options', 'wti_like_post_show_symbols');
     register_setting('wti_like_post_options', 'wti_like_post_show_dislike');
     register_setting('wti_like_post_options', 'wti_like_post_title_text');	
}

add_action('admin_init', 'WtiLikePostAdminRegisterSettings');

/**
 * Create the update function for this plugin
 * @param no-param
 * @return no-return
 */
function UpdateOptionsWtiLikePost() {
     global $wpdb, $wti_like_post_db_version;

	// Get current database version for this plugin
	$current_db_version = get_option('wti_like_post_db_version');
	
	if ($current_db_version != $wti_like_post_db_version) {
		// Increase column size to support IPv6
		$wpdb->query("ALTER TABLE `{$wpdb->prefix}wti_like_post` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
		
		$user_col = $wpdb->get_row("SHOW COLUMNS FROM {$wpdb->prefix}wti_like_post LIKE 'user_id'");
	
		if (count($user_col) == 0) {
			$wpdb->query("ALTER TABLE `{$wpdb->prefix}wti_like_post` ADD `user_id` INT NOT NULL DEFAULT '0'");
		}

		// Update the database version
		update_option('wti_like_post_db_version', $wti_like_post_db_version);
	}
}

add_action('plugins_loaded', 'UpdateOptionsWtiLikePost');

if (is_admin()) {
	// Include the file for loading plugin settings
	require_once('wti_like_post_admin.php');
} else {
	// Include the file for loading plugin settings for
	require_once('wti_like_post_site.php');

	// Load the js and css files
	add_action('init', 'WtiLikePostEnqueueScripts');
	add_action('wp_head', 'WtiLikePostAddHeaderLinks');
}

/**
 * Get the actual ip address
 * @param no-param
 * @return string
 */
function WtiGetRealIpAddress() {
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	} elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	} elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return $ip;
}

/**
 * Check whether user has already voted or not
 * @param $post_id integer
 * @param $ip string
 * @return integer
 */
function HasWtiAlreadyVoted($post_id, $ip = null) {
	global $wpdb;
	
	if (null == $ip) {
		$ip = WtiGetRealIpAddress();
	}
	
	$wti_has_voted = $wpdb->get_var("SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND ip = '$ip'");
	
	return $wti_has_voted;
}

/**
 * Get last voted date for a given post by ip
 * @param $post_id integer
 * @param $ip string
 * @return string
 */
function GetWtiLastVotedDate($post_id, $ip = null) {
     global $wpdb;
     
     if (null == $ip) {
          $ip = WtiGetRealIpAddress();
     }
     
     $wti_has_voted = $wpdb->get_var("SELECT date_time FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND ip = '$ip'");

     return $wti_has_voted;
}

/**
 * Get next vote date for a given user
 * @param $last_voted_date string
 * @param $voting_period integer
 * @return string
 */
function GetWtiNextVoteDate($last_voted_date, $voting_period) {
     switch($voting_period) {
          case "1":
               $day = 1;
               break;
          case "2":
               $day = 2;
               break;
          case "3":
               $day = 3;
               break;
          case "7":
               $day = 7;
               break;
          case "14":
               $day = 14;
               break;
          case "21":
               $day = 21;
               break;
          case "1m":
               $month = 1;
               break;
          case "2m":
               $month = 2;
               break;
          case "3m":
               $month = 3;
               break;
          case "6m":
               $month = 6;
               break;
          case "1y":
               $year = 1;
            break;
     }
     
     $last_strtotime = strtotime($last_voted_date);
     $next_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
                    date('m', $last_strtotime) + $month, date('d', $last_strtotime) + $day, date('Y', $last_strtotime) + $year);
     
     $next_voting_date = date('Y-m-d H:i:s', $next_strtotime);
     
     return $next_voting_date;
}

/**
 * Get last voted date as per voting period
 * @param $post_id integer
 * @return string
 */
function GetWtiLastDate($voting_period) {
     switch($voting_period) {
          case "1":
               $day = 1;
               break;
          case "2":
               $day = 2;
               break;
          case "3":
               $day = 3;
               break;
          case "7":
               $day = 7;
               break;
          case "14":
               $day = 14;
               break;
          case "21":
               $day = 21;
               break;
          case "1m":
               $month = 1;
               break;
          case "2m":
               $month = 2;
               break;
          case "3m":
               $month = 3;
               break;
          case "6m":
               $month = 6;
               break;
          case "1y":
               $year = 1;
            break;
     }
     
     $last_strtotime = strtotime(date('Y-m-d H:i:s'));
     $last_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
                    date('m', $last_strtotime) - $month, date('d', $last_strtotime) - $day, date('Y', $last_strtotime) - $year);
     
     $last_voting_date = date('Y-m-d H:i:s', $last_strtotime);
     
     return $last_voting_date;
}

/**
 * Get like count for a post
 * @param $post_id integer
 * @return string
 */
function GetWtiLikeCount($post_id) {
	global $wpdb;
	$show_symbols = get_option('wti_like_post_show_symbols');
	$wti_like_count = $wpdb->get_var("SELECT SUM(value) FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND value >= 0");
	
	if (!$wti_like_count) {
		$wti_like_count = 0;
	} else {
		if ($show_symbols) {
			$wti_like_count = "+" . $wti_like_count;
		} else {
			$wti_like_count = $wti_like_count;
		}
	}
	
	return $wti_like_count;
}

/**
 * Get unlike count for a post
 * @param $post_id integer
 * @return string
 */
function GetWtiUnlikeCount($post_id) {
	global $wpdb;
	$show_symbols = get_option('wti_like_post_show_symbols');
	$wti_unlike_count = $wpdb->get_var("SELECT SUM(value) FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND value <= 0");
	
	if (!$wti_unlike_count) {
		$wti_unlike_count = 0;
	} else {
		if ($show_symbols) {
		} else {
			$wti_unlike_count = str_replace('-', '', $wti_unlike_count);
		}
	}
	
	return $wti_unlike_count;
}

// Load the widgets
require_once('wti_like_post_widgets.php');

// Include the file for ajax calls
require_once('wti_like_post_ajax.php');

// Associate the respective functions with the ajax call
add_action('wp_ajax_wti_like_post_process_vote', 'WtiLikePostProcessVote');
add_action('wp_ajax_nopriv_wti_like_post_process_vote', 'WtiLikePostProcessVote');