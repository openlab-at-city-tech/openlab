<?php
/**
 * Admin functions.
 *
 * @package CV Portfolio Blocks
 */

define('CV_PORTFOLIO_BLOCKS_SUPPORT',__('https://wordpress.org/support/theme/cv-portfolio-blocks/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_REVIEW',__('https://wordpress.org/support/theme/cv-portfolio-blocks/reviews/#new-post','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_BUY_NOW',__('https://www.wpradiant.net/products/cv-wordpress-theme/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_DOC_URL',__('https://preview.wpradiant.net/tutorial/cv-portfolio-blocks/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_LIVE_DEMO',__('https://preview.wpradiant.net/cv-portfolio-blocks/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_PRO_DOC',__('https://preview.wpradiant.net/tutorial/cv-portfolio-blocks-pro/','cv-portfolio-blocks'));

/**
 * Register admin page.
 *
 * @since 1.0.0
 */

function cv_portfolio_blocks_admin_menu_page() {

	$theme = wp_get_theme( get_template() );

	add_theme_page(
		$theme->display( 'Name' ),
		$theme->display( 'Name' ),
		'manage_options',
		'cv-portfolio-blocks',
		'cv_portfolio_blocks_do_admin_page'
	);

}
add_action( 'admin_menu', 'cv_portfolio_blocks_admin_menu_page' );

function cv_portfolio_blocks_admin_theme_style() {
	wp_enqueue_style('cv-portfolio-blocks-custom-admin-style', esc_url(get_template_directory_uri()) . '/get-started/getstart.css');
	wp_enqueue_script( 'admin-notice-script', get_template_directory_uri() . '/get-started/js/admin-notice-script.js', array( 'jquery' ) );
    wp_localize_script('admin-notice-script', 'example_ajax_obj', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'cv_portfolio_blocks_admin_theme_style');

/**
 * Render admin page.
 *
 * @since 1.0.0
 */
function cv_portfolio_blocks_do_admin_page() {

	$theme = wp_get_theme( get_template() );
	?>
	<div class="cv-portfolio-blocks-appearence wrap about-wrap">
		<div class="head-btn">
			<div><h1><?php echo $theme->display( 'Name' ); ?></h1></div>
			<div class="demo-btn">
				<span>
					<a class="button button-pro" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_BUY_NOW ); ?>" target="_blank"><?php esc_html_e( 'Buy Now', 'cv-portfolio-blocks' ); ?></a>
				</span>
				<span>
					<a class="button button-demo" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e( 'Live Preview', 'cv-portfolio-blocks' ); ?></a>
				</span>
				<span>
					<a class="button button-doc" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_PRO_DOC ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'cv-portfolio-blocks' ); ?></a>
				</span>
			</div>
		</div>
		
		<div class="two-col">

			<div class="about-text">
				<?php
					$description_raw = $theme->display( 'Description' );
					$main_description = explode( 'Official', $description_raw );
					?>
				<?php echo wp_kses_post( $main_description[0] ); ?>
			</div><!-- .col -->

			<div class="about-img">
				<a href="<?php echo esc_url( $theme->display( 'ThemeURI' ) ); ?>" target="_blank"><img src="<?php echo trailingslashit( get_template_directory_uri() ); ?>screenshot.png" alt="<?php echo esc_attr( $theme->display( 'Name' ) ); ?>" /></a>
			</div><!-- .col -->

		</div><!-- .two-col -->
		<div class="four-col">

			<div class="col">

				<h3><i class="dashicons dashicons-book-alt"></i><?php esc_html_e( 'Free Theme Directives', 'cv-portfolio-blocks' ); ?></h3>

				<p>
					<?php esc_html_e( 'This article will walk you through the different phases of setting up and handling your WordPress website.', 'cv-portfolio-blocks' ); ?>
				</p>

				<p>
					<a class="button green button-primary" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_DOC_URL ); ?>" target="_blank"><?php esc_html_e( 'Free Documentation', 'cv-portfolio-blocks' ); ?></a>
				</p>

			</div><!-- .col -->

			<div class="col">

				<h3><i class="dashicons dashicons-admin-customizer"></i><?php esc_html_e( 'Full Site Editing', 'cv-portfolio-blocks' ); ?></h3>

				<p>
					<?php esc_html_e( 'We have used Full Site Editing which will help you preview your changes live and fast.', 'cv-portfolio-blocks' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" ><?php esc_html_e( 'Use Site Editor', 'cv-portfolio-blocks' ); ?></a>
				</p>

			</div><!-- .col -->

			<div class="col">

				<h3><i class="dashicons dashicons-book-alt"></i><?php esc_html_e( 'Leave us a review', 'cv-portfolio-blocks' ); ?></h3>
				<p>
					<?php esc_html_e( 'We would love to hear your feedback.', 'cv-portfolio-blocks' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_REVIEW ); ?>" target="_blank"><?php esc_html_e( 'Review', 'cv-portfolio-blocks' ); ?></a>
				</p>

			</div><!-- .col -->


			<div class="col">

				<h3><i class="dashicons dashicons-sos"></i><?php esc_html_e( 'Help &amp; Support', 'cv-portfolio-blocks' ); ?></h3>

				<p>
					<?php esc_html_e( 'If you have any question/feedback regarding theme, please post in our official support forum.', 'cv-portfolio-blocks' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_SUPPORT ); ?>" target="_blank"><?php esc_html_e( 'Get Support', 'cv-portfolio-blocks' ); ?></a>
				</p>

			</div><!-- .col -->

		</div><!-- .four-col -->


	</div><!-- .wrap -->
	<?php

}