<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ePortfolio
 */

?>
<div class="site-content">
	<section class="twp-not-found twp-min-height data-bg twp-overlay-black" data-background="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/404.jpg'); ?>">
		<div class="twp-wrapper">
			<div class="twp-icon">
				<i class="fa fa-compass"></i>
			</div>
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'eportfolio' ); ?></h1>
			</header><!-- .page-header -->
			<div class="page-content">
				<?php
				if ( is_home() && current_user_can( 'publish_posts' ) ) :
	
					printf(
						'<p>' . wp_kses(
							/* translators: 1: link to WP admin new post page. */
							__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'eportfolio' ),
							array(
								'a' => array(
									'href' => array(),
								),
							)
						) . '</p>',
						esc_url( admin_url( 'post-new.php' ) )
					);
	
				elseif ( is_search() ) :
					?>
	
					<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'eportfolio' ); ?></p>
					<?php
					get_search_form();
	
				else :
					?>
	
					<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'eportfolio' ); ?></p>
					<?php
					get_search_form();
	
				endif;
				?>
			</div><!-- .page-content -->
		</div>

	</section><!-- .no-results -->
