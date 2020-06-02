<?php
/**
 * Plugin Name: OpenLab Private Comments
 * Description: Private comments for posts and pages.
 * Author: OpenLab
 * Author URI: http://openlab.citytech.cuny.edu
 * Plugin URI: http://openlab.citytech.cuny.edu
 * Version: 1.0.0
 * License: GPL-2.0-or-later
 * Text Domain: openlab-private-comments
 * Domain Path: /languages
 */

namespace OpenLab\PrivateComments;

if ( is_admin() ) {
	require __DIR__ . '/src/admin.php';
}

/**
 * Load the plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'openlab-private-comments' );
}
add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );

/**
 * Is the current user the post author?
 *
 * @param int $post_id Optional. ID of the post. Defaults to current post ID.
 * @return bool
 */
function is_author( $post_id = null ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return false;
	}

	return is_user_logged_in() && get_current_user_id() == $post->post_author;
}

/**
 * Markup for checkboxes in comment form.
 *
 * @return void
 */
function render_checkbox() {
	if ( is_user_logged_in() ) {
		include __DIR__ . '/views/form-checkbox.php';
	}
}
add_action( 'comment_form_logged_in_after', __NAMESPACE__ . '\\render_checkbox' );

/**
 * Handle private comment submission.
 *
 * @param int         $comment_id ID of the comment.
 * @param \WP_Comment $comment    The comment object.
 * @return void
 */
function insert_comment( $comment_id, $comment ) {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$is_private = ! empty( $_POST['ol-private-comment'] );

	// Inherit comment status from parent.
	if ( ! $is_private && ! empty( $comment->comment_parent ) ) {
		$is_private = (bool) get_comment_meta( $comment->comment_parent, 'ol_is_private', true );
	}

	if ( $is_private ) {
		update_comment_meta( $comment_id, 'ol_is_private', '1' );
	}
}
add_action( 'wp_insert_comment', __NAMESPACE__ . '\\insert_comment', 10, 2 );

/**
 * Display private comments only for specific users.
 * Private comments are only visible to the post author/site admin and the commenter.
 *
 * @param \WP_Comment_Query $query
 * @return void
 */
function remove_private_comments( \WP_Comment_Query $query ) {
	$post_id = ! empty( $query->query_vars['post_id'] ) ? $query->query_vars['post_id'] : 0;

	// Unfiltered.
	if ( current_user_can( 'manage_options' ) || is_author( $post_id ) ) {
		return;
	}

	$pc_ids = get_inaccessible_comments( get_current_user_id(), $post_id );

	$not__in = (array) $query->query_vars['comment__not_in'];
	$not__in = array_merge( $not__in, $pc_ids );
	$query->query_vars['comment__not_in'] = $not__in;
}
add_action( 'pre_get_comments', __NAMESPACE__ . '\\remove_private_comments' );

/**
 * Get inaccessible comments for a user.
 *
 * Optionally by post ID.
 *
 * @param int $user_id ID of the user.
 * @param int $post_id Optional. ID of the post.
 * @return int[]       Array of comment IDs.
 */
function get_inaccessible_comments( $user_id, $post_id = 0 ) {
	// Get a list of private comments
	remove_action( 'pre_get_comments', __NAMESPACE__ . '\\remove_private_comments' );

	$comment_args = [
		'meta_query' => [
			[
				'key'   => 'ol_is_private',
				'value' => '1',
			],
		],
		'status' => 'any',
	];

	if ( ! empty( $post_id ) ) {
		$comment_args['post_id'] = $post_id;
	}

	$private_comments = get_comments( $comment_args );

	add_action( 'pre_get_comments', __NAMESPACE__ . '\\remove_private_comments' );

	// Filter out the ones that are written by the logged-in user, as well
	// as those that are attached to a post that the user is the author of
	$pc_ids = [];
	foreach ( $private_comments as $private_comment ) {
		if ( $user_id && ! empty( $private_comment->user_id ) && $user_id == $private_comment->user_id ) {
			continue;
		}

		if ( $user_id ) {
			$comment_post = get_post( $private_comment->comment_post_ID );
			if ( $user_id == $comment_post->post_author ) {
				continue;
			}
		}

		$pc_ids[] = $private_comment->comment_ID;
	}

	return array_unique( array_map( 'absint', $pc_ids ) );
}

/**
 * Filter comments out of comment feeds.
 *
 * @since 1.0.2
 *
 * @param string $where WHERE clause from comment feed query.
 * @return string
 */
function filter_comments_from_feed( $where ) {
	$pc_ids = get_inaccessible_comments( get_current_user_id(), get_queried_object_id() );
	if ( $pc_ids ) {
		$where .= ' AND comment_ID NOT IN (' . implode( ',', array_map( 'intval', $pc_ids ) ) . ')';
	}

	return $where;
}
add_filter( 'comment_feed_where', __NAMESPACE__ . '\\filter_comments_from_feed' );

/**
 * Filter comment count for post/page. Not cool.
 *
 * @param int $count   Comment count.
 * @param int $post_id ID of the post.
 * @return int $count
 */
function filter_comment_count( $count, $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		return $count;
	}

	$query = new \WP_Comment_Query();
	$new_count = $query->query( [
		'post_id' => $post_id,
		'count'   => true,
	] );

	return $new_count;
}
add_filter( 'get_comments_number', __NAMESPACE__ . '\\filter_comment_count', 10, 2 );

/**
 * Prevent private comments from appearing in BuddyPress activity streams.
 *
 * For now, we are going with the sledgehammer of deleting the comment altogether. In the
 * future, we could use hide_sitewide.
 *
 * @param int $comment_id ID of the comment.
 */
function prevent_private_comments_from_creating_bp_activity_items( $comment_id ) {
	$is_private = get_comment_meta( $comment_id, 'ol_is_private', true );

	if ( ! $is_private ) {
		return;
	}

	if ( 'comment_post' === current_action() ) {
		remove_action( 'comment_post', 'bp_blogs_record_comment', 10, 2 );
		remove_action( 'comment_post', 'bp_activity_post_type_comment', 10, 2 );
	} elseif ( 'edit_comment' === current_action() ) {
		remove_action( 'edit_comment', 'bp_blogs_record_comment', 10 );
		remove_action( 'edit_comment', 'bp_activity_post_type_comment', 10 );
	}
}
add_action( 'comment_post', __NAMESPACE__ . '\\prevent_private_comments_from_creating_bp_activity_items', 0 );
add_action( 'edit_comment', __NAMESPACE__ . '\\prevent_private_comments_from_creating_bp_activity_items', 0 );

/**
 * Prevent private comments from appearing in BuddyPress activity streams.
 *
 * @param string     $new_status New comment status.
 * @param string     $old_status Old comment status.
 * @param WP_Comment $comment    Comment object.
 */
function prevent_private_comments_from_creating_bp_activity_items_on_transition( $new_staus, $old_status, $comment ) {
	$is_private = get_comment_meta( $comment->comment_ID, 'ol_is_private', true );

	if ( ! $is_private ) {
		return;
	}

	remove_action( 'transition_comment_status', 'bp_activity_transition_post_type_comment_status', 10 );
}
add_action( 'transition_comment_status', __NAMESPACE__ . '\\prevent_private_comments_from_creating_bp_activity_items_on_transition', 0, 3 );
