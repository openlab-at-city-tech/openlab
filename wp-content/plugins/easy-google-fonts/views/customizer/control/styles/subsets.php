<?php 
/**
 * Font Subset Select Control
 *
 * Outputs a select control which allows the user to 
 * narrow down a list of available fonts based on the
 * subset.
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
	var egfSubset = typeof egfSettings.subset !== "undefined" ? egfSettings.subset : data.egf_defaults.subset;
#>
<span class="customize-control-title"><?php _e( 'Script/Subset', 'easy-google-fonts' ); ?></span>
<select class="egf-font-subsets" autocomplete="off">
	<# _.each( data.egf_subsets, function( value, key ) {

		/**
		 * Only adds the latin prefix if applicable and
		 * if not already applied. This check is required
		 * for backwards compatibility.
		 */
		if ( 'khmer' !== key && 
		     'latin' !== key && 
		     key.indexOf( 'latin,' ) !== -1 ) {	
			
			// Add latin prefix to key.
		    key = 'latin,' + key; 
		}
		
		// Check if selected.
		var selected = ( egfSubset === key ) ? 'selected="selected"' : ""; 
	#>
		<option value="{{ key }}" {{ selected }}>{{ value }}</option>	
	<# }); #>
</select>
