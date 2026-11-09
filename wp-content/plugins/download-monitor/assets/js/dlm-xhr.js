/* eslint-disable camelcase */
/* eslint-disable no-undef */
jQuery( function( $ ) {
	// Let's initiate the XHR.
	new DLM_XHR_Download();
} );

// Inherits the button's own text color via currentColor - only used while
// dlmXHRgif still points at our own default spinner (see dlmXHRInlineSpinner).
const DLM_XHR_SPINNER_SVG = '<svg class="dlm-xhr-loading-gif dlm-xhr-spinner" viewBox="0 0 44 44" width="22" height="22" aria-hidden="true">' +
	'<circle cx="22" cy="22" r="18" fill="none" stroke="currentColor" stroke-width="6" opacity="0.25"></circle>' +
	'<circle class="dlm-spinner-arc" cx="22" cy="22" r="18" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-dasharray="28 200"></circle>' +
	'</svg>';

class DLM_XHR_Download {
	responsHeaders = {};

	constructor() {
		// dlmXHRinstance defined in inline script
		dlmXHRinstance = this;
		this.init();
	}

	init() {
		dlmXHRinstance.attachButtonEvent();
	}

	// Function to attach events for the download buttons
	attachButtonEvent() {
		// Stop propagation on child elements
		jQuery( 'html,body' ).on( 'click', '.dlm-no-access-modal-window > div', function( e ) {
			e.stopPropagation();
		} );
		// Close modal on these clicks
		jQuery( 'html, body' ).on( 'click', '.dlm-no-access-modal-overlay, .dlm-no-access-modal-close, .dlm-no-access-modal-window', function( e ) {
			e.stopPropagation();
			jQuery( '#dlm-no-access-modal' ).remove();
		} );
		// Close modal on ESC key press
		jQuery( document ).on( 'keydown', function( e ) {
			if ( e.key === 'Escape' ) {
				jQuery( '#dlm-no-access-modal' ).remove();
			}
		} );

		jQuery( 'html, body' ).on( 'click', 'a', function( e ) {
			let url = jQuery( this ).attr( 'href' );
			// Let's see if we need to do XHR download on this link.
			let noXHR = false;
			if ( jQuery( this ).hasClass( 'dlm-no-xhr-download' ) ) {
				noXHR = true;
			}

			if ( 'undefined' !== typeof dlmNonXHRGlobalLinks && dlmNonXHRGlobalLinks.length > 0 ) {
				if ( 'undefined' !== typeof url ) {
					dlmNonXHRGlobalLinks.forEach( ( element ) => {
						if ( url.indexOf( element ) >= 0 ) {
							noXHR = true;
						}
					} );
				}
			}

			if ( noXHR ) { // No XHR so return;
				jQuery( '#dlm-no-access-modal' ).remove(); // Close the modal also in case we opened it before.
				return;
			}

			if ( typeof url === 'string' ) {
				try {
					url = new URL( url, window.location.origin ).href;
				} catch ( e ) {}
			}

			jQuery( document ).trigger( 'dlm-xhr-download-button-click', [ url, this, dlmXHRGlobalLinks ] );
			if ( 'undefined' !== typeof url && dlmXHRinstance.isDownloadLink( url ) ) {
				const redirect_link = jQuery( this ).data( 'redirect' ),
					  target = jQuery( this ).attr( 'target' );
				// Check if the link has a target attribute, and if it does, we need to open it in a new window.
				if ( 'undefined' !== typeof redirect_link && true === redirect_link && '_blank' === target ) {
					// If we need to redirect/open in a new tab, then we do not need to do XHR, so return.
					return;
				}
				dlmXHRinstance.handleDownloadClick( this, e );
			}
		} );
	}

	// Same host + endpoint path (or query var when permalinks are off), not a substring match.
	isDownloadLink( url ) {
		try {
			const link = new URL( url, document.baseURI );
			const endpoint = new URL( dlmXHRGlobalLinks );

			if ( ! [ 'http:', 'https:' ].includes( link.protocol ) || link.host !== endpoint.host ) {
				return false;
			}

			if ( endpoint.search ) {
				const queryVar = endpoint.searchParams.keys().next().value;
				return link.pathname === endpoint.pathname && link.searchParams.has( queryVar );
			}

			return link.pathname.startsWith( endpoint.pathname );
		} catch ( e ) {
			return false;
		}
	}

	handleDownloadClick( obj, e ) {
		e.stopPropagation();
		const href = obj.getAttribute( 'href' );

		const triggerObject = {
			button: obj,
			href,
			buttonObj: jQuery( obj ),
		};

		// Show the progress bar after complete download also
		if ( triggerObject.href.indexOf( 'blob:http' ) !== -1 ) {
			/*triggerObject.buttonObj.addClass('download-100');
			 setTimeout(function () {
			 triggerObject.buttonObj.removeClass('download-100');
			 }, 1500);*/

			return;
		}

		if ( '#' === triggerObject.href ) {
			return;
		}

		e.preventDefault();
		dlmXHRinstance.retrieveBlob( triggerObject );
	}

	retrieveBlob( triggerObject ) {
		const instance = this;
		const {
			button,
			href,
			buttonObj,
		} = triggerObject;

		// This will hold the file as a local object URL
		let _OBJECT_URL;
		const request = new XMLHttpRequest();
		const $setCookie = dlmXHR.prevent_duplicates,
			  buttonTarget = buttonObj.attr( 'target' );
		let buttonClass = buttonObj.attr( 'class' );
		buttonClass = ( 'undefined' !== typeof buttonClass && '' !== buttonClass ) ? buttonClass.replace( 'dlm-download-started', '' ).replace( 'dlm-download-completed', '' ) : '';

		buttonObj.addClass( 'dlm-download-started' );
		button.setAttribute( 'href', '#' );
		button.removeAttribute( 'download' );
		button.setAttribute( 'disabled', 'disabled' );
		if ( dlmXHRProgress ) {
			if ( ! button.querySelector( '.dlm-original-content' ) ) {
				Array.from( button.childNodes ).forEach( ( node ) => {
					if ( node.nodeType === Node.ELEMENT_NODE ) {
						node.classList.add( 'dlm-original-content' );
					} else if ( node.nodeType === Node.TEXT_NODE && '' !== node.textContent.trim() ) {
						const wrapper = document.createElement( 'span' );
						wrapper.className = 'dlm-original-content';
						node.replaceWith( wrapper );
						wrapper.appendChild( node );
					}
				} );
			}
			if ( dlmXHRInlineSpinner ) {
				button.insertAdjacentHTML( 'beforeend', DLM_XHR_SPINNER_SVG );
			} else {
				const loadingGif = document.createElement( 'img' );
				loadingGif.src = dlmXHRgif;
				loadingGif.className = 'dlm-xhr-loading-gif';
				button.appendChild( loadingGif );
			}
			buttonObj.append( '<span class="dlm-xhr-progress">&nbsp;0%</span>' );
		}

		// Trigger the `dlm_download_triggered` action
		jQuery( document ).trigger( 'dlm_download_triggered', [ this, button, buttonObj, _OBJECT_URL, request ] );
		let check_headers = false;
		request.responseType = 'blob';
		request.onreadystatechange = function() {
			const {
				status,
				readyState,
				statusText,
			} = request;
			const headers = request
				.getAllResponseHeaders()
				.split( '\r\n' )
				.reduce( ( result, current ) => {
					const [ name, value ] = current.split( ': ' );
					result[ name ] = value;
					return result;
				}, {} );

			if ( Object.keys( headers ).length > 1 ) {
				check_headers = true;
				// Set the response headers to the instance so that we can use it later.
				instance.responseHeaders = headers;
			}

			let file_name = 'download';
			let dlmFilenameHeader = false,
				dlmNoWaypoints = false,
				dlmRedirectHeader = false,
				dlmExternalDownloadHeader = false,
				dlmNoAccessHeader = null,
				dlmNoAccessModalHeader = false,
				dlmErrorHeader = false,
				dlmDownloadIdHeader = false,
				dlmDownloadVersionHeader = false,
				dlmNoAccessModalTextHeader = false;

			/**
			 * Old headers check
			 */
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-file-name' ] ) {
				dlmFilenameHeader = instance.responseHeaders[ 'dlm-file-name' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-no-waypoints' ] ) {
				dlmNoWaypoints = true;
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-redirect' ] ) {
				dlmRedirectHeader = instance.responseHeaders[ 'dlm-redirect' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-external-download' ] ) {
				dlmExternalDownloadHeader = true;
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-no-access' ] ) {
				dlmNoAccessHeader = instance.responseHeaders[ 'dlm-no-access' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-no-access-modal' ] ) {
				dlmNoAccessModalHeader = instance.responseHeaders[ 'dlm-no-access-modal' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-error' ] ) {
				dlmErrorHeader = instance.responseHeaders[ 'dlm-error' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-download-id' ] ) {
				dlmDownloadIdHeader = instance.responseHeaders[ 'dlm-download-id' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-version-id' ] ) {
				dlmDownloadVersionHeader = instance.responseHeaders[ 'dlm-version-id' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'dlm-no-access-modal-text' ] ) {
				dlmNoAccessModalTextHeader = instance.responseHeaders[ 'dlm-no-access-modal-text' ];
			}
			// End old headers check

			/**
			 * New headers check
			 */
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-file-name' ] ) {
				dlmFilenameHeader = instance.responseHeaders[ 'x-dlm-file-name' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-no-waypoints' ] ) {
				dlmNoWaypoints = true;
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-redirect' ] ) {
				dlmRedirectHeader = instance.responseHeaders[ 'x-dlm-redirect' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-external-download' ] ) {
				dlmExternalDownloadHeader = true;
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-no-access' ] ) {
				dlmNoAccessHeader = instance.responseHeaders[ 'x-dlm-no-access' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-no-access-modal' ] ) {
				dlmNoAccessModalHeader = instance.responseHeaders[ 'x-dlm-no-access-modal' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-error' ] ) {
				dlmErrorHeader = instance.responseHeaders[ 'x-dlm-error' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-download-id' ] ) {
				dlmDownloadIdHeader = instance.responseHeaders[ 'x-dlm-download-id' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-version-id' ] ) {
				dlmDownloadVersionHeader = instance.responseHeaders[ 'x-dlm-version-id' ];
			}
			if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-no-access-modal-text' ] ) {
				dlmNoAccessModalTextHeader = instance.responseHeaders[ 'x-dlm-no-access-modal-text' ];
			}

			// End new headers check

			if ( dlmFilenameHeader ) {
				file_name = dlmFilenameHeader.replace( /\"/g, '' ).replace( ';', '' );
				file_name = decodeURIComponent( file_name );
			} else if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'content-disposition' ] ) {
				const contentDisposition = instance.responseHeaders[ 'content-disposition' ];
				const fileNameMatch = contentDisposition.match( /filename\*=UTF-8''([^;]+)/i ) || contentDisposition.match( /filename=([^;]+)/i );
				file_name = fileNameMatch ? fileNameMatch[ 1 ].replace( /"/g, '' ).trim() : file_name;
				// We use this method because we urlencoded it on the server so that characters like chinese or persian are not broken
				file_name = decodeURIComponent( file_name );
			}

			// Error translations
			if ( dlmErrorHeader ) {
				//var obj = { key: undefined };
				if ( dlmErrorHeader in dlmXHRtranslations ) {
					dlmErrorHeader = dlmXHRtranslations[ dlmErrorHeader ];
				} else {
					dlmErrorHeader = dlmXHRtranslations.error;
				}
			}

			// Let's check for DLM request headers
			if ( request.readyState === 2 ) {
				// Add other checks for responseHeaders that can be taken care of from extensions.
				if ( check_headers && 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-force-abort' ] && '' !== instance.responseHeaders[ 'x-dlm-force-abort' ] ) {
					button.removeAttribute( 'download' );
					button.setAttribute( 'href', href );
					buttonObj.removeClass().addClass( buttonClass ).find( 'span.dlm-xhr-progress' ).remove();
					buttonObj.find( '.dlm-xhr-loading-gif' ).remove();
					request.abort();
					jQuery( '#dlm-no-access-modal' ).remove();
					return;
				}

				// If the dlm-no-waypoints header is set we need to redirect.
				if ( dlmNoWaypoints ) {
					request.abort();
					if ( dlmRedirectHeader ) {
						window.location.href = dlmRedirectHeader;
						return;
					}
					window.location.href = href;
					return;
				}

				// If it's an external link we need to redirect.
				if ( dlmExternalDownloadHeader ) {
					request.abort();
					dlmXHRinstance.dlmExternalDownload( instance.responseHeaders, button, buttonObj, file_name, href );
					return;
				}

				// If there are no specific DLM headers then we need to abort and redirect.
				const responseDLM = Object.keys( instance.responseHeaders ).filter( ( element ) => {
					return element.indexOf( 'dlm-' ) !== -1;
				} ).length;

				if ( 0 === responseDLM ) {
					request.abort();
					window.location.href = href;
					return;
				}

				if ( dlmNoAccessHeader && 'true' === dlmNoAccessHeader ) {
					if ( dlmNoAccessModalHeader && 0 != dlmNoAccessModalHeader ) {
						dlmXHRinstance.dlmNoAccessModal( instance.responseHeaders );
						button.removeAttribute( 'download' );
						button.setAttribute( 'href', href );
						buttonObj.removeClass().addClass( buttonClass ).find( 'span.dlm-xhr-progress' ).remove();
						buttonObj.find( '.dlm-xhr-loading-gif' ).remove();
						request.abort();
						return;
					}
				}

				// If there is a dlm-error headers means we have an error. Display the error and abort.
				if ( dlmErrorHeader && '' !== dlmErrorHeader && null !== dlmErrorHeader ) {
					dlmXHRinstance.dlmLogDownload( instance.responseHeaders, 'failed', false );
					button.removeAttribute( 'download' );
					button.setAttribute( 'href', href );
					buttonObj.removeClass().addClass( buttonClass ).find( 'span.dlm-xhr-progress' ).remove();
					buttonObj.find( '.dlm-xhr-loading-gif' ).remove();
					request.abort();

					if ( dlmNoAccessModalHeader && 0 != dlmNoAccessModalHeader ) {
						dlmXHRinstance.dlmNoAccessModal( instance.responseHeaders );
					} else {
						buttonObj.find( '.dlm-xhr-error' ).remove();
						buttonObj.append( '<span class="dlm-xhr-error">' + dlmErrorHeader + '</span>' );
					}

					return;
				}

				// If we have a x-dlm-redirect header means this is a redirect. Let's do that.
				if ( dlmRedirectHeader && '' !== dlmRedirectHeader && null !== dlmRedirectHeader ) {
					dlmXHRinstance.dlmLogDownload( instance.responseHeaders, 'redirected', false, dlmRedirectHeader, dlmNoAccessHeader, buttonTarget );
					button.removeAttribute( 'download' );
					button.setAttribute( 'href', href );
					buttonObj.removeClass().addClass( buttonClass ).find( 'span.dlm-xhr-progress' ).remove();
					buttonObj.find( '.dlm-xhr-loading-gif' ).remove();
					request.abort();

					return;
				}
			}

			if ( status == 404 && readyState == 2 ) {
				const p = document.createElement( 'p' );
				p.textContent = statusText;
				button.parentNode.appendChild( p );
			}

			if ( status == 403 && readyState == 2 ) {
				const p = document.createElement( 'p' );
				p.textContent = statusText;
				button.parentNode.appendChild( p );
			}

			if ( status == 200 && readyState == 4 ) {
				const blob = request.response;
				_OBJECT_URL = URL.createObjectURL( blob );
				// Remove event listener
				button.removeEventListener( 'click', dlmXHRinstance.handleDownloadClick );
				// Set the download attribute of the a.download-complete to the file name
				button.setAttribute( 'download', `${ file_name }` );
				// Set the href of the a.download-complete to the object URL
				button.setAttribute( 'href', _OBJECT_URL );
				// Trigger click on a.download-complete
				button.click();
				buttonObj.removeClass().addClass( buttonClass + ' dlm-download-complete' );

				dlmXHRinstance.attachButtonEvent();

				// Append the paragraph to the download-contaner
				// Trigger the `dlm_download_complete` action
				jQuery( document ).trigger( 'dlm_download_complete', [ this, button, buttonObj, _OBJECT_URL ] );
				dlmXHRinstance.dlmLogDownload( instance.responseHeaders, 'completed', $setCookie );
				// Recommended : Revoke the object URL after some time to free up resources
				window.URL.revokeObjectURL( _OBJECT_URL );
				button.removeAttribute( 'download' );
				button.setAttribute( 'href', href );
				buttonObj.find( '.dlm-xhr-loading-gif' ).remove();
				buttonObj.find( 'span.dlm-xhr-progress' ).remove();
				// There is no way to find out whether user finished downloading
				setTimeout( function() {
					buttonObj.removeClass().addClass( buttonClass );
				}, 4000 );
			}
		};

		if ( dlmXHRProgress ) {
			request.addEventListener( 'progress', function( e ) {
				let total = e.total;
				// Set the total file size from the custom response header
				if ( 'undefined' === typeof e.total || e.total === 'undefined' || 0 === e.total ) {
					if ( 'undefined' !== typeof instance.responseHeaders[ 'x-dlm-filesize' ] ) {
						total = instance.responseHeaders[ 'x-dlm-filesize' ];
					}
				}
				let percent_complete = ( e.loaded / total ) * 100;
				// Force perfect complete to have 2 digits
				percent_complete = percent_complete.toFixed();
				let $class = 'dlm-download-started';
				buttonObj.find( 'span.dlm-xhr-progress' ).remove();
				// Comment below lines for the new XHR loader so that we know where to rever
				$class = $class + ' dlm-download-' + Math.ceil( percent_complete / 10 ) * 10;

				if ( Infinity != percent_complete ) {
					buttonObj.append( '<span class="dlm-xhr-progress">&nbsp;' + percent_complete + '%</span>' );
				}

				// Show spinner
				buttonObj.removeClass().addClass( buttonClass + ' ' + $class );
				// Trigger the `dlm_download_progress` action
				jQuery( document ).trigger( 'dlm_download_progress', [ this, button, buttonObj, _OBJECT_URL, e, percent_complete ] );
			} );
		}

		request.onerror = function() {
			button.removeAttribute( 'download' );
			button.setAttribute( 'href', href );
			buttonObj.removeClass().addClass( buttonClass + ' dlm-no-xhr-download' ).find( 'span.dlm-xhr-progress' ).remove();
			buttonObj.find( '.dlm-xhr-error' ).remove();
			buttonObj.append( '<span class="dlm-xhr-error">' + dlmXHRtranslations.error + '</span>' );
			console.log( '** An error occurred during the transaction' );
		};

		request.open( 'GET', href, true );
		request.setRequestHeader( 'Cache-Control', 'no-store, no-cache, no-transform, max-age=0' );
		request.setRequestHeader( 'dlm-xhr-request', 'dlm_XMLHttpRequest' );
		request.send();
	}

	dlmLogDownload( headers, status, cookie, redirect_path = null, no_access = null, target = '_self' ) {
		const currentURL = window.location.href;
		const download_id = headers[ 'x-dlm-download-id' ] ?? headers[ 'dlm-download-id' ];
		const version_id = headers[ 'x-dlm-version-id' ] ?? headers[ 'dlm-version-id' ];

		const ua = navigator.userAgent || '';
		const isSafari = /^((?!chrome|android|crios|fxios|edgios).)*safari/i.test( ua );
		const isAppleSafari = isSafari && ! /CriOS|FxiOS|EdgiOS/i.test( ua );
		// Early redirect — skip logging if no_access = true
		if ( no_access && redirect_path ) {
			if ( isAppleSafari ) {
				window.location.href = redirect_path;
			} else {
				const tgt = target || '_self';
				const downloadTab = window.open( redirect_path, tgt, 'noopener' );
				if ( downloadTab ) {
					downloadTab.location.href = redirect_path;
				}
			}
			return;
		}

		const params = new URLSearchParams( {
			action: 'log_dlm_xhr_download',
			download_id,
			version_id,
			status,
			cookie,
			currentURL,
			nonce: headers[ 'x-dlm-nonce' ],
			log_token: headers[ 'x-dlm-log-token' ],
		} );

		for ( const [ key, value ] of Object.entries( headers ) ) {
			params.append( `responseHeaders[${ key }]`, value );
		}

		const countEls = [];

		if ( 'completed' === status && ! dlmXHR.countExcluded ) {
			document.querySelectorAll( `[id="download-link-${ download_id }"]` ).forEach( ( link ) => {
				const nested = link.querySelector( '.dlm-download-count-value' );

				if ( nested ) {
					countEls.push( nested );
					return;
				}

				const box = link.closest( '.dlm-download-box' );
				const boxCount = box ? box.querySelector( '.dlm-download-count' ) : null;

				if ( boxCount ) {
					countEls.push( boxCount );
				}
			} );
		}

		if ( countEls.length ) {
			const formats = new Set();
			countEls.forEach( ( el ) => {
				if ( el.dataset.countFormat ) {
					formats.add( el.dataset.countFormat );
				}
			} );
			formats.forEach( ( format ) => params.append( 'count_formats[]', format ) );

			fetch( dlmXHR.ajaxUrl, { method: 'POST', body: params, keepalive: true } )
				.then( ( response ) => response.json() )
				.then( ( json ) => {
					const data = json && json.data ? json.data : null;

					if ( ! data ) {
						return;
					}

					countEls.forEach( ( el ) => {
						const format = el.dataset.countFormat;

						if ( format && data.counts && 'undefined' !== typeof data.counts[ format ] ) {
							el.textContent = data.counts[ format ];
						} else if ( 'undefined' !== typeof data.download_count ) {
							el.textContent = data.download_count;
						}
					} );
				} )
				.catch( () => {} );
		} else {
			try {
				navigator.sendBeacon(
					dlmXHR.ajaxUrl,
					new Blob( [ params.toString() ], { type: 'application/x-www-form-urlencoded;charset=UTF-8' } ),
				);
			} catch ( e ) {
				try {
					fetch( dlmXHR.ajaxUrl, { method: 'POST', body: params, keepalive: true } );
				} catch ( _ ) {}
			}
		}

		if ( ! redirect_path ) {
			return;
		}

		if ( isAppleSafari ) {
			window.location.href = redirect_path;
			return;
		}

		const tgt = target || '_self';
		const downloadTab = window.open( redirect_path, tgt, 'noopener' );
		if ( no_access && downloadTab ) {
			downloadTab.location.href = redirect_path;
		}
	}

	dlmNoAccessModal( headers ) {
		const instance = this;
		let download = 'empty-download',
			version = 'empty-version',
			restriction = 'empty-restriction',
			text = '';

		/**
		 * Old headers, will be removed in a future release. DLM Amazon S3 adds these headers.
		 */
		if ( 'undefined' !== typeof headers[ 'dlm-download-id' ] ) {
			download = headers[ 'dlm-download-id' ];
		}
		if ( 'undefined' !== typeof headers[ 'dlm-version-id' ] ) {
			version = headers[ 'dlm-version-id' ];
		}
		if ( 'undefined' !== typeof headers[ 'dlm-no-access-modal-text' ] ) {
			text = headers[ 'dlm-no-access-modal-text' ];
		}
		if ( 'undefined' !== typeof headers[ 'dlm-no-access-restriction' ] ) {
			restriction = headers[ 'dlm-no-access-restriction' ];
		}
		// End of old headers.

		/**
		 * New headers.
		 */
		if ( 'undefined' !== typeof headers[ 'x-dlm-download-id' ] ) {
			download = headers[ 'x-dlm-download-id' ];
		}
		if ( 'undefined' !== typeof headers[ 'x-dlm-version-id' ] ) {
			version = headers[ 'x-dlm-version-id' ];
		}
		if ( 'undefined' !== typeof headers[ 'x-dlm-no-access-modal-text' ] ) {
			text = headers[ 'x-dlm-no-access-modal-text' ];
		}
		if ( 'undefined' !== typeof headers[ 'x-dlm-no-access-restriction' ] ) {
			restriction = headers[ 'x-dlm-no-access-restriction' ];
		}
		// End of new headers.

		const data = {
			download_id: download,
			version_id: version,
			modal_text: text,
			restriction,
			action: 'no_access_dlm_xhr_download',
			nonce: headers[ 'x-dlm-nonce' ],
		};

		jQuery( document ).trigger( 'dlm-xhr-modal-data', [ data, headers ] );
		document.dispatchEvent(
			new CustomEvent( 'dlm-xhr-modal-data', { detail: { data, headers } } ),
		);

		jQuery.post( dlmXHR.ajaxUrl, data, function( response ) {
			jQuery( '#dlm-no-access-modal' ).remove();
			//document.body.insertAdjacentHTML('beforeend', response);
			const tempDiv = document.createElement( 'div' );
			tempDiv.innerHTML = response;
			// Extract and execute scripts from the response using a regular expression
			const scriptRegex = /<script\b[^>]*>([\s\S]*?)<\/script>/gm;
			// Remove script tags from tempDiv.innerHTML
			tempDiv.innerHTML = tempDiv.innerHTML.replace( scriptRegex, '' );
			let match;
			while ( ( match = scriptRegex.exec( response ) ) !== null ) {
				const scriptTag = match[ 0 ];
				const scriptContent = match[ 1 ];
				const newScript = document.createElement( 'script' );
				// Check if the script has a src attribute
				const srcMatch = scriptTag.match( /src=["']([^"']+)["']/ );
				if ( srcMatch ) {
					newScript.src = srcMatch[ 1 ];
				} else {
					newScript.textContent = scriptContent;
				}
				// Append the new script to the modal if exists
				const modal = tempDiv.querySelector( '#dlm-no-access-modal' );
				if ( modal ) {
					modal.appendChild( newScript );
				} else {
					document.body.appendChild( newScript );
				}
			}
			// Append the parsed HTML elements to the body
			while ( tempDiv.firstChild ) {
				document.body.appendChild( tempDiv.firstChild );
			}

			//jQuery('body').append(response);
			jQuery( document ).trigger( data.action, [ response, data ] );

			document.dispatchEvent(
				new CustomEvent( data.action, { detail: [ response, data ] } ),
			);
		} );
	}

	dlmExternalDownload( headers, button, buttonObj, file_name, href ) {
		const request = new XMLHttpRequest(),
			  buttonTarget = buttonObj.attr( 'target' );
		let buttonClass = buttonObj.attr( 'class' ),
			_OBJECT_URL,
			uri = '';

		if ( 'undefined' !== typeof headers[ 'dlm-external-download' ] ) {
			uri = headers[ 'dlm-external-download' ];
		}
		if ( 'undefined' !== typeof headers[ 'x-dlm-external-download' ] ) {
			uri = headers[ 'x-dlm-external-download' ];
		}
		buttonClass = ( 'undefined' !== typeof buttonClass && '' !== buttonClass ) ? buttonClass.replace( 'dlm-download-started', '' ).replace( 'dlm-download-completed', '' ) : '';

		buttonObj.addClass( 'dlm-download-started' );
		button.setAttribute( 'href', '#' );
		button.removeAttribute( 'download' );
		button.setAttribute( 'disabled', 'disabled' );

		// Trigger the `dlm_download_triggered` action
		jQuery( document ).trigger( 'dlm_download_triggered', [ this, button, buttonObj, _OBJECT_URL, request ] );

		request.responseType = 'blob';
		request.onreadystatechange = function() {
			const {
				status,
				readyState,
				statusText,
			} = request;

			if ( 403 === status ) {
				dlmXHRinstance.dlmLogDownload( headers, 'failed', false );
				request.abort();
				buttonObj.find( '.dlm-xhr-error' ).remove();
				buttonObj.append( '<span class="dlm-xhr-error">Acces Denied to file.</span>' );
				return;
			}
			if ( status == 200 && readyState == 4 ) {
				const blob = request.response;

				_OBJECT_URL = URL.createObjectURL( blob );
				// Remove event listener
				button.removeEventListener( 'click', dlmXHRinstance.handleDownloadClick );
				// Set the download attribute of the a.download-complete to the file name
				button.setAttribute( 'download', `${ file_name }` );
				// Set the href of the a.download-complete to the object URL
				button.setAttribute( 'href', _OBJECT_URL );
				// Trigger click on a.download-complete
				button.click();
				buttonObj.removeClass().addClass( buttonClass + ' dlm-download-complete' );

				dlmXHRinstance.attachButtonEvent();

				// Append the paragraph to the download-contaner
				// Trigger the `dlm_download_complete` action
				jQuery( document ).trigger( 'dlm_download_complete', [ this, button, buttonObj, _OBJECT_URL ] );
				dlmXHRinstance.dlmLogDownload( headers, 'completed', false );

				// Recommended : Revoke the object URL after some time to free up resources
				window.URL.revokeObjectURL( _OBJECT_URL );
				button.removeAttribute( 'download' );
				button.setAttribute( 'href', href );
				// There is no way to find out whether user finished downloading
				buttonObj.find( '.dlm-xhr-loading-gif' ).remove();
				buttonObj.find( 'span.dlm-xhr-progress' ).remove();
				setTimeout( function() {
					buttonObj.removeClass().addClass( buttonClass );
				}, 1000 );
			}
		};

		if ( dlmXHRProgress ) {
			request.addEventListener( 'progress', function( e ) {
				let total = e.total;

				if ( 'undefined' === typeof e.total || e.total === 'undefined' ) {
					total = request.getResponseHeader( 'X-DLM-Filesize' );
				}
				let percent_complete = ( e.loaded / total ) * 100;
				// Force perfect complete to have 2 digits
				percent_complete = percent_complete.toFixed();
				let $class = 'dlm-download-started';
				buttonObj.find( 'span.dlm-xhr-progress' ).remove();
				// Comment below lines for the new XHR loader so that we know where to rever
				$class = $class + ' dlm-download-' + Math.ceil( percent_complete / 10 ) * 10;

				if ( Infinity != percent_complete ) {
					buttonObj.append( '<span class="dlm-xhr-progress">&nbsp;' + percent_complete + '%</span>' );
				}

				// Show spinner
				buttonObj.removeClass().addClass( buttonClass + ' ' + $class );
				// Trigger the `dlm_download_progress` action
				jQuery( document ).trigger( 'dlm_download_progress', [ this, button, buttonObj, _OBJECT_URL, e, percent_complete ] );
			} );
		}

		request.onerror = function() {
			button.removeAttribute( 'download' );
			button.setAttribute( 'href', href );
			buttonObj.removeClass().addClass( buttonClass + ' .dlm-no-xhr-download' ).find( 'span.dlm-xhr-progress' ).remove();
			buttonObj.find( '.dlm-xhr-error' ).remove();
			buttonObj.append( '<span class="dlm-xhr-error">' + dlmXHRtranslations.error + '</span>' );
			console.log( '** An error occurred during the transaction' );
		};

		request.open( 'GET', uri, true );
		request.setRequestHeader( 'Cache-Control', 'no-store, no-cache, no-transform, max-age=0' );
		request.setRequestHeader( 'dlm-xhr-request', 'dlm_XMLHttpRequest' );
		request.send();
	}
}
