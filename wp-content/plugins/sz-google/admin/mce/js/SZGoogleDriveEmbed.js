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

		var type       = jQuery('#ID_type'      ).val();
		var id         = jQuery('#ID_id'        ).val();
		var width      = jQuery('#ID_width'     ).val();
		var height     = jQuery('#ID_height'    ).val();
		var gid        = jQuery('#ID_gid'       ).val();
		var range      = jQuery('#ID_range'     ).val();
		var delay      = jQuery('#ID_delay'     ).val();
		var folderview = jQuery('#ID_folderview').val();

		var single     = jQuery("#MCE input[name='NAME_single']:checked").val();
		var start      = jQuery("#MCE input[name='NAME_start' ]:checked").val();
		var loop       = jQuery("#MCE input[name='NAME_loop'  ]:checked").val();

		if (jQuery('#ID_width_auto' ).is(':checked')) width  = 'auto';
		if (jQuery('#ID_height_auto').is(':checked')) height = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		if (type != 'spreadsheet')  single     = '';
		if (type != 'spreadsheet')  gid        = '';
		if (type != 'spreadsheet')  range      = '';

		if (type != 'presentation') start      = '';
		if (type != 'presentation') loop       = '';
		if (type != 'presentation') delay      = '';

		if (type != 'folder')       folderview = '';

		// Composizione shortcode selezionato con elenco
		// delle opzioni disponibili e valore associato

		output = '[sz-drive-embed ';

		if (type       != '') output += 'type="'      + type       + '" ';
		if (id         != '') output += 'id="'        + id         + '" ';
		if (width      != '') output += 'width="'     + width      + '" ';
		if (height     != '') output += 'height="'    + height     + '" ';
		if (gid        != '') output += 'gid="'       + gid        + '" ';
		if (range      != '') output += 'range="'     + range      + '" ';
		if (delay      != '') output += 'delay="'     + delay      + '" ';
		if (single     != '') output += 'single="'    + single     + '" ';
		if (folderview != '') output += 'folderview="'+ folderview + '" ';
		if (start      != '') output += 'start="'     + start      + '" ';
		if (loop       != '') output += 'loop="'      + loop       + '" ';

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