<?php

/**
 * Mods related to email, including BPGES.
 */

/**
 * Put the group type in email notification subject lines
 * @param type $subject
 * @return type
 */
function openlab_group_type_in_notification_subject( $subject ) {

	if ( ! empty( $groups_template->group->id ) ) {
		$group_id = $groups_template->group->id;
	} elseif ( ! empty( $bp->groups->current_group->id ) ) {
		$group_id = $bp->groups->current_group->id;
	} else {
		return $subject;
	}

	if ( isset( $_COOKIE['wds_bp_group_type'] ) ) {
		$grouptype = $_COOKIE['wds_bp_group_type'];
	} else {
		$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
	}

	return str_replace( 'in the group', 'in the ' . $grouptype, $subject );
}

add_filter( 'ass_clean_subject', 'openlab_group_type_in_notification_subject' );

/**
 * Default subscription level for group emails should be All
 */
function openlab_default_group_subscription( $level ) {
	if ( ! $level ) {
		$level = 'supersub';
	}

	return $level;
}

add_filter( 'ass_default_subscription_level', 'openlab_default_group_subscription' );

/**
 * Load the bp-ass textdomain.
 *
 * We do this because `load_plugin_textdomain()` in `activitysub_textdomain()` doesn't support custom locations.
 */
function openlab_load_bpass_textdomain() {
	load_textdomain( 'bp-ass', WP_LANG_DIR . '/bp-ass-en_US.mo' );
}
add_action( 'init', 'openlab_load_bpass_textdomain', 11 );

/**
 * Use entire text of comment or blog post when sending BPGES notifications.
 *
 * @param string $content Activity content.
 * @param object $activity Activity object.
 */
function openlab_use_full_text_for_blog_related_bpges_notifications( $content, $activity ) {
	if ( 'groups' !== $activity->component ) {
		return $content;
	}

	// @todo new-style blog comments?
	if ( ! in_array( $activity->type, array( 'new_blog_post', 'new_blog_comment' ) ) ) {
		return $content;
	}

	$group_id = $activity->item_id;
	$blog_id  = openlab_get_site_id_by_group_id( $group_id );

	if ( ! $blog_id ) {
		return $content;
	}

	switch_to_blog( $blog_id );

	if ( 'new_blog_post' === $activity->type ) {
		$post    = get_post( $activity->secondary_item_id );
		$content = empty( $post->post_password ) ? $post->post_content : 'This post is password protected.';
	} elseif ( 'new_blog_comment' === $activity->type ) {
		$comment = get_comment( $activity->secondary_item_id );
		$content = $comment->comment_content;
	}

	restore_current_blog();

	return $content;
}
add_action( 'bp_ass_activity_notification_content', 'openlab_use_full_text_for_blog_related_bpges_notifications', 10, 2 );

// Don't allow BPGES to convert links in HTML email.
add_filter(
	'ass_clean_content',
	function( $content ) {
		remove_filter( 'ass_clean_content', 'ass_convert_links', 6 );
		return $content;
	},
	0
);

/**
 * Respect 'Hidden' site setting when BPGES sends blog-related notifications.
 *
 * @param bool   $send_it
 * @param object $activity
 * @param int    $user_id
 */
function openlab_respect_hidden_site_setting_for_bpges_notifications( $send_it, $activity, $user_id ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	if ( ! in_array( $activity->type, array( 'new_blog_post', 'new_blog_comment' ) ) ) {
		return $send_it;
	}

	$group_id = $activity->item_id;

	$site_id = openlab_get_site_id_by_group_id( $group_id );
	if ( ! $site_id ) {
		return $send_it;
	}

	$site_privacy = get_blog_option( $site_id, 'blog_public' );
	if ( '-3' !== $site_privacy ) {
		return $send_it;
	}

	// Email notifications should only go to site admins.
	if ( ! is_super_admin( $user_id ) ) {
		$send_it = false;
	}

	return $send_it;
}
add_filter( 'bp_ass_send_activity_notification_for_user', 'openlab_respect_hidden_site_setting_for_bpges_notifications', 10, 3 );

/**
 * Force most kinds of content to go to weekly digests.
 */
add_filter(
	'bp_ges_add_to_digest_queue_for_user',
	function( $add, $activity, $user_id, $subscription_type ) {
		if ( 'sum' !== $subscription_type ) {
			return $add;
		}

		if ( $add ) {
			return $add;
		}

		$force = [
			'added_group_document'  => 1,
			'bbp_reply_create'      => 1, // topic creation already whitelisted
			'bp_doc_comment'        => 1,
			'bp_doc_created'        => 1,
			'bp_doc_edited'         => 1,
			'edited_group_document' => 1,
			'new_blog_comment'      => 1,
			'new_blog_post'         => 1,
		];

		if ( isset( $force[ $activity->type ] ) ) {
			$add = true;
		}

		return $add;
	},
	10,
	4
);

/**
 * Change the content type to "text/html" of the comment notification emails sent by WP.
 */
function ol_comment_notification_headers( $message_headers, $comment_id ) {
	$comment = get_comment( $comment_id );

	if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
		return false;
	}

	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$wp_email = 'wordpress@' . preg_replace( '#^www\.#', '', wp_parse_url( network_home_url(), PHP_URL_HOST ) );


	if ( '' === $comment->comment_author ) {
		$from = "From: \"$blogname\" <$wp_email>";
		if ( '' !== $comment->comment_author_email ) {
			$reply_to = "Reply-To: $comment->comment_author_email";
		}
	} else {
		$from = "From: \"$comment->comment_author\" <$wp_email>";
		if ( '' !== $comment->comment_author_email ) {
			$reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
		}
	}

	$message_headers = "$from\n"
	. 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n";

	if ( isset( $reply_to ) ) {
		$message_headers .= $reply_to . "\n";
	}

	return $message_headers;
}
add_filter( 'comment_notification_headers', 'ol_comment_notification_headers', 10, 2);
add_filter( 'comment_moderation_headers', 'ol_comment_notification_headers', 10, 2);

/**
 * Change "<br />|" with "<br />" in the comment notification email content,
 * and use "<br />" for new line, instead of paragraphs due to the spacing between the lines.
 */
function ol_comment_notification_text( $notify_message, $comment_id ) {
	$comment = get_comment( $comment_id );

	if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
		return false;
	}

	$post   = get_post( $comment->comment_post_ID );
	$comment_content = wp_specialchars_decode( $comment->comment_content );

	$comment_author_domain = '';
	if ( WP_Http::is_ip_address( $comment->comment_author_IP ) ) {
		$comment_author_domain = gethostbyaddr( $comment->comment_author_IP );
	}

	switch ( $comment->comment_type ) {
		case 'trackback':
			/* translators: %s: Post title. */
			$notify_message = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "<br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			$notify_message .= __( 'You can see all trackbacks on this post here:' ) . "<br />";
			break;

		case 'pingback':
			/* translators: %s: Post title. */
			$notify_message = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "<br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			$notify_message .= __( 'You can see all pingbacks on this post here:' ) . "<br />";
			break;

		default: // Comments.
			/* translators: %s: Post title. */
			$notify_message = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "<br />";
			/* translators: 1: Comment author's name, 2: Comment author's IP address, 3: Comment author's hostname. */
			$notify_message .= sprintf( __( 'Author: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Comment author email. */
			$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";

			if ( $comment->comment_parent && user_can( $post->post_author, 'edit_comment', $comment->comment_parent ) ) {
				/* translators: Comment moderation. %s: Parent comment edit URL. */
				$notify_message .= sprintf( __( 'In reply to: %s' ), admin_url( "comment.php?action=editcomment&c={$comment->comment_parent}#wpbody-content" ) ) . "<br />";
			}

			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			$notify_message .= __( 'You can see all comments on this post here:' ) . "<br />";
			break;
	}

	$notify_message .= get_permalink( $comment->comment_post_ID ) . "#comments<br /><br />";
	/* translators: %s: Comment URL. */
	$notify_message .= sprintf( __( 'Permalink: %s' ), get_comment_link( $comment ) ) . "<br />";

	if ( user_can( $post->post_author, 'edit_comment', $comment->comment_ID ) ) {
		if ( EMPTY_TRASH_DAYS ) {
			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment->comment_ID}#wpbody-content" ) ) . "<br />";
		} else {
			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment->comment_ID}#wpbody-content" ) ) . "<br />";
		}
		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment->comment_ID}#wpbody-content" ) ) . "<br />";
	}

	// Remove <p> from the message
	$notify_message = str_replace( '<p>', '', $notify_message);
	// Replace </p> with <br />
	$notify_message = str_replace( '</p>', '<br />', $notify_message);

	return $notify_message;
}
add_filter( 'comment_notification_text', 'ol_comment_notification_text', 10, 2);

/**
 * Change "<br />|" with "<br />" in the comment held for moderation notification email content,
 * and use "<br />" for new line, instead of paragraphs due to the spacing between the lines.
 */
function ol_comment_moderation_text( $notify_message, $comment_id ) {
	global $wpdb;
	$comment = get_comment( $comment_id );

	if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
		return false;
	}

	$post    = get_post( $comment->comment_post_ID );

	$comment_author_domain = '';
	if ( WP_Http::is_ip_address( $comment->comment_author_IP ) ) {
		$comment_author_domain = gethostbyaddr( $comment->comment_author_IP );
	}

	$comments_waiting = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'" );
	$comment_content = wp_specialchars_decode( $comment->comment_content );

	switch ( $comment->comment_type ) {
		case 'trackback':
			/* translators: %s: Post title. */
			$notify_message  = sprintf( __( 'A new trackback on the post "%s" is waiting for your approval' ), $post->post_title ) . "<br />";
			$notify_message .= get_permalink( $comment->comment_post_ID ) . "<br /><br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			$notify_message .= __( 'Trackback excerpt: ' ) . "<br />" . $comment_content . "<br /><br />";
			break;

		case 'pingback':
			/* translators: %s: Post title. */
			$notify_message  = sprintf( __( 'A new pingback on the post "%s" is waiting for your approval' ), $post->post_title ) . "<br />";
			$notify_message .= get_permalink( $comment->comment_post_ID ) . "<br /><br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			$notify_message .= __( 'Pingback excerpt: ' ) . "<br />" . $comment_content . "<br /><br />";
			break;

		default: // Comments.
			/* translators: %s: Post title. */
			$notify_message  = sprintf( __( 'A new comment on the post "%s" is waiting for your approval' ), $post->post_title ) . "<br />";
			$notify_message .= get_permalink( $comment->comment_post_ID ) . "<br /><br />";
			/* translators: 1: Comment author's name, 2: Comment author's IP address, 3: Comment author's hostname. */
			$notify_message .= sprintf( __( 'Author: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Comment author email. */
			$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";

			if ( $comment->comment_parent ) {
				/* translators: Comment moderation. %s: Parent comment edit URL. */
				$notify_message .= sprintf( __( 'In reply to: %s' ), admin_url( "comment.php?action=editcomment&c={$comment->comment_parent}#wpbody-content" ) ) . "<br />";
			}

			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			break;
	}

	/* translators: Comment moderation. %s: Comment action URL. */
	$notify_message .= sprintf( __( 'Approve it: %s' ), admin_url( "comment.php?action=approve&c={$comment_id}#wpbody-content" ) ) . "<br />";

	if ( EMPTY_TRASH_DAYS ) {
		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment_id}#wpbody-content" ) ) . "<br />";
	} else {
		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment_id}#wpbody-content" ) ) . "<br />";
	}

	/* translators: Comment moderation. %s: Comment action URL. */
	$notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment_id}#wpbody-content" ) ) . "<br />";

	$notify_message .= sprintf(
		/* translators: Comment moderation. %s: Number of comments awaiting approval. */
		_n(
			'Currently %s comment is waiting for approval. Please visit the moderation panel:',
			'Currently %s comments are waiting for approval. Please visit the moderation panel:',
			$comments_waiting
		),
		number_format_i18n( $comments_waiting )
	) . "<br />";
	$notify_message .= admin_url( 'edit-comments.php?comment_status=moderated#wpbody-content' ) . "<br />";

	// Remove <p> from the message
	$notify_message = str_replace( '<p>', '', $notify_message);
	// Replace </p> with <br />
	$notify_message = str_replace( '</p>', '<br />', $notify_message);

	return $notify_message;
}
add_filter( 'comment_moderation_text', 'ol_comment_moderation_text', 10, 2);
