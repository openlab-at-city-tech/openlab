        <?php if(bp_current_component() !== 'groups'): ?>
            <h2 class="event-title"><?php the_title() ?></h2>
        <?php endif; ?>

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
