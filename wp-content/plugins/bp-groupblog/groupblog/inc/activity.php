<div class="activity single-group">

	<h3><?php _e( 'Blog Activity', 'bp-groupblog' ) ?></h3>

	<?php if ( bp_has_activities( 'per_page=5&object=blogs&count_total=1&primary_id=' . get_groupblog_blog_id ( bp_get_group_id() ) ) ) : ?>

		<div class="pagination">
			<div class="pag-count"><?php bp_activity_pagination_count() ?></div>
			<div class="pagination-links"><?php bp_activity_pagination_links() ?></div>
		</div>

		<?php if ( empty( $_POST['page'] ) ) : ?>
			<ul id="activity-stream" class="activity-list item-list">
		<?php endif; ?>

		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php bp_get_template_part( 'activity/entry' ); ?>

		<?php endwhile; ?>

		<?php if ( empty( $_POST['page'] ) ) : ?>
			</ul>
		<?php endif; ?>

	<?php else : ?>

		<div id="message" class="info">
			<p><?php _e( 'Sorry, there was no blog activity found.', 'bp-groupblog' ) ?></p>
		</div>

	<?php endif; ?>

</div><!-- .activity -->
