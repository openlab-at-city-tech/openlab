<?php

function cubebp_admin() {
	if( isset($_POST['_wpnonce']) && isset($_POST['cp_bp_admin_form_submit']) ) {

		$bp_create_group_add_cp_bp = (int)$_POST['bp_create_group_add_cp_bp'];
		$bp_delete_group_add_cp_bp = (int)$_POST['bp_delete_group_add_cp_bp'];
		$bp_join_group_add_cp_bp = (int)$_POST['bp_join_group_add_cp_bp'];
		$bp_leave_group_add_cp_bp = (int)$_POST['bp_leave_group_add_cp_bp'];
		$bp_update_post_add_cp_bp = (int)$_POST['bp_update_post_add_cp_bp'];
		$bp_update_comment_add_cp_bp = (int)$_POST['bp_update_comment_add_cp_bp'];
		$bp_update_group_add_cp_bp = (int)$_POST['bp_update_group_add_cp_bp'];
		$bp_delete_comment_add_cp_bp = (int)$_POST['bp_delete_comment_add_cp_bp'];
		$bp_friend_add_cp_bp = (int)$_POST['bp_friend_add_cp_bp'];
		$bp_friend_delete_add_cp_bp = (int)$_POST['bp_friend_delete_add_cp_bp'];
		$bp_forum_new_topic_add_cp_bp = (int)$_POST['bp_forum_new_topic_add_cp_bp'];
		$bp_forum_new_post_add_cp_bp = (int)$_POST['bp_forum_new_post_add_cp_bp'];
		$bp_group_avatar_add_cp_bp = (int)$_POST['bp_group_avatar_add_cp_bp'];
		$bp_avatar_add_cp_bp = (int)$_POST['bp_avatar_add_cp_bp'];
		$bp_pm_cp_bp = (int)$_POST['bp_pm_cp_bp'];
		$bp_bplink_add_cp_bp = (int)$_POST['bp_bplink_add_cp_bp'];
		$bp_bplink_vote_add_cp_bp = (int)$_POST['bp_bplink_vote_add_cp_bp'];		
		$bp_bplink_comment_add_cp_bp = (int)$_POST['bp_bplink_comment_add_cp_bp'];
		$bp_bplink_delete_add_cp_bp = (int)$_POST['bp_bplink_delete_add_cp_bp'];
		$bp_gift_given_cp_bp = (int)$_POST['bp_gift_given_cp_bp'];
		$bp_gallery_upload_cp_bp = (int)$_POST['bp_gallery_upload_cp_bp'];
		$bp_gallery_delete_cp_bp = (int)$_POST['bp_gallery_delete_cp_bp'];
		$bp_spammer_cp_bp = $_POST['bp_spammer_cp_bp'];
		$bp_slug_cp_bp = $_POST['bp_slug_cp_bp'];
		$bp_points_logs_per_page_cp_bp = (int)$_POST['bp_points_logs_per_page_cp_bp'];
		$bp_tallyuserpoints_cp_bp = (bool)$_POST['bp_tallyuserpoints_cp_bp'];
		$bp_sitewide_menu_cp_bp = (bool)$_POST['bp_sitewide_menu_cp_bp'];
		$bp_sitewidemtitle_cp_bp = $_POST['bp_sitewidemtitle_cp_bp'];
		$bp_earnpoints_menu_cp_bp = (bool)$_POST['bp_earnpoints_menu_cp_bp'];
		$bp_earnpoints_menutitle_cp_bp = $_POST['bp_earnpoints_menutitle_cp_bp'];
		$bp_earnpointstitle_cp_bp = $_POST['bp_earnpointstitle_cp_bp'];
		$bp_earnpoints_extra_cp_bp = $_POST['bp_earnpoints_extra_cp_bp'];
		$bp_leaderboard_onoff_cp_bp = (bool)$_POST['bp_leaderboard_onoff_cp_bp'];		
		$bp_leaderboardtitle_cp_bp = $_POST['bp_leaderboardtitle_cp_bp'];
		$bp_leaderboard_cp_bp = (int)$_POST['bp_leaderboard_cp_bp'];
		// Awards Menu Options
		$bp_awards_menutitle_cp_bp = $_POST['bp_awards_menutitle_cp_bp'];
		$bp_awards_menu_onoff_cp_bp = (bool)$_POST['bp_awards_menu_onoff_cp_bp'];
		// Awards
		$bp_award_groupimg_cp_bp = $_POST['bp_award_groupimg_cp_bp'];
		$bp_award_grouptitle_cp_bp = $_POST['bp_award_grouptitle_cp_bp'];
		$bp_award_groupvalue_cp_bp = (int)$_POST['bp_award_groupvalue_cp_bp'];
		$bp_award_group2img_cp_bp = $_POST['bp_award_group2img_cp_bp'];
		$bp_award_group2title_cp_bp = $_POST['bp_award_group2title_cp_bp'];
		$bp_award_group2value_cp_bp = (int)$_POST['bp_award_group2value_cp_bp'];
		$bp_award_group3img_cp_bp = $_POST['bp_award_group3img_cp_bp'];
		$bp_award_group3title_cp_bp = $_POST['bp_award_group3title_cp_bp'];
		$bp_award_group3value_cp_bp = (int)$_POST['bp_award_group3value_cp_bp'];
		$bp_award_group4img_cp_bp = $_POST['bp_award_group4img_cp_bp'];
		$bp_award_group4title_cp_bp = $_POST['bp_award_group4title_cp_bp'];
		$bp_award_group4value_cp_bp = (int)$_POST['bp_award_group4value_cp_bp'];
		$bp_award_group5img_cp_bp = $_POST['bp_award_group5img_cp_bp'];
		$bp_award_group5title_cp_bp = $_POST['bp_award_group5title_cp_bp'];
		$bp_award_group5value_cp_bp = (int)$_POST['bp_award_group5value_cp_bp'];
		$bp_award_friendimg_cp_bp = $_POST['bp_award_friendimg_cp_bp'];
		$bp_award_friendtitle_cp_bp = $_POST['bp_award_friendtitle_cp_bp'];
		$bp_award_friendvalue_cp_bp = (int)$_POST['bp_award_friendvalue_cp_bp'];
		$bp_award_friend2img_cp_bp = $_POST['bp_award_friend2img_cp_bp'];
		$bp_award_friend2title_cp_bp = $_POST['bp_award_friend2title_cp_bp'];
		$bp_award_friend2value_cp_bp = (int)$_POST['bp_award_friend2value_cp_bp'];
		$bp_award_friend3img_cp_bp = $_POST['bp_award_friend3img_cp_bp'];
		$bp_award_friend3title_cp_bp = $_POST['bp_award_friend3title_cp_bp'];
		$bp_award_friend3value_cp_bp = (int)$_POST['bp_award_friend3value_cp_bp'];
		$bp_award_friend4img_cp_bp = $_POST['bp_award_friend4img_cp_bp'];
		$bp_award_friend4title_cp_bp = $_POST['bp_award_friend4title_cp_bp'];
		$bp_award_friend4value_cp_bp = (int)$_POST['bp_award_friend4value_cp_bp'];
		$bp_award_friend5img_cp_bp = $_POST['bp_award_friend5img_cp_bp'];
		$bp_award_friend5title_cp_bp = $_POST['bp_award_friend5title_cp_bp'];
		$bp_award_friend5value_cp_bp = (int)$_POST['bp_award_friend5value_cp_bp'];
		$bp_award_updateimg_cp_bp = $_POST['bp_award_updateimg_cp_bp'];
		$bp_award_updatetitle_cp_bp = $_POST['bp_award_updatetitle_cp_bp'];
		$bp_award_updatevalue_cp_bp = (int)$_POST['bp_award_updatevalue_cp_bp'];
		$bp_award_update2img_cp_bp = $_POST['bp_award_update2img_cp_bp'];
		$bp_award_update2title_cp_bp = $_POST['bp_award_update2title_cp_bp'];
		$bp_award_update2value_cp_bp = (int)$_POST['bp_award_update2value_cp_bp'];
		$bp_award_update3img_cp_bp = $_POST['bp_award_update3img_cp_bp'];
		$bp_award_update3title_cp_bp = $_POST['bp_award_update3title_cp_bp'];
		$bp_award_update3value_cp_bp = (int)$_POST['bp_award_update3value_cp_bp'];
		$bp_award_update4img_cp_bp = $_POST['bp_award_update4img_cp_bp'];
		$bp_award_update4title_cp_bp = $_POST['bp_award_update4title_cp_bp'];
		$bp_award_update4value_cp_bp = (int)$_POST['bp_award_update4value_cp_bp'];
		$bp_award_update5img_cp_bp = $_POST['bp_award_update5img_cp_bp'];
		$bp_award_update5title_cp_bp = $_POST['bp_award_update5title_cp_bp'];
		$bp_award_update5value_cp_bp = (int)$_POST['bp_award_update5value_cp_bp'];
		$bp_award_replyimg_cp_bp = $_POST['bp_award_replyimg_cp_bp'];
		$bp_award_replytitle_cp_bp = $_POST['bp_award_replytitle_cp_bp'];
		$bp_award_replyvalue_cp_bp = (int)$_POST['bp_award_replyvalue_cp_bp'];
		$bp_award_reply2img_cp_bp = $_POST['bp_award_reply2img_cp_bp'];
		$bp_award_reply2title_cp_bp = $_POST['bp_award_reply2title_cp_bp'];
		$bp_award_reply2value_cp_bp = (int)$_POST['bp_award_reply2value_cp_bp'];
		$bp_award_reply3img_cp_bp = $_POST['bp_award_reply3img_cp_bp'];
		$bp_award_reply3title_cp_bp = $_POST['bp_award_reply3title_cp_bp'];
		$bp_award_reply3value_cp_bp = (int)$_POST['bp_award_reply3value_cp_bp'];
		$bp_award_reply4img_cp_bp = $_POST['bp_award_reply4img_cp_bp'];
		$bp_award_reply4title_cp_bp = $_POST['bp_award_reply4title_cp_bp'];
		$bp_award_reply4value_cp_bp = (int)$_POST['bp_award_reply4value_cp_bp'];
		$bp_award_reply5img_cp_bp = $_POST['bp_award_reply5img_cp_bp'];
		$bp_award_reply5title_cp_bp = $_POST['bp_award_reply5title_cp_bp'];
		$bp_award_reply5value_cp_bp = (int)$_POST['bp_award_reply5value_cp_bp'];
		$bp_award_forumtopicimg_cp_bp = $_POST['bp_award_forumtopicimg_cp_bp'];
		$bp_award_forumtopictitle_cp_bp = $_POST['bp_award_forumtopictitle_cp_bp'];
		$bp_award_forumtopicvalue_cp_bp = (int)$_POST['bp_award_forumtopicvalue_cp_bp'];
		$bp_award_forumtopic2img_cp_bp = $_POST['bp_award_forumtopic2img_cp_bp'];
		$bp_award_forumtopic2title_cp_bp = $_POST['bp_award_forumtopic2title_cp_bp'];
		$bp_award_forumtopic2value_cp_bp = (int)$_POST['bp_award_forumtopic2value_cp_bp'];
		$bp_award_forumtopic3img_cp_bp = $_POST['bp_award_forumtopic3img_cp_bp'];
		$bp_award_forumtopic3title_cp_bp = $_POST['bp_award_forumtopic3title_cp_bp'];
		$bp_award_forumtopic3value_cp_bp = (int)$_POST['bp_award_forumtopic3value_cp_bp'];
		$bp_award_forumtopic4img_cp_bp = $_POST['bp_award_forumtopic4img_cp_bp'];
		$bp_award_forumtopic4title_cp_bp = $_POST['bp_award_forumtopic4title_cp_bp'];
		$bp_award_forumtopic4value_cp_bp = (int)$_POST['bp_award_forumtopic4value_cp_bp'];
		$bp_award_forumtopic5img_cp_bp = $_POST['bp_award_forumtopic5img_cp_bp'];
		$bp_award_forumtopic5title_cp_bp = $_POST['bp_award_forumtopic5title_cp_bp'];
		$bp_award_forumtopic5value_cp_bp = (int)$_POST['bp_award_forumtopic5value_cp_bp'];
		$bp_award_forumreplyimg_cp_bp = $_POST['bp_award_forumreplyimg_cp_bp'];
		$bp_award_forumreplytitle_cp_bp = $_POST['bp_award_forumreplytitle_cp_bp'];
		$bp_award_forumreplyvalue_cp_bp = (int)$_POST['bp_award_forumreplyvalue_cp_bp'];
		$bp_award_forumreply2img_cp_bp = $_POST['bp_award_forumreply2img_cp_bp'];
		$bp_award_forumreply2title_cp_bp = $_POST['bp_award_forumreply2title_cp_bp'];
		$bp_award_forumreply2value_cp_bp = (int)$_POST['bp_award_forumreply2value_cp_bp'];
		$bp_award_forumreply3img_cp_bp = $_POST['bp_award_forumreply3img_cp_bp'];
		$bp_award_forumreply3title_cp_bp = $_POST['bp_award_forumreply3title_cp_bp'];
		$bp_award_forumreply3value_cp_bp = (int)$_POST['bp_award_forumreply3value_cp_bp'];
		$bp_award_forumreply4img_cp_bp = $_POST['bp_award_forumreply4img_cp_bp'];
		$bp_award_forumreply4title_cp_bp = $_POST['bp_award_forumreply4title_cp_bp'];
		$bp_award_forumreply4value_cp_bp = (int)$_POST['bp_award_forumreply4value_cp_bp'];
		$bp_award_forumreply5img_cp_bp = $_POST['bp_award_forumreply5img_cp_bp'];
		$bp_award_forumreply5title_cp_bp = $_POST['bp_award_forumreply5title_cp_bp'];
		$bp_award_forumreply5value_cp_bp = (int)$_POST['bp_award_forumreply5value_cp_bp'];
		$bp_award_blogcommentimg_cp_bp = $_POST['bp_award_blogcommentimg_cp_bp'];
		$bp_award_blogcommenttitle_cp_bp = $_POST['bp_award_blogcommenttitle_cp_bp'];
		$bp_award_blogcommentvalue_cp_bp = (int)$_POST['bp_award_blogcommentvalue_cp_bp'];
		$bp_award_blogcomment2img_cp_bp = $_POST['bp_award_blogcomment2img_cp_bp'];
		$bp_award_blogcomment2title_cp_bp = $_POST['bp_award_blogcomment2title_cp_bp'];
		$bp_award_blogcomment2value_cp_bp = (int)$_POST['bp_award_blogcomment2value_cp_bp'];
		$bp_award_blogcomment3img_cp_bp = $_POST['bp_award_blogcomment3img_cp_bp'];
		$bp_award_blogcomment3title_cp_bp = $_POST['bp_award_blogcomment3title_cp_bp'];
		$bp_award_blogcomment3value_cp_bp = (int)$_POST['bp_award_blogcomment3value_cp_bp'];
		$bp_award_blogcomment4img_cp_bp = $_POST['bp_award_blogcomment4img_cp_bp'];
		$bp_award_blogcomment4title_cp_bp = $_POST['bp_award_blogcomment4title_cp_bp'];
		$bp_award_blogcomment4value_cp_bp = (int)$_POST['bp_award_blogcomment4value_cp_bp'];
		$bp_award_blogcomment5img_cp_bp = $_POST['bp_award_blogcomment5img_cp_bp'];
		$bp_award_blogcomment5title_cp_bp = $_POST['bp_award_blogcomment5title_cp_bp'];
		$bp_award_blogcomment5value_cp_bp = (int)$_POST['bp_award_blogcomment5value_cp_bp'];
		$bp_award_bloggerimg_cp_bp = $_POST['bp_award_bloggerimg_cp_bp'];
		$bp_award_bloggertitle_cp_bp = $_POST['bp_award_bloggertitle_cp_bp'];
		$bp_award_bloggervalue_cp_bp = (int)$_POST['bp_award_bloggervalue_cp_bp'];
		$bp_award_blogger2img_cp_bp = $_POST['bp_award_blogger2img_cp_bp'];
		$bp_award_blogger2title_cp_bp = $_POST['bp_award_blogger2title_cp_bp'];
		$bp_award_blogger2value_cp_bp = (int)$_POST['bp_award_blogger2value_cp_bp'];
		$bp_award_blogger3img_cp_bp = $_POST['bp_award_blogger3img_cp_bp'];
		$bp_award_blogger3title_cp_bp = $_POST['bp_award_blogger3title_cp_bp'];
		$bp_award_blogger3value_cp_bp = (int)$_POST['bp_award_blogger3value_cp_bp'];
		$bp_award_blogger4img_cp_bp = $_POST['bp_award_blogger4img_cp_bp'];
		$bp_award_blogger4title_cp_bp = $_POST['bp_award_blogger4title_cp_bp'];
		$bp_award_blogger4value_cp_bp = (int)$_POST['bp_award_blogger4value_cp_bp'];
		$bp_award_blogger5img_cp_bp = $_POST['bp_award_blogger5img_cp_bp'];
		$bp_award_blogger5title_cp_bp = $_POST['bp_award_blogger5title_cp_bp'];
		$bp_award_blogger5value_cp_bp = (int)$_POST['bp_award_blogger5value_cp_bp'];
		$bp_award_donationsimg_cp_bp = $_POST['bp_award_donationsimg_cp_bp'];
		$bp_award_donationstitle_cp_bp = $_POST['bp_award_donationstitle_cp_bp'];
		$bp_award_donationsvalue_cp_bp = (int)$_POST['bp_award_donationsvalue_cp_bp'];
		$bp_award_donations2img_cp_bp = $_POST['bp_award_donations2img_cp_bp'];
		$bp_award_donations2title_cp_bp = $_POST['bp_award_donations2title_cp_bp'];
		$bp_award_donations2value_cp_bp = (int)$_POST['bp_award_donations2value_cp_bp'];
		$bp_award_donations3img_cp_bp = $_POST['bp_award_donations3img_cp_bp'];
		$bp_award_donations3title_cp_bp = $_POST['bp_award_donations3title_cp_bp'];
		$bp_award_donations3value_cp_bp = (int)$_POST['bp_award_donations3value_cp_bp'];
		$bp_award_donations4img_cp_bp = $_POST['bp_award_donations4img_cp_bp'];
		$bp_award_donations4title_cp_bp = $_POST['bp_award_donations4title_cp_bp'];
		$bp_award_donations4value_cp_bp = (int)$_POST['bp_award_donations4value_cp_bp'];
		$bp_award_donations5img_cp_bp = $_POST['bp_award_donations5img_cp_bp'];
		$bp_award_donations5title_cp_bp = $_POST['bp_award_donations5title_cp_bp'];
		$bp_award_donations5value_cp_bp = (int)$_POST['bp_award_donations5value_cp_bp'];
		$bp_award_dailyloginimg_cp_bp = $_POST['bp_award_dailyloginimg_cp_bp'];
		$bp_award_dailylogintitle_cp_bp = $_POST['bp_award_dailylogintitle_cp_bp'];
		$bp_award_dailyloginvalue_cp_bp = (int)$_POST['bp_award_dailyloginvalue_cp_bp'];
		$bp_award_dailylogin2img_cp_bp = $_POST['bp_award_dailylogin2img_cp_bp'];
		$bp_award_dailylogin2title_cp_bp = $_POST['bp_award_dailylogin2title_cp_bp'];
		$bp_award_dailylogin2value_cp_bp = (int)$_POST['bp_award_dailylogin2value_cp_bp'];
		$bp_award_dailylogin3img_cp_bp = $_POST['bp_award_dailylogin3img_cp_bp'];
		$bp_award_dailylogin3title_cp_bp = $_POST['bp_award_dailylogin3title_cp_bp'];
		$bp_award_dailylogin3value_cp_bp = (int)$_POST['bp_award_dailylogin3value_cp_bp'];
		$bp_award_dailylogin4img_cp_bp = $_POST['bp_award_dailylogin4img_cp_bp'];
		$bp_award_dailylogin4title_cp_bp = $_POST['bp_award_dailylogin4title_cp_bp'];
		$bp_award_dailylogin4value_cp_bp = (int)$_POST['bp_award_dailylogin4value_cp_bp'];
		$bp_award_dailylogin5img_cp_bp = $_POST['bp_award_dailylogin5img_cp_bp'];
		$bp_award_dailylogin5title_cp_bp = $_POST['bp_award_dailylogin5title_cp_bp'];
		$bp_award_dailylogin5value_cp_bp = (int)$_POST['bp_award_dailylogin5value_cp_bp'];
		// Category
		$bp_award_bloggercatimg_cp_bp = $_POST['bp_award_bloggercatimg_cp_bp'];
		$bp_award_bloggercattitle_cp_bp = $_POST['bp_award_bloggercattitle_cp_bp'];
		$bp_award_bloggercatvalue_cp_bp = (int)$_POST['bp_award_bloggercatvalue_cp_bp'];
		$bp_award_bloggercat2img_cp_bp = $_POST['bp_award_bloggercat2img_cp_bp'];
		$bp_award_bloggercat2title_cp_bp = $_POST['bp_award_bloggercat2title_cp_bp'];
		$bp_award_bloggercat2value_cp_bp = (int)$_POST['bp_award_bloggercat2value_cp_bp'];
		$bp_award_bloggercat3img_cp_bp = $_POST['bp_award_bloggercat3img_cp_bp'];
		$bp_award_bloggercat3title_cp_bp = $_POST['bp_award_bloggercat3title_cp_bp'];
		$bp_award_bloggercat3value_cp_bp = (int)$_POST['bp_award_bloggercat3value_cp_bp'];
		$bp_award_bloggercat4img_cp_bp = $_POST['bp_award_bloggercat4img_cp_bp'];
		$bp_award_bloggercat4title_cp_bp = $_POST['bp_award_bloggercat4title_cp_bp'];
		$bp_award_bloggercat4value_cp_bp = (int)$_POST['bp_award_bloggercat4value_cp_bp'];
		$bp_award_bloggercat5img_cp_bp = $_POST['bp_award_bloggercat5img_cp_bp'];
		$bp_award_bloggercat5title_cp_bp = $_POST['bp_award_bloggercat5title_cp_bp'];
		$bp_award_bloggercat5value_cp_bp = (int)$_POST['bp_award_bloggercat5value_cp_bp'];
		$bp_award_bloggercatselector_cp_bp = (int)$_POST['bp_award_bloggercatselector_cp_bp'];
		$bp_award_bloggercatselector2_cp_bp = (int)$_POST['bp_award_bloggercatselector2_cp_bp'];
		$bp_award_bloggercatselector3_cp_bp = (int)$_POST['bp_award_bloggercatselector3_cp_bp'];
		$bp_award_bloggercatselector4_cp_bp = (int)$_POST['bp_award_bloggercatselector4_cp_bp'];
		$bp_award_bloggercatselector5_cp_bp = (int)$_POST['bp_award_bloggercatselector5_cp_bp'];
		//Awards for point levels
		$bp_award_points_1img_cp_bp = $_POST['bp_award_points_1img_cp_bp'];
		$bp_award_points_1title_cp_bp = $_POST['bp_award_points_1title_cp_bp'];
		$bp_award_points_1value_cp_bp = (int)$_POST['bp_award_points_1value_cp_bp'];
		$bp_award_points_2img_cp_bp = $_POST['bp_award_points_2img_cp_bp'];
		$bp_award_points_2title_cp_bp = $_POST['bp_award_points_2title_cp_bp'];
		$bp_award_points_2value_cp_bp = (int)$_POST['bp_award_points_2value_cp_bp'];
		$bp_award_points_3img_cp_bp = $_POST['bp_award_points_3img_cp_bp'];
		$bp_award_points_3title_cp_bp = $_POST['bp_award_points_3title_cp_bp'];
		$bp_award_points_3value_cp_bp = (int)$_POST['bp_award_points_3value_cp_bp'];
		$bp_award_points_4img_cp_bp = $_POST['bp_award_points_4img_cp_bp'];
		$bp_award_points_4title_cp_bp = $_POST['bp_award_points_4title_cp_bp'];
		$bp_award_points_4value_cp_bp = (int)$_POST['bp_award_points_4value_cp_bp'];
		$bp_award_points_5img_cp_bp = $_POST['bp_award_points_5img_cp_bp'];
		$bp_award_points_5title_cp_bp = $_POST['bp_award_points_5title_cp_bp'];
		$bp_award_points_5value_cp_bp = (int)$_POST['bp_award_points_5value_cp_bp'];
		$bp_award_points_6img_cp_bp = $_POST['bp_award_points_6img_cp_bp'];
		$bp_award_points_6title_cp_bp = $_POST['bp_award_points_6title_cp_bp'];
		$bp_award_points_6value_cp_bp = (int)$_POST['bp_award_points_6value_cp_bp'];
		$bp_award_points_7img_cp_bp = $_POST['bp_award_points_7img_cp_bp'];
		$bp_award_points_7title_cp_bp = $_POST['bp_award_points_7title_cp_bp'];
		$bp_award_points_7value_cp_bp = (int)$_POST['bp_award_points_7value_cp_bp'];
		$bp_award_points_8img_cp_bp = $_POST['bp_award_points_8img_cp_bp'];
		$bp_award_points_8title_cp_bp = $_POST['bp_award_points_8title_cp_bp'];
		$bp_award_points_8value_cp_bp = (int)$_POST['bp_award_points_8value_cp_bp'];
		$bp_award_points_9img_cp_bp = $_POST['bp_award_points_9img_cp_bp'];
		$bp_award_points_9title_cp_bp = $_POST['bp_award_points_9title_cp_bp'];
		$bp_award_points_9value_cp_bp = (int)$_POST['bp_award_points_9value_cp_bp'];
		$bp_award_points_10img_cp_bp = $_POST['bp_award_points_10img_cp_bp'];
		$bp_award_points_10title_cp_bp = $_POST['bp_award_points_10title_cp_bp'];
		$bp_award_points_10value_cp_bp = (int)$_POST['bp_award_points_10value_cp_bp'];
		// Simple Press Forum
		$bp_award_spf_forumimg_cp_bp = $_POST['bp_award_spf_forumimg_cp_bp'];
		$bp_award_spf_forumtitle_cp_bp = $_POST['bp_award_spf_forumtitle_cp_bp'];
		$bp_award_spf_forumvalue_cp_bp = (int)$_POST['bp_award_spf_forumvalue_cp_bp'];
		$bp_award_spf_forum2img_cp_bp = $_POST['bp_award_spf_forum2img_cp_bp'];
		$bp_award_spf_forum2title_cp_bp = $_POST['bp_award_spf_forum2title_cp_bp'];
		$bp_award_spf_forum2value_cp_bp = (int)$_POST['bp_award_spf_forum2value_cp_bp'];
		$bp_award_spf_forum3img_cp_bp = $_POST['bp_award_spf_forum3img_cp_bp'];
		$bp_award_spf_forum3title_cp_bp = $_POST['bp_award_spf_forum3title_cp_bp'];
		$bp_award_spf_forum3value_cp_bp = (int)$_POST['bp_award_spf_forum3value_cp_bp'];
		$bp_award_spf_forum4img_cp_bp = $_POST['bp_award_spf_forum4img_cp_bp'];
		$bp_award_spf_forum4title_cp_bp = $_POST['bp_award_spf_forum4title_cp_bp'];
		$bp_award_spf_forum4value_cp_bp = (int)$_POST['bp_award_spf_forum4value_cp_bp'];
		$bp_award_spf_forum5img_cp_bp = $_POST['bp_award_spf_forum5img_cp_bp'];
		$bp_award_spf_forum5title_cp_bp = $_POST['bp_award_spf_forum5title_cp_bp'];
		$bp_award_spf_forum5value_cp_bp = (int)$_POST['bp_award_spf_forum5value_cp_bp'];
		// Turn off award sections
		$bp_spf_support_onoff_cp_bp = (bool)$_POST['bp_spf_support_onoff_cp_bp'];
		// Earned all awards
		$bp_award_earnedall_img_cp_bp = $_POST['bp_award_earnedall_img_cp_bp'];
		$bp_award_earnedall_title_cp_bp = $_POST['bp_award_earnedall_title_cp_bp'];
		$bp_messagespamcheck_cp_bp = (int)$_POST['bp_messagespamcheck_cp_bp'];
		$bp_groupcreatespamcheck_cp_bp = (int)$_POST['bp_groupcreatespamcheck_cp_bp'];
		$bp_update_n_reply_spamcheck_cp_bp = (int)$_POST['bp_update_n_reply_spamcheck_cp_bp'];
		//Lottery Tie In
		$bp_lottery1_open_cp_bp = $_POST['bp_lottery1_open_cp_bp'];
		$bp_lottery1_entered_cp_bp = $_POST['bp_lottery1_entered_cp_bp'];
		$bp_lottery1_url_cp_bp = $_POST['bp_lottery1_url_cp_bp'];
		$bp_lottery2_open_cp_bp = $_POST['bp_lottery2_open_cp_bp'];
		$bp_lottery2_entered_cp_bp = $_POST['bp_lottery2_entered_cp_bp'];
		$bp_lottery2_url_cp_bp = $_POST['bp_lottery2_url_cp_bp'];
		$bp_lottery3_open_cp_bp = $_POST['bp_lottery3_open_cp_bp'];
		$bp_lottery3_entered_cp_bp = $_POST['bp_lottery3_entered_cp_bp'];
		$bp_lottery3_url_cp_bp = $_POST['bp_lottery3_url_cp_bp'];
		$bp_lottery4_open_cp_bp = $_POST['bp_lottery4_open_cp_bp'];
		$bp_lottery4_entered_cp_bp = $_POST['bp_lottery4_entered_cp_bp'];
		$bp_lottery4_url_cp_bp = $_POST['bp_lottery4_url_cp_bp'];
		$bp_lottery5_open_cp_bp = $_POST['bp_lottery5_open_cp_bp'];
		$bp_lottery5_entered_cp_bp = $_POST['bp_lottery5_entered_cp_bp'];
		$bp_lottery5_url_cp_bp = $_POST['bp_lottery5_url_cp_bp'];
		$bp_bet1_open_cp_bp = $_POST['bp_bet1_open_cp_bp'];
		$bp_bet1_entered_cp_bp = $_POST['bp_bet1_entered_cp_bp'];
		$bp_bet1_url_cp_bp = $_POST['bp_bet1_url_cp_bp'];
		$bp_bet2_open_cp_bp = $_POST['bp_bet2_open_cp_bp'];
		$bp_bet2_entered_cp_bp = $_POST['bp_bet2_entered_cp_bp'];
		$bp_bet2_url_cp_bp = $_POST['bp_bet2_url_cp_bp'];
		$bp_bet3_open_cp_bp = $_POST['bp_bet3_open_cp_bp'];
		$bp_bet3_entered_cp_bp = $_POST['bp_bet3_entered_cp_bp'];
		$bp_bet3_url_cp_bp = $_POST['bp_bet3_url_cp_bp'];
		$bp_bet4_open_cp_bp = $_POST['bp_bet4_open_cp_bp'];
		$bp_bet4_entered_cp_bp = $_POST['bp_bet4_entered_cp_bp'];
		$bp_bet4_url_cp_bp = $_POST['bp_bet4_url_cp_bp'];
		$bp_bet5_open_cp_bp = $_POST['bp_bet5_open_cp_bp'];
		$bp_bet5_entered_cp_bp = $_POST['bp_bet5_entered_cp_bp'];
		$bp_bet5_url_cp_bp = $_POST['bp_bet5_url_cp_bp'];
		// bbPress 2.0
		$bp_cp_bbpress2_new_topic = $_POST['bp_cp_bbpress2_new_topic'];
		$bp_cp_bbpress2_new_reply = $_POST['bp_cp_bbpress2_new_reply'];
		
		update_option('bp_create_group_add_cp_bp', $bp_create_group_add_cp_bp);
		update_option('bp_delete_group_add_cp_bp', $bp_delete_group_add_cp_bp);
		update_option('bp_join_group_add_cp_bp', $bp_join_group_add_cp_bp);
		update_option('bp_leave_group_add_cp_bp', $bp_leave_group_add_cp_bp);
		update_option('bp_update_post_add_cp_bp', $bp_update_post_add_cp_bp);
		update_option('bp_update_comment_add_cp_bp', $bp_update_comment_add_cp_bp);
		update_option('bp_update_group_add_cp_bp', $bp_update_group_add_cp_bp);
		update_option('bp_delete_comment_add_cp_bp', $bp_delete_comment_add_cp_bp);
		update_option('bp_friend_add_cp_bp', $bp_friend_add_cp_bp);
		update_option('bp_friend_delete_add_cp_bp', $bp_friend_delete_add_cp_bp);
		update_option('bp_forum_new_topic_add_cp_bp', $bp_forum_new_topic_add_cp_bp);
		update_option('bp_forum_new_post_add_cp_bp', $bp_forum_new_post_add_cp_bp);
		update_option('bp_avatar_add_cp_bp', $bp_avatar_add_cp_bp);
		update_option('bp_group_avatar_add_cp_bp', $bp_group_avatar_add_cp_bp);
		update_option('bp_pm_cp_bp', $bp_pm_cp_bp);		
		update_option('bp_bplink_add_cp_bp', $bp_bplink_add_cp_bp);
		update_option('bp_bplink_vote_add_cp_bp', $bp_bplink_vote_add_cp_bp);
		update_option('bp_bplink_comment_add_cp_bp', $bp_bplink_comment_add_cp_bp);
		update_option('bp_bplink_delete_add_cp_bp', $bp_bplink_delete_add_cp_bp);
		update_option('bp_gift_given_cp_bp', $bp_gift_given_cp_bp);
		update_option('bp_gallery_upload_cp_bp', $bp_gallery_upload_cp_bp);		
		update_option('bp_gallery_delete_cp_bp', $bp_gallery_delete_cp_bp);
		update_option('bp_spammer_cp_bp', $bp_spammer_cp_bp);
		update_option('bp_slug_cp_bp', $bp_slug_cp_bp);
		update_option('bp_points_logs_per_page_cp_bp', $bp_points_logs_per_page_cp_bp);
		update_option('bp_tallyuserpoints_cp_bp', $bp_tallyuserpoints_cp_bp);
		update_option('bp_sitewide_menu_cp_bp', $bp_sitewide_menu_cp_bp);
		update_option('bp_sitewidemtitle_cp_bp', $bp_sitewidemtitle_cp_bp);
		update_option('bp_earnpoints_menu_cp_bp', $bp_earnpoints_menu_cp_bp);		
		update_option('bp_earnpoints_menutitle_cp_bp', $bp_earnpoints_menutitle_cp_bp);
		update_option('bp_earnpointstitle_cp_bp', $bp_earnpointstitle_cp_bp);
		update_option('bp_earnpoints_extra_cp_bp', $bp_earnpoints_extra_cp_bp);
		update_option('bp_leaderboard_onoff_cp_bp', $bp_leaderboard_onoff_cp_bp);
		update_option('bp_leaderboardtitle_cp_bp', $bp_leaderboardtitle_cp_bp);
		update_option('bp_leaderboard_cp_bp', $bp_leaderboard_cp_bp);
		// Awards Menu Options
		update_option('bp_awards_menutitle_cp_bp', $bp_awards_menutitle_cp_bp);
		update_option('bp_awards_menu_onoff_cp_bp', $bp_awards_menu_onoff_cp_bp);
		//Awards
		update_option('bp_award_groupimg_cp_bp', $bp_award_groupimg_cp_bp);
		update_option('bp_award_grouptitle_cp_bp', $bp_award_grouptitle_cp_bp);
		update_option('bp_award_groupvalue_cp_bp', $bp_award_groupvalue_cp_bp);
		update_option('bp_award_group2img_cp_bp', $bp_award_group2img_cp_bp);
		update_option('bp_award_group2title_cp_bp', $bp_award_group2title_cp_bp);
		update_option('bp_award_group2value_cp_bp', $bp_award_group2value_cp_bp);
		update_option('bp_award_group3img_cp_bp', $bp_award_group3img_cp_bp);
		update_option('bp_award_group3title_cp_bp', $bp_award_group3title_cp_bp);
		update_option('bp_award_group3value_cp_bp', $bp_award_group3value_cp_bp);
		update_option('bp_award_group4img_cp_bp', $bp_award_group4img_cp_bp);
		update_option('bp_award_group4title_cp_bp', $bp_award_group4title_cp_bp);
		update_option('bp_award_group4value_cp_bp', $bp_award_group4value_cp_bp);
		update_option('bp_award_group5img_cp_bp', $bp_award_group5img_cp_bp);
		update_option('bp_award_group5title_cp_bp', $bp_award_group5title_cp_bp);
		update_option('bp_award_group5value_cp_bp', $bp_award_group5value_cp_bp);
		update_option('bp_award_friendimg_cp_bp', $bp_award_friendimg_cp_bp);
		update_option('bp_award_friendtitle_cp_bp', $bp_award_friendtitle_cp_bp);
		update_option('bp_award_friendvalue_cp_bp', $bp_award_friendvalue_cp_bp);
		update_option('bp_award_friend2img_cp_bp', $bp_award_friend2img_cp_bp);
		update_option('bp_award_friend2title_cp_bp', $bp_award_friend2title_cp_bp);
		update_option('bp_award_friend2value_cp_bp', $bp_award_friend2value_cp_bp);
		update_option('bp_award_friend3img_cp_bp', $bp_award_friend3img_cp_bp);
		update_option('bp_award_friend3title_cp_bp', $bp_award_friend3title_cp_bp);
		update_option('bp_award_friend3value_cp_bp', $bp_award_friend3value_cp_bp);
		update_option('bp_award_friend4img_cp_bp', $bp_award_friend4img_cp_bp);
		update_option('bp_award_friend4title_cp_bp', $bp_award_friend4title_cp_bp);
		update_option('bp_award_friend4value_cp_bp', $bp_award_friend4value_cp_bp);
		update_option('bp_award_friend5img_cp_bp', $bp_award_friend5img_cp_bp);
		update_option('bp_award_friend5title_cp_bp', $bp_award_friend5title_cp_bp);
		update_option('bp_award_friend5value_cp_bp', $bp_award_friend5value_cp_bp);
		update_option('bp_award_updateimg_cp_bp', $bp_award_updateimg_cp_bp);
		update_option('bp_award_updatetitle_cp_bp', $bp_award_updatetitle_cp_bp);
		update_option('bp_award_updatevalue_cp_bp', $bp_award_updatevalue_cp_bp);
		update_option('bp_award_update2img_cp_bp', $bp_award_update2img_cp_bp);
		update_option('bp_award_update2title_cp_bp', $bp_award_update2title_cp_bp);
		update_option('bp_award_update2value_cp_bp', $bp_award_update2value_cp_bp);
		update_option('bp_award_update3img_cp_bp', $bp_award_update3img_cp_bp);
		update_option('bp_award_update3title_cp_bp', $bp_award_update3title_cp_bp);
		update_option('bp_award_update3value_cp_bp', $bp_award_update3value_cp_bp);
		update_option('bp_award_update4img_cp_bp', $bp_award_update4img_cp_bp);
		update_option('bp_award_update4title_cp_bp', $bp_award_update4title_cp_bp);
		update_option('bp_award_update4value_cp_bp', $bp_award_update4value_cp_bp);
		update_option('bp_award_update5img_cp_bp', $bp_award_update5img_cp_bp);
		update_option('bp_award_update5title_cp_bp', $bp_award_update5title_cp_bp);
		update_option('bp_award_update5value_cp_bp', $bp_award_update5value_cp_bp);
		update_option('bp_award_replyimg_cp_bp', $bp_award_replyimg_cp_bp);
		update_option('bp_award_replytitle_cp_bp', $bp_award_replytitle_cp_bp);
		update_option('bp_award_replyvalue_cp_bp', $bp_award_replyvalue_cp_bp);
		update_option('bp_award_reply2img_cp_bp', $bp_award_reply2img_cp_bp);
		update_option('bp_award_reply2title_cp_bp', $bp_award_reply2title_cp_bp);
		update_option('bp_award_reply2value_cp_bp', $bp_award_reply2value_cp_bp);
		update_option('bp_award_reply3img_cp_bp', $bp_award_reply3img_cp_bp);
		update_option('bp_award_reply3title_cp_bp', $bp_award_reply3title_cp_bp);
		update_option('bp_award_reply3value_cp_bp', $bp_award_reply3value_cp_bp);
		update_option('bp_award_reply4img_cp_bp', $bp_award_reply4img_cp_bp);
		update_option('bp_award_reply4title_cp_bp', $bp_award_reply4title_cp_bp);
		update_option('bp_award_reply4value_cp_bp', $bp_award_reply4value_cp_bp);
		update_option('bp_award_reply5img_cp_bp', $bp_award_reply5img_cp_bp);
		update_option('bp_award_reply5title_cp_bp', $bp_award_reply5title_cp_bp);
		update_option('bp_award_reply5value_cp_bp', $bp_award_reply5value_cp_bp);
		update_option('bp_award_forumtopicimg_cp_bp', $bp_award_forumtopicimg_cp_bp);
		update_option('bp_award_forumtopictitle_cp_bp', $bp_award_forumtopictitle_cp_bp);
		update_option('bp_award_forumtopicvalue_cp_bp', $bp_award_forumtopicvalue_cp_bp);
		update_option('bp_award_forumtopic2img_cp_bp', $bp_award_forumtopic2img_cp_bp);
		update_option('bp_award_forumtopic2title_cp_bp', $bp_award_forumtopic2title_cp_bp);
		update_option('bp_award_forumtopic2value_cp_bp', $bp_award_forumtopic2value_cp_bp);
		update_option('bp_award_forumtopic3img_cp_bp', $bp_award_forumtopic3img_cp_bp);
		update_option('bp_award_forumtopic3title_cp_bp', $bp_award_forumtopic3title_cp_bp);
		update_option('bp_award_forumtopic3value_cp_bp', $bp_award_forumtopic3value_cp_bp);
		update_option('bp_award_forumtopic4img_cp_bp', $bp_award_forumtopic4img_cp_bp);
		update_option('bp_award_forumtopic4title_cp_bp', $bp_award_forumtopic4title_cp_bp);
		update_option('bp_award_forumtopic4value_cp_bp', $bp_award_forumtopic4value_cp_bp);
		update_option('bp_award_forumtopic5img_cp_bp', $bp_award_forumtopic5img_cp_bp);
		update_option('bp_award_forumtopic5title_cp_bp', $bp_award_forumtopic5title_cp_bp);
		update_option('bp_award_forumtopic5value_cp_bp', $bp_award_forumtopic5value_cp_bp);
		update_option('bp_award_forumreplyimg_cp_bp', $bp_award_forumreplyimg_cp_bp);
		update_option('bp_award_forumreplytitle_cp_bp', $bp_award_forumreplytitle_cp_bp);
		update_option('bp_award_forumreplyvalue_cp_bp', $bp_award_forumreplyvalue_cp_bp);
		update_option('bp_award_forumreply2img_cp_bp', $bp_award_forumreply2img_cp_bp);
		update_option('bp_award_forumreply2title_cp_bp', $bp_award_forumreply2title_cp_bp);
		update_option('bp_award_forumreply2value_cp_bp', $bp_award_forumreply2value_cp_bp);
		update_option('bp_award_forumreply3img_cp_bp', $bp_award_forumreply3img_cp_bp);
		update_option('bp_award_forumreply3title_cp_bp', $bp_award_forumreply3title_cp_bp);
		update_option('bp_award_forumreply3value_cp_bp', $bp_award_forumreply3value_cp_bp);
		update_option('bp_award_forumreply4img_cp_bp', $bp_award_forumreply4img_cp_bp);
		update_option('bp_award_forumreply4title_cp_bp', $bp_award_forumreply4title_cp_bp);
		update_option('bp_award_forumreply4value_cp_bp', $bp_award_forumreply4value_cp_bp);
		update_option('bp_award_forumreply5img_cp_bp', $bp_award_forumreply5img_cp_bp);
		update_option('bp_award_forumreply5title_cp_bp', $bp_award_forumreply5title_cp_bp);
		update_option('bp_award_forumreply5value_cp_bp', $bp_award_forumreply5value_cp_bp);
		update_option('bp_award_blogcommentimg_cp_bp', $bp_award_blogcommentimg_cp_bp);
		update_option('bp_award_blogcommenttitle_cp_bp', $bp_award_blogcommenttitle_cp_bp);
		update_option('bp_award_blogcommentvalue_cp_bp', $bp_award_blogcommentvalue_cp_bp);
		update_option('bp_award_blogcomment2img_cp_bp', $bp_award_blogcomment2img_cp_bp);
		update_option('bp_award_blogcomment2title_cp_bp', $bp_award_blogcomment2title_cp_bp);
		update_option('bp_award_blogcomment2value_cp_bp', $bp_award_blogcomment2value_cp_bp);
		update_option('bp_award_blogcomment3img_cp_bp', $bp_award_blogcomment3img_cp_bp);
		update_option('bp_award_blogcomment3title_cp_bp', $bp_award_blogcomment3title_cp_bp);
		update_option('bp_award_blogcomment3value_cp_bp', $bp_award_blogcomment3value_cp_bp);
		update_option('bp_award_blogcomment4img_cp_bp', $bp_award_blogcomment4img_cp_bp);
		update_option('bp_award_blogcomment4title_cp_bp', $bp_award_blogcomment4title_cp_bp);
		update_option('bp_award_blogcomment4value_cp_bp', $bp_award_blogcomment4value_cp_bp);
		update_option('bp_award_blogcomment5img_cp_bp', $bp_award_blogcomment5img_cp_bp);
		update_option('bp_award_blogcomment5title_cp_bp', $bp_award_blogcomment5title_cp_bp);
		update_option('bp_award_blogcomment5value_cp_bp', $bp_award_blogcomment5value_cp_bp);
		update_option('bp_award_bloggerimg_cp_bp', $bp_award_bloggerimg_cp_bp);
		update_option('bp_award_bloggertitle_cp_bp', $bp_award_bloggertitle_cp_bp);
		update_option('bp_award_bloggervalue_cp_bp', $bp_award_bloggervalue_cp_bp);
		update_option('bp_award_blogger2img_cp_bp', $bp_award_blogger2img_cp_bp);
		update_option('bp_award_blogger2title_cp_bp', $bp_award_blogger2title_cp_bp);
		update_option('bp_award_blogger2value_cp_bp', $bp_award_blogger2value_cp_bp);
		update_option('bp_award_blogger3img_cp_bp', $bp_award_blogger3img_cp_bp);
		update_option('bp_award_blogger3title_cp_bp', $bp_award_blogger3title_cp_bp);
		update_option('bp_award_blogger3value_cp_bp', $bp_award_blogger3value_cp_bp);
		update_option('bp_award_blogger4img_cp_bp', $bp_award_blogger4img_cp_bp);
		update_option('bp_award_blogger4title_cp_bp', $bp_award_blogger4title_cp_bp);
		update_option('bp_award_blogger4value_cp_bp', $bp_award_blogger4value_cp_bp);
		update_option('bp_award_blogger5img_cp_bp', $bp_award_blogger5img_cp_bp);
		update_option('bp_award_blogger5title_cp_bp', $bp_award_blogger5title_cp_bp);
		update_option('bp_award_blogger5value_cp_bp', $bp_award_blogger5value_cp_bp);
		update_option('bp_award_donationsimg_cp_bp', $bp_award_donationsimg_cp_bp);
		update_option('bp_award_donationstitle_cp_bp', $bp_award_donationstitle_cp_bp);
		update_option('bp_award_donationsvalue_cp_bp', $bp_award_donationsvalue_cp_bp);
		update_option('bp_award_donations2img_cp_bp', $bp_award_donations2img_cp_bp);
		update_option('bp_award_donations2title_cp_bp', $bp_award_donations2title_cp_bp);
		update_option('bp_award_donations2value_cp_bp', $bp_award_donations2value_cp_bp);
		update_option('bp_award_donations3img_cp_bp', $bp_award_donations3img_cp_bp);
		update_option('bp_award_donations3title_cp_bp', $bp_award_donations3title_cp_bp);
		update_option('bp_award_donations3value_cp_bp', $bp_award_donations3value_cp_bp);
		update_option('bp_award_donations4img_cp_bp', $bp_award_donations4img_cp_bp);
		update_option('bp_award_donations4title_cp_bp', $bp_award_donations4title_cp_bp);
		update_option('bp_award_donations4value_cp_bp', $bp_award_donations4value_cp_bp);
		update_option('bp_award_donations5img_cp_bp', $bp_award_donations5img_cp_bp);
		update_option('bp_award_donations5title_cp_bp', $bp_award_donations5title_cp_bp);
		update_option('bp_award_donations5value_cp_bp', $bp_award_donations5value_cp_bp);
		update_option('bp_award_dailyloginimg_cp_bp', $bp_award_dailyloginimg_cp_bp);
		update_option('bp_award_dailylogintitle_cp_bp', $bp_award_dailylogintitle_cp_bp);
		update_option('bp_award_dailyloginvalue_cp_bp', $bp_award_dailyloginvalue_cp_bp);
		update_option('bp_award_dailylogin2img_cp_bp', $bp_award_dailylogin2img_cp_bp);
		update_option('bp_award_dailylogin2title_cp_bp', $bp_award_dailylogin2title_cp_bp);
		update_option('bp_award_dailylogin2value_cp_bp', $bp_award_dailylogin2value_cp_bp);
		update_option('bp_award_dailylogin3img_cp_bp', $bp_award_dailylogin3img_cp_bp);
		update_option('bp_award_dailylogin3title_cp_bp', $bp_award_dailylogin3title_cp_bp);
		update_option('bp_award_dailylogin3value_cp_bp', $bp_award_dailylogin3value_cp_bp);
		update_option('bp_award_dailylogin4img_cp_bp', $bp_award_dailylogin4img_cp_bp);
		update_option('bp_award_dailylogin4title_cp_bp', $bp_award_dailylogin4title_cp_bp);
		update_option('bp_award_dailylogin4value_cp_bp', $bp_award_dailylogin4value_cp_bp);
		update_option('bp_award_dailylogin5img_cp_bp', $bp_award_dailylogin5img_cp_bp);
		update_option('bp_award_dailylogin5title_cp_bp', $bp_award_dailylogin5title_cp_bp);
		update_option('bp_award_dailylogin5value_cp_bp', $bp_award_dailylogin5value_cp_bp);
		// Category
		update_option('bp_award_bloggercatimg_cp_bp', $bp_award_bloggercatimg_cp_bp);
		update_option('bp_award_bloggercattitle_cp_bp', $bp_award_bloggercattitle_cp_bp);
		update_option('bp_award_bloggercatvalue_cp_bp', $bp_award_bloggercatvalue_cp_bp);
		update_option('bp_award_bloggercat2img_cp_bp', $bp_award_bloggercat2img_cp_bp);
		update_option('bp_award_bloggercat2title_cp_bp', $bp_award_bloggercat2title_cp_bp);
		update_option('bp_award_bloggercat2value_cp_bp', $bp_award_bloggercat2value_cp_bp);
		update_option('bp_award_bloggercat3img_cp_bp', $bp_award_bloggercat3img_cp_bp);
		update_option('bp_award_bloggercat3title_cp_bp', $bp_award_bloggercat3title_cp_bp);
		update_option('bp_award_bloggercat3value_cp_bp', $bp_award_bloggercat3value_cp_bp);
		update_option('bp_award_bloggercat4img_cp_bp', $bp_award_bloggercat4img_cp_bp);
		update_option('bp_award_bloggercat4title_cp_bp', $bp_award_bloggercat4title_cp_bp);
		update_option('bp_award_bloggercat4value_cp_bp', $bp_award_bloggercat4value_cp_bp);
		update_option('bp_award_bloggercat5img_cp_bp', $bp_award_bloggercat5img_cp_bp);
		update_option('bp_award_bloggercat5title_cp_bp', $bp_award_bloggercat5title_cp_bp);
		update_option('bp_award_bloggercat5value_cp_bp', $bp_award_bloggercat5value_cp_bp);
		update_option('bp_award_bloggercatselector_cp_bp', $bp_award_bloggercatselector_cp_bp);
		update_option('bp_award_bloggercatselector2_cp_bp', $bp_award_bloggercatselector2_cp_bp);
		update_option('bp_award_bloggercatselector3_cp_bp', $bp_award_bloggercatselector3_cp_bp);
		update_option('bp_award_bloggercatselector4_cp_bp', $bp_award_bloggercatselector4_cp_bp);
		update_option('bp_award_bloggercatselector5_cp_bp', $bp_award_bloggercatselector5_cp_bp);
		//Awards for point levels
		update_option('bp_award_points_1img_cp_bp', $bp_award_points_1img_cp_bp);
		update_option('bp_award_points_1title_cp_bp', $bp_award_points_1title_cp_bp);
		update_option('bp_award_points_1value_cp_bp', $bp_award_points_1value_cp_bp);
		update_option('bp_award_points_2img_cp_bp', $bp_award_points_2img_cp_bp);
		update_option('bp_award_points_2title_cp_bp', $bp_award_points_2title_cp_bp);
		update_option('bp_award_points_2value_cp_bp', $bp_award_points_2value_cp_bp);
		update_option('bp_award_points_3img_cp_bp', $bp_award_points_3img_cp_bp);
		update_option('bp_award_points_3title_cp_bp', $bp_award_points_3title_cp_bp);
		update_option('bp_award_points_3value_cp_bp', $bp_award_points_3value_cp_bp);
		update_option('bp_award_points_4img_cp_bp', $bp_award_points_4img_cp_bp);
		update_option('bp_award_points_4title_cp_bp', $bp_award_points_4title_cp_bp);
		update_option('bp_award_points_4value_cp_bp', $bp_award_points_4value_cp_bp);
		update_option('bp_award_points_5img_cp_bp', $bp_award_points_5img_cp_bp);
		update_option('bp_award_points_5title_cp_bp', $bp_award_points_5title_cp_bp);
		update_option('bp_award_points_5value_cp_bp', $bp_award_points_5value_cp_bp);
		update_option('bp_award_points_6img_cp_bp', $bp_award_points_6img_cp_bp);
		update_option('bp_award_points_6title_cp_bp', $bp_award_points_6title_cp_bp);
		update_option('bp_award_points_6value_cp_bp', $bp_award_points_6value_cp_bp);
		update_option('bp_award_points_7img_cp_bp', $bp_award_points_7img_cp_bp);
		update_option('bp_award_points_7title_cp_bp', $bp_award_points_7title_cp_bp);
		update_option('bp_award_points_7value_cp_bp', $bp_award_points_7value_cp_bp);
		update_option('bp_award_points_8img_cp_bp', $bp_award_points_8img_cp_bp);
		update_option('bp_award_points_8title_cp_bp', $bp_award_points_8title_cp_bp);
		update_option('bp_award_points_8value_cp_bp', $bp_award_points_8value_cp_bp);
		update_option('bp_award_points_9img_cp_bp', $bp_award_points_9img_cp_bp);
		update_option('bp_award_points_9title_cp_bp', $bp_award_points_9title_cp_bp);
		update_option('bp_award_points_9value_cp_bp', $bp_award_points_9value_cp_bp);
		update_option('bp_award_points_10img_cp_bp', $bp_award_points_10img_cp_bp);
		update_option('bp_award_points_10title_cp_bp', $bp_award_points_10title_cp_bp);
		update_option('bp_award_points_10value_cp_bp', $bp_award_points_10value_cp_bp);
		// Simple Press Forum Support
		update_option('bp_award_spf_forumimg_cp_bp', $bp_award_spf_forumimg_cp_bp);
		update_option('bp_award_spf_forumtitle_cp_bp', $bp_award_spf_forumtitle_cp_bp);
		update_option('bp_award_spf_forumvalue_cp_bp', $bp_award_spf_forumvalue_cp_bp);
		update_option('bp_award_spf_forum2img_cp_bp', $bp_award_spf_forum2img_cp_bp);
		update_option('bp_award_spf_forum2title_cp_bp', $bp_award_spf_forum2title_cp_bp);
		update_option('bp_award_spf_forum2value_cp_bp', $bp_award_spf_forum2value_cp_bp);
		update_option('bp_award_spf_forum3img_cp_bp', $bp_award_spf_forum3img_cp_bp);
		update_option('bp_award_spf_forum3title_cp_bp', $bp_award_spf_forum3title_cp_bp);
		update_option('bp_award_spf_forum3value_cp_bp', $bp_award_spf_forum3value_cp_bp);
		update_option('bp_award_spf_forum4img_cp_bp', $bp_award_spf_forum4img_cp_bp);
		update_option('bp_award_spf_forum4title_cp_bp', $bp_award_spf_forum4title_cp_bp);
		update_option('bp_award_spf_forum4value_cp_bp', $bp_award_spf_forum4value_cp_bp);
		update_option('bp_award_spf_forum5img_cp_bp', $bp_award_spf_forum5img_cp_bp);
		update_option('bp_award_spf_forum5title_cp_bp', $bp_award_spf_forum5title_cp_bp);
		update_option('bp_award_spf_forum5value_cp_bp', $bp_award_spf_forum5value_cp_bp);
		// Turn off award sections
		update_option('bp_spf_support_onoff_cp_bp', $bp_spf_support_onoff_cp_bp);
		// Earned all awards
		update_option('bp_award_earnedall_img_cp_bp', $bp_award_earnedall_img_cp_bp);
		update_option('bp_award_earnedall_title_cp_bp', $bp_award_earnedall_title_cp_bp);
		update_option('bp_messagespamcheck_cp_bp', $bp_messagespamcheck_cp_bp);
		update_option('bp_groupcreatespamcheck_cp_bp', $bp_groupcreatespamcheck_cp_bp);
		update_option('bp_update_n_reply_spamcheck_cp_bp', $bp_update_n_reply_spamcheck_cp_bp);
		//Lottery Tie In
		update_option('bp_lottery1_open_cp_bp', $bp_lottery1_open_cp_bp);
		update_option('bp_lottery1_entered_cp_bp', $bp_lottery1_entered_cp_bp);
		update_option('bp_lottery1_url_cp_bp', $bp_lottery1_url_cp_bp);
		update_option('bp_lottery2_open_cp_bp', $bp_lottery2_open_cp_bp);
		update_option('bp_lottery2_entered_cp_bp', $bp_lottery2_entered_cp_bp);
		update_option('bp_lottery2_url_cp_bp', $bp_lottery2_url_cp_bp);
		update_option('bp_lottery3_open_cp_bp', $bp_lottery3_open_cp_bp);
		update_option('bp_lottery3_entered_cp_bp', $bp_lottery3_entered_cp_bp);
		update_option('bp_lottery3_url_cp_bp', $bp_lottery3_url_cp_bp);
		update_option('bp_lottery4_open_cp_bp', $bp_lottery4_open_cp_bp);
		update_option('bp_lottery4_entered_cp_bp', $bp_lottery4_entered_cp_bp);
		update_option('bp_lottery4_url_cp_bp', $bp_lottery4_url_cp_bp);
		update_option('bp_lottery5_open_cp_bp', $bp_lottery5_open_cp_bp);
		update_option('bp_lottery5_entered_cp_bp', $bp_lottery5_entered_cp_bp);
		update_option('bp_lottery5_url_cp_bp', $bp_lottery5_url_cp_bp);
		update_option('bp_bet1_open_cp_bp', $bp_bet1_open_cp_bp);
		update_option('bp_bet1_entered_cp_bp', $bp_bet1_entered_cp_bp);
		update_option('bp_bet1_url_cp_bp', $bp_bet1_url_cp_bp);
		update_option('bp_bet2_open_cp_bp', $bp_bet2_open_cp_bp);
		update_option('bp_bet2_entered_cp_bp', $bp_bet2_entered_cp_bp);
		update_option('bp_bet2_url_cp_bp', $bp_bet2_url_cp_bp);
		update_option('bp_bet3_open_cp_bp', $bp_bet3_open_cp_bp);
		update_option('bp_bet3_entered_cp_bp', $bp_bet3_entered_cp_bp);
		update_option('bp_bet3_url_cp_bp', $bp_bet3_url_cp_bp);
		update_option('bp_bet4_open_cp_bp', $bp_bet4_open_cp_bp);
		update_option('bp_bet4_entered_cp_bp', $bp_bet4_entered_cp_bp);
		update_option('bp_bet4_url_cp_bp', $bp_bet4_url_cp_bp);
		update_option('bp_bet5_open_cp_bp', $bp_bet5_open_cp_bp);
		update_option('bp_bet5_entered_cp_bp', $bp_bet5_entered_cp_bp);
		update_option('bp_bet5_url_cp_bp', $bp_bet5_url_cp_bp);
		// bbPress 2.0
		update_option('bp_cp_bbpress2_new_topic', $bp_cp_bbpress2_new_topic);
		update_option('bp_cp_bbpress2_new_reply', $bp_cp_bbpress2_new_reply);
		
		do_action("bp_cubepoint_settings_updated"); // Allow other plugins to hook here and update their settings on save	
		
		echo '<div class="updated"><p><strong>'.__('Settings Updated','cp_buddypress').'</strong></p></div>';
  	}
?>


<script type="text/javascript">
function mainsettings() {
document.getElementById("mainsettings").style.display = "block";
document.getElementById("awardssettings").style.display = "none";
document.getElementById("lotterynbets").style.display = "none";
}
function awardssettings() {
document.getElementById("mainsettings").style.display = "none";
document.getElementById("awardssettings").style.display = "block";
document.getElementById("lotterynbets").style.display = "none";
}
function lotterynbets() {
document.getElementById("mainsettings").style.display = "none";
document.getElementById("awardssettings").style.display = "none";
document.getElementById("lotterynbets").style.display = "block";
}
</script>


<div class="wrap">
<h2><?php _e( 'CubePoints Buddypress Integration', 'cp_buddypress' ) ?></h2>
<form name="cp_bp_admin_form" method="post" action="">
	<input type="hidden" name="cp_bp_admin_form_submit" value="Y" />
	<h3><?php _e('CubePoints For BuddyPress Actions','cp_buddypress'); ?><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KPFWQ3GJDJ9BG"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" align="right" /></a></h3>
	
	<h3 align="center"><a href="javascript:mainsettings()"><?php _e('Main Settings','cp_buddypress'); ?></a> | <a href="javascript:awardssettings()"><?php _e('Awards Settings','cp_buddypress'); ?></a> | <a href="javascript:lotterynbets()"><?php _e('Giveaway & Betting Settings','cp_buddypress'); ?></a></h3>

	<div id="mainsettings" style="display:block;">
	 <table class="widefat fixed" cellspacing="0">
	  <thead>
	    <tr>
	      <td colspan="3" align="center"><strong><?php _e( 'You can take away points for any BuddyPress action just use a negative number instead of a positive.', 'cp_buddypress' ) ?></strong></td>
	    </tr>	  
	    <tr>
	      <th scope="col" id="action" class="column-name" style="width: 43%;"><?php _e('Action', 'cp_buddypress'); ?></th>
	      <th scope="col" id="value" class="column-name" style=""><?php _e('Value', 'cp_buddypress'); ?></th>
		  <th scope="col" id="reset" class="column-name" style=""><?php _e('Reset', 'cp_buddypress'); ?></th>
	    </tr>
	 </thead>
	  <tfoot>
	    <tr>
	      <th colspan="3" id="default" class="column-name"></th>
	    </tr>
	  </tfoot>	  
		<tr valign="top">
			<th scope="row"><label for="bp_create_group_add_cp_bp"><?php _e('Points for creating a group', 'cp_buddypress'); ?>:</label></th>
			<td valign="middle" width="190"><input type="text" id="bp_create_group_add_cp_bp" name="bp_create_group_add_cp_bp" value="<?php echo get_option('bp_create_group_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_create_group_add_cp_bp').value='0'" value="<?php _e('Do not add points', 'cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_group_avatar_add_cp_bp"><?php _e('Points for uploading a group avatar', 'cp_buddypress'); ?>:</label></th>
			<td valign="middle" width="190"><input type="text" id="bp_group_avatar_add_cp_bp" name="bp_group_avatar_add_cp_bp" value="<?php echo get_option('bp_group_avatar_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_group_avatar_add_cp_bp').value='0'" value="<?php _e('Do not add points', 'cp_buddypress'); ?>" class="button" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_delete_group_add_cp_bp"><?php _e('Remove points for group deletion', 'cp_buddypress'); ?>:</label></th>
			<td valign="middle" width="190"><input type="text" id="bp_delete_group_add_cp_bp" name="bp_delete_group_add_cp_bp" value="<?php echo get_option('bp_delete_group_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_delete_group_add_cp_bp').value='0'" value="<?php _e('Do not delete points', 'cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_join_group_add_cp_bp"><?php _e('Points for joining a group', 'cp_buddypress'); ?>:</label></th>
			<td valign="middle" width="190"><input type="text" id="bp_join_group_add_cp_bp" name="bp_join_group_add_cp_bp" value="<?php echo get_option('bp_join_group_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_join_group_add_cp_bp').value='0'" value="<?php _e('Do not add points', 'cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_leave_group_add_cp_bp"><?php _e('Points for leaving a group', 'cp_buddypress'); ?>:</label></th>
			<td valign="middle" width="190"><input type="text" id="bp_leave_group_add_cp_bp" name="bp_leave_group_add_cp_bp" value="<?php echo get_option('bp_leave_group_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_leave_group_add_cp_bp').value='0'" value="<?php _e('Do not add points', 'cp_buddypress'); ?>" class="button" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_update_post_add_cp_bp"><?php _e('Points for a update','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_update_post_add_cp_bp" name="bp_update_post_add_cp_bp" value="<?php echo get_option('bp_update_post_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_update_post_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_update_comment_add_cp_bp"><?php _e('Points for a comment or reply','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_update_comment_add_cp_bp" name="bp_update_comment_add_cp_bp" value="<?php echo get_option('bp_update_comment_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_update_comment_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_update_group_add_cp_bp"><?php _e('Group comment or reply','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_update_group_add_cp_bp" name="bp_update_group_add_cp_bp" value="<?php echo get_option('bp_update_group_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_update_group_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="bp_delete_comment_add_cp_bp"><?php _e('Remove points for comment deletion','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_delete_comment_add_cp_bp" name="bp_delete_comment_add_cp_bp" value="<?php echo get_option('bp_delete_comment_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_delete_comment_add_cp_bp').value='0'" value="<?php _e('Do not remove points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_friend_add_cp_bp"><?php _e('Points for Completed Friend Request','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_friend_add_cp_bp" name="bp_friend_add_cp_bp" value="<?php echo get_option('bp_friend_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_friend_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
			<tr valign="top">
			<th scope="row"><label for="bp_friend_delete_add_cp_bp"><?php _e('Remove points for Canceled Friendship','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_friend_delete_add_cp_bp" name="bp_friend_delete_add_cp_bp" value="<?php echo get_option('bp_friend_delete_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_friend_delete_add_cp_bp').value='0'" value="<?php _e('Do not remove points','cp_buddypress'); ?>" class="button" /></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="bp_forum_new_topic_add_cp_bp"><?php _e('Points for New Group Forum Topic','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_forum_new_topic_add_cp_bp" name="bp_forum_new_topic_add_cp_bp" value="<?php echo get_option('bp_forum_new_topic_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_forum_new_topic_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="bp_forum_new_post_add_cp_bp"><?php _e('Points for New Group Forum Post','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_forum_new_post_add_cp_bp" name="bp_forum_new_post_add_cp_bp" value="<?php echo get_option('bp_forum_new_post_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_forum_new_post_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_avatar_add_cp_bp"><?php _e('Points for Avatar Upload','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_avatar_add_cp_bp" name="bp_avatar_add_cp_bp" value="<?php echo get_option('bp_avatar_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_avatar_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_pm_cp_bp"><?php _e('Points for Message Sent','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_pm_cp_bp" name="bp_pm_cp_bp" value="<?php echo get_option('bp_pm_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_pm_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr>
			<th colspan="3"><h4 align="center"><?php _e('Below Requires','cp_buddypress'); ?> <a href="http://bbpress.org/">bbPress 2.0</a></h4></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_cp_bbpress2_new_topic"><?php _e('Points for New Forum Topic','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_cp_bbpress2_new_topic" name="bp_cp_bbpress2_new_topic" value="<?php echo get_option('bp_cp_bbpress2_new_topic'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_cp_bbpress2_new_topic').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_cp_bbpress2_new_reply"><?php _e('Points for New Forum Reply','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_cp_bbpress2_new_reply" name="bp_cp_bbpress2_new_reply" value="<?php echo get_option('bp_cp_bbpress2_new_reply'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_cp_bbpress2_new_reply').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr>
			<th colspan="3"><h4 align="center"><?php _e('Below Requires','cp_buddypress'); ?> <a href="http://buddypress.org/community/groups/buddypress-links/">BuddyPress Links</a> [Marshall Sorenson (MrMaz)]</h4></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bplink_add_cp_bp"><?php _e('Points for BuddyPress Link Creation','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_bplink_add_cp_bp" name="bp_bplink_add_cp_bp" value="<?php echo get_option('bp_bplink_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_bplink_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bplink_vote_add_cp_bp"><?php _e('Points for BuddyPress Link Vote','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_bplink_vote_add_cp_bp" name="bp_bplink_vote_add_cp_bp" value="<?php echo get_option('bp_bplink_vote_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_bplink_vote_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bplink_comment_add_cp_bp"><?php _e('Points for BuddyPress Link Comment','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_bplink_comment_add_cp_bp" name="bp_bplink_comment_add_cp_bp" value="<?php echo get_option('bp_bplink_comment_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_bplink_comment_add_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bplink_delete_add_cp_bp"><?php _e('Remove points for BuddyPress Link deletion','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_bplink_delete_add_cp_bp" name="bp_bplink_delete_add_cp_bp" value="<?php echo get_option('bp_bplink_delete_add_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_bplink_delete_add_cp_bp').value='0'" value="<?php _e('Do not remove points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr>
			<th colspan="3"><h4 align="center"><?php _e('Below Requires','cp_buddypress'); ?> <a href="http://buddypress.org/community/groups/buddypress-gifts/">BuddyPress Gifts</a> [Warut Sudpoothong]</h4></th>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_gift_given_cp_bp"><?php _e('Points for giving a BuddyPress Gift','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_gift_given_cp_bp" name="bp_gift_given_cp_bp" value="<?php echo get_option('bp_gift_given_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_gift_given_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr>
			<th colspan="3"><h4 align="center"><?php _e('Below Requires','cp_buddypress'); ?> <a href="http://buddydev.com/premium/">BP Gallery</a> [Brajesh Singh]</h4></th>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_gallery_upload_cp_bp"><?php _e('Points for Gallery Upload','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_gallery_upload_cp_bp" name="bp_gallery_upload_cp_bp" value="<?php echo get_option('bp_gallery_upload_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_gallery_upload_cp_bp').value='0'" value="<?php _e('Do not add points','cp_buddypress'); ?>" class="button" /></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="bp_gallery_delete_cp_bp"><?php _e('Remove points for Gallery deletion','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_gallery_delete_cp_bp" name="bp_gallery_delete_cp_bp" value="<?php echo get_option('bp_gallery_delete_cp_bp'); ?>" size="2" /></td>
			<td><input type="button" onclick="document.getElementById('bp_gallery_delete_cp_bp').value='0'" value="<?php _e('Do not remove points','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		
		<?php do_action("bp_cubepoint_main_settings"); /* Allow other plugins to hook here and update their settings on save */ ?>
		
		<tr>
			<th colspan="3"><h2><?php _e('Point Blocker!','cp_buddypress'); ?></h2></th>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="3"><label for="bp_spammer_cp_bp"><?php _e('Enter the User ID(s) to block members from earning points on BuddyPress:<br />Use a comma after each User ID. For example 66,33,90,120','cp_buddypress'); ?></label></th>
		</tr>
		<tr>
			<td valign="middle" colspan="2"><input type="text" id="bp_spammer_cp_bp" name="bp_spammer_cp_bp" value="<?php echo get_option('bp_spammer_cp_bp'); ?>" size="60" style="color: #FF0000; font-family: Verdana; font-weight: bold; font-size: 12px; background-color: #262626;" />
			<br /><small><?php _e('To find the User ID. Go to Users > Authors &amp; Users. Then search for the user you want to block.<br />Click on the username. In the URL you will see user_id=595 , that is the User ID for that member.','cp_buddypress'); ?>
			<br /><strong><?php _e('Note: They will still be able to earn points with the settings you have set up in the ','cp_buddypress'); ?> <a href="admin.php?page=cp_admin_config"><?php _e('CubePoints Configure page. ','cp_buddypress'); ?></a>.
			<br /><?php _e('There is a <a href="http://cubepoints.com/forums/topic/ignore-users-points-module/" target="_blank">Ignore Users Points Module</a> that will take care of that though.','cp_buddypress'); ?></strong></small>
			</td>
			<td><input type="button" onclick="document.getElementById('bp_spammer_cp_bp').value=''" value="<?php _e('Remove User IDs','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr>
			<th colspan="3"><h2><?php _e('Spam Control','cp_buddypress'); ?></h2></th>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="3"><label for="bp_messagespamcheck_cp_bp"><?php _e('Enter how many points a member must have before they have access to compose a message to another member.','cp_buddypress'); ?></label></th>
		</tr>
		<tr>
			<td valign="middle" colspan="2"><input type="text" id="bp_messagespamcheck_cp_bp" name="bp_messagespamcheck_cp_bp" value="<?php echo get_option('bp_messagespamcheck_cp_bp'); ?>" size="20" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_messagespamcheck_cp_bp').value='0'" value="<?php _e('Allow anyone to compose messages','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="3"><label for="bp_groupcreatespamcheck_cp_bp"><?php _e('Enter how many points a member must have before they have access to create a group.','cp_buddypress'); ?></label></th>
		</tr>
		<tr>
			<td valign="middle" colspan="2"><input type="text" id="bp_groupcreatespamcheck_cp_bp" name="bp_groupcreatespamcheck_cp_bp" value="<?php echo get_option('bp_groupcreatespamcheck_cp_bp'); ?>" size="20" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_groupcreatespamcheck_cp_bp').value='0'" value="<?php _e('Allow anyone to create groups','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="3"><label for="bp_update_n_reply_spamcheck_cp_bp"><?php _e('Enter how many points a member must have before they have access to updates & replies.','cp_buddypress'); ?></label></th>
		</tr>
		<tr>
			<td valign="middle" colspan="2"><input type="text" id="bp_update_n_reply_spamcheck_cp_bp" name="bp_update_n_reply_spamcheck_cp_bp" value="<?php echo get_option('bp_update_n_reply_spamcheck_cp_bp'); ?>" size="20" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_update_n_reply_spamcheck_cp_bp').value='0'" value="<?php _e('Allow anyone access to updates & replies','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr>
			<th colspan="3"><h2><?php _e('Point Logs Settings','cp_buddypress'); ?></h2></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_points_logs_per_page_cp_bp"><?php _e('How many point logs per page','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_points_logs_per_page_cp_bp" name="bp_points_logs_per_page_cp_bp" value="<?php echo get_option('bp_points_logs_per_page_cp_bp'); ?>" size="20" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_points_logs_per_page_cp_bp').value='20'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_slug_cp_bp"><?php _e('BuddyPress Slug','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_slug_cp_bp" name="bp_slug_cp_bp" value="<?php echo get_option('bp_slug_cp_bp'); ?>" size="20" />
			<br /><small>
			<?php _e('This will change the slug in bold below.','cp_buddypress'); ?><br />yoursite.com/members/username/<b><?php echo get_option('bp_slug_cp_bp'); ?></b>/</small>
			</td>
			<td><input type="button" onclick="document.getElementById('bp_slug_cp_bp').value='cubepoints'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="bp_tallyuserpoints_cp_bp"><?php _e('Show community point grand total on points log page','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="2">
			<input id="bp_tallyuserpoints_cp_bp" name="bp_tallyuserpoints_cp_bp" type="checkbox" value="1" <?php if(get_option('bp_tallyuserpoints_cp_bp')) {echo 'checked="checked"';} ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_sitewide_menu_cp_bp"><?php _e('Show Global Points Log Menu Item','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="2">
			<input id="bp_sitewide_menu_cp_bp" name="bp_sitewide_menu_cp_bp" type="checkbox" value="1" <?php if(get_option('bp_sitewide_menu_cp_bp')) {echo 'checked="checked"';} ?> />
			</td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_sitewidemtitle_cp_bp"><?php _e('Global Points Log Menu Title','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_sitewidemtitle_cp_bp" name="bp_sitewidemtitle_cp_bp" value="<?php echo get_option('bp_sitewidemtitle_cp_bp'); ?>" size="24" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_sitewidemtitle_cp_bp').value='Sitewide Points'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>		
		<tr>
			<th colspan="3"><h2><?php _e('Point Legend','cp_buddypress'); ?></h2></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_earnpoints_cp_bp"><?php _e('Show Points Legend Menu Item','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="2">
			<input id="bp_earnpoints_menu_cp_bp" name="bp_earnpoints_menu_cp_bp" type="checkbox" value="1" <?php if(get_option('bp_earnpoints_menu_cp_bp')) {echo 'checked="checked"';} ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_earnpoints_menutitle_cp_bp"><?php _e('Menu Title','cp_buddypress'); ?>:</label></th>
			<td valign="middle"><input type="text" id="bp_earnpoints_menutitle_cp_bp" name="bp_earnpoints_menutitle_cp_bp" value="<?php echo get_option('bp_earnpoints_menutitle_cp_bp'); ?>" size="24" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_earnpoints_menutitle_cp_bp').value='Point Legend'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_earnpointstitle_cp_bp"><?php _e('Points Legend Title','cp_buddypress'); ?>:</label><br /><small><?php _e('HTML is allowed','cp_buddypress'); ?></small></th>
			<td valign="middle">
			<input type="text" id="bp_earnpointstitle_cp_bp" name="bp_earnpointstitle_cp_bp" value="<?php echo get_option('bp_earnpointstitle_cp_bp'); ?>" size="24" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_earnpointstitle_cp_bp').value='Here is how you can earn points'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_earnpoints_extra_cp_bp"><?php _e('Additional Way to earn points','cp_buddypress'); ?>:</label><br /><small><?php _e('Very basic HTML is allowed<br /><br />For example you can put ways to earn points that are not in the Cubepoint settings.','cp_buddypress'); ?></small></th>
			<td valign="middle" colspan="2">
			<textarea rows="10" cols="45" id="bp_earnpoints_extra_cp_bp" name="bp_earnpoints_extra_cp_bp"><?php echo get_option('bp_earnpoints_extra_cp_bp'); ?></textarea>
			</td>
		</tr>		
		<!--
		<tr valign="top">
			<th scope="row"><label for="bp_leaderboard_onoff_cp_bp"><?php _e('Show Points Leaderboard','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="2">
			<input id="bp_earnpoints_menu_cp_bp" name="bp_leaderboard_onoff_cp_bp" type="checkbox" value="1" <?php if(get_option('bp_leaderboard_onoff_cp_bp')) {echo 'checked="checked"';} ?> />
			</td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_leaderboardtitle_cp_bp"><?php _e('Top Members title','cp_buddypress'); ?>:</label><br /><small><?php _e('HTML is allowed','cp_buddypress'); ?></small></th>
			<td valign="middle"><input type="text" id="bp_leaderboardtitle_cp_bp" name="bp_leaderboardtitle_cp_bp" value="<?php echo get_option('bp_leaderboardtitle_cp_bp'); ?>" size="24" />
			</td>
			<td><input type="button" onclick="document.getElementById('bp_leaderboardtitle_cp_bp').value='Top Members'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>		
		<tr valign="top">
		<th scope="row"><label for="bp_leaderboard_cp_bp"><?php _e('Points Leaderboard:','cp_buddypress'); ?></label></th>
			<td valign="middle"><input type="text" id="bp_leaderboard_cp_bp" name="bp_leaderboard_cp_bp" value="<?php echo get_option('bp_leaderboard_cp_bp'); ?>" size="2" />
			<?php _e('Put in how many you want to show up','cp_buddypress'); ?>
			</td>
			<td><input type="button" onclick="document.getElementById('bp_leaderboard_cp_bp').value='0'" value="<?php _e('Show all members','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		-->
		<tr>
		<th colspan="3">
		    <a onclick="document.getElementById('codes').style.display='block';" href="#"><?php _e('Want to use the shortcode [earnpoints] ?','cp_buddypress'); ?></a>
		</th>
		</tr>		
		</table>
		</div>
		
		<!-- AWARDS SETTINGS -->
		
		<div id="awardssettings" style="display:none;">
	 <table class="widefat fixed" cellspacing="0">
		<tr>
		    <th colspan="4"><h2><?php _e('Awards','cp_buddypress'); ?></h2></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_awards_menu_onoff_cp_bp"><?php _e('Show Awards Menu Item','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="2">
			<input id="bp_awards_menu_onoff_cp_bp" name="bp_awards_menu_onoff_cp_bp" type="checkbox" value="1" <?php if(get_option('bp_awards_menu_onoff_cp_bp')) {echo 'checked="checked"';} ?> />
			</td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="bp_awards_menutitle_cp_bp"><?php _e('Awards Menu Title','cp_buddypress'); ?>:</label></th>
			<td valign="middle">
			<input type="text" id="bp_awards_menutitle_cp_bp" name="bp_awards_menutitle_cp_bp" value="<?php echo get_option('bp_awards_menutitle_cp_bp'); ?>" size="24" />
			</td>
			<td></td>
			<td><input type="button" onclick="document.getElementById('bp_awards_menutitle_cp_bp').value='Awards'" value="<?php _e('Set to default','cp_buddypress'); ?>" class="button" /></td>
		</tr>
		<tr><td colspan="4">	
		<?php _e('<p>Use the full image URL path for the award image. For example http://www.mysite.com/awards/myawesomeimage.jpg 
			     The image will be resized to 75 by 75 pixels. But you can change this in the .css file if you wish. 
			     On the awards value. Put in what you want your members to earn in order to unlock the award. For example 
			     I want my member to have 5 friends before they get this award.</p>','cp_buddypress'); ?>
		</td></tr>
	</table>
	<table class="widefat fixed" cellspacing="0">
	   <thead>	  
	    <tr>
	      <th scope="col" id="action" class="column-name" style="width: 43%;"><?php _e('Award Image', 'cp_buddypress'); ?></th>
	      <th scope="col" id="value" class="column-name" style=""><?php _e('Award Title', 'cp_buddypress'); ?></th>
	      <th scope="col" id="reset" class="column-name" style=""><?php _e('Value Needed for Award', 'cp_buddypress'); ?></th>
	      <th scope="col" id="reset" class="column-name" style=""><?php _e('Disable', 'cp_buddypress'); ?></th>
	    </tr>
	  </thead>
	  <tfoot>
	    <tr>
	    <th colspan="4" id="default" class="column-name"></th>
	    </tr>
	  </tfoot>
		<tr valign="top" class="column-name">
			<th scope="row" colspan="4"><h3><label for="bp_award_grouptitle_cp_bp"><?php _e('Groups Created Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_groupimg_cp_bp" name="bp_award_groupimg_cp_bp" value="<?php echo get_option('bp_award_groupimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_grouptitle_cp_bp" name="bp_award_grouptitle_cp_bp" value="<?php echo get_option('bp_award_grouptitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_groupvalue_cp_bp" name="bp_award_groupvalue_cp_bp" value="<?php echo get_option('bp_award_groupvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_groupvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_group2img_cp_bp" name="bp_award_group2img_cp_bp" value="<?php echo get_option('bp_award_group2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group2title_cp_bp" name="bp_award_group2title_cp_bp" value="<?php echo get_option('bp_award_group2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group2value_cp_bp" name="bp_award_group2value_cp_bp" value="<?php echo get_option('bp_award_group2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_group2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>	
		<tr>
			<td><input type="text" id="bp_award_group3img_cp_bp" name="bp_award_group3img_cp_bp" value="<?php echo get_option('bp_award_group3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group3title_cp_bp" name="bp_award_group3title_cp_bp" value="<?php echo get_option('bp_award_group3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group3value_cp_bp" name="bp_award_group3value_cp_bp" value="<?php echo get_option('bp_award_group3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_group3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_group4img_cp_bp" name="bp_award_group4img_cp_bp" value="<?php echo get_option('bp_award_group4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group4title_cp_bp" name="bp_award_group4title_cp_bp" value="<?php echo get_option('bp_award_group4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group4value_cp_bp" name="bp_award_group4value_cp_bp" value="<?php echo get_option('bp_award_group4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_group4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_group5img_cp_bp" name="bp_award_group5img_cp_bp" value="<?php echo get_option('bp_award_group5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group5title_cp_bp" name="bp_award_group5title_cp_bp" value="<?php echo get_option('bp_award_group5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_group5value_cp_bp" name="bp_award_group5value_cp_bp" value="<?php echo get_option('bp_award_group5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_group5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_friendtitle_cp_bp"><?php _e('Friends Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_friendimg_cp_bp" name="bp_award_friendimg_cp_bp" value="<?php echo get_option('bp_award_friendimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friendtitle_cp_bp" name="bp_award_friendtitle_cp_bp" value="<?php echo get_option('bp_award_friendtitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friendvalue_cp_bp" name="bp_award_friendvalue_cp_bp" value="<?php echo get_option('bp_award_friendvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_friendvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_friend2img_cp_bp" name="bp_award_friend2img_cp_bp" value="<?php echo get_option('bp_award_friend2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend2title_cp_bp" name="bp_award_friend2title_cp_bp" value="<?php echo get_option('bp_award_friend2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend2value_cp_bp" name="bp_award_friend2value_cp_bp" value="<?php echo get_option('bp_award_friend2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_friend2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_friend3img_cp_bp" name="bp_award_friend3img_cp_bp" value="<?php echo get_option('bp_award_friend3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend3title_cp_bp" name="bp_award_friend3title_cp_bp" value="<?php echo get_option('bp_award_friend3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend3value_cp_bp" name="bp_award_friend3value_cp_bp" value="<?php echo get_option('bp_award_friend3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_friend3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_friend4img_cp_bp" name="bp_award_friend4img_cp_bp" value="<?php echo get_option('bp_award_friend4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend4title_cp_bp" name="bp_award_friend4title_cp_bp" value="<?php echo get_option('bp_award_friend4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend4value_cp_bp" name="bp_award_friend4value_cp_bp" value="<?php echo get_option('bp_award_friend4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_friend4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_friend5img_cp_bp" name="bp_award_friend5img_cp_bp" value="<?php echo get_option('bp_award_friend5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend5title_cp_bp" name="bp_award_friend5title_cp_bp" value="<?php echo get_option('bp_award_friend5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_friend5value_cp_bp" name="bp_award_friend5value_cp_bp" value="<?php echo get_option('bp_award_friend5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_friend5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_updatetitle_cp_bp"><?php _e('BuddyPress Updates Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_updateimg_cp_bp" name="bp_award_updateimg_cp_bp" value="<?php echo get_option('bp_award_updateimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_updatetitle_cp_bp" name="bp_award_updatetitle_cp_bp" value="<?php echo get_option('bp_award_updatetitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_updatevalue_cp_bp" name="bp_award_updatevalue_cp_bp" value="<?php echo get_option('bp_award_updatevalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_updatevalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_update2img_cp_bp" name="bp_award_update2img_cp_bp" value="<?php echo get_option('bp_award_update2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update2title_cp_bp" name="bp_award_update2title_cp_bp" value="<?php echo get_option('bp_award_update2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update2value_cp_bp" name="bp_award_update2value_cp_bp" value="<?php echo get_option('bp_award_update2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_update2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_update3img_cp_bp" name="bp_award_update3img_cp_bp" value="<?php echo get_option('bp_award_update3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update3title_cp_bp" name="bp_award_update3title_cp_bp" value="<?php echo get_option('bp_award_update3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update3value_cp_bp" name="bp_award_update3value_cp_bp" value="<?php echo get_option('bp_award_update3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_update3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_update4img_cp_bp" name="bp_award_update4img_cp_bp" value="<?php echo get_option('bp_award_update4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update4title_cp_bp" name="bp_award_update4title_cp_bp" value="<?php echo get_option('bp_award_update4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update4value_cp_bp" name="bp_award_update4value_cp_bp" value="<?php echo get_option('bp_award_update4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_update4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_update5img_cp_bp" name="bp_award_update5img_cp_bp" value="<?php echo get_option('bp_award_update5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update5title_cp_bp" name="bp_award_update5title_cp_bp" value="<?php echo get_option('bp_award_update5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_update5value_cp_bp" name="bp_award_update5value_cp_bp" value="<?php echo get_option('bp_award_update5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_update5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_replytitle_cp_bp"><?php _e('BuddyPress Replies Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_replyimg_cp_bp" name="bp_award_replyimg_cp_bp" value="<?php echo get_option('bp_award_replyimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_replytitle_cp_bp" name="bp_award_replytitle_cp_bp" value="<?php echo get_option('bp_award_replytitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_replyvalue_cp_bp" name="bp_award_replyvalue_cp_bp" value="<?php echo get_option('bp_award_replyvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_replyvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_reply2img_cp_bp" name="bp_award_reply2img_cp_bp" value="<?php echo get_option('bp_award_reply2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply2title_cp_bp" name="bp_award_reply2title_cp_bp" value="<?php echo get_option('bp_award_reply2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply2value_cp_bp" name="bp_award_reply2value_cp_bp" value="<?php echo get_option('bp_award_reply2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_reply2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_reply3img_cp_bp" name="bp_award_reply3img_cp_bp" value="<?php echo get_option('bp_award_reply3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply3title_cp_bp" name="bp_award_reply3title_cp_bp" value="<?php echo get_option('bp_award_reply3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply3value_cp_bp" name="bp_award_reply3value_cp_bp" value="<?php echo get_option('bp_award_reply3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_reply3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_reply4img_cp_bp" name="bp_award_reply4img_cp_bp" value="<?php echo get_option('bp_award_reply4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply4title_cp_bp" name="bp_award_reply4title_cp_bp" value="<?php echo get_option('bp_award_reply4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply4value_cp_bp" name="bp_award_reply4value_cp_bp" value="<?php echo get_option('bp_award_reply4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_reply4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_reply5img_cp_bp" name="bp_award_reply5img_cp_bp" value="<?php echo get_option('bp_award_reply5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply5title_cp_bp" name="bp_award_reply5title_cp_bp" value="<?php echo get_option('bp_award_reply5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_reply5value_cp_bp" name="bp_award_reply5value_cp_bp" value="<?php echo get_option('bp_award_reply5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_reply5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_forumtopictitle_cp_bp"><?php _e('Group Forum New Topic Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumtopicimg_cp_bp" name="bp_award_forumtopicimg_cp_bp" value="<?php echo get_option('bp_award_forumtopicimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopictitle_cp_bp" name="bp_award_forumtopictitle_cp_bp" value="<?php echo get_option('bp_award_forumtopictitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopicvalue_cp_bp" name="bp_award_forumtopicvalue_cp_bp" value="<?php echo get_option('bp_award_forumtopicvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumtopicvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumtopic2img_cp_bp" name="bp_award_forumtopic2img_cp_bp" value="<?php echo get_option('bp_award_forumtopic2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic2title_cp_bp" name="bp_award_forumtopic2title_cp_bp" value="<?php echo get_option('bp_award_forumtopic2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic2value_cp_bp" name="bp_award_forumtopic2value_cp_bp" value="<?php echo get_option('bp_award_forumtopic2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumtopic2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumtopic3img_cp_bp" name="bp_award_forumtopic3img_cp_bp" value="<?php echo get_option('bp_award_forumtopic3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic3title_cp_bp" name="bp_award_forumtopic3title_cp_bp" value="<?php echo get_option('bp_award_forumtopic3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic3value_cp_bp" name="bp_award_forumtopic3value_cp_bp" value="<?php echo get_option('bp_award_forumtopic3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumtopic3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumtopic4img_cp_bp" name="bp_award_forumtopic4img_cp_bp" value="<?php echo get_option('bp_award_forumtopic4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic4title_cp_bp" name="bp_award_forumtopic4title_cp_bp" value="<?php echo get_option('bp_award_forumtopic4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic4value_cp_bp" name="bp_award_forumtopic4value_cp_bp" value="<?php echo get_option('bp_award_forumtopic4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumtopic4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumtopic5img_cp_bp" name="bp_award_forumtopic5img_cp_bp" value="<?php echo get_option('bp_award_forumtopic5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic5title_cp_bp" name="bp_award_forumtopic5title_cp_bp" value="<?php echo get_option('bp_award_forumtopic5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumtopic5value_cp_bp" name="bp_award_forumtopic5value_cp_bp" value="<?php echo get_option('bp_award_forumtopic5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumtopic5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_forumreplytitle_cp_bp"><?php _e('Group Forum Replies Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumreplyimg_cp_bp" name="bp_award_forumreplyimg_cp_bp" value="<?php echo get_option('bp_award_forumreplyimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreplytitle_cp_bp" name="bp_award_forumreplytitle_cp_bp" value="<?php echo get_option('bp_award_forumreplytitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreplyvalue_cp_bp" name="bp_award_forumreplyvalue_cp_bp" value="<?php echo get_option('bp_award_forumreplyvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumreplyvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumreply2img_cp_bp" name="bp_award_forumreply2img_cp_bp" value="<?php echo get_option('bp_award_forumreply2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply2title_cp_bp" name="bp_award_forumreply2title_cp_bp" value="<?php echo get_option('bp_award_forumreply2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply2value_cp_bp" name="bp_award_forumreply2value_cp_bp" value="<?php echo get_option('bp_award_forumreply2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumreply2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumreply3img_cp_bp" name="bp_award_forumreply3img_cp_bp" value="<?php echo get_option('bp_award_forumreply3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply3title_cp_bp" name="bp_award_forumreply3title_cp_bp" value="<?php echo get_option('bp_award_forumreply3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply3value_cp_bp" name="bp_award_forumreply3value_cp_bp" value="<?php echo get_option('bp_award_forumreply3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumreply3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumreply4img_cp_bp" name="bp_award_forumreply4img_cp_bp" value="<?php echo get_option('bp_award_forumreply4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply4title_cp_bp" name="bp_award_forumreply4title_cp_bp" value="<?php echo get_option('bp_award_forumreply4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply4value_cp_bp" name="bp_award_forumreply4value_cp_bp" value="<?php echo get_option('bp_award_forumreply4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumreply4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_forumreply5img_cp_bp" name="bp_award_forumreply5img_cp_bp" value="<?php echo get_option('bp_award_forumreply5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply5title_cp_bp" name="bp_award_forumreply5title_cp_bp" value="<?php echo get_option('bp_award_forumreply5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_forumreply5value_cp_bp" name="bp_award_forumreply5value_cp_bp" value="<?php echo get_option('bp_award_forumreply5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_forumreply5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_blogcommenttitle_cp_bp"><?php _e('Blog Comments Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogcommentimg_cp_bp" name="bp_award_blogcommentimg_cp_bp" value="<?php echo get_option('bp_award_blogcommentimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcommenttitle_cp_bp" name="bp_award_blogcommenttitle_cp_bp" value="<?php echo get_option('bp_award_blogcommenttitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcommentvalue_cp_bp" name="bp_award_blogcommentvalue_cp_bp" value="<?php echo get_option('bp_award_blogcommentvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogcommentvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogcomment2img_cp_bp" name="bp_award_blogcomment2img_cp_bp" value="<?php echo get_option('bp_award_blogcomment2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment2title_cp_bp" name="bp_award_blogcomment2title_cp_bp" value="<?php echo get_option('bp_award_blogcomment2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment2value_cp_bp" name="bp_award_blogcomment2value_cp_bp" value="<?php echo get_option('bp_award_blogcomment2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogcomment2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogcomment3img_cp_bp" name="bp_award_blogcomment3img_cp_bp" value="<?php echo get_option('bp_award_blogcomment3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment3title_cp_bp" name="bp_award_blogcomment3title_cp_bp" value="<?php echo get_option('bp_award_blogcomment3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment3value_cp_bp" name="bp_award_blogcomment3value_cp_bp" value="<?php echo get_option('bp_award_blogcomment3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogcomment3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogcomment4img_cp_bp" name="bp_award_blogcomment4img_cp_bp" value="<?php echo get_option('bp_award_blogcomment4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment4title_cp_bp" name="bp_award_blogcomment4title_cp_bp" value="<?php echo get_option('bp_award_blogcomment4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment4value_cp_bp" name="bp_award_blogcomment4value_cp_bp" value="<?php echo get_option('bp_award_blogcomment4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogcomment4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogcomment5img_cp_bp" name="bp_award_blogcomment5img_cp_bp" value="<?php echo get_option('bp_award_blogcomment5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment5title_cp_bp" name="bp_award_blogcomment5title_cp_bp" value="<?php echo get_option('bp_award_blogcomment5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogcomment5value_cp_bp" name="bp_award_blogcomment5value_cp_bp" value="<?php echo get_option('bp_award_blogcomment5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogcomment5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_bloggertitle_cp_bp"><?php _e('Blogger Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_bloggerimg_cp_bp" name="bp_award_bloggerimg_cp_bp" value="<?php echo get_option('bp_award_bloggerimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggertitle_cp_bp" name="bp_award_bloggertitle_cp_bp" value="<?php echo get_option('bp_award_bloggertitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggervalue_cp_bp" name="bp_award_bloggervalue_cp_bp" value="<?php echo get_option('bp_award_bloggervalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_bloggervalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogger2img_cp_bp" name="bp_award_blogger2img_cp_bp" value="<?php echo get_option('bp_award_blogger2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger2title_cp_bp" name="bp_award_blogger2title_cp_bp" value="<?php echo get_option('bp_award_blogger2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger2value_cp_bp" name="bp_award_blogger2value_cp_bp" value="<?php echo get_option('bp_award_blogger2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogger2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogger3img_cp_bp" name="bp_award_blogger3img_cp_bp" value="<?php echo get_option('bp_award_blogger3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger3title_cp_bp" name="bp_award_blogger3title_cp_bp" value="<?php echo get_option('bp_award_blogger3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger3value_cp_bp" name="bp_award_blogger3value_cp_bp" value="<?php echo get_option('bp_award_blogger3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogger3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogger4img_cp_bp" name="bp_award_blogger4img_cp_bp" value="<?php echo get_option('bp_award_blogger4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger4title_cp_bp" name="bp_award_blogger4title_cp_bp" value="<?php echo get_option('bp_award_blogger4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger4value_cp_bp" name="bp_award_blogger4value_cp_bp" value="<?php echo get_option('bp_award_blogger4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogger4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_blogger5img_cp_bp" name="bp_award_blogger5img_cp_bp" value="<?php echo get_option('bp_award_blogger5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger5title_cp_bp" name="bp_award_blogger5title_cp_bp" value="<?php echo get_option('bp_award_blogger5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_blogger5value_cp_bp" name="bp_award_blogger5value_cp_bp" value="<?php echo get_option('bp_award_blogger5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_blogger5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>	
		<!-- Blogger in a category awards -->
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_bloggercattitle_cp_bp"><?php _e('Blogger in a Specific Category Award','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		
		<tr>
		<th scope="row" colspan="4" align="left"><label for="bp_award_bloggercatselector_cp_bp"><?php _e('Enter in Category ID','cp_buddypress'); ?>:</label>
		<input type="text" id="bp_award_bloggercatselector_cp_bp" name="bp_award_bloggercatselector_cp_bp" value="<?php echo get_option('bp_award_bloggercatselector_cp_bp'); ?>" size="5" />
		</th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_bloggercatimg_cp_bp" name="bp_award_bloggercatimg_cp_bp" value="<?php echo get_option('bp_award_bloggercatimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercattitle_cp_bp" name="bp_award_bloggercattitle_cp_bp" value="<?php echo get_option('bp_award_bloggercattitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercatvalue_cp_bp" name="bp_award_bloggercatvalue_cp_bp" value="<?php echo get_option('bp_award_bloggercatvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_bloggercatvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
		<th scope="row" colspan="4" align="left"><label for="bp_award_bloggercatselector2_cp_bp"><?php _e('Enter in Category ID','cp_buddypress'); ?>:</label>
		<input type="text" id="bp_award_bloggercatselector2_cp_bp" name="bp_award_bloggercatselector2_cp_bp" value="<?php echo get_option('bp_award_bloggercatselector2_cp_bp'); ?>" size="5" />
		</th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_bloggercat2img_cp_bp" name="bp_award_bloggercat2img_cp_bp" value="<?php echo get_option('bp_award_bloggercat2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat2title_cp_bp" name="bp_award_bloggercat2title_cp_bp" value="<?php echo get_option('bp_award_bloggercat2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat2value_cp_bp" name="bp_award_bloggercat2value_cp_bp" value="<?php echo get_option('bp_award_bloggercat2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_bloggercat2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
		<th scope="row" colspan="4" align="left"><label for="bp_award_bloggercatselector3_cp_bp"><?php _e('Enter in Category ID','cp_buddypress'); ?>:</label>
		<input type="text" id="bp_award_bloggercatselector3_cp_bp" name="bp_award_bloggercatselector3_cp_bp" value="<?php echo get_option('bp_award_bloggercatselector3_cp_bp'); ?>" size="5" />
		</th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_bloggercat3img_cp_bp" name="bp_award_bloggercat3img_cp_bp" value="<?php echo get_option('bp_award_bloggercat3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat3title_cp_bp" name="bp_award_bloggercat3title_cp_bp" value="<?php echo get_option('bp_award_bloggercat3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat3value_cp_bp" name="bp_award_bloggercat3value_cp_bp" value="<?php echo get_option('bp_award_bloggercat3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_bloggercat3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
		<th scope="row" colspan="4" align="left"><label for="bp_award_bloggercatselector4_cp_bp"><?php _e('Enter in Category ID','cp_buddypress'); ?>:</label>
		<input type="text" id="bp_award_bloggercatselector4_cp_bp" name="bp_award_bloggercatselector4_cp_bp" value="<?php echo get_option('bp_award_bloggercatselector4_cp_bp'); ?>" size="5" />
		</th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_bloggercat4img_cp_bp" name="bp_award_bloggercat4img_cp_bp" value="<?php echo get_option('bp_award_bloggercat4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat4title_cp_bp" name="bp_award_bloggercat4title_cp_bp" value="<?php echo get_option('bp_award_bloggercat4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat4value_cp_bp" name="bp_award_bloggercat4value_cp_bp" value="<?php echo get_option('bp_award_bloggercat4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_bloggercat4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
		<th scope="row" colspan="4" align="left"><label for="bp_award_bloggercatselector5_cp_bp"><?php _e('Enter in Category ID','cp_buddypress'); ?>:</label>
		<input type="text" id="bp_award_bloggercatselector5_cp_bp" name="bp_award_bloggercatselector5_cp_bp" value="<?php echo get_option('bp_award_bloggercatselector5_cp_bp'); ?>" size="5" />
		</th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_bloggercat5img_cp_bp" name="bp_award_bloggercat5img_cp_bp" value="<?php echo get_option('bp_award_bloggercat5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat5title_cp_bp" name="bp_award_bloggercat5title_cp_bp" value="<?php echo get_option('bp_award_bloggercat5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_bloggercat5value_cp_bp" name="bp_award_bloggercat5value_cp_bp" value="<?php echo get_option('bp_award_bloggercat5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_bloggercat5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<!-- Donation Awards -->
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_donationstitle_cp_bp"><?php _e('Point Donation Award','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_donationsimg_cp_bp" name="bp_award_donationsimg_cp_bp" value="<?php echo get_option('bp_award_donationsimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donationstitle_cp_bp" name="bp_award_donationstitle_cp_bp" value="<?php echo get_option('bp_award_donationstitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donationsvalue_cp_bp" name="bp_award_donationsvalue_cp_bp" value="<?php echo get_option('bp_award_donationsvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_donationsvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_donations2img_cp_bp" name="bp_award_donations2img_cp_bp" value="<?php echo get_option('bp_award_donations2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations2title_cp_bp" name="bp_award_donations2title_cp_bp" value="<?php echo get_option('bp_award_donations2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations2value_cp_bp" name="bp_award_donations2value_cp_bp" value="<?php echo get_option('bp_award_donations2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_donations2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_donations3img_cp_bp" name="bp_award_donations3img_cp_bp" value="<?php echo get_option('bp_award_donations3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations3title_cp_bp" name="bp_award_donations3title_cp_bp" value="<?php echo get_option('bp_award_donations3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations3value_cp_bp" name="bp_award_donations3value_cp_bp" value="<?php echo get_option('bp_award_donations3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_donations3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>                
		<tr>
			<td><input type="text" id="bp_award_donations4img_cp_bp" name="bp_award_donations4img_cp_bp" value="<?php echo get_option('bp_award_donations4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations4title_cp_bp" name="bp_award_donations4title_cp_bp" value="<?php echo get_option('bp_award_donations4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations4value_cp_bp" name="bp_award_donations4value_cp_bp" value="<?php echo get_option('bp_award_donations4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_donations4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_donations5img_cp_bp" name="bp_award_donations5img_cp_bp" value="<?php echo get_option('bp_award_donations5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations5title_cp_bp" name="bp_award_donations5title_cp_bp" value="<?php echo get_option('bp_award_donations5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_donations5value_cp_bp" name="bp_award_donations5value_cp_bp" value="<?php echo get_option('bp_award_donations5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_donations5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>                		
		<!-- Daily Login -->
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_dailylogintitle_cp_bp"><?php _e('Logging in Award','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_dailyloginimg_cp_bp" name="bp_award_dailyloginimg_cp_bp" value="<?php echo get_option('bp_award_dailyloginimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogintitle_cp_bp" name="bp_award_dailylogintitle_cp_bp" value="<?php echo get_option('bp_award_dailylogintitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailyloginvalue_cp_bp" name="bp_award_dailyloginvalue_cp_bp" value="<?php echo get_option('bp_award_dailyloginvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_dailyloginvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_dailylogin2img_cp_bp" name="bp_award_dailylogin2img_cp_bp" value="<?php echo get_option('bp_award_dailylogin2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin2title_cp_bp" name="bp_award_dailylogin2title_cp_bp" value="<?php echo get_option('bp_award_dailylogin2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin2value_cp_bp" name="bp_award_dailylogin2value_cp_bp" value="<?php echo get_option('bp_award_dailylogin2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_dailylogin2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_dailylogin3img_cp_bp" name="bp_award_dailylogin3img_cp_bp" value="<?php echo get_option('bp_award_dailylogin3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin3title_cp_bp" name="bp_award_dailylogin3title_cp_bp" value="<?php echo get_option('bp_award_dailylogin3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin3value_cp_bp" name="bp_award_dailylogin3value_cp_bp" value="<?php echo get_option('bp_award_dailylogin3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_dailylogin3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_dailylogin4img_cp_bp" name="bp_award_dailylogin4img_cp_bp" value="<?php echo get_option('bp_award_dailylogin4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin4title_cp_bp" name="bp_award_dailylogin4title_cp_bp" value="<?php echo get_option('bp_award_dailylogin4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin4value_cp_bp" name="bp_award_dailylogin4value_cp_bp" value="<?php echo get_option('bp_award_dailylogin4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_dailylogin4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_dailylogin5img_cp_bp" name="bp_award_dailylogin5img_cp_bp" value="<?php echo get_option('bp_award_dailylogin5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin5title_cp_bp" name="bp_award_dailylogin5title_cp_bp" value="<?php echo get_option('bp_award_dailylogin5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_dailylogin5value_cp_bp" name="bp_award_dailylogin5value_cp_bp" value="<?php echo get_option('bp_award_dailylogin5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_dailylogin5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<!-- Point Levels -->
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_points_1title_cp_bp"><?php _e('Point Level Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_1img_cp_bp" name="bp_award_points_1img_cp_bp" value="<?php echo get_option('bp_award_points_1img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_1title_cp_bp" name="bp_award_points_1title_cp_bp" value="<?php echo get_option('bp_award_points_1title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_1value_cp_bp" name="bp_award_points_1value_cp_bp" value="<?php echo get_option('bp_award_points_1value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_1value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_2img_cp_bp" name="bp_award_points_2img_cp_bp" value="<?php echo get_option('bp_award_points_2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_2title_cp_bp" name="bp_award_points_2title_cp_bp" value="<?php echo get_option('bp_award_points_2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_2value_cp_bp" name="bp_award_points_2value_cp_bp" value="<?php echo get_option('bp_award_points_2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_3img_cp_bp" name="bp_award_points_3img_cp_bp" value="<?php echo get_option('bp_award_points_3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_3title_cp_bp" name="bp_award_points_3title_cp_bp" value="<?php echo get_option('bp_award_points_3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_3value_cp_bp" name="bp_award_points_3value_cp_bp" value="<?php echo get_option('bp_award_points_3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_4img_cp_bp" name="bp_award_points_4img_cp_bp" value="<?php echo get_option('bp_award_points_4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_4title_cp_bp" name="bp_award_points_4title_cp_bp" value="<?php echo get_option('bp_award_points_4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_4value_cp_bp" name="bp_award_points_4value_cp_bp" value="<?php echo get_option('bp_award_points_4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_5img_cp_bp" name="bp_award_points_5img_cp_bp" value="<?php echo get_option('bp_award_points_5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_5title_cp_bp" name="bp_award_points_5title_cp_bp" value="<?php echo get_option('bp_award_points_5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_5value_cp_bp" name="bp_award_points_5value_cp_bp" value="<?php echo get_option('bp_award_points_5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_6img_cp_bp" name="bp_award_points_6img_cp_bp" value="<?php echo get_option('bp_award_points_6img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_6title_cp_bp" name="bp_award_points_6title_cp_bp" value="<?php echo get_option('bp_award_points_6title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_6value_cp_bp" name="bp_award_points_6value_cp_bp" value="<?php echo get_option('bp_award_points_6value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_6value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_7img_cp_bp" name="bp_award_points_7img_cp_bp" value="<?php echo get_option('bp_award_points_7img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_7title_cp_bp" name="bp_award_points_7title_cp_bp" value="<?php echo get_option('bp_award_points_7title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_7value_cp_bp" name="bp_award_points_7value_cp_bp" value="<?php echo get_option('bp_award_points_7value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_7value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_8img_cp_bp" name="bp_award_points_8img_cp_bp" value="<?php echo get_option('bp_award_points_8img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_8title_cp_bp" name="bp_award_points_8title_cp_bp" value="<?php echo get_option('bp_award_points_8title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_8value_cp_bp" name="bp_award_points_8value_cp_bp" value="<?php echo get_option('bp_award_points_8value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_8value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_9img_cp_bp" name="bp_award_points_9img_cp_bp" value="<?php echo get_option('bp_award_points_9img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_9title_cp_bp" name="bp_award_points_9title_cp_bp" value="<?php echo get_option('bp_award_points_9title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_9value_cp_bp" name="bp_award_points_9value_cp_bp" value="<?php echo get_option('bp_award_points_9value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_9value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_points_10img_cp_bp" name="bp_award_points_10img_cp_bp" value="<?php echo get_option('bp_award_points_10img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_10title_cp_bp" name="bp_award_points_10title_cp_bp" value="<?php echo get_option('bp_award_points_10title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_points_10value_cp_bp" name="bp_award_points_10value_cp_bp" value="<?php echo get_option('bp_award_points_10value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_points_10value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<!-- Simple Press Forum Support -->
		<tr valign="top">
		<th scope="row" colspan="4"><h3><label for="bp_award_spf_forumtitle_cp_bp"><?php _e('SimplePress: Forum Posts Awards','cp_buddypress'); ?>:</label></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="4"><label for="bp_spf_support_onoff_cp_bp"><?php _e('Enable <a href="http://simple-press.com/">SimplePress: Forum </a>Support','cp_buddypress'); ?>:</label>
			<input id="bp_spf_support_onoff_cp_bp" name="bp_spf_support_onoff_cp_bp" type="checkbox" value="1" <?php if(get_option('bp_spf_support_onoff_cp_bp')) {echo 'checked="checked"';} ?> />
			</th>
		</tr>		
		<tr>
			<td><input type="text" id="bp_award_spf_forumimg_cp_bp" name="bp_award_spf_forumimg_cp_bp" value="<?php echo get_option('bp_award_spf_forumimg_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forumtitle_cp_bp" name="bp_award_spf_forumtitle_cp_bp" value="<?php echo get_option('bp_award_spf_forumtitle_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forumvalue_cp_bp" name="bp_award_spf_forumvalue_cp_bp" value="<?php echo get_option('bp_award_spf_forumvalue_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_spf_forumvalue_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_spf_forum2img_cp_bp" name="bp_award_spf_forum2img_cp_bp" value="<?php echo get_option('bp_award_spf_forum2img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum2title_cp_bp" name="bp_award_spf_forum2title_cp_bp" value="<?php echo get_option('bp_award_spf_forum2title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum2value_cp_bp" name="bp_award_spf_forum2value_cp_bp" value="<?php echo get_option('bp_award_spf_forum2value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_spf_forum2value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_spf_forum3img_cp_bp" name="bp_award_spf_forum3img_cp_bp" value="<?php echo get_option('bp_award_spf_forum3img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum3title_cp_bp" name="bp_award_spf_forum3title_cp_bp" value="<?php echo get_option('bp_award_spf_forum3title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum3value_cp_bp" name="bp_award_spf_forum3value_cp_bp" value="<?php echo get_option('bp_award_spf_forum3value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_spf_forum3value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_spf_forum4img_cp_bp" name="bp_award_spf_forum4img_cp_bp" value="<?php echo get_option('bp_award_spf_forum4img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum4title_cp_bp" name="bp_award_spf_forum4title_cp_bp" value="<?php echo get_option('bp_award_spf_forum4title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum4value_cp_bp" name="bp_award_spf_forum4value_cp_bp" value="<?php echo get_option('bp_award_spf_forum4value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_spf_forum4value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>
		<tr>
			<td><input type="text" id="bp_award_spf_forum5img_cp_bp" name="bp_award_spf_forum5img_cp_bp" value="<?php echo get_option('bp_award_spf_forum5img_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum5title_cp_bp" name="bp_award_spf_forum5title_cp_bp" value="<?php echo get_option('bp_award_spf_forum5title_cp_bp'); ?>" size="24" /></td>
			<td><input type="text" id="bp_award_spf_forum5value_cp_bp" name="bp_award_spf_forum5value_cp_bp" value="<?php echo get_option('bp_award_spf_forum5value_cp_bp'); ?>" size="5" /></td>
			<td><input type="button" onclick="document.getElementById('bp_award_spf_forum5value_cp_bp').value='0'" value="<?php _e('Disable','cp_buddypress'); ?>" class="button" /></td>			
		</tr>		
		<!-- All Awards Earned -->		
		<tr valign="top">
			<th scope="row" colspan="4"><h3><label for="bp_award_earnedall_title_cp_bp"><?php _e('Earned all awards','cp_buddypress'); ?>:</label></h3></th>
		</tr><tr>
			<td><input type="text" id="bp_award_earnedall_img_cp_bp" name="bp_award_earnedall_img_cp_bp" value="<?php echo get_option('bp_award_earnedall_img_cp_bp'); ?>" size="24" /></td>
			<td colspan="2"><input type="text" id="bp_award_earnedall_title_cp_bp" name="bp_award_earnedall_title_cp_bp" value="<?php echo get_option('bp_award_earnedall_title_cp_bp'); ?>" size="35" /></td>
			<td></td>			
		</tr>		
		</table>
		</div>
		
<div id="lotterynbets" style="display:none;">

	<?php 
	if(function_exists('cp_lottery_show_logs')){ ?>

	 <table class="widefat fixed" cellspacing="0">
	  <thead>
		<tr>
		    <td colspan="4" align="center"><strong><?php _e( 'Here you can customize the menu text and link to show your members if they have entered the lottery or bet their points.', 'cp_buddypress' ) ?></strong></td>
		</tr>	  
	  </thead>
	  <tfoot>
	    <tr>
	    <th colspan="4" id="default" class="column-name"></th>
	    </tr>
	  </tfoot>
		<tr valign="top">
		<th scope="row" colspan="4"><h3><?php _e('Lottery #1 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_lottery1_open_cp_bp"><?php _e('Text if they have NOT entered the lottery','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_lottery1_open_cp_bp" name="bp_lottery1_open_cp_bp" value="<?php echo get_option('bp_lottery1_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery1_entered_cp_bp"><?php _e('Text if they have entered the lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery1_entered_cp_bp" name="bp_lottery1_entered_cp_bp" value="<?php echo get_option('bp_lottery1_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery1_url_cp_bp"><?php _e('Enter the link where they can enter lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery1_url_cp_bp" name="bp_lottery1_url_cp_bp" value="<?php echo get_option('bp_lottery1_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="4"><h3><?php _e('Lottery #2 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_lottery2_open_cp_bp"><?php _e('Text if they have NOT entered the lottery','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_lottery2_open_cp_bp" name="bp_lottery2_open_cp_bp" value="<?php echo get_option('bp_lottery2_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery2_entered_cp_bp"><?php _e('Text if they have entered the lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery2_entered_cp_bp" name="bp_lottery2_entered_cp_bp" value="<?php echo get_option('bp_lottery2_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery2_url_cp_bp"><?php _e('Enter the link where they can enter lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery2_url_cp_bp" name="bp_lottery2_url_cp_bp" value="<?php echo get_option('bp_lottery2_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="4"><h3><?php _e('Lottery #3 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_lottery3_open_cp_bp"><?php _e('Text if they have NOT entered the lottery','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_lottery3_open_cp_bp" name="bp_lottery3_open_cp_bp" value="<?php echo get_option('bp_lottery3_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery3_entered_cp_bp"><?php _e('Text if they have entered the lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery3_entered_cp_bp" name="bp_lottery3_entered_cp_bp" value="<?php echo get_option('bp_lottery3_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery3_url_cp_bp"><?php _e('Enter the link where they can enter lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery3_url_cp_bp" name="bp_lottery3_url_cp_bp" value="<?php echo get_option('bp_lottery3_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="4"><h3><?php _e('Lottery #4 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_lottery4_open_cp_bp"><?php _e('Text if they have NOT entered the lottery','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_lottery4_open_cp_bp" name="bp_lottery4_open_cp_bp" value="<?php echo get_option('bp_lottery4_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery4_entered_cp_bp"><?php _e('Text if they have entered the lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery4_entered_cp_bp" name="bp_lottery4_entered_cp_bp" value="<?php echo get_option('bp_lottery4_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery4_url_cp_bp"><?php _e('Enter the link where they can enter lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery4_url_cp_bp" name="bp_lottery4_url_cp_bp" value="<?php echo get_option('bp_lottery4_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
		<th scope="row" colspan="4"><h3><?php _e('Lottery #5 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_lottery5_open_cp_bp"><?php _e('Text if they have NOT entered the lottery','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_lottery5_open_cp_bp" name="bp_lottery5_open_cp_bp" value="<?php echo get_option('bp_lottery5_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery5_entered_cp_bp"><?php _e('Text if they have entered the lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery5_entered_cp_bp" name="bp_lottery5_entered_cp_bp" value="<?php echo get_option('bp_lottery5_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_lottery5_url_cp_bp"><?php _e('Enter the link where they can enter lottery','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_lottery5_url_cp_bp" name="bp_lottery5_url_cp_bp" value="<?php echo get_option('bp_lottery5_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		
		
		<tr valign="top">
		<th scope="row" colspan="4"><h3><?php _e('Gamble #1 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_bet1_open_cp_bp"><?php _e('Text if they have NOT placed a bet','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_bet1_open_cp_bp" name="bp_bet1_open_cp_bp" value="<?php echo get_option('bp_bet1_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet1_entered_cp_bp"><?php _e('Text if they have placed a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet1_entered_cp_bp" name="bp_bet1_entered_cp_bp" value="<?php echo get_option('bp_bet1_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet1_url_cp_bp"><?php _e('Enter the link where they can place a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet1_url_cp_bp" name="bp_bet1_url_cp_bp" value="<?php echo get_option('bp_bet1_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<th scope="row" colspan="4"><h3><?php _e('Gamble #2 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_bet2_open_cp_bp"><?php _e('Text if they have NOT placed a bet','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_bet2_open_cp_bp" name="bp_bet2_open_cp_bp" value="<?php echo get_option('bp_bet2_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet2_entered_cp_bp"><?php _e('Text if they have placed a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet2_entered_cp_bp" name="bp_bet2_entered_cp_bp" value="<?php echo get_option('bp_bet2_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet2_url_cp_bp"><?php _e('Enter the link where they can place a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet2_url_cp_bp" name="bp_bet2_url_cp_bp" value="<?php echo get_option('bp_bet2_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<th scope="row" colspan="4"><h3><?php _e('Gamble #3 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_bet3_open_cp_bp"><?php _e('Text if they have NOT placed a bet','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_bet3_open_cp_bp" name="bp_bet3_open_cp_bp" value="<?php echo get_option('bp_bet3_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet3_entered_cp_bp"><?php _e('Text if they have placed a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet3_entered_cp_bp" name="bp_bet3_entered_cp_bp" value="<?php echo get_option('bp_bet3_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet3_url_cp_bp"><?php _e('Enter the link where they can place a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet3_url_cp_bp" name="bp_bet3_url_cp_bp" value="<?php echo get_option('bp_bet3_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<th scope="row" colspan="4"><h3><?php _e('Gamble #4 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_bet4_open_cp_bp"><?php _e('Text if they have NOT placed a bet','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_bet4_open_cp_bp" name="bp_bet4_open_cp_bp" value="<?php echo get_option('bp_bet4_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet4_entered_cp_bp"><?php _e('Text if they have placed a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet4_entered_cp_bp" name="bp_bet4_entered_cp_bp" value="<?php echo get_option('bp_bet4_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet4_url_cp_bp"><?php _e('Enter the link where they can place a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet4_url_cp_bp" name="bp_bet4_url_cp_bp" value="<?php echo get_option('bp_bet4_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<th scope="row" colspan="4"><h3><?php _e('Gamble #5 Menu Settings','cp_buddypress'); ?></h3></th>
		</tr>
		<tr valign="top">
			<th scope="row" colspan="1"><label for="bp_bet5_open_cp_bp"><?php _e('Text if they have NOT placed a bet','cp_buddypress'); ?>:</label></th>
			<td align="left" colspan="3"><input type="text" id="bp_bet5_open_cp_bp" name="bp_bet5_open_cp_bp" value="<?php echo get_option('bp_bet5_open_cp_bp'); ?>" size="70" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet5_entered_cp_bp"><?php _e('Text if they have placed a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet5_entered_cp_bp" name="bp_bet5_entered_cp_bp" value="<?php echo get_option('bp_bet5_entered_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bp_bet5_url_cp_bp"><?php _e('Enter the link where they can place a bet','cp_buddypress'); ?>:</label></th>
			<td valign="middle" colspan="3"><input type="text" id="bp_bet5_url_cp_bp" name="bp_bet5_url_cp_bp" value="<?php echo get_option('bp_bet5_url_cp_bp'); ?>" size="70" />
			</td>
		</tr>
		</table>

	<?php
	} else { ?>
		
	 <table class="widefat fixed" cellspacing="0">
	  <thead>
		<tr>
		<td colspan="4" align="center"><h3><?php _e( 'You do not have the CubePoints Giveaway & Betting System installed ;(', 'cp_buddypress' ) ?></h3></td>
		</tr>	  
	  </thead>
	  <tfoot>
	    <tr>
	    <th colspan="4" id="default" class="column-name"></th>
	    </tr>
	  </tfoot>
	  <tr valign="top">
		<td colspan="4">
		
		<?php echo '<p align="center"><img src="'.CP_BUDDYPRESS_PATH.'includes/css/lottery2.0-tab1-n-admin1.png" /></p>'; ?>
		
		<h2><?php _e( 'Most Feature Rich Giveaway / Contest Plugin Available For WordPress!', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'I noticed that there are very few giveaway / contest plugins available for WordPress. None of came close to what I truly needed. I got tired of searching and built this plugin and decided to share it with the world.', 'cp_buddypress' ) ?></h3>

		<h2><?php _e( 'CubePoints Support', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'This ties directly into the <a href="http://www.cubepoints.com/">most popular points system</a> available for WordPress. It finally gives your users meaning behind the points they have earned. They value them more and want to keep earning more and more points. Which means more activity on your site.', 'cp_buddypress' ) ?></h3>
		<h3><?php _e( 'To enter a giveaway they must purchase at least 1 ticket using points they have earned by being active on your site. Then other entry options are available to them.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Watch Your Social Sharing Soar', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Your users can get extra entries into your giveaway by sharing your post on Facebook, Google+ & Twitter. They also earn points, so even more incentive for them! Includes support for video & bonus entires.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Increase Your Followers On Your Social Networks', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Reward your users with points for following you On Twitter, Facebook, Google+ & YouTube.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Answer Trivia Questions For Entries', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'You have the option to set up a trivia question, when our users answer it correctly they are granted bonus entires into your giveaway and earn points in the process.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Flexible System', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Set how many points a ticket will cost and the max allowed. Change the text for the social entry steps and set the default twitter text when they click the tweet button. Or you can disable social entries if you wish. Change the word "ticket" to whatever you need it to be.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Giveaway Details', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Your users can see all their entires into the giveaway. Plus it shows them how many entries them have left to complete and their chance of winning. You can choose if you want your users to see entries from everyone else or just be able to see their own.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Pick Your Winners', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'You can easily and quickly shuffle all the entries and pick your winners at random with a couple clicks of a button.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Giveaway Stats', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Your members can see at a glance how many entries they need to complete. Plus their chance of winning as well. Which gives them more urgency to finish all the possible ways to enter your giveaway. Which means more activity on your site and social sharing.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Multiple Giveaways', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'You can run up to 5 active giveaways with ease.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Banner System', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Promote your giveaway on your site with a image banner. Just insert some simple PHP code into your theme files where you want it to show or just use a simple shortcode.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Gambling / Betting System', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Set up bets so your users can bet their points against each other. You can have up to 5 options per bet. Put it on any post or page with a simple shortcode! You can have up to 5 bets running at once too. Awards the points with a simple click when the bet is over.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Show Text If Giveaway Or Bet Is Open / Closed', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Using another simple shortcode you can display any basic html you want based on if any of your giveaways or bets are active or closed.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Administrative', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Do not want to give somebody full admin access to your WordPress site? But want them to help out with managing your giveaway or award bets? Enter their usernames in the settings and you are good to go! Here is what they can do:', 'cp_buddypress' ) ?></h3>
		<h3><?php _e( '<ul>
				<li>Manually add video or bonus entries</li>
				<li>Pick winners at random</li>
				<li>Remove all entries for a user or just a specific type of entry. Useful when you have people that try to cheat the point system.</li>
				<li>Retrieve user entry details so you can see how many entries they have & their chance of winning.</li>
				<li>Retrieve all giveaways entry details.</li>
				<li>Award Bets</li>
				</ul>', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Built In Countdown', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Seeing a the seconds, minutes, hours & days tick away with give your users urgency to participate in your giveaway or bet. It will automatically close the giveaway or bet for you, so less for you to worry about.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'BuddyPress', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( '<img src="'.CP_BUDDYPRESS_PATH.'includes/css/cblottery-notify.png" align="right" style="margin:-10px 15px 15px 15px;" />Since your using my CubePoints Buddypress Integration already, then it will tie right into this system! It displays the number of lotteries or bets they have not entered, in the top BuddyPress admin bar.', 'cp_buddypress' ) ?></h3>
		<h3><?php _e( 'In this example there are 2 lotteries & 2 bets available. This user has entered 1 lottery & 1 bet. Leaving 2 that they need to complete. The ones they need to enter are always at the top of the list. You can change the text and link to whatever you want in the admin area.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Can I Try It Out?', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Sure! Here is a <a href="http://store.slyspyder.com/giveaway-demo/?utm_source=cb_buddypress_plugin&utm_medium=admin_page&utm_campaign=cb_buddypress_free_plugin">demo</a>, have at it.', 'cp_buddypress' ) ?></h3>
		
		<h2><?php _e( 'Where Can I Get It?', 'cp_buddypress' ) ?></h2>
		<h3><?php _e( 'Go <a href="http://store.slyspyder.com/?utm_source=cb_buddypress_plugin&utm_medium=admin_page&utm_campaign=cb_buddypress_free_plugin">here</a> for full details.', 'cp_buddypress' ) ?></h3>

		</td>
	  </tr>
	  </table>
		
	<?php 
	} ?>
		
</div>
	
	<p class="submit">
	<a href="http://www.SlySpyder.com"><img src="<?php echo WP_PLUGIN_URL; ?>/cubepoints-buddypress-integration/createdby.png" alt="Created by SlySpyder" title="Reviews for Everything | SlySpyder.com" align="right" /></a>
      <input class="button-primary" type="submit" name="cubepoints-bp-admin-submit" id="cubepoints-bp-admin-submit" value="<?php _e('Save Changes', 'cp_buddypress'); ?>"/>
    </p>
    <?php wp_nonce_field( 'cubepoints-bp-admin' ) ?>
    </form>
    
    <div id="codes" style="position:fixed; top:90px; width: 750px; height: 530px; background:#262626; color:#ffffff; border:double 1px #666666; display:none;">
    <a name="codes"></a><center>
    <h3><?php _e('Copy the code below into your themes functions.php file.<br />Then use the shortcode [earnpoints] in posts or pages.','cp_buddypress'); ?></h3>

    <textarea rows="21" cols="60">
    	// **Begin** Enable CubePoints BuddyPress Shortcode [earnpoints]	
	function cbbp_earnpoints () {
	
					output .= '<p><strong>';
					output .= get_option('bp_earnpointstitle_cp_bp');
					output .= '</strong></p>';
		
					output .= '<strong>'; 
					_e('Community','cp_buddypress');
					output .= '</strong><br /><br />';
					
					if (get_option('bp_update_post_add_cp_bp') > 0) {
					output .= get_option('bp_update_post_add_cp_bp');
					_e(' Points - Update','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('bp_update_comment_add_cp_bp') > 0) {
					output .= get_option('bp_update_comment_add_cp_bp');
					_e(' Points - Leaving a reply','cp_buddypress');
					output .= '<br />';
					}					
					
					if (get_option('bp_create_group_add_cp_bp') > 0) {
					output .= get_option('bp_create_group_add_cp_bp');
					_e(' Points - Creating a group','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('bp_group_avatar_add_cp_bp') > 0) {
					output .= get_option('bp_group_avatar_add_cp_bp');
					_e(' Points - Uploading a group avatar','cp_buddypress');
					output .= '<br />';
					}					
					
					if (get_option('bp_join_group_add_cp_bp') > 0) {
					output .= get_option('bp_join_group_add_cp_bp');
					_e(' Points - Joining a group','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_leave_group_add_cp_bp') > 0) {
					output .= get_option('bp_leave_group_add_cp_bp');
					_e(' Points - Leaving a group','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_update_group_add_cp_bp') > 0) {
					output .= get_option('bp_update_group_add_cp_bp');
					_e(' Points - Group Update or Reply','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_friend_add_cp_bp') > 0) {
					output .= get_option('bp_friend_add_cp_bp');
					_e(' Points - Completed Friend Request','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_forum_new_topic_add_cp_bp') > 0) {
					output .= get_option('bp_forum_new_topic_add_cp_bp');
					_e(' Points - New Group Forum Topic','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_forum_new_post_add_cp_bp') > 0) {
					output .= get_option('bp_forum_new_post_add_cp_bp');
					_e(' Points - New Group Forum Post','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_avatar_add_cp_bp') > 0) {
					output .= get_option('bp_avatar_add_cp_bp');
					_e(' Points - Avatar Uploaded','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_pm_cp_bp') > 0) {
					output .= get_option('bp_pm_cp_bp');
					_e(' Points - Message Sent','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('bp_bplink_add_cp_bp') > 0) {
					output .= get_option('bp_bplink_add_cp_bp');
					_e(' Points - Link Created','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('bp_bplink_vote_add_cp_bp') > 0) {
					output .= get_option('bp_bplink_vote_add_cp_bp');
					_e(' Points - Link Voted','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('bp_bplink_comment_add_cp_bp') > 0) {
					output .= get_option('bp_bplink_comment_add_cp_bp');
					_e(' Points - Link Comment','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('bp_gift_given_cp_bp') > 0) {
					output .= get_option('bp_gift_given_cp_bp');
					_e(' Points - Gift Given','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('bp_gallery_upload_cp_bp') > 0) {
					output .= get_option('bp_gallery_upload_cp_bp');
					_e(' Points - Gallery Upload','cp_buddypress');
					output .= '<br />';
					}				
					
					output .= '<br /><strong>';
					_e('Blog Activity','cp_buddypress');
					output .= '</strong><br /><br />';
				
					if (get_option('cp_comment_points') > 0) {
					output .= get_option('cp_comment_points');
					_e(' Points - Blog Comment','cp_buddypress');
					output .= '<br />';
					}

					if (get_option('cp_post_points') > 0) {
					output .= get_option('cp_post_points');
					_e(' Points - Blog Post','cp_buddypress');
					output .= '<br />';
					}
					
					output .= '<br /><strong>'; 
					_e('Misc','cp_buddypress');
					output .= '</strong><br /><br />';

					if (get_option('cp_reg_points') > 0) {
					output .= get_option('cp_reg_points');
					_e(' Points - Becoming a Member','cp_buddypress');
					output .= '<br />';
					}
					
					if (get_option('cp_daily_points') > 0) {
					output .= get_option('cp_daily_points');
					_e(' Points - Daily Login','cp_buddypress');
					output .= '<br />';
					}
					output .= get_option('bp_earnpoints_extra_cp_bp');
	return $output;			
	}				
	add_shortcode('earnpoints', 'cbbp_earnpoints');
	// **END** Enable CubePoints BuddyPress Shortcode [earnpoints]</textarea>
	<br /><br /><button onclick="document.getElementById('codes').style.display='none';"><?php _e('Close','cp_buddypress'); ?></button></center>
    </div>

<hr /><h3><?php _e('Other Plugin News and Info','cp_buddypress'); ?></h3>
<p><strong><?php _e('Learn more about the "CubePoints Giveaway & Betting System" and how to get it <a href="http://store.slyspyder.com/">here</a>.','cp_buddypress'); ?></strong><br />
<p><strong><?php _e('How to Integrate CubePoints into the following:','cp_buddypress'); ?></strong>
<a href="http://blog.slyspyder.com/2010/04/25/integrate-cubepoints-into-simplepress-forum-wordpress/"><?php _e('Simple:Press Forum','cp_buddypress'); ?></a> | 
<a href="http://blog.slyspyder.com/2010/05/22/cubepoints-support-for-the-calendar-plugin-by-kieran-oshea/"><?php _e('Calendar Plugin [Kieran O Shea]','cp_buddypress'); ?></a> | 
<a href="http://blog.slyspyder.com/2010/05/06/cubepoints-and-wp-polls-wordpress-plugin/"><?php _e('WP-Polls','cp_buddypress'); ?></a>
</p>
<p><a href="http://blog.slyspyder.com/?p=601"><?php _e('Adding Support For Other BuddyPress Plugins To The CubePoints Buddypress Integration Plugin','cp_buddypress'); ?></a></p>
<p><a href="http://blog.slyspyder.com/2010/08/15/integrate-simplepress-forum-into-the-buddypress-activity-stream/"><?php _e('Integrate Simple:Press Forum into the BuddyPress Activity Stream!','cp_buddypress'); ?></a></p>
<p><?php _e('Join the','cp_buddypress'); ?> <a href="http://buddypress.org/community/groups/cubepoints-buddypress-integration/"><?php _e('CubePoints Buddypress Integration','cp_buddypress'); ?></a> <?php _e('group at BuddyPress.org!','cp_buddypress'); ?></p>
<p><a href="http://cubepoints.com/forums/"><?php _e('Official Cubepoints Support Forum','cp_buddypress'); ?></a>
<p><strong><?php _e('If this plugin works for you please','cp_buddypress'); ?> <a href="http://wordpress.org/extend/plugins/cubepoints-buddypress-integration/"><?php _e('rate it and vote','cp_buddypress'); ?></a> <?php _e('that it works. If not please let me know so I can fix it.','cp_buddypress'); ?></strong></p>

</div>
<?php
}

/**
 * cubepoints_bp_verify_nonce()
 *
 * When the settings form is submitted, verifies the nonce to ensure security.
 *
 * @version 1.9.8.2
 * @since 1.0
 */
function cubepoints_bp_verify_nonce() {
    if( isset($_POST['_wpnonce']) && isset($_POST['cp_bp_admin_form_submit']) ) {
	$nonce = $_REQUEST['_wpnonce'];
	
    if ( !wp_verify_nonce($nonce, 'cubepoints-bp-admin') )
	wp_die( __('You do not have permission to do that.') );
    
    }
}
add_action('init', 'cubepoints_bp_verify_nonce');
?>