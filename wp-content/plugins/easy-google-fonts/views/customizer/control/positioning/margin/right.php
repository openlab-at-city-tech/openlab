<?php 
/**
 * Right Margin Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the right margin of an 
 * element.
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
	var egfMarginRight = typeof egfSettings.margin_right !== "undefined" ? egfSettings.margin_right : data.egf_defaults.margin_right;
#>
<div class="egf-font-slider-control egf-margin-right-slider">
	<span class="egf-slider-title"><?php _e( 'Right', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfMarginRight.amount }}{{ egfMarginRight.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfMarginRight.amount }}"></div>
	<div class="egf-clear"></div>
</div>
