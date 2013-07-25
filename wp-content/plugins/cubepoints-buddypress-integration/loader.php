<?php
/*
Plugin Name: CubePoints Buddypress Integration
Plugin URI: http://wordpress.org/extend/plugins/cubepoints-buddypress-integration/
Description: Adds CubePoints support to Buddypress. Reward members using your BuddyPress portion of your website by giving them points and awards!
Version: 1.9.8.9
Revision Date: Sep 14, 2012
Requires at least: WP 3.1, BuddyPress 1.2.5.2, Cubepoints 3.0.1
Tested up to: WP 3.4.1, BuddyPress 1.6.1, CubePoints 3.1.1
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Tosh Hatch
Author URI: http://www.SlySpyder.com
Contributors: @xberserker
Network: false
*/

define ( 'BP_CUBEPOINT_VERSION', '1.9.8.9' );

define('CP_BUDDYPRESS_PATH', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));

if(in_array('cubepoints/cubepoints.php',apply_filters('active_plugins',get_option('active_plugins')))){

require_once( WP_PLUGIN_DIR . '/cubepoints/cp_core.php' );

cp_module_register('BuddyPress Integration' , 'cpbpintegration' ,
                   BP_CUBEPOINT_VERSION, 'Tosh Hatch', 'http://www.SlySpyder.com',
                   'http://buddypress.org/community/groups/cubepoints-buddypress-integration/activity/' ,
                   'Adds CubePoints support to Buddypress. Reward members using your BuddyPress portion of your website by giving them points and awards!',
                   false);
}

/**
 * Attaches plugin to Buddypress and starts the BuddyPress CubePoints core.
 *
 * This function is REQUIRED to prevent WordPress from white-screening if plugin is activated on a
 * system that does not have an active copy of BuddyPress.
 *
 */
function bp_cubepoint_init() {
	
	require( dirname( __FILE__ ) . '/includes/bp-cubepoint-core.php' );
	
	do_action('bp_cubepoint_init');
}

if(function_exists('cp_ready')){
	add_action( 'bp_include', 'bp_cubepoint_init' );
}

function bp_cubepoint_install(){

}

register_activation_hook( __FILE__, 'bp_cubepoint_install' );

function bp_cubepoint_activate() {
	add_option('bp_cubepoint_per_page', 20);
	add_option('bp_create_group_add_cp_bp', 100);
	add_option('bp_delete_group_add_cp_bp', -100);
	add_option('bp_join_group_add_cp_bp', -25);
	add_option('bp_leave_group_add_cp_bp', 25);	
	add_option('bp_update_post_add_cp_bp', 5);
	add_option('bp_update_comment_add_cp_bp', 5);
	add_option('bp_update_group_add_cp_bp', 5);
	add_option('bp_delete_comment_add_cp_bp', -5);
	add_option('bp_friend_add_cp_bp', 15);
	add_option('bp_friend_delete_add_cp_bp', -15);
	add_option('bp_forum_new_topic_add_cp_bp', 20);
	add_option('bp_forum_new_post_add_cp_bp', 5);
	add_option('bp_group_avatar_add_cp_bp', 5);
	add_option('bp_avatar_add_cp_bp', 5);
	add_option('bp_pm_cp_bp', 1);
	add_option('bp_bplink_add_cp_bp', 0);
	add_option('bp_bplink_vote_add_cp_bp', 0);
	add_option('bp_bplink_comment_add_cp_bp', 0);
	add_option('bp_bplink_delete_add_cp_bp', 0);
	add_option('bp_gift_given_cp_bp', 0);
	add_option('bp_gallery_upload_cp_bp', 0);
	add_option('bp_gallery_delete_cp_bp', 0);
	add_option('bp_spammer_cp_bp', '');
	add_option('bp_slug_cp_bp', 'cubepoints');
	add_option('bp_points_logs_per_page_cp_bp', 20);
	add_option('bp_tallyuserpoints_cp_bp', false);
	add_option('bp_sitewidemtitle_cp_bp', 'Sitewide Points');
	add_option('bp_earnpoints_menutitle_cp_bp', 'Point Legend');
	add_option('bp_sitewide_menu_cp_bp', true);	
	add_option('bp_earnpoints_menu_cp_bp', true);
	add_option('bp_earnpointstitle_cp_bp', 'Here is how you can earn points');
	add_option('bp_earnpoints_extra_cp_bp', '');
	add_option('bp_leaderboard_onoff_cp_bp', true);
	add_option('bp_leaderboardtitle_cp_bp', 'Top 15 Members');
	add_option('bp_leaderboard_cp_bp', 15);
	// Award Menu Options
	add_option('bp_awards_menutitle_cp_bp', 'Awards');
	add_option('bp_awards_menu_onoff_cp_bp', false);
	// Awards
	add_option('bp_award_groupimg_cp_bp', '');
	add_option('bp_award_grouptitle_cp_bp', '');
	add_option('bp_award_groupvalue_cp_bp', 0);
	add_option('bp_award_group2img_cp_bp', '');
	add_option('bp_award_group2title_cp_bp', '');
	add_option('bp_award_group2value_cp_bp', 0);
	add_option('bp_award_group3img_cp_bp', '');
	add_option('bp_award_group3title_cp_bp', '');
	add_option('bp_award_group3value_cp_bp', 0);
	add_option('bp_award_group4img_cp_bp', '');
	add_option('bp_award_group4title_cp_bp', '');
	add_option('bp_award_group4value_cp_bp', 0);
	add_option('bp_award_group5img_cp_bp', '');
	add_option('bp_award_group5title_cp_bp', '');
	add_option('bp_award_group5value_cp_bp', 0);
	add_option('bp_award_friendimg_cp_bp', '');
	add_option('bp_award_friendtitle_cp_bp', '');
	add_option('bp_award_friendvalue_cp_bp', 0);
	add_option('bp_award_friend2mg_cp_bp', '');
	add_option('bp_award_friend2title_cp_bp', '');
	add_option('bp_award_friend2value_cp_bp', 0);
	add_option('bp_award_friend3img_cp_bp', '');
	add_option('bp_award_friend3title_cp_bp', '');
	add_option('bp_award_friend3value_cp_bp', 0);
	add_option('bp_award_friend4img_cp_bp', '');
	add_option('bp_award_friend4title_cp_bp', '');
	add_option('bp_award_friend4value_cp_bp', 0);
	add_option('bp_award_friend5img_cp_bp', '');
	add_option('bp_award_friend5title_cp_bp', '');
	add_option('bp_award_friend5value_cp_bp', 0);
	add_option('bp_award_updateimg_cp_bp', '');
	add_option('bp_award_updatetitle_cp_bp', '');
	add_option('bp_award_updatevalue_cp_bp', 0);
	add_option('bp_award_update2img_cp_bp', '');
	add_option('bp_award_update2title_cp_bp', '');
	add_option('bp_award_update2value_cp_bp', 0);
	add_option('bp_award_update3img_cp_bp', '');
	add_option('bp_award_update3title_cp_bp', '');
	add_option('bp_award_update3value_cp_bp', 0);
	add_option('bp_award_update4img_cp_bp', '');
	add_option('bp_award_update4title_cp_bp', '');
	add_option('bp_award_update4value_cp_bp', 0);
	add_option('bp_award_update5img_cp_bp', '');
	add_option('bp_award_update5title_cp_bp', '');
	add_option('bp_award_update5value_cp_bp', 0);
	add_option('bp_award_replyimg_cp_bp', '');
	add_option('bp_award_replytitle_cp_bp', '');
	add_option('bp_award_replyvalue_cp_bp', 0);
	add_option('bp_award_reply2img_cp_bp', '');
	add_option('bp_award_reply2title_cp_bp', '');
	add_option('bp_award_reply2value_cp_bp', 0);
	add_option('bp_award_reply3img_cp_bp', '');
	add_option('bp_award_reply3title_cp_bp', '');
	add_option('bp_award_reply3value_cp_bp', 0);
	add_option('bp_award_reply4img_cp_bp', '');
	add_option('bp_award_reply4title_cp_bp', '');
	add_option('bp_award_reply4value_cp_bp', 0);
	add_option('bp_award_reply5img_cp_bp', '');
	add_option('bp_award_reply5title_cp_bp', '');
	add_option('bp_award_reply5value_cp_bp', 0);
	add_option('bp_award_forumtopicimg_cp_bp', '');
	add_option('bp_award_forumtopictitle_cp_bp', '');
	add_option('bp_award_forumtopicvalue_cp_bp', 0);
	add_option('bp_award_forumtopic2img_cp_bp', '');
	add_option('bp_award_forumtopic2title_cp_bp', '');
	add_option('bp_award_forumtopic2value_cp_bp', 0);
	add_option('bp_award_forumtopic3img_cp_bp', '');
	add_option('bp_award_forumtopic3title_cp_bp', '');
	add_option('bp_award_forumtopic3value_cp_bp', 0);
	add_option('bp_award_forumtopic4img_cp_bp', '');
	add_option('bp_award_forumtopic4title_cp_bp', '');
	add_option('bp_award_forumtopic4value_cp_bp', 0);
	add_option('bp_award_forumtopic5img_cp_bp', '');
	add_option('bp_award_forumtopic5title_cp_bp', '');
	add_option('bp_award_forumtopic5value_cp_bp', 0);
	add_option('bp_award_forumreplyimg_cp_bp', '');
	add_option('bp_award_forumreplytitle_cp_bp', '');
	add_option('bp_award_forumreplyvalue_cp_bp', 0);
	add_option('bp_award_forumreply2img_cp_bp', '');
	add_option('bp_award_forumreply2title_cp_bp', '');
	add_option('bp_award_forumreply2value_cp_bp', 0);
	add_option('bp_award_forumreply3img_cp_bp', '');
	add_option('bp_award_forumreply3title_cp_bp', '');
	add_option('bp_award_forumreply3value_cp_bp', 0);
	add_option('bp_award_forumreply4img_cp_bp', '');
	add_option('bp_award_forumreply4title_cp_bp', '');
	add_option('bp_award_forumreply4value_cp_bp', 0);
	add_option('bp_award_forumreply5img_cp_bp', '');
	add_option('bp_award_forumreply5title_cp_bp', '');
	add_option('bp_award_forumreply5value_cp_bp', 0);
	add_option('bp_award_blogcommentimg_cp_bp', '');
	add_option('bp_award_blogcommenttitle_cp_bp', '');
	add_option('bp_award_blogcommentvalue_cp_bp', 0);
	add_option('bp_award_blogcomment2img_cp_bp', '');
	add_option('bp_award_blogcomment2title_cp_bp', '');
	add_option('bp_award_blogcomment2value_cp_bp', 0);
	add_option('bp_award_blogcomment3img_cp_bp', '');
	add_option('bp_award_blogcomment3title_cp_bp', '');
	add_option('bp_award_blogcomment3value_cp_bp', 0);
	add_option('bp_award_blogcomment4img_cp_bp', '');
	add_option('bp_award_blogcomment4title_cp_bp', '');
	add_option('bp_award_blogcomment4value_cp_bp', 0);
	add_option('bp_award_blogcomment5img_cp_bp', '');
	add_option('bp_award_blogcomment5title_cp_bp', '');
	add_option('bp_award_blogcomment5value_cp_bp', 0);
	add_option('bp_award_bloggerimg_cp_bp', '');
	add_option('bp_award_bloggertitle_cp_bp', '');
	add_option('bp_award_bloggervalue_cp_bp', 0);
	add_option('bp_award_blogger2img_cp_bp', '');
	add_option('bp_award_blogger2title_cp_bp', '');
	add_option('bp_award_blogger2value_cp_bp', 0);
	add_option('bp_award_blogger3img_cp_bp', '');
	add_option('bp_award_blogger3title_cp_bp', '');
	add_option('bp_award_blogger3value_cp_bp', 0);
	add_option('bp_award_blogger4img_cp_bp', '');
	add_option('bp_award_blogger4title_cp_bp', '');
	add_option('bp_award_blogger4value_cp_bp', 0);
	add_option('bp_award_blogger5img_cp_bp', '');
	add_option('bp_award_blogger5title_cp_bp', '');
	add_option('bp_award_blogger5value_cp_bp', 0);
	add_option('bp_award_donationsimg_cp_bp', '');
	add_option('bp_award_donationstitle_cp_bp', '');
	add_option('bp_award_donationsvalue_cp_bp', 0);
	add_option('bp_award_donations2img_cp_bp', '');
	add_option('bp_award_donations2title_cp_bp', '');
	add_option('bp_award_donations2value_cp_bp', 0);
	add_option('bp_award_donations3img_cp_bp', '');
	add_option('bp_award_donations3title_cp_bp', '');
	add_option('bp_award_donations3value_cp_bp', 0);
	add_option('bp_award_donations4img_cp_bp', '');
	add_option('bp_award_donations4title_cp_bp', '');
	add_option('bp_award_donations4value_cp_bp', 0);
	add_option('bp_award_donations5img_cp_bp', '');
	add_option('bp_award_donations5title_cp_bp', '');
	add_option('bp_award_donations5value_cp_bp', 0);
	add_option('bp_award_dailyloginimg_cp_bp', '');
	add_option('bp_award_dailylogintitle_cp_bp', '');
	add_option('bp_award_dailyloginvalue_cp_bp', 0);
	add_option('bp_award_dailylogin2img_cp_bp', '');
	add_option('bp_award_dailylogin2title_cp_bp', '');
	add_option('bp_award_dailylogin2value_cp_bp', 0);
	add_option('bp_award_dailylogin3img_cp_bp', '');
	add_option('bp_award_dailylogin3title_cp_bp', '');
	add_option('bp_award_dailylogin3value_cp_bp', 0);
	add_option('bp_award_dailylogin4img_cp_bp', '');
	add_option('bp_award_dailylogin4title_cp_bp', '');
	add_option('bp_award_dailylogin4value_cp_bp', 0);
	add_option('bp_award_dailylogin5img_cp_bp', '');
	add_option('bp_award_dailylogin5title_cp_bp', '');
	add_option('bp_award_dailylogin5value_cp_bp', 0);
	// Blog Category
	add_option('bp_award_bloggercatimg_cp_bp', '');
	add_option('bp_award_bloggercattitle_cp_bp', '');
	add_option('bp_award_bloggercatvalue_cp_bp', 0);
	add_option('bp_award_bloggercat2img_cp_bp', '');
	add_option('bp_award_bloggercat2title_cp_bp', '');
	add_option('bp_award_bloggercat2value_cp_bp', 0);
	add_option('bp_award_bloggercat3img_cp_bp', '');
	add_option('bp_award_bloggercat3title_cp_bp', '');
	add_option('bp_award_bloggercat3value_cp_bp', 0);
	add_option('bp_award_bloggercat4img_cp_bp', '');
	add_option('bp_award_bloggercat4title_cp_bp', '');
	add_option('bp_award_bloggercat4value_cp_bp', 0);
	add_option('bp_award_bloggercat5img_cp_bp', '');
	add_option('bp_award_bloggercat5title_cp_bp', '');
	add_option('bp_award_bloggercat5value_cp_bp', 0);
	add_option('bp_award_bloggercatselector_cp_bp', 0);
	add_option('bp_award_bloggercatselector2_cp_bp', 0);
	add_option('bp_award_bloggercatselector3_cp_bp', 0);
	add_option('bp_award_bloggercatselector4_cp_bp', 0);
	add_option('bp_award_bloggercatselector5_cp_bp', 0);
	// Awards for point levels
	add_option('bp_award_points_1img_cp_bp', '');
	add_option('bp_award_points_1title_cp_bp', '');
	add_option('bp_award_points_1value_cp_bp', 0);
	add_option('bp_award_points_2img_cp_bp', '');
	add_option('bp_award_points_2title_cp_bp', '');
	add_option('bp_award_points_2value_cp_bp', 0);
	add_option('bp_award_points_3img_cp_bp', '');
	add_option('bp_award_points_3title_cp_bp', '');
	add_option('bp_award_points_3value_cp_bp', 0);
	add_option('bp_award_points_4img_cp_bp', '');
	add_option('bp_award_points_4title_cp_bp', '');
	add_option('bp_award_points_4value_cp_bp', 0);
	add_option('bp_award_points_5img_cp_bp', '');
	add_option('bp_award_points_5title_cp_bp', '');
	add_option('bp_award_points_5value_cp_bp', 0);
	add_option('bp_award_points_6img_cp_bp', '');
	add_option('bp_award_points_6title_cp_bp', '');
	add_option('bp_award_points_6value_cp_bp', 0);
	add_option('bp_award_points_7img_cp_bp', '');
	add_option('bp_award_points_7title_cp_bp', '');
	add_option('bp_award_points_7value_cp_bp', 0);
	add_option('bp_award_points_8img_cp_bp', '');
	add_option('bp_award_points_8title_cp_bp', '');
	add_option('bp_award_points_8value_cp_bp', 0);
	add_option('bp_award_points_9img_cp_bp', '');
	add_option('bp_award_points_9title_cp_bp', '');
	add_option('bp_award_points_9value_cp_bp', 0);
	add_option('bp_award_points_10img_cp_bp', '');
	add_option('bp_award_points_10title_cp_bp', '');
	add_option('bp_award_points_10value_cp_bp', 0);
	// Simple Press Forum Support	
	add_option('bp_award_spf_forumimg_cp_bp', '');
	add_option('bp_award_spf_forumtitle_cp_bp', '');
	add_option('bp_award_spf_forumvalue_cp_bp', 0);
	add_option('bp_award_spf_forum2img_cp_bp', '');
	add_option('bp_award_spf_forum2title_cp_bp', '');
	add_option('bp_award_spf_forum2value_cp_bp', 0);
	add_option('bp_award_spf_forum3img_cp_bp', '');
	add_option('bp_award_spf_forum3title_cp_bp', '');
	add_option('bp_award_spf_forum3value_cp_bp', 0);
	add_option('bp_award_spf_forum4img_cp_bp', '');
	add_option('bp_award_spf_forum4title_cp_bp', '');
	add_option('bp_award_spf_forum4value_cp_bp', 0);
	add_option('bp_award_spf_forum5img_cp_bp', '');
	add_option('bp_award_spf_forum5title_cp_bp', '');
	add_option('bp_award_spf_forum5value_cp_bp', 0);
	// Turn off award sections	
	add_option('bp_spf_support_onoff_cp_bp', false);
	// Earned all awards
	add_option('bp_award_earnedall_img_cp_bp', '');
	add_option('bp_award_earnedall_title_cp_bp', '');
	add_option('bp_messagespamcheck_cp_bp', 0);
	add_option('bp_groupcreatespamcheck_cp_bp', 0);
	add_option('bp_update_n_reply_spamcheck_cp_bp', 0);
	//Lottery Tie In
	add_option('bp_lottery1_open_cp_bp', '');
	add_option('bp_lottery1_entered_cp_bp', '');
	add_option('bp_lottery1_url_cp_bp', '');
	add_option('bp_lottery2_open_cp_bp', '');
	add_option('bp_lottery2_entered_cp_bp', '');
	add_option('bp_lottery2_url_cp_bp', '');
	add_option('bp_lottery3_open_cp_bp', '');
	add_option('bp_lottery3_entered_cp_bp', '');
	add_option('bp_lottery3_url_cp_bp', '');
	add_option('bp_lottery4_open_cp_bp', '');
	add_option('bp_lottery4_entered_cp_bp', '');
	add_option('bp_lottery4_url_cp_bp', '');
	add_option('bp_lottery5_open_cp_bp', '');
	add_option('bp_lottery5_entered_cp_bp', '');
	add_option('bp_lottery5_url_cp_bp', '');
	add_option('bp_bet1_open_cp_bp', '');
	add_option('bp_bet1_entered_cp_bp', '');
	add_option('bp_bet1_url_cp_bp', '');
	add_option('bp_bet2_open_cp_bp', '');
	add_option('bp_bet2_entered_cp_bp', '');
	add_option('bp_bet2_url_cp_bp', '');
	add_option('bp_bet3_open_cp_bp', '');
	add_option('bp_bet3_entered_cp_bp', '');
	add_option('bp_bet3_url_cp_bp', '');
	add_option('bp_bet4_open_cp_bp', '');
	add_option('bp_bet4_entered_cp_bp', '');
	add_option('bp_bet4_url_cp_bp', '');
	add_option('bp_bet5_open_cp_bp', '');
	add_option('bp_bet5_entered_cp_bp', '');
	add_option('bp_bet5_url_cp_bp', '');
	//bbPress 2.0
	add_option('bp_cp_bbpress2_new_topic', 20);
	add_option('bp_cp_bbpress2_new_reply', 5);
}
register_activation_hook( __FILE__, 'bp_cubepoint_activate' );

function bp_cubepoint_deactivate() {
	/*
	delete_option('bp_cubepoint_per_page');
	delete_option('bp_create_group_add_cp_bp');
	delete_option('bp_delete_group_add_cp_bp');
	delete_option('bp_join_group_add_cp_bp');
	delete_option('bp_leave_group_add_cp_bp');
	delete_option('bp_update_post_add_cp_bp');
	delete_option('bp_update_comment_add_cp_bp');
	delete_option('bp_update_group_add_cp_bp');
	delete_option('bp_delete_comment_add_cp_bp');
	delete_option('bp_friend_add_cp_bp');
	delete_option('bp_friend_delete_add_cp_bp');
	delete_option('bp_forum_new_topic_add_cp_bp');
	delete_option('bp_forum_new_post_add_cp_bp');
	delete_option('bp_group_avatar_add_cp_bp');
	delete_option('bp_avatar_add_cp_bp');
	delete_option('bp_pm_cp_bp');
	delete_option('bp_bplink_add_cp_bp');
	delete_option('bp_bplink_vote_add_cp_bp');
	delete_option('bp_bplink_comment_add_cp_bp');
	delete_option('bp_bplink_delete_add_cp_bp');
	delete_option('bp_gift_given_cp_bp');
	delete_option('bp_gallery_upload_cp_bp');		
	delete_option('bp_gallery_delete_cp_bp');
	delete_option('bp_spammer_cp_bp');
	delete_option('bp_slug_cp_bp');
	delete_option('bp_points_logs_per_page_cp_bp');
	delete_option('bp_sitewide_menu_cp_bp');
	delete_option('bp_sitewidemtitle_cp_bp');
	delete_option('bp_earnpoints_menutitle_cp_bp');
	delete_option('bp_earnpoints_menu_cp_bp');	
	delete_option('bp_earnpointstitle_cp_bp');
	delete_option('bp_earnpoints_extra_cp_bp');
	delete_option('bp_leaderboard_onoff_cp_bp');
	delete_option('bp_leaderboardtitle_cp_bp');
	delete_option('bp_leaderboard_cp_bp');
	// Award Menu Options
	delete_option('bp_awards_menutitle_cp_bp');
	delete_option('bp_awards_menu_onoff_cp_bp');
	// Awards
	delete_option('bp_award_groupimg_cp_bp');
	delete_option('bp_award_grouptitle_cp_bp');
	delete_option('bp_award_groupvalue_cp_bp');
	delete_option('bp_award_group2img_cp_bp');
	delete_option('bp_award_group2title_cp_bp');
	delete_option('bp_award_group2value_cp_bp');
	delete_option('bp_award_group3img_cp_bp');
	delete_option('bp_award_group3title_cp_bp');
	delete_option('bp_award_group3value_cp_bp');
	delete_option('bp_award_group4img_cp_bp');
	delete_option('bp_award_group4title_cp_bp');
	delete_option('bp_award_group4value_cp_bp');
	delete_option('bp_award_group5img_cp_bp');
	delete_option('bp_award_group5title_cp_bp');
	delete_option('bp_award_group5value_cp_bp');	
	delete_option('bp_award_friendimg_cp_bp');
	delete_option('bp_award_friendtitle_cp_bp');
	delete_option('bp_award_friendvalue_cp_bp');
	delete_option('bp_award_friend2img_cp_bp');
	delete_option('bp_award_friend2title_cp_bp');
	delete_option('bp_award_friend2value_cp_bp');
	delete_option('bp_award_friend3img_cp_bp');
	delete_option('bp_award_friend3title_cp_bp');
	delete_option('bp_award_friend3value_cp_bp');
	delete_option('bp_award_friend4img_cp_bp');
	delete_option('bp_award_friend4title_cp_bp');
	delete_option('bp_award_friend4value_cp_bp');
	delete_option('bp_award_friend5img_cp_bp');
	delete_option('bp_award_friend5title_cp_bp');
	delete_option('bp_award_friend5value_cp_bp');
	delete_option('bp_award_updateimg_cp_bp');
	delete_option('bp_award_updatetitle_cp_bp');
	delete_option('bp_award_updatevalue_cp_bp');
	delete_option('bp_award_update2img_cp_bp');
	delete_option('bp_award_update2title_cp_bp');
	delete_option('bp_award_update2value_cp_bp');
	delete_option('bp_award_update3img_cp_bp');
	delete_option('bp_award_update3title_cp_bp');
	delete_option('bp_award_update3value_cp_bp');
	delete_option('bp_award_update4img_cp_bp');
	delete_option('bp_award_update4title_cp_bp');
	delete_option('bp_award_update4value_cp_bp');
	delete_option('bp_award_update5img_cp_bp');
	delete_option('bp_award_update5title_cp_bp');
	delete_option('bp_award_update5value_cp_bp');
	delete_option('bp_award_replyimg_cp_bp');
	delete_option('bp_award_replytitle_cp_bp');
	delete_option('bp_award_replyvalue_cp_bp');
	delete_option('bp_award_reply2img_cp_bp');
	delete_option('bp_award_reply2title_cp_bp');
	delete_option('bp_award_reply2value_cp_bp');
	delete_option('bp_award_reply3img_cp_bp');
	delete_option('bp_award_reply3title_cp_bp');
	delete_option('bp_award_reply3value_cp_bp');
	delete_option('bp_award_reply4img_cp_bp');
	delete_option('bp_award_reply4title_cp_bp');
	delete_option('bp_award_reply4value_cp_bp');
	delete_option('bp_award_reply5img_cp_bp');
	delete_option('bp_award_reply5title_cp_bp');
	delete_option('bp_award_reply5value_cp_bp');
	delete_option('bp_award_forumtopicimg_cp_bp');
	delete_option('bp_award_forumtopictitle_cp_bp');
	delete_option('bp_award_forumtopicvalue_cp_bp');
	delete_option('bp_award_forumtopic2img_cp_bp');
	delete_option('bp_award_forumtopic2title_cp_bp');
	delete_option('bp_award_forumtopic2value_cp_bp');
	delete_option('bp_award_forumtopic3img_cp_bp');
	delete_option('bp_award_forumtopic3title_cp_bp');
	delete_option('bp_award_forumtopic3value_cp_bp');
	delete_option('bp_award_forumtopic4img_cp_bp');
	delete_option('bp_award_forumtopic4title_cp_bp');
	delete_option('bp_award_forumtopic4value_cp_bp');
	delete_option('bp_award_forumtopic5img_cp_bp');
	delete_option('bp_award_forumtopic5title_cp_bp');
	delete_option('bp_award_forumtopic5value_cp_bp');
	delete_option('bp_award_forumreplyimg_cp_bp');
	delete_option('bp_award_forumreplytitle_cp_bp');
	delete_option('bp_award_forumreplyvalue_cp_bp');
	delete_option('bp_award_forumreply2img_cp_bp');
	delete_option('bp_award_forumreply2title_cp_bp');
	delete_option('bp_award_forumreply2value_cp_bp');
	delete_option('bp_award_forumreply3img_cp_bp');
	delete_option('bp_award_forumreply3title_cp_bp');
	delete_option('bp_award_forumreply3value_cp_bp');
	delete_option('bp_award_forumreply4img_cp_bp');
	delete_option('bp_award_forumreply4title_cp_bp');
	delete_option('bp_award_forumreply4value_cp_bp');
	delete_option('bp_award_forumreply5img_cp_bp');
	delete_option('bp_award_forumreply5title_cp_bp');
	delete_option('bp_award_forumreply5value_cp_bp');
	delete_option('bp_award_blogcommentimg_cp_bp');
	delete_option('bp_award_blogcommenttitle_cp_bp');
	delete_option('bp_award_blogcommentvalue_cp_bp');
	delete_option('bp_award_blogcomment2img_cp_bp');
	delete_option('bp_award_blogcomment2title_cp_bp');
	delete_option('bp_award_blogcomment2value_cp_bp');
	delete_option('bp_award_blogcomment3img_cp_bp');
	delete_option('bp_award_blogcomment3title_cp_bp');
	delete_option('bp_award_blogcomment3value_cp_bp');
	delete_option('bp_award_blogcomment4img_cp_bp');
	delete_option('bp_award_blogcomment4title_cp_bp');
	delete_option('bp_award_blogcomment4value_cp_bp');
	delete_option('bp_award_blogcomment5img_cp_bp');
	delete_option('bp_award_blogcomment5title_cp_bp');
	delete_option('bp_award_blogcomment5value_cp_bp');
	delete_option('bp_award_bloggerimg_cp_bp');
	delete_option('bp_award_bloggertitle_cp_bp');
	delete_option('bp_award_bloggervalue_cp_bp');
	delete_option('bp_award_blogger2img_cp_bp');
	delete_option('bp_award_blogger2title_cp_bp');
	delete_option('bp_award_blogger2value_cp_bp');
	delete_option('bp_award_blogger3img_cp_bp');
	delete_option('bp_award_blogger3title_cp_bp');
	delete_option('bp_award_blogger3value_cp_bp');
	delete_option('bp_award_blogger4img_cp_bp');
	delete_option('bp_award_blogger4title_cp_bp');
	delete_option('bp_award_blogger4value_cp_bp');
	delete_option('bp_award_blogger5img_cp_bp');
	delete_option('bp_award_blogger5title_cp_bp');
	delete_option('bp_award_blogger5value_cp_bp');
	delete_option('bp_award_donationsimg_cp_bp');
	delete_option('bp_award_donationstitle_cp_bp');
	delete_option('bp_award_donationsvalue_cp_bp');
	delete_option('bp_award_donations2img_cp_bp');
	delete_option('bp_award_donations2title_cp_bp');
	delete_option('bp_award_donations2value_cp_bp');
	delete_option('bp_award_donations3img_cp_bp');
	delete_option('bp_award_donations3title_cp_bp');
	delete_option('bp_award_donations3value_cp_bp');
	delete_option('bp_award_donations4img_cp_bp');
	delete_option('bp_award_donations4title_cp_bp');
	delete_option('bp_award_donations4value_cp_bp');
	delete_option('bp_award_donations5img_cp_bp');
	delete_option('bp_award_donations5title_cp_bp');
	delete_option('bp_award_donations5value_cp_bp');
	delete_option('bp_award_dailyloginimg_cp_bp');
	delete_option('bp_award_dailylogintitle_cp_bp');
	delete_option('bp_award_dailyloginvalue_cp_bp');
	delete_option('bp_award_dailylogin2img_cp_bp');
	delete_option('bp_award_dailylogin2title_cp_bp');
	delete_option('bp_award_dailylogin2value_cp_bp');
	delete_option('bp_award_dailylogin3img_cp_bp');
	delete_option('bp_award_dailylogin3title_cp_bp');
	delete_option('bp_award_dailylogin3value_cp_bp');
	delete_option('bp_award_dailylogin4img_cp_bp');
	delete_option('bp_award_dailylogin4title_cp_bp');
	delete_option('bp_award_dailylogin4value_cp_bp');
	delete_option('bp_award_dailylogin5img_cp_bp');
	delete_option('bp_award_dailylogin5title_cp_bp');
	delete_option('bp_award_dailylogin5value_cp_bp');	
	// Blog Category
	delete_option('bp_award_bloggercatimg_cp_bp');
	delete_option('bp_award_bloggercattitle_cp_bp');
	delete_option('bp_award_bloggercatvalue_cp_bp');
	delete_option('bp_award_bloggercat2img_cp_bp');
	delete_option('bp_award_bloggercat2title_cp_bp');
	delete_option('bp_award_bloggercat2value_cp_bp');
	delete_option('bp_award_bloggercat3img_cp_bp');
	delete_option('bp_award_bloggercat3title_cp_bp');
	delete_option('bp_award_bloggercat3value_cp_bp');
	delete_option('bp_award_bloggercat4img_cp_bp');
	delete_option('bp_award_bloggercat4title_cp_bp');
	delete_option('bp_award_bloggercat4value_cp_bp');
	delete_option('bp_award_bloggercat5img_cp_bp');
	delete_option('bp_award_bloggercat5title_cp_bp');
	delete_option('bp_award_bloggercat5value_cp_bp');
	delete_option('bp_award_bloggercatselector_cp_bp');
	delete_option('bp_award_bloggercatselector2_cp_bp');
	delete_option('bp_award_bloggercatselector3_cp_bp');
	delete_option('bp_award_bloggercatselector4_cp_bp');
	delete_option('bp_award_bloggercatselector5_cp_bp');
	// Awards for point levels
	delete_option('bp_award_points_1img_cp_bp');
	delete_option('bp_award_points_1title_cp_bp');
	delete_option('bp_award_points_1value_cp_bp');
	delete_option('bp_award_points_2img_cp_bp');
	delete_option('bp_award_points_2title_cp_bp');
	delete_option('bp_award_points_2value_cp_bp');
	delete_option('bp_award_points_3img_cp_bp');
	delete_option('bp_award_points_3title_cp_bp');
	delete_option('bp_award_points_3value_cp_bp');
	delete_option('bp_award_points_4img_cp_bp');
	delete_option('bp_award_points_4title_cp_bp');
	delete_option('bp_award_points_4value_cp_bp');
	delete_option('bp_award_points_5img_cp_bp');
	delete_option('bp_award_points_5title_cp_bp');
	delete_option('bp_award_points_5value_cp_bp');
	delete_option('bp_award_points_6img_cp_bp');
	delete_option('bp_award_points_6title_cp_bp');
	delete_option('bp_award_points_6value_cp_bp');
	delete_option('bp_award_points_7img_cp_bp');
	delete_option('bp_award_points_7title_cp_bp');
	delete_option('bp_award_points_7value_cp_bp');
	delete_option('bp_award_points_8img_cp_bp');
	delete_option('bp_award_points_8title_cp_bp');
	delete_option('bp_award_points_8value_cp_bp');
	delete_option('bp_award_points_9img_cp_bp');
	delete_option('bp_award_points_9title_cp_bp');
	delete_option('bp_award_points_9value_cp_bp');
	delete_option('bp_award_points_9img_cp_bp');
	delete_option('bp_award_points_10title_cp_bp');
	delete_option('bp_award_points_10value_cp_bp');
	// Simple Press Forum
	delete_option('bp_award_spf_forumimg_cp_bp');
	delete_option('bp_award_spf_forumtitle_cp_bp');
	delete_option('bp_award_spf_forumvalue_cp_bp');
	delete_option('bp_award_spf_forum2img_cp_bp');
	delete_option('bp_award_spf_forum2title_cp_bp');
	delete_option('bp_award_spf_forum2value_cp_bp');
	delete_option('bp_award_spf_forum3img_cp_bp');
	delete_option('bp_award_spf_forum3title_cp_bp');
	delete_option('bp_award_spf_forum3value_cp_bp');
	delete_option('bp_award_spf_forum4img_cp_bp');
	delete_option('bp_award_spf_forum4title_cp_bp');
	delete_option('bp_award_spf_forum4value_cp_bp');
	delete_option('bp_award_spf_forum5img_cp_bp');
	delete_option('bp_award_spf_forum5title_cp_bp');
	delete_option('bp_award_spf_forum5value_cp_bp');
	// Turn off award sections
	delete_option('bp_spf_support_onoff_cp_bp');	
	// Earned All Trophies
	delete_option('bp_award_earnedall_img_cp_bp');
	delete_option('bp_award_earnedall_title_cp_bp');
	delete_option('bp_messagespamcheck_cp_bp');
	*/
}
register_deactivation_hook( __FILE__, 'bp_cubepoint_deactivate' );

?>