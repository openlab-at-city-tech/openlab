/**
 * Smash Balloon Deactivation Survey Modal
 *
 * Intercepts plugin deactivation links on the WP plugins page,
 * shows a feedback modal, collects the reason, and submits via AJAX.
 * Deactivation always proceeds regardless of API response.
 *
 * The modal lives inside a Shadow DOM for complete CSS isolation
 * from WordPress admin styles.
 */
( function() {
	'use strict';

	if ( typeof sbFeedbackData === 'undefined' ) {
		return;
	}

	// Set up Shadow DOM.
	// When multiple SB plugins are installed, each scoped instance may
	// render its own host/template pair. Use the first and remove dupes.
	var hosts     = document.querySelectorAll( '[id="sb-deactivation-host"]' );
	var templates = document.querySelectorAll( '[id="sb-deactivation-template"]' );

	if ( ! hosts.length || ! templates.length ) {
		return;
	}

	var host     = hosts[0];
	var template = templates[0];

	for ( var d = 1; d < hosts.length; d++ )     hosts[ d ].remove();
	for ( var d = 1; d < templates.length; d++ ) templates[ d ].remove();

	var shadow = host.attachShadow( { mode: 'open' } );
	shadow.appendChild( template.content.cloneNode( true ) );

	// Remove template from DOM — no longer needed.
	template.remove();

	// Query elements from the shadow root.
	var modal        = shadow.querySelector( '.sb-deactivation-overlay' );
	var reasonsWrap  = modal ? modal.querySelector( '.sb-deactivation-reasons' ) : null;
	var contextWrap  = modal ? modal.querySelector( '.sb-deactivation-context' ) : null;
	var contextHead  = modal ? modal.querySelector( '.sb-deactivation-context-heading' ) : null;
	var contextDesc  = modal ? modal.querySelector( '.sb-deactivation-context-description' ) : null;
	var textarea     = modal ? modal.querySelector( '.sb-deactivation-textarea' ) : null;
	var pluginName   = modal ? modal.querySelector( '.sb-deactivation-plugin-name' ) : null;
	var submitBtn    = modal ? modal.querySelector( '.sb-deactivation-btn--submit' ) : null;
	var cancelBtn    = modal ? modal.querySelector( '.sb-deactivation-btn--cancel' ) : null;
	var closeBtn     = modal ? modal.querySelector( '.sb-deactivation-close' ) : null;
	var supportLink  = modal ? modal.querySelector( '.sb-deactivation-btn--support' ) : null;

	if ( ! modal || ! reasonsWrap ) {
		return;
	}

	var currentPlugin  = null;
	var deactivateUrl  = null;
	var selectedReason = null;

	/**
	 * Initialize: attach click handlers to all registered plugin deactivate links.
	 *
	 * Deactivate links live in the regular DOM (WP plugins page), not the shadow.
	 */
	function init() {
		var plugins = sbFeedbackData.plugins || {};

		Object.keys( plugins ).forEach( function( basename ) {
			var link = null;

			// Method 1: WordPress data-plugin attribute on the row.
			var row = document.querySelector( 'tr[data-plugin="' + basename + '"]' );
			if ( row ) {
				link = row.querySelector( '.deactivate a' );
			}

			// Method 2: Search by href containing the encoded plugin basename.
			if ( ! link ) {
				var encoded = encodeURIComponent( basename );
				var allDeactivateLinks = document.querySelectorAll( '.deactivate a' );
				for ( var i = 0; i < allDeactivateLinks.length; i++ ) {
					var href = allDeactivateLinks[ i ].getAttribute( 'href' ) || '';
					if ( href.indexOf( 'plugin=' + encoded ) !== -1 || href.indexOf( 'plugin=' + basename ) !== -1 ) {
						link = allDeactivateLinks[ i ];
						break;
					}
				}
			}

			if ( link ) {
				link.addEventListener( 'click', function( e ) {
					e.preventDefault();
					deactivateUrl = link.getAttribute( 'href' );
					currentPlugin = plugins[ basename ];
					openModal();
				} );
			}
		} );

		// Close handlers.
		if ( closeBtn )  closeBtn.addEventListener( 'click', closeModal );
		if ( cancelBtn ) cancelBtn.addEventListener( 'click', closeModal );

		modal.addEventListener( 'click', function( e ) {
			if ( e.target === modal ) {
				closeModal();
			}
		} );

		document.addEventListener( 'keydown', function( e ) {
			if ( e.key === 'Escape' && modal.style.display !== 'none' ) {
				closeModal();
			}
		} );

		// Submit handler.
		if ( submitBtn ) {
			submitBtn.addEventListener( 'click', handleSubmit );
		}
	}

	/**
	 * Open the modal for the given plugin.
	 */
	function openModal() {
		if ( ! currentPlugin ) return;

		// Set plugin name.
		if ( pluginName ) {
			pluginName.textContent = currentPlugin.name;
		}

		// Set support URL.
		if ( supportLink ) {
			supportLink.setAttribute( 'href', currentPlugin.supportUrl || 'https://smashballoon.com/support/' );
		}

		// Build reasons.
		buildReasons( currentPlugin.reasons );

		// Select first reason by default.
		selectedReason = currentPlugin.reasons[0] || null;
		var firstRadio = reasonsWrap.querySelector( 'input[type="radio"]' );
		if ( firstRadio ) {
			firstRadio.checked = true;
		}
		updateContext();

		// Reset textarea.
		if ( textarea ) {
			textarea.value = '';
		}

		// Show modal with animation.
		modal.style.display = 'flex';
		// Force reflow for transition.
		void modal.offsetHeight;
		modal.classList.add( 'sb-visible' );

		// Trap focus.
		if ( closeBtn ) {
			closeBtn.focus();
		}
	}

	/**
	 * Close the modal.
	 */
	function closeModal() {
		modal.classList.remove( 'sb-visible' );

		setTimeout( function() {
			modal.style.display = 'none';
		}, 200 );

		currentPlugin  = null;
		deactivateUrl  = null;
		selectedReason = null;
	}

	/**
	 * Build radio buttons for the reasons.
	 */
	function buildReasons( reasons ) {
		reasonsWrap.innerHTML = '';

		reasons.forEach( function( reason, index ) {
			var label  = document.createElement( 'label' );
			label.className = 'sb-deactivation-reason';

			var radio  = document.createElement( 'span' );
			radio.className = 'sb-deactivation-radio';

			var input  = document.createElement( 'input' );
			input.type  = 'radio';
			input.name  = 'sb-deactivation-reason';
			input.value = reason.id;
			if ( index === 0 ) {
				input.checked = true;
			}

			var circle = document.createElement( 'span' );
			circle.className = 'sb-deactivation-radio-circle';

			radio.appendChild( input );
			radio.appendChild( circle );

			var text   = document.createElement( 'span' );
			text.className   = 'sb-deactivation-reason-label';
			text.textContent = reason.label;

			label.appendChild( radio );
			label.appendChild( text );

			input.addEventListener( 'change', function() {
				selectedReason = reason;
				updateContext();
			} );

			reasonsWrap.appendChild( label );
		} );
	}

	/**
	 * Update the context section based on selected reason.
	 */
	function updateContext() {
		if ( ! selectedReason || ! contextWrap ) return;

		if ( contextHead ) {
			contextHead.textContent = selectedReason.heading;
		}

		if ( contextDesc ) {
			contextDesc.textContent = selectedReason.description;
		}

		if ( textarea ) {
			textarea.setAttribute( 'placeholder', selectedReason.placeholder || '' );
		}

		// Show context.
		contextWrap.style.display = '';
	}

	/**
	 * Handle form submission.
	 */
	function handleSubmit() {
		if ( ! currentPlugin || ! selectedReason ) {
			proceed();
			return;
		}

		// Disable button to prevent double-clicks.
		if ( submitBtn ) {
			submitBtn.disabled = true;
		}

		var formData = new FormData();
		formData.append( 'action', 'sb_deactivation_feedback' );
		formData.append( 'nonce', sbFeedbackData.nonce );
		formData.append( 'plugin_slug', currentPlugin.slug );
		formData.append( 'reason_id', selectedReason.id );
		formData.append( 'details', textarea ? textarea.value : '' );

		// Fire-and-forget: proceed with deactivation regardless.
		var xhr = new XMLHttpRequest();
		xhr.open( 'POST', sbFeedbackData.ajaxUrl, true );
		xhr.onreadystatechange = function() {
			if ( xhr.readyState === XMLHttpRequest.DONE ) {
				proceed();
			}
		};
		xhr.onerror = function() {
			proceed();
		};

		// Fallback timeout — deactivate even if AJAX hangs.
		setTimeout( proceed, 3000 );

		xhr.send( formData );
	}

	/**
	 * Proceed with plugin deactivation.
	 */
	function proceed() {
		if ( deactivateUrl ) {
			window.location.href = deactivateUrl;
		}
	}

	// Initialize when DOM is ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
