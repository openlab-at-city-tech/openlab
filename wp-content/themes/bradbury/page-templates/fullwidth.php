<?php
/**
 * Template Name: Full Width Page
 */

get_header();
?>

<main id="site-main">

	<?php
	while (have_posts()) : the_post();
	?>

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php
			// Function to display Breadcrumbs
			academiathemes_helper_display_breadcrumbs($post);

			academiathemes_helper_display_title($post);
			academiathemes_helper_display_content($post);
			academiathemes_helper_display_comments($post);

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

	<?php
	endwhile;
	?>

</main><!-- #site-main -->
	
<?php get_footer(); ?>