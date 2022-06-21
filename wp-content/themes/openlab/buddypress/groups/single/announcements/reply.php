<?php

$reply_id = $args['reply_id'];
$reply    = get_comment( $reply_id );

if ( ! $reply ) {
	return;
}

$announcement_id = $reply->comment_post_ID;
$announcement    = get_post( $announcement_id );

if ( ! $announcement ) {
	return;
}

// Group ID
$group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );

// Get group by ID
$group = groups_get_group( $group_id );

// Get author info.
$author_name   = bp_core_get_user_displayname( $reply->user_id );
$author_url    = bp_core_get_user_domain( $reply->user_id );
$author_avatar = bp_core_fetch_avatar(
	[
		'item_id' => $reply->user_id,
		'type'    => 'full',
		'width'   => 75,
		'height'  => 75,
		'html'    => true,
		'alt'     => sprintf( __( 'Profile picture of %s', 'buddypress' ), $author_name )
	]
);

$group_name = bp_get_group_name( $group );
$group_url = bp_get_group_permalink( $group );

// @todo This probably will not stay.
$reply_title_prefix = ! empty( $reply->comment_parent ) ? 'RE: RE: ' : 'RE: ';
$reply_title        = $reply_title_prefix . $announcement->post_title;

$reply_replies = get_comments(
	[
		'parent' => $reply_id,
	]
);

$user_can_reply  = openlab_user_can_reply_to_reply( bp_loggedin_user_id(), $reply_id );
$can_reply_class = $user_can_reply ? 'user-can-reply' : '';

$editor_id = 'reply-' . $reply_id;

$delete_url = wp_nonce_url( $group_url . 'announcements/?delete-announcement-reply=' . $reply_id, 'announcement_delete_' . $reply_id );

?>

<div class="group-item updateable-item announcement-reply-item <?php echo esc_attr( $can_reply_class ); ?>" id="announcement-reply-item-<?php echo esc_attr( $reply_id ); ?>" data-reply-id="<?php echo esc_attr( $reply_id ); ?>" data-announcement-id="<?php echo esc_attr( $announcement_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'announcement_' . $editor_id ) ); ?>" data-item-type="reply" data-editor-id="<?php echo esc_attr( $editor_id ); ?>">
	<div class="group-item-wrapper">
		<header class="row announcement-header">
			<div class="item-avatar">
				<div class="activity-avatar">
					<a href="<?php echo esc_attr( $author_url ) ?>" title="<?php echo esc_attr( $author_name ) ?>"><?php echo $author_avatar; ?></a>
				</div>
			</div>

			<div>
				<h2 class="announcement-title-rendered"><?php echo esc_html( $reply_title ); ?></h2>
				<div class="announcement-info">
					<?php printf( 'Posted by: %s', esc_html( $author_name ) ); ?>
					<br />
					<?php printf( 'Posted on: %1$s at %2$s', esc_html( gmdate( 'F j, Y', strtotime( $reply->comment_date ) ) ), esc_html( gmdate( 'g:i a', strtotime( $reply->comment_date ) ) ) ); ?>
				</div>
			</div>
		</header>

		<div class="row announcement-body">
			<div class="item col-xs-21">
				<?php echo wp_kses_post( $reply->comment_content ); ?>
			</div>
		</div>

		<?php if ( is_user_logged_in() ) : ?>
			<div class="row announcement-actions">
				<?php if ( $user_can_reply ) : ?>
					<div class="hide-if-no-js announcement-action">
						<a class="announcement-reply-link" href="">Reply</a>
					</div>
				<?php endif; ?>

				<?php if ( current_user_can( 'edit_comment', $reply_id ) ) : ?>
					<div class="announcement-action">
						<a class="announcement-edit-link" href="">Edit</a>
					</div>

					<div class="announcement-action">
						<a class="announcement-delete-link" href="<?php echo esc_url( $delete_url ); ?>">Delete</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $user_can_reply ) : ?>
				<div class="row announcement-reply-container"></div>
			<?php endif; ?>

		<?php endif; ?>

		<div class="row announcement-reply-replies">
			<?php foreach ( $reply_replies as $reply_reply ) : ?>
				<?php bp_get_template_part( 'groups/single/announcements/reply', '', [ 'reply_id' => $reply_reply->comment_ID ] ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>
