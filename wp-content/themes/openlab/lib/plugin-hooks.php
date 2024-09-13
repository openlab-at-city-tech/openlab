<?php
/**
 * Plugin hooks
 * Complete archive of plugin hooking for openlab theme, wds-citytech plugin, and mu-plugins
 * Includes actual hooks, related includes, and references for folder/file overwrites and hooks that need to stay elsewhere
 */
/**
 * Invite Anyone
 * See also: openlab/buddypress/members/single/invite-anyone.php for template overrides
 */
require_once STYLESHEETPATH . '/lib/plugin-mods/invite-funcs.php';

/**
 * Event Organiser
 * BuddyPress Event Organiser
 */
require_once STYLESHEETPATH . '/lib/plugin-mods/calendar-control.php';

/**
 * Contact Form 7
 */
require_once STYLESHEETPATH . '/lib/plugin-mods/contact-form-seven.php';
require_once STYLESHEETPATH . '/lib/plugin-mods/contact-form-seven-module.php';

/**
 * Plugin: Invite Anyone
 * Don't send friend requests when accepting Invite Anyone invitations
 *
 * @see #666
 */
add_filter( 'invite_anyone_send_friend_requests_on_acceptance', '__return_false' );

/**
 * Buddypress Group Documents
 * See also: mu-plugins/openlab-group-documents-privacy.php
 */
require_once STYLESHEETPATH . '/lib/plugin-mods/files-funcs.php';

/**
 * Plugin: BuddyPress Docs
 * See also: openlab/buddypress/groups/single/docs for template overrides
 */
require_once STYLESHEETPATH . '/lib/plugin-mods/docs-funcs.php';

/**
 * BuddyPress Group Email Subscription
 * See also: openlab/buddypress/groups/single/notifications.php for template overrides
 */
require_once STYLESHEETPATH . '/lib/plugin-mods/email-funcs.php';

/**
 * Plugin: BuddyPress Group Email Subscription
 * This function overwrites the email status output from the buddypress group email subscription plugin
 * Allows for layout control and Bootstrap injection
 * @global type $members_template
 * @global type $groups_template
 * @param type $user_id
 * @param type $group
 */
function openlab_manage_members_email_status( $user_id = '', $group = '' ) {
	global $members_template, $groups_template;

	// if group admins / mods cannot manage email subscription settings, stop now!
	if ( get_option( 'ass-admin-can-edit-email' ) === 'no' ) {
		return;
	}

	// no user ID? fallback on members loop user ID if it exists
	if ( ! $user_id ) {
		$user_id = ! empty( $members_template->member->user_id ) ? $members_template->member->user_id : false;
	}

	// no user ID? fallback on group loop if it exists
	if ( ! $group ) {
		$group = ! empty( $groups_template->group ) ? $groups_template->group : false;
	}

	// no user or group? stop now!
	if ( ! $user_id || ! is_object( $group ) ) {
		return;
	}

	$user_id = (int) $user_id;

	$group_url = bp_get_group_permalink( $group ) . 'admin/manage-members/email';
	$sub_type  = ass_get_group_subscription_status( $user_id, $group->id );
	echo '<h3>Email Status</h3>';

	echo '<ul class="group-manage-members-bpges-status">';
	echo '  <li><label><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'supersub', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/supersub/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="supersub" /> All Email</label></li>';
	echo '  <li><label><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'dig', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/dig/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="dig" /> Daily</label></li>';
	echo '  <li><label><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'sum', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/sum/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="sum" /> Weekly</label></li>';
	echo '  <li><label><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'no', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/no/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="no" /> No Email</label></li>';

	echo '</ul>';

	wp_enqueue_script( 'openlab-bpges-js', get_stylesheet_directory_uri() . '/js/bpges.js', array( 'jquery' ) );
}
remove_action( 'bp_group_manage_members_admin_item', 'ass_manage_members_email_status' );
add_action( 'bp_group_manage_members_admin_item', 'openlab_manage_members_email_status' );

//remove status from group profile pages
add_action(
	'bp_screens',
	function() {
		remove_action( 'bp_after_group_settings_admin', 'ass_default_subscription_settings_form' );
		add_action( 'bp_after_group_settings_admin', 'openlab_default_subscription_settings_form' );

		remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );
	},
	0
);


/**
 * Bbpress
 * See also: openlab/bbpress for template overrides
 */

/**
 * Plugin: BBPress
 * Adding the forums submenu into the BBPress layout
 */
function openlab_forum_tabs_output() {
	echo openlab_submenu_markup( 'group-forum' );
}

add_action( 'bbp_before_group_forum_display', 'openlab_forum_tabs_output' );

/**
 * Plugin: BBPress
 * Injectiong bootstrap classes into BBPress comment textarea field
 * @param type $output
 * @param type $args
 * @param type $post_content
 * @return type
 */
function openlab_custom_bbp_content( $output ) {
	if ( strpos( $output, 'textarea' ) !== false ) {
		$output = str_replace( 'wp-editor-area', 'form-control', $output );
	}

	return $output;
}

add_filter( 'bbp_get_the_content', 'openlab_custom_bbp_content', 10 );

/**
 * Plugin: BBPress
 * Updating BBPress page navigation to include font awesome icons
 * @param type $pag_args
 * @return string
 */
function openlab_bbp_pagination( $pag_args ) {

	$pag_args['prev_text'] = __( '<i class="fa fa-angle-left"></i>' );
	$pag_args['next_text'] = __( '<i class="fa fa-angle-right"></i>' );
	$pag_args['type']      = 'list';

	return $pag_args;
}

add_filter( 'bbp_topic_pagination', 'openlab_bbp_pagination' );

/**
 * Plugin: BBPress
 * Injecting classes into pagination container to unify pagination styling
 * @param type $pagination
 * @return type
 */
function openlab_bbp_paginatin_custom_markup( $pagination ) {

	$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );

	return $pagination;
}

add_filter( 'bbp_get_forum_pagination_links', 'openlab_bbp_paginatin_custom_markup' );

/**
 * Plugin: BBpress
 * Injecting bootstrap and site standard button classes into subscription toggle button
 * @param type $html
 * @param type $r
 * @param type $user_id
 * @param type $topic_id
 * @return type
 */
function openlab_style_bbp_subscribe_link( $html ) {
	if ( ! bbp_is_single_topic() ) {
		$html = str_replace( 'class="subscription-toggle"', 'class="subscription-toggle btn btn-primary btn-margin btn-margin-top no-deco"', $html );
	}

	return $html;
}

add_filter( 'bbp_get_user_subscribe_link', 'openlab_style_bbp_subscribe_link', 10 );

/**
 * More generous cap mapping for bbPress topic posting.
 *
 * bbPress maps everything onto Participant. We don't want to have to use that.
 */
function openlab_bbp_map_group_forum_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	if ( ! bp_is_group() ) {
		return $caps;
	}
	switch ( $cap ) {
		// If user is a group mmember, allow them to create content.
		case 'read_forum':
		case 'publish_replies':
		case 'publish_topics':
		case 'read_hidden_forums':
		case 'read_private_forums':
			if ( bbp_group_is_member() || bbp_group_is_mod() || bbp_group_is_admin() ) {
				$caps = array( 'exist' );
			}
			break;
		// If user is a group mod ar admin, map to participate cap.
		case 'moderate':
		case 'edit_topic':
		case 'edit_reply':
		case 'view_trash':
		case 'edit_others_replies':
		case 'edit_others_topics':
			if ( bbp_group_is_mod() || bbp_group_is_admin() ) {
				$caps = array( 'exist' );
			}
			break;
		// If user is a group admin, allow them to delete topics and replies.
		case 'delete_topic':
		case 'delete_reply':
			if ( bbp_group_is_admin() ) {
				$caps = array( 'exist' );
			}
			break;
	}
	return apply_filters( 'bbp_map_group_forum_topic_meta_caps', $caps, $cap, $user_id, $args );
}

add_filter( 'bbp_map_meta_caps', 'openlab_bbp_map_group_forum_meta_caps', 10, 4 );

/**
 * Force bbPress to display all forums (ie don't hide any hidden forums during bbp_has_forums() queries).
 *
 * We manage visibility ourselves.
 *
 * See #1299.
 */
add_filter( 'bbp_include_all_forums', '__return_true' );

/**
 * Force bbp_has_forums() to show all post statuses.
 *
 * As above, I have no idea why bbPress makes some items hidden, but it appears
 * incompatible with BuddyPress groups.
 */
function openlab_bbp_force_all_forum_statuses( $r ) {
	$r['post_status'] = array( bbp_get_public_status_id(), bbp_get_private_status_id(), bbp_get_hidden_status_id() );
	return $r;
}

add_filter( 'bbp_before_has_forums_parse_args', 'openlab_bbp_force_all_forum_statuses' );

/**
 * Ensure that post results for bbPres forum queries are never marked hidden.
 *
 * Working with bbPress is really exhausting.
 */
function openlab_bbp_force_forums_to_public( $posts, $query ) {
	if ( ! function_exists( 'bp_is_group' ) || ! bp_is_group() ) {
		return $posts;
	}
	if ( 'forum' !== $query->get( 'post_type' ) ) {
		return $posts;
	}
	foreach ( $posts as &$post ) {
		$post->post_status = 'publish';
	}
	return $posts;
}

add_filter( 'posts_results', 'openlab_bbp_force_forums_to_public', 10, 2 );

/**
 * Force site public to 1 for bbPress.
 *
 * Otherwise activity is not posted.
 */
function openlab_bbp_force_site_public_to_1( $public, $site_id ) {
	if ( 1 === (int) $site_id ) {
		$public = 1;
	}
	return $public;
}

add_filter( 'bbp_is_site_public', 'openlab_bbp_force_site_public_to_1', 10, 2 );

/**
 * Ensure that 'tinymce' flag is set for all bbPress interfaces.
 *
 * This gives us access to the Visual editor.
 */
add_filter(
	'bbp_before_get_the_content_parse_args',
	function( $args ) {
		$args['tinymce'] = true;
		return $args;
	}
);

/**
 * Fix bbPress's bad 'hide_super_sticky_admin_link' regex.
 *
 * It only removes the text, but we want to remove the entire empty element.
 *
 * This ensures that we don't get "empty link" errors in WAVE tests.
 *
 * @return string
 */
function openlab_bbpress_remove_super_sticky_link( $link ) {
	// Remove anchor elements with the class bbp-topic-super-sticky-link.
	return preg_replace( '/<a[^>]*class="bbp-topic-super-sticky-link"[^>]*><\/a>/', '', $link );
}
add_filter( 'bbp_get_topic_stick_link', 'openlab_bbpress_remove_super_sticky_link', 100 );

/**
 * Handle feature toggling for groups.
 */
function openlab_group_feature_toggle( $group ) {
	$group_id = $group->id;

	if ( ! isset( $_POST['openlab-collaboration-tools-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'openlab_collaboration_tools', 'openlab-collaboration-tools-nonce' );

	// Announcements.
	$enable_announcements = ! empty( $_POST['openlab-edit-group-announcements'] );
	if ( $enable_announcements ) {
		groups_delete_groupmeta( $group_id, 'openlab_announcements_disabled' );
	} else {
		groups_update_groupmeta( $group_id, 'openlab_announcements_disabled', '1' );
	}

	// Discussion.
	$enable_forum        = ! empty( $_POST['openlab-edit-group-forum'] );
	$group->enable_forum = $enable_forum;

	// Prevent loops.
	remove_action( 'groups_group_after_save', __FUNCTION__ );
	$group->save();

	if ( $enable_forum ) {
		groups_delete_groupmeta( $group_id, 'openlab_disable_forum' );
	} else {
		groups_update_groupmeta( $group_id, 'openlab_disable_forum', '1' );
	}

	// Docs.
	$enable_docs                   = ! empty( $_POST['openlab-edit-group-docs'] );
	$docs_settings                 = bp_docs_get_group_settings( $group_id );
	$docs_settings['group-enable'] = (int) $enable_docs;
	groups_update_groupmeta( $group_id, 'bp-docs', $docs_settings );

	// Files.
	$enable_files = ! empty( $_POST['openlab-edit-group-files'] );
	if ( $enable_files ) {
		groups_update_groupmeta( $group_id, 'group_documents_documents_disabled', '0' );
	} else {
		groups_update_groupmeta( $group_id, 'group_documents_documents_disabled', '1' );
	}

	// Calendar.
	$enable_calendar = ! empty( $_POST['openlab-edit-group-calendar'] );
	if ( $enable_calendar ) {
		groups_update_groupmeta( $group_id, 'calendar_is_disabled', '0' );
	} else {
		groups_update_groupmeta( $group_id, 'calendar_is_disabled', '1' );
	}

	// Connections.
	$enable_connections = ! empty( $_POST['openlab-edit-group-connections'] );
	if ( ! $enable_connections ) {
		groups_delete_groupmeta( $group_id, 'openlab_connections_enabled' );
	} else {
		groups_update_groupmeta( $group_id, 'openlab_connections_enabled', '1' );
	}
}
add_action( 'groups_group_after_save', 'openlab_group_feature_toggle' );

/**
 * Failsafe method for determining whether forums should be enabled for a group.
 *
 * Another kewl hack due to issues with bbPress. It should be possible to rely on the `enable_forum` group toggle to
 * determine whether the Discussion tab should be shown. But something about the combination between the old bbPress
 * and the new one means that some groups used to have an associated forum_id without having enable_forum turned on,
 * yet still expect to see the Discussion tab. Our workaround is to require the explicit presence of a 'disable' flag
 * for a group's Discussion tab to be turned off.
 */
function openlab_is_forum_enabled_for_group( $group_id = false ) {
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	if ( ! $group_id ) {
		return false;
	}

	$disable  = (bool) groups_get_groupmeta( $group_id, 'openlab_disable_forum' );
	$forum_id = groups_get_groupmeta( $group_id, 'forum_id' );

	if ( $disable || ! $forum_id ) {
		return false;
	}

	return true;
}

/**
 * If Discussion is disabled for a group, ensure it's removed from the menu.
 *
 * Gah gah gah gah gah gah.
 */
function openlab_bbp_remove_group_nav_item() {
	if ( ! bp_is_group() ) {
		return;
	}

	if ( ! openlab_is_forum_enabled_for_group() ) {
		bp_core_remove_subnav_item( bp_get_current_group_slug(), 'forum' );
	}
}

add_action( 'bp_screens', 'openlab_bbp_remove_group_nav_item', 1 );

/**
 * Enforce group privacy settings when determining bbPress forum privacy.
 *
 * This helps ensure that activity items are marked hide_sitewide as appropriate.
 *
 * See https://bbpress.trac.wordpress.org/ticket/2782,
 * https://bbpress.trac.wordpress.org/ticket/2327,
 * http://openlab.citytech.cuny.edu/redmine/issues/1428
 */
function openlab_enforce_forum_privacy( $is_public, $forum_id ) {
	$group_ids = bbp_get_forum_group_ids( $forum_id );

	if ( ! empty( $group_ids ) ) {
		foreach ( $group_ids as $group_id ) {
			$group = groups_get_group( array( 'group_id' => $group_id ) );

			if ( 'public' !== $group->status ) {
				$is_public = false;
				break;
			}
		}
	}

	return $is_public;
}

add_filter( 'bbp_is_forum_public', 'openlab_enforce_forum_privacy', 10, 2 );

/**
 * Prevent bbPress from recounting forum topics.
 *
 * This can cause a costly tree rebuild. See bbPress #1799. See OL #1663,
 */
function openlab_prevent_bbp_recounts( $r ) {
	if ( (int) bbp_get_group_forums_root_id() === (int) $r['forum_id'] ) {
		$r['forum_id'] = 0;
	}

	return $r;
}

add_filter( 'bbp_after_update_forum_parse_args', 'openlab_prevent_bbp_recounts' );

function openlab_prevent_bbpress_from_recalculating_group_root_reply_count( $id ) {
	$db = debug_backtrace(); // phpcs:disable WordPress.PHP.DevelopmentFunctions

	$group_root = (int) bbp_get_group_forums_root_id();
	if ( ! $group_root ) {
		return $id;
	}

	$group_root_parent = (int) get_post( $group_root )->post_parent;
	if ( $group_root !== $id && $group_root_parent !== $id ) {
		return $id;
	}

	$caller = '';
	foreach ( $db as $key => $step ) {
		if ( ! empty( $step['function'] ) && 'bbp_get_forum_id' === $step['function'] ) {
			$caller = $db[ $key + 1 ]['function'];
		}
	}

	$cb_blacklist = [
		'bbp_update_forum_reply_count',
		'bbp_update_forum_last_topic_id',
		'bbp_update_forum_last_reply_id',
		'bbp_update_forum_last_active_id',
		'bbp_update_forum_last_active_time',
		'bbp_update_forum_subforum_count',
	];

	if ( in_array( $caller, $cb_blacklist ) ) {
		return 0;
	}

	return $id;
}

add_filter( 'bbp_get_forum_id', 'openlab_prevent_bbpress_from_recalculating_group_root_reply_count' );

/**
 * Removes 'This forum is empty' status message.
 */
function openlab_remove_bbpress_empty_forum_description( $description, $r ) {
	$topic_count = bbp_get_forum_topic_count( $r['forum_id'], false );
	if ( ! $topic_count ) {
		return '';
	}

	return $description;
}
add_filter( 'bbp_get_single_forum_description', 'openlab_remove_bbpress_empty_forum_description', 10, 2 );

/**
 * Removes single forum title from group forum page.
 */
function openlab_remove_bbpress_forum_title( $title ) {
	$dbs = debug_backtrace(); // phpcs:disable WordPress.PHP.DevelopmentFunctions

	if ( ! bp_is_group() || ! bp_is_current_action( 'forum' ) ) {
		return $title;
	}

	// Pretty cool technique.
	$is_display_forums        = false;
	$is_single_forum_template = false;
	$is_forum_nav             = false;

	foreach ( $dbs as $db ) {
		if ( ! empty( $db['class'] ) && 'BBP_Forums_Group_Extension' === $db['class'] && ! empty( $db['function'] ) && 'display_forums' === $db['function'] ) {
			$is_display_forums = true;
		}

		if ( ! empty( $db['function'] ) && 'bbp_locate_template' === $db['function'] && ! empty( $db['args'][0][0] ) && 'content-single-forum.php' === $db['args'][0][0] ) {
			$is_single_forum_template = true;
		}

		if ( ! empty( $db['function'] ) && 'openlab_group_forum_submenu' === $db['function'] ) {
			$is_forum_nav = true;
		}
	}

	if ( $is_display_forums && ! $is_single_forum_template && ! $is_forum_nav ) {
		return '';
	}

	return $title;
}
add_filter( 'bbp_get_forum_title', 'openlab_remove_bbpress_forum_title' );
add_filter( 'bbp_get_topic_title', 'openlab_remove_bbpress_forum_title' );

/**
 * Filters bbPress's default search parameters.
 *
 * @param array $r
 * @return array
 */
function openlab_filter_bbpress_search_parameters( $r ) {
	if ( ! bp_is_group() ) {
		return $r;
	}

	unset( $r['post_type']['forum'] );

	if ( ! isset( $r['meta_query'] ) ) {
		$r['meta_query'] = [];
	}

	$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );
	if ( ! $forum_ids ) {
		$forum_ids = [ 0 ];
	}

	$r['meta_query'][] = [
		'key'     => '_bbp_forum_id',
		'value'   => $forum_ids,
		'compare' => 'IN',
	];

	$r['paged'] = ! empty( $_GET['search_paged'] ) ? (int) $_GET['search_paged'] : 1;

	return $r;
}
add_filter( 'bbp_after_has_search_results_parse_args', 'openlab_filter_bbpress_search_parameters' );

/**
 * Filters bbPress's default search pagination parameters.
 *
 * @param array $r
 * @return array
 */
function openlab_filter_bbpress_search_pagination_parameters( $r ) {
	if ( ! bp_is_group() ) {
		return $r;
	}

	if ( empty( $_GET['bbp_search'] ) ) {
		return $r;
	}

	$search_term  = sanitize_text_field( wp_unslash( $_GET['bbp_search'] ) );
	$search_paged = ! empty( $_GET['search_paged'] ) ? (int) $_GET['search_paged'] : 1;

	$r['base']    = add_query_arg( 'bbp_search', $search_term, bp_get_group_permalink( groups_get_current_group() ) . 'forum/' ) . '&search_paged=%#%';
	$r['current'] = $search_paged;

	return $r;
}
add_filter( 'bbp_search_results_pagination', 'openlab_filter_bbpress_search_pagination_parameters' );

/**
 * Email notification management.
 */
function openlab_bbp_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	switch ( $activity->type ) {
		case 'bbp_topic_create':
		case 'bbp_reply_create':
			return openlab_notify_group_members_of_this_action() && 'no' !== $sub;

		default:
			return $send_it;
	}
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_bbp_activity_notification_control', 100, 4 );
add_action( 'bp_ges_add_to_digest_queue_for_user', 'openlab_bbp_activity_notification_control', 100, 4 );

/**
 * Plugin: Social
 */

/**
 * Don't let users logged into an account created by Social remain logged in
 *
 * See #3476
 */
function openlab_log_out_social_accounts() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_id = get_current_user_id();
	$social  = get_user_meta( $user_id, 'social_commenter', true );

	if ( 'true' === $social ) {
		// Make sure there's no last_activity, so the user doesn't show in directories.
		BP_Core_User::delete_last_activity( $user_id );

		// Mark the user as spam, so the profile can't be viewed directly.
		global $wpdb;
		$wpdb->update( $wpdb->users, array( 'status' => 1 ), array( 'ID' => $user_id ) );

		$user = new WP_User( $user_id );
		clean_user_cache( $user );

		// Log out and redirect.
		wp_clear_auth_cookie();
		wp_safe_redirect( '/' );
		die();
	}
}

add_action( 'init', 'openlab_log_out_social_accounts', 0 );

/**
 * Plugin: Category Order and Taxonomy Terms Order
 */
function openlab_refresh_term_cache_after_ordering_update() {

	$taxonomy          = stripslashes( $_POST['taxonomy'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$data              = stripslashes( $_POST['order'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$unserialised_data = unserialize( $data );
	if ( is_array( $unserialised_data ) ) {
		foreach ( $unserialised_data as $key => $values ) {
			$items = explode( '&', $values );
			foreach ( $items as $item_key => $item_ ) {
				$items[ $item_key ] = trim( str_replace( 'item[]=', '', $item_ ) );
			}

			if ( is_array( $items ) && count( $items ) > 0 ) {
				foreach ( $items as $item_key => $term_id ) {
					clean_term_cache( $term_id, $taxonomy );
				}
			}
		}
	}
}

add_action( 'tto/update-order', 'openlab_refresh_term_cache_after_ordering_update' );

/**
 * Change the text displayed along with the pagination on the bbpress main page
 */
function openlab_forum_pagination_count( $html ) {
	$bbp = bbpress();

	// Define local variable(s)
	$retstr = '';

	// Topic query exists
	if ( ! empty( $bbp->topic_query ) ) {

		// Set pagination values
		$count_int = intval( $bbp->topic_query->post_count );
		$start_num = intval( ( $bbp->topic_query->paged - 1 ) * $bbp->topic_query->posts_per_page ) + 1;
		$total_int = ! empty( $bbp->topic_query->found_posts )
			? (int) $bbp->topic_query->found_posts
			: $count_int;

		// Format numbers for display
		$count_num = bbp_number_format( $count_int );
		$from_num  = bbp_number_format( $start_num );
		$total     = bbp_number_format( $total_int );
		$to_num    = bbp_number_format( ( $start_num + ( $bbp->topic_query->posts_per_page - 1 ) > $bbp->topic_query->found_posts )
			? $bbp->topic_query->found_posts
			: $start_num + ( $bbp->topic_query->posts_per_page - 1 ) );

		// Several topics in a forum with a single page
		if ( empty( $to_num ) ) {
			$retstr = sprintf( _n( 'Viewing %1$s', 'Viewing %1$s', $total_int, 'bbpress' ), $total );

		// Several topics in a forum with several pages
		} else {
			$retstr = sprintf( _n( 'Viewing topic %2$s (of %4$s total)', 'Viewing %2$s to %3$s (of %4$s total)', $total_int, 'bbpress' ), $count_num, $from_num, $to_num, $total );
		}

		// Escape results of _n()
		$retstr = esc_html( $retstr );
	}

	return $retstr;
}
add_filter( 'bbp_get_forum_pagination_count', 'openlab_forum_pagination_count' );

/**
 * Change the text displayed along with the pagination on the bbpress search results page
 */
function openlab_search_results_pagination_count() {
	$bbp = bbpress();

	// Define local variable(s)
	$retstr = '';

	// Set pagination values
	$total_int = intval( $bbp->search_query->found_posts    );
	$ppp_int   = intval( $bbp->search_query->posts_per_page );
	$start_int = intval( ( $bbp->search_query->paged - 1 ) * $ppp_int ) + 1;
	$to_int    = intval( ( $start_int + ( $ppp_int - 1 ) > $total_int )
			? $total_int
			: $start_int + ( $ppp_int - 1 ) );

	// Format numbers for display
	$total_num = bbp_number_format( $total_int );
	$from_num  = bbp_number_format( $start_int );
	$to_num    = bbp_number_format( $to_int    );

	// Single page of results
	if ( empty( $to_num ) ) {
		$retstr = sprintf( _n( 'Viewing %1$s', 'Viewing %1$s results', $total_int, 'bbpress' ), $total_num );

	// Several pages of results
	} else {
		$retstr = sprintf( _n( 'Viewing %2$s (of %4$s total)', 'Viewing %2$s to %3$s (of %4$s total)', $bbp->search_query->post_count, 'bbpress' ), $bbp->search_query->post_count, $from_num, $to_num, $total_num );
	}

	return $retstr;
}
add_filter( 'bbp_get_search_pagination_count', 'openlab_search_results_pagination_count' );

/**
 * Change icons on the prev/next buttons in bbpress pagination
 */
function openlab_change_bbpress_pagination_prev_and_next_icons($arr) {
    $arr['next_text'] = '<span class="fa fa-long-arrow-right"></span>';
	$arr['prev_text'] = '<span class="fa fa-long-arrow-left"></span>';

    return $arr;
}
add_filter( 'bbp_topic_pagination', 'openlab_change_bbpress_pagination_prev_and_next_icons' );
add_filter( 'bbp_search_results_pagination', 'openlab_change_bbpress_pagination_prev_and_next_icons' );

function openlab_bbp_topic_pagination_count( $string ) {
	$bbp = bbpress();

	// We are threading replies
	if ( bbp_thread_replies() ) {
		$walker  = new BBP_Walker_Reply();
		$threads = absint( $walker->get_number_of_root_elements( $bbp->reply_query->posts ) - 1 );
		$string  = sprintf( _n( 'Viewing %1$s replies', 'Viewing %1$s replies', $threads, 'bbpress' ), bbp_number_format( $threads ) );
	}

	return $string;
}
add_filter( 'bbp_get_topic_pagination_count', 'openlab_bbp_topic_pagination_count' );


function openlab_bbp_single_topic_description() {
	return '';
}
add_filter( 'bbp_get_single_topic_description', 'openlab_bbp_single_topic_description' );

function openlab_bp_docs_info_header_message() {
	$filters = bp_docs_get_current_filters();
	$message = '';

	// All docs
	if ( empty( $filters ) ) {
		$message = __( 'Viewing <strong>All</strong> Docs', 'openlab' );
	} else {

		// Search
		if ( ! empty( $filters['search_terms'] ) ) {
			$message = sprintf( __( 'Viewing docs containing the term: %s', 'bp-docs' ), esc_html( $filters['search_terms'] ) );
		}

		// Tag
		if ( ! empty( $filters['tags'] ) ) {
			$tagtext = array();

			foreach ( $filters['tags'] as $tag ) {
				$tagtext[] = bp_docs_get_tag_link( array( 'tag' => $tag ) );
			}

			$message = sprintf( __( 'Viewing docs with the tag: %s', 'buddypress-docs' ), implode( ', ', $tagtext ) );
		}
	}
	?>
	<p class="currently-viewing"><?php echo $message ?></p>
	<?php
}

add_filter( 'bp_docs_paginate_links', 'openlab_bp_docs_paginate_links' );
function openlab_bp_docs_paginate_links() {
	global $bp, $wp_query, $wp_rewrite;

	$page_links_total = $bp->bp_docs->doc_query->max_num_pages;

	$pagination_args = array(
		'base' 		=> add_query_arg( 'paged', '%#%' ),
		'format' 	=> '',
		'prev_text' 	=> '<span class="fa fa-long-arrow-left">',
		'next_text' 	=> '<span class="fa fa-long-arrow-right"></span>',
		'total' 	=> $page_links_total,
		'end_size'  => 2,
	);

	if ( $wp_rewrite->using_permalinks() ) {
		$pagination_args['base'] = apply_filters( 'bp_docs_page_links_base_url', user_trailingslashit( trailingslashit( bp_docs_get_archive_link() ) . $wp_rewrite->pagination_base . '/%#%/', 'bp-docs-directory' ), $wp_rewrite->pagination_base );
	}

	$page_links = paginate_links( $pagination_args );

	echo $page_links;
}
