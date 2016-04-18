// Defining the main variable to contain the 
// functions that will be called from the popup

var SZGoogleDialog = 
{
	local_ed:'ed',

	// Init function for the initial operations of 
	// the component to be executed in this file

	init: function(ed) {
		SZGoogleDialog.local_ed = ed;
		tinyMCEPopup.resizeToInnerSize();
	},

	// Function associated with the cancel button at 
	// the end of the screen in each popup shortcode

	cancel: function(ed) {
		tinyMCEPopup.close();
	},

	// Insert function for creating the code 
	// shortcode with all the preset options

	insert: function(ed) {

		var SZGoogleEditor = tinyMCE.get("content");

		// Execution command after calculating the variable 
		// editor currently displayed and stored in SZGoogleEditor

		SZGoogleEditor.execCommand('mceRemoveNode',false,null);

		// Calculating the values ​​of variables directly 
		// from the form fields without submission standards

		var output   = '';

		var width    = jQuery('#ID_width'   ).val();
		var height   = jQuery('#ID_height'  ).val();
		var lat      = jQuery('#ID_lat'     ).val();
		var lng      = jQuery('#ID_lng'     ).val();
		var zoom     = jQuery('#ID_zoom'    ).val();
		var view     = jQuery('#ID_view'    ).val();
		var layer    = jQuery('#ID_layer'   ).val();
		var wheel    = jQuery('#ID_wheel'   ).val();
		var marker   = jQuery('#ID_marker'  ).val();
		var lazyload = jQuery('#ID_lazyload').val();

		if (jQuery('#ID_width_auto' ).is(':checked')) width  = 'auto';
		if (jQuery('#ID_height_auto').is(':checked')) height = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-maps ';

		if (width    != '') output += 'width="'    + width    + '" ';
		if (height   != '') output += 'height="'   + height   + '" ';
		if (lat      != '') output += 'lat="'      + lat      + '" ';
		if (lng      != '') output += 'lng="'      + lng      + '" ';
		if (zoom     != '') output += 'zoom="'     + zoom     + '" ';
		if (view     != '') output += 'view="'     + view     + '" ';
		if (layer    != '') output += 'layer="'    + layer    + '" ';
		if (wheel    != '') output += 'wheel="'    + wheel    + '" ';
		if (marker   != '') output += 'marker="'   + marker   + '" ';
		if (lazyload != '') output += 'lazyload="' + lazyload + '" ';

		output += '/]';

		// Once the composition of the command shortcode 
		// recall methods for inclusion in TinyMCE editor

		SZGoogleEditor.execCommand('mceReplaceContent',false,output);
		tinyMCEPopup.close();
	}
};

// Initialize the dialog and TinyMCE also call 
// the init routine for the initial operations

tinyMCEPopup.onInit.add(SZGoogleDialog.init,SZGoogleDialog);