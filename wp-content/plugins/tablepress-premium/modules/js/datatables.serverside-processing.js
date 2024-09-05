/*
 * Pipelining function for DataTables. To be used in the `ajax` option of DataTables.
 * @link https://datatables.net/examples/server_side/pipeline.html
 */

/* globals jQuery */

( function( $ ) {

	$.fn.dataTable.pipeline = function( opts ) {
		// Configuration options
		const conf = $.extend(
			{
				pages: 5, // number of pages to cache
				url: '', // script url
				data: null, // function or object with parameters to send to the server
				// matching how `ajax.data` works in DataTables
				method: 'GET', // Ajax HTTP method
			},
			opts
		);

		// Private variables for storing the cache
		let cacheLower = -1;
		let cacheUpper = null;
		let cacheLastRequest = null;
		let cacheLastJson = null;

		return function( request, drawCallback, settings ) {
			let ajax = false;
			let requestStart = request.start;
			const drawStart = request.start;
			const requestLength = request.length;
			const requestEnd = requestStart + requestLength;

			if ( settings.clearCache ) {
				// API requested that the cache be cleared
				ajax = true;
				settings.clearCache = false;
			} else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
				// outside cached data - need to make a request
				ajax = true;
			} else if (
				JSON.stringify( request.order ) !== JSON.stringify( cacheLastRequest.order ) ||
				JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
				JSON.stringify( request.search ) !== JSON.stringify( cacheLastRequest.search )
			) {
				// properties changed (ordering, columns, searching)
				ajax = true;
			}

			// Store the request for checking next time around
			cacheLastRequest = $.extend( true, {}, request );

			if ( ajax ) {
				// Need data from the server
				if ( requestStart < cacheLower ) {
					requestStart = requestStart - requestLength * ( conf.pages - 1 );

					if ( requestStart < 0 ) {
						requestStart = 0;
					}
				}

				cacheLower = requestStart;
				cacheUpper = requestStart + requestLength * conf.pages;

				request.start = requestStart;
				request.length = requestLength * conf.pages;

				// Provide the same `data` options as DataTables.
				if ( typeof conf.data === 'function' ) {
					// As a function it is executed with the data object as an arg
					// for manipulation. If an object is returned, it is used as the
					// data object to submit
					const d = conf.data( request );
					if ( d ) {
						$.extend( request, d );
					}
				} else if ( $.isPlainObject( conf.data ) ) {
					// As an object, the data given extends the default
					$.extend( request, conf.data );
				}

				return $.ajax( {
					type: conf.method,
					url: conf.url,
					data: request,
					dataType: 'json',
					cache: false,
					success( json ) {
						cacheLastJson = $.extend( true, {}, json );

						if ( cacheLower !== drawStart ) {
							json.data.splice( 0, drawStart - cacheLower );
						}
						if ( requestLength >= -1 ) {
							json.data.splice( requestLength, json.data.length );
						}

						drawCallback( json );
					},
				} );
			} else { // eslint-disable-line no-else-return
				const json = $.extend( true, {}, cacheLastJson );
				json.draw = request.draw; // Update the echo for each response
				json.data.splice( 0, requestStart - cacheLower );
				json.data.splice( requestLength, json.data.length );

				drawCallback( json );
			}
		};
	};

	// Register an API method that will empty the pipelined data, forcing an Ajax
	// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
	$.fn.dataTable.Api.register( 'clearPipeline()', function() {
		return this.iterator('table', function( settings ) {
			settings.clearCache = true;
		} );
	} );

}( jQuery ) );
