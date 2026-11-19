<?php
/**
 * Cron-driven worker that propagates "All sites" network defaults out to
 * every subsite in a multisite network, and the site-creation seeder that
 * handles the "new sites only" mode + the new-site half of "all sites".
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Backfill worker + site-creation seeder for unlocked network defaults.
 *
 * Why this exists:
 *   The network-defaults read pipeline ({@see ed11y_get_settings()}) no
 *   longer overlays unlocked network values onto a site's stored option —
 *   doing so silently undid per-site clears, which was a bug. Unlocked
 *   network defaults now propagate to site storage out-of-band:
 *
 *     - mode = 'new'  → seeded once into a new site's `ed11y_plugin_settings`
 *                       (and `ed11y_csa_plugin_settings`) at site creation.
 *     - mode = 'all'  → seeded at creation AND backfilled into existing
 *                       sites whose stored value is "still tracking the
 *                       network": absent, equal to ANY previous network
 *                       value, or equal to the hardcoded default. Sites
 *                       whose value is anything else are left alone.
 *     - mode = 'lock' → enforced at read time; no propagation needed.
 *
 *   The backfill is what this class owns. The "still tracking" check
 *   needs the *previous* network value(s), which the save handler in
 *   {@see NetworkSettingsPage::handle_save()} snapshots before writing
 *   the new blob.
 *
 * Multi-old / live-coalesce model:
 *   Each dirty key carries an `olds[]` list of every previous network value
 *   in the current propagation chain, plus the latest `new` value. When a
 *   save lands mid-run, the worker COALESCES into the running job rather
 *   than queueing a separate run:
 *
 *     - Append the just-overwritten network value to each affected key's
 *       `olds` list.
 *     - Update each affected key's `new` to the latest value.
 *     - Reset cursor to 0 so already-visited sites get re-walked under the
 *       updated payload.
 *
 *   The multi-old approach handles the "admin made a typo, saves a new
 *   value mid-run" cancellation case gracefully; both values update.
 *
 * Durable history trail:
 *   A run's `olds[]` only accumulates while that run is in flight, and
 *   a small network finishes a run in one tick — so on its own the
 *   in-run trail is one entry deep, and any site that misses a single
 *   link (a value edited under "new sites only", a save that never
 *   ran, an archived site) would look customized forever. The save
 *   handler therefore also records every network value a key has ever
 *   held ({@see record_history()}) in `ed11y_network_defaults_history`,
 *   regardless of mode, and {@see enqueue()} folds that history into
 *   each dirty key's `olds` for fresh runs and coalesces alike. A site
 *   is "still tracking the network" if it holds ANY value the network
 *   ever published for that key — which is what the network settings
 *   page promises ({@see NetworkSettingsPage::propagation_help_text()}).
 *
 *   Cap on both trails: MAX_OLDS_PER_KEY entries per key, oldest
 *   dropped first.
 *
 * Concurrency / state model:
 *   - State lives in a single network option `ed11y_network_defaults_backfill_state`.
 *   - Status state machine: idle → running → completed | failed.
 *   - One worker per network at a time. The `wp_cache_add` lock only
 *     serializes ticks that share a request-lifetime object cache: with a
 *     persistent drop-in it prevents two overlapping ticks; without one it
 *     is request-local and the real cross-request serializer is WP-Cron's
 *     own `doing_cron` guard. Per-blog writes are idempotent (the
 *     three-way overwrite rule + the `site_value === new_value`
 *     short-circuit), so an overlap re-walks harmlessly rather than
 *     corrupting state.
 *   - The cursor is the high-water `blog_id` already processed, walked
 *     via direct SQL on `{$wpdb->blogs}` with `WHERE blog_id > $cursor
 *     ORDER BY blog_id ASC`. A mid-run save resets cursor=0 and re-walks.
 *
 * Cadence:
 *   Self-rescheduling single events with a 15s minimum gap (override via
 *   `ED11Y_DEFAULTS_WORKER_MIN_GAP_SECONDS`). Mirrors
 *   {@see NetworkLicenseWorker}.
 *
 * Pattern source:
 *   Direct mirror of {@see NetworkLicenseWorker} — same cursor / lock /
 *   error-buffer / state-shape primitives. Diverges in the per-blog tick
 *   body and the multi-old coalesce semantics.
 *
 * Non-multisite:
 *   The cron tick is a no-op on single-site installs — `is_multisite()`
 *   short-circuits the cursor SQL so it never touches `$wpdb->blogs`
 *   (which is empty on single-site and would emit a SQL syntax error).
 *   The seeder is only wired on `wp_initialize_site`, which only fires on
 *   multisite, so it doesn't need its own guard.
 */
final class NetworkDefaultsWorker {

	/** Network option key storing the worker state machine. */
	const OPTION_KEY = 'ed11y_network_defaults_backfill_state';

	/**
	 * Network option key storing the durable per-key trail of every value
	 * the network has published. Deliberately separate from OPTION_KEY:
	 * the run state is transient and {@see reset()} discards it; the
	 * history must outlive every run or the orphaning bug returns.
	 */
	const HISTORY_OPTION_KEY = 'ed11y_network_defaults_history';

	/** WP-Cron hook the backfill tick is registered against. */
	const CRON_HOOK = 'ed11y_network_defaults_backfill';

	/** Single-writer lock key serializing concurrent ticks. */
	const LOCK_KEY = 'ed11y_network_defaults_backfill_lock';

	/** Sites processed per tick. */
	const BATCH_SIZE = 25;

	/** Maximum per-blog errors retained in the state ring buffer. */
	const MAX_ERRORS = 50;

	/** Maximum length of the per-key `olds` trail and of the durable history trail. */
	const MAX_OLDS_PER_KEY = 20;

	/** Default minimum seconds between ticks. */
	const DEFAULT_MIN_GAP_SECONDS = 15;

	/** Cache group shared with the other workers. */
	const CACHE_GROUP = 'editoria11y';

	/** Main-option name (per-site settings). */
	const MAIN_OPTION = 'ed11y_plugin_settings';

	/** CSA-option name (per-site CSA settings). */
	const CSA_OPTION = 'ed11y_csa_plugin_settings';

	/** Modes under which a network value reaches sites. */
	const PROPAGATING_MODES = array( 'new', 'all', 'lock' );

	/**
	 * Register cron hook + site-creation hook. Called from editoria11y.php at
	 * file load time. Free-build-safe: nothing here depends on CSA being
	 * active or on `ed11ycsa()` being present, beyond the inline
	 * `is__premium_only()` gates inside per-blog work.
	 */
	public static function register(): void {
		add_action( self::CRON_HOOK, array( __CLASS__, 'tick' ) );
		add_action( 'wp_initialize_site', array( __CLASS__, 'seed_new_site' ), 10, 1 );
	}

	/* ===== Site-creation seeder ===== */

	/**
	 * `wp_initialize_site` callback. Seeds the new site's per-site options
	 * with any network defaults whose mode is `'new'` or `'all'`.
	 *
	 * Locked-mode keys are intentionally skipped: their values reach the
	 * site at read time via {@see ed11y_get_settings()} / {@see
	 * ed11y_get_csa_settings()}, so seeding them into storage would be
	 * redundant.
	 *
	 * Thin wrapper around {@see seed_current_blog()}: the only thing this
	 * method adds is the blog_id extraction + switch_to_blog dance. The
	 * actual option work is testable without going through multisite-only
	 * symbols.
	 *
	 * @param \WP_Site|object $new_site The newly created site.
	 */
	public static function seed_new_site( $new_site ): void {
		if ( ! is_object( $new_site ) || empty( $new_site->blog_id ) ) {
			return;
		}
		$blog_id = (int) $new_site->blog_id;
		if ( $blog_id <= 0 ) {
			return;
		}
		// Defense in depth: in production this callback is wired to
		// `wp_initialize_site`, which only fires on multisite — but a
		// direct programmatic invocation on single-site would land here
		// without `switch_to_blog` being loaded. Skip cleanly.
		if ( ! function_exists( 'switch_to_blog' ) ) {
			return;
		}
		switch_to_blog( $blog_id );
		try {
			self::seed_current_blog();
		} finally {
			restore_current_blog();
		}
	}

	/**
	 * Seed the CURRENT blog's per-site options with any network defaults
	 * whose mode is `'new'` or `'all'`. Caller is responsible for the
	 * `switch_to_blog` dance (or for running in the right blog context).
	 *
	 * Public so unit tests can exercise the seed logic without depending
	 * on multisite-only globals.
	 */
	public static function seed_current_blog(): void {
		$main_storage = ed11y_get_network_default_settings_storage();
		// Read in BOTH builds: the bundle mode lives in the CSA blob but
		// governs the main blob's `tests_off`, which the free build's
		// network form authors (and propagates) too.
		$csa_storage = ed11y_get_network_default_csa_settings_storage();

		$seed_main = self::seed_values_for_storage( $main_storage );
		$seed_csa  = self::csa_active()
			? self::seed_values_for_storage( $csa_storage )
			: array();

		// Cross-blob bundle expansion: tests_off has a destination in
		// BOTH blobs (the routed-main and routed-csa values differ — see
		// {@see TestStateNormalizer::from_csa_post()}). The main half
		// seeds regardless of build; the CSA half only when CSA exists.
		$bundle_seed = self::seed_bundle_for_storages( $main_storage, $csa_storage );
		$seed_main   = array_merge( $seed_main, $bundle_seed['main'] );
		if ( self::csa_active() ) {
			$seed_csa = array_merge( $seed_csa, $bundle_seed['csa'] );
		}

		if ( empty( $seed_main ) && empty( $seed_csa ) ) {
			return;
		}
		self::merge_seed_into_option( self::MAIN_OPTION, $seed_main );
		self::merge_seed_into_option( self::CSA_OPTION, $seed_csa );
	}

	/**
	 * Extract `key => value` for every storage entry whose mode is `'new'`
	 * or `'all'` and whose value is non-empty. The result is the seed
	 * payload the new-site hook merges into per-site storage.
	 *
	 * Bundle-governed keys are intentionally skipped here for the same
	 * cross-blob reason described on {@see diff_dirty_keys()}; the bundle
	 * seed is computed cross-blob by {@see seed_bundle_for_storages()}
	 * and merged in by {@see seed_current_blog()}.
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $storage Normalized storage.
	 * @return array<string,mixed>
	 */
	private static function seed_values_for_storage( array $storage ): array {
		$seed        = array();
		$bundle_key  = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES;
		$bundle_keys = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS;

		foreach ( $storage['modes'] as $key => $mode ) {
			if ( $key === $bundle_key ) {
				continue;
			}
			if ( in_array( $key, $bundle_keys, true ) ) {
				continue;
			}
			if ( 'new' !== $mode && 'all' !== $mode ) {
				continue;
			}
			$value = $storage['values'][ $key ] ?? null;
			if ( '' === $value || null === $value ) {
				continue;
			}
			$seed[ $key ] = $value;
		}
		return $seed;
	}

	/**
	 * Cross-blob bundle expansion for new-site seeding.
	 *
	 * The bundle mode lives in the CSA blob's `modes`, but its governed
	 * keys are distributed across both blobs:
	 *
	 *   - `tests_off` exists in BOTH the main and CSA blobs (different
	 *     routed values — see {@see TestStateNormalizer::from_csa_post()}).
	 *   - `tests_content`, `tests_dev`, `roles` exist only in the CSA blob.
	 *
	 * When the bundle mode is `'new'` or `'all'`, every non-empty value
	 * above is added to the matching blob's seed payload.
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $main_storage Main-blob storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $csa_storage  CSA-blob storage.
	 * @return array{main: array<string,mixed>, csa: array<string,mixed>}
	 */
	private static function seed_bundle_for_storages( array $main_storage, array $csa_storage ): array {
		$bundle_key  = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES;
		$bundle_keys = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS;
		$main_seed   = array();
		$csa_seed    = array();

		$bundle_mode = $csa_storage['modes'][ $bundle_key ] ?? null;
		if ( 'new' !== $bundle_mode && 'all' !== $bundle_mode ) {
			return array(
				'main' => $main_seed,
				'csa'  => $csa_seed,
			);
		}

		// tests_off has a destination in both blobs.
		$main_tests_off = $main_storage['values']['tests_off'] ?? null;
		if ( '' !== $main_tests_off && null !== $main_tests_off ) {
			$main_seed['tests_off'] = $main_tests_off;
		}
		// All four governed keys (including the CSA-side tests_off) seed
		// the CSA blob.
		foreach ( $bundle_keys as $key ) {
			$value = $csa_storage['values'][ $key ] ?? null;
			if ( '' === $value || null === $value ) {
				continue;
			}
			$csa_seed[ $key ] = $value;
		}
		return array(
			'main' => $main_seed,
			'csa'  => $csa_seed,
		);
	}

	/**
	 * Cross-blob bundle expansion for the save → backfill diff.
	 *
	 * Same destination map as {@see seed_bundle_for_storages()}; differs
	 * in that this returns `{old, new}` pairs against the previous
	 * storage so the worker's olds trail can be built, and that the
	 * bundle's previous mode controls whether an unchanged value is
	 * still considered dirty (the "already propagated under the same
	 * value" no-op gate).
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_main Previous main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_main New main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_csa  Previous CSA storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_csa  New CSA storage.
	 * @return array{main: array<string, array{old:mixed,new:mixed}>, csa: array<string, array{old:mixed,new:mixed}>}
	 */
	public static function diff_bundle_dirty_keys( array $old_main, array $new_main, array $old_csa, array $new_csa ): array {
		$bundle_key      = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES;
		$bundle_keys     = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS;
		$new_bundle_mode = $new_csa['modes'][ $bundle_key ] ?? null;
		$old_bundle_mode = $old_csa['modes'][ $bundle_key ] ?? null;

		if ( 'all' !== $new_bundle_mode && 'lock' !== $new_bundle_mode ) {
			return array(
				'main' => array(),
				'csa'  => array(),
			);
		}

		$main_dirty = self::diff_bundle_keys_in_blob(
			array( 'tests_off' ),
			$old_main,
			$new_main,
			$old_bundle_mode,
			$new_bundle_mode
		);
		$csa_dirty  = self::diff_bundle_keys_in_blob(
			$bundle_keys,
			$old_csa,
			$new_csa,
			$old_bundle_mode,
			$new_bundle_mode
		);
		return array(
			'main' => $main_dirty,
			'csa'  => $csa_dirty,
		);
	}

	/**
	 * Per-blob slice of the bundle dirty walk — picked out so the two
	 * destination blobs in {@see diff_bundle_dirty_keys()} share one
	 * predicate set rather than copy-pasting.
	 *
	 * @param array<int,string>                                               $keys            Bundle keys to look up in this blob.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_blob        Previous blob storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_blob        New blob storage.
	 * @param string|null                                                     $old_bundle_mode Previous CSA bundle mode (single source of truth).
	 * @param string                                                          $new_bundle_mode Current CSA bundle mode (caller pre-gated to 'all' or 'lock').
	 * @return array<string, array{old:mixed, new:mixed, force:bool}>
	 */
	private static function diff_bundle_keys_in_blob( array $keys, array $old_blob, array $new_blob, $old_bundle_mode, string $new_bundle_mode ): array {
		$dirty = array();
		$force = ( 'lock' === $new_bundle_mode );
		foreach ( $keys as $key ) {
			$entry = self::classify_bundle_key( $key, $old_blob, $new_blob, $old_bundle_mode, $force );
			if ( null !== $entry ) {
				$dirty[ $key ] = $entry;
			}
		}
		return $dirty;
	}

	/**
	 * Decide whether one bundle key is "dirty" within a single blob, returning
	 * its propagation entry or null. Extracted from {@see diff_bundle_keys_in_blob()}
	 * so the per-key predicate chain (mode coercion + two no-op skips) lives in
	 * one place rather than inflating the loop's branch count.
	 *
	 * @param string                                                          $key             Bundle key to evaluate in this blob.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_blob        Previous blob storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_blob        New blob storage.
	 * @param string|null                                                     $old_bundle_mode Previous CSA bundle mode (single source of truth).
	 * @param bool                                                            $force           Whether the new bundle mode is 'lock' (lock-as-unit).
	 * @return array{old:mixed, new:mixed, force:bool}|null Dirty entry, or null when the key is unchanged or not propagated in this mode.
	 */
	private static function classify_bundle_key( string $key, array $old_blob, array $new_blob, $old_bundle_mode, bool $force ) {
		if ( $force ) {
			// Lock-as-unit: an absent key is meaningful (admins can
			// lock the bundle without filling in every governed key);
			// coerce sites to empty so the unit stays consistent.
			$new_value = $new_blob['values'][ $key ] ?? '';
		} else {
			// 'all' mode: skip keys not present in the new blob —
			// for cross-mode saves (e.g., free-mode admin save under
			// CSA build), the CSA-side bundle keys aren't authored
			// and shouldn't propagate as accidental clears.
			if ( ! array_key_exists( $key, $new_blob['values'] ) ) {
				return null;
			}
			$new_value = $new_blob['values'][ $key ];
		}
		$old_value = $old_blob['values'][ $key ] ?? null;
		if ( ! $force && 'all' === $old_bundle_mode && $old_value === $new_value ) {
			return null;
		}
		if ( $force && 'lock' === $old_bundle_mode && $old_value === $new_value ) {
			// True no-op under sustained lock.
			return null;
		}
		return array(
			'old'   => $old_value,
			'new'   => $new_value,
			'force' => $force,
		);
	}

	/**
	 * Detect "orphan" saves: keys whose value changed but whose
	 * propagation mode is "No network default" (mode absent or set to a
	 * value other than `'new'` / `'all'` / `'lock'`).
	 *
	 * The save handler uses this as a hard validation gate — a value that
	 * goes nowhere is almost always a UX mistake (the admin meant to also
	 * flip the mode dropdown but didn't), so we reject the save and let
	 * them either configure the mode or revert.
	 *
	 * Bundle-governed keys are grouped behind the bundle's mode; if the
	 * bundle is configured, none of its four governed keys can be orphans
	 * even if their individual values changed. Conversely, if the bundle
	 * is unconfigured and any of its governed values changed, the bundle
	 * itself surfaces as a single orphan entry (using its dropdown label)
	 * rather than four duplicates.
	 *
	 * Default-valued keys are never orphans. Selects and radios have no
	 * blank option, so an untouched network form posts the hardcoded
	 * default for every choice field on every save; and a stored value
	 * equal to the default with no mode IS the "no network default"
	 * state — nothing is stranded by saving it. Without this exemption a
	 * fresh network could never save the page at all, and an inert value
	 * could never be set back to the default without choosing a mode.
	 * The bundle applies the same rule against what an untouched CSA-mode
	 * form posts ({@see TestStateNormalizer::default_csa_routing()} and
	 * the default roles, compared as a set).
	 *
	 * Returns a list of human-readable labels for the offending keys, in
	 * the order they were detected.
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_main Previous main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_main New main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_csa  Previous CSA storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_csa  New CSA storage.
	 * @return array<int,string>
	 */
	public static function detect_orphan_changed_keys( array $old_main, array $new_main, array $old_csa, array $new_csa ): array {
		$bundle_key  = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES;
		$bundle_keys = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS;
		$orphans     = array();

		$new_bundle_mode = $new_csa['modes'][ $bundle_key ] ?? null;
		if (
			! in_array( $new_bundle_mode, self::PROPAGATING_MODES, true )
			&& self::bundle_values_changed( $old_main, $new_main, $old_csa, $new_csa )
		) {
			$orphans[] = __( 'Tests + roles assignment', 'editoria11y' );
		}

		// Per-key checks skip the bundle-governed keys (covered above) and,
		// on the CSA side, the synthetic bundle key itself.
		$main_defaults = ed11y_get_default_options();
		$csa_defaults  = ed11y_get_csa_default_options();
		$orphans       = array_merge(
			$orphans,
			self::orphan_keys_in_blob(
				$old_main,
				$new_main,
				is_array( $main_defaults ) ? $main_defaults : array(),
				$bundle_keys
			),
			self::orphan_keys_in_blob(
				$old_csa,
				$new_csa,
				is_array( $csa_defaults ) ? $csa_defaults : array(),
				array_merge( array( $bundle_key ), $bundle_keys )
			)
		);

		return array_values( array_unique( $orphans ) );
	}

	/**
	 * Per-blob half of {@see detect_orphan_changed_keys()}: keys whose
	 * non-empty, non-default value changed while their mode does not
	 * propagate.
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_blob  Previous blob storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_blob  New blob storage.
	 * @param array<string,mixed>                                             $defaults  Hardcoded defaults for this blob.
	 * @param array<int,string>                                               $skip_keys Keys governed elsewhere.
	 * @return array<int,string>
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Single loop of sequential continue gates; flattening would obscure the read.
	 */
	private static function orphan_keys_in_blob( array $old_blob, array $new_blob, array $defaults, array $skip_keys ): array {
		$orphans = array();
		foreach ( $new_blob['values'] as $key => $new_value ) {
			if ( in_array( $key, $skip_keys, true ) ) {
				continue;
			}
			if ( '' === $new_value || null === $new_value ) {
				continue;
			}
			$old_value = $old_blob['values'][ $key ] ?? null;
			if ( $old_value === $new_value ) {
				continue;
			}
			$mode = $new_blob['modes'][ $key ] ?? null;
			if ( in_array( $mode, self::PROPAGATING_MODES, true ) ) {
				continue;
			}
			if ( array_key_exists( $key, $defaults ) && $defaults[ $key ] === $new_value ) {
				continue;
			}
			$orphans[] = (string) $key;
		}
		return $orphans;
	}

	/**
	 * Whether any bundle-governed value changed to something other than
	 * empty or what an untouched CSA-mode form posts.
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_main Previous main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_main New main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_csa  Previous CSA storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_csa  New CSA storage.
	 */
	private static function bundle_values_changed( array $old_main, array $new_main, array $old_csa, array $new_csa ): bool {
		$main_off_old = $old_main['values']['tests_off'] ?? null;
		$main_off_new = $new_main['values']['tests_off'] ?? null;
		if ( $main_off_old !== $main_off_new && '' !== $main_off_new && null !== $main_off_new ) {
			return true;
		}
		foreach ( SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS as $key ) {
			$csa_old = $old_csa['values'][ $key ] ?? null;
			$csa_new = $new_csa['values'][ $key ] ?? null;
			if ( $csa_old === $csa_new || '' === $csa_new || null === $csa_new ) {
				continue;
			}
			if ( self::is_untouched_bundle_value( $key, $csa_new ) ) {
				continue;
			}
			return true;
		}
		return false;
	}

	/**
	 * Whether a posted CSA bundle value is exactly what the untouched
	 * form posts for that key.
	 *
	 * @param string $key   One of the bundle-governed CSA keys.
	 * @param mixed  $value Sanitized posted value.
	 */
	private static function is_untouched_bundle_value( string $key, $value ): bool {
		if ( 'roles' === $key ) {
			$default_roles = ed11y_get_csa_default_options( 'roles' );
			return is_string( $default_roles ) && RoleNormalizer::same_set( (string) $value, $default_roles );
		}
		$routing_keys = array(
			'tests_off'     => 'csa_off',
			'tests_content' => 'csa_content',
			'tests_dev'     => 'csa_dev',
		);
		if ( ! isset( $routing_keys[ $key ] ) ) {
			return false;
		}
		$routing = TestStateNormalizer::default_csa_routing();
		return $routing[ $routing_keys[ $key ] ] === $value;
	}

	/**
	 * Insert each `key => value` from `$seed` into the named option, but
	 * only where the key is currently absent. Never clobbers a stored
	 * value (including `''`). No-op when seed is empty.
	 *
	 * @param string              $option_name Option name on the current blog.
	 * @param array<string,mixed> $seed        Seed payload.
	 */
	private static function merge_seed_into_option( string $option_name, array $seed ): void {
		if ( empty( $seed ) ) {
			return;
		}
		$stored = get_option( $option_name, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$changed = false;
		foreach ( $seed as $key => $value ) {
			if ( array_key_exists( $key, $stored ) ) {
				continue;
			}
			$stored[ $key ] = $value;
			$changed        = true;
		}
		if ( $changed ) {
			SettingsStorage::write_canonical( $option_name, $stored );
		}
	}

	/* ===== Save-time diffing ===== */

	/**
	 * Diff two normalized storage blobs and return the per-key
	 * `(old, new)` pairs that need to be propagated.
	 *
	 * Output shape: `array<key, {old: mixed, new: mixed}>`. The save
	 * handler hands this to {@see enqueue()}; the worker turns the single
	 * `old` into / merges into a multi-element `olds[]` trail.
	 *
	 * A key is "dirty" when:
	 *   - its mode is `'all'` in the new storage AND
	 *   - it either is newly present, or its value/mode in the new storage
	 *     differs from the old.
	 *
	 * Bundle-governed keys ({@see SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS})
	 * are intentionally skipped here — the bundle key lives in the CSA
	 * blob's modes, but two of its governed keys (notably `tests_off`)
	 * live in the main blob's values. A per-blob diff cannot see both
	 * sides at once, so the bundle expansion is done at a higher level by
	 * {@see diff_bundle_dirty_keys()} and merged into the per-blob result
	 * by the save handler.
	 *
	 * A key that switched away from `'all'` (e.g. to `'new'` or to "no
	 * default") is NOT propagated — sites already updated keep the value;
	 * the network admin's intent is "stop propagating from now on", not
	 * "roll everything back".
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $previous Old storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $current  New storage.
	 * @return array<string, array{old:mixed, new:mixed}>
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Single per-key loop with seven sequential continue gates; flattening would obscure the read.
	 */
	public static function diff_dirty_keys( array $previous, array $current ): array {
		$dirty       = array();
		$bundle_key  = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES;
		$bundle_keys = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS;

		foreach ( $current['modes'] as $key => $mode ) {
			if ( $key === $bundle_key ) {
				continue;
			}
			// Per-key modes for bundle-governed keys are ignored — see
			// the docblock above.
			if ( in_array( $key, $bundle_keys, true ) ) {
				continue;
			}
			if ( 'all' !== $mode && 'lock' !== $mode ) {
				continue;
			}
			if ( ! isset( $current['values'][ $key ] ) ) {
				continue;
			}
			$new_value = $current['values'][ $key ];
			if ( '' === $new_value || null === $new_value ) {
				continue;
			}
			$old_value = $previous['values'][ $key ] ?? null;
			$old_mode  = $previous['modes'][ $key ] ?? null;
			// Lock mode propagates as a hard write that ignores the
			// three-way overwrite rule: the network admin's lock is the
			// single source of truth, and the write is what makes the
			// "toggle lock on, then off, to force-propagate" idiom work —
			// after the unlock, sites stay at the network value rather
			// than reverting to a pre-lock customization.
			$force = ( 'lock' === $mode );
			if ( ! $force && 'all' === $old_mode && $old_value === $new_value ) {
				continue;
			}
			if ( $force && 'lock' === $old_mode && $old_value === $new_value ) {
				// True no-op save: lock was already on and value didn't
				// change — nothing to force-write that wasn't already
				// written by a prior lock save.
				continue;
			}
			$dirty[ $key ] = array(
				'old'   => $old_value,
				'new'   => $new_value,
				'force' => $force,
			);
		}
		return $dirty;
	}

	/* ===== Enqueue + state ===== */

	/**
	 * Enqueue a backfill payload — start a new run, or coalesce into the
	 * running one.
	 *
	 * Coalesce rules (when status === 'running'):
	 *   - For each new dirty key that the running job already tracks:
	 *     append the new entry's `old` value to the running job's `olds`
	 *     trail (de-duping), then update `new` to the latest. Cap the
	 *     trail at MAX_OLDS_PER_KEY entries; drop the oldest if exceeded.
	 *   - For each new dirty key that the running job does NOT yet track:
	 *     add it with `olds = [ entry['old'] ]`, `new = entry['new']`.
	 *   - Reset cursor to 0 so already-visited sites get re-walked under
	 *     the updated payload. (Sites already on the latest value short-
	 *     circuit in apply_dirty_to_option, so the re-walk is cheap.)
	 *
	 * In both branches every dirty key's `olds` is merged with the
	 * durable history for that key ({@see record_history()}), so the
	 * "still tracking" check covers every value the network ever
	 * published — not just the one the previous save overwrote.
	 *
	 * @param array<string, array{old:mixed,new:mixed}> $main_dirty Dirty main-option keys.
	 * @param array<string, array{old:mixed,new:mixed}> $csa_dirty  Dirty CSA-option keys.
	 */
	public static function enqueue( array $main_dirty, array $csa_dirty ): void {
		if ( empty( $main_dirty ) && empty( $csa_dirty ) ) {
			return;
		}
		$state   = self::get_state();
		$history = self::get_history();

		if ( 'running' === $state['status'] ) {
			$state['main_dirty'] = self::coalesce_dirty( (array) $state['main_dirty'], $main_dirty, $history['main'] );
			$state['csa_dirty']  = self::coalesce_dirty( (array) $state['csa_dirty'], $csa_dirty, $history['csa'] );
			// Refresh hardcoded snapshots so any concurrent code change is
			// reflected on the re-walk; cheap and correct.
			$state['main_hardcoded'] = self::hardcoded_main_defaults();
			$state['csa_hardcoded']  = self::hardcoded_csa_defaults();
			$state                   = self::reset_walk_progress( $state );
			self::save_state( $state );
			self::schedule_next_tick( 0 );
			return;
		}

		// Fresh run.
		$state                   = self::default_state();
		$state['status']         = 'running';
		$state['main_dirty']     = self::seed_olds_trail( $main_dirty, $history['main'] );
		$state['csa_dirty']      = self::seed_olds_trail( $csa_dirty, $history['csa'] );
		$state['main_hardcoded'] = self::hardcoded_main_defaults();
		$state['csa_hardcoded']  = self::hardcoded_csa_defaults();
		$state                   = self::reset_walk_progress( $state );
		$state['started_at']     = $state['updated_at']; // updated_at was set in reset_walk_progress.
		self::save_state( $state );
		self::schedule_next_tick( 0 );
	}

	/**
	 * Convert the save-handler's `{old, new}` shape into the worker's
	 * `{olds: [old], new}` shape, for a fresh run.
	 *
	 * Drops the `old` entry when null (no previous value known) so the
	 * olds trail is never just `[null]` — the "site value is absent"
	 * branch of apply_dirty_to_option already covers that case.
	 *
	 * @param array<string, array{old:mixed,new:mixed,force?:bool}> $dirty   Save-handler input.
	 * @param array<string, array<int,mixed>>                       $history Durable per-key trail for this blob.
	 * @return array<string, array{olds:array<int,mixed>, new:mixed, force:bool}>
	 */
	private static function seed_olds_trail( array $dirty, array $history ): array {
		$out = array();
		foreach ( $dirty as $key => $entry ) {
			$old         = $entry['old'] ?? null;
			$new         = $entry['new'] ?? null;
			$out[ $key ] = array(
				'olds'  => self::fold_history_into_olds(
					( null === $old ) ? array() : array( $old ),
					$history[ $key ] ?? array(),
					$new
				),
				'new'   => $new,
				'force' => ! empty( $entry['force'] ),
			);
		}
		return $out;
	}

	/**
	 * Union a key's in-run `olds` with its durable history, minus the
	 * value now being published, capped at MAX_OLDS_PER_KEY (oldest
	 * history first so the cap drops the least recent values).
	 *
	 * The `new` value is excluded only for tidiness — a site already on
	 * it short-circuits in {@see apply_dirty_to_option()} regardless.
	 *
	 * @param array<int,mixed> $olds      Trail built from the save-handler `old` values.
	 * @param array<int,mixed> $history   Every value the network has stored for this key.
	 * @param mixed            $new_value Value being published.
	 * @return array<int,mixed>
	 */
	private static function fold_history_into_olds( array $olds, array $history, $new_value ): array {
		$merged = array();
		foreach ( array_merge( $history, $olds ) as $value ) {
			if ( null === $value || $value === $new_value || in_array( $value, $merged, true ) ) {
				continue;
			}
			$merged[] = $value;
		}
		if ( count( $merged ) > self::MAX_OLDS_PER_KEY ) {
			$merged = array_slice( $merged, -self::MAX_OLDS_PER_KEY );
		}
		return array_values( $merged );
	}

	/**
	 * Coalesce save-handler-shaped dirty entries into the running job's
	 * multi-old shape.
	 *
	 * For each key in `$incoming`:
	 *   - If `$existing[$key]` is present: append the incoming `old` to
	 *     the existing `olds` (de-dup, cap at MAX_OLDS_PER_KEY), and
	 *     update `new` to the incoming `new`.
	 *   - Else: add a fresh entry with the incoming `old` as the sole
	 *     olds element.
	 *
	 * Either way the result is merged with the key's durable history via
	 * {@see fold_history_into_olds()}.
	 *
	 * @param array<string, array{olds:array<int,mixed>, new:mixed, force:bool}> $existing Running job's dirty map.
	 * @param array<string, array{old:mixed,new:mixed,force?:bool}>              $incoming New save's dirty map.
	 * @param array<string, array<int,mixed>>                                    $history  Durable per-key trail for this blob.
	 * @return array<string, array{olds:array<int,mixed>, new:mixed, force:bool}>
	 */
	private static function coalesce_dirty( array $existing, array $incoming, array $history ): array {
		foreach ( $incoming as $key => $entry ) {
			$incoming_old   = $entry['old'] ?? null;
			$incoming_new   = $entry['new'] ?? null;
			$incoming_force = ! empty( $entry['force'] );
			$key_history    = $history[ $key ] ?? array();
			if ( ! isset( $existing[ $key ] ) ) {
				$existing[ $key ] = array(
					'olds'  => self::fold_history_into_olds(
						( null === $incoming_old ) ? array() : array( $incoming_old ),
						$key_history,
						$incoming_new
					),
					'new'   => $incoming_new,
					'force' => $incoming_force,
				);
				continue;
			}
			$olds = is_array( $existing[ $key ]['olds'] ?? null ) ? $existing[ $key ]['olds'] : array();
			if ( null !== $incoming_old ) {
				$olds[] = $incoming_old;
			}
			// Lock supersedes once latched — a mid-run incoming lock save
			// upgrades the in-flight run to force-write for that key.
			$existing_force   = ! empty( $existing[ $key ]['force'] );
			$existing[ $key ] = array(
				'olds'  => self::fold_history_into_olds( $olds, $key_history, $incoming_new ),
				'new'   => $incoming_new,
				'force' => $existing_force || $incoming_force,
			);
		}
		return $existing;
	}

	/* ===== Durable history trail ===== */

	/**
	 * Record every network value that changed in this save into the
	 * durable per-key history — old and new, for both blobs, regardless
	 * of propagation mode.
	 *
	 * Called by {@see NetworkSettingsPage::save_from_post()} after the new
	 * blobs are written and before {@see enqueue()}. Mode is deliberately
	 * ignored: a value published under "new sites only" was still seeded
	 * into the sites created while it stood, and a value later switched to
	 * "all" must recognize those sites as tracking the network.
	 *
	 * Both the old and the new value are recorded so the trail is
	 * "every value ever stored" even when recording began mid-life (the
	 * first save after upgrading from a build without a history records
	 * the value sites are currently on).
	 *
	 * Absent keys (`null`) are never recorded — the "site value is absent"
	 * branch of {@see apply_dirty_to_option()} already covers that case.
	 * An empty string IS recorded: an empty network value is what an
	 * unset default looks like in storage, and a site holding `''` reads
	 * back as the hardcoded default, which the overwrite rule treats as
	 * "still tracking".
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_main Previous main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_main New main storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_csa  Previous CSA storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_csa  New CSA storage.
	 */
	public static function record_history( array $old_main, array $new_main, array $old_csa, array $new_csa ): void {
		$history = self::get_history();
		$main    = self::append_history_for_blob( $history['main'], $old_main, $new_main );
		$csa     = self::append_history_for_blob( $history['csa'], $old_csa, $new_csa );
		if ( $main === $history['main'] && $csa === $history['csa'] ) {
			return;
		}
		update_site_option(
			self::HISTORY_OPTION_KEY,
			array(
				'main' => $main,
				'csa'  => $csa,
			)
		);
	}

	/**
	 * Per-blob half of {@see record_history()}: for each key whose value
	 * differs between the two blobs, append the old then the new value to
	 * that key's trail (de-duplicated, capped at MAX_OLDS_PER_KEY, oldest
	 * dropped first).
	 *
	 * @param array<string, array<int,mixed>>                                 $trail    Existing per-key trail for this blob.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_blob Previous blob storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $new_blob New blob storage.
	 * @return array<string, array<int,mixed>>
	 */
	private static function append_history_for_blob( array $trail, array $old_blob, array $new_blob ): array {
		$old_values = is_array( $old_blob['values'] ?? null ) ? $old_blob['values'] : array();
		$new_values = is_array( $new_blob['values'] ?? null ) ? $new_blob['values'] : array();
		$keys       = array_unique( array_merge( array_keys( $old_values ), array_keys( $new_values ) ) );
		foreach ( $keys as $key ) {
			$old = $old_values[ $key ] ?? null;
			$new = $new_values[ $key ] ?? null;
			if ( $old === $new ) {
				continue;
			}
			$values = $trail[ $key ] ?? array();
			foreach ( array( $old, $new ) as $value ) {
				if ( null === $value ) {
					continue;
				}
				// Re-publishing a value already in the trail moves it to
				// the "most recent" end so the cap never evicts it first.
				$position = array_search( $value, $values, true );
				if ( false !== $position ) {
					array_splice( $values, (int) $position, 1 );
				}
				$values[] = $value;
			}
			if ( count( $values ) > self::MAX_OLDS_PER_KEY ) {
				$values = array_slice( $values, -self::MAX_OLDS_PER_KEY );
			}
			$trail[ $key ] = array_values( $values );
		}
		return $trail;
	}

	/**
	 * Read the durable history, validated to the canonical
	 * `{main: key => list, csa: key => list}` shape.
	 *
	 * Corrupt or partial storage (a manual edit, a non-array subkey, a
	 * non-list trail) falls back to empty rather than fataling the save
	 * path — losing the trail degrades to the pre-history behavior, which
	 * is strictly better than a broken network settings page.
	 *
	 * @return array{main: array<string, array<int,mixed>>, csa: array<string, array<int,mixed>>}
	 */
	public static function get_history(): array {
		$raw = get_site_option( self::HISTORY_OPTION_KEY, null );
		$out = array(
			'main' => array(),
			'csa'  => array(),
		);
		if ( ! is_array( $raw ) ) {
			return $out;
		}
		foreach ( array( 'main', 'csa' ) as $blob ) {
			$trail = $raw[ $blob ] ?? null;
			if ( ! is_array( $trail ) ) {
				continue;
			}
			foreach ( $trail as $key => $values ) {
				if ( ! is_string( $key ) || ! is_array( $values ) ) {
					continue;
				}
				$out[ $blob ][ $key ] = array_values( $values );
			}
		}
		return $out;
	}

	/**
	 * Reset the walk progress (cursor / counters / job_started_at /
	 * updated_at) on the state. Called when starting a fresh run AND when
	 * coalescing a mid-run save (because mid-run save means re-walking).
	 *
	 * @param array<string, mixed> $state Existing state.
	 * @return array<string, mixed> Updated state.
	 */
	private static function reset_walk_progress( array $state ): array {
		$now                     = time();
		$state['cursor']         = 0;
		$state['total']          = self::count_pending_sites( 0 );
		$state['processed']      = 0;
		$state['updated']        = 0;
		$state['job_started_at'] = $now;
		$state['updated_at']     = $now;
		if ( empty( $state['started_at'] ) ) {
			$state['started_at'] = $now;
		}
		return $state;
	}

	/**
	 * Read the current state, merged against the default shape.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_state(): array {
		$state = get_site_option( self::OPTION_KEY, null );
		if ( ! is_array( $state ) ) {
			return self::default_state();
		}
		return wp_parse_args( $state, self::default_state() );
	}

	/**
	 * Default / idle state shape.
	 *
	 * @return array<string, mixed>
	 */
	public static function default_state(): array {
		return array(
			'status'         => 'idle',
			'cursor'         => 0,
			'total'          => 0,
			'processed'      => 0,
			'updated'        => 0,
			'main_dirty'     => array(),
			'csa_dirty'      => array(),
			'main_hardcoded' => array(),
			'csa_hardcoded'  => array(),
			'errors'         => array(),
			'started_at'     => 0,
			'job_started_at' => 0,
			'updated_at'     => 0,
		);
	}

	/**
	 * Clear state and unschedule pending ticks. Used by tests and by the
	 * "discard run" admin action (when we add one).
	 */
	public static function reset(): void {
		delete_site_option( self::OPTION_KEY );
		self::unschedule();
	}

	/* ===== Tick body ===== */

	/**
	 * Cron callback: one batched backfill tick.
	 *
	 * Throwable boundary: like the license worker, a single bad tick must
	 * not break WP-Cron for the rest of the site. We catch Throwable,
	 * mark the run failed so the progress UI surfaces the breakage to the
	 * super-admin, unschedule future ticks, and return cleanly.
	 */
	public static function tick(): void {
		if ( ! wp_cache_add( self::LOCK_KEY, 1, self::CACHE_GROUP, 90 ) ) {
			return;
		}
		try {
			self::tick_locked();
		} catch ( \Throwable $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- one-shot diagnostic.
			error_log( '[Editoria11y] NetworkDefaultsWorker::tick() threw: ' . $e->getMessage() );
			try {
				$state           = self::get_state();
				$state['status'] = 'failed';
				self::push_error( $state, 0, $e->getMessage() );
				self::save_state( $state );
				self::unschedule();
			} catch ( \Throwable $inner ) {
				unset( $inner );
			}
		} finally {
			wp_cache_delete( self::LOCK_KEY, self::CACHE_GROUP );
		}
	}

	/**
	 * Single locked tick. Walks one batch of sites, advances the cursor,
	 * and either reschedules or completes.
	 */
	private static function tick_locked(): void {
		$state = self::get_state();
		if ( 'running' !== $state['status'] ) {
			self::unschedule();
			return;
		}

		$blog_ids = self::next_batch( (int) $state['cursor'], self::BATCH_SIZE );

		if ( empty( $blog_ids ) ) {
			$state['status'] = empty( $state['errors'] ) ? 'completed' : 'failed';
			self::save_state( $state );
			self::unschedule();
			return;
		}

		foreach ( $blog_ids as $blog_id ) {
			self::process_blog( (int) $blog_id, $state );
			++$state['processed'];
		}
		$state['cursor'] = (int) end( $blog_ids );
		self::save_state( $state );

		if ( self::count_pending_sites( $state['cursor'] ) > 0 ) {
			self::schedule_next_tick( self::min_gap_seconds() );
			return;
		}
		$state['status'] = empty( $state['errors'] ) ? 'completed' : 'failed';
		self::save_state( $state );
		self::unschedule();
	}

	/**
	 * Apply the running job's dirty-key set to a single blog's options.
	 *
	 * @param int                  $blog_id Target blog.
	 * @param array<string, mixed> $state   State by reference.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Defensive switch+restore around per-option work.
	 */
	private static function process_blog( int $blog_id, array &$state ): void {
		switch_to_blog( $blog_id );
		try {
			$changed_main = self::apply_dirty_to_option(
				self::MAIN_OPTION,
				(array) $state['main_dirty'],
				(array) $state['main_hardcoded']
			);
			$changed_csa  = false;
			if ( self::csa_active() ) {
				$changed_csa = self::apply_dirty_to_option(
					self::CSA_OPTION,
					(array) $state['csa_dirty'],
					(array) $state['csa_hardcoded']
				);
			}
			if ( $changed_main || $changed_csa ) {
				++$state['updated'];
			}
		} catch ( \Throwable $e ) {
			self::push_error( $state, $blog_id, $e->getMessage() );
		} finally {
			restore_current_blog();
		}
	}

	/**
	 * Apply the multi-old comparison rule to one option on the current blog.
	 *
	 * For each dirty key, write the new network value IF the site's
	 * current stored value is one of:
	 *   - absent
	 *   - in the `olds` trail (site was tracking the network at some
	 *     point in the propagation chain)
	 *   - equal to the hardcoded default
	 *
	 * Skip the write when the site's stored value already equals the new
	 * network value — this is the "cancellation short-circuit" that makes
	 * a corrective save cheap: the un-visited prefix of the network never
	 * actually leaves the original value, so the re-walk skips it.
	 *
	 * Public for unit-test access — callers from outside this class are
	 * expected to be either the tick body (which has already
	 * `switch_to_blog`-ed) or the PHPUnit suite (running on the test blog).
	 *
	 * @param string                                                 $option_name Per-site option name.
	 * @param array<string, array{olds:array<int,mixed>, new:mixed}> $dirty       Job's dirty-key set for this option.
	 * @param array<string, mixed>                                   $hardcoded   Hardcoded defaults snapshot.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Three-branch overwrite predicate kept inline for read clarity.
	 */
	public static function apply_dirty_to_option( string $option_name, array $dirty, array $hardcoded ): bool {
		if ( empty( $dirty ) ) {
			return false;
		}
		$stored = get_option( $option_name, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$changed = false;
		foreach ( $dirty as $key => $entry ) {
			$olds          = is_array( $entry['olds'] ?? null ) ? $entry['olds'] : array();
			$new_value     = $entry['new'] ?? null;
			$force         = ! empty( $entry['force'] );
			$site_value    = $stored[ $key ] ?? null;
			$hardcoded_val = $hardcoded[ $key ] ?? null;
			$site_absent   = ! array_key_exists( $key, $stored );

			// `force` = lock-mode save. The lock is the single source of
			// truth, so bypass the three-way "is this site still tracking?"
			// rule and write the network value through. This is what makes
			// "lock on, then off, to force-propagate" leave sites at the
			// network value instead of reverting to a pre-lock customization.
			$should_overwrite = $force
				|| $site_absent
				|| in_array( $site_value, $olds, true )
				|| $site_value === $hardcoded_val;

			if ( ! $should_overwrite ) {
				continue;
			}
			if ( ! $site_absent && $site_value === $new_value ) {
				continue;
			}
			$stored[ $key ] = $new_value;
			$changed        = true;
		}
		if ( $changed ) {
			SettingsStorage::write_canonical( $option_name, $stored );
		}
		return $changed;
	}

	/* ===== Helpers ===== */

	/**
	 * Whether CSA is currently active in this build / runtime. Used to
	 * gate CSA-option work inside the per-blog tick body and the seeder.
	 *
	 * The body is written as a full if-block so Freemius's preprocessor
	 * can strip the premium-gated call from the free build (the strip
	 * script only removes the if-block form, not ternaries or expression-
	 * position calls). Post-strip, this method unconditionally returns
	 * false in the free build.
	 */
	private static function csa_active(): bool {

		return false;
	}

	/**
	 * Hardcoded main-option defaults snapshot.
	 *
	 * @return array<string, mixed>
	 */
	private static function hardcoded_main_defaults(): array {
		$defaults = ed11y_get_default_options();
		return is_array( $defaults ) ? $defaults : array();
	}

	/**
	 * Hardcoded CSA-option defaults snapshot. Empty when CSA isn't active
	 * in the current build / runtime.
	 *
	 * @return array<string, mixed>
	 */
	private static function hardcoded_csa_defaults(): array {
		if ( ! self::csa_active() ) {
			return array();
		}
		$defaults = ed11y_get_csa_default_options();
		return is_array( $defaults ) ? $defaults : array();
	}

	/**
	 * Cursor advance: next batch of live blog_ids strictly > cursor.
	 *
	 * Filters archived / spam / deleted. Direct SQL because WP_Site_Query
	 * has no "id greater than" predicate and we want index-only on the PK.
	 * Returns an empty array on non-multisite, where `$wpdb->blogs` is
	 * blank and the SQL would error.
	 *
	 * @param int $cursor High-water blog_id already processed.
	 * @param int $limit  Batch size.
	 * @return int[]
	 */
	private static function next_batch( int $cursor, int $limit ): array {
		if ( ! is_multisite() ) {
			return array();
		}
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $wpdb->blogs is a literal; cache irrelevant for cursor advance.
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT blog_id FROM {$wpdb->blogs}
				WHERE blog_id > %d AND archived = '0' AND spam = '0' AND deleted = '0'
				ORDER BY blog_id ASC LIMIT %d",
				$cursor,
				$limit
			)
		);
		// phpcs:enable
		return array_map( 'intval', is_array( $rows ) ? $rows : array() );
	}

	/**
	 * Count live blog_ids strictly > cursor. Returns 0 on non-multisite.
	 *
	 * @param int $cursor High-water blog_id already processed.
	 */
	private static function count_pending_sites( int $cursor ): int {
		if ( ! is_multisite() ) {
			return 0;
		}
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->blogs}
				WHERE blog_id > %d AND archived = '0' AND spam = '0' AND deleted = '0'",
				$cursor
			)
		);
		// phpcs:enable
	}

	/**
	 * Append an error to the ring-buffered error list, capped at MAX_ERRORS.
	 *
	 * @param array<string, mixed> $state   State by reference.
	 * @param int                  $blog_id Failing blog (0 for global).
	 * @param string               $message Surface-friendly error message.
	 */
	private static function push_error( array &$state, int $blog_id, string $message ): void {
		$state['errors'][] = array(
			'blog_id' => $blog_id,
			'message' => $message,
		);
		if ( count( $state['errors'] ) > self::MAX_ERRORS ) {
			array_shift( $state['errors'] );
		}
	}

	/**
	 * Resolve the minimum gap between ticks. Constant-overrideable.
	 */
	private static function min_gap_seconds(): int {
		if ( defined( 'ED11Y_DEFAULTS_WORKER_MIN_GAP_SECONDS' ) ) {
			return max( 1, (int) constant( 'ED11Y_DEFAULTS_WORKER_MIN_GAP_SECONDS' ) );
		}
		return self::DEFAULT_MIN_GAP_SECONDS;
	}

	/**
	 * Schedule the next single-event tick. Idempotent.
	 *
	 * @param int $delay_seconds Seconds from now.
	 */
	private static function schedule_next_tick( int $delay_seconds ): void {
		wp_schedule_single_event( time() + max( 0, $delay_seconds ), self::CRON_HOOK );
	}

	/**
	 * Drain any queued tick events in one pass. Idempotent.
	 *
	 * Single wp_unschedule_hook() call, never a retry loop — see the
	 * Installer::unschedule_rehash() docblock for the production hang the
	 * `while ( wp_next_scheduled() )` shape caused.
	 */
	public static function unschedule(): void {
		wp_unschedule_hook( self::CRON_HOOK );
	}

	/**
	 * Persist state with an automatically refreshed updated_at.
	 *
	 * @param array<string, mixed> $state Full state array.
	 */
	private static function save_state( array $state ): void {
		$state['updated_at'] = time();
		update_site_option( self::OPTION_KEY, $state );
	}
}
