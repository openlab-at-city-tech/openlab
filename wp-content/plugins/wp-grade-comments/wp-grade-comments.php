<?php
/*
Plugin Name: WP Grade Comments
Version: 1.3.2
Description: Grades and private comments for WordPress blog posts. Built for the City Tech OpenLab.
Author: Boone Gorges
Author URI: http://boone.gorg.es
Plugin URI: http://openlab.citytech.cuny.edu
Text Domain: wp-grade-comments
Domain Path: /languages
*/

define( 'OLGC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OLGC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( is_admin() ) {
	require OLGC_PLUGIN_DIR . '/includes/admin.php';
}

/**
 * Load textdomain.
 *
 * @since 1.0.0
 */
function olgc_load_plugin_textdomain() {
	load_plugin_textdomain( 'wp-grade-comments' );
}
add_action( 'init', 'olgc_load_plugin_textdomain' );

/**
 * Markup for the checkboxes on the Leave a Comment section.
 *
 * @since 1.0.0
 */
function olgc_leave_comment_checkboxes() {
	if ( ! olgc_is_instructor() ) {
		return;
	}

	?>
	<div class="olgc-checkboxes">
		<label for="olgc-private-comment"><?php _e( 'Make this comment private.', 'wp-grade-comments' ) ?></label> <input type="checkbox" name="olgc-private-comment" id="olgc-private-comment" value="1" />
		<br />
		<label for="olgc-add-a-grade"><?php _e( 'Add a grade.', 'wp-grade-comments' ) ?></label> <input type="checkbox" name="olgc-add-a-grade" id="olgc-add-a-grade" value="1" />
		<br />
	</div>
	<?php
}
add_action( 'comment_form_logged_in_after', 'olgc_leave_comment_checkboxes' );

/**
 * Markup for the grade box on the Leave a Comment section.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments from `comment_form()`.
 * @return array
 */
function olgc_leave_comment_after_comment_fields( $args ) {
	if ( ! olgc_is_instructor() ) {
		return $args;
	}

	$args['comment_notes_after'] .= '
	<div class="olgc-grade-entry">
		<label for="olgc-grade">' . __( 'Grade:', 'wp-grade-comments' ) . '</label> <input type="text" maxlength="5" name="olgc-grade" id="olgc-grade" />
	</div>

	<div class="olgc-privacy-description">
		' . __( 'NOTE: Private response and grade will only be visible to instructors and the post\'s author.', 'wp-grade-comments' ) . '
	</div>' . wp_nonce_field( 'olgc-grade-entry-' . get_the_ID(), '_olgc_nonce', false, false );

	return $args;
}
add_filter( 'comment_form_defaults', 'olgc_leave_comment_after_comment_fields', 1000 );

/**
 * Catch and save values after comment submit.
 *
 * @since 1.0.0
 *
 * @param int        $comment_id ID of the comment.
 * @param WP_Comment $comment    Comment object.
 */
function olgc_insert_comment( $comment_id, $comment ) {
	// Private
	$is_private = olgc_is_instructor() && ! empty( $_POST['olgc-private-comment'] );
	if ( ! $is_private && ! empty( $comment->comment_parent ) ) {
		$is_private = (bool) get_comment_meta( $comment->comment_parent, 'olgc_is_private', true );
	}

	if ( $is_private ) {
		update_comment_meta( $comment_id, 'olgc_is_private', '1' );
	}

	if ( ! isset( $_POST['_olgc_nonce'] ) ) {
		return;
	}

	// Grade
	if ( olgc_is_instructor() && wp_verify_nonce( $_POST['_olgc_nonce'], 'olgc-grade-entry-' . $comment->comment_post_ID ) && ! empty( $_POST['olgc-add-a-grade'] ) && isset( $_POST['olgc-grade'] ) ) {
		$grade = wp_unslash( $_POST['olgc-grade'] );
		update_comment_meta( $comment_id, 'olgc_grade', $grade );
	}
}
add_action( 'wp_insert_comment', 'olgc_insert_comment', 10, 2 );

/**
 * Add 'Private' message, grade, and gloss to comment text.
 *
 * @since 1.0.0
 *
 * @param string     $text    Comment text.
 * @param WP_Comment $comment Comment object.
 * @return string
 */
function olgc_add_private_info_to_comment_text( $text, $comment ) {
	global $pagenow;

	// Grade has its own column on edit-comments.php.
	$grade = '';
	$grade_text = '';
	if ( 'edit-comments.php' !== $pagenow && ( olgc_is_instructor() || olgc_is_author() ) ) {
		$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );
		if ( '' !== $grade ) {
			$grade_text .= sprintf(
				'<div class="olgc-grade-display olgc-grade-hidden">' .
				    '<span class="olgc-grade-label">%s</span>&nbsp;' .
				    '<a href="#" class="olgc-show-grade olgc-grade-toggle">%s</a>' .
				    '<noscript>' .
				        '<span class="olgc-grade-value-noscript">%s</span>' .
				    '</noscript>' .
				    '<a href="#" class="olgc-hide-grade olgc-grade-toggle">%s</a>' .
				    '<span class="olgc-grade-value-script"><br />%s</span>' .
				'</div>',
				esc_html__( 'Grade (Private):', 'wp-grade-comments' ),
				esc_html__( '(show)', 'wp-grade-comments' ),
				esc_html( $grade ),
				esc_html__( '(hide)', 'wp-grade-comments' ),
				esc_html( $grade )
			);
		}
	}

	$is_private = get_comment_meta( $comment->comment_ID, 'olgc_is_private', true );
	$comment_text = $text;
	if ( $is_private ) {
		$comment_text = sprintf(
			'<div class="olgc-grade-display olgc-grade-hidden">' .
			    '<strong class="olgc-private-notice">%s</strong>&nbsp;' .
			    '<a href="#" class="olgc-show-grade olgc-grade-toggle">%s</a>' .
			    '<noscript>' .
			        '<span class="olgc-grade-value-noscript">%s</span>' .
			    '</noscript>' .
			    '<a href="#" class="olgc-hide-grade olgc-grade-toggle">%s</a>' .
			    '<span class="olgc-grade-value-script"><br />%s</span>' .
			'</div>',
			esc_html__( 'Comment (Private):', 'wp-grade-comments' ),
			esc_html__( '(show)', 'wp-grade-comments' ),
			esc_html( $text ),
			esc_html__( '(hide)', 'wp-grade-comments' ),
			esc_html( $text )
		);
	}

	$text = $comment_text . $grade_text;

	$gloss = '';
	if ( '' !== $grade && $is_private ) {
		$gloss = __( 'NOTE: Private response and grade are visible only to instructors and to the post\'s author.', 'wp-grade-comments' );
	} elseif ( $is_private ) {
		$gloss = __( 'NOTE: Private response is visible only to instructors and to the post\'s author.', 'wp-grade-comments' );
	}

	if ( $gloss ) {
		$text .= '<p class="olgc-privacy-description">' . $gloss . '</p>';
	}

	return $text;
}
add_filter( 'get_comment_text', 'olgc_add_private_info_to_comment_text', 100, 2 ); // Late to avoid kses

/**
 * Add a "Private" label to the Reply button on reply comments.
 *
 * @since 1.0.0
 *
 * @param array      $args    Arguments passed to `comment_reply_link()`.
 * @param WP_Comment $comment Comment object.
 */
function olgc_add_private_label_to_comment_reply_link( $args, $comment ) {
	$is_private = get_comment_meta( $comment->comment_ID, 'olgc_is_private', true );
	if ( $is_private ) {
		$args['reply_text']    = '(Private) ' . $args['reply_text'];
		$args['reply_to_text'] = '(Private) ' . $args['reply_to_text'];
	}

	return $args;
}
add_filter( 'comment_reply_link_args', 'olgc_add_private_label_to_comment_reply_link', 10, 2 );

/**
 * Remove private comments via WP_Comment_Query query args.
 *
 * @since 1.2.0
 */
function olgc_remove_private_comments( WP_Comment_Query $comment_query ) {
	$post_id = 0;
	if ( ! empty( $comment_query->query_vars['post_id'] ) ) {
		$post_id = $comment_query->query_vars['post_id'];
	} elseif ( ! empty( $comment_query->query_vars['post_ID'] ) ) {
		$post_id = $comment_query->query_vars['post_ID'];
	}

	// Unfiltered
	if ( olgc_is_instructor() || olgc_is_author( $post_id ) ) {
		return;
	}

	$pc_ids = olgc_get_inaccessible_comments( get_current_user_id(), $post_id );
	if ( ! $pc_ids ) {
		return;
	}

	$not__in = (array) $comment_query->query_vars['comment__not_in'];
	$not__in = array_merge( $not__in, $pc_ids );
	$comment_query->query_vars['comment__not_in'] = $not__in;
}
add_action( 'pre_get_comments', 'olgc_remove_private_comments' );

/**
 * Filter comments out of comment feeds.
 *
 * @since 1.0.2
 *
 * @param string $where WHERE clause from comment feed query.
 * @return string
 */
function olgc_filter_comments_from_feed( $where ) {
	$pc_ids = olgc_get_inaccessible_comments( get_current_user_id(), get_queried_object_id() );
	if ( $pc_ids ) {
		$where .= ' AND comment_ID NOT IN (' . implode( ',', array_map( 'intval', $pc_ids ) ) . ')';
	}

	return $where;
}
add_filter( 'comment_feed_where', 'olgc_filter_comments_from_feed' );

/**
 * Get inaccessible comments for a user.
 *
 * Optionally by post ID.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the user.
 * @param int $post_id Optional. ID of the post.
 * @return array Array of comment IDs.
 */
function olgc_get_inaccessible_comments( $user_id, $post_id = 0 ) {
	// Get a list of private comments
	remove_action( 'pre_get_comments', 'olgc_remove_private_comments' );

	$comment_args = array(
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key'   => 'olgc_is_private',
				'value' => '1',
			),
			array(
				'key'      => 'olgc_grade',
				'operator' => 'EXISTS',
			),
		),
		'status' => 'any',
	);

	if ( ! empty( $post_id ) ) {
		$comment_args['post_id'] = $post_id;
	}

	$private_comments = get_comments( $comment_args );

	add_action( 'pre_get_comments', 'olgc_remove_private_comments' );

	/**
	 * Filter out the comments that the user should in fact have access to:
	 * 1. Those written by the logged-in user
	 * 2. Those attached to a post of which the logged-in user is the author
	 * 3. Those comments that are public and non-empty but have a grade attached
	 */
	$pc_ids = array();
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

		if ( get_comment_meta( $private_comment->comment_ID, 'olgc_grade', true ) ) {
			$private = get_comment_meta( $private_comment->comment_ID, 'olgc_is_private', true );
			if ( ! $private && ! empty( $private_comment->comment_content ) ) {
				continue;
			}
		}

		$pc_ids[] = $private_comment->comment_ID;
	}

	$pc_ids = wp_parse_id_list( $pc_ids );

	return $pc_ids;
}

/**
 * Filter comment count. Not cool.
 *
 * @since 1.0.0
 *
 * @param int $count   Comment count.
 * @param int $post_id ID of the post.
 * @return int
 */
function olgc_get_comments_number( $count, $post_id = 0 ) {
	// No need for fallback when we don't have post or comments.
	if ( empty( $post_id ) || empty( $count ) ) {
		return $count;
	}

	$cquery = new WP_Comment_Query();
	$comments_for_post = $cquery->query( array(
		'post_id' => $post_id,
		'fields'  => 'ids',
	) );

	return count( $comments_for_post );
}
add_filter( 'get_comments_number', 'olgc_get_comments_number', 10, 2 );

/**
 * Enqueue assets.
 *
 * @since 1.0.0
 */
function olgc_enqueue_assets() {
	wp_enqueue_style( 'wp-grade-comments', OLGC_PLUGIN_URL . 'assets/css/wp-grade-comments.css' );
	wp_enqueue_script( 'wp-grade-comments', OLGC_PLUGIN_URL . 'assets/js/wp-grade-comments.js', array( 'jquery' ) );
}
add_action( 'comment_form_before', 'olgc_enqueue_assets' );

/**
 * Is the current user the course instructor?
 *
 * @since 1.0.0
 *
 * @return bool
 */
function olgc_is_instructor() {
	$is_admin = current_user_can( 'manage_options' );

	/**
	 * Filters whether the current user is an "instructor" for the purposes of grade comments.
	 *
	 * @param bool $is_admin By default, `current_user_can( 'manage_options' )`.
	 */
	return apply_filters( 'olgc_is_instructor', $is_admin );
}

/**
 * Is the current user the post author?
 *
 * @since 1.0.0
 *
 * @param int $post_id Optional. ID of the post. Defaults to current post ID.
 * @return bool
 */
function olgc_is_author( $post_id = null ) {
	if ( $post_id ) {
		$post = get_post( $post_id );
	} else {
		$post = get_queried_object();
	}

	if ( ! is_a( $post, 'WP_Post' ) ) {
		return false;
	}

	return is_user_logged_in() && get_current_user_id() == $post->post_author;
}

/**
 * Prevent non-instructors from editing comments that are private or have grades.
 *
 * @since 1.0.2
 */
function olgc_prevent_edit_comment_for_olgc_comments( $caps, $cap, $user_id, $args ) {
	if ( 'edit_comment' === $cap && ! olgc_is_instructor( $user_id ) ) {
		$comment_id = $args[0];
		$is_private = get_comment_meta( $comment_id, 'olgc_is_private', true );
		$grade      = get_comment_meta( $comment_id, 'olgc_grade', true );
		if ( $is_private || $grade ) {
			$caps = array( 'do_not_allow' );
		}
	}

	return $caps;

}
add_filter( 'map_meta_cap', 'olgc_prevent_edit_comment_for_olgc_comments', 10, 4 );

/**
 * Allows empty comments when a grade is attached.
 *
 * Works only in WP 5.0+.
 */
function olgc_allow_empty_comment( $allow, $commentdata ) {
	// Only instructors can do this.
	if ( ! olgc_is_instructor() ) {
		return $allow;
	}

	return ! empty( $_POST['olgc-grade'] );
}
add_filter( 'allow_empty_comment', 'olgc_allow_empty_comment', 10, 2 );

/**
 * Prevent private comments from appearing in BuddyPress activity streams.
 *
 * For now, we are going with the sledgehammer of deleting the comment altogether. In the
 * future, we could use hide_sitewide.
 *
 * @since 1.0.0
 *
 * @param int $comment_id ID of the comment.
 */
function olgc_prevent_private_comments_from_creating_bp_activity_items( $comment_id ) {
	$is_private = get_comment_meta( $comment_id, 'olgc_is_private', true );

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
add_action( 'comment_post', 'olgc_prevent_private_comments_from_creating_bp_activity_items', 0 );
add_action( 'edit_comment', 'olgc_prevent_private_comments_from_creating_bp_activity_items', 0 );

/**
 * Prevent private comments from appearing in BuddyPress activity streams.
 *
 * @since 1.2.0
 *
 * @param string     $new_status New comment status.
 * @param string     $old_status Old comment status.
 * @param WP_Comment $comment    Comment object.
 */
function olgc_prevent_private_comments_from_creating_bp_activity_items_on_transition( $new_staus, $old_status, $comment ) {
	$is_private = get_comment_meta( $comment->comment_ID, 'olgc_is_private', true );

	if ( ! $is_private ) {
		return;
	}

	remove_action( 'transition_comment_status', 'bp_activity_transition_post_type_comment_status', 10 );
}
add_action( 'transition_comment_status', 'olgc_prevent_private_comments_from_creating_bp_activity_items_on_transition', 0, 3 );

/**
 * Add custom classes for private and grade comments.
 *
 * @since 1.4.0
 *
 * @param string[]  $classes    An array of comment classes.
 * @param string    $class      A comma-separated list of additional classes added to the list.
 * @param int       $comment_id The comment id.
 * @return string[] $classes    An array of classes.
 */
function olgc_add_comment_classes( $classes, $class, $comment_id ) {
	if ( get_comment_meta( $comment_id, 'olgc_is_private', true ) ) {
		$classes[] = 'comment-is-private';
	}

	if ( get_comment_meta( $comment_id, 'olgc_grade', true ) ) {
		$classes[] = 'comment-has-grade';
	}

	return $classes;
}
add_filter( 'comment_class', 'olgc_add_comment_classes', 10, 3 );
