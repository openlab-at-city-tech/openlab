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

		var name            = jQuery('#ID_name'   ).val();
		var domain          = jQuery('#ID_domain' ).val();
		var width           = jQuery('#ID_width'  ).val();
		var height          = jQuery('#ID_height' ).val();

		var showsearch      = jQuery("#MCE input[name='NAME_showsearch'    ]:checked").val();
		var showtabs        = jQuery("#MCE input[name='NAME_showtabs'      ]:checked").val();
		var hideforumtitle  = jQuery("#MCE input[name='NAME_hideforumtitle']:checked").val();
		var hidesubject     = jQuery("#MCE input[name='NAME_hidesubject'   ]:checked").val();

		if (jQuery('#ID_width_auto' ).is(':checked')) width  = 'auto';
		if (jQuery('#ID_height_auto').is(':checked')) height = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-ggroups ';

		if (name           != '') output += 'name="'           + name           + '" ';
		if (domain         != '') output += 'domain="'         + domain         + '" ';
		if (width          != '') output += 'width="'          + width          + '" ';
		if (height         != '') output += 'height="'         + height         + '" ';
		if (showsearch     != '') output += 'showsearch="'     + showsearch     + '" ';
		if (showtabs       != '') output += 'showtabs="'       + showtabs       + '" ';
		if (hideforumtitle != '') output += 'hideforumtitle="' + hideforumtitle + '" ';
		if (hidesubject    != '') output += 'hidesubject="'    + hidesubject    + '" ';

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