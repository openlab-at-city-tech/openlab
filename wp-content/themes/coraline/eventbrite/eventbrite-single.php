<?php
/**
 * The Template for displaying all single Eventbrite events.
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">
		<?php
			// Get our event based on the ID passed by query variable.
			$event = new Eventbrite_Query( array( 'p' => get_query_var( 'eventbrite_id' ) ) );

			if ( $event->have_posts() ) :
				while ( $event->have_posts() ) : $event->the_post(); ?>

					<div id="event-<?php the_ID(); ?>" <?php post_class(); ?>>
						<span class="entry-format"><?php esc_html_e( 'Event', 'coraline' ); ?></span>

						<h1 class="entry-title"><?php the_title(); ?></h1>

						<div class="entry-meta">
							<?php eventbrite_event_meta(); ?>
						</div><!-- .entry-meta -->

						<div class="entry-content">
							<?php the_content(); ?>

							<?php eventbrite_ticket_form_widget(); ?>
						</div><!-- .entry-content -->

						<div class="entry-info">
							<?php eventbrite_edit_post_link( __( 'Edit', 'eventbrite_api' ), '<span class="edit-link">', '</span>' ); ?>
						</div><!-- .entry-info -->
					</div><!-- #post-## -->

				<?php endwhile;

			else :
				// If no content, include the "No posts found" template.
				get_template_part( 'content', 'none' );

			endif;

			// Return $post to its rightful owner.
			wp_reset_postdata();
		?>
	</div><!-- #content -->
</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
