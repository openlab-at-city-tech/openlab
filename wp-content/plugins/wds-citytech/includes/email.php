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
 * Filters the subject line of comment notification emails.
 */
add_filter(
	'comment_notification_subject',
	function( $subject, $comment_id ) {
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		if ( ! $group_id ) {
			return $subject;
		}

		$group = groups_get_group( $group_id );

		$comment = get_comment( $comment_id );

		if ( $comment->user_id ) {
			$comment_author = bp_core_get_user_displayname( $comment->user_id );
		} else {
			$comment_author = $comment->comment_author;
		}

		$post = get_post( $comment->comment_post_ID );

		switch ( $comment->comment_type ) {
			case 'trackback' :
				$base = 'A new trackback from %s on %s in %s';
			break;

			case 'pingback' :
				$base = 'A new pingback from %s on %s in %s';
			break;

			case 'comment' :
			default :
				$base = 'A new comment from %s on %s in %s';
			break;
		}

		return sprintf(
			$base,
			$comment_author,
			$post->post_title,
			$group->name
		);
	},
	10,
	2
);

/**
 * Filters the subject line of comment moderation emails.
 */
add_filter(
	'comment_moderation_subject',
	function( $subject, $comment_id ) {
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		if ( ! $group_id ) {
			return $subject;
		}

		$group = groups_get_group( $group_id );

		$comment = get_comment( $comment_id );

		$post = get_post( $comment->comment_post_ID );

		switch ( $comment->comment_type ) {
			case 'trackback' :
				$base = 'Please moderate a trackback on %s in %s';
			break;

			case 'pingback' :
				$base = 'Please moderate a pingback on %s in %s';
			break;

			case 'comment' :
			default :
				$base = 'Please moderate a comment on %s in %s';
			break;
		}

		return sprintf(
			$base,
			$post->post_title,
			$group->name
		);
	},
	10,
	2
);
