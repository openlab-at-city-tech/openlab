<?php

/**
 * Modifications to BuddyPress Docs behavior.
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
 * Closes Docs settings toggle panels by default.
 */
add_filter(
	'bp_docs_toggleable_open_or_closed_class',
	function() {
		return 'toggle-closed';
	}
);

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
 * Adds a Delete link to the "action links" in the doc loop.
 */
function openlab_add_delete_to_bp_docs_doc_action_links( $links, $doc_id ) {
	if ( current_user_can( 'bp_docs_manage', $doc_id ) && ! bp_docs_is_doc_trashed( $doc_id ) ) {
		$links[] = '<a href="' . bp_docs_get_delete_doc_link( false ) . '" class="delete confirm">' . __( 'Delete', 'buddypress-docs' ) . '</a>';
	}

	return $links;
}
add_filter( 'bp_docs_doc_action_links', 'openlab_add_delete_to_bp_docs_doc_action_links', 10, 2 );

/**
 * Don't allow any of budypress-docs's native directory filters.
 *
 * Instead, we have a Search filter in the theme.
 */
add_filter(
	'bp_docs_filter_types',
	function( $types ) {
		// We only return an empty aray when getting filter titles.
		$dbs              = debug_backtrace();
		$is_filter_titles = false;
		foreach ( $dbs as $db ) {
			if ( ! empty( $db['function'] ) && 'bp_docs_filter_titles' === $db['function'] ) {
				$is_filter_titles = true;
				break;
			}
		}

		if ( $is_filter_titles ) {
			return [];
		}

		return array_filter(
			$types,
			function( $type ) {
				return 'search' === $type['slug'];
			}
		);
	},
	999
);

/**
 * Don't show openlab-private-comments or wp-grade-comments Private checkbox on Docs comments.
 */
add_action(
	'comment_form_logged_in_after',
	function() {
		if ( ! bp_docs_get_current_doc() ) {
			return;
		}

		remove_action( 'comment_form_logged_in_after', 'OpenLab\\PrivateComments\\render_checkbox' );
		remove_action( 'comment_form_logged_in_after', 'olgc_leave_comment_checkboxes' );

	},
	5
);
