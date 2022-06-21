<?php

$announcement_id = $args['announcement_id'];
$announcement    = get_post( $announcement_id );

$read_only = ! empty( $args['read_only'] );

// Group ID
$group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );

// Get group by ID
$group = groups_get_group( $group_id );

// Get author info.
$author_name   = bp_core_get_user_displayname( $announcement->post_author );
$author_url    = bp_core_get_user_domain( $announcement->post_author );
$author_avatar = bp_core_fetch_avatar(
	[
		'item_id' => $announcement->post_author,
		'type'    => 'full',
		'width'   => 75,
		'height'  => 75,
		'html'    => true,
		'alt'     => sprintf( __( 'Profile picture of %s', 'buddypress' ), $author_name )
	]
);

$group_name = bp_get_group_name( $group );
$group_url = bp_get_group_permalink( $group );

$top_level_comments = get_comments(
	[
		'post_id'    => $announcement_id,
		'parent__in' => [ 0 ],
	]
);

$can_reply_class = openlab_user_can_reply_to_announcement( bp_loggedin_user_id(), $announcement_id ) ? 'user-can-reply' : '';

$editor_id = 'announcement-' . $announcement_id;

$delete_url = wp_nonce_url( $group_url . 'announcements/?delete-announcement=' . $announcement_id, 'announcement_delete_' . $announcement_id );

$announcement_url = bp_get_group_permalink( $group ) . 'announcements/#announcement-item-' . $announcement_id;

$announcement_content = $announcement->post_content;
if ( $read_only ) {
	$truncated = bp_create_excerpt( $announcement_content, 400, [ 'ending' => '**readmore**' ] );

	if ( $truncated !== $announcement_content ) {
		$announcement_content = str_replace(
			'**readmore**',
			sprintf(
				'&hellip; <a class="announcement-read-more" href="%s">%s</a>',
				esc_url( $announcement_url ),
				'See More'
			),
			$truncated
		);
	}
}

?>

<article class="group-item updateable-item announcement-item <?php echo esc_attr( $can_reply_class ); ?>" id="announcement-item-<?php echo esc_attr( $announcement_id ); ?>" data-announcement-id="<?php echo esc_attr( $announcement_id ); ?>" data-editor-id="<?php echo esc_attr( $editor_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'announcement_' . $editor_id ) ); ?>" data-item-type="announcement">
	<div class="group-item-wrapper">
		<header class="row announcement-header">
			<div class="item-avatar">
				<div class="activity-avatar">
					<a href="<?php echo esc_attr( $author_url ) ?>" title="<?php echo esc_attr( $author_name ) ?>"><?php echo $author_avatar; ?></a>
				</div>
			</div>

			<div>
				<?php if ( $read_only ) : ?>
					<a href="<?php echo esc_url( $announcement_url ); ?>">
				<?php endif; ?>

				<h1 id="title-rendered-<?php echo esc_attr( $editor_id ); ?>" class="announcement-title-rendered"><?php echo esc_html( $announcement->post_title ); ?></h1>

				<?php if ( $read_only ) : ?>
					</a>
				<?php endif; ?>

				<div class="announcement-info">
					<?php printf( 'Posted by: %s', esc_html( $author_name ) ); ?>
					<br />
					<?php printf( 'Posted on: %1$s at %2$s', esc_html( get_the_date( 'F j, Y', $announcement ) ), esc_html( get_the_date( 'g:i a', $announcement ) ) ); ?>
				</div>
			</div>
		</header>

		<div class="row announcement-body">
			<?php echo wp_kses_post( $announcement_content ); ?>
		</div>

		<?php if ( is_user_logged_in() && ! $read_only ) : ?>
			<div class="row announcement-actions">
				<?php if ( openlab_user_can_reply_to_announcement( bp_loggedin_user_id(), $announcement_id ) ) : ?>
					<div class="hide-if-no-js announcement-action">
						<a class="announcement-reply-link" href="">Reply</a>
					</div>
				<?php endif; ?>

				<?php if ( current_user_can( 'edit_post', $announcement_id ) ) : ?>
					<div class="hide-if-no-js announcement-action">
						<a class="announcement-edit-link" href="">Edit</a>
					</div>

					<div class="announcement-action">
						<a class="announcement-delete-link" href="<?php echo esc_url( $delete_url ); ?>">Delete</a>
					</div>
				<?php endif; ?>
			</div>

			<div class="row announcement-reply-container"></div>
		<?php endif; ?>

		<?php if ( ! $read_only ) : ?>
			<div class="announcement-replies">
				<?php foreach ( $top_level_comments as $top_level_comment ) : ?>
					<?php bp_get_template_part( 'groups/single/announcements/reply', '', [ 'reply_id' => $top_level_comment->comment_ID ] ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
