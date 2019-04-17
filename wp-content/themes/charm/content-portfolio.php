<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

		<div class="post-content">
			<?php the_content(); ?>
		</div>

	<?php else : ?>

		<div class="project-thumbnail-image">
			<?php the_post_thumbnail( 'project' ); ?>
		</div>

		<div class="project-thumbnail-content">
			<?php the_title( '<h4 class="project-thumbnail-title">', '</h4>' ); ?>
			<?php the_terms( $post->ID, 'project-category', '<div class="project-thumbnail-category">', ', ', '</div>' ); ?>
		</div>

		<a class="project-thumbnail-link" href="<?php the_permalink(); ?>"></a>

	<?php endif; ?>

</article>