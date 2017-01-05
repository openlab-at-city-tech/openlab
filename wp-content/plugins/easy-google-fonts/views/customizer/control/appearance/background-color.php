<?php 
/**
 * Background Color Control
 *
 * Outputs the new background color control from 
 * Automattic. This is used to control the background
 * color of a particular font.
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
	var egfBackgroundColor = typeof egfSettings.background_color !== "undefined" ? egfSettings.background_color : data.egf_defaults.background_color;
#>
<span class="customize-control-title"><?php _e( 'Background Color', 'easy-google-fonts' ); ?></span>
<div class="customize-control-content egf-background-color-container">
	<input autocomplete="off" class="egf-color-picker-hex" data-default-color="{{ data.egf_defaults.background_color }}" value="{{ egfBackgroundColor }}" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'easy-google-fonts' ); ?>" />
</div>
