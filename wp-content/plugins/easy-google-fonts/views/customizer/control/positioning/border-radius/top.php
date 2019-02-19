<?php 
/**
 * Bottom Right Border Radius Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the bottom right 
 * border-radius of an element.
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
	var egfBorderRadiusTopLeft = typeof egfSettings.border_radius_top_left !== "undefined" ? egfSettings.border_radius_top_left : data.egf_defaults.border_radius_top_left;
#>
<div class="egf-font-slider-control egf-border-radius-top-left-slider">
	<span class="egf-slider-title"><?php _e( 'Top Left', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfBorderRadiusTopLeft.amount }}{{ data.egf_defaults.border_radius_top_left.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfBorderRadiusTopLeft.amount }}"></div>
	<div class="egf-clear"></div>
</div>
