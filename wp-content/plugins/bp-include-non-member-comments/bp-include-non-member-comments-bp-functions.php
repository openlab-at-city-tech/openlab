<?php

function bp_blogs_record_nonmember_comment_approved( $comment_id, $comment_status ) {
	if ( $comment_status === 'approve' )
		bp_blogs_record_nonmember_comment( $comment_id, 1 );
}


function bp_blogs_record_nonmember_comment( $comment_id, $is_approved ) {
	global $wpdb, $bp;

	if ( !$is_approved )
		return false;

	$comment = get_comment($comment_id);

	if ( empty( $comment ) ) {
		return;
	}

	/* Thanks, Andrius! */
	if ( $comment->comment_approved == 'spam' )
		return false;

	if ( email_exists( $comment->comment_author_email ) )
		return false;

	$comment->post = get_post( $comment->comment_post_ID );

	/* If this is a password protected post, don't record the comment */
	if ( !empty( $post->post_password ) )
		return false;

	if ( (int)get_blog_option( $comment->blog_id, 'blog_public' ) || ! is_multisite() ) {
		/* Record in activity streams */
		$comment_link = get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id;

		$activity_action = sprintf( __( '%s commented on the blog post %s', 'buddypress' ), '<a href="' . $comment->comment_author_url . '">' . $comment->comment_author . '</a>', '<a href="' . $comment_link . '">' . $comment->post->post_title . '</a>' );

		$activity_content = $comment->comment_content;

		/* Record this in activity streams */
		bp_blogs_record_activity( array(
			'user_id' => false,
			'action' => apply_filters( 'bp_blogs_activity_new_comment_action', $activity_action, $comment, $comment, $comment_link ),
			'content' => apply_filters( 'bp_blogs_activity_new_comment_content', $activity_content, $comment, $comment, $comment_link ),
			'primary_link' => apply_filters( 'bp_blogs_activity_new_comment_primary_link', $comment_link, $comment, $comment ),
			'type' => 'new_blog_comment',
			'item_id' => $wpdb->blogid,
			'secondary_item_id' => $comment_id,
			'recorded_time' => $comment->comment_date_gmt
		) );
	}

	return $comment;
}

/* For BP < 1.2 */
function bp_blogs_record_nonmember_comment_old( $comment_id, $is_approved ) {
	global $wpdb, $bp;

	if ( !$is_approved )
		return false;

	$comment = get_comment($comment_id);
	$comment->post = get_post( $comment->comment_post_ID );

	/* If this is a password protected post, don't record the comment */
	if ( !empty( $post->post_password ) )
		return false;

	$recorded_comment = new BP_Blogs_Comment;
	$recorded_comment->user_id = $user_id;
	$recorded_comment->blog_id = $wpdb->blogid;
	$recorded_comment->comment_id = $comment_id;
	$recorded_comment->comment_post_id = $comment->comment_post_ID;
	$recorded_comment->date_created = strtotime( $comment->comment_date );

	$recorded_commment_id = $recorded_comment->save();

	bp_blogs_update_blogmeta( $recorded_comment->blog_id, 'last_activity', time() );

	if ( (int)get_blog_option( $recorded_comment->blog_id, 'blog_public' ) ) {
		/* Record in activity streams */
		$comment_link = bp_post_get_permalink( $comment->post, $recorded_comment->blog_id );
		$activity_content = sprintf( __( '%s commented on the blog post %s', 'buddypress' ), '<a href="' . $comment->comment_author_url . '">' . $comment->comment_author . '</a>', '<a href="' . $comment_link . '#comment-' . $comment->comment_ID . '">' . $comment->post->post_title . '</a>' );
		$activity_content .= '<blockquote>' . bp_create_excerpt( $comment->comment_content ) . '</blockquote>';

		/* Record this in activity streams */
		bp_blogs_record_activity( array(
			'user_id' => $recorded_comment->user_id,
			'content' => apply_filters( 'bp_blogs_activity_new_comment', $activity_content, comment, recorded_comment, $comment_link ),
			'primary_link' => apply_filters( 'bp_blogs_activity_new_comment_primary_link', $comment_link, comment, recorded_comment ),
			'component_action' => 'new_blog_comment',
			'item_id' => $comment_id,
			'secondary_item_id' => $recorded_comment->blog_id,
			'recorded_time' =>  $recorded_comment->date_created
		) );
	}

	return $recorded_comment;
}



function bp_nonmember_comment_content( $content ) {
	global $bp;

	if ( $bp->loggedin_user->id != 0 )
		return $content;

	/* Todo: Add patch to core that makes these buttons not appear for user_id=0 */
	$content = preg_replace( "|(View</a>).*?<a href=.+?>Delete</a></span>|", '$1</span>', $content ); // for bp-default 1.2+
	$content = preg_replace( "|<span class=\"activity-delete-link.+?</span>|", '', $content ); // for bp-classic

	return $content;
}
?>
