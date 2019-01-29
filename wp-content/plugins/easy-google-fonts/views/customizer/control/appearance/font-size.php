<?php 
/**
 * Font Size Slider Control
 *
 * Outputs the new font size slider control which is
 * designed to be used with jQuery UI. This is used 
 * to control the font size of a particular font.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
?>
<# 
	// Get settings and defaults.
	var egfFontSize = typeof egfSettings.font_size !== "undefined" ? egfSettings.font_size : data.egf_defaults.font_size;
#>
<div class="egf-font-slider-control egf-font-size-slider">
	<span class="egf-slider-title"><?php _e( 'Font Size', 'easy-google-fonts' ); ?></span>
	
	<div class="egf-font-slider-display">
		<span>{{ egfFontSize.amount }}{{ egfFontSize.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>

	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfFontSize.amount }}"></div>	
	
	<div class="egf-clear"></div>
</div>
