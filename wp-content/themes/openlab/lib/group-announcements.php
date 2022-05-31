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
			'show_in_rest'        => true,
		];

		register_post_type( 'openlab_announcement', $args );
	}
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
			[ 'openlab-quill-script' ],
			OL_VERSION,
			true
		);

		wp_localize_script(
			'openlab-group-announcements',
			'OpenLabGroupAnnouncements',
			[
				'reply' => 'Reply',
			]
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

	return groups_is_user_member( $user_id, $announcement_group_id ) || user_can( $user_id, 'bp_moderate' );
}

/**
 * Create an announcement.
 *
 * @param array $args {
 *   @type int    $group_id ID of the group.
 *   @type int    $user_id  ID of the author.
 *   @type string $content  Content of the announcement.
 * }
 * @return int ID of announcement object.
 */
function openlab_create_announcement( $args = [] ) {
	$r = array_merge(
		[
			'group_id' => 0,
			'user_id'  => 0,
			'content'  => ''
		],
		$args
	);

	$user  = get_userdata( $r['user_id'] );
	$group = groups_get_group( $r['group_id'] );

	if ( ! $user || ! $group ) {
		return false;
	}

	$post_title = sprintf(
		'New announcement by %1$s in %2$s',
		bp_core_get_user_displayname( $r['user_id'] ),
		$group->name
	);

	$post_id = wp_insert_post(
		[
			'post_type'    => 'openlab_announcement',
			'post_status'  => 'publish',
			'post_author'  => $r['user_id'],
			'post_content' => $r['content'],
			'post_title'   => $post_title,
		],
		true
	);

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return false;
	}

	update_post_meta( $post_id, 'openlab_announcement_group_id', $r['group_id'] );

	// @todo generate activity item

	return $post_id;
}

/**
 * Create an announcement reply.
 *
 * @param array $args {
 *   @type int    $group_id  ID of the group.
 *   @type int    $parent_id ID of the immediate parent item.
 *   @type string $content   Content of the reply.
 * }
 * @return int ID of reply object.
 */
function openlab_create_announcement_reply( $args = [] ) {
	$r = array_merge(
		[
			'group_id'  => 0,
			'parent_id' => 0,
			'content'   => ''
		],
		$args
	);

	$user      = get_userdata( $r['user_id'] );
	$parent_id = (int) $r['parent_id'];

	if ( ! $user || ! $parent_id ) {
		return false;
	}

	$comment_id = wp_insert_comment(
		[
			'comment_post_ID' => $parent_id,
			'user_id'         => $r['user_id'],
			'comment_content' => $r['content'],
		]
	);

	if ( ! $comment_id ) {
		return false;
	}

	// @todo generate activity item

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
//	@todo permission check
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	// Check the nonce
	check_admin_referer( 'post_update', '_wpnonce_post_update' );

	if ( ! is_user_logged_in() || empty( $_POST['group_id'] ) ) {
		wp_send_json_error( 'Could not post announcement' );
	}

	if ( empty( $_POST['content'] ) ) {
		wp_send_json_error( 'Please enter some content to post.' );
	}

	$announcement_id = openlab_create_announcement(
		[
			'content'  => $_POST['content'],
			'group_id' => (int) $_POST['group_id'],
			'user_id'  => bp_loggedin_user_id(),
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

	if ( empty( $_POST['parentId'] ) ) {
		return;
	}

	$parent_id = (int) $_POST['parentId'];

	// Check the nonce
	check_admin_referer( 'announcement_reply', 'announcement-reply-nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( 'Could not post announcement' );
	}

	if ( empty( $_POST['content'] ) ) {
		wp_send_json_error( 'Please enter some content to post.' );
	}

	$reply_id = openlab_create_announcement_reply(
		[
			'content'   => $_POST['content'],
			'parent_id' => $parent_id,
			'user_id'   => bp_loggedin_user_id(),
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
