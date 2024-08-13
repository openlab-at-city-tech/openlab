/**
 * jQuery plugin.
 */
export function jQueryPDFEmbedder( cmapURL ) {

	this.each( function( index, rawAContainer ) {

		var divContainer;

		var aContainer = jQuery( rawAContainer );

		if ( aContainer.is( 'a' ) ) {
			// Copy 'a' to a 'div' version
			var adata = aContainer.data();

			divContainer = jQuery( '<div></div>', {
				'class': aContainer.attr( 'class' ),
				'style': aContainer.attr( 'style' ),
			} );

			divContainer.data(
				jQuery.extend( { 'pdf-url': aContainer.attr( 'href' ) }, adata ),
			);

			aContainer.replaceWith( divContainer );
		}
		else {
			// It was a div all along.
			divContainer = aContainer;
		}

		divContainer.append(
			jQuery( '<div></div>', { 'class': 'pdfemb-loadingmsg' } ).append( document.createTextNode( pdfemb_trans.objectL10n.loading ) )
		);

		// Disable right click?
		if ( divContainer.data( 'disablerightclick' ) === 'on' || divContainer.data( 'disablerightclick' ) === '1' ) {
			divContainer.bind( 'contextmenu', function( e ) {
				e.preventDefault();
			} );
		}

		// Load PDF.
		var initPdfDoc = function( pdfDoc, showIsSecure ) {

			var pagesViewer = new window.PDFEMB_NS.pdfembPagesViewerUsable( pdfDoc, divContainer, showIsSecure );

			pagesViewer.setup();
		};

		var callback = function( pdf, showIsSecure ) {

			/**
			 * Asynchronously downloads PDF.
			 */
			if ( pdf === null ) {
				divContainer
					.empty()
					.append(
						jQuery( '<div></div>', { 'class': 'pdfemb-errormsg' } )
							.append(
								jQuery( '<span></span>' ).append(
									document.createTextNode( 'Failed to load and decrypt PDF' ),
								),
							),
					);

				return;
			}

			let params = {};

			params.url = pdf;
			params.cMapUrl = cmapURL;
			// Do not allow scripts execution in FontMatrix used for rendering glyphs.
			params.isEvalSupported = false;

			var loadingTask = pdfjsLib.getDocument( params, cmapURL );

			loadingTask.promise.then(
				function( pdfDoc_ ) {
					// you can now use *pdf* here
					initPdfDoc( pdfDoc_, showIsSecure );
				},
				function( e ) {
					var msgnode = document.createTextNode( e.message );

					if ( e.name === 'UnknownErrorException' && e.message === 'Failed to fetch' ) {
						// "Failed to fetch" - probably cross-domain issue
						msgnode = jQuery( '<span></span>' )
							.append(
								document.createTextNode( e.message + ' ' + pdfemb_trans.objectL10n.domainerror + ' ' )
							)
							.append(
								// TODO: this should come straight from PHP.
								jQuery( '<a href="https://wp-pdf.com/kb/error-url-to-the-pdf-file-must-be-on-exactly-the-same-domain-as-the-current-web-page/?utm_campaign=liteplugin&utm_source=WordPress&utm_medium=Viewer&utm_content=Failed+to+fetch" target="_blank">'
									+ pdfemb_trans.objectL10n.clickhereinfo + '</a>' )
							);
					}
					divContainer.empty().append( jQuery( '<div></div>', { 'class': 'pdfemb-errormsg' } ).append( msgnode ) );
				},
			);

		};

		if ( divContainer.data( 'pdfDoc' ) ) {
			initPdfDoc( divContainer.data( 'pdfDoc' ), divContainer.data( 'showIsSecure' ) );
		}
		else {
			var url = divContainer.data( 'pdf-url' );
			window.PDFEMB_NS.pdfembGetPDF( url, callback );
		}
	} );

	return this;
}
