<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
			<div class="clearboth"><!-- --></div>
		</div>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<div class="post-info">

				<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

				<div class="timestamp"><?php the_time('F j, Y'); ?> <!-- by <?php the_author() ?> --> //</div> <div class="comment-bubble"><a href="#comments"><?php comments_number('0', '1', '%'); ?></a></div>
				<div class="clearboth"><!-- --></div>

				<p><?php edit_post_link('Edit this entry', '', ''); ?></p>

			</div>


			<div class="post-content">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>

			<div class="clearboth"><!-- --></div>

				<div class="post-meta-data">Tags <span><?php the_tags('', ', ', ''); ?></span></div>

				<div class="post-meta-data">Categories <span><?php the_category(', ') ?></span></div>

		</div>

	<?php comments_template(); ?>

	<!-- <?php trackback_rdf(); ?> -->

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
