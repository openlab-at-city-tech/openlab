<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_enqueue_style( 'uris-feature-notice-css', URIS_PLUGIN_URL . 'assets/css/uris-feature-notice.css', array(), '1.2', 'all' );
wp_enqueue_style('uris-bootstrap-min', URIS_PLUGIN_URL.'assets/css/bootstrap-latest/bootstrap.css');
?>
<div class="row col-md-12 wpfrank_banner">
	<div class="col-md-6 col-sm-12 wpfrank_banner_img">
		<a href="http://wpfrank.com/account/signup/ultimate-responsive-image-slider-pro" target="_blank"><img class="img-fluid" src="<?php echo esc_url(URIS_PLUGIN_URL . "assets/img/products/Ultimate-Responsive-Image-Slider-Pro.jpg"); ?>"></a>
	</div>
	<div class="col-md-6 col-sm-12 wpfrank_banner_features">
		<h1 style="color:#FFFFFF;">Ultimate Responsive Image Slider Pro Features</h1>
		<ul>
			<li>5 Slider Layouts</li>
			<li>Two Lightbox Style</li>
			<li>500 Plus Google Fonts</li>
			<li>Link On Slide</li>
			<li>Multiple Image Uploader</li>
			<li>Multiple Setting & Configurations</li>
			<li>Drag and Drop Slide Image Control</li>
			<li>Widget Slider Utility</li>
		</ul>
		<div class="col-md-12 wpfrank_banner_actions">
			<a class="button-primary button-hero" href="http://wpfrank.com/demo/ultimate-responsive-image-slider-pro/" target="_blank">Live Demo</a>
			<a class="button-primary button-hero" href="http://wpfrank.com/account/signup/ultimate-responsive-image-slider-pro" target="_blank">Buy Now $25</a>
		</div>
		<div class="plugin_version">
			<span><b>7.x</b>Version</span>
		</div>
	</div>
</div>