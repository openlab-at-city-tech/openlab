<?php gconnect_get_header(); ?>
<div class="activity no-ajax" role="main">
	<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>
		<ul id="activity-stream" class="activity-list item-list">
<?php 
		while ( bp_activities() ) : 
			bp_the_activity();
			gconnect_locate_template( array( 'activity/entry.php' ), true );
		endwhile; 
?>
		</ul>
	<?php endif; ?>
</div>
<?php
gconnect_get_footer();