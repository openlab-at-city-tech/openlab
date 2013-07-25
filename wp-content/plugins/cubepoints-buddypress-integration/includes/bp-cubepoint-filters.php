<?php
/**
 * BUDDYPRESS CUBEPOINTS FILTERS
 *
 * @version 0.1.9.8
 * @since 1.0
 * @package BuddyPress CubePoints
 * @subpackage Main
 * @license GPL v2.0
 * @link http://wordpress.org/extend/plugins/cubepoints-buddypress-integration/
 *
 * ========================================================================================================
 */

// bbPress 2.0 New Topic
// ==============================================================
add_action('bbp_new_topic','bp_cp_bbpress2_new_topic_add_cppoints');
add_action('cp_logs_description','bp_cp_bbpress2_new_topic_log', 10, 4);
function bp_cp_bbpress2_new_topic_log($type,$uid,$points,$data){
 if($type!='bp_cp_bbpress2_new_topic') { return; }
 _e('New Forum Topic','cp_buddypress');
}

// bbPress 2.0 New Reply
// ==============================================================
add_action('bbp_new_reply','bp_cp_bbpress2_new_reply_add_cppoints');
add_action('cp_logs_description','bp_cp_bbpress2_new_reply_log', 10, 4);
function bp_cp_bbpress2_new_reply_log($type,$uid,$points,$data){
 if($type!='bp_cp_bbpress2_new_reply') { return; }
 _e('New Forum Reply','cp_buddypress');
}

// Hide updates & replies for member under certain point value
// ==============================================================
add_action ('bp_before_activity_post_form','my_bp_hide_updates_cb');
add_action ('bp_before_activity_entry_comments','my_bp_hide_updates_cb');
add_action ('bp_activity_entry_content','my_bp_hide_updates_cb');
 
// Hide compose sent to box for member under certain point value
// ==============================================================
add_action ('bp_before_messages_compose_content','my_bp_hide_compose_message');

// Hide send message to member under certain point value
// =======================================================
add_action ('bp_before_member_header_meta','my_bp_hide_send_message');

// Hide create a group under certain point value
// =======================================================
add_action ('groups_custom_group_fields_editable','my_bp_hide_group_create_button');

// Show Points in BP Admin Bar
// =======================================================
add_action ('bp_adminbar_menus','my_bp_admin_bar_points');

// Add Points for creating a group
// =======================================================
add_action('groups_group_create_complete','my_bp_create_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_create_log', 10, 4);
function cp_bp_group_create_log($type,$uid,$points,$data){
if($type!='cp_bp_group_create') { return; }
_e('Group Creation','cp_buddypress');
}

// Remove points for deleting a group
// =======================================================
add_action('groups_group_deleted','my_bp_delete_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_delete_log', 10, 4);
function cp_bp_group_delete_log($type,$uid,$points,$data){
if($type!='cp_bp_group_delete') { return; }
_e('Group Deleted','cp_buddypress');
}

// Add Points for a update
// =======================================================
add_action('bp_activity_posted_update','my_bp_update_post_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_update_log', 10, 4);
function cp_bp_update_log($type,$uid,$points,$data){
if($type!='cp_bp_update') { return; }
_e('Update','cp_buddypress');
}

// Points for Joining a group
// =======================================================
add_action('groups_join_group','my_bp_join_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_joined_log', 10, 4);
function cp_bp_group_joined_log($type,$uid,$points,$data){
if($type!='cp_bp_group_joined') { return; }
_e('Group Joined','cp_buddypress');
}

// Points for Leaving a group
// =======================================================
add_action('groups_leave_group','my_bp_leave_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_left_log', 10, 4);
function cp_bp_group_left_log($type,$uid,$points,$data){
if($type!='cp_bp_group_left') { return; }
_e('Left Group','cp_buddypress');
}

// Add Points for a comment or reply
// =======================================================
add_action('bp_activity_comment_posted','my_bp_update_comment_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_reply_log', 10, 4);
function cp_bp_reply_log($type,$uid,$points,$data){
if($type!='cp_bp_reply') { return; }
_e('Reply','cp_buddypress');
}

// Add Points for a GROUP comment or reply
// =======================================================
add_action('bp_groups_posted_update','my_bp_update_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_reply_log', 10, 4);
function cp_bp_group_reply_log($type,$uid,$points,$data){
if($type!='cp_bp_group_reply') { return; }
_e('Reply','cp_buddypress');
}

// Remove points for comment deletion
// =======================================================
add_action('bp_activity_action_delete_activity','my_bp_delete_comment_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_update_removed_log', 10, 4);
function cp_bp_update_removed_log($type,$uid,$points,$data){
if($type!='cp_bp_update_removed') { return; }
_e('Update Deleted','cp_buddypress');
}

// Add Points for a completed Friend Request
// =======================================================
add_action('friends_friendship_accepted','my_bp_friend_add_cppoints', 10, 3);
// Log
add_action('cp_logs_description','cp_bp_new_friend_log', 10, 4);
function cp_bp_new_friend_log($type,$uid,$points,$data){
if($type!='cp_bp_new_friend') { return; }
_e('Friend Added','cp_buddypress');
}

// Remove points for Canceled Friendship
// =======================================================
add_action('friends_friendship_deleted','my_bp_friend_delete_add_cppoints', 10, 3);
// Log
add_action('cp_logs_description','cp_bp_lost_friend_log', 10, 4);
function cp_bp_lost_friend_log($type,$uid,$points,$data){
if($type!='cp_bp_lost_friend') { return; }
_e('Friendship Canceled','cp_buddypress');
}

//  Add Points New Group Forum Topic (See FAQ in readme.txt for more info)
// =======================================================
add_action('bp_forums_new_topic','my_bp_forum_new_topic_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_new_group_forum_topic_log', 10, 4);
function cp_bp_new_group_forum_topic_log($type,$uid,$points,$data){
if($type!='cp_bp_new_group_forum_topic') { return; }
_e('New Group Forum Topic','cp_buddypress');
}

// Add Points New Group Forum Post
// =======================================================
add_action('bp_forums_new_post','my_bp_forum_new_post_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_new_group_forum_post_log', 10, 4);
function cp_bp_new_group_forum_post_log($type,$uid,$points,$data){
if($type!='cp_bp_new_group_forum_post') { return; }
_e('Group Forum Post','cp_buddypress');
}

// POINTS FIX for New Forum Topic Edit
// =======================================================
add_action('groups_edit_forum_topic','my_bp_forum_edit_topic_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_new_group_forum_post_edit_log', 10, 4);
function cp_bp_new_group_forum_post_edit_log($type,$uid,$points,$data){
if($type!='cp_bp_new_group_forum_post_edit') { return; }
_e('Group Forum Post Edit','cp_buddypress');
}

// POINTS FIX for Forum Post Edit
// =======================================================
add_action('groups_edit_forum_post','my_bp_forum_edit_post_add_cppoints');
// Log Above log covers this as well.

// Add Points Avatar Upload
// =======================================================
add_action('xprofile_avatar_uploaded','my_bp_avatar_add_cppoints');
// Log WORKS BUT CHECKING OR MORE THAN ONCE DOESN'T WORK
add_action('cp_logs_description','cp_bp_avatar_uploaded_log', 10, 4);
function cp_bp_avatar_uploaded_log($type,$uid,$points,$data){
if($type!='cp_bp_avatar_uploaded') { return; }
_e('Avatar Uploaded','cp_buddypress');
}

// Add Points Group Avatar Upload
// =======================================================
add_action('groups_screen_group_admin_avatar','my_bp_group_avatar_add_cppoints');
// Log WORKS BUT CHECKING OR MORE THAN ONCE DOESN'T WORK
add_action('cp_logs_description','cp_bp_group_avatar_uploaded_log', 10, 4);
function cp_bp_group_avatar_uploaded_log($type,$uid,$points,$data){
if($type!='cp_bp_group_avatar_uploaded') { return; }
_e('Group Avatar Uploaded','cp_buddypress');
}

// Add Point Message Sent
// =======================================================
add_action('messages_message_sent','my_bp_pm_cppoints');
// Log
add_action('cp_logs_description','cp_bp_message_sent_log', 10, 4);
function cp_bp_message_sent_log($type,$uid,$points,$data){
if($type!='cp_bp_message_sent') { return; }
_e('Message Sent','cp_buddypress');
}

// Add Points for BuddyPress Link Creation
// =======================================================
add_action('bp_links_create_complete','my_bp_bplink_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_added_log', 10, 4);
function cp_bp_link_added_log($type,$uid,$points,$data){
if($type!='cp_bp_link_added') { return; }
_e('Link Added','cp_buddypress');
}

// Add Points for BuddyPress Link Vote
// =======================================================
add_action('bp_links_cast_vote_success','my_bp_bplinkvote_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_voted_log', 10, 4);
function cp_bp_link_voted_log($type,$uid,$points,$data){
if($type!='cp_bp_link_voted') { return; }
_e('Link Voted','cp_buddypress');
}

// Add Points for BuddyPress Link Comment/Update
// =======================================================
add_action('bp_links_posted_update','my_bp_bplinkcomment_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_comment_log', 10, 4);
function cp_bp_link_comment_log($type,$uid,$points,$data){
if($type!='cp_bp_link_comment') { return; }
_e('Link Comment','cp_buddypress');
}

// Add Points for BuddyPress Link Delete
// =======================================================
add_action('bp_links_delete_link','my_bp_bplink_delete_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_delete_log', 10, 4);
function cp_bp_link_delete_log($type,$uid,$points,$data){
if($type!='cp_bp_link_delete') { return; }
_e('Link Deleted','cp_buddypress');
}

// Add Points for BuddyPress Gifts
// =======================================================
add_action('bp_gifts_send_gifts','my_bp_gift_given_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_gift_given_log', 10, 4);
function cp_bp_gift_given_log($type,$uid,$points,$data){
if($type!='cp_bp_gift_given') { return; }
_e('Gave a Gift','cp_buddypress');
}

// Add Points for BP Gallery Upload
// =======================================================
add_action('gallery_media_upload_complete','my_bp_gallery_upload_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_galery_upload_log', 10, 4);
function cp_bp_galery_upload_log($type,$uid,$points,$data){
if($type!='cp_bp_galery_upload') { return; }
_e('Gallery Upload','cp_buddypress');
}

// Add Points for BP Gallery Delete
// =======================================================
add_action('gallery_media_after_delete','my_bp_gallery_delete_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_galery_delete_log', 10, 4);
function cp_bp_galery_delete_log($type,$uid,$points,$data){
if($type!='cp_bp_galery_delete') { return; }
_e('Gallery Delete','cp_buddypress');
}

/* Adds CubePoints to Profile Page*/
add_action( 'bp_before_member_header_meta', 'cubepoints_bp_profile' );

?>