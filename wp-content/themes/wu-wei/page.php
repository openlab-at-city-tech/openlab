<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<div class="post-info">

				<h1><?php the_title(); ?></h1>

				<div class="clearboth"><!-- --></div>

				<?php edit_post_link( __( 'Edit this entry', 'wu-wei' ), '<p>', '</p>' ); ?>
			</div>

			<div class="post-content">
					<?php the_content(); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>

			<div class="clearboth"><!-- --></div>

			<?php if ( comments_open() ) { comments_template(); } ?>

		</div>

		<?php endwhile; endif; ?>



<?php get_sidebar(); ?>

<?php get_footer(); ?>