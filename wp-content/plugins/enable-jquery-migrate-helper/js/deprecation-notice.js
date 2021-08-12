/**
 * Show all jQuery Migrate warnings in the UI.
 */
jQuery( document ).ready( function( $ ) {
	const notice       = $( '.notice.jquery-migrate-deprecation-notice' );
	const warnings     = jQuery.migrateWarnings || [];
	const adminbar     = $( '#wp-admin-bar-enable-jquery-migrate-helper' );
	const countWrapper = $( '.count-wrapper', adminbar );

	var previousDeprecations = [];

	/**
	 * Filter the trace, return the first URI that is to a plugin or theme script.
	 */
	function getSlugFromTrace( trace ) {
		let traceLines = trace.split( '\n' ),
			match = null;

		// Loop over each line in the stack trace
		traceLines.forEach( function( line ) {
			if ( ! line ) {
				return;
			}

			// Remove cache-busting.
			line = line.split( '?' )[0];

			// The first few lines are going to be references to the jquery-migrate script.
			// The first instance that is not one of them is probably a valid plugin or theme.
			if (
				! match &&
				line.indexOf( '/' + JQMH.plugin_slug + '/js' ) === -1 &&
				( line.indexOf( '/plugins/' ) > -1 || line.indexOf( '/themes/' ) > -1 || line.indexOf( '/wp-admin/js/' ) > -1 )
			) {
				match = line.replace( /.*?http/, 'http' );
			}
		} );

		// If the stack trace did not contain a matching plugin or theme, just return a null value.
		return match;
	}

	/**
	 * Update the count of deprecations found on this page.
	 *
	 * @param count
	 */
	function setAdminBarCount( count ) {
		if ( ! adminbar.length ) {
			return;
		}

		// The live counter may be disabled if jQuery 3 is used during WordPress 5.6
		if ( ! JQMH.capture_deprecations ) {
			return;
		}

		if ( ! countWrapper.is( ':visible' ) ) {
			countWrapper.show();

			countWrapperVisibility();
		}

		$( '.count', adminbar ).text( count );
	}

	/**
	 * Set the admin bar visibility level based on the warning counters.
	 */
	function countWrapperVisibility() {
		if ( countWrapper.is( ':visible' ) ) {
			adminbar
				.css( 'background-color', '#be4400' )
				.css( 'color', '#eeeeee' );
		} else {
			adminbar
				.css( 'background-color', '' )
				.css( 'color', '' );
		}
	}

	/**
	 * Append the deprecation to the admin dashbaord, if applicable.
	 *
	 * @param message
	 */
	function appendNoticeDisplay( message ) {
		const list = notice.find( '.jquery-migrate-deprecation-list' );

		if ( ! notice.length ) {
			return;
		}

		// Only list one case of the same error per file.
		if ( JQMH.single_instance_log ) {
			if ( previousDeprecations.indexOf( message ) > -1 ) {
				return;
			}

			previousDeprecations.push( message );
		}

		if ( ! notice.is( ':visible' ) ) {
			notice.show();
		}

		list.append( $( '<li></li>' ).text( message ) );
	}

	/**
	 * Try to log the deprecation for the admin area.
	 *
	 * @param message
	 */
	function reportDeprecation( message ) {
		// Do not write to the logfile if this is the backend and the notices are written to the screen.
		if ( JQMH.backend && notice.length ) {
			return;
		}

		if ( ! JQMH.capture_deprecations ) {
			return;
		}

		let data = {
			action: 'jquery-migrate-log-notice',
			notice: message,
			nonce: JQMH.report_nonce,
			backend: JQMH.backend,
			url: window.location.href,
		};

		$.post( {
			url: JQMH.ajaxurl,
			data
		} );
	}

	if ( warnings.length ) {
		warnings.forEach( function( entry ) {
			const trace = getSlugFromTrace( entry.trace ? entry.trace : "" );
			let message = trace ? trace + ': ' : '';

			// Traces some times get a null value, skip these.
			if ( '' === message ) {
				return;
			}

			message += entry.warning;

			appendNoticeDisplay( message );

			reportDeprecation( message );
		} );

		setAdminBarCount( warnings.length );
	}

	// Add handler for dismissing of the dashboard notice.
	$( document ).on( 'click', '.jquery-migrate-dashboard-notice .notice-dismiss', function() {
		const $notice = $( this ).closest( '.notice' );
		const notice_id = $notice.data( 'notice-id' );

		$.post( {
			url: window.ajaxurl,
			data: {
				action: 'jquery-migrate-dismiss-notice',
				'notice': notice_id,
				'dismiss-notice-nonce': $( '#' + notice_id + '-nonce' ).val(),
			},
		} );
	} );

	// When the previous deprecations are dismissed, reset the admin bar log display.
	$( document ).on( 'click', '.jquery-migrate-previous-deprecations .notice-dismiss', function() {
		countWrapper.hide();
		countWrapperVisibility();
	} );

	// Check if the counter is visible on page load.
	countWrapperVisibility();
} );
