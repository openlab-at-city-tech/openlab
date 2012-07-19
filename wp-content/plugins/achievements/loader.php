<?php
/**
 * Main loader file; ensures that BuddyPress is loaded first.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements for BuddyPress
 * @subpackage loader
 *
 * $Id: loader.php 1026 2012-04-01 12:23:14Z DJPaul $
 */

/*
Plugin Name: Achievements
Plugin URI: http://achievementsapp.com/
Description: Achievements gives your BuddyPress community fresh impetus by promoting and rewarding social interaction with challenges, badges and points.
Version: 2.3
Requires at least: WP 3.0.1, BuddyPress 1.5
Tested up to: WP 3.3.1, BuddyPress 1.6
License: General Public License version 3
Author: Paul Gibbs
Author URI: http://byotos.com/
Network: true
Domain Path: /includes/languages/
Text Domain: dpa

"Achievements for BuddyPress"
Copyright (C) 2009-12 Paul Gibbs

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 3 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/.
*/

define ( 'ACHIEVEMENTS_DB_VERSION', 27 );

// You can override these in wp-config.php.
if ( !defined( 'DPA_SLUG' ) )
	define ( 'DPA_SLUG', __( 'achievements', 'dpa' ) );

if ( !defined( 'DPA_SLUG_CREATE' ) )
	define ( 'DPA_SLUG_CREATE', __( 'create', 'dpa' ) );

if ( !defined( 'DPA_SLUG_MY_ACHIEVEMENTS' ) )
	define ( 'DPA_SLUG_MY_ACHIEVEMENTS', __( 'unlocked', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_EDIT' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_EDIT', __( 'edit', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_DELETE' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_DELETE', __( 'delete', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_GRANT' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_GRANT', __( 'give', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE', __( 'change-picture', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_UNLOCKED_BY' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_UNLOCKED_BY', __( 'unlocked-by', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_ACTIVITY' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_ACTIVITY', __( 'home', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ACHIEVEMENT_ACTIVITY_RSS' ) )
	define ( 'DPA_SLUG_ACHIEVEMENT_ACTIVITY_RSS', __( 'feed', 'dpa' ) );

if ( !defined( 'DPA_SLUG_ADMIN_SUPPORT' ) )
	define ( 'DPA_SLUG_ADMIN_SUPPORT', __( 'support', 'dpa' ) );

/**
 * Only load the component if BuddyPress is loaded and initialised. 
 *
 * @since 2.0
 */
function dpa_init() {
	dpa_install_and_upgrade();
	require( dirname( __FILE__ ) . '/includes/achievements-core.php' );

	do_action( 'dpa_init' );
}
add_action( 'bp_include', 'dpa_init' );

/**
 * Manages plugin install and upgrade.
 *
 * @since 2.0.2
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 */
function dpa_install_and_upgrade() {
	global $bp, $wpdb;

	$version = get_site_option( 'achievements-db-version' );
	if ( false !== $version && ACHIEVEMENTS_DB_VERSION == $version )
		return;

	if ( !$version )
		$version = 0;

	$charset_collate = ( !empty( $wpdb->charset ) ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
	$table_prefix = bp_core_get_table_prefix();

	if ( $version != ACHIEVEMENTS_DB_VERSION ) {
		for ( $i=$version; $i<ACHIEVEMENTS_DB_VERSION; $i++ ) {

			switch ( $i ) {
				case 0:
					$sql = array();
					$sql[] = "CREATE TABLE {$table_prefix}achievements (
									id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
									action_id bigint(20) NOT NULL,
									picture_id bigint(20) NOT NULL,
									action_count SMALLINT NOT NULL,
									name VARCHAR(200) NOT NULL,
									description text NOT NULL,
									points int NOT NULL,
									is_active int(1) NOT NULL,
									slug VARCHAR(200) NOT NULL,
									KEY action_id_is_active (action_id,is_active),
									KEY slug (slug(20))
								 ) {$charset_collate};";
					$sql[] = "CREATE TABLE {$table_prefix}achievements_unlocked (
									id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
									achievement_id bigint(20) NOT NULL,
								  user_id bigint(20) NOT NULL,
									achieved_at DATETIME NOT NULL,
									KEY user_id (user_id),
									KEY achievement_id (achievement_id),
									KEY user_achievement_ids (user_id,achievement_id)
								 ) {$charset_collate};";
					$sql[] = "CREATE TABLE {$table_prefix}achievements_actions (
									id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
									category TINYTEXT NOT NULL,
									name text NOT NULL,
									description text NOT NULL
								 ) {$charset_collate};";

					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
					dbDelta( $sql );

					// Insert the default actions
					$actions = array();
					$actions[] = array( 'category' => 'blog', 'name' => 'comment_post', 'description' => __( "The user writes a comment on a post or page.", 'dpa' ) );
					$actions[] = array( 'category' => 'blog', 'name' => 'publish_post', 'description' => __( "The user publishes a post or page.", 'dpa' ) );
					$actions[] = array( 'category' => 'members', 'name' => 'friends_friendship_requested', 'description' => __( "The user sends a friendship request to someone.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_invite_user', 'description' => __( "The user invites someone to join a group.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_join_group', 'description' => __( "The user joins a group.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_promoted_member', 'description' => __( "The user promotes a group member to a moderator or administrator.", 'dpa' ) );
					$actions[] = array( 'category' => 'messaging', 'name' => 'messages_message_sent', 'description' => __( "The user sends or replies to a private message.", 'dpa' ) );
					$actions[] = array( 'category' => 'profile', 'name' => 'xprofile_updated_profile', 'description' => __( "The user updates their profile.", 'dpa' ) );
					$actions[] = array( 'category' => 'profile', 'name' => 'bp_core_activated_user', 'description' => __( "A new user activates their account.", 'dpa' ) );

					foreach ( $actions as $action )
						$wpdb->insert( "{$table_prefix}achievements_actions", $action );

					// Per-achievement settings: number of people who have unlocked it.
					update_site_option( 'achievements_meta', array() );  // 0 => array( 'no_of_unlocks' => 0 )
				break;

				case 1:
					$wpdb->query( $wpdb->prepare( "ALTER TABLE {$table_prefix}achievements ADD COLUMN site_id bigint(20) NOT NULL" ) );
					$wpdb->update( "{$table_prefix}achievements", array( 'site_id' => BP_ROOT_BLOG ), null, '%d' );
				break;

				case 2:
					$actions = array();
					$actions[] = array( 'category' => 'profile', 'name' => 'xprofile_avatar_uploaded', 'description' => __( "The user changes their profile's avatar.", 'dpa' ) );
					$actions[] = array( 'category' => 'members', 'name' => 'friends_friendship_accepted', 'description' => __( "The user accepts a friendship request from someone.", 'dpa' ) );
					$actions[] = array( 'category' => 'members', 'name' => 'friends_friendship_rejected', 'description' => __( "The user rejects a friendship request from someone.", 'dpa' ) );
					$actions[] = array( 'category' => 'blog', 'name' => 'trashed_post', 'description' => __( "The user trashes a post or page.", 'dpa' ) );
					$actions[] = array( 'category' => 'messaging', 'name' => 'messages_delete_thread', 'description' => __( "The user deletes a private message.", 'dpa' ) );
					$actions[] = array( 'category' => 'members', 'name' => 'friends_friendship_deleted', 'description' => __( "The user cancels a friendship.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_created_group', 'description' => __( "The user creates a group.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_leave_group', 'description' => __( "The user leaves a group.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_delete_group', 'description' => __( "The user deletes a group.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_new_forum_topic', 'description' => __( "The user creates a new group forum topic.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_new_forum_topic_post', 'description' => __( "The user replies to a group forum topic.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_delete_group_forum_post', 'description' => __( "The user deletes a group forum post.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_delete_group_forum_topic', 'description' => __( "The user deletes a group forum topic.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_update_group_forum_post', 'description' => __( "The user modifies a group forum post.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'groups_update_group_forum_topic', 'description' => __( "The user modifies a group forum topic.", 'dpa' ) );
					$actions[] = array( 'category' => 'groups', 'name' => 'bp_groups_posted_update', 'description' => __( "The user writes a message in a group's activity stream.", 'dpa' ) );
					$actions[] = array( 'category' => 'profile', 'name' => 'bp_activity_posted_update', 'description' => __( "The user writes a message in their activity stream.", 'dpa' ) );

					foreach ( $actions as $action ) {
						$wpdb->insert( "{$table_prefix}achievements_actions", $action );
					}
				break;

				case 3:
					$actions = array();
					$actions[] = array( 'category' => 'members', 'name' => 'bp_activity_comment_posted', 'description' => __( "The user replies to any item in any activity stream.", 'dpa' ) );
					$actions[] = array( 'category' => 'blog', 'name' => 'signup_finished', 'description' => __( "The user creates a new site.", 'dpa' ) );

					foreach ( $actions as $action ) {
						$wpdb->insert( "{$table_prefix}achievements_actions", $action );
					}
				break;

				case 4:
					$wpdb->query( $wpdb->prepare( "CREATE INDEX name ON {$table_prefix}achievements (name(20))" ) );
					$wpdb->query( $wpdb->prepare( "CREATE INDEX action_id ON {$table_prefix}achievements (action_id)" ) );
					$wpdb->query( $wpdb->prepare( "CREATE INDEX description ON {$table_prefix}achievements (description(20))" ) );
				break;

				case 5:
					$wpdb->query( $wpdb->prepare( "ALTER TABLE {$table_prefix}achievements ADD COLUMN group_id bigint(20) NOT NULL" ) );
					$wpdb->update( "{$table_prefix}achievements", array( 'group_id' => -1 ), null, '%d' );
					$wpdb->query( $wpdb->prepare( "CREATE INDEX group_id ON {$table_prefix}achievements (group_id)" ) );
				break;

				case 6:
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'name' => 'bp_core_activated_user' ), array( 'name' => 'bp_core_account_activated' ), '%s' );
				break;

				case 7:
					$actions = array();
					$actions[] = array( 'category' => 'achievements', 'name' => 'dpa_points_incremented', 'description' => __( "The user is awarded points by unlocking an Achievement.", 'dpa' ) );
					$actions[] = array( 'category' => 'achievements', 'name' => 'dpa_achievement_unlocked', 'description' => __( "The user unlocks an Achievement.", 'dpa' ) );

					foreach ( $actions as $action ) {
						$wpdb->insert( "{$table_prefix}achievements_actions", $action );
					}
				break;

				case 8:
					$actions = array();
					$actions[] = array( 'category' => 'eventpress', 'name' => 'publish_ep_event', 'description' => __( "The user publishes an event.", 'dpa' ) );
					$actions[] = array( 'category' => 'eventpress', 'name' => 'reg_approved_ep_reg', 'description' => __( "The user registers to attend an event.", 'dpa' ) );

					foreach ( $actions as $action ) {
						$wpdb->insert( "{$table_prefix}achievements_actions", $action );
					}
				break;

				case 9:
					$wpdb->query( $wpdb->prepare( "ALTER TABLE {$table_prefix}achievements_actions ADD COLUMN is_group_action int(1) NOT NULL" ) );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'is_group_action' => 0 ), null, '%d' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'is_group_action' => 1 ), array( 'category' => 'groups' ), '%d' );
				break;

				case 10:
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'name' => 'groups_create_group' ), array( 'name' => 'groups_created_group' ), '%s' );
				break;

				case 11:
					$actions = array();
					$actions[] = array( 'category' => 'bpmoderation', 'name' => 'bp_moderation_content_status_changed', 'description' => __( "When the reported content is moderated by an administrator", 'dpa' ), 'is_group_action' => 0 );

					foreach ( $actions as $action ) {
						$wpdb->insert( "{$table_prefix}achievements_actions", $action );
					}
				break;

				case 12:
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'blog' ), array( 'name' => 'bp_core_activated_user' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'forum' ), array( 'name' => 'groups_new_forum_topic' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'forum' ), array( 'name' => 'groups_new_forum_topic_post' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'forum' ), array( 'name' => 'groups_delete_group_forum_post' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'forum' ), array( 'name' => 'groups_delete_group_forum_topic' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'forum' ), array( 'name' => 'groups_update_group_forum_post' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'forum' ), array( 'name' => 'groups_update_group_forum_topic' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'activitystream' ), array( 'name' => 'bp_groups_posted_update' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'activitystream' ), array( 'name' => 'bp_activity_posted_update' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'activitystream' ), array( 'name' => 'bp_activity_comment_posted' ), '%s' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'category' => 'multisite' ), array( 'name' => 'signup_finished' ), '%s' );
				break;

				case 13:
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'is_group_action' => 0 ), array( 'name' => 'groups_create_group' ), '%d' );
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'is_group_action' => 0 ), array( 'name' => 'groups_delete_group' ), '%d' );
				break;

				case 14:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'bpprivacy', 'name' => 'bp_privacy_update_privacy_settings', 'description' => __( "The user updates their privacy settings.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 15:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'activitystream', 'name' => 'bp_activity_add_user_favorite', 'description' => __( "The user marks any item in any activity stream as a favourite.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'activitystream', 'name' => 'bp_activity_remove_user_favorite', 'description' => __( "The user removes a favourited item from any activity stream.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 16:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'bpmoderation', 'name' => 'bp_moderation_content_flagged', 'description' => __( "The user flags any content as inappropiate.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'bpmoderation', 'name' => 'bp_moderation_content_unflagged', 'description' => __( "The user unflags content that was previously marked as inappropiate.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 17:
					$wpdb->update( "{$table_prefix}achievements_actions", array( 'name' => 'draft_to_publish' ), array( 'name' => 'publish_post' ), '%s' );
				break;

				case 18:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'groups', 'name' => 'groups_demoted_member', 'description' => __( "The user demotes a group member from a moderator or administrator.", 'dpa' ), 'is_group_action' => 1 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'groups', 'name' => 'groups_banned_member', 'description' => __( "The user bans a member from a group.", 'dpa' ), 'is_group_action' => 1 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'groups', 'name' => 'groups_unbanned_member', 'description' => __( "The user unbans a member from a group.", 'dpa' ), 'is_group_action' => 1 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'groups', 'name' => 'groups_premote_member', 'description' => __( "The user receives a promotion to group moderator or administrator.", 'dpa' ), 'is_group_action' => 1 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'groups', 'name' => 'groups_demote_member', 'description' => __( "The user receives a demotion from group moderator or administrator.", 'dpa' ), 'is_group_action' => 1 ) );
				break;

				case 19:
					$wpdb->query( $wpdb->prepare( "CREATE INDEX is_active ON {$table_prefix}achievements (is_active)" ) );
				break;

				case 20:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddystream', 'name' => 'buddystream_twitter_activated', 'description' => __( "The user succesfully authorises and connects to their Twitter account.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddystream', 'name' => 'buddystream_facebook_activated', 'description' => __( "The user succesfully authorises and connects to their Facebook account.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 21:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddystream', 'name' => 'buddystream_lastfm_activated', 'description' => __( "The user connects to their Last.fm account.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddystream', 'name' => 'buddystream_youtube_activated', 'description' => __( "The user connects to their YouTube account.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddystream', 'name' => 'buddystream_flickr_activated', 'description' => __( "The user connects to their Flickr account.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 22:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'inviteanyone', 'name' => 'accepted_email_invite', 'description' => __( "A new user activates their account.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'inviteanyone', 'name' => 'sent_email_invite', 'description' => __( "The user invites someone to join the site.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 23:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddypresslinks', 'name' => 'bp_links_cast_vote_success', 'description' => __( "The user votes on a link.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddypresslinks', 'name' => 'bp_links_delete_link', 'description' => __( "The user deletes a link.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddypresslinks', 'name' => 'bp_links_posted_update', 'description' => __( "The user writes a comment on a link.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 24:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'buddypresslinks', 'name' => 'bp_links_create_complete', 'description' => __( "The user creates a link.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 25:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'jes', 'name' => 'events_event_create_complete', 'description' => __( "The user creates an event.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'jes', 'name' => 'events_join_event', 'description' => __( "The user registers to attend an event.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'jes', 'name' => 'events_leave_event', 'description' => __( "The user cancels their registration to an event.", 'dpa' ), 'is_group_action' => 0 ) );
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'jes', 'name' => 'events_event_deleted', 'description' => __( "The user deletes an event.", 'dpa' ), 'is_group_action' => 0 ) );
				break;

				case 26:
					$wpdb->insert( "{$table_prefix}achievements_actions", array( 'category' => 'blog', 'name' => 'wp_login', 'description' => __( "The user logs in to the site.", 'dpa' ), 'is_group_action' => 0 ) );
				break;
			}
		}

		update_site_option( 'achievements-db-version', ACHIEVEMENTS_DB_VERSION );
	}
}
?>