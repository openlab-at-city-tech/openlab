<?php 
/**
 * Bottom Padding Control
 *
 * Outputs a jquery ui slider to allow the
 * user to control the bottom padding of an 
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
	var egfPaddingBottom = typeof egfSettings.padding_bottom !== "undefined" ? egfSettings.padding_bottom : data.egf_defaults.padding_bottom;
#>
<div class="egf-font-slider-control egf-padding-bottom-slider">
	<span class="egf-slider-title"><?php _e( 'Bottom', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfPaddingBottom.amount }}{{ egfPaddingBottom.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfPaddingBottom.amount }}"></div>
	<div class="egf-clear"></div>
</div>
