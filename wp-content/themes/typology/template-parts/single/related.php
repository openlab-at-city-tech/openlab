<?php if( typology_get_option( 'single_related' ) ): ?>

	<?php $related_query = typology_get_related_posts(); ?>

	<?php if( $related_query->have_posts() ) : ?>

		<div class="typology-section typology-section-related">

			<?php typology_section_heading( array( 'title' => __typology('related') ) ); ?>

			<?php $related_layout = typology_get_option( 'related_layout'); ?>

			<div class="section-content section-content-<?php echo esc_attr( $related_layout ); ?>">

				<div class="typology-posts">

					<?php while( $related_query->have_posts() ) : $related_query->the_post(); ?>
						<?php get_template_part('template-parts/layouts/content-' . $related_layout ); ?>
					<?php endwhile; ?>
				
				</div>
			</div>
		
		</div>

	<?php endif; ?>

	<?php wp_reset_postdata(); ?>

<?php endif; ?>