<?php
/**
 * Template Name: Full Width Page
 *
 * This is the template for display full-width pages,
 * ie., that displays all pages in one column with
 * NO sidebar(s). For the default styling of pages in
 * this theme, see page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package gillian
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
