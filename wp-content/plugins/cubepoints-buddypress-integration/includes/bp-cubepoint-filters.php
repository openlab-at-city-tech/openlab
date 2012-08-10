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
 echo 'New Forum Topic';
}

// bbPress 2.0 New Reply
// ==============================================================
add_action('bbp_new_reply','bp_cp_bbpress2_new_reply_add_cppoints');
add_action('cp_logs_description','bp_cp_bbpress2_new_reply_log', 10, 4);
function bp_cp_bbpress2_new_reply_log($type,$uid,$points,$data){
 if($type!='bp_cp_bbpress2_new_reply') { return; }
 echo 'New Forum Reply';
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
echo 'Group Creation';
}

// Remove points for deleting a group
// =======================================================
add_action('groups_group_deleted','my_bp_delete_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_delete_log', 10, 4);
function cp_bp_group_delete_log($type,$uid,$points,$data){
if($type!='cp_bp_group_delete') { return; }
echo 'Group Deleted';
}

// Add Points for a update
// =======================================================
add_action('bp_activity_posted_update','my_bp_update_post_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_update_log', 10, 4);
function cp_bp_update_log($type,$uid,$points,$data){
if($type!='cp_bp_update') { return; }
echo 'Update';
}

// Points for Joining a group
// =======================================================
add_action('groups_join_group','my_bp_join_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_joined_log', 10, 4);
function cp_bp_group_joined_log($type,$uid,$points,$data){
if($type!='cp_bp_group_joined') { return; }
echo 'Group Joined';
}

// Points for Leaving a group
// =======================================================
add_action('groups_leave_group','my_bp_leave_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_left_log', 10, 4);
function cp_bp_group_left_log($type,$uid,$points,$data){
if($type!='cp_bp_group_left') { return; }
echo 'Left Group';
}

// Add Points for a comment or reply
// =======================================================
add_action('bp_activity_comment_posted','my_bp_update_comment_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_reply_log', 10, 4);
function cp_bp_reply_log($type,$uid,$points,$data){
if($type!='cp_bp_reply') { return; }
echo 'Reply';
}

// Add Points for a GROUP comment or reply
// =======================================================
add_action('bp_groups_posted_update','my_bp_update_group_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_group_reply_log', 10, 4);
function cp_bp_group_reply_log($type,$uid,$points,$data){
if($type!='cp_bp_group_reply') { return; }
echo 'Reply';
}

// Remove points for comment deletion
// =======================================================
add_action('bp_activity_action_delete_activity','my_bp_delete_comment_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_update_removed_log', 10, 4);
function cp_bp_update_removed_log($type,$uid,$points,$data){
if($type!='cp_bp_update_removed') { return; }
echo 'Update Deleted';
}

// Add Points for a completed Friend Request
// =======================================================
add_action('friends_friendship_accepted','my_bp_friend_add_cppoints', 10, 3);
// Log
add_action('cp_logs_description','cp_bp_new_friend_log', 10, 4);
function cp_bp_new_friend_log($type,$uid,$points,$data){
if($type!='cp_bp_new_friend') { return; }
echo 'Friend Added';
}

// Remove points for Canceled Friendship
// =======================================================
add_action('friends_friendship_deleted','my_bp_friend_delete_add_cppoints', 10, 3);
// Log
add_action('cp_logs_description','cp_bp_lost_friend_log', 10, 4);
function cp_bp_lost_friend_log($type,$uid,$points,$data){
if($type!='cp_bp_lost_friend') { return; }
echo 'Friendship Canceled';
}

//  Add Points New Group Forum Topic (See FAQ in readme.txt for more info)
// =======================================================
add_action('bp_forums_new_topic','my_bp_forum_new_topic_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_new_group_forum_topic_log', 10, 4);
function cp_bp_new_group_forum_topic_log($type,$uid,$points,$data){
if($type!='cp_bp_new_group_forum_topic') { return; }
echo 'New Group Forum Topic';
}

// Add Points New Group Forum Post
// =======================================================
add_action('bp_forums_new_post','my_bp_forum_new_post_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_new_group_forum_post_log', 10, 4);
function cp_bp_new_group_forum_post_log($type,$uid,$points,$data){
if($type!='cp_bp_new_group_forum_post') { return; }
echo 'Group Forum Post';
}

// POINTS FIX for New Forum Topic Edit
// =======================================================
add_action('groups_edit_forum_topic','my_bp_forum_edit_topic_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_new_group_forum_post_edit_log', 10, 4);
function cp_bp_new_group_forum_post_edit_log($type,$uid,$points,$data){
if($type!='cp_bp_new_group_forum_post_edit') { return; }
echo 'Group Forum Post Edit';
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
echo 'Avatar Uploaded';
}

// Add Points Group Avatar Upload
// =======================================================
add_action('groups_screen_group_admin_avatar','my_bp_group_avatar_add_cppoints');
// Log WORKS BUT CHECKING OR MORE THAN ONCE DOESN'T WORK
add_action('cp_logs_description','cp_bp_group_avatar_uploaded_log', 10, 4);
function cp_bp_group_avatar_uploaded_log($type,$uid,$points,$data){
if($type!='cp_bp_group_avatar_uploaded') { return; }
echo 'Group Avatar Uploaded';
}

// Add Point Message Sent
// =======================================================
add_action('messages_message_sent','my_bp_pm_cppoints');
// Log
add_action('cp_logs_description','cp_bp_message_sent_log', 10, 4);
function cp_bp_message_sent_log($type,$uid,$points,$data){
if($type!='cp_bp_message_sent') { return; }
echo 'Message Sent';
}

// Add Points for BuddyPress Link Creation
// =======================================================
add_action('bp_links_create_complete','my_bp_bplink_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_added_log', 10, 4);
function cp_bp_link_added_log($type,$uid,$points,$data){
if($type!='cp_bp_link_added') { return; }
echo 'Link Added';
}

// Add Points for BuddyPress Link Vote
// =======================================================
add_action('bp_links_cast_vote_success','my_bp_bplinkvote_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_voted_log', 10, 4);
function cp_bp_link_voted_log($type,$uid,$points,$data){
if($type!='cp_bp_link_voted') { return; }
echo 'Link Voted';
}

// Add Points for BuddyPress Link Comment/Update
// =======================================================
add_action('bp_links_posted_update','my_bp_bplinkcomment_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_comment_log', 10, 4);
function cp_bp_link_comment_log($type,$uid,$points,$data){
if($type!='cp_bp_link_comment') { return; }
echo 'Link Comment';
}

// Add Points for BuddyPress Link Delete
// =======================================================
add_action('bp_links_delete_link','my_bp_bplink_delete_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_link_delete_log', 10, 4);
function cp_bp_link_delete_log($type,$uid,$points,$data){
if($type!='cp_bp_link_delete') { return; }
echo 'Link Deleted';
}

// Add Points for BuddyPress Gifts
// =======================================================
add_action('bp_gifts_send_gifts','my_bp_gift_given_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_gift_given_log', 10, 4);
function cp_bp_gift_given_log($type,$uid,$points,$data){
if($type!='cp_bp_gift_given') { return; }
echo 'Gave a Gift';
}

// Add Points for BP Gallery Upload
// =======================================================
add_action('gallery_media_upload_complete','my_bp_gallery_upload_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_galery_upload_log', 10, 4);
function cp_bp_galery_upload_log($type,$uid,$points,$data){
if($type!='cp_bp_galery_upload') { return; }
echo 'Gallery Upload';
}

// Add Points for BP Gallery Delete
// =======================================================
add_action('gallery_media_after_delete','my_bp_gallery_delete_add_cppoints');
// Log
add_action('cp_logs_description','cp_bp_galery_delete_log', 10, 4);
function cp_bp_galery_delete_log($type,$uid,$points,$data){
if($type!='cp_bp_galery_delete') { return; }
echo 'Gallery Delete';
}

/* Adds CubePoints to Profile Page*/
add_action( 'bp_before_member_header_meta', 'cubepoints_bp_profile' );

?>