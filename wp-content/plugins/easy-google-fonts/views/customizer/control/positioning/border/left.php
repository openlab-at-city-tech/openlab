<?php 
/**
 * Left Border Control
 *
 * Outputs a color picker, jquery ui slider
 * and a <select> menu to allow the user to
 * control the border-left css property 
 * of an element.
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
	var egfBorderLeftStyle = typeof egfSettings.border_left_style !== "undefined" ? egfSettings.border_left_style : data.egf_defaults.border_left_style;
	var egfBorderLeftWidth = typeof egfSettings.border_left_width !== "undefined" ? egfSettings.border_left_width : data.egf_defaults.border_left_width;
	var egfBorderLeftColor = typeof egfSettings.border_left_color !== "undefined" ? egfSettings.border_left_color : data.egf_defaults.border_left_color;
#>
<div class="egf-border-left-controls">

	<!-- Border Style -->
	<span class="customize-control-title"><?php _e( 'Left Style', 'easy-google-fonts' ); ?></span>
	<select class="egf-border-style" autocomplete="off">
		<option value="{{ data.egf_defaults.border_left_style }}">{{ egfTranslation.themeDefault }}</option>
		<# _.each( data.egf_border_styles, function( value, key ) { 
			// Check if selected.
			var selected = ( egfBorderLeftStyle === key ) ? 'selected="selected"' : ""; 
		#>
			<option value="{{ key }}" {{ selected }}>{{ value }}</option>
		<# }); #>
	</select>

	<!-- Border Width -->
	<div class="egf-font-slider-control egf-border-left-width-slider">
		<span class="egf-slider-title"><?php _e( 'Left Width', 'easy-google-fonts' ); ?></span>
		<div class="egf-font-slider-display">
			<span>{{ egfBorderLeftWidth.amount }}{{ egfBorderLeftWidth.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
		</div>
		<div class="egf-clear" ></div>

		<!-- Slider -->
		<div class="egf-slider" value="{{ egfBorderLeftWidth.amount }}"></div>
		<div class="egf-clear"></div>
	</div>

	<!-- Border Color -->
	<span class="customize-control-title"><?php _e( 'Left Color', 'easy-google-fonts' ); ?></span>
	<div class="customize-control-content egf-border-left-color-container">
		<input autocomplete="off" class="egf-color-picker-hex" data-default-color="{{ data.egf_defaults.border_left_color }}" value="{{ egfBorderLeftColor }}" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'easy-google-fonts' ); ?>"/>
	</div>
</div>
