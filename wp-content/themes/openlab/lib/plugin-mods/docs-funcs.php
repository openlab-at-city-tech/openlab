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

	// Default to false in case no value is found.
	if ( ! $group_id ) {
		return false;
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
 *
 * @return array
 */
function openlab_remove_directory_filters_from_doc_list( $types ) {
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
}
add_filter( 'bp_docs_filter_types', 'openlab_remove_directory_filters_from_doc_list', 999 );

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

/**
 * Fix the redirect URL after restoring a Docs version.
 */
add_filter(
	'wp_redirect',
	function( $location ) {
		if ( empty( $_GET['revision'] ) || empty( $_GET['action'] ) || 'restore' !== $_GET['action'] ) {
			return $location;
		}

		return bp_docs_get_doc_link();
	}
);

/**
 * Get the info header for a list of docs.
 *
 * Contains things like tag filters.
 */
function openlab_bp_docs_info_header() {
	do_action( 'bp_docs_before_info_header' );

	$filters = bp_docs_get_current_filters();

	// Set the message based on the current filters
	if ( empty( $filters ) ) {
		$message = 'Viewing <strong>All</strong> Docs';
	} else {
		$message = array();

		$message = apply_filters( 'bp_docs_info_header_message', $message, $filters );

		$message = array_map(
			function( $m ) {
				$m = str_replace(
					[
						'You are viewing',
						'with the following tags',
						'You are searching for docs',
					],
					[
						'Viewing',
						'with the tag',
						'Viewing docs',
					],
					$m
				);

				$m = preg_replace(
					'|the term <em>(.*?)</em>|',
					'the term: \1',
					$m
				);

				return $m;
			},
			$message
		);

		$message = implode( "<br />", $message );

		// We are viewing a subset of docs, so we'll add a link to clear filters
		// Figure out what the possible filter query args are.
		$filter_args = apply_filters( 'bp_docs_filter_types', array() );
		$filter_args = wp_list_pluck( $filter_args, 'query_arg' );
		$filter_args = array_merge( $filter_args, array( 'search_submit', 'folder' ) );
	}

	?>

	<p class="currently-viewing"><?php echo $message ?></p>

	<?php if ( $filter_titles = bp_docs_filter_titles() ) : ?>
		<div class="docs-filters">
			<p id="docs-filter-meta">
				<?php printf( __( 'Filter by: %s', 'buddypress-docs' ), $filter_titles ) ?>
			</p>

			<div id="docs-filter-sections">
				<?php do_action( 'bp_docs_filter_sections' ) ?>
			</div>
		</div>

		<div class="clear"> </div>
	<?php endif ?>
	<?php
}

/**
 * Add an excerpt as the content of Docs activity.
 *
 * @param array $args Activity args.
 */
function openlab_add_excerpt_to_docs_activity( $args ) {
	$doc = get_post( $args['secondary_item_id'] );

	$args['content'] = bp_create_excerpt( $doc->post_content );

	return $args;
}
add_filter( 'bp_docs_activity_args', 'openlab_add_excerpt_to_docs_activity' );

/**
 * Modifies caps for BP Docs.
 *
 * - Allows group admins/mods to manage docs in their group.
 *
 * @param array  $caps    Capabilities.
 * @param string $cap     Capability.
 * @param int    $user_id User ID.
 * @param array  $args    Args.
 * @return array
 */
function openlab_bp_docs_map_meta_caps( $caps, $cap, $user_id, $args ) {
	if ( 'bp_docs_manage' !== $cap ) {
		return $caps;
	}

	$doc = bp_docs_get_doc_for_caps( $args );
	if ( $doc instanceof WP_Post && 0 === $doc->ID && bp_docs_get_post_type_name() === $doc->post_type ) {
		return $caps;
	}

	$group_id = bp_docs_get_associated_group_id( $doc->ID, $doc );
	if ( ! $group_id ) {
		return $caps;
	}

	if ( ! groups_is_user_admin( $user_id, $group_id ) && ! groups_is_user_mod( $user_id, $group_id ) ) {
		return $caps;
	}

	$caps = [ 'read' ];

	return $caps;
}
add_filter( 'bp_docs_map_meta_caps', 'openlab_bp_docs_map_meta_caps', 100, 4 );

/**
 * Checks whether comments are allowed on a doc.
 *
 * @param int $doc_id Doc ID.
 * @return bool
 */
function openlab_comments_allowed_on_doc( $doc_id ) {
	$allowed = true;

	$doc = get_post( $doc_id );
	if ( ! $doc || 'bp_doc' !== $doc->post_type ) {
		return $allowed;
	}

	$disabled = get_post_meta( $doc_id, 'openlab_comments_disabled', true );
	if ( 'yes' === $disabled ) {
		$allowed = false;
	}

	return $allowed;
}

/**
 * Gets 'View' setting for a doc.
 *
 * @param int $doc_id Doc ID.
 * @return string
 */
function openlab_get_doc_view_setting( $doc_id ) {
	$saved_setting = get_post_meta( $doc_id, 'openlab_view_setting', true );

	if ( ! $saved_setting ) {
		$group_id = bp_docs_get_associated_group_id( $doc_id );

		// During Doc creation.
		if ( ! $group_id && bp_docs_is_doc_create() ) {
			$group_id = bp_get_current_group_id();
		}

		$group = groups_get_group( $group_id );

		if ( $group && 'public' === $group->status ) {
			$setting = 'everyone';
		} else {
			$setting = 'group-members';
		}
	} else {
		$setting = $saved_setting;
	}

	return $setting;
}

/**
 * Gets 'Edit' setting for a doc.
 *
 * @param int $doc_id Doc ID.
 * @return string
 */
function openlab_get_doc_edit_setting( $doc_id ) {
	$saved_setting = get_post_meta( $doc_id, 'openlab_edit_setting', true );

	if ( ! $saved_setting ) {
		$setting = 'group-members';
	} else {
		$setting = $saved_setting;
	}

	return $setting;
}

/**
 * Saves our custom Doc-specific settings.
 *
 * @param int $doc_id Doc ID.
 * @return void
 */
function openlab_save_custom_doc_settings( $doc_id ) {
	if ( empty( $_POST['bp-docs-save-doc-privacy-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'bp-docs-save-doc-privacy', 'bp-docs-save-doc-privacy-nonce' );

	if ( ! current_user_can( 'bp_docs_manage', $doc_id ) ) {
		return;
	}

	if ( isset( $_POST['doc']['allow_comments'] ) ) {
		$allow_comments = '1' === wp_unslash( $_POST['doc']['allow_comments'] );

		if ( $allow_comments ) {
			delete_post_meta( $doc_id, 'openlab_comments_disabled' );
		} else {
			update_post_meta( $doc_id, 'openlab_comments_disabled', 'yes' );
		}
	}

	if ( isset( $_POST['doc']['view_setting'] ) ) {
		$view_setting = wp_unslash( $_POST['doc']['view_setting'] );

		$allowed_settings = [ 'everyone', 'group-members', 'admins' ];
		if ( in_array( $view_setting, $allowed_settings, true ) ) {
			update_post_meta( $doc_id, 'openlab_view_setting', $view_setting );
		}
	}

	if ( isset( $_POST['doc']['edit_setting'] ) ) {
		$edit_setting = wp_unslash( $_POST['doc']['edit_setting'] );

		$allowed_settings = [ 'group-members', 'admins' ];
		if ( in_array( $edit_setting, $allowed_settings, true ) ) {
			update_post_meta( $doc_id, 'openlab_edit_setting', $edit_setting );
		}
	}
}
add_action( 'bp_docs_after_save', 'openlab_save_custom_doc_settings' );

/**
 * Custom implementation of comments_open for docs.
 *
 * Old Docs can have comments closed by default. We must respect
 * openlab_comments_disabled meta and other doc-specific settings.
 */
function openlab_force_doc_comments_open( $open, $post_id ) {
	return openlab_comments_allowed_on_doc( $post_id );
}
add_action( 'comments_open', 'openlab_force_doc_comments_open', 999, 2 );

/**
 * Meta cap mapping for our custom doc settings.
 *
 * @param array  $caps    Capabilities.
 * @param string $cap     Capability.
 * @param int    $user_id User ID.
 * @param array  $args    Args.
 * @return array
 */
function openlab_bp_docs_map_meta_caps_for_custom_settings( $caps, $cap, $user_id, $args ) {
	switch ( $cap ) {
		case 'bp_docs_read':
		case 'bp_docs_view_history':
		case 'bp_docs_read_comments':
		case 'bp_docs_edit':
			$doc = bp_docs_get_doc_for_caps( $args );

			if ( ! $doc ) {
				return $caps;
			}

			$group_id = bp_docs_get_associated_group_id( $doc->ID, $doc );
			if ( ! $group_id ) {
				return $caps;
			}

			if ( 'bp_docs_edit' === $cap ) {
				$setting = openlab_get_doc_edit_setting( $doc->ID );
			} else {
				$setting = openlab_get_doc_view_setting( $doc->ID );
			}

			$caps = [ 'do_not_allow' ];

			switch ( $setting ) {
				case 'everyone':
					$caps = [ 'read' ];
					break;

				case 'group-members':
					if ( groups_is_user_member( $user_id, $group_id ) ) {
						$caps = [ 'read' ];
					}
					break;

				case 'admins':
					if ( groups_is_user_admin( $user_id, $group_id ) ) {
						$caps = [ 'read' ];
					}
					break;
			}

			break;
	}

	return $caps;
}
add_filter( 'bp_docs_map_meta_caps', 'openlab_bp_docs_map_meta_caps_for_custom_settings', 100, 4 );
