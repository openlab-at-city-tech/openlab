<?php 
/**
 * Top Right Border Radius Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the top right border-radius
 * of an element.
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
	var egfBorderRadiusTopRight = typeof egfSettings.border_radius_top_right !== "undefined" ? egfSettings.border_radius_top_right : data.egf_defaults.border_radius_top_right;
#>
<div class="egf-font-slider-control egf-border-radius-top-right-slider">
	<span class="egf-slider-title"><?php _e( 'Top Right', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfBorderRadiusTopRight.amount }}{{ data.egf_defaults.border_radius_top_right.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfBorderRadiusTopRight.amount }}"></div>
	<div class="egf-clear"></div>
</div>
