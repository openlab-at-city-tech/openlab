<?php
/**
 * The template for displaying Author Archive pages.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<?php
			/* Queue the first post, that way we know who
			 * the author is when we try to get their name,
			 * URL, description, avatar, etc.
			 *
			 * We reset this later so we can run the loop
			 * properly with a call to rewind_posts().
			 */
			if ( have_posts() )
				the_post();
		?>

		<h1 class="page-title author">
			<?php
				printf( __( 'Author Archives: %s', 'pilcrow' ),
					sprintf(
						'<span class="vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="me">%3$s</a></span>',
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						esc_attr( get_the_author() ),
						get_the_author()
					)
				);
			?>
		</h1>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
		<div id="entry-author-info">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), apply_filters( 'pilcrow_author_bio_avatar_size', 60 ) ); ?>
			</div><!-- #author-avatar -->
			<div id="author-description">
				<h2><?php printf( __( 'About %s', 'pilcrow' ), get_the_author() ); ?></h2>
				<?php the_author_meta( 'description' ); ?>
			</div><!-- #author-description	-->
		</div><!-- #entry-author-info -->
		<?php endif; ?>

		<?php
			/* Since we called the_post() above, we need to
			 * rewind the loop back to the beginning that way
			 * we can run the loop properly, in full.
			 */
			rewind_posts();

			/* Run the loop for the author archive page to output the authors posts
			 * If you want to overload this in a child theme then include a file
			 * called loop-author.php and that will be used instead.
			 */
			get_template_part( 'loop', 'author' );
		?>

	</div><!-- #content -->
</div><!-- #content-container -->

<?php
get_sidebar();
get_footer();
