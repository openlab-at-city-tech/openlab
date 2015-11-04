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

		var type     = jQuery('#ID_type'    ).val();
		var topic    = jQuery('#ID_topic'   ).val();
		var width    = jQuery('#ID_width'   ).val();
		var align    = jQuery('#ID_align'   ).val();
		var text     = jQuery('#ID_text'    ).val();
		var img      = jQuery('#ID_img'     ).val();
		var position = jQuery('#ID_position').val();
		var profile  = jQuery('#ID_profile' ).val();
		var email    = jQuery('#ID_email'   ).val();

		var logged   = jQuery("#MCE input[name='NAME_logged']:checked").val();
		var guest    = jQuery("#MCE input[name='NAME_guest' ]:checked").val();

		if (jQuery('#ID_badge'  ).val() == '0') text     = '';
		if (jQuery('#ID_badge'  ).val() == '0') img      = '';
		if (jQuery('#ID_badge'  ).val() == '0') position = '';

		if (jQuery('#ID_width_auto' ).is(':checked')) width  = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-hangouts-start ';

		if (type     != '') output += 'type="'     + type     + '" ';
		if (topic    != '') output += 'topic="'    + topic    + '" ';
		if (width    != '') output += 'width="'    + width    + '" ';
		if (align    != '') output += 'align="'    + align    + '" ';
		if (text     != '') output += 'text="'     + text     + '" ';
		if (img      != '') output += 'img="'      + img      + '" ';
		if (position != '') output += 'position="' + position + '" ';
		if (profile  != '') output += 'profile="'  + profile  + '" ';
		if (email    != '') output += 'email="'    + email    + '" ';
		if (logged   != '') output += 'logged="'   + logged   + '" ';
		if (guest    != '') output += 'guest="'    + guest    + '" ';

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