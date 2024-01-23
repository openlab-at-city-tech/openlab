<?php 

if ( 'posts' == get_option( 'show_on_front' ) ) {

	get_template_part( 'index' );

} else {

get_header(); ?>

<main id="site-main">

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php
			// Function to display Breadcrumbs
			academiathemes_helper_display_breadcrumbs();

			// Function to display the START of the content column markup
			academiathemes_helper_display_page_content_wrapper_start();

			the_archive_title( '<h1 class="page-title archives-title">', '</h1>' );
			the_archive_description( '<div class="archives-content clearfix">', '</div>' );

			echo '<hr />';
			get_template_part('loop');

			// Function to display the END of the content column markup
			academiathemes_helper_display_page_content_wrapper_end();

			// Function to display the SIDEBAR (if not hidden)
			academiathemes_helper_display_page_sidebar_column();

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

</main><!-- #site-main -->
	
<?php get_footer();

}
?>