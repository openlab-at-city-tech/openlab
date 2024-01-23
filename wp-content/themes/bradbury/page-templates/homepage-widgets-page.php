<?php
/**
 * Template Name: Homepage: Widgets + Page Content
 */

get_header();

?>
<main id="site-main">
<?php
if ( is_front_page() && ( is_active_sidebar('homepage-content-widgets') || is_active_sidebar('homepage-welcome-widgets-left') || is_active_sidebar('homepage-welcome-widgets-right') ) ) { ?>
	<div id="site-homepage-widgets">
	
		<?php if ( is_active_sidebar('homepage-content-widgets') ) { dynamic_sidebar( 'homepage-content-widgets' ); } ?>

		<?php if ( is_active_sidebar('homepage-welcome-widgets-left') || is_active_sidebar('homepage-welcome-widgets-right') ) { ?>
		<div id="site-home-welcome" class="site-section">
			<div class="site-section-wrapper clearfix">

				<div class="site-widget-columns site-widget-columns-2 clearfix">

					<div class="site-widget-column site-widget-column-1">

						<div class="site-column-wrapper clearfix">

							<?php dynamic_sidebar( 'homepage-welcome-widgets-left' ); ?>

						</div><!-- .site-column-wrapper .clearfix -->

					</div><!-- .site-widget-column .site-widget-column-1 --><!-- ws fix
					--><div class="site-widget-column site-widget-column-2">

						<div class="site-column-wrapper clearfix">

							<?php dynamic_sidebar( 'homepage-welcome-widgets-right' ); ?>

						</div><!-- .site-column-wrapper .clearfix -->

					</div><!-- .site-widget-column .site-widget-column-2 -->

				</div><!-- .site-widget-columns .site-widget-columns-2 .clearfix -->

			</div><!-- .site-section-wrapper .site-section-wrapper-main -->
		</div><!-- #site-home-welcome .site-section --><?php } ?>

	</div><!-- #site-homepage-widgets -->

<?php } ?>

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php

			// Function to display the START of the content column markup
			academiathemes_helper_display_page_content_wrapper_start();

				academiathemes_helper_display_title($post);
				academiathemes_helper_display_content($post);
				academiathemes_helper_display_comments($post);

			// Function to display the END of the content column markup
			academiathemes_helper_display_page_content_wrapper_end();

			// Function to display the SIDEBAR (if not hidden)
			academiathemes_helper_display_page_sidebar_column();

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

</main><!-- #site-main -->

<?php get_footer(); ?>