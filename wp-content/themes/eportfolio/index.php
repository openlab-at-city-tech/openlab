<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ePortfolio
 */

get_header();
?>
<div id="content" class="twp-min-height">
<?php
	$eportfolio_blog_view_layout = '';
	$eportfolio_blog_active_list_class = '';
	$eportfolio_blog_active_grid_class = '';
	if ((eportfolio_get_option('blog_layout_style')) == 'list-post-layout'){
		$eportfolio_blog_view_layout = 'twp-full-width-post';
		$eportfolio_blog_active_list_class = 'twp-active';
	} else {
		$eportfolio_blog_view_layout = 'twp-post-with-bg-image';
		$eportfolio_blog_active_grid_class = 'twp-active';
	}
?>

	<?php if ((eportfolio_get_option('enable_blog_layout_switch')) == 1) { ?>
		<div class="twp-gallery-grid-section">
			<span id="list-view"  class="<?php echo esc_attr($eportfolio_blog_active_list_class); ?>">
				<i class="fa fa-list"></i>
			</span>
			<span id="grid-view"  class="<?php echo esc_attr($eportfolio_blog_active_grid_class); ?>">
				<i class="fa fa-th"></i>
			</span>
		</div>
	<?php } ?>

	<?php
	if ( have_posts() ) :

		if ( is_home() && ! is_front_page() ) :
			?>
			<header>
				<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
			</header>
			<?php
		endif; ?>
		<?php if ((eportfolio_get_option('blog_layout_grid_column')) == '2-column') {
			$eportfolio_masonry_block = "twp-masonary-gallery-no-space twp-2-col-masonary";
		} else {
			$eportfolio_masonry_block = "twp-masonary-gallery-no-space twp-3-col-masonary";
		}?>

		<div class="masonry-blocks <?php echo esc_attr($eportfolio_masonry_block); ?> <?php echo esc_attr($eportfolio_blog_view_layout); ?>" id="masonary-gallery">
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
		echo '</div>';
		do_action( 'eportfolio_posts_navigation' );
		

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>
</div>
<?php
get_footer();
