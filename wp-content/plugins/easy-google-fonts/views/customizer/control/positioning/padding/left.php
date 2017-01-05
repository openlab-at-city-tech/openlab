<?php 
/**
 * Left Padding Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the left padding of an 
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
	var egfPaddingLeft = typeof egfSettings.padding_left !== "undefined" ? egfSettings.padding_left : data.egf_defaults.padding_left;
#>
<div class="egf-font-slider-control egf-padding-left-slider">
	<span class="egf-slider-title"><?php _e( 'Left', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfPaddingLeft.amount }}{{ egfPaddingLeft.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfPaddingLeft.amount }}"></div>
	<div class="egf-clear"></div>
</div>
