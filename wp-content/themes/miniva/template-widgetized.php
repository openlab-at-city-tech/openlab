<?php
/**
 * The template for displaying a widgetized page.
 *
 * Template Name: Widgetized Page
 *
 * @package Miniva
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( ! get_theme_mod( 'widgetized_page_hide_title_content', false ) ) {
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content', 'page' );

			endwhile; // End of the loop.
		}
		?>

		<?php if ( is_active_sidebar( 'widgetized-page' ) ) : ?>
			<?php dynamic_sidebar( 'widgetized-page' ); ?>
		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
