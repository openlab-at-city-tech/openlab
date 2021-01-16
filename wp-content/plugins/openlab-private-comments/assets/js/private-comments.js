jQuery( document ).ready( function( $ ) {
	/**
	 * When responding to a private comment, comment privacy should be enforced.
	 */
	$( '.comment-reply-link' ).on( 'click', function( event ) {
		var parent = $( this ).closest( '.comment' );
		var privateCheckbox = $( '#ol-private-comment' );

		console.log( event );
		console.log( parent );
		console.log( privateCheckbox );

		if ( parent.length && parent.find( '.ol-private-comment-notice' ).length ) {
			privateCheckbox.prop( 'checked', true );
			privateCheckbox.prop( 'disabled', true );
		} else {
			privateCheckbox.prop( 'checked', false );
			privateCheckbox.prop( 'disabled', false );
		}
	} );

	/**
	 * Toggle private comment visibility.
	 */
	$( '.ol-private-comment-toggle' ).on( 'click', function( event ) {
		event.preventDefault();

		$( this )
			.closest( '.ol-private-comment-display' )
			.toggleClass( 'ol-private-comment-hidden' );
	} );
} );
