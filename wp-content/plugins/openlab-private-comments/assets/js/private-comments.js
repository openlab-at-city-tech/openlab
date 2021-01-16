jQuery( document ).ready( function( $ ) {
	/**
	 * When responding to a private comment, comment privacy should be enforced.
	 */
	$( document ).on( 'click', '.comment-reply-link', function() {
		var parent = $( this ).closest( '.comment' );
		var privateCheckbox = parent.find( '#ol-private-comment' );

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
	$( document ).on( 'click', '.ol-private-comment-toggle', function( event ) {
		event.preventDefault();

		$( this )
			.closest( '.ol-private-comment-display' )
			.toggleClass( 'ol-private-comment-hidden' );
	} );
} );
