document.addEventListener( 'DOMContentLoaded', function () {
	try {
		const form = document.querySelector( '#respond form' );
		if ( form ) {
			// This is a dirty method, but there is no hook in WordPress to add attributes to the commenting form.
			form.setAttribute( 'enctype', 'multipart/form-data' );

			const attachmentArea = document.querySelector(
				'.comment-form-attachment'
			);
			if ( attachmentArea ) {
				attachmentArea.addEventListener( 'dragenter', dragEnter );
				attachmentArea.addEventListener( 'dragover', dragOver );
				attachmentArea.addEventListener( 'dragleave', dragLeave );
				attachmentArea.addEventListener( 'drop', drop );

				let counter = 0;

				function dragEnter( e ) {
					e.preventDefault();

					counter++;
					attachmentArea.classList.add( 'dragenter' );
				}

				function dragOver( e ) {
					e.preventDefault();
				}

				function dragLeave( e ) {
					e.preventDefault();

					counter--;
					if ( 0 === counter ) {
						attachmentArea.classList.remove( 'dragenter' );
					}
				}

				function drop( e ) {
					e.preventDefault();

					document.querySelector(
						'.comment-form-attachment__input'
					).files = e.dataTransfer.files;
					counter = 0;
					attachmentArea.classList.remove( 'dragenter' );
				}
			}
		} else {
			// eslint-disable-next-line no-undef
			throw new Error( dco_ca.commenting_form_not_found );
		}
	} catch ( e ) {
		// eslint-disable-next-line no-console
		console.log( e );
	}
} );
