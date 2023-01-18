<div <?php post_class(); ?>>
	<article>
		<?php do_action( 'post_before' ); ?>
		<?php ct_period_featured_image(); ?>
		<div class="post-container">
			<div class='post-header'>
				<h1 class='post-title'><?php the_title(); ?></h1>
				<?php get_template_part( 'content/post-byline' ); ?>
			</div>
			<div class="post-content">
				<?php ct_period_output_last_updated_date(); ?>
				<?php the_content(); ?>
				<?php wp_link_pages( array(
					'before' => '<p class="singular-pagination">' . esc_html__( 'Pages:', 'period' ),
					'after'  => '</p>',
				) ); ?>
				<?php do_action( 'post_after' ); ?>
			</div>
			<div class="post-meta">
				<?php get_template_part( 'content/post-categories' ); ?>
				<?php get_template_part( 'content/post-tags' ); ?>
				<?php get_template_part( 'content/post-nav' ); ?>
			</div>
		</div>
	</article>
	<div class="comments-container">
		<?php comments_template(); ?>
	</div>
</div>