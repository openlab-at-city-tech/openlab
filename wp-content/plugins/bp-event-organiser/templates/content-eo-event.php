
	<h4><?php _e( 'Event Description', 'bp-event-organiser' ); ?></h4>

	<?php the_content(); ?>

	<?php if ( false === post_password_required() )  : ?>
		<?php
			if ( false === bpeo_has_thumbnail_shown() ) {
				the_post_thumbnail( 'medium' );
			}
		 ?>
		<?php eo_get_template_part( 'event-meta', 'event-single' ); ?>
	<?php endif; ?>

	<?php bpeo_the_single_event_action_links(); ?>
