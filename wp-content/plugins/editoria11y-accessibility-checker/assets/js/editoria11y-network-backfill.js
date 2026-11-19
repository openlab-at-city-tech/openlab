/**
 * Live-refresh the network-defaults backfill status panel via the WP
 * Heartbeat API, without reloading the surrounding settings form.
 *
 * Contract with the server (NetworkSettingsPage::heartbeat_received):
 *   - On heartbeat-send, attach `ed11y_backfill_request: 1` while the
 *     panel's `data-running` attribute is `"1"`. Once we observe the
 *     worker has transitioned to a terminal state we stop attaching the
 *     key, so the server stops shipping the panel HTML — the last seen
 *     state is what the user keeps looking at.
 *   - On heartbeat-tick, replace the panel's inner HTML with the
 *     server-rendered chunk and update `data-running` to mirror the
 *     reported state. After the swap we trigger `wp-notice-added` so
 *     WP's `makeNoticesDismissible()` rebinds the close button on any
 *     freshly injected `.is-dismissible` notice (the DOMReady binder
 *     only runs once and won't see dynamically inserted nodes).
 */
( function ( $ ) {
	var panel = document.getElementById( 'ed11y-backfill-panel' );
	if ( ! panel ) {
		return;
	}

	$( document ).on( 'heartbeat-send', function ( event, data ) {
		if ( '1' === panel.getAttribute( 'data-running' ) ) {
			data.ed11y_backfill_request = 1;
		}
	} );

	$( document ).on( 'heartbeat-tick', function ( event, data ) {
		if ( typeof data.ed11y_backfill_panel !== 'string' ) {
			return;
		}
		panel.innerHTML = data.ed11y_backfill_panel;
		panel.setAttribute( 'data-running', data.ed11y_backfill_running ? '1' : '0' );
		$( document ).trigger( 'wp-notice-added' );
	} );
}( jQuery ) );
