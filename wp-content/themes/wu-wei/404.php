<?php get_header(); ?>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<div class="post-info">

				<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

				<div class="timestamp"><?php the_time('F j, y') ?> <!-- by <?php the_author() ?> --> //</div> <div class="comment-bubble"><?php comments_popup_link('0', '1', '%'); ?></div>
				<div class="clearboth"><!-- --></div>

				<p><?php edit_post_link('Edit this entry', '', ''); ?></p>

			</div>

			<div class="post-content">
				<?php the_content('Read the rest of this entry &raquo;'); ?>
			</div>

			<div class="clearboth"><!-- --></div>

			</div>

		<?php endwhile; ?>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
				<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
				<div class="clearboth"><!-- --></div>
			</div>

	<?php else : ?>

		<div class="post">

			<div class="post-info">

				<h1>404 Not Found</h1>

			</div>

			<div class="post-content">
				<p>Oops! This page does not exist. Maybe you can try searching for it again.</p>

				<?php get_search_form(); ?>
			</div>

			<div class="clearboth"><!-- --></div>

		</div>

	<?php endif; ?>

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
