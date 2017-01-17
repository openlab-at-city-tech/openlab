<?php 
/**
 * Font Size Slider Control
 *
 * Outputs the new font line height control which is
 * designed to be used with jQuery UI. This is used 
 * to control the font size of a particular font.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.2
 *
 */
?>
<# 
	// Get settings and defaults.
	var egfLineHeight = typeof egfSettings.line_height !== "undefined" ? egfSettings.line_height : data.egf_defaults.line_height;
#>
<div class="egf-font-slider-control egf-line-height-slider">
	<span class="egf-slider-title"><?php _e( 'Line Height', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfLineHeight }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfLineHeight }}"></div>
	<div class="egf-clear"></div>
</div>
