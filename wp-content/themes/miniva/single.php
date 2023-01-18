<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Miniva
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			if ( function_exists( 'jetpack_author_bio' ) ) {
				jetpack_author_bio();
			}

			the_post_navigation(
				array(
					'prev_text' => '<span>' . __( 'Previous Post', 'miniva' ) . '</span>%title',
					'next_text' => '<span>' . __( 'Next Post', 'miniva' ) . '</span>%title',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
