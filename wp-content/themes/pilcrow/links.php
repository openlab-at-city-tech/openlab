<?php
/**
 * Template Name: Links
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<div class="entry entry-content">
				<ul>
					<?php wp_list_bookmarks(); ?>
				</ul>
				<?php edit_post_link( __( 'Edit', 'pilcrow' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-content -->
		</div><!-- #post-## -->

		<?php comments_template(); ?>

		<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->
</div><!-- #container -->

<?php
get_sidebar();
get_footer();
