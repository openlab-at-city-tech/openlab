<?php

/**
 * Register the Announcements group extension.
 */
add_action(
	'bp_init',
	function() {
		require __DIR__ . '/class-openlab-group-announcements-extension.php';
		bp_register_group_extension( 'OpenLab_Group_Announcements_Extension' );
	}
);

/**
 * Register the Announcements post type.
 */
add_action(
	'init',
	function() {
		$labels = [
			'name'     => 'Group Announcements',
			'singular' => 'Group Announcement',
		];

		$args = [
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => [ 'title', 'editor', 'revisions' ],
			'public'              => false,
			'show_ui'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'map_meta_cap'        => true,
			'show_in_rest'        => false,
		];

		register_post_type( 'openlab_announcement', $args );
	}
);

/**
 * Capability mapping for announcements.
 */
add_filter(
	'map_meta_cap',
	function( $caps, $cap, $user_id, $args ) {
		if ( 'edit_openlab_announcement' !== $cap && 'edit_openlab_announcement_comment' !== $cap ) {
			return $caps;
		}

		if ( empty( $args ) ) {
			return $caps;
		}

		if ( ! $user_id ) {
			return [ 'do_not_allow' ];
		}

		if ( 'edit_openlab_announcement' === $cap ) {
			$announcement_id = (int) $args[0];

			$announcement = get_post( $announcement_id );
			if ( ! $announcement ) {
				return [ 'do_not_allow' ] ;
			}

			if ( (int) $user_id === (int) $announcement->post_author ) {
				return [ 'exist' ];
			}

			$group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );
			if ( $group_id ) {
				if ( groups_is_user_admin( $user_id, $group_id ) || groups_is_user_mod( $user_id, $group_id ) ) {
					return [ 'exist' ];
				}
			}
		} elseif ( 'edit_openlab_announcement_comment' === $cap ) {
			$comment_id = (int) $args[0];

			$comment = get_comment( $comment_id );
			if ( ! $comment ) {
				return [ 'do_not_allow' ];
			}

			if ( (int) $user_id === (int) $comment->user_id ) {
				return [ 'exist' ];
			}

			$announcement = get_post( $comment->comment_post_ID );
			if ( ! $announcement ) {
				return [ 'do_not_allow' ];
			}

			if ( user_can( $user_id, 'edit_openlab_announcement', $announcement->ID ) ) {
				return [ 'exist' ];
			}
		}

		return $caps;
	},
	10,
	4
);

/**
 * Register assets.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_register_script(
			'openlab-quill-script',
			get_stylesheet_directory_uri() . '/js/quill.min.js',
			[ 'jquery' ],
			OL_VERSION,
			true
		);

		wp_register_script(
			'openlab-group-announcements',
			get_stylesheet_directory_uri() . '/js/group-announcements.js',
			[ 'openlab-quill-script', 'wp-backbone' ],
			OL_VERSION,
			true
		);

		wp_register_style(
			'openlab-quill-style',
			get_stylesheet_directory_uri() . '/css/quill.snow.css',
			[],
			OL_VERSION
		);
	}
);

/**
 * Returns whether a user can post announcements.
 *
 * @param int $user_id  Optional. Defaults to current user.
 * @param int $group_id Optional. Defaults to current user.
 * @return bool
 */
function openlab_user_can_post_announcements( $user_id = null, $group_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	$can_post_announcements = false;
	if ( $user_id && $group_id ) {
		$can_post_announcements = groups_is_user_admin( $user_id, $group_id ) || groups_is_user_mod( $user_id, $group_id ) || current_user_can( 'bp_moderate' );
	}

	return $can_post_announcements;
}

/**
 * Returns whether a user can reply to a given announcement.
 *
 * @param int $user_id
 * @param int $announcement_id
 * @return bool
 */
function openlab_user_can_reply_to_announcement( $user_id = null, $announcement_id = null ) {
	$announcement_group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );

	$can_reply = groups_is_user_member( $user_id, $announcement_group_id ) || user_can( $user_id, 'bp_moderate' );

	return $can_reply;
}

/**
 * Returns whether a user can reply to a given reply.
 *
 * @param int $user_id
 * @param int $reply_id
 * @return bool
 */
function openlab_user_can_reply_to_reply( $user_id = null, $reply_id = null ) {
	$reply = get_comment( $reply_id );
	if ( ! $reply ) {
		return false;
	}

	$parent_id       = $reply->comment_parent;
	$announcement_id = $reply->comment_post_ID;

	// Must be able to reply to announcement to reply to its reply.
	$can_reply = openlab_user_can_reply_to_announcement( $user_id, $announcement_id );

	// Further restriction: Replies can only be nested one level.
	if ( $can_reply && $parent_id ) {
		$parent_reply = get_comment( $reply_id );
		$can_reply    = $parent_reply && 0 === $parent_reply->comment_parent;
	}

	return $can_reply;
}

/**
 * Create an announcement.
 *
 * @param array $args {
 *   @type int    $group_id ID of the group.
 *   @type int    $user_id  ID of the author.
 *   @type string $title    Title of the announcement.
 *   @type string $content  Content of the announcement.
 * }
 * @return int ID of announcement object.
 */
function openlab_create_announcement( $args = [] ) {
	$r = array_merge(
		[
			'group_id' => 0,
			'user_id'  => 0,
			'title'    => '',
			'content'  => ''
		],
		$args
	);

	$user  = get_userdata( $r['user_id'] );
	$group = groups_get_group( $r['group_id'] );

	if ( ! $user || ! $group ) {
		return false;
	}

	$post_id = wp_insert_post(
		[
			'post_type'    => 'openlab_announcement',
			'post_status'  => 'publish',
			'post_author'  => $r['user_id'],
			'post_content' => $r['content'],
			'post_title'   => $r['title'],
		],
		true
	);

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return false;
	}

	update_post_meta( $post_id, 'openlab_announcement_group_id', $r['group_id'] );

	/**
	 * Fires after an announcement has been created.
	 *
	 * @param int $post_id ID of the announcement object.
	 */
	do_action( 'openlab_create_announcement', $post_id );

	groups_update_last_activity( $r['group_id'] );

	return $post_id;
}

/**
 * Create an announcement reply.
 *
 * @param array $args {
 *   @type int    $group_id        ID of the group.
 *   @type int    $announcement_id ID of the immediate parent item.
 *   @type int    $parent_id       ID of the parent reply, if any.
 *   @type string $content         Content of the reply.
 * }
 * @return int ID of reply object.
 */
function openlab_create_announcement_reply( $args = [] ) {
	$r = array_merge(
		[
			'group_id'        => 0,
			'announcement_id' => 0,
			'parent_id'       => 0,
			'content'         => ''
		],
		$args
	);

	$user            = get_userdata( $r['user_id'] );
	$parent_id       = (int) $r['parent_id'];
	$announcement_id = (int) $r['announcement_id'];

	if ( ! $user || ! $announcement_id ) {
		return false;
	}

	$comment_id = wp_insert_comment(
		[
			'comment_post_ID' => $announcement_id,
			'comment_parent'  => $parent_id,
			'user_id'         => $r['user_id'],
			'comment_content' => $r['content'],
		]
	);

	if ( ! $comment_id ) {
		return false;
	}

	/**
	 * Fires after an announcement reply has been created.
	 *
	 * @param int $comment_id      ID of the reply comment.
	 * @param int $announcement_id ID of the announcement object.
	 */
	do_action( 'openlab_create_announcement_reply', $comment_id, $announcement_id );

	groups_update_last_activity( $r['group_id'] );

	return $comment_id;
}

/**
 * Handles announcement post requests (non-AJAX).
 */
function openlab_handle_announcement_post() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( ! bp_is_current_action( 'announcements' ) || ! bp_is_action_variable( 'post', 0 ) ) {
		return false;
	}


	// Check the nonce.
	check_admin_referer( 'post_update', '_wpnonce_post_update' );

	$content = apply_filters( 'bp_activity_post_update_content', $_POST['announcement-text'] );

	if ( ! empty( $_POST['whats-new-post-in'] ) ) {
		$group_id = apply_filters( 'bp_activity_post_update_item_id', $_POST['whats-new-post-in'] );
	}

	if ( ! empty( $_POST['announcement-title'] ) ) {
		$title = sanitize_text_field( wp_unslash( $_POST['announcement-title'] ) );
	}

	if ( ! $group_id ) {
		return;
	}

	// Verify permissions.
	if ( ! openlab_user_can_post_announcements( bp_loggedin_user_id(), $group_id ) ) {
		return;
	}

	// No activity content so provide feedback and redirect.
	if ( empty( $content ) ) {
		bp_core_add_message( __( 'Please enter some content to post.', 'buddypress' ), 'error' );
		bp_core_redirect( wp_get_referer() );
	}

	$announcement_id = openlab_create_announcement(
		[
			'content'  => $content,
			'title'    => $title,
			'group_id' => $group_id,
			'user_id'  => bp_loggedin_user_id(),
		]
	);

	// Provide user feedback.
	if ( $announcement_id ) {
		bp_core_add_message( 'Announcement posted!' );
	} else {
		bp_core_add_message( 'There was an error when posting your announcement. Please try again.', 'error' );
	}

	// Redirect.
	bp_core_redirect( wp_get_referer() );
}
add_action( 'bp_actions', 'openlab_handle_announcement_post' );

/**
 * Process announcement posts via AJAX.
 */
function openlab_handle_announcement_post_ajax() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	// Check the nonce
	check_admin_referer( 'post_update', '_wpnonce_post_update' );

	if ( ! is_user_logged_in() || empty( $_POST['group_id'] ) ) {
		wp_send_json_error( 'Could not post announcement' );
	}

	$group_id = (int) $_POST['group_id'];
	$user_id  = bp_loggedin_user_id();
	if ( ! openlab_user_can_post_announcements( $user_id, $group_id ) ) {
		return;
	}

	if ( empty( $_POST['content'] ) ) {
		wp_send_json_error( 'Please enter some content to post.' );
	}

	if ( empty( $_POST['title'] ) ) {
		wp_send_json_error( 'Please enter a title.' );
	}

	$announcement_id = openlab_create_announcement(
		[
			'content'  => $_POST['content'],
			'title'    => $_POST['title'],
			'group_id' => $group_id,
			'user_id'  => $user_id,
		]
	);

	if ( false === $announcement_id ) {
		wp_send_json_error( 'Could not post announcement' );
	}

	ob_start();

	bp_get_template_part( 'groups/single/announcements/entry', '', [ 'announcement_id' => $announcement_id ] );

	$contents = ob_get_clean();

	wp_send_json_success( $contents );
}
add_action( 'wp_ajax_openlab_post_announcement', 'openlab_handle_announcement_post_ajax' );

/**
 * Process announcement replies via AJAX.
 */
function openlab_handle_announcement_reply_ajax() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	if ( empty( $_POST['announcementId'] ) || empty( $_POST['editorId'] ) ) {
		return;
	}

	$announcement_id = (int) $_POST['announcementId'];
	$parent_id       = ! empty( $_POST['parentReplyId'] ) ? (int) $_POST['parentReplyId'] : 0;

	$editor_id = wp_unslash( $_POST['editorId'] );
	check_admin_referer( 'announcement_' . $editor_id, 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( 'Could not post announcement' );
	}

	if ( empty( $_POST['content'] ) ) {
		wp_send_json_error( 'Please enter some content to post.' );
	}

	$reply_id = openlab_create_announcement_reply(
		[
			'content'         => $_POST['content'],
			'parent_id'       => $parent_id,
			'announcement_id' => $announcement_id,
			'user_id'         => bp_loggedin_user_id(),
		]
	);

	if ( false === $reply_id ) {
		wp_send_json_error( 'Could not post reply' );
	}

	ob_start();

	bp_get_template_part( 'groups/single/announcements/reply', '', [ 'reply_id' => $reply_id ] );

	$contents = ob_get_clean();

	wp_send_json_success( $contents );
}
add_action( 'wp_ajax_openlab_post_announcement_reply', 'openlab_handle_announcement_reply_ajax' );

/**
 * Process AJAX announcement edit.
 */
function openlab_handle_announcement_edit_ajax() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	if ( empty( $_POST['editorId'] ) || empty( $_POST['announcementId'] ) ) {
		return;
	}

	$editor_id = wp_unslash( $_POST['editorId'] );

	check_admin_referer( 'announcement_' . $editor_id, 'nonce' );

	$announcement_id = wp_unslash( $_POST['announcementId'] );

	if ( ! current_user_can( 'edit_openlab_announcement', $announcement_id ) ) {
		wp_send_json_error();
	}

	// wp_update_post() expects slashed content.
	$content = $_POST['content'];

	$saved = wp_update_post(
		[
			'ID'           => $announcement_id,
			'post_content' => $content,
			'post_title'   => $_POST['title'],
		]
	);

	if ( ! $saved ) {
		wp_send_json_error();
	}

	$announcement = get_post( $announcement_id );

	wp_send_json_success(
		[
			'content' => $announcement->post_content,
			'title'   => $announcement->post_title,
		]
	);
}
add_action( 'wp_ajax_openlab_edit_announcement', 'openlab_handle_announcement_edit_ajax' );

/**
 * Process AJAX announcement reply edit.
 */
function openlab_handle_announcement_reply_edit_ajax() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	if ( empty( $_POST['editorId'] ) || empty( $_POST['replyId'] ) ) {
		return;
	}

	$editor_id = wp_unslash( $_POST['editorId'] );

	check_admin_referer( 'announcement_' . $editor_id, 'nonce' );

	$reply_id = intval( $_POST['replyId'] );

	if ( ! current_user_can( 'edit_openlab_announcement_comment', $reply_id ) ) {
		wp_send_json_error();
	}

	// wp_update_post() expects slashed content.
	$content = $_POST['content'];

	$saved = wp_update_comment(
		[
			'comment_ID'      => $reply_id,
			'comment_content' => $content,
		]
	);

	if ( ! $saved ) {
		wp_send_json_error();
	}

	$reply = get_comment( $reply_id );

	wp_send_json_success(
		[
			'content' => $reply->comment_content,
		]
	);
}
add_action( 'wp_ajax_openlab_edit_announcement_reply', 'openlab_handle_announcement_reply_edit_ajax' );

/**
 * Handles announcement delete request.
 */
function openlab_handle_announcement_delete_request() {
	if ( ! bp_is_group() || ! bp_is_current_action( 'announcements' ) || empty( $_GET['delete-announcement'] ) ) {
		return;
	}

	$announcement_id = (int) $_GET['delete-announcement'];

	check_admin_referer( 'announcement_delete_' . $announcement_id );

	if ( ! current_user_can( 'edit_openlab_announcement', $announcement_id ) ) {
		return;
	}

	$deleted = wp_delete_post( $announcement_id, true );

	if ( $deleted ) {
		bp_core_add_message( 'Announcement successfully deleted.' );
	} else {
		bp_core_add_message( 'Could not delete announcement.', 'error' );
	}

	bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'announcements/' );
	die;
}
add_action( 'bp_actions', 'openlab_handle_announcement_delete_request' );

/**
 * Handles announcement reply delete request.
 */
function openlab_handle_announcement_reply_delete_request() {
	if ( ! bp_is_group() || ! bp_is_current_action( 'announcements' ) || empty( $_GET['delete-announcement-reply'] ) ) {
		return;
	}

	$reply_id = (int) $_GET['delete-announcement-reply'];

	check_admin_referer( 'announcement_delete_' . $reply_id );

	if ( ! current_user_can( 'edit_openlab_announcement_comment', $reply_id ) ) {
		return;
	}

	$deleted = wp_delete_comment( $reply_id, true );

	if ( $deleted ) {
		bp_core_add_message( 'Reply successfully deleted.' );
	} else {
		bp_core_add_message( 'Could not delete reply.', 'error' );
	}

	bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'announcements/' );
	die;
}
add_action( 'bp_actions', 'openlab_handle_announcement_reply_delete_request' );

/**
 * Generates an activity item after an announcement is posted.
 *
 * @param int $announcement_id
 */
function openlab_generate_announcement_activity( $announcement_id ) {
	$post = get_post( $announcement_id );
	if ( ! $post ) {
		return;
	}

	$group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );
	$group    = groups_get_group( $group_id );

	$hide_sitewide = ! empty( $group ) && isset( $group->status ) && 'public' !== $group->status;

	$announcement_url  = bp_get_group_permalink( $group ) . 'announcements/#announcement-item-' . $announcement_id;
	$announcement_link = sprintf( '<a href="%s">%s</a>', esc_url( $announcement_url ), get_the_title( $post ) );

	$action = sprintf(
		'%1$s posted the announcement %2$s in the group %3$s',
		bp_core_get_userlink( $post->post_author ),
		$announcement_link,
		openlab_get_group_link( $group_id )
	);

	$args = array(
		'type'          => 'created_announcement',
		'content'       => get_the_content( null, false, $announcement_id ),
		'component'     => 'groups',
		'action'        => $action,
		'primary_link'  => $announcement_url,
		'user_id'       => $post->post_author,
		'item_id'       => $group_id,
		'hide_sitewide' => $hide_sitewide,
	);

	bp_activity_add( $args );
}
add_action( 'openlab_create_announcement', 'openlab_generate_announcement_activity' );

/**
 * Generates an activity item after an announcement reply is posted.
 *
 * @param int $reply_id
 * @param int $announcement_id
 */
function openlab_generate_announcement_reply_activity( $reply_id, $announcement_id ) {
	$post = get_post( $announcement_id );
	if ( ! $post ) {
		return;
	}

	$comment = get_comment( $reply_id );
	if ( ! $comment ) {
		return;
	}

	$group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );
	$group    = groups_get_group( $group_id );

	$hide_sitewide = ! empty( $group ) && isset( $group->status ) && 'public' !== $group->status;

	$announcement_url  = bp_get_group_permalink( $group ) . 'announcements/#announcement-item-' . $announcement_id;
	$announcement_link = sprintf( '<a href="%s">%s</a>', esc_url( $announcement_url ), get_the_title( $post ) );

	$action = sprintf(
		'%1$s replied to the announcement %2$s in the group %3$s',
		bp_core_get_userlink( $comment->user_id ),
		$announcement_link,
		openlab_get_group_link( $group_id )
	);

	$args = array(
		'type'          => 'created_announcement_reply',
		'content'       => $comment->comment_content,
		'component'     => 'groups',
		'action'        => $action,
		'primary_link'  => $announcement_url,
		'user_id'       => $comment->user_id,
		'item_id'       => $group_id,
		'hide_sitewide' => $hide_sitewide,
	);

	bp_activity_add( $args );
}
add_action( 'openlab_create_announcement_reply', 'openlab_generate_announcement_reply_activity', 10, 2 );

/**
 * Checks whether Announcements tab is enabled for a group.
 *
 * @param int $group_id Group id.
 * @return bool
 */
function openlab_is_announcements_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to true in case no value is found, except for portfolios.
	if ( ! $group_id ) {
		return true;
	}

	$is_disabled = groups_get_groupmeta( $group_id, 'openlab_announcements_disabled' );

	// Empty value should default to disabled for portfolios.
	if ( '' === $is_disabled && openlab_is_portfolio( $group_id ) ) {
		$is_disabled = true;
	}

	return empty( $is_disabled );
}
