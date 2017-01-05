<?php 
/**
 * Text Decoration Select Control
 *
 * Outputs a select control containing all of 
 * the available text decoration options.
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
	var egfTextDecoration = typeof egfSettings.text_decoration !== "undefined" ? egfSettings.text_decoration : data.egf_defaults.text_decoration;
#>
<span class="customize-control-title"><?php _e( 'Text Decoration', 'easy-google-fonts' ); ?></span>
<select class="egf-text-decoration" autocomplete="off">
	<option value="{{ data.egf_defaults.text_decoration }}">{{ egfTranslation.themeDefault }}</option>
	<# _.each( data.egf_text_decoration, function( value, key ) {
		var selected = ( egfTextDecoration === key ) ? 'selected="selected"' : ""; 
	#>
		<option value="{{ key }}" {{ selected }}>{{ value }}</option>	
	<# }); #>
</select>
