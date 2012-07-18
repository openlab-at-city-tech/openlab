<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
			<div class="clearboth"><!-- --></div>
		</div>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<div class="post-info">

				<h1><?php the_title(); ?></h1>

				<div class="timestamp"><?php the_time( get_option( 'date_format' ) ); ?> <!-- by <?php the_author() ?> --> //</div> <?php if ( comments_open() ) : ?><div class="comment-bubble"><a href="#comments"><?php comments_number('0', '1', '%'); ?></a></div><?php endif; ?>
				<div class="clearboth"><!-- --></div>

				<?php edit_post_link( __( 'Edit this entry', 'wu-wei' ), '<p>', '</p>' ); ?>

			</div>


			<div class="post-content">
				<?php the_content( __( 'Read the rest of this entry &raquo;', 'wu-wei' ) ); ?>
				
				<?php wp_link_pages( array('before' => '<p><strong>' . __( 'Pages:', 'wu-wei' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>

			<div class="clearboth"><!-- --></div>

			<?php the_tags( '<div class="post-meta-data">' . __( 'Tags', 'wu-wei' ) . ' <span>', ', ', '</span></div>' ); ?>

			<div class="post-meta-data"><?php _e( 'Categories', 'wu-wei' ); ?> <span><?php the_category(', ') ?></span></div>

		</div>

	<?php comments_template(); ?>

	<!-- <?php trackback_rdf(); ?> -->

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
