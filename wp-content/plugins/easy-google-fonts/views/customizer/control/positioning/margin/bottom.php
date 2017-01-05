<?php 
/**
 * Bottom Margin Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the bottom margin of an 
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
	var egfMarginBottom = typeof egfSettings.margin_bottom !== "undefined" ? egfSettings.margin_bottom : data.egf_defaults.margin_bottom;
#>
<div class="egf-font-slider-control egf-margin-bottom-slider">
	<span class="egf-slider-title"><?php _e( 'Bottom', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfMarginBottom.amount }}{{ egfMarginBottom.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfMarginBottom.amount }}"></div>
	<div class="egf-clear"></div>
</div>
