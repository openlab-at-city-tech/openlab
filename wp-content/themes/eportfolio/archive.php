<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ePortfolio
 */

get_header();
?>

<?php
	$eportfolio_archive_view_layout = '';
	$eportfolio_archive_active_list_class = '';
	$eportfolio_archive_active_grid_class = '';
	if ((eportfolio_get_option('archive_layout_style')) == 'archive-list-post-layout'){
		$eportfolio_archive_view_layout = 'twp-full-width-post';
		$eportfolio_archive_active_list_class = 'twp-active';
	} else {
		$eportfolio_archive_view_layout = 'twp-post-with-bg-image';
		$eportfolio_archive_active_grid_class = 'twp-active';
	}
?>

	<?php if ((eportfolio_get_option('enable_archive_layout_switch')) == 1) { ?>
		<div class="twp-gallery-grid-section">
			<span id="list-view" class="<?php echo esc_attr($eportfolio_archive_active_list_class); ?>">
				<i class="fa fa-list"></i>
			</span>
			<span id="grid-view"  class="<?php echo esc_attr($eportfolio_archive_active_grid_class); ?>" >
				<i class="fa fa-th"></i>
			</span>
		</div>
	<?php } ?>
	<div class="content twp-archive-section twp-min-height">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header twp-title-with-bar">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description"><p>', '</p></div>' );
				?>
			</header><!-- .page-header -->
			<?php if ((eportfolio_get_option('archive_layout_grid_column')) == '2-column-arc') {
				$eportfolio_masonry_block_archive = "twp-masonary-gallery-no-space twp-2-col-masonary";
			} else {
				$eportfolio_masonry_block_archive = "twp-masonary-gallery-no-space twp-3-col-masonary";
			}?>
			<div class="masonry-blocks <?php echo esc_attr($eportfolio_archive_view_layout); ?> <?php echo esc_attr($eportfolio_masonry_block_archive); ?>" id="masonary-gallery">
			
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

		</main><!-- #main -->
	</div>
<?php get_footer();