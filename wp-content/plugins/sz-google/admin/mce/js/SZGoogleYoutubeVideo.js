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

		var output  = '';

		var url             = jQuery('#ID_url'   ).val();
		var width           = jQuery('#ID_width' ).val();
		var height          = jQuery('#ID_height').val();
		var theme           = jQuery('#ID_theme' ).val();
		var cover           = jQuery('#ID_cover' ).val();
		var start           = jQuery('#ID_start' ).val();
		var end             = jQuery('#ID_end'   ).val();
		
		var responsive      = jQuery("#MCE input[name='NAME_responsive'     ]:checked").val();
		var autoplay        = jQuery("#MCE input[name='NAME_autoplay'       ]:checked").val();
		var loop            = jQuery("#MCE input[name='NAME_loop'           ]:checked").val();
		var fullscreen      = jQuery("#MCE input[name='NAME_fullscreen'     ]:checked").val();
		var disablekeyboard = jQuery("#MCE input[name='NAME_disablekeyboard']:checked").val();
		var disableiframe   = jQuery("#MCE input[name='NAME_disableiframe'  ]:checked").val();
		var disablerelated  = jQuery("#MCE input[name='NAME_disablerelated' ]:checked").val();
		var delayed         = jQuery("#MCE input[name='NAME_delayed'        ]:checked").val();
		var schemaorg       = jQuery("#MCE input[name='NAME_schemaorg'      ]:checked").val();

		if (responsive == 'y') width  = '';
		if (responsive == 'y') height = '';	

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-ytvideo ';

		if (url             != '') output += 'url="'             + url             + '" ';
		if (width           != '') output += 'width="'           + width           + '" ';
		if (height          != '') output += 'height="'          + height          + '" ';
		if (theme           != '') output += 'theme="'           + theme           + '" ';
		if (cover           != '') output += 'cover="'           + cover           + '" ';
		if (start           != '') output += 'start="'           + start           + '" ';
		if (end             != '') output += 'end="'             + end             + '" ';
		if (responsive      != '') output += 'responsive="'      + responsive      + '" ';
		if (autoplay        != '') output += 'autoplay="'        + autoplay        + '" ';
		if (loop            != '') output += 'loop="'            + loop            + '" ';
		if (fullscreen      != '') output += 'fullscreen="'      + fullscreen      + '" ';
		if (disablekeyboard != '') output += 'disablekeyboard="' + disablekeyboard + '" ';
		if (disableiframe   != '') output += 'disableiframe="'   + disableiframe   + '" ';
		if (disablerelated  != '') output += 'disablerelated="'  + disablerelated  + '" ';
		if (delayed         != '') output += 'delayed="'         + delayed         + '" ';
		if (schemaorg       != '') output += 'schemaorg="'       + schemaorg       + '" ';

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