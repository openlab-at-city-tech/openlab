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

		var channel    = jQuery('#ID_channel').val();
		var layout     = jQuery('#ID_layout' ).val();
		var theme      = jQuery('#ID_theme'  ).val();
		var align      = jQuery('#ID_align'  ).val();

		var subscriber = jQuery("#MCE input[name='NAME_subscriber']:checked").val();

		if (jQuery('#ID_method').val() == '1') channel = '';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-ytbutton ';

		if (channel    != '') output += 'channel="'    + channel    + '" ';
		if (layout     != '') output += 'layout="'     + layout     + '" ';
		if (theme      != '') output += 'theme="'      + theme      + '" ';
		if (subscriber != '') output += 'subscriber="' + subscriber + '" ';
		if (align      != '') output += 'align="'      + align      + '" ';

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