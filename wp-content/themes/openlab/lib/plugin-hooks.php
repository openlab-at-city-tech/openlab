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

/**
 * Checks whether Docs is enabled for a group.
 *
 * @param int $group_id Group ID.
 * @return bool
 */
function openlab_is_docs_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to true in case no value is found.
	if ( ! $group_id ) {
		return true;
	}

	$group_settings = bp_docs_get_group_settings( $group_id );

	// Default to true in case no value is found, except for portfolios.
	if ( ! $group_settings || ! isset( $group_settings['group-enable'] ) ) {
		return ! openlab_is_portfolio( $group_id );
	}

	return ! empty( $group_settings['group-enable'] );
}

/**
 * Plugin: BuddyPress Docs
 *
 * Disable attachments.
 */

/**
 * Plugin: BuddyPress Docs
 * Overriding the BP Docs header file to clean up sub menus
 * @param type $menu_template
 * @return string
 */
function openlab_hide_docs_native_menu() {
	$path = STYLESHEETPATH . '/buddypress/groups/single/docs/docs-header.php';
	return $path;
}
add_filter( 'bp_docs_header_template', 'openlab_hide_docs_native_menu' );

/**
 * Plugin: BuddyPress Docs
 * Custom templates for BP Docs pages
 * Allows for layout control and Bootstrap injection
 * @param type $path
 * @param type $template
 * @return type
 */
function openlab_custom_docs_templates( $path, $template ) {
	if ( 'list' === $template->current_view ) {
		$path = bp_locate_template( 'groups/single/docs/docs-loop.php', false );
	} elseif ( 'create' === $template->current_view || 'edit' === $template->current_view ) {
		$path = bp_locate_template( 'groups/single/docs/edit-doc.php', false );
	} elseif ( 'single' === $template->current_view ) {
		$path = bp_locate_template( 'groups/single/docs/single-doc.php', false );
	}

	return $path;
}
add_filter( 'bp_docs_template', 'openlab_custom_docs_templates', 10, 2 );

/**
 * Allow super admins to edit any BuddyPress Doc
 * @global type $bp
 * @param type $user_can
 * @param type $action
 * @return boolean
 */
function openlab_allow_super_admins_to_edit_bp_docs( $user_can, $action ) {
	global $bp;

	if ( 'edit' === $action ) {
		if ( is_super_admin() || (int) bp_loggedin_user_id() === (int) get_the_author_meta( 'ID' ) || $user_can ) {
			$user_can                                 = true;
			$bp->bp_docs->current_user_can[ $action ] = 'yes';
		} else {
			$user_can                                 = false;
			$bp->bp_docs->current_user_can[ $action ] = 'no';
		}
	}

	return $user_can;
}
add_filter( 'bp_docs_current_user_can', 'openlab_allow_super_admins_to_edit_bp_docs', 10, 2 );

/**
 * Hack alert! Allow group avatars to be deleted
 *
 * There is a bug in BuddyPress Docs that blocks group avatar deletion, because
 * BP Docs is too greedy about setting its current view, and thinks that you're
 * trying to delete a Doc instead. Instead of fixing that, which I have no
 * patience for at the moment, I'm just going to override BP Docs's current
 * view in the case of deleting an avatar.
 */
function openlab_fix_avatar_delete( $view ) {
	if ( bp_is_group_admin_page() ) {
		$view = '';
	}

	return $view;
}

add_filter( 'bp_docs_get_current_view', 'openlab_fix_avatar_delete', 9999 );

/**
 * Inject "Notify members" interface before Docs comment submit button.
 */
add_filter(
	'comment_form_submit_button',
	function( $button ) {
		if ( ! bp_docs_is_existing_doc() ) {
			return $button;
		}

		ob_start();
		?>
		<div class="notify-group-members-ui">
			<?php openlab_notify_group_members_ui( true ); ?>
		</div>
		<?php
		$ui = ob_get_contents();
		ob_end_clean();

		return $ui . $button;
	},
	100
);

/**
 * Email notification management.
 */
function openlab_docs_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	switch ( $activity->type ) {
		case 'bp_doc_created':
		case 'bp_doc_edited':
		case 'bp_doc_comment':
			return openlab_notify_group_members_of_this_action() && 'no' !== $sub;

		default:
			return $send_it;
	}
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_docs_activity_notification_control', 100, 4 );
add_action( 'bp_ges_add_to_digest_queue_for_user', 'openlab_docs_activity_notification_control', 100, 4 );

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
 * @return type
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
	echo '<h5>Email Status</h5>';

	echo '<ul class="group-manage-members-bpges-status">';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'no', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/no/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="no" /> No Email</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'sum', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/sum/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="sum" /> Weekly</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'dig', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/dig/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="dig" /> Daily</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'supersub', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/supersub/' . esc_attr( $user_id ) . '/', 'ass_member_email_status' ) ) . '" value="supersub" /> All Email</li>';

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
	?>
	<ul class="nav nav-inline">
		<?php openlab_forum_tabs(); ?>
	</ul>
	<?php
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
 * Handle feature toggling for groups.
 */
function openlab_group_feature_toggle( $group_id ) {
	// Discussion.
	$enable_forum        = ! empty( $_POST['openlab-edit-group-forum'] );
	$group               = groups_get_group( $group_id );
	$group->enable_forum = $enable_forum;
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
}
add_action( 'groups_settings_updated', 'openlab_group_feature_toggle' );

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
function openlab_prevent_bbp_recounts() {
	if ( (int) bbp_get_group_forums_root_id() === (int) $r['forum_id'] ) {
		$r['forum_id'] = 0;
	}

	return $r;
}

add_filter( 'bbp_after_update_forum_parse_args', 'openlab_prevent_bbp_recounts' );

function openlab_prevent_bbpress_from_recalculating_group_root_reply_count( $id ) {
	$group_root        = (int) bbp_get_group_forums_root_id();
	$group_root_parent = (int) get_post( $group_root )->post_parent;
	if ( $group_root !== $id && $group_root_parent !== $id ) {
		return $id;
	}

	$db     = debug_backtrace(); // phpcs:disable WordPress.PHP.DevelopmentFunctions
	$caller = '';
	foreach ( $db as $key => $step ) {
		if ( ! empty( $step['function'] ) && 'bbp_get_forum_id' === $step['function'] ) {
			$caller = $db[ $key + 1 ]['function'];
		}
	}

	if ( 'bbp_update_forum_reply_count' === $caller ) {
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
	if ( ! bp_is_group() || ! bp_is_current_action( 'forum' ) ) {
		return $title;
	}

	// Pretty cool technique.
	$is_display_forums        = false;
	$is_single_forum_template = false;
	$is_forum_nav             = false;

	foreach ( debug_backtrace() as $db ) { // phpcs:disable WordPress.PHP.DevelopmentFunctions
		if ( ! empty( $db['class'] ) && 'BBP_Forums_Group_Extension' === $db['class'] && ! empty( $db['function'] ) && 'display_forums' === $db['function'] ) {
			$is_display_forums = true;
		}

		if ( ! empty( $db['function'] ) && 'bbp_locate_template' === $db['function'] && ! empty( $db['args'][0][0] ) && 'content-single-forum.php' === $db['args'][0][0] ) {
			$is_single_forum_template = true;
		}

		if ( ! empty( $db['function'] ) && 'openlab_forum_tabs' === $db['function'] ) {
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
			unset( $item );
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
