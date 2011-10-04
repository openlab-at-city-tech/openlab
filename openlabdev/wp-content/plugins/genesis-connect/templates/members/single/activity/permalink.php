<?php
get_header();
genesis_before_content_sidebar_wrap();
gconnect_before_content();
?>
<div class="activity no-ajax">
	<?php if ( bp_has_activities( 'display_comments=threaded&include=' . bp_current_action() ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">
		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php gconnect_locate_template( array( 'activity/entry.php' ), true ) ?>

		<?php endwhile; ?>
		</ul>

	<?php endif; ?>
</div>
<?php
gconnect_after_content();
genesis_after_content_sidebar_wrap();
get_footer();
?>
