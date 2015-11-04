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

		var calendar      = jQuery('#ID_calendar' ).val();
		var title         = jQuery('#ID_calendarT').val();
		var width         = jQuery('#ID_width'    ).val();
		var height        = jQuery('#ID_height'   ).val();
		var mode          = jQuery('#ID_mode'     ).val();
		var weekstart     = jQuery('#ID_weekstart').val();
		var language      = jQuery('#ID_language' ).val();
		var timezone      = jQuery('#ID_timezone' ).val();

		var showtitle     = jQuery("#MCE input[name='NAME_showtitle'    ]:checked").val();
		var shownavs      = jQuery("#MCE input[name='NAME_shownavs'     ]:checked").val();
		var showdate      = jQuery("#MCE input[name='NAME_showdate'     ]:checked").val();
		var showprint     = jQuery("#MCE input[name='NAME_showprint'    ]:checked").val();
		var showcalendars = jQuery("#MCE input[name='NAME_showcalendars']:checked").val();
		var showtimezone  = jQuery("#MCE input[name='NAME_showtimezone' ]:checked").val();

		if (jQuery('#ID_width_auto' ).is(':checked')) width  = 'auto';
		if (jQuery('#ID_height_auto').is(':checked')) height = 'auto';

		if (language == '99') language = '';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-calendar ';

		if (calendar      != '') output += 'calendar="'      + calendar      + '" ';
		if (title         != '') output += 'title="'         + title         + '" ';
		if (mode          != '') output += 'mode="'          + mode          + '" ';
		if (weekstart     != '') output += 'weekstart="'     + weekstart     + '" ';
		if (language      != '') output += 'language="'      + language      + '" ';
		if (timezone      != '') output += 'timezone="'      + timezone      + '" ';
		if (width         != '') output += 'width="'         + width         + '" ';
		if (height        != '') output += 'height="'        + height        + '" ';
		if (showtitle     != '') output += 'showtitle="'     + showtitle     + '" ';
		if (shownavs      != '') output += 'shownavs="'      + shownavs      + '" ';
		if (showdate      != '') output += 'showdate="'      + showdate      + '" ';
		if (showprint     != '') output += 'showprint="'     + showprint     + '" ';
		if (showcalendars != '') output += 'showcalendars="' + showcalendars + '" ';
		if (showtimezone  != '') output += 'showtimezone="'  + showtimezone  + '" ';

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