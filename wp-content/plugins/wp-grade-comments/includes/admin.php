<?php

/**
 * wp-admin functionality.
 *
 * @since 1.1.0
 */

// Grade column on edit.php.
add_filter( 'manage_post_posts_columns', 'olgc_add_grade_column' );
add_action( 'manage_post_posts_custom_column', 'olgc_add_grade_column_content', 10, 2 );

// Grade column on edit-comments.php.
add_filter( 'manage_edit-comments_columns', 'olgc_add_grade_column_to_editcomments' );
add_action( 'manage_comments_custom_column', 'olgc_add_grade_column_content_to_editcomments', 10, 2 );

// Comment editing.
add_action( 'add_meta_boxes_comment', 'olgc_register_meta_boxes' );
add_action( 'edit_comment', 'olgc_save_comment_extras' );

/**
 * Add Grade column to wp-admin Posts list.
 *
 * @since 1.0.0
 *
 * @param array $columns Column info.
 */
function olgc_add_grade_column( $columns ) {
	global $wp_query;

	$show_column = olgc_is_instructor();
	if ( ! $show_column ) {
		// Look ahead to see if any posts have grades.
		foreach ( $wp_query->posts as $post ) {
			// Skip posts not written by the current user.
			if ( get_current_user_id() != $post->post_author ) {
				continue;
			}

			// Find the first available grade on a post comment.
			$comments = get_comments( array(
				'post_id' => $post->ID,
			) );

			foreach ( $comments as $comment ) {
				$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );
				if ( '' !== $grade ) {
					$show_column = true;
					break 2;
				}
			}
		}
	}

	if ( $show_column ) {
		$columns['grade'] = __( 'Grade', 'wp-grade-comments' );
	}

	return $columns;
}

/**
 * Content of the Grade column on Dashboard > Posts.
 *
 * @since 1.0.0
 *
 * @param string $column_name Name of the current column.
 * @param int    $post_id     ID of the post for the current row.
 */
function olgc_add_grade_column_content( $column_name, $post_id ) {
	if ( 'grade' !== $column_name ) {
		return;
	}

	// Only instructors and post authors should see grade.
	$post = get_post( $post_id );
	if ( ! olgc_is_instructor() && ( ! $post || get_current_user_id() != $post->post_author ) ) {
		return;
	}

	// Find the first available grade on a post comment.
	$comments = get_comments( array(
		'post_id' => $post_id,
	) );

	foreach ( $comments as $comment ) {
		$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );

		if ( '' !== $grade ) {
			echo esc_html( $grade );
			break;
		}
	}
}

/**
 * Add Grade column to wp-admin Comments list.
 *
 * @since 1.1.0
 *
 * @param array $columns Column info.
 */
function olgc_add_grade_column_to_editcomments( $columns ) {
	global $wp_list_table;

	// Non-instructors only see column if there's something to show.
	$show_column = olgc_is_instructor();
	if ( ! $show_column ) {
		foreach ( $wp_list_table->items as $comment ) {
			// Skip posts not written by the current user.
			$post = get_post( $comment->comment_post_ID );
			if ( ! $post || get_current_user_id() != $post->post_author ) {
				continue;
			}

			$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );
			if ( '' !== $grade ) {
				$show_column = true;
				break;
			}
		}
	}

	if ( $show_column ) {
		$columns['grade'] = __( 'Grade', 'wp-grade-comments' );
	}

	return $columns;
}

/**
 * Content of the Grade column on Dashboard > Comments.
 *
 * @since 1.1.0
 *
 * @param string $column_name Name of the current column.
 * @param int    $comment_id  ID of the comment for the current row.
 */
function olgc_add_grade_column_content_to_editcomments( $column_name, $comment_id ) {
	if ( 'grade' !== $column_name ) {
		return;
	}

	// Only instructors and post authors should see grade.
	$comment = get_comment( $comment_id );
	$post    = get_post( $comment->comment_post_ID );
	if ( ! olgc_is_instructor() && ( ! $post || get_current_user_id() != $post->post_author ) ) {
		return;
	}

	$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );
	if ( '' !== $grade ) {
		echo esc_html( $grade );
	}
}

/**
 * Add the WP Grade Comments meta boxes to the comment edit screen.
 *
 * @since 1.1.0
 *
 * @param WP_Comment $comment Comment object.
 */
function olgc_register_meta_boxes( $comment ) {
	$comment_post = get_post( $comment->comment_post_ID );

	wp_enqueue_style( 'olgc-meta-boxes', OLGC_PLUGIN_URL . '/assets/css/meta-boxes.css' );

	if ( olgc_is_instructor() || ( ! empty( $comment_post->post_author ) && $comment_post->post_author == get_current_user_id() ) ) {
		add_meta_box(
			'olgc-comment-grade',
			__( 'Grade', 'wp-grade-comments' ),
			'olgc_grade_meta_box',
			'comment',
			'normal'
		);
	}

	if ( olgc_is_instructor() ) {
		add_meta_box(
			'olgc-comment-privacy',
			__( 'Privacy', 'wp-grade-comments' ),
			'olgc_privacy_meta_box',
			'comment',
			'normal'
		);
	}
}

/**
 * Render the Grade meta box.
 *
 * @since 1.1.0
 *
 * @param WP_Comment $comment Comment object.
 */
function olgc_grade_meta_box( $comment ) {
	// Only instructors can edit the grade.
	$disabled = '';
	if ( ! olgc_is_instructor() ) {
		$disabled = 'disabled="disabled"';
	}

	$grade = get_comment_meta( $comment->comment_ID, 'olgc_grade', true );

	?>
	<table class="form-table editcomment">
		<tr>
			<th scope="col">
				<label for="olgc-grade"><?php esc_html_e( 'Grade:', 'wp-grade-comments' ); ?></label>
			</th>

			<td>
				<input id="olgc-grade" name="olgc-grade" value="<?php echo esc_attr( $grade ); ?>" <?php echo $disabled; ?> />
				<?php wp_nonce_field( 'olgc-grade-edit-' . $comment->comment_ID, 'olgc_grade_edit_nonce' ); ?>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Render the Privacy meta box.
 *
 * @since 1.1.0
 *
 * @param WP_Comment $comment Comment object.
 */
function olgc_privacy_meta_box( $comment ) {
	$is_private = get_comment_meta( $comment->comment_ID, 'olgc_is_private', true );

	?>
	<input type="checkbox" value="1" <?php checked( $is_private ); ?> id="olgc-privacy" name="olgc-privacy" /> <label for="olgc-privacy"><?php esc_html_e( 'Make this comment private.', 'wp-grade-comments' ); ?></label>
	<p class="description"><?php esc_html_e( 'Private comments are visible only to instructors and to the post\'s author.', 'wp-grade-comments' ); ?></p>
	<?php wp_nonce_field( 'olgc-privacy-edit-' . $comment->comment_ID, 'olgc_privacy_edit_nonce' ); ?>
	<?php
}

/**
 * Save grade settings when saving comment from the admin.
 *
 * @since 1.1.0
 *
 * @param int $comment_id ID of the comment being saved.
 */
function olgc_save_comment_extras( $comment_id ) {
	// Cap check.
	if ( ! olgc_is_instructor() ) {
		return;
	}

	if ( isset( $_POST['olgc_grade_edit_nonce'] ) && wp_verify_nonce( $_POST['olgc_grade_edit_nonce'], 'olgc-grade-edit-' . $comment_id ) ) {
		// Sanitize and update.
		if ( isset( $_POST['olgc-grade'] ) ) {
			$grade = trim( wp_unslash( $_POST['olgc-grade'] ) );
			if ( $grade ) {
				update_comment_meta( $comment_id, 'olgc_grade', $grade );
			} else {
				delete_comment_meta( $comment_id, 'olgc_grade' );
			}
		}
	}

	if ( isset( $_POST['olgc_privacy_edit_nonce'] ) && wp_verify_nonce( $_POST['olgc_privacy_edit_nonce'], 'olgc-privacy-edit-' . $comment_id ) ) {
		// Sanitize and update.
		$private = (int) ! empty( $_POST['olgc-privacy'] );
		update_comment_meta( $comment_id, 'olgc_is_private', $private );
	}
}

/**
 * Show activation admin notice.
 *
 * @return void
 */
function olgc_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! apply_filters( 'olgc_display_notices', true ) ) {
		return;
	}

	// Notice was dissmised.
	if ( get_option( 'olgc_notice_dismissed' ) ) {
		return;
	}

	// Groan
	$dismiss_url = $_SERVER['REQUEST_URI'];
	$nonce       = wp_create_nonce( 'olgc_notice_dismiss' );
	$dismiss_url = add_query_arg( 'olgc-notice-dismiss', '1', $dismiss_url );
	$dismiss_url = add_query_arg( '_wpnonce', $nonce, $dismiss_url );

	?>
	<style type="text/css">
		.olgc-notice-message p {
			display: flex;
		}
		.olgc-notice-message-dismiss {
			align-self: center;
			margin-left: 8px;
		}
	</style>
	<div class="notice notice-warning fade olgc-notice-message">
		<p><span><?php esc_html_e( 'Please note: The WP Grade Comments plugin allows all Site Administrators to add, view, and edit private comments and grades.', 'wp-grade-comments' ); ?> <strong><?php esc_html_e( 'If you deactivate this plugin, any private comments made while the plugin was activated will become visible on your site to anyone who can view the site.', 'wp-grade-comments' ); ?></strong></span>
		<a class="olgc-notice-message-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss', 'wp-grade-comments' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'olgc_admin_notice' );

/**
 * Catch notice dismissals.
 *
 * @return void
 */
function olgc_catch_notice_dismissals() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['olgc-notice-dismiss'] ) ) {
		return;
	}

	check_admin_referer( 'olgc_notice_dismiss' );

	update_option( 'olgc_notice_dismissed', 1 );
}
add_action( 'admin_init', 'olgc_catch_notice_dismissals' );

/**
 * Display confirmation modal on the plugin deactivation.
 *
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @return void
 */
function olgc_deactivation_notice( $plugin_file ) {
	if ( 'wp-grade-comments/wp-grade-comments.php' !== $plugin_file ) {
		return;
	}

	if ( ! apply_filters( 'olgc_display_notices', true ) ) {
		return;
	}

	wp_enqueue_script( 'oglc-deactivation', OLGC_PLUGIN_URL . 'assets/js/deactivation.js', [], false, true );
	wp_localize_script( 'oglc-deactivation', 'OLGCDeactivate', [
		'message' => esc_html__( 'If you deactivate this plugin, any private comments made while the plugin was activated will become visible on your site to anyone who can view the site.', 'wp-grade-comments' ),
	] );
}
add_action( 'after_plugin_row', 'olgc_deactivation_notice' );
