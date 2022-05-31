<?php if ( openlab_user_can_post_announcements() ) : ?>
	<?php bp_get_template_part( 'groups/single/announcements/post-form' ); ?>
<?php endif; ?>

<?php
$announcements = get_posts(
	[
		'post_type'   => 'openlab_announcement',
		'post_status' => 'publish',
		'meta_query'  => [
			[
				'key'   => 'openlab_announcement_group_id',
				'value' => bp_get_current_group_id(),
			]
		],
		// @todo pagination
	]
);
?>

<?php if ( $announcements ) : ?>

	<div class="item-list announcement-list clearfix">
		<?php foreach ( $announcements as $announcement ) : ?>
			<?php bp_get_template_part( 'groups/single/announcements/entry', '', [ 'announcement_id' => $announcement->ID ] ); ?>
		<?php endforeach; ?>
	</div>

	<div id="pag-top" class="pagination clearfix">
		<div class="pagination-links" id="member-dir-pag-top">
			pagination links
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p class="bold">No announcements.</p>
	</div>

<?php endif; ?>
