jQuery( document ).ready( function( $ ) {
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
