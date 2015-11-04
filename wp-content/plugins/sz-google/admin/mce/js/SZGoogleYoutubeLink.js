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

		var channel      = jQuery('#ID_channel').val();
		var text         = jQuery('#ID_text'   ).val();
		var image        = jQuery('#ID_image'  ).val();

		var subscription = jQuery("#MCE input[name='NAME_subscription']:checked").val();
		var newtab       = jQuery("#MCE input[name='NAME_newtab'      ]:checked").val();

		if (jQuery('#ID_method').val() == '1') channel = '';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-ytlink ';

		if (channel      != '') output += 'channel="'      + channel      + '" ';
		if (subscription != '') output += 'subscription="' + subscription + '" ';
		if (text         != '') output += 'text="'         + text         + '" ';
		if (image        != '') output += 'image="'        + image        + '" ';
		if (newtab       != '') output += 'newtab="'       + newtab       + '" ';

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