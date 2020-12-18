<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package ePortfolio
 */

get_header();
?>
<div class="site-content">
	<section class="twp-not-found twp-min-height data-bg twp-overlay-black" data-background="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/404.jpg'); ?>">
		<div class="twp-wrapper">
			<div class="twp-icon">
				<i class="fa fa-compass"></i>
			</div>
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'eportfolio' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'eportfolio' ); ?></p>
				<?php
					get_search_form();
				?>
			</div><!-- .page-content -->
		</div>
	</section><!-- .error-404 -->

<?php
get_footer();
