document.addEventListener( 'DOMContentLoaded', function() {
	// This is a dirty method, but there is no hook in WordPress to add attributes to the commenting form.
	document
		.getElementById( 'respond' )
		.querySelector( 'form' )
		.setAttribute( 'enctype', 'multipart/form-data' );
} );
