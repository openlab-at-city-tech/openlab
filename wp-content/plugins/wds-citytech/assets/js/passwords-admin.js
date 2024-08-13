/* global jQuery */
(function($){
	$(document).ready(function(){
		const $passwordExpiration = $('#password-expiration');

		$( '#set-password-expiration' ).click(function(e){
			e.preventDefault();

			const now = new Date();
			const formattedDate = now.toISOString().slice( 0, 16 );

			$passwordExpiration.val( formattedDate );
		});

		$( '#clear-password-expiration' ).click(function(e){
			e.preventDefault();

			$passwordExpiration.val( '' );
		});

	});
})(jQuery);
