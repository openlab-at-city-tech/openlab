<?php

function ct_period_register_theme_page()
{
    add_theme_page(
        sprintf(esc_html__('%s Dashboard', 'period'), wp_get_theme()),
        sprintf(esc_html__('%s Dashboard', 'period'), wp_get_theme()),
        'edit_theme_options',
        'period-options',
        'ct_period_options_content'
    );
}
add_action('admin_menu', 'ct_period_register_theme_page');

function ct_period_options_content()
{
    $pro_url = 'https://www.competethemes.com/period-pro/?utm_source=wp-dashboard&utm_medium=Dashboard&utm_campaign=Period%20Pro%20-%20Dashboard'; ?>
	<div id="period-dashboard-wrap" class="wrap period-dashboard-wrap">
		<h2><?php printf(esc_html__('%s Dashboard', 'period'), wp_get_theme()); ?></h2>
		<?php do_action('theme_options_before'); ?>
		<div class="main">
			<?php if (function_exists('ct_period_pro_init')) : ?>
			<div class="thanks-upgrading" style="background-image: url(<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/bg-texture.png'; ?>)">
				<h3>Thanks for upgrading!</h3>
				<p>You can find the new features in the Customizer</p>
			</div>
			<?php endif; ?>
			<?php if (!function_exists('ct_period_pro_init')) : ?>
			<div class="getting-started">
				<h3>Get Started with Period</h3>
				<p>Follow this step-by-step guide to customize your website with Period:</p>
				<a href="https://www.competethemes.com/help/getting-started-period/" target="_blank">Read the Getting Started Guide</a>
			</div>
			<div class="pro">
				<h3>Customize More with Period Pro</h3>
				<p>Add 13 new customization features to your site with the <a href="<?php echo $pro_url; ?>" target="_blank">Period Pro</a> plugin.</p>
				<ul class="feature-list">
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/layouts.png'; ?>" />
						</div>
						<div class="text">
							<h4>New Layouts</h4>
							<p>New layouts help your content look and perform its best. You can switch to new layouts effortlessly from the Customizer, or from specific posts or pages.</p>
							<p>Period Pro adds 6 new layouts.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/custom-colors.png'; ?>" />
						</div>
						<div class="text">
							<h4>Custom Colors</h4>
							<p>Custom colors let you match the color of your site with your brand. Point-and-click to select a color, and watch your site update instantly.</p>
							<p>With 60 color controls, you can change the color of any element on your site.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/fonts.png'; ?>" />
						</div>
						<div class="text">
							<h4>New Fonts</h4>
							<p>Stylish new fonts add character and charm to your content. Select and instantly preview fonts from the Customizer.</p>
							<p>Since Period Pro is powered by Google Fonts, it comes with 728 fonts for you to choose from.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/header-image.png'; ?>" />
						</div>
						<div class="text">
							<h4>Flexible Header Image</h4>
							<p>Header images welcome visitors and set your site apart. Upload your image and quickly resize it to the perfect size.</p>
							<p>Display the header image on just the homepage, or leave it sitewide and link it to the homepage.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/featured-videos.png'; ?>" />
						</div>
						<div class="text">
							<h4>Featured Videos</h4>
							<p>Featured Videos are an easy way to share videos in place of Featured Images. Instantly embed a Youtube video by copying and pasting its URL into an input.</p>
							<p>Period Pro auto-embeds videos from Youtube, Vimeo, DailyMotion, Flickr, Animoto, TED, Blip, Cloudup, FunnyOrDie, Hulu, Vine, WordPress.tv, and VideoPress.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/featured-sliders.png'; ?>" />
						</div>
						<div class="text">
							<h4>Featured Sliders</h4>
							<p>Featured Sliders are an easy way to share image sliders in place of Featured Images. Quickly add responsive sliders to any page or post.</p>
							<p>Period Pro integrates with the free Meta Slider plugin with styling and sizing controls for your sliders.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/background-images.png'; ?>" />
						</div>
						<div class="text">
							<h4>Background Images</h4>
							<p>Background images help you stand out from the rest. Upload a unique image to use as the backdrop for your site.</p>
							<p>Background images are automatically centered and sized to fit the screen.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/background-textures.png'; ?>" />
						</div>
						<div class="text">
							<h4>Background Textures</h4>
							<p>Background textures transform the look and feel of your site. Switch to a textured background with a click.</p>
							<p>Period Pro includes 39 bundled textures to choose from.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/featured-image-size.png'; ?>" />
						</div>
						<div class="text">
							<h4>Featured Image Size</h4>
							<p>Set each Featured Image to the perfect size. You can change the aspect ratio for all Featured Images and individual Featured Images with ease.</p>
							<p>Period Pro includes twelve different aspect ratios for your Featured Images.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/widget-areas.png'; ?>" />
						</div>
						<div class="text">
							<h4>New Widget Areas</h4>
							<p>Utilize a sidebar and four additional widget areas for greater flexibility. Increase ad revenue and generate more email subscribers by adding widgets throughout your site.</p>
							<p>Period Pro adds 4 new widget areas.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/secondary-menu.png'; ?>" />
						</div>
						<div class="text">
							<h4>Secondary Menu</h4>
							<p>The additional menu allows you to expand and optimize your site's navigation. Quickly create and publish your new menu just like the Primary menu.</p>
							<p>Period Pro adds a Secondary to the top of the site.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/display-controls.png'; ?>" />
						</div>
						<div class="text">
							<h4>Display Controls</h4>
							<p>Display controls let you display the parts of your site you want to show off, and hide the rest. Remove elements with just one click.</p>
							<p>Period Pro includes display controls for 11 different elements.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/footer-text.png'; ?>" />
						</div>
						<div class="text">
							<h4>Custom Footer Text</h4>
							<p>Custom footer text lets you further brand your site. Just start typing to add your own text to the footer.</p>
							<p>The footer text supports plain text and full HTML for adding links.</p>
						</div>
					</li>
				</ul>
				<p><a href="<?php echo $pro_url; ?>" target="_blank">Click here</a> to view Period Pro now, and see what it can do for your site.</p>
			</div>
			<div class="pro-ad" style="background-image: url(<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/bg-texture.png'; ?>)">
				<h3>Add Incredible Flexibility to Your Site</h3>
				<p>Start customizing with Period Pro today</p>
				<a href="<?php echo $pro_url; ?>" target="_blank">View Period Pro</a>
			</div>
			<?php endif; ?>
		</div>
		<div class="sidebar">
			<div class="dashboard-widget">
				<h4>More Amazing Resources</h4>
				<ul>
					<li><a href="https://www.competethemes.com/documentation/period-support-center/" target="_blank">Period Support Center</a></li>
					<li><a href="https://wordpress.org/support/theme/period" target="_blank">Support Forum</a></li>
					<li><a href="https://www.competethemes.com/help/period-changelog/" target="_blank">Changelog</a></li>
					<li><a href="https://www.competethemes.com/help/period-css-snippets/" target="_blank">CSS Snippets</a></li>
					<li><a href="https://www.competethemes.com/help/child-theme-period/" target="_blank">Starter child theme</a></li>
					<li><a href="https://www.competethemes.com/help/period-demo-data/" target="_blank">Period demo data</a></li>
					<li><a href="<?php echo $pro_url; ?>" target="_blank">Period Pro</a></li>
				</ul>
			</div>
			<div class="ad iawp">
				<div class="logo-container">
					<img width="308px" height="46px" src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/iawp.svg'; ?>" alt="Independent Analytics logo" />
				</div>
				<div class="features">
					<div class="title">Free WordPress Analytics Plugin</div>
					<ul>
						<li>Beautiful analytics dashboard</li>
						<li>Views & traffic sources</li>
						<li>Easy setup</li>
						<li>GDPR compliant</li>
						<li>Google Analytics alternative</li>
					</ul>
				</div>
				<div class="button">
					<a href="https://independentwp.com" target="_blank" data-product-name="Independent Analytics">Learn More</a>
				</div>
			</div>
			<div class="ad strive">
				<div class="logo-container">
					<img width="308px" height="62px" src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/strive.svg'; ?>" alt="Strive logo" />
				</div>
				<div class="features">
					<div class="title">Content Calendar for WordPress</div>
					<ul>
						<li>Plan your posts visually</li>
						<li>Track progress with post statuses</li>
						<li>Create custom post checklists</li>
						<li>Republish posts with revisions</li>
						<li>Monitor content using the Pipeline</li>
					</ul>
				</div>
				<div class="button">
					<a href="https://strivecalendar.com/?utm_source=competethemes&utm_medium=sidebar&utm_campaign=competethemes_sidebar_widget" target="_blank" data-product-name="Strive">Learn More</a>
				</div>
			</div>
			<div class="dashboard-widget">
				<h4>User Reviews</h4>
				<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/reviews.png'; ?>" />
				<p>Users are loving Period! <a href="https://wordpress.org/support/theme/period/reviews/?filter=5#new-post" target="_blank">Click here</a> to leave your own review</p>
			</div>
			<div class="dashboard-widget">
				<h4>Reset Customizer Settings</h4>
				<p><b>Warning:</b> Clicking this buttin will erase the Period theme's current settings in the Customizer.</p>
				<form method="post">
					<input type="hidden" name="period_reset_customizer" value="period_reset_customizer_settings"/>
					<p>
						<?php wp_nonce_field('period_reset_customizer_nonce', 'period_reset_customizer_nonce'); ?>
						<?php submit_button('Reset Customizer Settings', 'delete', 'delete', false); ?>
					</p>
				</form>
			</div>
		</div>
		<?php do_action('theme_options_after'); ?>
	</div>
<?php
}
