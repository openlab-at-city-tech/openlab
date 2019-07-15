/**
 * Custom JavaScript functions for the customizer controls.
 */

( function( $ ) {

	// Multiple checkboxes: Add the values of the checked checkboxes to the hidden input
	$( '.customize-control-checkbox-multiple input[type="checkbox"]' ).live( 'change', function() {

		// Get the values of all of the checkboxes into a comma seperated variable
		checkbox_values = $( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
			function() {
				return this.value;
			}
		).get().join( ',' );

		// If there are no values, make that explicit in the variable so we know whether the default output is needed
		if ( ! checkbox_values ) {
			checkbox_values = 'empty';
		}

		// Update the hidden input with the variable
		$( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );

	} );

} )( jQuery );
