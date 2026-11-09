/**
 * Migrations Admin JavaScript
 *
 * Handles AJAX interactions with the REST API for migration actions.
 *
 * @since 0.0.1
 *
 * @package StellarWP\Migrations
 */

( function( domReady, apiFetch, i18n, $, adminSettings ) {
	'use strict';

	const { __, sprintf } = i18n;

	/**
	 * Logs per page for pagination.
	 *
	 * Note: wp_localize_script converts values to strings, so we parse as int.
	 *
	 * @type {number}
	 */
	const LOGS_PER_PAGE = parseInt( adminSettings?.logsPerPage ) || 10;

	/**
	 * Current logs state for single view.
	 *
	 * @type {Object}
	 */
	const logsState = {
		executionId: null,
		offset: 0,
		hasMore: true,
		loading: false,
	};

	/**
	 * Initialize the migrations admin functionality.
	 */
	function init() {
		// Initialize Select2 on tags multi-select.
		initSelect2();

		// Initialize list view.
		initListView();

		// Initialize single view.
		initSingleView();
	}

	/**
	 * Initialize the list view functionality.
	 */
	function initListView() {
		const container = document.querySelector( '.stellarwp-migrations-list' );
		if ( ! container ) {
			return;
		}

		const restUrl = container.dataset.restUrl;

		if ( ! restUrl ) {
			console.error( 'Migrations: Missing REST URL' );
			return;
		}

		// Attach event listeners to all action buttons
		container.addEventListener( 'click', function( event ) {
			const button = event.target.closest( '.stellarwp-migration-btn[data-action]' );
			if ( ! button ) {
				return;
			}

			event.preventDefault();

			const card = button.closest( '.stellarwp-migration-card' );
			if ( ! card ) {
				return;
			}

			const migrationId = card.dataset.migrationId;
			const action = button.dataset.action;

			if ( ! migrationId || ! action ) {
				return;
			}

			handleAction( card, button, migrationId, action, restUrl );
		} );
	}

	/**
	 * Initialize the single view functionality.
	 */
	function initSingleView() {
		const container = document.querySelector( '.stellarwp-migration-single' );
		if ( ! container ) {
			return;
		}

		const restUrl = container.dataset.restUrl;

		if ( ! restUrl ) {
			console.error( 'Migrations: Missing REST URL' );
			return;
		}

		// Initialize action buttons on status card.
		initSingleViewActions( container, restUrl );

		// Initialize logs.
		initLogs( restUrl );
	}

	/**
	 * Initialize action buttons on the single view status card.
	 *
	 * @param {HTMLElement} container The single view container.
	 * @param {string}      restUrl   The REST API base URL.
	 */
	function initSingleViewActions( container, restUrl ) {
		const statusCard = container.querySelector( '.stellarwp-migration-status-card' );
		if ( ! statusCard ) {
			return;
		}

		statusCard.addEventListener( 'click', function( event ) {
			const button = event.target.closest( '.stellarwp-migration-btn[data-action]' );
			if ( ! button ) {
				return;
			}

			event.preventDefault();

			const migrationId = statusCard.dataset.migrationId;
			const action = button.dataset.action;

			if ( ! migrationId || ! action ) {
				return;
			}

			handleAction( statusCard, button, migrationId, action, restUrl );
		} );
	}

	/**
	 * Handle a migration action (run or rollback).
	 *
	 * @param {HTMLElement} card        The migration card element.
	 * @param {HTMLElement} button      The button that was clicked.
	 * @param {string}      migrationId The migration ID.
	 * @param {string}      action      The action to perform ('run' or 'rollback').
	 * @param {string}      restUrl     The REST API base URL.
	 */
	function handleAction( card, button, migrationId, action, restUrl ) {
		// Disable all buttons on this card
		const buttons = card.querySelectorAll( '.stellarwp-migration-btn' );
		buttons.forEach( function( btn ) {
			btn.disabled = true;
		} );

		// Add loading state to clicked button
		button.classList.add( 'stellarwp-migration-btn--loading' );
		const originalText = button.textContent;
		button.textContent = action === 'run' ? __( 'Adding to queue...', 'stellarwp-migrations' ) : __( 'Rolling back...', 'stellarwp-migrations' );

		// Build the endpoint path (relative to REST root)
		const endpoint = restUrl + '/migrations/' + encodeURIComponent( migrationId ) + '/' + action;

		// Make the API request using wp.apiFetch
		apiFetch( {
			url: endpoint,
			method: 'POST',
		} )
			.then( function( data ) {
				if ( data.success ) {
					showMessage( card, 'success', getSuccessMessage( action, data ) );
					updateCardStatus( card, action, data );
				} else {
					const errorMessage = data.message || __( 'An error occurred', 'stellarwp-migrations' );
					showMessage( card, 'error', errorMessage );
				}
			} )
			.catch( function( error ) {
				console.error( 'Migrations API error:', error );
				const errorMessage = error.message || __( 'Network error. Please try again.', 'stellarwp-migrations' );
				showMessage( card, 'error', errorMessage );
			} )
			.finally( function() {
				// Re-enable buttons and remove loading state
				buttons.forEach( function( btn ) {
					btn.disabled = false;
				} );
				button.classList.remove( 'stellarwp-migration-btn--loading' );
				button.textContent = originalText;
			} );
	}

	/**
	 * Get a success message for the action.
	 *
	 * @param {string} action The action that was performed.
	 * @param {Object} data   The response data.
	 *
	 * @return {string} The success message.
	 */
	function getSuccessMessage( action, data ) {
		const executionId = data.execution_id || __( 'N/A', 'stellarwp-migrations' );
		if ( action === 'run' ) {
			/* translators: %s: execution ID */
			return sprintf( __( 'Migration scheduled. Execution ID: %s', 'stellarwp-migrations' ), executionId );
		}
		/* translators: %s: execution ID */
		return sprintf( __( 'Rollback scheduled. Execution ID: %s', 'stellarwp-migrations' ), executionId );
	}

	/**
	 * Show a message in the card.
	 *
	 * @param {HTMLElement} card    The migration card element.
	 * @param {string}      type    Message type ('success' or 'error').
	 * @param {string}      message The message to display.
	 */
	function showMessage( card, type, message ) {
		const messageEl = card.querySelector( '.stellarwp-migration-card__message' );
		if ( ! messageEl ) {
			return;
		}

		messageEl.className = 'stellarwp-migration-card__message stellarwp-migration-card__message--' + type;
		messageEl.textContent = message;
		messageEl.style.display = 'block';

		// Hide after 5 seconds
		setTimeout( function() {
			messageEl.style.display = 'none';
		}, 5000 );
	}

	/**
	 * Update the card status after a successful action.
	 *
	 * @param {HTMLElement} card   The migration card element.
	 * @param {string}      action The action that was performed.
	 * @param {Object}      data   The response data.
	 */
	function updateCardStatus( card, action, data ) {
		const statusLabel = card.querySelector( '.stellarwp-migration-card__status-label' );
		if ( statusLabel ) {
			// Update status to "scheduled"
			statusLabel.className = 'stellarwp-migration-card__status-label stellarwp-migration-card__status-label--scheduled';
			statusLabel.textContent = __( 'Scheduled', 'stellarwp-migrations' );
		}

		// Update progress bar status
		const progressFill = card.querySelector( '.stellarwp-migration-progress__fill' );
		if ( progressFill ) {
			progressFill.className = 'stellarwp-migration-progress__fill stellarwp-migration-progress__fill--scheduled';
		}

		// Update button visibility
		updateButtonVisibility( card, 'scheduled' );
	}

	/**
	 * Update button visibility based on the new status.
	 *
	 * @param {HTMLElement} card   The migration card element.
	 * @param {string}      status The new status.
	 */
	function updateButtonVisibility( card, status ) {
		const actionsContainer = card.querySelector( '.stellarwp-migration-card__actions' );
		if ( ! actionsContainer ) {
			return;
		}

		// For scheduled status, hide all action buttons temporarily
		// The page will need to refresh to get accurate button states
		if ( status === 'scheduled' ) {
			const buttons = actionsContainer.querySelectorAll( '.stellarwp-migration-btn' );
			buttons.forEach( function( btn ) {
				btn.style.display = 'none';
			} );
		}
	}

	/**
	 * Initialize Select2 on tags multi-select.
	 */
	function initSelect2() {
		if ( typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined' ) {
			return;
		}

		$( '.stellarwp-migrations-select2' ).select2( {
			allowClear: true,
			width: '100%',
		} );
	}

	/**
	 * Initialize the logs functionality.
	 *
	 * @param {string} restUrl The REST API base URL.
	 */
	function initLogs( restUrl ) {
		const logsContainer = document.querySelector( '.stellarwp-migration-logs' );
		if ( ! logsContainer ) {
			return;
		}

		const select = logsContainer.querySelector( '#stellarwp-execution-select' );
		if ( ! select ) {
			return;
		}

		// Handle execution selection change.
		select.addEventListener( 'change', function() {
			const selectedOption = select.options[ select.selectedIndex ];
			const executionId = selectedOption.value;

			if ( executionId ) {
				logsState.executionId = executionId;
				logsState.offset = 0;
				logsState.hasMore = true;

				updateExecutionInfo( selectedOption );
				updateDownloadLink( selectedOption );
				loadLogs( restUrl, true );
			}
		} );

		// Handle load more button.
		const loadMoreBtn = logsContainer.querySelector( '.stellarwp-migration-logs__load-more-btn' );
		if ( loadMoreBtn ) {
			loadMoreBtn.addEventListener( 'click', function() {
				loadLogs( restUrl, false );
			} );
		}

		// Handle download logs button.
		const downloadBtn = logsContainer.querySelector( '#stellarwp-download-logs-link' );
		if ( downloadBtn ) {
			downloadBtn.addEventListener( 'click', handleDownloadLogsClick );
		}

		// Load logs for initially selected execution.
		if ( select.value ) {
			logsState.executionId = select.value;
			const selectedOption = select.options[ select.selectedIndex ];
			updateExecutionInfo( selectedOption );
			updateDownloadLink( selectedOption );
			loadLogs( restUrl, true );
		}
	}

	/**
	 * Update the download logs button to point to the selected execution.
	 *
	 * @param {HTMLOptionElement} option The selected option element.
	 */
	function updateDownloadLink( option ) {
		const downloadBtn = document.getElementById( 'stellarwp-download-logs-link' );
		if ( ! downloadBtn || ! option ) {
			return;
		}

		const downloadUrl = option.dataset.downloadUrl;
		if ( downloadUrl ) {
			downloadBtn.dataset.downloadUrl = downloadUrl;
			downloadBtn.disabled = false;
		} else {
			downloadBtn.dataset.downloadUrl = '';
			downloadBtn.disabled = true;
		}
	}

	/**
	 * Handle download logs button click: navigate to the current download URL.
	 */
	function handleDownloadLogsClick() {
		const downloadBtn = document.getElementById( 'stellarwp-download-logs-link' );
		if ( ! downloadBtn || downloadBtn.disabled ) {
			return;
		}

		const url = downloadBtn.dataset.downloadUrl;
		if ( url ) {
			window.location.href = url;
		}
	}

	/**
	 * Update execution info display.
	 *
	 * @param {HTMLOptionElement} option The selected option element.
	 */
	function updateExecutionInfo( option ) {
		const startEl = document.querySelector( '.stellarwp-migration-logs__execution-start' );
		const endEl = document.querySelector( '.stellarwp-migration-logs__execution-end' );

		if ( startEl ) {
			startEl.textContent = option.dataset.start || __( 'Not started', 'stellarwp-migrations' );
		}

		if ( endEl ) {
			const endDate = option.dataset.end;
			endEl.textContent = endDate || __( 'In progress', 'stellarwp-migrations' );
			endEl.style.display = endDate ? '' : 'none';
		}
	}

	/**
	 * Load logs for the current execution.
	 *
	 * @param {string}  restUrl The REST API base URL.
	 * @param {boolean} reset   Whether to reset the logs list.
	 */
	function loadLogs( restUrl, reset ) {
		if ( logsState.loading || ! logsState.hasMore ) {
			return;
		}

		if ( reset ) {
			logsState.offset = 0;
			logsState.hasMore = true;
		}

		logsState.loading = true;

		const logsContainer = document.querySelector( '.stellarwp-migration-logs' );
		const containerEl = logsContainer.querySelector( '.stellarwp-migration-logs__container' );
		const listEl = logsContainer.querySelector( '.stellarwp-migration-logs__list' );
		const loadingEl = logsContainer.querySelector( '.stellarwp-migration-logs__loading' );
		const noLogsEl = logsContainer.querySelector( '.stellarwp-migration-logs__no-logs' );
		const loadMoreEl = logsContainer.querySelector( '.stellarwp-migration-logs__load-more' );

		// Show loading state.
		loadingEl.style.display = 'flex';
		loadMoreEl.style.display = 'none';
		noLogsEl.style.display = 'none';
		containerEl.classList.remove( 'stellarwp-migration-logs__container--has-more' );

		if ( reset ) {
			listEl.innerHTML = '';
		}

		const endpoint = restUrl + '/executions/' + encodeURIComponent( logsState.executionId ) + '/logs';
		const url = endpoint + '?limit=' + LOGS_PER_PAGE + '&offset=' + logsState.offset + '&order=ASC';

		apiFetch( { url: url } )
			.then( function( logs ) {
				loadingEl.style.display = 'none';

				if ( ! logs || logs.length === 0 ) {
					if ( reset ) {
						noLogsEl.style.display = 'block';
					}
					logsState.hasMore = false;
					loadMoreEl.style.display = 'none';
					containerEl.classList.remove( 'stellarwp-migration-logs__container--has-more' );
					return;
				}

				// Render logs.
				logs.forEach( function( log ) {
					listEl.appendChild( createLogElement( log ) );
				} );

				// Update pagination state.
				logsState.offset += logs.length;
				logsState.hasMore = logs.length === LOGS_PER_PAGE;

				// Show/hide load more button and update container styling.
				loadMoreEl.style.display = logsState.hasMore ? 'block' : 'none';
				containerEl.classList.toggle( 'stellarwp-migration-logs__container--has-more', logsState.hasMore );
			} )
			.catch( function( error ) {
				console.error( 'Failed to load logs:', error );
				loadingEl.style.display = 'none';

				if ( reset ) {
					noLogsEl.style.display = 'block';
					/* translators: %s: error message */
					noLogsEl.querySelector( 'p' ).textContent = sprintf( __( 'Failed to load logs: %s', 'stellarwp-migrations' ), error.message || __( 'Unknown error', 'stellarwp-migrations' ) );
				}
			} )
			.finally( function() {
				logsState.loading = false;
			} );
	}

	/**
	 * Create a log entry element.
	 *
	 * @param {Object} log The log data.
	 *
	 * @return {HTMLElement} The log element.
	 */
	function createLogElement( log ) {
		const type = ( log.type || 'info' ).toLowerCase();
		const message = log.message || '';
		const createdAt = log.created_at || '';

		const el = document.createElement( 'div' );
		el.className = 'stellarwp-migration-log stellarwp-migration-log--' + type;

		// Add icon (except for info type).
		const iconEl = document.createElement( 'span' );
		iconEl.className = 'stellarwp-migration-log__icon';
		iconEl.textContent = getLogIcon( type );
		el.appendChild( iconEl );

		// Add content.
		const contentEl = document.createElement( 'span' );
		contentEl.className = 'stellarwp-migration-log__content';

		const messageEl = document.createElement( 'span' );
		messageEl.className = 'stellarwp-migration-log__message';
		messageEl.textContent = message;
		contentEl.appendChild( messageEl );

		el.appendChild( contentEl );

		// Add timestamp.
		if ( createdAt ) {
			const timeEl = document.createElement( 'span' );
			timeEl.className = 'stellarwp-migration-log__time';
			timeEl.textContent = formatLogTime( createdAt );
			el.appendChild( timeEl );
		}

		return el;
	}

	/**
	 * Get the icon for a log type.
	 *
	 * @param {string} type The log type.
	 *
	 * @return {string} The icon character.
	 */
	function getLogIcon( type ) {
		switch ( type ) {
			case 'error':
				return '\u2718'; // ✘
			case 'warning':
				return '\u26A0'; // ⚠
			case 'debug':
				return '\u2699'; // ⚙
			default:
				return ''; // No icon for info.
		}
	}

	/**
	 * Format a log timestamp for display.
	 *
	 * @param {string} timestamp The ISO timestamp.
	 *
	 * @return {string} The formatted time.
	 */
	function formatLogTime( timestamp ) {
		try {
			const date = new Date( timestamp );
			return date.toLocaleTimeString( [], { hour: '2-digit', minute: '2-digit', second: '2-digit' } );
		} catch ( e ) {
			return timestamp;
		}
	}

	// Initialize when DOM is ready using wp.domReady.
	domReady( init );
} )( wp.domReady, wp.apiFetch, wp.i18n, jQuery, window.stellarwpMigrationsAdmin );
