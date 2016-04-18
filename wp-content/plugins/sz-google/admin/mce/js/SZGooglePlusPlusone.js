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

		var url        = jQuery('#ID_url'       ).val();
		var size       = jQuery('#ID_size'      ).val();
		var align      = jQuery('#ID_align'     ).val();
		var annotation = jQuery('#ID_annotation').val();
		var text       = jQuery('#ID_text'      ).val();
		var img        = jQuery('#ID_img'       ).val();
		var position   = jQuery('#ID_position'  ).val();

		if (jQuery('#ID_urltype').val() == '0') url      = '';
		if (jQuery('#ID_badge'  ).val() == '0') text     = '';
		if (jQuery('#ID_badge'  ).val() == '0') img      = '';
		if (jQuery('#ID_badge'  ).val() == '0') position = '';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-gplus-one ';

		if (url        != '') output += 'url="'        + url        + '" ';
		if (size       != '') output += 'size="'       + size       + '" ';
		if (align      != '') output += 'align="'      + align      + '" ';
		if (annotation != '') output += 'annotation="' + annotation + '" ';
		if (text       != '') output += 'text="'       + text       + '" ';
		if (img        != '') output += 'img="'        + img        + '" ';
		if (position   != '') output += 'position="'   + position   + '" ';

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