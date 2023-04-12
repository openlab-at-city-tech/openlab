/**
 * Initializes in a Strict env the code that manages the Stellar Installer buttons.
 *
 * @since 1.0.0
 *
 * @param  {Object} $     jQuery
 * @param  {Object} hooks WP Hooks
 * @param  {Object} obj   window.stellarwp.installer
 *
 * @return {void}
 */

( function( $, hooks, document ) {
	'use strict';

	/**
	 * Initialize the necessary varaibles for StellarWP libraries such that
	 * they are isolated from other instances of this library in the wild.
	 */
	// BEGIN: stellarwp library initialization.
	const currentScript           = typeof document.currentScript !== 'undefined' ? document.currentScript : document.scripts[document.scripts.length - 1];
	const namespace               = currentScript.getAttribute( 'data-stellarwp-namespace' );
	if ( namespace === null ) {
		console.info( 'The stellarwp/installer library failed to initialize because the data-stellarwp-namespace attribute could not be read from the script tag.' );
		return;
	}
	window.stellarwp              = window.stellarwp || {};
	window.stellarwp[ namespace ] = window.stellarwp[ namespace ] || {};
	// END: stellarwp library initialization.

	// If the library has already been initialized, bail.
	if ( typeof window.stellarwp[ namespace ].installer === 'object' ) {
		return;
	}

	window.stellarwp[ namespace ].installer = JSON.parse( currentScript.getAttribute( 'data-stellarwp-data' ) );
	const obj                               = window.stellarwp[ namespace ].installer;
	const $document                         = $( document );

	/**
	 * Gets the AJAX request data.
	 *
	 * @since 1.0.0
	 *
	 * @param  {Element|jQuery} $button The button where the configuration data is.
	 *
	 * @return {Object} data
	 */
	obj.getData = ( $button ) => {
		const data = {
			'action': $button.data( 'action' ),
			'request': $button.data( 'request-action' ),
			'slug': $button.data( 'slug' ),
			'_wpnonce': $button.data( 'nonce' ),
		};

		return data;
	};

	/**
	 * Handles the plugin install AJAX call.
	 *
	 * @since 1.0.0
	 */
	obj.handleInstall = ( event ) => {
		const $button = $( event.target );
		const ajaxUrl = obj.ajaxurl;
		const data = obj.getData( $button );
		const requestType = $button.data( 'request-action' );

		$button.addClass( obj.busyClass );
		$button.prop( 'disabled', true );

		if ( 'install' === requestType ) {
			$button.text( $button.data( 'installing-label' ) );
		} else if ( 'activate' === requestType  ) {
			$button.text( $button.data( 'activating-label' ) );
		}

		$.post( ajaxUrl, data, ( response ) => {
			$button.removeClass( obj.busyClass );
			$button.prop( 'disabled', false );

			if ( 'undefined' === typeof response.data || 'object' !== typeof response.data ) {
				return;
			}

			if ( response.success ) {
				if ( 'install' === requestType ) {
					$button.text( $button.data( 'installed-label' ) );
				} else if ( 'activate' === requestType ) {
					$button.text( $button.data( 'activated-label' ) );
				}

				if ( $button.data('redirect-url') ) {
					location.replace( $button.data('redirect-url') );
				}
			} else {
				hooks.doAction(
					'stellarwp_installer_' + $button.data( 'hook-prefix' ) + '_error',
					event.data.selector,
					$button.data( 'slug' ),
					data.action,
					response.data.message,
					$button.data( 'hook-prefix' )
				);
			}
		} );
	}

	/**
	 * Handles the initialization of the notice actions.
	 *
	 * @since 1.0.0
	 *
	 * @return {void}
	 */
	obj.ready = ( event ) => {
		for ( const key in obj.selectors ) {
			$document.on(
				'click',
				obj.selectors[ key ],
				{
					slug: key,
					selector: obj.selectors[ key ]
				},
				obj.handleInstall
			);
		}
	};

	// Configure on document ready.
	$document.ready( obj.ready );
} )( window.jQuery, window.wp.hooks, document );
