jQuery(document).ready(function( $ ){		
	function anr_admin_show_hide_fields(){
		var selected_value = $('#anr_admin_options_captcha_version').val();
		$( '.hidden' ).hide();
		$( '.anr-show-field-for-'+ selected_value ).show('slow');
	}
	if( $('#anr_admin_options_captcha_version').length ){
		anr_admin_show_hide_fields();
	}
	
	$('.form-table').on( "change", "#anr_admin_options_captcha_version", function(e) {
		anr_admin_show_hide_fields();
	});
});