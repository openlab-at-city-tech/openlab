<?php
add_action( 'admin_menu', 'fse_freelancer_portfolio_getting_started' );
function fse_freelancer_portfolio_getting_started() {
	add_theme_page( esc_html__('Get Started', 'fse-freelancer-portfolio'), esc_html__('Get Started', 'fse-freelancer-portfolio'), 'edit_theme_options', 'fse-freelancer-portfolio-guide-page', 'fse_freelancer_portfolio_test_guide');
}

// Add a Custom CSS file to WP Admin Area (kept global: also styles theme-activation admin notices).
function fse_freelancer_portfolio_admin_theme_style() {
	wp_enqueue_style( 'custom-admin-style', esc_url( get_template_directory_uri() ) . '/inc/get-started/get-started.css', array(), wp_get_theme( 'fse-freelancer-portfolio' )->get( 'Version' ) );
}
add_action( 'admin_enqueue_scripts', 'fse_freelancer_portfolio_admin_theme_style' );

// Countdown + copy-to-clipboard script, only on the Get Started page itself.
function fse_freelancer_portfolio_admin_theme_script( $hook ) {
	if ( 'appearance_page_fse-freelancer-portfolio-guide-page' !== $hook ) {
		return;
	}
	$fse_freelancer_portfolio_theme = wp_get_theme( 'fse-freelancer-portfolio' );
	wp_enqueue_script( 'fse-freelancer-portfolio-get-started', esc_url( get_template_directory_uri() ) . '/inc/get-started/js/get-started.js', array(), $fse_freelancer_portfolio_theme->get( 'Version' ), true );
	wp_localize_script( 'fse-freelancer-portfolio-get-started', 'fseFreelancerPortfolioGetStarted', array(
		'countdownMinHours' => 48,
		'countdownMaxHours' => 72,
		'copiedText'        => esc_html__( 'Copied!', 'fse-freelancer-portfolio' ),
		'copyText'          => esc_html__( 'Copy', 'fse-freelancer-portfolio' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'fse_freelancer_portfolio_admin_theme_script' );

//guidline for about theme
function fse_freelancer_portfolio_test_guide() {
	$fse_freelancer_portfolio_theme        = wp_get_theme( 'fse-freelancer-portfolio' );
	$fse_freelancer_portfolio_theme_name   = $fse_freelancer_portfolio_theme->get( 'Name' );
	$fse_freelancer_portfolio_description  = $fse_freelancer_portfolio_theme->get( 'Description' );
	$fse_freelancer_portfolio_hero_tagline = wp_trim_words( $fse_freelancer_portfolio_description, 25, '...' );
	$fse_freelancer_portfolio_screenshot   = esc_url( get_template_directory_uri() ) . '/inc/get-started/images/screenshot.png';
	$fse_freelancer_portfolio_premium_img  = esc_url( get_template_directory_uri() ) . '/inc/get-started/images/bundle.png';
?>
	<div class="pm-wrap">

		<div class="pm-quicklinks">
			<a href="<?php echo esc_url( home_url() ); ?>" target="_blank"><?php esc_html_e( 'Visit Site', 'fse-freelancer-portfolio' ); ?></a>
			<span class="pm-sep">/</span>
			<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e( 'Free Setup Guide', 'fse-freelancer-portfolio' ); ?></a>
			<span class="pm-sep">/</span>
			<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e( 'Pro Setup Guide', 'fse-freelancer-portfolio' ); ?></a>
			<span class="pm-sep">/</span>
			<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_SUPPORT ); ?>" target="_blank"><?php esc_html_e( 'Support Forum', 'fse-freelancer-portfolio' ); ?></a>
			<span class="pm-sep">/</span>
			<a href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_REVIEW ); ?>" target="_blank"><?php esc_html_e( 'Rate Us', 'fse-freelancer-portfolio' ); ?> &#9733;</a>
		</div>

		<div class="pm-grid">
			<div class="pm-main">

				<div class="pm-hero">
					<div class="pm-hero-content">
						<span class="pm-pill"><?php esc_html_e( 'Premium WordPress Theme', 'fse-freelancer-portfolio' ); ?></span>
						<p class="pm-hero-eyebrow"><?php esc_html_e( 'Welcome to', 'fse-freelancer-portfolio' ); ?></p>
						<h1 class="pm-hero-title"><?php echo esc_html( $fse_freelancer_portfolio_theme_name ); ?></h1>
						<p class="pm-hero-desc"><?php echo esc_html( $fse_freelancer_portfolio_hero_tagline ); ?></p>
						<div class="pm-hero-actions">
							<a class="pm-btn pm-btn-primary" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_BUY_NOW ); ?>" target="_blank">
								<span class="dashicons dashicons-awards"></span><?php esc_html_e( 'Upgrade To Pro', 'fse-freelancer-portfolio' ); ?>
							</a>
							<a class="pm-btn pm-btn-outline" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_DEMO ); ?>" target="_blank">
								<?php esc_html_e( 'Live Demo', 'fse-freelancer-portfolio' ); ?><span class="dashicons dashicons-external"></span>
							</a>
							<a class="pm-btn pm-btn-outline" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_THEME_BUNDLE ); ?>" target="_blank">
								<span class="dashicons dashicons-screenoptions"></span><?php esc_html_e( 'Get Theme Bundle', 'fse-freelancer-portfolio' ); ?>
							</a>
						</div>
					</div>
					<div class="pm-hero-media">
						<img src="<?php echo esc_url( $fse_freelancer_portfolio_screenshot ); ?>" alt="<?php echo esc_attr( $fse_freelancer_portfolio_theme_name ); ?>" />
						<span class="pm-hero-badge"><?php esc_html_e( '100% Responsive', 'fse-freelancer-portfolio' ); ?></span>
					</div>
				</div>

				<div class="pm-card pm-about">
					<div class="pm-about-icon"><span class="dashicons dashicons-info-outline"></span></div>
					<div class="pm-about-body">
						<h2><?php
							/* translators: %s: theme name */
							echo esc_html( sprintf( __( 'About %s', 'fse-freelancer-portfolio' ), $fse_freelancer_portfolio_theme_name ) );
						?></h2>
						<p><?php echo esc_html( $fse_freelancer_portfolio_description ); ?></p>
						<div class="pm-feature-row">
							<div class="pm-feature">
								<span class="dashicons dashicons-smartphone"></span>
								<div>
									<strong><?php esc_html_e( 'Modern & Responsive', 'fse-freelancer-portfolio' ); ?></strong>
									<p><?php esc_html_e( 'Looks perfect on all devices', 'fse-freelancer-portfolio' ); ?></p>
								</div>
							</div>
							<div class="pm-feature">
								<span class="dashicons dashicons-search"></span>
								<div>
									<strong><?php esc_html_e( 'SEO Optimized', 'fse-freelancer-portfolio' ); ?></strong>
									<p><?php esc_html_e( 'Built with best SEO practices', 'fse-freelancer-portfolio' ); ?></p>
								</div>
							</div>
							<div class="pm-feature">
								<span class="dashicons dashicons-performance"></span>
								<div>
									<strong><?php esc_html_e( 'Fast & Lightweight', 'fse-freelancer-portfolio' ); ?></strong>
									<p><?php esc_html_e( 'Optimized for speed & performance', 'fse-freelancer-portfolio' ); ?></p>
								</div>
							</div>
							<div class="pm-feature">
								<span class="dashicons dashicons-admin-customizer"></span>
								<div>
									<strong><?php esc_html_e( 'Easy Customization', 'fse-freelancer-portfolio' ); ?></strong>
									<p><?php esc_html_e( 'No coding skills required', 'fse-freelancer-portfolio' ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="pm-card pm-quickstart">
					<h2><?php esc_html_e( 'Get Started Quickly', 'fse-freelancer-portfolio' ); ?></h2>
					<div class="pm-quickstart-grid">
						<div class="pm-qs-box">
							<span class="pm-qs-icon pm-qs-purple dashicons dashicons-media-document"></span>
							<h4><?php esc_html_e( 'Theme Documentation', 'fse-freelancer-portfolio' ); ?></h4>
							<p><?php esc_html_e( 'Learn how to set up and use all theme features.', 'fse-freelancer-portfolio' ); ?></p>
							<a class="pm-qs-link pm-qs-link-purple" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e( 'View Documentation', 'fse-freelancer-portfolio' ); ?></a>
						</div>
						<div class="pm-qs-box">
							<span class="pm-qs-icon pm-qs-green dashicons dashicons-desktop"></span>
							<h4><?php esc_html_e( 'Theme Demo', 'fse-freelancer-portfolio' ); ?></h4>
							<p><?php esc_html_e( 'Preview the theme in action before you start customizing.', 'fse-freelancer-portfolio' ); ?></p>
							<a class="pm-qs-link pm-qs-link-green" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_DEMO ); ?>" target="_blank"><?php esc_html_e( 'View Demo', 'fse-freelancer-portfolio' ); ?></a>
						</div>
						<div class="pm-qs-box">
							<span class="pm-qs-icon pm-qs-orange dashicons dashicons-admin-appearance"></span>
							<h4><?php esc_html_e( 'Theme Editor', 'fse-freelancer-portfolio' ); ?></h4>
							<p><?php esc_html_e( 'Edit your site appearance and layout.', 'fse-freelancer-portfolio' ); ?></p>
							<a class="pm-qs-link pm-qs-link-orange" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" target="_blank"><?php esc_html_e( 'Edit Now', 'fse-freelancer-portfolio' ); ?></a>
						</div>
						<div class="pm-qs-box">
							<span class="pm-qs-icon pm-qs-blue dashicons dashicons-sos"></span>
							<h4><?php esc_html_e( 'Quick Support', 'fse-freelancer-portfolio' ); ?></h4>
							<p><?php esc_html_e( 'Get expert help from our dedicated support team.', 'fse-freelancer-portfolio' ); ?></p>
							<a class="pm-qs-link pm-qs-link-blue" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_SUPPORT ); ?>" target="_blank"><?php esc_html_e( 'Get Support', 'fse-freelancer-portfolio' ); ?></a>
						</div>
					</div>
				</div>

				<div class="pm-premium-banner">
					<div class="pm-premium-left">
						<h3><span class="dashicons dashicons-star-filled"></span><?php esc_html_e( 'Why Go Premium?', 'fse-freelancer-portfolio' ); ?></h3>
						<div class="pm-premium-list">
							<ul>
								<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Advanced Customization Options', 'fse-freelancer-portfolio' ); ?></li>
								<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'One-Click Demo Import', 'fse-freelancer-portfolio' ); ?></li>
								<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'WooCommerce Integration & Enhanced Features', 'fse-freelancer-portfolio' ); ?></li>
							</ul>
							<ul>
								<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Performance Optimization & SEO-Ready', 'fse-freelancer-portfolio' ); ?></li>
								<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Premium Support & Regular Updates', 'fse-freelancer-portfolio' ); ?></li>
							</ul>
						</div>
					</div>
					<div class="pm-premium-right">
						<p class="pm-premium-from"><?php esc_html_e( 'Starting From', 'fse-freelancer-portfolio' ); ?></p>
						<p class="pm-premium-price"><?php esc_html_e( '$39', 'fse-freelancer-portfolio' ); ?></p>
						<p class="pm-premium-note"><?php esc_html_e( 'One Time Payment', 'fse-freelancer-portfolio' ); ?></p>
						<a class="pm-btn pm-btn-primary" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_BUY_NOW ); ?>" target="_blank">
							<span class="dashicons dashicons-awards"></span><?php esc_html_e( 'Upgrade To Pro', 'fse-freelancer-portfolio' ); ?>
						</a>
					</div>
				</div>

				<div class="pm-stats-row">
					<div class="pm-stat">
						<span class="dashicons dashicons-translation"></span>
						<strong><?php esc_html_e( 'Translation Ready', 'fse-freelancer-portfolio' ); ?></strong>
					</div>
					<div class="pm-stat">
						<span class="dashicons dashicons-block-default"></span>
						<strong><?php esc_html_e( 'Full Site Editing', 'fse-freelancer-portfolio' ); ?></strong>
					</div>
					<div class="pm-stat">
						<span class="dashicons dashicons-welcome-widgets-menus"></span>
						<strong><?php esc_html_e( 'Widget Ready', 'fse-freelancer-portfolio' ); ?></strong>
					</div>
					<div class="pm-stat">
						<span class="dashicons dashicons-shield-alt"></span>
						<strong><?php esc_html_e( 'GPL Licensed', 'fse-freelancer-portfolio' ); ?></strong>
					</div>
				</div>

			</div>

			<div class="pm-sidebar">

				<div class="pm-card pm-offer">
					<span class="pm-offer-badge"><?php esc_html_e( 'LIMITED TIME OFFER', 'fse-freelancer-portfolio' ); ?></span>
					<h3><?php esc_html_e( 'Special Launch Offer!', 'fse-freelancer-portfolio' ); ?></h3>
					<p><?php esc_html_e( 'Get 25% OFF on Our WordPress Theme', 'fse-freelancer-portfolio' ); ?></p>

					<p class="pm-coupon-label"><?php esc_html_e( 'Use Coupon Code:', 'fse-freelancer-portfolio' ); ?></p>
					<div class="pm-coupon">
						<span class="pm-coupon-code"><?php esc_html_e( 'HALLOWEEN25', 'fse-freelancer-portfolio' ); ?></span>
						<button type="button" class="pm-copy-btn" data-copy-text="HALLOWEEN25">
							<span class="dashicons dashicons-clipboard"></span>
						</button>
					</div>

					<p class="pm-offer-price">
						<strong class="pm-price-cancel"><?php esc_html_e( '$39', 'fse-freelancer-portfolio' ); ?></strong>
						<span class="pm-price-final"><?php esc_html_e( '$29.25', 'fse-freelancer-portfolio' ); ?></span>
					</p>

					<div class="pm-countdown" data-countdown>
						<div class="pm-countdown-box"><span data-countdown-days>00</span><small><?php esc_html_e( 'Days', 'fse-freelancer-portfolio' ); ?></small></div>
						<div class="pm-countdown-box"><span data-countdown-hours>00</span><small><?php esc_html_e( 'Hours', 'fse-freelancer-portfolio' ); ?></small></div>
						<div class="pm-countdown-box"><span data-countdown-minutes>00</span><small><?php esc_html_e( 'Minutes', 'fse-freelancer-portfolio' ); ?></small></div>
						<div class="pm-countdown-box"><span data-countdown-seconds>00</span><small><?php esc_html_e( 'Seconds', 'fse-freelancer-portfolio' ); ?></small></div>
					</div>

					<a class="pm-btn pm-btn-primary pm-btn-block" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_BUY_NOW ); ?>" target="_blank">
						<span class="dashicons dashicons-awards"></span><?php esc_html_e( 'GET PREMIUM', 'fse-freelancer-portfolio' ); ?>
					</a>
					<p class="pm-offer-footnote"><?php esc_html_e( 'Offer valid for a limited time only!', 'fse-freelancer-portfolio' ); ?></p>
				</div>

				<div class="pm-card pm-bundle">
					<h3><?php esc_html_e( 'Get Theme Bundle', 'fse-freelancer-portfolio' ); ?></h3>
					<p><?php esc_html_e( '80+ Premium WordPress Themes in one bundle.', 'fse-freelancer-portfolio' ); ?></p>
					<img src="<?php echo esc_url( $fse_freelancer_portfolio_premium_img ); ?>" alt="<?php esc_attr_e( 'Theme bundle preview', 'fse-freelancer-portfolio' ); ?>" />
					<p class="pm-offer-price">
						<strong class="pm-price-cancel"><?php esc_html_e( '$2340', 'fse-freelancer-portfolio' ); ?></strong>
						<span class="pm-price-final"><?php esc_html_e( '$86', 'fse-freelancer-portfolio' ); ?></span>
					</p>
					<a class="pm-btn pm-btn-primary pm-btn-block" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_PRO_THEME_BUNDLE ); ?>" target="_blank">
						<span class="dashicons dashicons-awards"></span><?php esc_html_e( 'Get Theme Bundle', 'fse-freelancer-portfolio' ); ?>
					</a>
				</div>

				<div class="pm-card pm-help">
					<h3><?php esc_html_e( 'Need Help?', 'fse-freelancer-portfolio' ); ?></h3>
					<p><?php esc_html_e( 'Our support team is here to help you succeed. Get expert assistance whenever you need it.', 'fse-freelancer-portfolio' ); ?></p>
					<ul class="pm-help-list">
						<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Quick Response', 'fse-freelancer-portfolio' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Expert Support', 'fse-freelancer-portfolio' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Detailed Solutions', 'fse-freelancer-portfolio' ); ?></li>
					</ul>
					<a class="pm-btn pm-btn-dark pm-btn-block" href="<?php echo esc_url( FSE_FREELANCER_PORTFOLIO_SUPPORT ); ?>" target="_blank">
						<span class="dashicons dashicons-format-chat"></span><?php esc_html_e( 'Get Support Now', 'fse-freelancer-portfolio' ); ?>
					</a>
				</div>

			</div>
		</div>
	</div>
<?php } ?>
