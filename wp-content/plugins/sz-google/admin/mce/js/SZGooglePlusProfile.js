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

		var id      = jQuery('#ID_specific').val();
		var type    = jQuery('#ID_type'    ).val();
		var width   = jQuery('#ID_width'   ).val();
		var align   = jQuery('#ID_align'   ).val();
		var text    = jQuery('#ID_text'    ).val();
		var image   = jQuery('#ID_image'   ).val();

		var layout  = jQuery("#MCE input[name='NAME_layout' ]:checked").val();
		var theme   = jQuery("#MCE input[name='NAME_theme'  ]:checked").val();
		var cover   = jQuery("#MCE input[name='NAME_cover'  ]:checked").val();
		var tagline = jQuery("#MCE input[name='NAME_tagline']:checked").val();
		var author  = jQuery("#MCE input[name='NAME_author' ]:checked").val();

		if (jQuery('#ID_method').val() == '1')       id    = '';
		if (jQuery('#ID_width_auto').is(':checked')) width = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-gplus-profile ';

		// Composizione opzioni che sono collegate al
		// tipo di badge nel formato standard 

		if (type == 'standard') {
			if (type    != '') output += 'type="'    + type    + '" ';
			if (id      != '') output += 'id="'      + id      + '" ';
			if (width   != '') output += 'width="'   + width   + '" ';
			if (align   != '') output += 'align="'   + align   + '" ';
			if (layout  != '') output += 'layout="'  + layout  + '" ';
			if (theme   != '') output += 'theme="'   + theme   + '" ';
			if (cover   != '') output += 'cover="'   + cover   + '" ';
			if (tagline != '') output += 'tagline="' + tagline + '" ';
			if (author  != '') output += 'author="'  + author  + '" ';
		}

		// Composizione opzioni che sono collegate al tipo
		// di badge nel formato popup che si attiva in hover

		if (type == 'popup') {
			if (type    != '') output += 'type="'    + type    + '" ';
			if (id      != '') output += 'id="'      + id      + '" ';
			if (text    != '') output += 'text="'    + text    + '" ';
			if (image   != '') output += 'image="'   + image   + '" ';
		}

		output += '/]';

		// Once the composition of the command shortcode 
		// recall methods for inclusion in TinyMCE editor

		SZGoogleEditor.execCommand('mceReplaceContent',false,output);
		tinyMCEPopup.close();
	}
}

// Initialize the dialog and TinyMCE also call 
// the init routine for the initial operations

tinyMCEPopup.onInit.add(SZGoogleDialog.init,SZGoogleDialog);