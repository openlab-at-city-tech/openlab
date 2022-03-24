<?php
/**
 * Plugin Name: OpenLab Private Comments
 * Description: Private comments for posts and pages.
 * Author: OpenLab
 * Author URI: http://openlab.citytech.cuny.edu
 * Plugin URI: http://openlab.citytech.cuny.edu
 * Version: 1.0.1
 * License: GPL-2.0-or-later
 * Text Domain: openlab-private-comments
 * Domain Path: /languages
 */

namespace OpenLab\PrivateComments;

const VERSION = '1.0.1';
const PLUGIN_FILE = __FILE__;

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
 * The plugin activation action.
 *
 * @return void
 */
function activate() {
	// Set up admin notice flag.
	if ( ! get_option( 'olpc_notice_dismissed' ) ) {
		update_option( 'olpc_notice_dismissed', '0' );
	}
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Register our assets.
 *
 * @return void
 */
function register_assets() {
	wp_register_style(
		'ol-private-comments-style',
		plugins_url( 'assets/css/private-comments.css' , __FILE__ ),
		[],
		VERSION
	);

	wp_register_script(
		'ol-private-comments-script',
		plugins_url( 'assets/js/private-comments.js' , __FILE__ ),
		[ 'jquery' ],
		VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_assets' );

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
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Add markup.
	include __DIR__ . '/views/form-checkbox.php';

	// Enqueue assets.
	wp_enqueue_style( 'ol-private-comments-style' );
	wp_enqueue_script( 'ol-private-comments-script' );
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
	$revisions = [];

	// Inherit comment status from parent.
	if ( ! $is_private && ! empty( $comment->comment_parent ) ) {
		$is_private = (bool) get_comment_meta( $comment->comment_parent, 'ol_is_private', true );
	}

	if ( $is_private ) {
		// Strips 'disabledupes' string from comments.
		$content = preg_replace( '/disabledupes\{.*\}disabledupes/', '', $comment->comment_content );

		// Add initial revision.
		$revisions[ time() ] = $content;

		update_comment_meta( $comment_id, 'ol_is_private', '1' );
		update_comment_meta( $comment_id, 'ol_private_comment_rev', $revisions );
	}
}
add_action( 'wp_insert_comment', __NAMESPACE__ . '\\insert_comment', 10, 2 );

/**
 * Stores private comment revisions.
 * Fires before comment data is updated in DB.
 *
 * @param array $data The new, processed comment data, or WP_Error.
 * @return array $data Data isn't modifiied in anyway.
 */
function save_comment_revision( $data ) {
	if ( is_wp_error( $data ) ) {
		return $data;
	}

	// Bail if comment is edited by comment author.
	if ( get_current_user_id() === (int) $data['user_id'] ) {
		return $data;
	}

	$comment_id = (int) $data['comment_ID'];
	$is_private = (bool) get_comment_meta( $comment_id, 'ol_is_private', true );

	if ( ! $is_private ) {
		return $data;
	}

	$comment   = get_comment( $comment_id );
	$revisions = get_comment_meta( $comment_id, 'ol_private_comment_rev', true );
	$revisions = empty( $revisions ) ? [] : $revisions;

	// Add new revision.
	$revisions[ time() ] = $comment->comment_content;

	update_comment_meta( $comment_id, 'ol_private_comment_rev', $revisions );

	return $data;
}
add_filter( 'wp_update_comment_data', __NAMESPACE__ . '\\save_comment_revision' );

/**
 * Add "Private" comment notice.
 *
 * @param string     $text    Comment text.
 * @param WP_Comment $comment Comment object.
 * @return string
 */
function comment_notice( $text, $comment ) {
	global $pagenow;

	if ( 'edit-comments.php' === $pagenow ) {
		return $text;
	}

	$is_private = (bool) get_comment_meta( $comment->comment_ID, 'ol_is_private', true );
	if ( ! $is_private ) {
		return $text;
	}

	$comment_text = sprintf(
		'<div class="ol-private-comment-display ol-private-comment-hidden">' .
			'<strong class="ol-private-comment-notice">%s</strong>&nbsp;' .
			'<a href="#" class="ol-private-comment-show ol-private-comment-toggle">%s</a>' .
			'<noscript>' .
				'<span class="ol-private-comment-value-noscript">%s</span>' .
			'</noscript>' .
			'<a href="#" class="ol-private-comment-hide ol-private-comment-toggle">%s</a>' .
			'<span class="ol-private-comment-value"><br />%s</span>' .
		'</div>',
		esc_html__( 'Comment (Private):', 'openlab-private-comments' ),
		esc_html__( '(show)', 'openlab-private-comments' ),
		wp_kses_data( $text ),
		esc_html__( '(hide)', 'openlab-private-comments' ),
		wp_kses_data( $text ),
	);

	return $comment_text;
}
add_filter( 'get_comment_text', __NAMESPACE__ . '\\comment_notice', 100, 2 );

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
function get_inaccessible_comments( $user_id = null, $post_id = 0 ) {
	$olgc = function_exists( 'olgc_remove_private_comments' );

	// Get a list of private comments
	remove_action( 'pre_get_comments', __NAMESPACE__ . '\\remove_private_comments' );

	if ( $olgc ) {
		remove_action( 'pre_get_comments', 'olgc_remove_private_comments' );
	}

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

	if ( $olgc ) {
		add_action( 'pre_get_comments', 'olgc_remove_private_comments' );
	}

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

		// Comment authors should see private replies.
		if ( ! empty( $private_comment->comment_parent ) ) {
			$parent_id      = (int) $private_comment->comment_parent;
			$parent_comment = get_comment( $parent_id );

			if ( $user_id == $parent_comment->user_id ) {
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
	// No need for fallback when we don't have post or comments.
	if ( empty( $post_id ) || empty( $count ) ) {
		return $count;
	}

	$query = new \WP_Comment_Query();
	$comments = $query->query( [
		'post_id' => $post_id,
		'fields'  => 'ids',
	] );

	return count( $comments );
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
