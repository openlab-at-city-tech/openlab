(function($) {
	$(document).ready( function() {
		/*  add to whitelist my ip */
		$( 'input[name="cptch_add_to_whitelist_my_ip"]' ).change( function() {			
			if ( $( this ).is( ':checked' ) ) {
				var my_ip = $( 'input[name="cptch_add_to_whitelist_my_ip_value"]' ).val();
				$( 'input[name="cptch_add_to_whitelist"]' ).val( my_ip ).attr( 'readonly', 'readonly' );
			} else {
				$( 'input[name="cptch_add_to_whitelist"]' ).val( '' ).removeAttr( 'readonly' );
			}
		});
	});
})(jQuery);