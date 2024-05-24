jQuery( function( $ ) {
	var btn = $( '#bp-data-export button' ),
		meta = $( '#bp-auto-export' ),
		doingExport = false,
		requestID, wpNonce, exporterCount;

	function setActionState( state ) {
		btn.removeClass( 'export-personal-data-processing export-personal-data-success export-personal-data-failed' );
		btn.addClass( state );
	}

	function onExportDoneSuccess( zipUrl ) {
		setActionState( 'export-personal-data-success' );

		btn.text( 'Export complete. Reloading page...' );
		window.location.href = window.location.href;
	}

	function onExportFailure( errorMessage ) {
		setActionState( 'export-personal-data-failed' );
		if ( errorMessage ) {
			btn.text( errorMessage );
		}
	}

	function setExportProgress( exporterIndex ) {
		var progress       = ( exporterCount > 0 ? exporterIndex / exporterCount : 0 );
		var progressString = Math.round( progress * 100 ).toString() + '%';

		doingExport = true;

		btn.text( 'Processing. ' + progressString + ' complete.' );
	}

	function doNextExport( exporterIndex, pageIndex ) {
		$.ajax(
			{
				url: window.ajaxurl,
				data: {
					action: 'wp-privacy-export-personal-data',
					exporter: exporterIndex,
					id: requestID,
					page: pageIndex,
					security: wpNonce,
					sendAsEmail: true,
					bpae: meta.val()
				},
				method: 'post'
			}
		).done( function( response ) {
			var responseData = response.data;

			if ( ! response.success ) {
				// e.g. invalid request ID
				onExportFailure( response.data );
				return;
			}

			if ( ! responseData.done ) {
				setTimeout( doNextExport( exporterIndex, pageIndex + 1 ) );
			} else {
				setExportProgress( exporterIndex );
				if ( exporterIndex < exporterCount ) {
					setTimeout( doNextExport( exporterIndex + 1, 1 ) );
				} else {
					doingExport = false;
					onExportDoneSuccess( responseData.url );
				}
			}
		}).fail( function( jqxhr, textStatus, error ) {
			// e.g. Nonce failure
			onExportFailure( error );
		});
	}

	function doDataRequest() {
		$.ajax(
			{
				url: window.ajaxurl,
				data: {
					action: 'bp-data-export',
					security: meta.val(),
				},
				method: 'post'
			}
		).done( function( response ) {
			var responseData = response.data;

			if ( ! response.success ) {
				// e.g. invalid request ID
				onExportFailure( response.data );
				return;
			}

			requestID = responseData.request_id;
			wpNonce = responseData.nonce;
			exporterCount = responseData.exporter_count;

			doNextExport( 1, 1 );

		}).fail( function( jqxhr, textStatus, error ) {
			// e.g. Nonce failure
			onExportFailure( error );
		});
	}

	$( '#bp-data-export' ).on('submit', function(event) {
		// Bail out of the form.
		event.preventDefault();
		event.returnValue = false;
		btn.prop( 'disabled', true );

		// And now, let's begin.
		setExportProgress( 0 );
		setActionState( 'export-personal-data-processing' );
		doDataRequest();
	});

	$(window).bind("beforeunload",function(event) {
		if ( ! doingExport ) {
			return undefined;
		}

		// Custom messages don't work anymore, but anyway...
		return 'Your data export is currently processing. Are you sure you want to leave this page?';
	} );
} );