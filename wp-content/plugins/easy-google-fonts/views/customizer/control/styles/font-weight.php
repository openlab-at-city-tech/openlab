<?php 
/**
 * Font Weight/Style Select Control
 *
 * Outputs a select control containing all of the available
 * fonts weights and variants. Added support for different 
 * subsets of fonts in this version.
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
	var egfFontId          = typeof egfSettings.font_id !== "undefined" ? egfSettings.font_id : data.egf_defaults.font_id;
	var egfFontWeightStyle = typeof egfSettings.font_weight_style !== "undefined" ? egfSettings.font_weight_style : data.egf_defaults.font_weight_style;
#>
<span class="customize-control-title"><?php _e( 'Font Weight/Style', 'easy-google-fonts' ); ?></span>
<select class="egf-font-weight" autocomplete="off">
	<# if ( '' === egfFontId ) { #>
		<option value="{{ data.egf_defaults.font_weight_style }}">{{ egfTranslation.themeDefault }}</option>
	<# } else { #>
		<# if ( "undefined" !== typeof( egfAllFonts[ egfFontId ] ) ) { #>
			<# _.each( egfAllFonts[ egfFontId ].font_weights, function( weight ) {
					var selected = ( egfFontWeightStyle === weight ) ? 'selected="selected"' : "";  
			#>
				<option value="{{ weight }}" {{ selected }}>{{ weight }}</option>
			<# }); #>
		<# } else { #>
			<option value="{{ data.egf_defaults.font_weight_style }}">{{ egfTranslation.themeDefault }}</option>
		<# } #>
	<# } #>
</select>
