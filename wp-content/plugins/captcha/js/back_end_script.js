(function( $ ) {
	$(document).ready(function() {
		$( 'input.insert-tag-captcha' ).click( function() {
			var $form = $( this ).closest( 'form.tag-generator-panel' );
			var tag = $form.find( 'input.captcha' ).val();
			//wpcf7.taggen.insert( tag ); it  does not work on  contact form 7 version 4.6
			var val = $('#wpcf7-form').val();
			var val = tag + val;
			$('#wpcf7-form').val(val);
			tb_remove(); // close thickbox
			return false;
		} );
	});
})(jQuery);