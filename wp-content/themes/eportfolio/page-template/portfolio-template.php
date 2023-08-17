<?php
/**
 * Template Name: Portfolio Page Template
 *
 * @subpackage ePortfolio
 * @since ePortfolio 1.0.0
 */

get_header(); ?>
<div class="site-content twp-min-height">
	<?php if ( (is_active_sidebar( 'portfolio-template-sidebar' )) && (eportfolio_get_option('enable_portfolio_widget_sidebar') == 1) ) { ?>
		<div class="twp-portfolio-widget-section widget-area">
			<?php dynamic_sidebar( 'portfolio-template-sidebar' ); ?>
		</div>
	<?php } ?>
	<?php if ((eportfolio_get_option('enable_portfolio_masonry_section') == 1) ) { ?>
		<div class="twp-portfolio-gallery-section">
			<?php if (eportfolio_get_option('enable_portfolio_page_title') == 1) { ?>
				<div class="twp-title-section">
					<h2 class="twp-section-title twp-title-with-bar"><?php the_title(); ?> </h2>
				</div>
			<?php } ?>
			<?php $portfolio_masonary_col = 'twp-masonary-gallery-with-space twp-3-col-masonary';
			if ( (is_active_sidebar( 'portfolio-template-sidebar' )) && (eportfolio_get_option('enable_portfolio_widget_sidebar') == 1) ) {
				$portfolio_masonary_col = 'twp-masonary-gallery-no-space twp-2-col-masonary';
			} ?>
			<div class="masonry-blocks <?php echo esc_attr($portfolio_masonary_col); ?> twp-post-with-bg-image">
				<?php 
					$args = array(
						'post_type' => 'post',
						'cat' => absint(eportfolio_get_option('select_category_for_portfolio_section')),
						'ignore_sticky_posts' => true,
						'posts_per_page' => absint(eportfolio_get_option('portfolio_section_post_number')),
					);
				?>
				<?php query_posts($args); ?>
					<?php if ( have_posts() ) : ?>

						<?php 
						/* Start the Loop */
						while ( have_posts() ) :
							the_post();
							$eportfolio_archive_classes = array(
								'twp-gallery-post',
								'twp-overlay-image-hover',
							);
							?>
							<?php
							if (has_post_thumbnail()) { ?>
								<article id="post-<?php the_ID(); ?>" <?php post_class($eportfolio_archive_classes); ?>>
									<a  class="post-thumbnail twp-d-block" href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail('medium_large'); ?>
										<span class="twp-post-format-white">
											<?php echo esc_attr(eportfolio_post_format(get_the_ID())); ?>
										</span>
									</a>
									<div class="twp-desc">
										<?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>
										<div class="twp-categories">
											<?php eportfolio_post_categories(); ?>
										</div>
									</div>
								</article><!-- #post-<?php the_ID(); ?> -->
							<?php } ?>
						<?php 
						endwhile;
						wp_reset_postdata();
						
					endif;
				?>
			</div>
		</div>
	<?php } ?>
				
<?php get_footer(); ?>
