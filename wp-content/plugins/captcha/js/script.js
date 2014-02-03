(function($) {
	$(document).ready( function() {
		$( '.cptch_help_box' ).mouseover( function() {
			$( this ).children().css( 'display', 'block' );
		});
		$( '.cptch_help_box' ).mouseout( function() {
			$( this ).children().css( 'display', 'none' );
		});
		/* add notice about changing in the settings page */
		$( '#cptch_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#cptch_settings_notice' ).css( 'display', 'block' );
			};
		});
	});
})(jQuery);