<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Flawless Blog
 */

get_header();
?>

<main id="primary" class="site-main">

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="archive-description">', '</div>' );
			?>
		</header><!-- .page-header -->
		<?php
		$breadcrumb_enable = get_theme_mod( 'flawless_blog_breadcrumb_enable', true );
		if ( $breadcrumb_enable ) :
			?>
				<div id="breadcrumb-list">
				<?php
				echo flawless_blog_breadcrumb(
					array(
						'show_on_front' => false,
						'show_browse'   => false,
					)
				);
				?>
				  
				</div><!-- #breadcrumb-list -->
			<?php endif; ?>

			<div class="theme-archive-layout list-layout list-style-1">

				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					/*
					* Include the Post-Type-specific template for the content.
					* If you want to override this in a child theme, then include a file
					* called content-___.php (where ___ is the Post Type name) and that will be used instead.
					*/

					get_template_part( 'template-parts/content', get_post_type() );

				endwhile;
				?>
			</div>
			<?php

			do_action( 'flawless_blog_posts_pagination' );

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

</main><!-- #main -->

<?php

if ( flawless_blog_is_sidebar_enabled() ) {
	get_sidebar();
}

get_footer();
