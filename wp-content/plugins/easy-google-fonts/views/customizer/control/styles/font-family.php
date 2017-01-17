<?php 
/**
 * Font Family Select Control
 *
 * Outputs a select control containing all of the available
 * fonts. Added support for different subsets of fonts in 
 * this version.
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
	// Organize fonts with selected subset.
	var egfFontSubset    = typeof egfSettings.subset  !== "undefined" ? egfSettings.subset  : data.egf_defaults.subset;
	var egfFontFamilyId  = typeof egfSettings.font_id !== "undefined" ? egfSettings.font_id : data.egf_defaults.font_id;
	var egfFontsBySubset = [
		{ label: egfTranslation.standardFontLabel,    fonts: egfGetFontsBySubset( egfFontSubset, egfAllFontsBySubset["standard"] )    },
		{ label: egfTranslation.serifFontLabel,       fonts: egfGetFontsBySubset( egfFontSubset, egfAllFontsBySubset["serif"] )       },
		{ label: egfTranslation.sansSerifFontLabel,   fonts: egfGetFontsBySubset( egfFontSubset, egfAllFontsBySubset["sansSerif"] )   },
		{ label: egfTranslation.displayFontLabel,     fonts: egfGetFontsBySubset( egfFontSubset, egfAllFontsBySubset["display"] )     },
		{ label: egfTranslation.handwritingFontLabel, fonts: egfGetFontsBySubset( egfFontSubset, egfAllFontsBySubset["handwriting"] ) },
		{ label: egfTranslation.monospaceFontLabel,   fonts: egfGetFontsBySubset( egfFontSubset, egfAllFontsBySubset["monospace"] )   }
	];
#>
<span class="customize-control-title"><?php _e( 'Font Family', 'easy-google-fonts' ); ?></span>
<select class="egf-font-family" autocomplete="off">
	<option value="{{ data.egf_defaults.font_id }}">{{ egfTranslation.themeDefault }}</option>
	<# _.each( egfFontsBySubset, function( font ) { #>
		<# if ( ! _.isEmpty( font.fonts ) ) { #>
			<optgroup label="{{ font.label }}">
				<# _.each( font.fonts, function( font, id ) {
					var selected = ( egfFontFamilyId === id ) ? 'selected="selected"' : "";  
				#>
					<option value="{{ id }}" data-font-type="{{ font.font_type }}" {{ selected }}>{{ font.name }}</option>
				<# }); #>
			</optgroup>
		<# } #>
	<# }); #>
</select>
