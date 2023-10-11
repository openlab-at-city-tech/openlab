( function( $ ) {
	// Change all h5 elements in .post-nav to div.post-nav-type-label.
	$( '.post-nav h5' ).each( function() {
		$( this ).replaceWith( '<div class="post-nav-type-label">' + $( this ).html() + '</div>' );
	} );
}( jQuery ) );
