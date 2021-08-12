// eslint-disable-next-line @wordpress/no-global-event-listener
document.addEventListener( 'DOMContentLoaded', function () {
	// This is a dirty method, but there is no hook in WordPress to add attributes to the commenting form.
	try {
		const form = document.querySelector( '#respond form' );
		if ( form ) {
			form.setAttribute( 'enctype', 'multipart/form-data' );
		} else {
			// eslint-disable-next-line no-undef
			throw new Error( dco_ca.commenting_form_not_found );
		}
	} catch ( e ) {
		// eslint-disable-next-line no-console
		console.log( e );
	}
} );
