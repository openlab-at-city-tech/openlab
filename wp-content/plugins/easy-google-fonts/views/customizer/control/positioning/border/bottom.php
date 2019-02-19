<?php 
/**
 * Bottom Border Control
 *
 * Outputs a color picker, jquery ui slider
 * and a <select> menu to allow the user to
 * control the border-bottom css property 
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
	var egfBorderBottomStyle = typeof egfSettings.border_bottom_style !== "undefined" ? egfSettings.border_bottom_style : data.egf_defaults.border_bottom_style;
	var egfBorderBottomWidth = typeof egfSettings.border_bottom_width !== "undefined" ? egfSettings.border_bottom_width : data.egf_defaults.border_bottom_width;
	var egfBorderBottomColor = typeof egfSettings.border_bottom_color !== "undefined" ? egfSettings.border_bottom_color : data.egf_defaults.border_bottom_color;
#>
<div class="egf-border-bottom-controls">

	<!-- Border Style -->
	<span class="customize-control-title"><?php _e( 'Bottom Style', 'easy-google-fonts' ); ?></span>
	<select class="egf-border-style" autocomplete="off">
		<option value="{{ data.egf_defaults.border_bottom_style }}">{{ egfTranslation.themeDefault }}</option>
		<# _.each( data.egf_border_styles, function( value, key ) { 
			// Check if selected.
			var selected = ( egfBorderBottomStyle === key ) ? 'selected="selected"' : ""; 
		#>
			<option value="{{ key }}" {{ selected }}>{{ value }}</option>
		<# }); #>
	</select>

	<!-- Border Width -->
	<div class="egf-font-slider-control egf-border-bottom-width-slider">
		<span class="egf-slider-title"><?php _e( 'Bottom Width', 'easy-google-fonts' ); ?></span>
		<div class="egf-font-slider-display">
			<span>{{ egfBorderBottomWidth.amount }}{{ egfBorderBottomWidth.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
		</div>
		<div class="egf-clear" ></div>

		<!-- Slider -->
		<div class="egf-slider" value="{{ egfBorderBottomWidth.amount }}"></div>
		<div class="egf-clear"></div>
	</div>

	<!-- Border Color -->
	<span class="customize-control-title"><?php _e( 'Bottom Color', 'easy-google-fonts' ); ?></span>
	<div class="customize-control-content egf-border-bottom-color-container">
		<input autocomplete="off" class="egf-color-picker-hex" data-default-color="{{ data.egf_defaults.border_bottom_color }}" value="{{ egfBorderBottomColor }}" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'easy-google-fonts' ); ?>"/>
	</div>
</div>
