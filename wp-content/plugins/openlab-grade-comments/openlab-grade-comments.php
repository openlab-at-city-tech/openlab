<?php
/*
Plugin Name: OpenLab Grade Comments
Version: 1.0
Description: Grades and private comments for WordPress blog posts. Built for the City Tech OpenLab.
Author: Boone Gorges
Author URI: http://boone.gorg.es
Plugin URI: http://openlab.citytech.cuny.edu
Text Domain: openlab-grade-comments
Domain Path: /languages
*/

/**
 * Markup for the checkboxes on the Leave a Comment section.
 */
function olgc_leave_comment_checkboxes() {
	if ( ! olgc_is_instructor() ) {
		return;
	}

	?>
	<div class="olgc-checkboxes">
		<label for="olgc-private-comment"><?php _e( 'Make this comment private.', 'openlab-grade-comments' ) ?></label> <input type="checkbox" name="olgc-private-comment" id="olgc-private-comment" value="1" />
		<br />
		<label for="olgc-add-a-grade"><?php _e( 'Add a grade.', 'openlab-grade-comments' ) ?></label> <input type="checkbox" name="olgc-add-a-grade" id="olgc-add-a-grade" value="1" />
		<br />
	</div>
	<?php
}
add_action( 'comment_form_logged_in_after', 'olgc_leave_comment_checkboxes' );

/**
 * Markup for the grade box on the Leave a Comment section.
 */
function olgc_leave_comment_after_comment_fields( $args ) {
	if ( ! olgc_is_instructor() ) {
		return $args;
	}

	$args['comment_notes_after'] .= '
	<div class="olgc-grade-entry">
		<label for="olgc-grade">' . __( 'Grade:', 'openlab-grade-comments' ) . '</label> <input type="text" maxlength="5" name="olgc-grade" id="olgc-grade" />
	</div>

	<div class="olgc-privacy-description">
		' . __( 'NOTE: Private response and grade will only be visible to instructors and the post\'s author.', 'openlab-grade-comments' ) . '
	</div>' . wp_nonce_field( 'olgc-grade-entry-' . get_the_ID(), '_olgc_nonce', false, false );

	return $args;
}
add_filter( 'comment_form_defaults', 'olgc_leave_comment_after_comment_fields', 1000 );

/**
 * Catch and save values after comment submit.
 */
function olgc_insert_comment( $comment_id, $comment ) {
	// User has permission
	if ( ! olgc_is_instructor() ) {
		return;
	}

	// User intended to do this
	if ( ! wp_verify_nonce( $_POST['_olgc_nonce'], 'olgc-grade-entry-' . $comment->comment_post_ID ) ) {
		return;
	}

	// Private
	$is_private = ! empty( $_POST['olgc-private-comment'] );
	if ( ! $is_private && ! empty( $comment->comment_parent ) ) {
		$is_private = (bool) get_comment_meta( $comment->comment_parent, 'olgc_is_private', true );
	}

	if ( $is_private ) {
		update_comment_meta( $comment_id, 'olgc_is_private', '1' );
	}

	// Grade
	if ( ! empty( $_POST['olgc-add-a-grade'] ) && ! empty( $_POST['olgc-grade'] ) ) {
		$grade = wp_unslash( $_POST['olgc-grade'] );
		update_comment_meta( $comment_id, 'olgc_grade', $grade );
	}
}
add_action( 'wp_insert_comment', 'olgc_insert_comment', 10, 2 );

/**
 * Add 'Private' message, grade, and gloss to comment text.
 */
function olgc_add_private_info_to_comment_text( $text, $comment ) {
	$grade = '';
	if ( olgc_is_instructor() || olgc_is_author() ) {
		$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );
		if ( $grade ) {
			$text .= '<div class="olgc-grade-display"><span class="olgc-grade-label">' . __( 'Grade (Private):', 'openlab-grade-comments' ) . '</span> ' . esc_html( $grade ) . '</div>';
		}
	}

	$is_private = get_comment_meta( $comment->comment_ID, 'olgc_is_private', true );
	if ( $is_private ) {
		$text = '<strong class="olgc-private-notice">' . __( '(Private)', 'openlab-grade-comments' ) . '</strong> ' . $text;
	}

	$gloss = '';
	if ( $grade && $is_private ) {
		$gloss = __( 'NOTE: Private response and grade are visible only to instructors and to the post\'s author.', 'openlab-grade-comments' );
	} else if ( $private ) {
		$gloss = __( 'NOTE: Private response is visible only to instructors and to the post\'s author.', 'openlab-grade-comments' );
	}

	if ( $gloss ) {
		$text .= '<p class="olgc-privacy-description">' . $gloss . '</p>';
	}

	return $text;
}
add_filter( 'get_comment_text', 'olgc_add_private_info_to_comment_text', 100, 2 ); // Late to avoid kses

/**
 * Ensure that private comments are only included for the proper users.
 */
function olgc_filter_private_comments( $clauses, $comment_query ) {
	$post_id = 0;
	if ( ! empty( $comment_query->query_vars['post_id'] ) ) {
		$post_id = $comment_query->query_vars['post_id'];
	} else if ( ! empty( $comment_query->query_vars['post_ID'] ) ) {
		$post_id = $comment_query->query_vars['post_ID'];
	}

	// Unfiltered
	if ( olgc_is_instructor() || olgc_is_author( $post_id ) ) {
		return $clauses;
	}

	$pc_ids = olgc_get_inaccessible_comments( get_current_user_id(), $post_id );

	// WP_Comment_Query sucks
	if ( ! empty( $pc_ids ) ) {
		$clauses['where'] .= ' AND comment_ID NOT IN (' . implode( ',', $pc_ids ) . ')';
	}

	return $clauses;
}
add_filter( 'comments_clauses', 'olgc_filter_private_comments', 10, 2 );

/**
 * Filter private comments out of the 'comments_array'
 *
 * This is called during comments_template, instead of the API. This is a damn
 * mess.
 */
function olgc_filter_comments_array( $comments, $post_id ) {
	$pc_ids = olgc_get_inaccessible_comments( get_current_user_id(), $post_id );

	foreach ( $comments as $ckey => $cvalue ) {
		if ( in_array( $cvalue->comment_ID, $pc_ids ) ) {
			unset( $comments[ $ckey ] );
		}
	}

	$comments = array_values( $comments );
	return $comments;
}
add_filter( 'comments_array', 'olgc_filter_comments_array', 10, 2 );

/**
 * Get inaccessible comments for a user.
 *
 * Optionally by post ID.
 */
function olgc_get_inaccessible_comments( $user_id, $post_id = 0 ) {
	// Get a list of private comments
	remove_filter( 'comments_clauses', 'olgc_filter_private_comments', 10, 2 );
	$comment_args = array(
		'meta_query' => array(
			array(
				'key'   => 'olgc_is_private',
				'value' => '1',
			),
		),
	);

	if ( ! empty( $post_id ) ) {
		$comment_args['post_id'] = $post_id;
	}

	$private_comments = get_comments( $comment_args );
	add_filter( 'comments_clauses', 'olgc_filter_private_comments', 10, 2 );

	// Filter out the ones that are written by the logged-in user, as well
	// as those that are attached to a post that the user is the author of
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

		$pc_ids[] = $private_comment->comment_ID;

	}

	$pc_ids = wp_parse_id_list( $pc_ids );

	return $pc_ids;
}

/**
 * Filter comment count. Not cool.
 */
function olgc_get_comments_number( $count, $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		return $count;
	}

	$cquery = new WP_Comment_Query();
	$comments_for_post = $cquery->query( array(
		'post_id' => $post_id,
		'count' => true,
	) );
	$count = $comments_for_post;

	return $count;
}
add_filter( 'get_comments_number', 'olgc_get_comments_number', 10, 2 );

/**
 * Enqueue assets
 */
function olgc_enqueue_assets() {
	wp_enqueue_style( 'openlab-grade-comments', plugins_url( 'openlab-grade-comments/assets/css/openlab-grade-comments.css' ) );
	wp_enqueue_script( 'openlab-grade-comments', plugins_url( 'openlab-grade-comments/assets/js/openlab-grade-comments.js' ), array( 'jquery' ) );
}
add_action( 'comment_form_before', 'olgc_enqueue_assets' );

/**
 * Is the current user the course instructor?
 */
function olgc_is_instructor() {
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	return groups_is_user_admin( get_current_user_id(), $group_id );
}

/**
 * Is the current user the post author?
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
 * Prevent private comments from appearing in BuddyPress activity streams.
 *
 * For now, we are going with the sledgehammer of deleting the comment altogether. In the
 * future, we could use hide_sitewide.
 */
function olgc_prevent_private_comments_from_creating_bp_activity_items( $comment_id ) {
	$is_private = get_comment_meta( $comment_id, 'olgc_is_private', true );

	if ( ! $is_private ) {
		return;
	}

	if ( 'comment_post' === current_action() ) {
		remove_action( 'comment_post', 'bp_blogs_record_comment', 10, 2 );
	} else if ( 'edit_comment' === current_action() ) {
		remove_action( 'edit_comment', 'bp_blogs_record_comment', 10 );
	}
}
add_action( 'comment_post', 'olgc_prevent_private_comments_from_creating_bp_activity_items', 0 );
add_action( 'edit_comment', 'olgc_prevent_private_comments_from_creating_bp_activity_items', 0 );



/** Admin ********************************************************************/

/**
 * Add Grade column to wp-admin Posts list.
 */
function olgc_add_grade_column( $columns ) {
	$columns['grade'] = __( 'Grade', 'openlab-grade-comments' );
	return $columns;
}
add_filter( 'manage_post_posts_columns', 'olgc_add_grade_column' );

/**
 * Content of the Grade column.
 */
function olgc_add_grade_column_content( $column_name, $post_id ) {
	if ( 'grade' !== $column_name ) {
		return;
	}

	// Find the first available grade on a post comment.
	$comments = get_comments( array(
		'post_id' => $post_id,
	) );

	foreach ( $comments as $comment ) {
		$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );

		if ( $grade ) {
			echo esc_html( $grade );
			break;
		}
	}
}
add_action( 'manage_post_posts_custom_column', 'olgc_add_grade_column_content', 10, 2 );
