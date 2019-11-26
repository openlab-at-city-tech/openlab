(function($) {
	
	$(document).ready(function() {
		
		// Toggle prefix id field
		toggleField( $( '#fixedtoc_option_general_title_to_id' ) );
		
		function toggleField( ele ) {
			toggle( ele );
			
			ele.change( function() {
				toggle( $( this ) );
			} );
			
			function toggle( ele ) {
				var currentTr = ele.parents( 'tr' );
				var nextTr = currentTr.next( 'tr' );

				if ( ele.is( ':checked' ) ) {
					nextTr.show(200);
				} else {
					nextTr.hide(200);
				}			
			}
		}
		
	});
	
})(jQuery);