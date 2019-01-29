<?php 
/**
 * Right Border Control
 *
 * Outputs a color picker, jquery ui slider
 * and a <select> menu to allow the user to
 * control the border-right css property 
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
	var egfBorderRightStyle = typeof egfSettings.border_right_style !== "undefined" ? egfSettings.border_right_style : data.egf_defaults.border_right_style;
	var egfBorderRightWidth = typeof egfSettings.border_right_width !== "undefined" ? egfSettings.border_right_width : data.egf_defaults.border_right_width;
	var egfBorderRightColor = typeof egfSettings.border_right_color !== "undefined" ? egfSettings.border_right_color : data.egf_defaults.border_right_color;
#>
<div class="egf-border-right-controls">

	<!-- Border Style -->
	<span class="customize-control-title"><?php _e( 'Right Style', 'easy-google-fonts' ); ?></span>
	<select class="egf-border-style" autocomplete="off">
		<option value="{{ data.egf_defaults.border_right_style }}">{{ egfTranslation.themeDefault }}</option>
		<# _.each( data.egf_border_styles, function( value, key ) { 
			// Check if selected.
			var selected = ( egfBorderRightStyle === key ) ? 'selected="selected"' : ""; 
		#>
			<option value="{{ key }}" {{ selected }}>{{ value }}</option>
		<# }); #>
	</select>

	<!-- Border Width -->
	<div class="egf-font-slider-control egf-border-right-width-slider">
		<span class="egf-slider-title"><?php _e( 'Right Width', 'easy-google-fonts' ); ?></span>
		<div class="egf-font-slider-display">
			<span>{{ egfBorderRightWidth.amount }}{{ egfBorderRightWidth.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
		</div>
		<div class="egf-clear" ></div>

		<!-- Slider -->
		<div class="egf-slider" value="{{ egfBorderRightWidth.amount }}"></div>
		<div class="egf-clear"></div>
	</div>

	<!-- Border Color -->
	<span class="customize-control-title"><?php _e( 'Right Color', 'easy-google-fonts' ); ?></span>
	<div class="customize-control-content egf-border-right-color-container">
		<input autocomplete="off" class="egf-color-picker-hex" data-default-color="{{ data.egf_defaults.border_right_color }}" value="{{ egfBorderRightColor }}" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'easy-google-fonts' ); ?>"/>
	</div>
</div>
