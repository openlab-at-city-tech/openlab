<?php
/**
 * Guards the Freemius SDK's network-level `fs_accounts` option against
 * WordPress core's duplicate-row defect on multisite.
 *
 * Background (field report: a Pressbooks network with ~1.2 million
 * identical `fs_accounts` rows in wp_sitemeta):
 *
 *   1. `get_network_option()` cannot tell "row absent" from "SELECT failed"
 *      or "stored value does not unserialize" — all three return `false`
 *      and the first two also poison the `notoptions` cache.
 *   2. `update_network_option()` then falls through to
 *      `add_network_option()`, which skips its existence check because of
 *      `notoptions` and runs a bare INSERT. wp_sitemeta has no unique key
 *      on (site_id, meta_key), so nothing stops the duplicate.
 *   3. The SDK treats a `false` read as an empty store, rebuilds it, and
 *      flushes it several times per request without checking the result,
 *      so every failed read becomes at least one new row. Each duplicate
 *      makes the next read heavier (wpdb fetches every matching row even
 *      for get_row()), which makes the next failure more likely.
 *
 * This class closes the loop from the plugin side, in both builds:
 *
 *   - It watches the `site_option_fs_accounts` read. When the value is
 *     `false` because the SELECT errored, or because a row exists but is
 *     unreadable, it blocks every `update_site_option('fs_accounts')` for
 *     the rest of the request via `pre_update_site_option_fs_accounts`
 *     and un-poisons `notoptions` so a persistent object cache cannot
 *     carry the "absent" verdict into later requests.
 *   - It counts `fs_accounts` rows for super-admins in network admin,
 *     offers a one-click repair (keep the newest row, delete the rest),
 *     and repairs automatically on plugin activation so a damaged
 *     network can still activate.
 *
 * Kept off the `@fs_premium_only` strip list: the free build bundles the
 * same SDK and is equally exposed.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Detects, blocks, and repairs duplicate `fs_accounts` network rows.
 */
final class NetworkOptionIntegrity {

	/** The SDK's network option (WP_FS__ACCOUNTS_OPTION_NAME in production). */
	const OPTION = 'fs_accounts';

	/** Admin-post action + nonce for the one-click repair. */
	const ACTION_REPAIR = 'ed11y_repair_fs_accounts';

	/** Site transient caching the last row count (throttles the COUNT). */
	const COUNT_TRANSIENT = 'ed11y_fs_accounts_row_count';

	/** Site transient recording the most recent failed read for the notice. */
	const READ_FAILED_TRANSIENT = 'ed11y_fs_accounts_read_failed';

	/** Site transient flagging the one-shot "repaired N rows" notice. */
	const REPAIRED_TRANSIENT = 'ed11y_fs_accounts_repaired';

	/** How long a row count is trusted before it is re-run. */
	const COUNT_TTL = HOUR_IN_SECONDS;

	/**
	 * Set once a read of the option fails in this request; sticky.
	 *
	 * @var bool
	 */
	private static $read_failed = false;

	/**
	 * Whether the per-request diagnostic has been logged already.
	 *
	 * @var bool
	 */
	private static $logged = false;

	/**
	 * Wire the read watcher, the write block, and the admin surfaces.
	 *
	 * Must run before the SDK's first `get_site_option('fs_accounts')`,
	 * i.e. before `fs_dynamic_init()` is called from editoria11y.php.
	 */
	public static function register(): void {
		if ( ! is_multisite() ) {
			return;
		}
		add_filter( 'site_option_' . self::OPTION, array( __CLASS__, 'flag_failed_read' ), 1 );
		add_filter( 'pre_update_site_option_' . self::OPTION, array( __CLASS__, 'block_write_after_failed_read' ), 1, 2 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_count' ) );
		add_action( 'admin_post_' . self::ACTION_REPAIR, array( __CLASS__, 'handle_repair' ) );
		add_action( 'network_admin_notices', array( __CLASS__, 'render_notice' ) );
	}

	/** Clear the per-request state. Used by tests. */
	public static function reset(): void {
		self::$read_failed = false;
		self::$logged      = false;
	}

	/** Whether a failed read has been recorded in this request. */
	public static function read_failed(): bool {
		return self::$read_failed;
	}

	// ------------------------------------------------------------------
	// Read watcher + write block
	// ------------------------------------------------------------------

	/**
	 * `site_option_fs_accounts` filter: detect a read that returned `false`
	 * for a reason other than "the row does not exist".
	 *
	 * Two signatures, both checked against the query wpdb just ran so an
	 * unrelated earlier error cannot trip the guard:
	 *
	 *   - the SELECT errored (`$wpdb->last_error` is set);
	 *   - the SELECT succeeded and returned a row (`$wpdb->num_rows > 0`)
	 *     but the value still came back `false`, i.e. the stored blob does
	 *     not unserialize.
	 *
	 * A genuinely absent row leaves `last_error` empty and `num_rows` at 0,
	 * so first activation on a fresh network still creates the option.
	 *
	 * @param mixed $value The value WordPress is about to return.
	 * @return mixed The same value, unchanged.
	 */
	public static function flag_failed_read( $value ) {
		if ( false !== $value || self::$read_failed ) {
			return $value;
		}
		global $wpdb;

		if ( ! self::last_query_targets_option( $wpdb ) ) {
			return $value;
		}

		if ( is_string( $wpdb->last_error ) && '' !== $wpdb->last_error ) {
			self::record_failure( 'database error: ' . $wpdb->last_error );
		} elseif ( (int) $wpdb->num_rows > 0 ) {
			self::record_failure( 'row exists but its serialized value cannot be read' );
		}

		return $value;
	}

	/**
	 * `pre_update_site_option_fs_accounts` filter: when the read failed in
	 * this request, return `$old_value` (which is `false`) so that
	 * `update_network_option()` takes its "unchanged" early return and
	 * never reaches `add_network_option()`'s blind INSERT.
	 *
	 * @param mixed $value     New value the SDK wants to store.
	 * @param mixed $old_value Value WordPress read back (false on a failed read).
	 * @return mixed `$value`, or `$old_value` to suppress the write.
	 */
	public static function block_write_after_failed_read( $value, $old_value ) {
		if ( self::$read_failed && false === $old_value ) {
			return $old_value;
		}
		return $value;
	}

	/**
	 * Whether wpdb's most recent query was a read of this option from
	 * wp_sitemeta (either `get_network_option()`'s single-row SELECT or
	 * `wp_prime_network_option_caches()`'s bulk SELECT).
	 *
	 * @param \wpdb $wpdb The database handle.
	 */
	private static function last_query_targets_option( $wpdb ): bool {
		$query = is_string( $wpdb->last_query ) ? $wpdb->last_query : '';
		if ( '' === $query || 0 !== stripos( ltrim( $query ), 'SELECT' ) ) {
			return false;
		}
		return false !== strpos( $query, $wpdb->sitemeta )
			&& false !== strpos( $query, self::OPTION );
	}

	/**
	 * Mark the request as read-failed, un-poison `notoptions`, log once,
	 * and leave a breadcrumb for the network-admin notice.
	 *
	 * @param string $reason Human-readable cause.
	 */
	private static function record_failure( string $reason ): void {
		self::$read_failed = true;
		self::forget_notoption();

		if ( ! self::$logged ) {
			self::$logged = true;
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- once per request, only when a network option read has already failed.
			error_log( '[Editoria11y] Read of network option "' . self::OPTION . '" failed (' . $reason . '); suppressing writes for this request so WordPress does not insert a duplicate wp_sitemeta row.' );
		}

		// Best-effort breadcrumb; a broken database may reject this too.
		set_site_transient(
			self::READ_FAILED_TRANSIENT,
			array(
				'reason' => $reason,
				'time'   => time(),
			),
			DAY_IN_SECONDS
		);
	}

	/**
	 * Remove this option from the network `notoptions` cache.
	 *
	 * `get_network_option()` adds it there on a failed read. With a
	 * persistent object cache that entry would outlive the request and
	 * make every later `add_network_option()` skip its existence check.
	 */
	private static function forget_notoption(): void {
		$notoptions_key = get_current_network_id() . ':notoptions';
		$notoptions     = wp_cache_get( $notoptions_key, 'site-options' );
		if ( is_array( $notoptions ) && isset( $notoptions[ self::OPTION ] ) ) {
			unset( $notoptions[ self::OPTION ] );
			wp_cache_set( $notoptions_key, $notoptions, 'site-options' );
		}
	}

	// ------------------------------------------------------------------
	// Row counting + repair
	// ------------------------------------------------------------------

	/**
	 * Number of `fs_accounts` rows for the current network.
	 *
	 * @return int Row count; -1 when the COUNT itself failed.
	 */
	public static function count_rows(): int {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- diagnostic COUNT on wp_sitemeta; WP has no API for duplicate network-option rows and the result must not be cached.
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->sitemeta} WHERE meta_key = %s AND site_id = %d",
				self::OPTION,
				get_current_network_id()
			)
		);
		if ( null === $count && '' !== $wpdb->last_error ) {
			return -1;
		}
		return (int) $count;
	}

	/**
	 * Keep the newest `fs_accounts` row (highest meta_id) and delete the rest.
	 *
	 * Newest is the safe choice: `update_network_option()` rewrites every
	 * duplicate with the current blob, so surviving rows are identical
	 * whenever any UPDATE has run, and the highest meta_id is the row most
	 * recently written when none has.
	 *
	 * @return int Rows deleted (0 when there was nothing to do or the DELETE failed).
	 */
	public static function repair(): int {
		global $wpdb;
		$network_id = get_current_network_id();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- repairing duplicate wp_sitemeta rows that the options API cannot address individually.
		$keep = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(meta_id) FROM {$wpdb->sitemeta} WHERE meta_key = %s AND site_id = %d",
				self::OPTION,
				$network_id
			)
		);
		if ( $keep <= 0 ) {
			return 0;
		}

		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->sitemeta} WHERE meta_key = %s AND site_id = %d AND meta_id <> %d",
				self::OPTION,
				$network_id,
				$keep
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( false === $deleted ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- one-shot diagnostic on a failed repair.
			error_log( '[Editoria11y] Failed to delete duplicate "' . self::OPTION . '" rows: ' . $wpdb->last_error );
			return 0;
		}

		wp_cache_delete( $network_id . ':' . self::OPTION, 'site-options' );
		self::forget_notoption();
		delete_site_transient( self::COUNT_TRANSIENT );

		return (int) $deleted;
	}

	/**
	 * Activation-time repair so a network already carrying duplicates can
	 * still activate. Runs in the network-admin (main blog) context because
	 * WordPress fires the activation hook only there for network activation.
	 *
	 * @return int Rows deleted.
	 */
	public static function repair_on_activate(): int {
		if ( ! is_multisite() ) {
			return 0;
		}
		try {
			$count = self::count_rows();
			if ( $count <= 1 ) {
				return 0;
			}
			$deleted = self::repair();
			if ( $deleted > 0 ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- activation-time diagnostic; the count is what an operator needs to see.
				error_log( '[Editoria11y] Removed ' . $deleted . ' duplicate "' . self::OPTION . '" rows from wp_sitemeta during activation (' . $count . ' found).' );
				set_site_transient( self::REPAIRED_TRANSIENT, $deleted, 5 * MINUTE_IN_SECONDS );
			}
			return $deleted;
		} catch ( \Throwable $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- activation must never fatal on a diagnostic.
			error_log( '[Editoria11y] repair_on_activate() threw: ' . $e->getMessage() );
			return 0;
		}
	}

	// ------------------------------------------------------------------
	// Network-admin surfaces
	// ------------------------------------------------------------------

	/**
	 * `admin_init`: refresh the cached row count at most once per
	 * COUNT_TTL, only for super-admins in network admin.
	 */
	public static function maybe_count(): void {
		if ( ! is_network_admin() || ! is_super_admin() ) {
			return;
		}
		if ( false !== get_site_transient( self::COUNT_TRANSIENT ) ) {
			return;
		}
		set_site_transient( self::COUNT_TRANSIENT, self::count_rows(), self::COUNT_TTL );
	}

	/**
	 * `admin_post_<ACTION_REPAIR>` handler: super-admin + nonce, repair,
	 * then bounce back to the referring network-admin page.
	 */
	public static function handle_repair(): void {
		if ( ! is_super_admin() ) {
			wp_die( esc_html__( 'Insufficient privileges.', 'editoria11y' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( self::ACTION_REPAIR );

		$deleted = self::repair();
		set_site_transient( self::REPAIRED_TRANSIENT, $deleted, 5 * MINUTE_IN_SECONDS );
		delete_site_transient( self::READ_FAILED_TRANSIENT );

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : network_admin_url() );
		exit;
	}

	/**
	 * `network_admin_notices`: duplicate-row warning with a Repair button,
	 * the most recent failed-read breadcrumb, and the one-shot repaired
	 * confirmation.
	 */
	public static function render_notice(): void {
		try {
			if ( ! is_super_admin() ) {
				return;
			}
			self::render_repaired_notice();
			self::render_duplicate_notice();
			self::render_read_failed_notice();
		} catch ( \Throwable $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- one-shot diagnostic.
			error_log( '[Editoria11y] NetworkOptionIntegrity::render_notice() threw: ' . $e->getMessage() );
		}
	}

	/** One-shot "repaired" confirmation after an activation or manual repair. */
	private static function render_repaired_notice(): void {
		$deleted = get_site_transient( self::REPAIRED_TRANSIENT );
		if ( false === $deleted ) {
			return;
		}
		delete_site_transient( self::REPAIRED_TRANSIENT );
		echo '<div class="notice notice-success is-dismissible"><p>'
			. esc_html(
				sprintf(
					/* translators: %d: number of database rows removed */
					_n(
						'Editoria11y removed %d duplicate Freemius account row from the network options table.',
						'Editoria11y removed %d duplicate Freemius account rows from the network options table.',
						(int) $deleted,
						'editoria11y'
					),
					(int) $deleted
				)
			)
			. '</p></div>';
	}

	/** Warning + Repair button when more than one row exists. */
	private static function render_duplicate_notice(): void {
		$count = get_site_transient( self::COUNT_TRANSIENT );
		if ( false === $count || (int) $count <= 1 ) {
			return;
		}
		$message = sprintf(
			/* translators: %s: number of database rows */
			esc_html__(
				'Editoria11y found %s copies of the Freemius "fs_accounts" row in the network options table (wp_sitemeta). WordPress inserts a new copy whenever it fails to read the existing one, and every later save rewrites all copies, which slows the whole network down. Repair keeps the newest copy and deletes the rest.',
				'editoria11y'
			),
			esc_html( number_format_i18n( (int) $count ) )
		);
		echo '<div class="notice notice-warning"><p>' . wp_kses_post( $message ) . '</p>'
			. '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
			. '<input type="hidden" name="action" value="' . esc_attr( self::ACTION_REPAIR ) . '" />';
		wp_nonce_field( self::ACTION_REPAIR );
		echo '<p><button type="submit" class="button button-secondary">'
			. esc_html__( 'Repair duplicate rows', 'editoria11y' )
			. '</button></p></form></div>';
	}

	/** Breadcrumb from the most recent blocked write, if any. */
	private static function render_read_failed_notice(): void {
		$failed = get_site_transient( self::READ_FAILED_TRANSIENT );
		if ( ! is_array( $failed ) || empty( $failed['reason'] ) ) {
			return;
		}
		$message = sprintf(
			/* translators: 1: date and time, 2: database error text */
			esc_html__(
				'Editoria11y blocked a save of the Freemius "fs_accounts" network option on %1$s because WordPress could not read the existing row (%2$s). Saves are skipped while this persists so the database does not fill with duplicate rows. Check the PHP error log for "WordPress database error" lines and your MySQL limits (max_execution_time, max_allowed_packet).',
				'editoria11y'
			),
			esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $failed['time'] ) ),
			esc_html( (string) $failed['reason'] )
		);
		echo '<div class="notice notice-error"><p>' . wp_kses_post( $message ) . '</p></div>';
	}
}
