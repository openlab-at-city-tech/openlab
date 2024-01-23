<?php get_header(); ?>

<main id="site-main">

	<?php
	while (have_posts()) : the_post();
	?>

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php
			// Function to display Breadcrumbs
			academiathemes_helper_display_breadcrumbs();

			// Function to display the START of the content column markup
			academiathemes_helper_display_page_content_wrapper_start();

				academiathemes_helper_display_postmeta($post);
				academiathemes_helper_display_title($post);
				academiathemes_helper_display_content($post);
				academiathemes_helper_display_tags($post);
				academiathemes_helper_display_comments($post);

			// Function to display the END of the content column markup
			academiathemes_helper_display_page_content_wrapper_end();

			// Function to display the SIDEBAR (if not hidden)
			academiathemes_helper_display_page_sidebar_column();

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

	<?php
	endwhile;
	?>

</main><!-- #site-main -->
	
<?php get_footer(); ?>