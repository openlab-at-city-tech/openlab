/**
 * ------------------------
 * AcademiaThemes Admin Scripts
 * ------------------------
 */
var $ = window.jQuery;

window.AcademiaAdmin = {
	init : function() {
		var self = this;

		self.widgetsPage();
	},
	widgetsPage : function() {
		/* Hide sidebars on the right page */
		if( ! $( 'body' ).hasClass( 'widgets-php' ) ) return;

	jQuery(document).ready(function($) {
    	var academiathemes_widget_regexp = /academia/;

	    $('.widget').filter(function () {
	        return academiathemes_widget_regexp.test(this.id);
	    }).addClass('academiathemes_widget_style');
	});

	},
}

$( document ).ready( function ( $ ) {

	/**
	Custom Image upload for widgets
	*/

	$(document).on("click", ".upload_image_button", function (event) {

		event.preventDefault();
		var $button = $(this);

		// Create the media frame.
		var file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select or upload image',
			library: { // remove these to show all
				type: 'image' // specific mime
			},
			button: {
				text: 'Select'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on('select', function () {

			var attachment = file_frame.state().get('selection').first().toJSON();
			$button.siblings('input').val(attachment.id).change();

		});

		// Finally, open the modal
		file_frame.open();
	});

	var academiathemesadmin = window.AcademiaAdmin;
	academiathemesadmin.init();

});