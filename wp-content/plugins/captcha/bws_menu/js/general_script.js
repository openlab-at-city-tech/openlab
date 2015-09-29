(function($) {
	$( document ).ready( function() {
		/**
		 * add notice about changing on the settings page 
		 */
		$( '.bws_form input, .bws_form textarea, .bws_form select' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' && ! $( this ).hasClass( 'bws_no_bind_notice' ) ) {
				$( '.updated.fade, .error' ).css( 'display', 'none' );
				$( '#bws_save_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '.bws_save_anchor' ).on( "click", function( event ) {
			event.preventDefault();
			$( '.bws_form #bws-submit-button' ).click();
		});		
	});
})(jQuery);
