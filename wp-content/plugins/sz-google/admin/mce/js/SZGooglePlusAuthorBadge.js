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

		var link      = jQuery('#ID_link'     ).val();
		var cover     = jQuery('#ID_cover'    ).val();
		var photo     = jQuery('#ID_photo'    ).val();
		var biography = jQuery('#ID_biography').val();
		var width     = jQuery('#ID_width'    ).val();

		if (jQuery('#ID_width_auto').is(':checked')) width = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-gplus-author ';

		if (link      != '') output += 'link="'      + link      + '" ';
		if (cover     != '') output += 'cover="'     + cover     + '" ';
		if (photo     != '') output += 'photo="'     + photo     + '" ';
		if (biography != '') output += 'biography="' + biography + '" ';
		if (width     != '') output += 'width="'     + width     + '" ';

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