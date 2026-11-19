<?php
/**
 * Multisite "Network → Editoria11y defaults" page: lets super-admins
 * author default values (and optionally lock them) for every per-site
 * setting.
 *
 * Visual parity by construction:
 *
 *   This page does NOT re-implement the per-site form. It calls the
 *   exact same `do_settings_sections( 'ed11y' )` registry, wrapped in
 *   `SettingsContext::push( 'network' )`. Every section, field, label,
 *   nested `<details>`, and widefat-table rendered on the per-site
 *   Settings page renders here too — only the network-context-only
 *   additions differ (a "Lock this default" checkbox after each field
 *   plus one bundle lock for the test/role assignment as a unit).
 *
 *   Field `name=` attributes from the buffered output are rewritten
 *   server-side so the form posts into the network blob shape
 *   (`free_values[KEY]`, `csa_values[KEY]`, `free_locked[KEY]`,
 *   `csa_locked[KEY]`, `network_tests_state[KEY]`,
 *   `network_tests_enabled[KEY]`). See {@see rewrite_field_names()}.
 *
 * Why a separate page rather than splicing onto the per-site one:
 *
 *   - WP's Settings API does not work cleanly under network admin
 *     (`options.php` is per-site). The standard pattern is a manual
 *     `admin_post_*` form handler + nonce, which is what this class
 *     implements.
 *   - Conflating per-site values with network defaults on the same
 *     screen invites the "which screen am I on?" footgun.
 *
 * The CSA license key itself is intentionally not part of this UI —
 * Freemius's network activation screen already provides equivalent
 * three-state semantics (network-active / delegated / per-site) and
 * owns the storage. See `editoria11y.php` Freemius init.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Network-admin "Editoria11y defaults" page registration and form handler.
 */
class NetworkSettingsPage {

	/** Page slug used in `admin.php?page=<slug>` and form action attrs. */
	const SLUG = 'ed11y-network';

	/** `admin_post_*` action key used by the form save handler. */
	const SAVE_ACTION = 'ed11y_save_network_defaults';

	/** Nonce action / field name pair. */
	const NONCE_ACTION = 'ed11y_network_defaults';
	const NONCE_FIELD  = 'ed11y_network_defaults_nonce';

	/**
	 * Wire all network-admin hooks the page needs.
	 *
	 * Called from {@see \Editoria11y\Plugin::admin()} only when
	 * `is_multisite()` is true.
	 */
	public function init(): void {
		add_action( 'network_admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_post_' . self::SAVE_ACTION, array( __CLASS__, 'handle_save' ) );
		add_filter( 'heartbeat_received', array( __CLASS__, 'heartbeat_received' ), 10, 2 );
	}

	/**
	 * Register Network → Settings → Editoria11y defaults.
	 */
	public static function register_menu(): void {
		$page = add_submenu_page(
			'settings.php',
			esc_html__( 'Editoria11y defaults', 'editoria11y' ),
			esc_html__( 'Editoria11y defaults', 'editoria11y' ),
			'manage_network_options',
			self::SLUG,
			array( __CLASS__, 'render_page' )
		);
		if ( $page ) {
			add_action( 'load-' . $page, array( SettingsPage::class, 'enqueue_styles_scripts' ) );
			add_action( 'load-' . $page, array( __CLASS__, 'enqueue_backfill_heartbeat' ) );
		}
	}

	/**
	 * Render the nav-tab row shown at the top of network-admin Editoria11y
	 * pages.
	 *
	 * Mirrors {@see SettingsPage::render_nav_tabs()} but for the network
	 * pair (Defaults + Custom rules). The Custom Rules tab is the only
	 * way for super-admins to reach the dedicated CRUD page — its
	 * submenu entry is hidden via CSS for parity with the per-site
	 * "License & Account" pattern.
	 *
	 * @param string $current Slug of the active page.
	 */
	public static function render_nav_tabs( string $current ): void {
		$tabs = array(
			self::SLUG => __( 'Defaults', 'editoria11y' ),
		);
		// Custom rules are a CSA feature, so this tab only exists in the
		// premium build. The preprocessor strips the block from the free
		// build; the runtime gate keeps free-mode multisite installs from
		// rendering a tab to a stripped page.

		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $slug => $label ) {
			$class = ( $slug === $current ) ? 'nav-tab nav-tab-active' : 'nav-tab';
			printf(
				'<a href="%s" class="%s">%s</a>',
				esc_url( add_query_arg( 'page', $slug, network_admin_url( 'settings.php' ) ) ),
				esc_attr( $class ),
				esc_html( $label )
			);
		}
		echo '</h2>';
	}

	/**
	 * Human-readable disclosure of the "all sites" / "lock" propagation
	 * semantics (finding A2).
	 *
	 * The backfill decides whether a site is "still tracking the network"
	 * by VALUE EQUALITY — it overwrites a site's stored value when that
	 * value is absent, equals ANY previous network default (the worker's
	 * durable history — {@see NetworkDefaultsWorker::record_history()}),
	 * or equals the hardcoded Editoria11y default (see
	 * {@see NetworkDefaultsWorker::apply_dirty_to_option()}). A per-site
	 * value that coincides with a default is therefore replaced. We do not
	 * track per-site "locally-owned" intent, so this surface warns admins
	 * of the overwrite rule rather than silently applying it.
	 *
	 * Deliberately says nothing about customized sites being "left
	 * untouched": that is true for "Default for all sites" but not for
	 * "Override for all sites", which force-writes and overlays at read
	 * time regardless of the site's value.
	 */
	public static function propagation_help_text(): string {
		return __(
			'Heads up: "Default for all sites" and "Override for all sites" overwrite a site\'s current value whenever that value is still an Editoria11y default or a previous network default — including a per-site value that happens to match a default.',
			'editoria11y'
		);
	}

	/**
	 * Render the page shell, then re-emit the per-site settings sections.
	 *
	 * One form, one submit. The save handler processes both option blobs
	 * (main + CSA) from the same POST.
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_network_options' ) ) {
			wp_die( esc_html__( 'Insufficient privileges.', 'editoria11y' ) );
		}
		$saved_notice  = isset( $_GET['updated'] ) && '1' === $_GET['updated']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$orphan_notice = isset( $_GET['orphans'] ) && '1' === $_GET['orphans']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$orphan_keys   = array();
		if ( $orphan_notice ) {
			$transient_key = 'ed11y_network_orphans_' . get_current_user_id();
			$stored        = get_transient( $transient_key );
			if ( is_array( $stored ) ) {
				$orphan_keys = array_values(
					array_filter(
						array_map( 'strval', $stored ),
						static function ( $value ) {
							return '' !== $value;
						}
					)
				);
			}
			delete_transient( $transient_key );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Editoria11y default or locked network settings', 'editoria11y' ); ?></h1>
			<?php self::render_nav_tabs( self::SLUG ); ?>
			<?php if ( $saved_notice ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Network defaults saved.', 'editoria11y' ); ?></p>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $orphan_keys ) ) : ?>
				<div class="notice notice-error">
					<p>
						<strong><?php esc_html_e( 'Network defaults were not saved.', 'editoria11y' ); ?></strong>
						<?php esc_html_e( 'You changed the following settings, but their propagation dropdown is still set to "No network default" — your change would not reach any site. Set the dropdown to "Default for new sites", "Default for all sites", or "Override for all sites" — or revert the value — and save again.', 'editoria11y' ); ?>
					</p>
					<ul style="list-style: disc; margin-left: 2em;">
						<?php foreach ( $orphan_keys as $label ) : ?>
							<li><code><?php echo esc_html( $label ); ?></code></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<?php self::render_backfill_status(); ?>

			<p class="description ed11y-propagation-help"><?php echo esc_html( self::propagation_help_text() ); ?></p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" autocomplete="off" class="ed11y-form-network">
				<input type="hidden" name="action" value="<?php echo esc_attr( self::SAVE_ACTION ); ?>" />
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>
				<?php
				SettingsContext::push( 'network' );
				try {
					ob_start();
					do_settings_sections( 'ed11y' );
					$html = (string) ob_get_clean();
					echo self::rewrite_field_names( $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} finally {
					SettingsContext::pop();
				}
				submit_button( esc_html__( 'Save network defaults', 'editoria11y' ), 'primary large' );
				ConditionalFields::print_script();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the backfill-status panel above the form.
	 *
	 * Reads {@see NetworkDefaultsWorker::get_state()} (a single network
	 * option) and renders a small status block. No `switch_to_blog` is
	 * needed — every metric we surface (total / processed / elapsed) lives
	 * on the worker's state option, which the worker updates after each
	 * batch.
	 *
	 * Mid-run save handling: when the network admin saves again while a
	 * propagation is running, the worker COALESCES the new value into the
	 * running job — extending its `olds` trail and resetting the cursor.
	 * That means the panel may show `processed` dropping back to 0 right
	 * after a save; we surface a small "value changed — re-walking from
	 * the start" note so the count reset doesn't look like a regression.
	 *
	 * While the worker is running, a `<meta http-equiv="refresh">` keeps
	 * the panel current without the super-admin manually reloading.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Status + ETA rendering with optional auto-refresh.
	 * @SuppressWarnings(PHPMD.NPathComplexity) Same — branches are independent status / ETA / error fragments.
	 */
	private static function render_backfill_status(): void {
		$state   = NetworkDefaultsWorker::get_state();
		$running = 'running' === ( $state['status'] ?? 'idle' );
		// The wrapper is always emitted (even when idle) so the heartbeat
		// handler has a stable target to swap inner HTML into when a
		// propagation starts after page load. `data-running` is the JS
		// throttle: while `1`, the heartbeat sender attaches a request
		// key and the server returns refreshed inner HTML; while `0`,
		// nothing is requested and the panel keeps its last state.
		printf(
			'<div id="ed11y-backfill-panel" data-running="%s">%s</div>',
			$running ? '1' : '0',
			self::render_backfill_status_inner() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal markup; per-field escaping happens inside the helper.
		);
	}

	/**
	 * Inner content for the backfill-status panel: a single WP notice
	 * div, or an empty string when the worker is idle.
	 *
	 * Public so the heartbeat handler can render the same markup on
	 * AJAX polls — see {@see heartbeat_received()}.
	 *
	 * @return string Sanitized HTML (escaping done inline per-field).
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Linear status → notice render with ETA, errors, and mid-run guidance branches.
	 * @SuppressWarnings(PHPMD.NPathComplexity) Same — branches are independent status / ETA / error fragments.
	 */
	public static function render_backfill_status_inner(): string {
		$state = NetworkDefaultsWorker::get_state();
		if ( 'idle' === ( $state['status'] ?? 'idle' ) ) {
			return '';
		}

		$total     = max( 0, (int) $state['total'] );
		$processed = max( 0, min( $total, (int) $state['processed'] ) );
		$remaining = max( 0, $total - $processed );

		$job_elapsed = ( $state['updated_at'] ?? 0 ) > ( $state['job_started_at'] ?? 0 )
			? ( (int) $state['updated_at'] - (int) $state['job_started_at'] )
			: 0;
		$eta         = '';
		if ( 'running' === $state['status'] && $processed > 0 && $remaining > 0 && $job_elapsed > 0 ) {
			$rate    = $processed / max( 1, $job_elapsed );
			$seconds = (int) round( $remaining / max( 0.001, $rate ) );
			if ( $seconds > 0 ) {
				$eta = human_time_diff( time(), time() + $seconds );
			}
		}

		// `.inline` keeps the notice inside our wrapper. WP core's
		// admin_notices hoist (`wp-admin/js/common.js`, the
		// `$('div.updated, div.error, div.notice').not('.inline, .below-h2')`
		// branch) otherwise yanks any `.notice` out of its natural DOM
		// position and moves it directly under the page `<h1>` —
		// which on first page load leaves the wrapper empty, then the
		// heartbeat refill drops the new notice into the (now-empty)
		// wrapper below the nav tabs, so the panel appears in two
		// different spots on the same screen.
		$class = 'notice notice-info inline';
		if ( 'completed' === $state['status'] ) {
			$class = 'notice notice-success is-dismissible inline';
		} elseif ( 'failed' === $state['status'] ) {
			$class = 'notice notice-error is-dismissible inline';
		}

		$progress_line = sprintf(
			/* translators: 1: processed sites, 2: total sites, 3: sites updated */
			esc_html__( '%1$s of %2$s sites visited (%3$s updated).', 'editoria11y' ),
			number_format_i18n( $processed ),
			number_format_i18n( $total ),
			number_format_i18n( (int) ( $state['updated'] ?? 0 ) )
		);
		ob_start();
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<p>
				<strong><?php self::print_status_label( (string) $state['status'] ); ?></strong>
				<?php echo ' ' . wp_kses_post( $progress_line ); ?>
				<?php if ( '' !== $eta ) : ?>
					<?php
					echo ' ' . esc_html(
						sprintf(
							/* translators: %s: human-readable duration estimate */
							__( 'Estimated %s remaining.', 'editoria11y' ),
							$eta
						)
					);
					?>
				<?php endif; ?>
			</p>
			<?php if ( ! empty( $state['errors'] ) ) : ?>
				<p class="description">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: number of errors recorded */
							__( 'Errors: %s (most recent retained). Check server logs for details.', 'editoria11y' ),
							number_format_i18n( count( (array) $state['errors'] ) )
						)
					);
					?>
				</p>
			<?php endif; ?>
			<?php if ( 'running' === $state['status'] ) : ?>
				<p class="description">
					<em><?php esc_html_e( 'Saving the form now will restart propagation with the new settings.', 'editoria11y' ); ?></em>
				</p>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * `load-<page>` callback: enqueue the WP Heartbeat module and the
	 * tiny client that swaps the backfill panel on `heartbeat-tick`.
	 *
	 * Both handles depend on jQuery; Heartbeat depends on it too, so
	 * declaring it as a dep just keeps the order explicit.
	 */
	public static function enqueue_backfill_heartbeat(): void {
		wp_enqueue_script( 'heartbeat' );
		wp_enqueue_script(
			'ed11y-network-backfill-heartbeat',
			trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-network-backfill.js',
			array( 'heartbeat', 'jquery' ),
			\Editoria11y\Plugin::VERSION,
			true
		);
	}

	/**
	 * `heartbeat_received` filter: ship the refreshed backfill-status
	 * panel HTML to clients that have asked for it.
	 *
	 * The polling cadence is WP's Heartbeat default (~15s on active
	 * tabs, auto-suspending when the tab is backgrounded). Compared
	 * with the previous `<meta http-equiv="refresh">` approach this
	 * doesn't blow away the in-progress form edits below the panel,
	 * which is the whole reason for the swap.
	 *
	 * @param array<string,mixed> $response Heartbeat response payload.
	 * @param array<string,mixed> $data     Client-supplied request data.
	 * @return array<string,mixed>
	 */
	public static function heartbeat_received( $response, $data ) {
		if ( empty( $data['ed11y_backfill_request'] ) ) {
			return $response;
		}
		if ( ! current_user_can( 'manage_network_options' ) ) {
			return $response;
		}

		$state = NetworkDefaultsWorker::get_state();

		$response['ed11y_backfill_panel']   = self::render_backfill_status_inner();
		$response['ed11y_backfill_running'] = ( 'running' === ( $state['status'] ?? 'idle' ) );
		return $response;
	}

	/**
	 * Print the human-readable label for a backfill state status.
	 *
	 * @param string $status One of 'running' / 'completed' / 'failed' / 'idle'.
	 */
	private static function print_status_label( string $status ): void {
		switch ( $status ) {
			case 'running':
				esc_html_e( 'Propagating network defaults to existing sites…', 'editoria11y' );
				break;
			case 'completed':
				esc_html_e( 'Network defaults propagated.', 'editoria11y' );
				break;
			case 'failed':
				esc_html_e( 'Network defaults propagation failed.', 'editoria11y' );
				break;
			default:
				esc_html_e( 'Network defaults propagation idle.', 'editoria11y' );
		}
	}

	/**
	 * Rewrite per-site `name=` attributes into the network form's POST
	 * namespaces.
	 *
	 * Order matters: more-specific patterns first so the generic flat-key
	 * rewrite doesn't swallow the nested-array shapes.
	 *
	 * @param string $html Buffered output of `do_settings_sections('ed11y')`.
	 */
	public static function rewrite_field_names( string $html ): string {
		// Each pattern matches BOTH `name=` and `data-when-input=` carriers:
		// ConditionalFields stores the trigger field's name in a
		// `data-when-input` attribute, and its JS drops any rule whose
		// recorded name no longer matches a rendered input — so leaving the
		// attribute un-rewritten silently disabled conditional reveals on
		// the network page (finding F5).
		//
		// CSA-nested per-key fields: ed11y_plugin_settings[csa_settings][KEY → csa_values[KEY
		// The trailing portion ("]" alone OR "][SLUG]" for the roles
		// checkbox group) is left alone, so role checkboxes still post as
		// csa_values[roles][SLUG] which the sanitizer treats as an
		// assoc-array equivalent of the CSV form.
		$html = (string) preg_replace(
			'/(name|data-when-input)="ed11y_plugin_settings\[csa_settings\]\[([A-Za-z0-9_]+)\]/',
			'$1="csa_values[$2]',
			$html
		);

		// CSA-mode 3-way per-test routing → network_tests_state[KEY].
		$html = (string) preg_replace(
			'/(name|data-when-input)="ed11y_plugin_settings\[tests_state\]\[([A-Za-z0-9_]+)\]"/',
			'$1="network_tests_state[$2]"',
			$html
		);

		// Free-mode per-test checkboxes → network_tests_enabled[KEY].
		$html = (string) preg_replace(
			'/(name|data-when-input)="ed11y_plugin_settings\[tests_enabled\]\[([A-Za-z0-9_]+)\]"/',
			'$1="network_tests_enabled[$2]"',
			$html
		);

		// Generic flat keys → free_values[KEY].
		$html = (string) preg_replace(
			'/(name|data-when-input)="ed11y_plugin_settings\[([A-Za-z0-9_]+)\]"/',
			'$1="free_values[$2]"',
			$html
		);

		// Fail-loud guard (finding A1). Any per-site field whose name shape
		// matched none of the four patterns above still carries its
		// `ed11y_plugin_settings[...]` name, so it posts into a bucket the
		// network save handler never reads — its network default silently
		// never saves. The parity test class-test-network-field-rewrite.php
		// asserts this can't happen for the shipped field set; this runtime
		// log catches a field shape that slips past the test (e.g. a
		// build-conditional field) so it surfaces in the error log instead
		// of vanishing without a trace.
		if ( false !== strpos( $html, 'name="ed11y_plugin_settings[' )
			|| false !== strpos( $html, 'data-when-input="ed11y_plugin_settings[' )
		) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- diagnostic for a silent network-defaults save gap.
			error_log( '[Editoria11y] NetworkSettingsPage::rewrite_field_names left an un-rewritten ed11y_plugin_settings[...] field; its network default will not save. See finding A1.' );
		}

		return $html;
	}

	/**
	 * `admin_post_<SAVE_ACTION>` handler.
	 *
	 * Validates nonce + capability, hands the POST to
	 * {@see save_from_post()}, and redirects back to the page with either
	 * the saved notice or the orphan-rejection notice.
	 */
	public static function handle_save(): void {
		if ( ! current_user_can( 'manage_network_options' ) ) {
			wp_die( esc_html__( 'Insufficient privileges.', 'editoria11y' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( self::NONCE_ACTION, self::NONCE_FIELD );

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce checked above.
		$post = wp_unslash( $_POST );
		// phpcs:enable

		$result = self::save_from_post( (array) $post );

		if ( ! $result['saved'] ) {
			set_transient(
				'ed11y_network_orphans_' . get_current_user_id(),
				$result['orphans'],
				MINUTE_IN_SECONDS
			);
			$redirect = add_query_arg(
				array(
					'page'    => self::SLUG,
					'orphans' => '1',
				),
				network_admin_url( 'settings.php' )
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		$redirect = add_query_arg(
			array(
				'page'    => self::SLUG,
				'updated' => '1',
			),
			network_admin_url( 'settings.php' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Validate, orphan-check, store, and enqueue one network-form POST.
	 *
	 * Runs both validators against the single POST in BOTH builds. The
	 * CSA blob is not premium-gated here on purpose: the tests + roles
	 * bundle dropdown renders in the free build too, and its mode lives in
	 * the CSA blob's `modes` while governing the main blob's `tests_off`.
	 * Gating the CSA half used to leave the free build's bundle dropdown
	 * un-saveable and its test selection un-propagated. The CSA-only
	 * *fields* are still absent from a free-mode render, and
	 * {@see NetworkSettingsValidator::keep_csa_storage_for_free_form()}
	 * keeps the previously authored CSA storage intact in that case.
	 *
	 * Backfill: before writing the new network blobs, we snapshot the
	 * previous storage so {@see NetworkDefaultsWorker::diff_dirty_keys()}
	 * can compute which `'all'`-mode keys changed and which previous values
	 * an existing site might still be tracking. Every changed value is
	 * also recorded in the worker's durable history
	 * ({@see NetworkDefaultsWorker::record_history()}) — mode-agnostic, so
	 * a value published under "new sites only" is still recognized once
	 * the key is later switched to "all". The worker uses all three
	 * (any previous network value, the durable history, AND the hardcoded
	 * default) when deciding whether to overwrite a per-site stored value
	 * — see the worker class docblock.
	 *
	 * Split out of {@see handle_save()} so the pipeline can be exercised
	 * without nonce / redirect / exit plumbing.
	 *
	 * @param array<string,mixed> $post POST array after wp_unslash().
	 * @return array{saved: bool, orphans: array<int,string>, main_dirty: array<string,array{old:mixed,new:mixed,force:bool}>, csa_dirty: array<string,array{old:mixed,new:mixed,force:bool}>}
	 */
	public static function save_from_post( array $post ): array {
		// Snapshot previous storage BEFORE writing the new blobs. Used by
		// the backfill worker's three-way comparison ("absent | equals old |
		// equals hardcoded → overwrite, else preserve").
		$old_free = ed11y_get_network_default_settings_storage();
		$old_csa  = ed11y_get_network_default_csa_settings_storage();

		$free_blob = NetworkSettingsValidator::validate_free( $post );
		$csa_blob  = NetworkSettingsValidator::validate_csa( $post );
		// CSA-mode form: mirror "nobody" content-test routing into the
		// main blob's tests_off, which the bundle-lock overlay and the
		// save-time bundle coercion read (finding F4). No-op for a
		// free-mode form.
		$free_blob = NetworkSettingsValidator::mirror_main_tests_off( $free_blob, $post );
		// Free-mode form: the CSA fields were never on the page, so keep
		// what the super-admin authored while licensed (bundle mode aside).
		$csa_blob = NetworkSettingsValidator::keep_csa_storage_for_free_form( $old_csa, $csa_blob, $post );

		// Orphan validation: reject the save outright when a value changed
		// but the matching propagation-mode dropdown is "No network
		// default" — the admin almost certainly meant to also flip the
		// dropdown. Done BEFORE the option writes so the previous storage
		// stays intact on rejection (the "revert" half of block + revert).
		$new_free_norm = ed11y_normalize_network_default_storage( $free_blob );
		$new_csa_norm  = ed11y_normalize_network_default_storage( $csa_blob );
		$orphans       = NetworkDefaultsWorker::detect_orphan_changed_keys(
			$old_free,
			$new_free_norm,
			$old_csa,
			$new_csa_norm
		);
		if ( ! empty( $orphans ) ) {
			return array(
				'saved'      => false,
				'orphans'    => $orphans,
				'main_dirty' => array(),
				'csa_dirty'  => array(),
			);
		}

		update_site_option( 'ed11y_network_default_settings', $free_blob );
		update_site_option( 'ed11y_network_default_csa_settings', $csa_blob );

		// Queue the backfill — only `'all'`-mode keys whose value/mode
		// actually changed end up in the dirty set. No-op when nothing
		// changed.
		$main_dirty = array();
		$csa_dirty  = array();
		try {
			// Record BEFORE diffing so the history is intact even if the
			// diff / enqueue below throws — the next save will still see
			// every value the network has held.
			NetworkDefaultsWorker::record_history( $old_free, $new_free_norm, $old_csa, $new_csa_norm );
			$main_dirty = NetworkDefaultsWorker::diff_dirty_keys( $old_free, $new_free_norm );
			$csa_dirty  = NetworkDefaultsWorker::diff_dirty_keys( $old_csa, $new_csa_norm );
			// Cross-blob bundle expansion: tests_off has destinations in
			// both blobs, so it cannot be diffed by the per-blob walks
			// above (the bundle mode lives only in CSA modes).
			$bundle_dirty = NetworkDefaultsWorker::diff_bundle_dirty_keys(
				$old_free,
				$new_free_norm,
				$old_csa,
				$new_csa_norm
			);
			$main_dirty   = array_merge( $main_dirty, $bundle_dirty['main'] );
			$csa_dirty    = array_merge( $csa_dirty, $bundle_dirty['csa'] );
			NetworkDefaultsWorker::enqueue( $main_dirty, $csa_dirty );
		} catch ( \Throwable $e ) {
			// Worker / autoload failure must not block the form save —
			// the network defaults are already written to storage by this
			// point. Surface the failure to error_log; super-admin can
			// inspect via the worker state option.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- one-shot diagnostic for save-time worker failure.
			error_log( '[Editoria11y] NetworkDefaultsWorker::enqueue() threw: ' . $e->getMessage() );
		}

		return array(
			'saved'      => true,
			'orphans'    => array(),
			'main_dirty' => $main_dirty,
			'csa_dirty'  => $csa_dirty,
		);
	}
}
