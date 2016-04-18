// Definition of functions to be used in forms related
// to the widget and shortcode in Administration mode

var szgoogle_media_frame;

// FORM SELECT Function called directly after the composition 
// of widgets that have some fields with the switch hidden

function szgoogle_switch_hidden_onload(id) {
	jQuery('#' + id + ' .sz-google-switch-hidden').each(function() {
			szgoogle_switch_hidden_onchange(this);
	});
}

function szgoogle_switch_hidden_onchange(clicked) 
{
	var dataopen  = jQuery(clicked).data('open');
	var dataclose = jQuery(clicked).data('close');
	var classname = '.' + jQuery(clicked).data('switch');

	// Check if you have defined the function for operation "open" 
	// so if the value of the select is indicated close the class

	if (jQuery(clicked).attr('data-open')) {
		if (clicked.value == dataopen) jQuery(clicked).parents('form:first').find(classname).slideDown();
			else jQuery(clicked).parents('form:first').find(classname).slideUp();
	}

	// Check if you have defined the function to do "close"
	// so if the value of the select is to open the specified class

	if (jQuery(clicked).attr('data-close')) {
		if (clicked.value == dataclose) jQuery(clicked).parents('form:first').find(classname).slideUp();
			else jQuery(clicked).parents('form:first').find(classname).slideDown();
	}
}

// FORM CHECKBOX - Codice per implementare la visualizzazione di
// divisioni nascoste in base al valore del checkbox selezionato

function szgoogle_checks_hidden_onload(id) {
	jQuery('#' + id + ' .sz-google-checks-hidden').each(function() {
		szgoogle_checks_hidden_onchange(this);
	});
}

function szgoogle_checks_hidden_onchange(clicked) 
{
	var classname = '.' + jQuery(clicked).data('switch');

	if (jQuery(clicked).is(':checked')) jQuery(clicked).parents('form:first').find(classname).prop('readonly',true);
		else jQuery(clicked).parents('form:first').find(classname).prop('readonly',false);
}

// FORM SELECT Code to implement the display of hidden 
// divisions based on the value of the select selected

function szgoogle_switch_select_onload(id) {
	jQuery('#' + id + ' .sz-google-row-select').each(function() {
		szgoogle_switch_select_onchange(this);
	});
}

function szgoogle_switch_select_onchange(clicked) 
{
	var dataopen  = jQuery('option:selected',clicked).data('open');
	var classname = '.sz-google-row-tab';
	var classview = classname + '-' + dataopen;

	jQuery(clicked).parents('form:first').find(classname).hide();
	jQuery(clicked).parents('form:first').find(classview).slideDown();
}

// Code to implement a call to the media uploader 
// selection attachments connected to the keys file

function szgoogle_upload_select_media() 
{
	jQuery('.sz-upload-image-button').on('click',function() {

		var element   = jQuery(this); 
		var classname = '.' + jQuery(this).data('field-url');

		if (typeof(szgoogle_media_frame)!=='undefined') {
			szgoogle_media_frame.close();
		}

		// Create frames to select the files to be attached to the 
		// link parameter setting for average characteristics uploader

		szgoogle_media_frame = wp.media.frames.customHeader = wp.media({
			frame: 'select',
			title: jQuery(element).data('title'),
			button: {
				text: jQuery(element).data('button-text'),
				close:true
			},
			multiple:false
		});

		// A callback function that is called when the 
		// selection is confirmed on the resource library

		szgoogle_media_frame.on('select',function() {
			attachment = szgoogle_media_frame.state().get('selection').first().toJSON();
			jQuery(element).parents('form:first').find(classname).val(attachment.url);
		});

		// Call the standard function for opening 
		// the popup media uploader (see documentation)

		szgoogle_media_frame.open();
	});
}