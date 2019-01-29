<?php 
/**
 * Left Margin Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the left margin of an 
 * element.
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
	var egfMarginLeft = typeof egfSettings.margin_left !== "undefined" ? egfSettings.margin_left : data.egf_defaults.margin_left;
#>
<div class="egf-font-slider-control egf-margin-left-slider">
	<span class="egf-slider-title"><?php _e( 'Left', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfMarginLeft.amount }}{{ egfMarginLeft.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfMarginLeft.amount }}"></div>
	<div class="egf-clear"></div>
</div>
