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
add_filter( 'comment_form_defaults', 'olgc_leave_comment_after_comment_fields' );

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
	if ( ! empty( $_POST['olgc-private-comment'] ) ) {
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
	// Unfiltered
	if ( olgc_is_instructor() || olgc_is_author( $comment_query->query_vars['post_ID'] ) ) {
		return;
	}

	// Get a list of private comments
	remove_filter( 'comments_clauses', 'olgc_filter_private_comments', 10, 2 );
	$private_comments = get_comments( array(
		'post_ID' => $comment_query->query_vars['post_ID'],
		'meta_query' => array(
			array(
				'key'   => 'olgc_is_private',
				'value' => '1',
			),
		),
	) );
	add_filter( 'comments_clauses', 'olgc_filter_private_comments', 10, 2 );

	// Filter out the ones that are written by the logged-in user, just
	// in case
	$pc_ids = array();
	foreach ( $private_comments as $private_comment ) {
		if ( is_user_logged_in() && ! empty( $private_comment->user_id ) && get_current_user_id() == $private_comment->user_id ) {
			continue;
		}

		$pc_ids[] = $private_comment->comment_ID;

	}

	$pc_ids = wp_parse_id_list( $pc_ids );

	// WP_Comment_Query sucks
	if ( ! empty( $pc_ids ) ) {
		$clauses['where'] .= ' AND comment_ID NOT IN (' . implode( ',', $pc_ids ) . ')';

		$GLOBALS['olgc_private_comment_count'] = count( $pc_ids );
		add_filter( 'get_comments_number', 'olgc_get_comments_number' );
	}

	return $clauses;
}
add_filter( 'comments_clauses', 'olgc_filter_private_comments', 10, 2 );

/**
 * Filter comment count. Not cool.
 */
function olgc_get_comments_number( $count ) {
	if ( ! empty( $GLOBALS['olgc_private_comment_count'] ) ) {
		$count = $count - intval( $GLOBALS['olgc_private_comment_count'] );
	}

	return $count;
}

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

	return is_user_logged_in() && get_current_user_id() == $post->post_author;
}
