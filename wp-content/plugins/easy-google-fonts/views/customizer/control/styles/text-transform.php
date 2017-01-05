<?php 
/**
 * Text Transform Select Control
 *
 * Outputs a select control containing all of 
 * the available text transform options.
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
	var egfTextTransform = typeof egfSettings.text_transform !== "undefined" ? egfSettings.text_transform : data.egf_defaults.text_transform;
#>
<span class="customize-control-title"><?php _e( 'Text Transform', 'easy-google-fonts' ); ?></span>
<select class="egf-text-transform" autocomplete="off">
	<option value="{{ data.egf_defaults.text_transform }}">{{ egfTranslation.themeDefault }}</option>
	<# _.each( data.egf_text_transform, function( value, key ) {
		var selected = ( egfTextTransform === key ) ? 'selected="selected"' : ""; 
	#>
		<option value="{{ key }}" {{ selected }}>{{ value }}</option>
	<# }); #>
</select>
