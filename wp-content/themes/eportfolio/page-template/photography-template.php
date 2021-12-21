<?php
/**
 * Template Name: Photography Page Template
 *
 * @subpackage ePortfolio
 * @since ePortfolio 1.0.0
 */

get_header(); ?>

<div class="site-content">
	<?php $twp_rtl_class = 'false';
	if(is_rtl()){ 
	    $twp_rtl_class = 'true';
	}?>
	<div class="twp-eportfolio-photography-slider" data-slick='{"rtl": <?php echo esc_attr($twp_rtl_class); ?>}'>

		<?php 
			$args = array(
				'post_type' => 'post',
				'cat' => absint(eportfolio_get_option('select_category_for_photography_slider')),
				'ignore_sticky_posts' => true,
				'posts_per_page' => absint(eportfolio_get_option('photography_page_slider_number')),
			);
		?>
		<?php query_posts($args); ?>
		<?php if ( have_posts() ) : ?>

		<?php 
			/* Start the Loop */
			while ( have_posts() ) :
			the_post(); 
		?>

		<?php if (has_post_thumbnail()) {
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			$url = $thumb['0']; ?>
				
		<?php } ?>
		<div class="twp-wrapper">
			<?php $eportfolio_photography_slider_overlay = '';
				if (1 == eportfolio_get_option('enable_photography_slider_overlay')) { 
					$eportfolio_photography_slider_overlay = 'twp-overlay-black';
				}
			?>
			<div class="twp-photography-post data-bg <?php echo esc_attr($eportfolio_photography_slider_overlay); ?>"  data-background="<?php echo esc_url($url); ?>">
				<a href="<?php echo esc_url($url); ?>" title="<?php the_title(); ?>">
					<img src="<?php echo esc_url($url); ?>">
					<i class="fa fa-expand"></i>
				</a>
				<?php $eportfolio_slider_text_bg = '';
					if (1 == eportfolio_get_option('enable_background_on_text_details')) { 
						$eportfolio_slider_text_bg = 'twp-overlay-black';
					}
				?>
				<div class="twp-desc <?php echo esc_attr($eportfolio_slider_text_bg); ?>">
					<?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>
					<div class="twp-categories">
						<?php eportfolio_post_categories(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php 
			endwhile;
			wp_reset_postdata();
			endif;
		?>
	</div>
<?php
get_footer();
