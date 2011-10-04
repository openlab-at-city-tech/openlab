<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<div class="post-info">

				<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

				<div class="timestamp"><?php the_time('F j, Y'); ?> <!-- by <?php the_author() ?> --> //</div> <div class="comment-bubble"><a href="<?php comments_link(); ?>"><?php comments_number('0', '1', '%'); ?></a></div>
				<div class="clearboth"><!-- --></div>

				<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
			</div>

			<div class="post-content">
					<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>

			<div class="clearboth"><!-- --></div>

			<?php comments_template(); ?>

		</div>

		<?php endwhile; endif; ?>



<?php get_sidebar(); ?>

<?php get_footer(); ?>