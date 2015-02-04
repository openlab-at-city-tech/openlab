jQuery(document).ready(function($) {

	var previous = $('#pw-esp-expiration').val();

	$('#pw-esp-expiration').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$('#pw-esp-edit-expiration, .pw-esp-hide-expiration').click(function(e) {

		e.preventDefault();

		var date = $('#pw-esp-expiration').val();

		if( $(this).hasClass('cancel') ) {

			$('#pw-esp-expiration').val( previous );

		} else if( date ) {

			$('#pw-esp-expiration-label').text( $('#pw-esp-expiration').val() );

		}

		$('#pw-esp-expiration-field').slideToggle();

	});
});