<?php get_header(); ?>

<main id="site-main">

<?php if ( is_front_page() && !is_paged() ) { ?>
	<?php if ( is_active_sidebar('homepage-content-widgets') ) { ?>
	<div id="site-homepage-widgets">
	
		<?php if ( is_active_sidebar('homepage-content-widgets') ) { dynamic_sidebar( 'homepage-content-widgets' ); } ?>

	</div><!-- #site-homepage-widgets --><?php }
}
?>

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php
			// Function to display the START of the content column markup
			academiathemes_helper_display_page_content_wrapper_start(); ?>

			<?php 
			if ( have_posts() ) { 
				$i = 0; 
			
				if ( is_home() && ! is_front_page() ) { ?>
				<h1 class="page-title archives-title"><?php single_post_title(); ?></h1>
				<?php } ?>

				<?php if ( is_home() ) { ?><p class="page-title archives-title"><?php esc_html_e('Recent Posts','bradbury'); ?></p><?php } ?>

				<hr />

				<?php get_template_part('loop');

			}

			// Function to display the END of the content column markup
			academiathemes_helper_display_page_content_wrapper_end();

			// Function to display the SIDEBAR (if not hidden)
			academiathemes_helper_display_page_sidebar_column();

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

</main><!-- #site-main -->

<?php get_footer(); ?>