<?php get_header(); ?>

<main id="site-main">

	<div class="site-page-content">
		<div class="site-section-wrapper site-section-wrapper-main clearfix">

			<?php
			// Function to display Breadcrumbs
			academiathemes_helper_display_breadcrumbs();

			// Function to display the START of the content column markup
			academiathemes_helper_display_page_content_wrapper_start(); ?>

			<h1 class="page-title"><?php esc_html_e('Search Results for', 'bradbury');?>: <strong><?php the_search_query(); ?></strong></h1>
			<?php get_search_form(); ?>

			<?php if (!have_posts()) { ?>
			
			<hr /><div class="entry-content clearfix">
			
				<p><?php esc_html_e( 'Apologies, but the search query did not return any results.', 'bradbury' ); ?></p>
				
				<h3><?php esc_html_e( 'Browse Categories', 'bradbury' ); ?></h3>
				<ul>
					<?php wp_list_categories('title_li=&hierarchical=0&show_count=1'); ?>	
				</ul>
			
				<h3><?php esc_html_e( 'Monthly Archives', 'bradbury' ); ?></h3>
				<ul>
					<?php wp_get_archives('type=monthly&show_post_count=1'); ?>	
				</ul>
			
			</div><!-- .entry-content .clearfix -->
			
			<?php } else { echo '<hr />'; }	?>

			<?php get_template_part('loop');

			// Function to display the END of the content column markup
			academiathemes_helper_display_page_content_wrapper_end();

			// Function to display the SIDEBAR (if not hidden)
			academiathemes_helper_display_page_sidebar_column();

			?>

		</div><!-- .site-section-wrapper .site-section-wrapper-main -->
	</div><!-- .site-page-content -->

</main><!-- #site-main -->
	
<?php get_footer(); ?>