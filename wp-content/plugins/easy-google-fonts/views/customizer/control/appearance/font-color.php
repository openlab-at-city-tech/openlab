<?php 
/**
 * Font Color Select Control
 *
 * Outputs the new font color control from Automattic.
 * This is used to control the color of a particular 
 * font.
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
	var egfFontColor = typeof egfSettings.font_color !== "undefined" ? egfSettings.font_color : data.egf_defaults.font_color;
#>
<span class="customize-control-title"><?php _e( 'Font Color', 'easy-google-fonts' ); ?></span>
<div class="customize-control-content egf-font-color-container">
	<input autocomplete="off" class="egf-color-picker-hex" data-default-color="{{ data.egf_defaults.font_color }}" value="{{ egfFontColor }}" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'easy-google-fonts' ); ?>"/>
</div>
