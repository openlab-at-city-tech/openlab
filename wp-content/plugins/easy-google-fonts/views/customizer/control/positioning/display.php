<?php 
/**
 * Display Controls
 *
 * Outputs a select control in order to change the 
 * display properties of an element.
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
	var egfDisplay = typeof egfSettings.display !== "undefined" ? egfSettings.display : data.egf_defaults.display;
#>
<div class="egf-font-toggle">
	<div class="toggle-section-title">
		<span class="customize-control-title inner-control-title"><?php _e( 'Display', 'easy-google-fonts' ); ?></span>
	</div>
	<div class="toggle-section-content">
		<span class="customize-control-title"><?php _e( 'Display', 'easy-google-fonts' ); ?></span>
		<select class="egf-font-display-element" autocomplete="off">
			<option value="{{ data.egf_defaults.display }}">{{ egfTranslation.themeDefault }}</option>
			<# _.each( data.egf_display, function( value, key ) {
				var selected = ( egfDisplay === key ) ? 'selected="selected"' : ""; 
			#>
				<option value="{{ key }}" {{ selected }}>{{ value }}</option>	
			<# }); #>
		</select>
	</div>
</div>
