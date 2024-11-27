<?php
add_action( 'admin_menu', 'fse_freelancer_portfolio_getting_started' );
function fse_freelancer_portfolio_getting_started() {
	add_theme_page( esc_html__('Get Started', 'fse-freelancer-portfolio'), esc_html__('Get Started', 'fse-freelancer-portfolio'), 'edit_theme_options', 'fse-freelancer-portfolio-guide-page', 'fse_freelancer_portfolio_test_guide');
}

// Add a Custom CSS file to WP Admin Area
function fse_freelancer_portfolio_admin_theme_style() {
   wp_enqueue_style('custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/get-started/get-started.css');
}
add_action('admin_enqueue_scripts', 'fse_freelancer_portfolio_admin_theme_style');

//guidline for about theme
function fse_freelancer_portfolio_test_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'fse-freelancer-portfolio' );
?>
	<div class="wrapper-outer">
		<div class="intro">
			<h3><?php echo esc_html( $theme->Name ); ?></h3>
			<p><?php esc_html_e( 'Free Full Site Editing WordPress Theme', 'fse-freelancer-portfolio' ); ?></p>
			<div class="banner-buttons">
				<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Theme Documentation', 'fse-freelancer-portfolio'); ?></a>
			</div>
		</div>
		<div class="left-main-box">
			<div class="about-wrapper">
				<div class="col-left">
					<p><?php echo esc_html( $theme->get( 'Description' ) ); ?></p>
				</div>
				<div class="col-right">
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/screenshot.png" alt="" />
				</div>
			</div>
			<div class="support-wrapper">
				<div class="review-box">
					<i class="dashicons dashicons-star-filled"></i>
					<h4><?php esc_html_e('Leave Us A Review', 'fse-freelancer-portfolio'); ?></h4>
					<p><?php esc_html_e('Are you enjoying our theme? We would love to hear your feedback.', 'fse-freelancer-portfolio'); ?></p>
					<div class="support-button">
						<a class="button button-primary" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_REVIEW ); ?>" target="_blank"><?php esc_html_e('Rate Us', 'fse-freelancer-portfolio'); ?></a>
					</div>
				</div>
				<div class="support-box">
					<i class="dashicons dashicons-microphone"></i>
					<h4><?php esc_html_e('Need Help?', 'fse-freelancer-portfolio'); ?></h4>
					<p><?php esc_html_e('Go to our support forum to help you out in case of queries.', 'fse-freelancer-portfolio'); ?></p>
					<div class="support-button">
						<a class="button button-primary" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Get Support', 'fse-freelancer-portfolio'); ?></a>
					</div>
				</div>
				<div class="editor-box">
					<i class="dashicons dashicons-admin-appearance"></i>
					<h4><?php esc_html_e('Theme Customization', 'fse-freelancer-portfolio'); ?></h4>
					<p><?php esc_html_e('Effortlessly modify and maintain your site using editor.', 'fse-freelancer-portfolio'); ?></p>
					<div class="support-button">
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" target="_blank"><?php esc_html_e('Site Editor', 'fse-freelancer-portfolio'); ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="right-main-box">
			<div class="pro-box">
				<i class="dashicons dashicons-cover-image"></i>
				<h4><?php esc_html_e('Go For Premium', 'fse-freelancer-portfolio'); ?></h4>
				<p><?php esc_html_e('Are you exited for our theme? Proceed for pro version of theme.', 'fse-freelancer-portfolio'); ?></p>
				<div class="pro-buttons">
					<a class="button button-primary doc-btn" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'fse-freelancer-portfolio'); ?></a>
					<a class="button button-primary buy-btn" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Pro', 'fse-freelancer-portfolio'); ?></a>
					<a class="button button-primary demo-btn" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_DEMO ); ?>" target="_blank"><?php esc_html_e('Pro Demo', 'fse-freelancer-portfolio'); ?></a>
				</div>
				<ul class="pro-list">
					<li><?php esc_html_e('Responsive Design', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Demo Content Import', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Aditional plugins', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Background sliders', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Video popups', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('More Fonts and Colors', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Multiple templates', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Multiple front page sections', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Woocommerce support', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Premium support', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('SEO optimization', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Speed optimization', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Browser compatibility', 'fse-freelancer-portfolio');?></li>
			</div>
		</div>
	</div>
<?php } ?>