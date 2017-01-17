<?php 
/**
 * Top Border Control
 *
 * Outputs a color picker, jquery ui slider
 * and a <select> menu to allow the user to
 * control the border-top css property 
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
	var egfBorderTopStyle = typeof egfSettings.border_top_style !== "undefined" ? egfSettings.border_top_style : data.egf_defaults.border_top_style;
	var egfBorderTopWidth = typeof egfSettings.border_top_width !== "undefined" ? egfSettings.border_top_width : data.egf_defaults.border_top_width;
	var egfBorderTopColor = typeof egfSettings.border_top_color !== "undefined" ? egfSettings.border_top_color : data.egf_defaults.border_top_color;
#>
<div class="egf-border-top-controls selected">

	<!-- Border Style -->
	<span class="customize-control-title"><?php _e( 'Top Style', 'easy-google-fonts' ); ?></span>
	<select class="egf-border-style" autocomplete="off">
		<option value="{{ data.egf_defaults.border_top_style }}">{{ egfTranslation.themeDefault }}</option>
		<# _.each( data.egf_border_styles, function( value, key ) { 
			// Check if selected.
			var selected = ( egfBorderTopStyle === key ) ? 'selected="selected"' : ""; 
		#>
			<option value="{{ key }}" {{ selected }}>{{ value }}</option>
		<# }); #>
	</select>

	<!-- Border Width -->
	<div class="egf-font-slider-control egf-border-top-width-slider">
		<span class="egf-slider-title"><?php _e( 'Top Width', 'easy-google-fonts' ); ?></span>
		<div class="egf-font-slider-display">
			<span>{{ egfBorderTopWidth.amount }}{{ egfBorderTopWidth.unit }}</span> | <a class="egf-font-slider-reset" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
		</div>
		<div class="egf-clear" ></div>

		<!-- Slider -->
		<div class="egf-slider" value="{{ egfBorderTopWidth.amount }}"></div>
		<div class="egf-clear"></div>
	</div>

	<!-- Border Color -->
	<span class="customize-control-title"><?php _e( 'Top Color', 'easy-google-fonts' ); ?></span>
	<div class="customize-control-content egf-border-top-color-container">
		<input autocomplete="off" class="egf-color-picker-hex" data-default-color="{{ data.egf_defaults.border_top_color }}" value="{{ egfBorderTopColor }}" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'easy-google-fonts' ); ?>"/>
	</div>
</div>
