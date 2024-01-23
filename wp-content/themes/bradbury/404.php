<?php get_header(); ?>

<main id="site-main">

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php
			// Function to display Breadcrumbs
			academiathemes_helper_display_breadcrumbs();

			// Function to display the START of the content column markup
			academiathemes_helper_display_page_content_wrapper_start(); ?>

			<h1 class="page-title"><?php esc_html_e( 'Page not found', 'bradbury' ); ?></h1>

			<div class="archives-content clearfix"><p><?php esc_html_e( 'Apologies, but the requested page cannot be found. Perhaps searching will help find a related page.', 'bradbury' ); ?></p></div>
			<hr />
			<?php get_search_form(); ?>

			<hr />
			<div class="entry-content clearfix">
			
				<h3><?php esc_html_e( 'Browse Categories', 'bradbury' ); ?></h3>
				<ul>
					<?php wp_list_categories('title_li=&hierarchical=0&show_count=1'); ?>	
				</ul>

				<hr>

				<h3><?php esc_html_e( 'Monthly Archives', 'bradbury' ); ?></h3>
				<ul>
					<?php wp_get_archives('type=monthly&show_post_count=1'); ?>	
				</ul>
			
			</div><!-- .entry-content .clearfix --><?php

			// Function to display the END of the content column markup
			academiathemes_helper_display_page_content_wrapper_end();

			// Function to display the SIDEBAR (if not hidden)
			academiathemes_helper_display_page_sidebar_column();

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

</main><!-- #site-main -->
	
<?php get_footer(); ?>