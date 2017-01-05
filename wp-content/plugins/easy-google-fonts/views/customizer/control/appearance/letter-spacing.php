<?php 
/**
 * Font Size Slider Control
 *
 * Outputs the new letter spacing slider control which is
 * designed to be used with jQuery UI. This is used 
 * to control the letter spacing of a particular font.
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
	var egfLetterSpacing = typeof egfSettings.letter_spacing !== "undefined" ? egfSettings.letter_spacing : data.egf_defaults.letter_spacing;
#>
<div class="egf-font-slider-control egf-letter-spacing-slider">
	<span class="egf-slider-title"><?php _e( 'Letter Spacing', 'easy-google-fonts' ); ?></span>
	<div class="egf-font-slider-display">
		<span>{{ egfLetterSpacing.amount }}{{ egfLetterSpacing.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	</div>
	<div class="egf-clear" ></div>
	
	<!-- Slider -->
	<div class="egf-slider" value="{{ egfLetterSpacing.amount }}"></div>
	<div class="egf-clear"></div>
</div>
