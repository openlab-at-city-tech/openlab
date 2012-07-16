<?php get_header(); ?>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

				<div class="post-info">

					<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'wu-wei' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a></h1>

					<div class="timestamp"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'wu-wei' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a> <!-- by <?php the_author(); ?> --> //</div> <?php if ( comments_open() ) : ?><div class="comment-bubble"><?php comments_popup_link( '0', '1', '%' ); ?></div><?php endif; ?>
					<div class="clearboth"><!-- --></div>

					<?php edit_post_link( __( 'Edit this entry', 'wu-wei' ), '<p>', '</p>' ); ?>

				</div>

				<div class="post-content">
					<?php the_content( __( 'Read the rest of this entry &raquo;', 'wu-wei' ) ); ?>

					<?php wp_link_pages( array('before' => '<p><strong>' . __( 'Pages:', 'wu-wei' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div>

				<div class="clearboth"><!-- --></div>

				<?php the_tags( '<div class="post-meta-data">' . __( 'Tags', 'wu-wei' ) . ' <span>', ', ', '</span></div>' ); ?>

				<div class="post-meta-data"><?php _e( 'Categories', 'wu-wei' ); ?> <span><?php the_category(', '); ?></span></div>

			</div>

		<?php endwhile; ?>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Entries', 'wu-wei' ) ); ?></div>
				<div class="alignright"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'wu-wei' ) ); ?></div>
				<div class="clearboth"><!-- --></div>
			</div>

	<?php else : ?>

		<div class="post">

			<div class="post-info">

				<h1><?php _e( 'Not Found', 'wu-wei' ); ?></h1>

			</div>

			<div class="post-content">
				<p><?php _e( 'Sorry, but you are looking for something that isn&rsquo;t here.', 'wu-wei'  ); ?></p>

				<?php get_search_form(); ?>
			</div>

			<div class="clearboth"><!-- --></div>

		</div>

	<?php endif; ?>

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
