<?php
/**
 * Schema migration UI: site-wide notice + inline progress panel + AJAX worker.
 *
 * Three concerns, all keyed on `Editoria11y\Installer::schema_state()`:
 *
 *   1. `admin_notice()` — wp-admin-wide reminder that surfaces the in-progress /
 *      failed schema state on every screen so the admin can't miss it.
 *   2. `render()` — the inline panel printed at the top of the Editoria11y
 *      settings page; drives a small JS loop that POSTs to the AJAX handler
 *      below until the schema reaches `hashed-only`.
 *   3. `handle_ajax_step()` — the worker AJAX endpoint. The same step is what
 *      the WP-Cron worker exercises one batch at a time on sites where no
 *      admin visits the settings page.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

use Editoria11y\Installer;

defined( 'ABSPATH' ) || exit;

/**
 * Owns the migration-state UX surfaces.
 */
class MigrationPanel {

	const AJAX_ACTION = 'ed11y_migration_step';
	const NONCE       = 'ed11y_migration';

	/** Wire admin_notices + the AJAX endpoint. */
	public function init() {
		add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( __CLASS__, 'handle_ajax_step' ) );
	}

	/**
	 * Render an admin notice when the schema migration is pending, in
	 * progress, or failed. Visible on every wp-admin page so an admin
	 * notices regardless of which screen they're on; deeper progress UI
	 * lives on the settings page (see `render()`).
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public static function admin_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Prime the schema on first admin pageview for sites that came up
		// without an activation hook firing (multisite subsites under
		// network activation, or any site where the runtime read paths
		// haven't been hit yet). check_tables() short-circuits cheaply
		// once at 2.0, so this is effectively free on the hot path.
		Installer::check_tables();
		$state     = Installer::schema_state();
		$version   = (string) get_option( 'editoria11y_db_version', '' );
		$is_failed = '-failed' === substr( $version, -7 );

		// Nothing to surface when the schema is fully migrated and not in a -failed marker.
		if ( 'hashed-only' === $state && ! $is_failed ) {
			return;
		}
		// Skip the notice on the settings page itself — the inline panel
		// covers it in more detail there.
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'settings_page_ed11y' === $screen->id ) {
			return;
		}

		$settings_url = admin_url( 'options-general.php?page=ed11y' );
		$class        = $is_failed || 'broken' === $state ? 'notice-error' : 'notice-warning';
		$message      = '';
		switch ( $state ) {
			case 'pre-v3':
				$message = __( 'Editoria11y needs to update its database tables.', 'editoria11y' );
				break;
			case 'dual':
				$message = $is_failed
					? __( 'Editoria11y database update encountered an error during finalization.', 'editoria11y' )
					: __( 'Editoria11y is updating existing dismissal records in the background.', 'editoria11y' );
				break;
			case 'broken':
				$message = __( 'Editoria11y database update failed and writes are paused.', 'editoria11y' );
				break;
			default:
				return;
		}
		?>
		<div class="notice <?php echo esc_attr( $class ); ?>">
			<p>
				<?php echo esc_html( $message ); ?>
				<a href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open Editoria11y settings', 'editoria11y' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Inline progress panel rendered at the top of the Editoria11y
	 * settings page.
	 *
	 * Drives a small JS loop that repeatedly POSTs to
	 * admin-ajax.php?action=ed11y_migration_step until the schema is at 2.0.
	 */
	public static function render() {
		// Same priming rationale as admin_notice(): the settings page may
		// be the first thing an admin loads on a fresh subsite, before any
		// read path has called check_tables(). Without this, schema_state()
		// would return 'pre-v3' and the panel would falsely demand an upgrade.
		Installer::check_tables();
		$state   = Installer::schema_state();
		$version = (string) get_option( 'editoria11y_db_version', '' );
		if ( 'hashed-only' === $state && '-failed' !== substr( $version, -7 ) ) {
			return;
		}
		$nonce = wp_create_nonce( self::NONCE );
		$ajax  = admin_url( 'admin-ajax.php' );
		$i18n  = array(
			'failed'      => __( 'Update failed. Please retry.', 'editoria11y' ),
			/* translators: 1: number of legacy dismissals already migrated, 2: total count detected at start. */
			'progress'    => __( 'Migrating dismissals: %1$d of %2$d processed.', 'editoria11y' ),
			'complete'    => __( 'Database update complete.', 'editoria11y' ),
			'working'     => __( 'Working…', 'editoria11y' ),
			'priorFailed' => __( 'A previous update attempt did not finish.', 'editoria11y' ),
			/* translators: %d: number of dismissal records the background migration could not convert. */
			'stalled'     => __( 'The update stopped: %d dismissal records could not be converted after repeated attempts. The checker remains fully functional. Retry to attempt them again.', 'editoria11y' ),
			'preV3'       => __( 'A schema update is required.', 'editoria11y' ),
			'dual'        => __( 'Background data update is in progress. Click below to drive it to completion now.', 'editoria11y' ),
		);
		?>
		<div class="notice notice-info" id="ed11y-migration-panel" style="padding: 12px;">
			<h2 style="margin-top:0;"><?php esc_html_e( 'Editoria11y database update', 'editoria11y' ); ?></h2>
			<p id="ed11y-migration-status">
				<?php esc_html_e( 'Checking schema state…', 'editoria11y' ); ?>
			</p>
			<p>
				<button type="button" class="button button-primary" id="ed11y-migration-run">
					<?php esc_html_e( 'Run update now', 'editoria11y' ); ?>
				</button>
				<button type="button" class="button" id="ed11y-migration-retry" style="display:none;">
					<?php esc_html_e( 'Retry', 'editoria11y' ); ?>
				</button>
			</p>
			<progress id="ed11y-migration-progress" max="100" value="0" style="width:100%; display:none;"></progress>
		</div>
		<script>
		(function () {
			var ajaxUrl = <?php echo wp_json_encode( $ajax ); ?>;
			var nonce   = <?php echo wp_json_encode( $nonce ); ?>;
			var state   = <?php echo wp_json_encode( $state ); ?>;
			var version = <?php echo wp_json_encode( $version ); ?>;
			var i18n    = <?php echo wp_json_encode( $i18n ); ?>;

			var btnRun    = document.getElementById('ed11y-migration-run');
			var btnRetry  = document.getElementById('ed11y-migration-retry');
			var status    = document.getElementById('ed11y-migration-status');
			var bar       = document.getElementById('ed11y-migration-progress');
			var totalSeen = 0;

			function step(retry) {
				var body = new URLSearchParams();
				body.set('action', 'ed11y_migration_step');
				body.set('_ajax_nonce', nonce);
				if (retry) {
					body.set('retry', '1');
				}
				return fetch(ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					body: body
				}).then(function (r) { return r.json(); });
			}

			function render(payload) {
				if (!payload || !payload.success) {
					status.textContent = (payload && payload.data && payload.data.message)
						? payload.data.message
						: i18n.failed;
					btnRun.style.display   = 'none';
					btnRetry.style.display = '';
					return;
				}
				var d = payload.data;
				version = d.version;
				state   = d.schema_state;
				if (d.failed) {
					// Sticky -failed marker: stop polling and hand control
					// back to the Retry button — the server no longer
					// auto-clears the marker on plain steps, so looping
					// would just spin on "Working…" forever. A straggler
					// count means the stall circuit-breaker tripped rather
					// than a DDL error; name the rows so the admin knows
					// what Retry will re-attempt.
					bar.style.display = 'none';
					status.textContent = d.stragglers > 0
						? i18n.stalled.replace('%d', d.stragglers)
						: i18n.priorFailed;
					btnRun.style.display   = 'none';
					btnRetry.style.display = '';
					btnRetry.disabled = false;
					return;
				}
				if (d.remaining > totalSeen) {
					totalSeen = d.remaining + (d.processed || 0);
				}
				if (d.remaining > 0 && totalSeen > 0) {
					bar.style.display = '';
					bar.max = totalSeen;
					bar.value = totalSeen - d.remaining;
					status.textContent = i18n.progress
						.replace('%1$d', totalSeen - d.remaining)
						.replace('%2$d', totalSeen);
				} else if (d.schema_state === 'hashed-only') {
					bar.style.display = 'none';
					status.textContent = i18n.complete;
					btnRun.style.display   = 'none';
					btnRetry.style.display = 'none';
					setTimeout(function () {
						var p = document.getElementById('ed11y-migration-panel');
						if (p) { p.style.display = 'none'; }
					}, 2500);
					return;
				} else {
					status.textContent = i18n.working;
				}
				// Keep stepping while there's work to do.
				if (d.remaining > 0 || d.schema_state !== 'hashed-only') {
					setTimeout(function () { step(false).then(render); }, 250);
				}
			}

			btnRun.addEventListener('click', function () {
				btnRun.disabled = true;
				step(false).then(render);
			});
			btnRetry.addEventListener('click', function () {
				btnRetry.disabled = true;
				step(true).then(function (p) { btnRetry.disabled = false; render(p); });
			});

			// Initial state hint.
			if (state === 'broken' || version.slice(-7) === '-failed') {
				status.textContent = i18n.priorFailed;
				btnRun.style.display   = 'none';
				btnRetry.style.display = '';
			} else if (state === 'pre-v3') {
				status.textContent = i18n.preV3;
			} else if (state === 'dual') {
				status.textContent = i18n.dual;
			}
		}());
		</script>
		<?php
	}

	/**
	 * AJAX handler that drives a single migration step.
	 *
	 * Branches on current state:
	 *   - retry=1 OR state=broken: clears the -failed marker, calls
	 *     check_tables.
	 *   - dual / dual-with-pending-rows: processes one rehash batch.
	 *   - rehash-complete: lets check_tables run the type narrow.
	 *
	 * Returns: `{ success, data: { version, schema_state, processed,
	 * remaining, message? } }`.
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function handle_ajax_step() {
		check_ajax_referer( self::NONCE );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'editoria11y' ) ), 403 );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- check_ajax_referer above.
		$retry   = ! empty( $_POST['retry'] );
		$version = (string) get_option( 'editoria11y_db_version', '' );

		// Only an EXPLICIT retry clears a sticky -failed marker. The old
		// `$retry || '-failed'` condition meant the JS polling loop
		// auto-retried a persistently failing step every 250ms forever,
		// defeating the circuit breaker and never surfacing the failure —
		// a plain step against a -failed version now reports it instead
		// (see the `failed` flag below).
		if ( $retry ) {
			Installer::retry_migration();
		}

		$state     = Installer::schema_state();
		$processed = 0;
		$remaining = 0;

		if ( 'pre-v3' === $state || 'broken' === $state ) {
			Installer::check_tables();
			$state = Installer::schema_state();
		}

		if ( 'dual' === $state ) {
			$batch     = Installer::rehash_batch();
			$processed = (int) $batch['processed'];
			$remaining = (int) $batch['remaining'];
			// rehash_batch() advances version to 2.0-narrow-pending when
			// remaining=0; the next check_tables call will run the type
			// narrow.
			if ( 0 === $remaining ) {
				Installer::check_tables();
			}
		}

		$state   = Installer::schema_state();
		$version = (string) get_option( 'editoria11y_db_version', '' );

		wp_send_json_success(
			array(
				'version'      => $version,
				'schema_state' => $state,
				'processed'    => $processed,
				'remaining'    => $remaining,
				// The JS loop stops and re-surfaces the Retry button on
				// this flag instead of polling a wedged migration forever.
				'failed'       => '-failed' === substr( $version, -7 ),
				// Non-zero when the rehash stall circuit-breaker tripped:
				// this many rows survived two full walks unconverted. The
				// JS renders the row-count message instead of the generic
				// failure text.
				'stragglers'   => (int) get_option( 'editoria11y_rehash_stragglers', 0 ),
			)
		);
	}
}
