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
		<div class="left-main-box">
			<div class="intro"><h3><?php echo esc_html( $theme->Name ); ?></h3></div>
			<div class="left-inner">
				<div class="about-wrapper">
					<div class="col-left">
						<p><?php echo esc_html( $theme->get( 'Description' ) ); ?></p>
					</div>
					<div class="col-right">
						<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/screenshot.png" alt="" />
					</div>
				</div>
				<div class="link-wrapper">
					<h4><?php esc_html_e('Important Links', 'fse-freelancer-portfolio'); ?></h4>
					<div class="link-buttons">
						<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Free Setup Guide', 'fse-freelancer-portfolio'); ?></a>
						<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'fse-freelancer-portfolio'); ?></a>
						<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'fse-freelancer-portfolio'); ?></a>
						<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Setup Guide', 'fse-freelancer-portfolio'); ?></a>
					</div>
				</div>
				<div class="support-wrapper">
					<div class="editor-box">
						<i class="dashicons dashicons-admin-appearance"></i>
						<h4><?php esc_html_e('Theme Customization', 'fse-freelancer-portfolio'); ?></h4>
						<p><?php esc_html_e('Effortlessly modify & maintain your site using editor.', 'fse-freelancer-portfolio'); ?></p>
						<div class="support-button">
							<a class="button button-primary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" target="_blank"><?php esc_html_e('Site Editor', 'fse-freelancer-portfolio'); ?></a>
						</div>
					</div>
					<div class="support-box">
						<i class="dashicons dashicons-microphone"></i>
						<h4><?php esc_html_e('Need Support?', 'fse-freelancer-portfolio'); ?></h4>
						<p><?php esc_html_e('Go to our support forum to help you in case of queries.', 'fse-freelancer-portfolio'); ?></p>
						<div class="support-button">
							<a class="button button-primary" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Get Support', 'fse-freelancer-portfolio'); ?></a>
						</div>
					</div>
					<div class="review-box">
						<i class="dashicons dashicons-star-filled"></i>
						<h4><?php esc_html_e('Leave Us A Review', 'fse-freelancer-portfolio'); ?></h4>
						<p><?php esc_html_e('Are you enjoying Our Theme? We would Love to hear your Feedback.', 'fse-freelancer-portfolio'); ?></p>
						<div class="support-button">
							<a class="button button-primary" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_REVIEW ); ?>" target="_blank"><?php esc_html_e('Rate Us', 'fse-freelancer-portfolio'); ?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="go-premium-box">
				<h4><?php esc_html_e('Why Go For Premium?', 'fse-freelancer-portfolio'); ?></h4>
				<ul class="pro-list">
					<li><?php esc_html_e('Advanced Customization Options', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('One-Click Demo Import', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('WooCommerce Integration & Enhanced Features', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Performance Optimization & SEO-Ready', 'fse-freelancer-portfolio');?></li>
					<li><?php esc_html_e('Premium Support & Regular Updates', 'fse-freelancer-portfolio');?></li>
				</ul>
			</div>
		</div>
		<div class="right-main-box">
			<div class="right-inner">
				<div class="pro-boxes">
					<h4><?php esc_html_e('Get Theme Bundle', 'fse-freelancer-portfolio'); ?></h4>
					<p><?php esc_html_e('60+ Premium WordPress Themes', 'fse-freelancer-portfolio'); ?></p>
					<p class="main-bundle-price" ><strong class="cancel-bundle-price"><?php esc_html_e('$2340', 'fse-freelancer-portfolio'); ?></strong><span class="bundle-price"><?php esc_html_e('$86', 'fse-freelancer-portfolio'); ?></span></p>
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/bundle.png" alt="bundle image" />
					<p><?php esc_html_e('SUMMER SALE: ', 'fse-freelancer-portfolio'); ?><strong><?php esc_html_e('Extra 20%', 'fse-freelancer-portfolio'); ?></strong><?php esc_html_e(' OFF on WordPress Theme Bundle Use Code: ', 'fse-freelancer-portfolio'); ?><strong><?php esc_html_e('“HEAT20”', 'fse-freelancer-portfolio'); ?></strong></p>
					<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_THEME_BUNDLE ); ?>" target="_blank"><?php esc_html_e('Get Theme Bundle For ', 'fse-freelancer-portfolio'); ?><span><?php esc_html_e('$86', 'fse-freelancer-portfolio'); ?></a>
				</div>
				<div class="pro-boxes pro-theme-container">
					<h4><?php esc_html_e('Fse Freelancer Portfolio Pro', 'fse-freelancer-portfolio'); ?></h4>
					<p class="pro-theme-price" ><?php esc_html_e('$39', 'fse-freelancer-portfolio'); ?></p>
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/premium.png" alt="premium image" />
					<p><?php esc_html_e('SUMMER SALE: ', 'fse-freelancer-portfolio'); ?><strong><?php esc_html_e('Extra 25%', 'fse-freelancer-portfolio'); ?></strong><?php esc_html_e(' OFF on WordPress Block Themes! Use Code: ', 'fse-freelancer-portfolio'); ?><strong><?php esc_html_e('“SUMMER25”', 'fse-freelancer-portfolio'); ?></strong></p>
					<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade To Pro At Just at $29.25', 'fse-freelancer-portfolio'); ?></a>
				</div>
				<div class="pro-boxes last-pro-box">
					<h4><?php esc_html_e('View All Our Themes', 'fse-freelancer-portfolio'); ?></h4>
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/all-themes.png" alt="all themes image" />
					<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_ALL_THEMES ); ?>" target="_blank"><?php esc_html_e('View All Our Premium Themes', 'fse-freelancer-portfolio'); ?></a>
				</div>
			</div>
		</div>
	</div>
<?php } ?>