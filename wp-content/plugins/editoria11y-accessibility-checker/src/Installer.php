<?php
/**
 * Schema, migration, rehash worker, and lifecycle hooks for the editoria11y plugin tables.
 *
 * The Editoria11y class is the runtime hook orchestrator; this class owns every
 * data-side concern: creating the three plugin tables, walking the
 * editoria11y_db_version state machine forward, running the dismissal
 * element_id rehash worker via WP-Cron and the inline progress UI, and
 * dropping the tables on uninstall.
 *
 * Version state machine (driven by the editoria11y_db_version option):
 *
 * | Version                | Meaning                                                       |
 * |------------------------|---------------------------------------------------------------|
 * | 1.2                    | Pre-v3 schema (no dev_total/dev_count/result_name/stale_date) |
 * | 1.3-failed             | Mid-migrate_to_1_3() ADD COLUMN run. Sticky until retry.      |
 * | 1.3                    | New columns present; pepper generated; element_id varchar.    |
 * | 2.0-migrating          | Cron / inline UI is migrating dismissal rows cursor-by-cursor:|
 * |                        | translating result_key (v2 camelCase → v3 UPPER_SNAKE) and    |
 * |                        | re-hashing element_id against the new key.                    |
 * | 2.0-narrow-pending     | Cursor reached MAX(id); ready for the element_id column       |
 * |                        | narrow. Transient inside the rehash worker (the walk hands    |
 * |                        | straight off to the narrow step) — only observable when the   |
 * |                        | worker died between the two, and then the cron re-drives it.  |
 * |                        | check_tables() never narrows from a page load.                |
 * | 2.0-failed             | MODIFY element_id char(64) failed, OR the straggler stall     |
 * |                        | detector tripped (same stuck rows after two full walks).      |
 * |                        | Schema is functional but column is wide; sticky until retry.  |
 * | 1.4                    | Legacy alias for "all migration steps complete." Pre-v3 sites |
 * |                        | that completed the original WP plugin's 1.2 → 1.4 migration   |
 * |                        | before the v3 result_key translation landed; treated as a     |
 * |                        | transient state — check_tables() pushes it through a one-shot |
 * |                        | key-translation pass and advances to 2.0. Fresh installs and  |
 * |                        | newly-completed migrations skip 1.4 entirely.                 |
 * | 1.4-rehashing,         | Legacy in-flight markers from the previously shipped 1.2→1.4  |
 * | 1.4-rehash-complete,   | rehash line, seen when this build lands on a site mid-drain.  |
 * | 1.4-failed             | check_tables() maps all three onto '2.0-migrating' with the   |
 * |                        | cursor reset — the walk repairs every population they leave.  |
 * | 2.0                    | v3 data shape: hashed element_ids, UPPER_SNAKE result_keys,   |
 * |                        | aligned with the bundled JS library. Transient — check_tables |
 * |                        | immediately runs the 2.1 hardening pass from here.            |
 * | 2.1-failed             | Mid-migrate_to_2_1() hardening run. Sticky until retry, but   |
 * |                        | NOT disabling: the v3 shape is intact and functional.         |
 * | 2.1                    | Hardening pass applied (user column width, unique page_url).  |
 * |                        | Transient — check_tables() runs the 2.2 column add from here. |
 * | 2.2-failed             | Mid-migrate_to_2_2() in_content ADD COLUMN. Sticky until      |
 * |                        | retry, NOT disabling for reads — but dismissal INSERTs        |
 * |                        | reference the column and error until the retry lands.         |
 * | 2.2                    | Terminal: dismissals carry in_content (okAll content/dev      |
 * |                        | bucket). The check_tables() hot-path short-circuit.           |
 *
 * The '-failed' markers are written BEFORE the destructive step and only
 * flipped to the success value AFTER it completes, so any partial DDL leaves
 * the marker as a circuit breaker. retry_migration() rolls back one state
 * and re-runs.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

use Editoria11y\Form\SettingsStorage;
use Editoria11y\UpdateHelpers;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installer / migrator / rehash worker for editoria11y plugin tables.
 */
class Installer {

	/**
	 * Batch size for the dismissal element_id rehash worker. Each batch is
	 * REHASH_BATCH_SIZE indexed single-row autocommit UPDATEs inside
	 * WP-Cron's loopback request — off the visitor path — so the sizing
	 * question is drain time, not page performance: 1000 rows every 5
	 * minutes clears a 100k-row dismissals table in ~8 hours (the old
	 * 250/15-minute pairing took over 4 days). On multisite the AGGREGATE
	 * load is bounded by the network concurrency slots below, not by this
	 * number — shrinking the batch does not reduce how many sites hit the
	 * database at once.
	 */
	const REHASH_BATCH_SIZE = 1000;

	/** Cron action name for the rehash worker. */
	const REHASH_CRON_HOOK = 'editoria11y_rehash_dismissals';

	/**
	 * Per-site advisory lock row for the rehash worker, stored in the
	 * blog's own options table. A DB row (not wp_cache) because on stock
	 * WP without a persistent object-cache drop-in `wp_cache_add()` is
	 * request-local — it "succeeds" in every request and locks nothing.
	 */
	const REHASH_LOCK_OPTION = 'editoria11y_rehash_lock';

	/**
	 * Seconds before a held rehash/narrow lock is treated as abandoned
	 * (worker killed mid-batch) and eligible for takeover. Must stay
	 * comfortably ABOVE the worst-case batch + narrow duration on a loaded
	 * server — a TTL shorter than a batch lets a second worker into the
	 * same cursor window, and the resulting row-lock contention makes
	 * batches slower, which causes more overlap (the failure mode a 40k-
	 * site multisite reported against the old 60-second cache lock).
	 */
	const REHASH_LOCK_TTL = 900;

	/**
	 * Attempts before a row whose UPDATE/DELETE keeps failing (e.g. a
	 * recurring deadlock victim) is parked and walked past. Parked rows
	 * surface later in the narrow pre-flight straggler count.
	 */
	const REHASH_MAX_ROW_RETRIES = 3;

	/**
	 * Option-name prefix for the network-wide concurrency slots, stored in
	 * the MAIN site's options table (the one table every site of the
	 * network can reach with an atomic unique-key insert — wp_sitemeta has
	 * no unique meta_key index, so it cannot provide test-and-set).
	 */
	const REHASH_NETWORK_SLOT_PREFIX = 'editoria11y_rehash_slot_';

	/**
	 * Default number of sites allowed to run a rehash batch (or the narrow
	 * DDL) concurrently across the whole network. Per-site batches are
	 * cheap — single-row primary-key autocommit UPDATEs — but a large
	 * multisite runs one worker per site, and the database server pays the
	 * AGGREGATE (redo log, binlog, buffer pool). Filterable via
	 * `editoria11y_rehash_network_concurrency` for hosts that want a
	 * different ceiling.
	 */
	const REHASH_NETWORK_SLOTS = 2;

	/**
	 * Custom cron schedule slug registered in cron_schedules (a true five
	 * minutes — interval set in editoria11y.php). The license worker's
	 * watchdog has its own fifteen-minute slug so retuning one cadence
	 * can't silently retune the other.
	 */
	const REHASH_CRON_SCHEDULE = 'editoria11y_five_minutes';

	/**
	 * Result keys that are dropped during the v3 migration rather than carried
	 * forward.
	 *
	 * Source: Drupal's `editoria11y_update_9011()` drop list (file at
	 * `/Users/jj/Sites/ed11yddev/web/modules/custom/editoria11y/editoria11y.install`
	 * around line 416). The comment there marks them "untranslatable keys"
	 * pending a discussion with the upstream maintainer; we mirror the same
	 * decision so the WP migration matches Drupal's data shape.
	 *
	 * Existing dismissal/result rows in the WP plugin that translate to one
	 * of these keys are deleted on the way through the migration. Future
	 * scans may produce fresh dismissals with these keys (the JS library
	 * still emits them); those are stored normally.
	 */
	const DROP_KEYS = array(
		'TABLES_SEMANTIC_HEADING',
		'TABLES_EMPTY_HEADING',
	);

	// ------------------------------------------------------------------
	// Lifecycle hooks
	// ------------------------------------------------------------------

	/**
	 * Plugin activation.
	 *
	 * Eagerly runs check_tables() so a fresh single-site install lands at
	 * version 2.0 before any admin page renders. check_tables() is cheap on
	 * the hot path (one autoloaded option read once version is '2.0'), so
	 * repeat activate/deactivate cycles don't pay measurable DDL cost.
	 *
	 * Note: WordPress only fires this hook on the main blog when a plugin
	 * is network-activated; subsites still rely on the lazy check_tables()
	 * calls in the runtime read paths (and in MigrationPanel) to seed their
	 * own schema on first admin/editor request.
	 *
	 * On multisite, first collapses any duplicate Freemius `fs_accounts`
	 * rows in wp_sitemeta (see NetworkOptionIntegrity) so activation on an
	 * already-damaged network does not stall rewriting every copy.
	 */
	public static function activate(): void {
		NetworkOptionIntegrity::repair_on_activate();
		self::check_tables();
	}

	/**
	 * Plugin deactivation. Drops scheduled work; data is preserved.
	 */
	public static function deactivate(): void {
		self::unschedule_rehash();
	}

	/**
	 * Plugin uninstall: drops all three tables and clears every option the
	 * migration touches plus the settings transient, on every site.
	 *
	 * No capability check here: `uninstall_plugin()` only runs from
	 * contexts that already passed the `delete_plugins` gate (the admin
	 * plugins screen) or are inherently privileged (WP-CLI, which runs
	 * with no current user — an earlier `current_user_can()` check here
	 * made `wp plugin uninstall` silently skip all cleanup).
	 *
	 * Multisite behavior: WordPress fires the uninstall hook exactly ONCE,
	 * in the network-admin (base prefix) blog context — NOT once per blog.
	 * We iterate the sites ourselves so subsite tables and options aren't
	 * orphaned. Network-scoped options (wp_sitemeta) are cleared once at
	 * the end; delete_site_option() falls through to delete_option on
	 * single-site, so those calls are unconditional.
	 */
	public static function uninstall(): void {
		if ( is_multisite() ) {
			$site_ids = get_sites(
				array(
					'fields' => 'ids',
					'number' => 0,
				)
			);
			foreach ( $site_ids as $site_id ) {
				switch_to_blog( (int) $site_id );
				self::uninstall_current_blog();
				restore_current_blog();
			}
		} else {
			self::uninstall_current_blog();
		}

		// Network-scoped options written by the multisite super-admin
		// pages and the CSA license worker. Safe to call on single-site
		// too — delete_site_option falls through to delete_option there.
		delete_site_option( 'ed11y_config_version_network' );
		delete_site_option( 'ed11y_network_default_settings' );
		delete_site_option( 'ed11y_network_default_csa_settings' );
		delete_site_option( 'ed11y_network_custom_rules' );
		delete_site_option( 'ed11ycsa_network_license_state' );
		delete_site_option( 'ed11y_network_defaults_backfill_state' );
		delete_site_option( 'ed11y_network_defaults_history' );
		// Pre-3.0 builds cached the payload in a (network-scoped) site
		// transient; clear that legacy key too.
		delete_site_transient( 'editoria11y_settings' );

		// Network rehash-concurrency slot rows live in the main site's
		// options table and are written with raw SQL (never through the
		// option API), so they're removed the same way. The LIKE pattern
		// tolerates a concurrency filter having raised the slot count.
		global $wpdb;
		$base_options = $wpdb->base_prefix . 'options';
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- one-shot uninstall cleanup of raw-written lock rows; table name is $wpdb->base_prefix.literal.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$base_options} WHERE option_name LIKE %s",
				$wpdb->esc_like( self::REHASH_NETWORK_SLOT_PREFIX ) . '%'
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	}

	/**
	 * Per-blog uninstall cleanup: the three tables plus every per-blog
	 * option/transient this plugin writes. Runs in the current blog
	 * context; {@see uninstall()} handles the site iteration.
	 */
	private static function uninstall_current_blog(): void {
		global $wpdb;

		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ed11y_dismissals" ); // phpcs:ignore
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ed11y_results" ); // phpcs:ignore
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ed11y_urls" ); // phpcs:ignore

		delete_option( 'ed11y_plugin_settings' );
		// CSA-side options shipped with the v3 settings split. Always
		// remove on uninstall regardless of whether the plugin is currently
		// running its premium build, so a downgrade-then-uninstall path
		// can't strand orphan rows in the options table.
		delete_option( 'ed11y_csa_plugin_settings' );
		delete_option( 'ed11y_csa_custom_rules' );
		delete_option( 'ed11y_config_version' );
		delete_option( 'editoria11y_db_version' );
		delete_option( 'editoria11y_id_pepper' );
		delete_option( 'editoria11y_rehash_cursor' );
		delete_option( 'editoria11y_rehash_retry' );
		delete_option( 'editoria11y_rehash_stragglers' );
		delete_option( self::REHASH_LOCK_OPTION );
		delete_option( 'ed11y_got_post_ids' );
		delete_option( 'ed11y_disabled_network_rules' );
		delete_option( \Editoria11y\FreemiusTrialNotice::OPTION );
		delete_transient( 'editoria11y_settings' );
	}

	/**
	 * `wpmu_drop_tables` filter callback: include the plugin's three
	 * tables in the list WordPress drops when a subsite is deleted.
	 *
	 * Wired from editoria11y.php at file load (cheap — no DB access
	 * outside the actual delete event). Uses `$wpdb->get_blog_prefix()`
	 * so the table names are scoped to the blog being deleted, not the
	 * current request's blog.
	 *
	 * @param string[] $tables  Existing tables WP plans to drop.
	 * @param int      $blog_id Blog being deleted.
	 * @return string[]
	 */
	public static function wpmu_drop_tables_filter( array $tables, int $blog_id ): array {
		global $wpdb;
		$prefix   = $wpdb->get_blog_prefix( $blog_id );
		$tables[] = $prefix . 'ed11y_dismissals';
		$tables[] = $prefix . 'ed11y_results';
		$tables[] = $prefix . 'ed11y_urls';
		return $tables;
	}

	// ------------------------------------------------------------------
	// Schema
	// ------------------------------------------------------------------

	/**
	 * Provides DB table schema for fresh installs.
	 *
	 * Defines the target shape (v3) so a fresh install lands directly at
	 * editoria11y_db_version=2.0 — no rehash needed for sites that never had
	 * v2 data. Existing v1.2 sites: maybe_create_table is a no-op and the new
	 * columns / element_id type narrowing are handled by migrate_to_1_3() and
	 * narrow_element_id() under check_tables() orchestration.
	 */
	public static function create_database(): bool {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_urls       = $wpdb->prefix . 'ed11y_urls';
		$table_results    = $wpdb->prefix . 'ed11y_results';
		$table_dismissals = $wpdb->prefix . 'ed11y_dismissals';

		// ENGINE=InnoDB is explicit because the FOREIGN KEY clauses below silently
		// no-op on MyISAM (which some legacy hosts still default to). Charset/collation
		// follows the site default. element_id is CHARACTER SET ascii to keep the
		// 64-byte hex hash from reserving 4 bytes/char in utf8mb4 (saves ~192 B/row
		// of buffer-pool pressure on busy multisite installs).
		$sql_urls = "CREATE TABLE $table_urls (
			pid int(9) unsigned AUTO_INCREMENT NOT NULL,
			post_id int(9) unsigned NOT NULL default '0',
			page_url varchar(190) NOT NULL,
			entity_type varchar(255) NOT NULL,
			page_title varchar(1024) NOT NULL,
			page_total smallint(4) unsigned NOT NULL,
			dev_total int unsigned NOT NULL default '0',
			PRIMARY KEY pid (pid),
			UNIQUE KEY page_url (page_url),
			KEY post_id (post_id)
			) ENGINE=InnoDB $charset_collate;";

		$sql_results = "CREATE TABLE $table_results (
			pid int(9) unsigned NOT NULL,
			result_key varchar(32) NOT NULL,
			result_count smallint(4) NOT NULL,
			dev_count int unsigned NOT NULL default '0',
			result_name varchar(255) NOT NULL default '',
			created datetime DEFAULT current_timestamp NOT NULL,
			updated datetime DEFAULT current_timestamp NOT NULL,
			PRIMARY KEY (pid, result_key),
			FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE
			) ENGINE=InnoDB $charset_collate;";

		// The composite (pid, result_key, element_id) covers every WHERE the
		// dismissals controllers run today: per-page reset DELETE, per-page
		// stale-touch UPDATE, and the leftmost-prefix `pid` lookups the read
		// path uses. We deliberately do NOT add a separate single-column pid
		// or element_id index — the composite serves both via leftmost-prefix.
		$sql_dismissals = "CREATE TABLE $table_dismissals (
			id int(9) unsigned AUTO_INCREMENT NOT NULL,
			pid int(9) unsigned NOT NULL,
			result_key varchar(32) NOT NULL,
			user bigint(20) unsigned NOT NULL,
			element_id char(64) CHARACTER SET ascii NOT NULL default '',
			dismissal_status varchar(64) NOT NULL,
			result_name varchar(255) NOT NULL default '',
			created datetime DEFAULT current_timestamp NOT NULL,
			updated datetime DEFAULT current_timestamp NOT NULL,
			stale tinyint(1) NOT NULL default '0',
			stale_date datetime DEFAULT NULL,
			in_content tinyint(1) NOT NULL default 1,
			PRIMARY KEY (id),
			KEY pid_result_key_element_id (pid, result_key, element_id),
			KEY user (user),
			KEY dismissal_status (dismissal_status),
			FOREIGN KEY(pid) REFERENCES $table_urls (pid) ON DELETE CASCADE
			) ENGINE=InnoDB $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// Capture each result: maybe_create_table() returns false on a
		// failed CREATE, and stamping the version as if the schema exists
		// surfaced the failure much later as a baffling ALTER error on a
		// missing table (finding I13). The caller leaves the '1.2-failed'
		// marker in place when this returns false.
		$created = maybe_create_table( $table_urls, $sql_urls );
		$created = maybe_create_table( $table_results, $sql_results ) && $created;
		$created = maybe_create_table( $table_dismissals, $sql_dismissals ) && $created;
		if ( ! $created ) {
			return false;
		}

		// v1.0 → v1.2: backfill post_id on the urls table when missing.
		// Kept verbatim from the old code path — sites still on v1.0 hit this on
		// the way through. Use column-existence rather than column count so future
		// schema additions don't accidentally re-trigger the ALTER.
		$has_post_id = false;
		foreach ( $wpdb->get_results( "DESC $table_urls", ARRAY_A ) as $col ) { // phpcs:ignore
			if ( 'post_id' === $col['Field'] ) {
				$has_post_id = true;
				break;
			}
		}
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Schema-mutation block. Table identifiers come from $wpdb->prefix . literals (never user input), placeholders %1s are intentional for identifiers (not values), the cache is irrelevant for one-shot DDL, and the path is gated by check_tables() so duplicate schema work is prevented above this scope.
		if ( ! $has_post_id ) {
			$wpdb->query(
				"ALTER TABLE $table_urls
				ADD post_id int(9) unsigned NOT NULL default 0,
				DROP PRIMARY KEY, ADD PRIMARY KEY pid ( pid ),
				ADD KEY post_id (post_id)
				;"
			);
		}

		// Replace any pre-existing (auto-named) pid foreign keys with the
		// plugin-named ON DELETE CASCADE constraints maybe_create_table()
		// can't reliably produce.
		self::ensure_pid_foreign_key( $table_results, $table_urls, $wpdb->prefix . 'ed11y_results_pid' );
		self::ensure_pid_foreign_key( $table_dismissals, $table_urls, $wpdb->prefix . 'ed11y_dismissal_pid' );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder, PluginCheck.Security.DirectDB.UnescapedDBParameter
		return true;
	}

	/**
	 * Ensure exactly one plugin-named `pid` foreign key on a child table,
	 * replacing any pre-existing (auto-named) constraint.
	 *
	 * `$wpdb` NEVER throws — the old implementation's try/catch "MariaDB
	 * fallback" arm was dead code, and a silently failed DROP followed by
	 * the unconditional ADD could stack a second, plugin-named FK next to
	 * the survivor. Failure detection and the MySQL→MariaDB syntax
	 * fallback now go through `$wpdb->last_error`; if neither DROP syntax
	 * removes the existing constraint, the ADD is skipped so the schema
	 * keeps exactly one FK either way.
	 *
	 * @param string $child_table     Fully-prefixed child table name.
	 * @param string $parent_table    Fully-prefixed parent (urls) table name.
	 * @param string $constraint_name Canonical plugin constraint name.
	 */
	private static function ensure_pid_foreign_key( string $child_table, string $parent_table, string $constraint_name ): void {
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder, PluginCheck.Security.DirectDB.UnescapedDBParameter -- One-shot DDL; identifiers are $wpdb->prefix . literals; %1s placeholders are intentional for identifiers.
		$row = $wpdb->get_row( "SHOW CREATE TABLE $child_table" );
		if ( ! $row ) {
			return;
		}
		$existing = null;
		if ( preg_match( '/CONSTRAINT `(.+?)` FOREIGN KEY \(`pid`\)/', (string) $row->{'Create Table'}, $matches ) ) {
			$existing = $matches[1];
		}
		if ( $existing === $constraint_name ) {
			// Already canonical — repeat runs are a clean no-op.
			return;
		}

		// Expected-failure branch below (MySQL syntax on MariaDB); keep the
		// noise out of debug output.
		$suppress = $wpdb->suppress_errors();

		if ( null !== $existing ) {
			$wpdb->last_error = '';
			$wpdb->query(
				$wpdb->prepare( "ALTER TABLE $child_table DROP FOREIGN KEY %1s", array( $existing ) )
			);
			if ( '' !== $wpdb->last_error ) {
				$wpdb->last_error = '';
				$wpdb->query(
					$wpdb->prepare( "ALTER TABLE $child_table DROP CONSTRAINT %1s", array( $existing ) )
				);
			}
			if ( '' !== $wpdb->last_error ) {
				// Drop failed under both syntaxes: keep the surviving FK
				// rather than stacking a second one on the same column.
				$wpdb->suppress_errors( $suppress );
				return;
			}
		}

		$wpdb->query(
			$wpdb->prepare(
				"ALTER TABLE $child_table ADD CONSTRAINT %1s FOREIGN KEY(pid) REFERENCES $parent_table (pid) ON DELETE CASCADE",
				array( $constraint_name )
			)
		);
		$wpdb->suppress_errors( $suppress );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder, PluginCheck.Security.DirectDB.UnescapedDBParameter
	}

	/**
	 * Detect whether ALL THREE tables already carry the v3 columns.
	 *
	 * Used to short-circuit migration on a fresh install where create_database()
	 * produced the target shape directly.
	 *
	 * Checks the full v3 column set, not a single sentinel column: the
	 * 1.3 ALTERs run urls-first, so a partial failure (urls succeeded,
	 * results/dismissals didn't) used to leave `dev_total` present — and a
	 * sentinel-only check made retry_migration() take THIS fresh-install
	 * short-circuit and stamp 2.0 over a half-migrated schema.
	 */
	public static function tables_already_v3(): bool {
		global $wpdb;
		$required = array(
			'ed11y_urls'       => array( 'dev_total' ),
			'ed11y_results'    => array( 'dev_count', 'result_name' ),
			'ed11y_dismissals' => array( 'result_name', 'stale_date' ),
		);
		foreach ( $required as $table => $columns ) {
			$existing = array();
			foreach ( $wpdb->get_results( "DESC {$wpdb->prefix}{$table}", ARRAY_A ) as $col ) { // phpcs:ignore
				$existing[] = $col['Field'];
			}
			foreach ( $columns as $column ) {
				if ( ! in_array( $column, $existing, true ) ) {
					return false;
				}
			}
		}
		return true;
	}

	// ------------------------------------------------------------------
	// Migration orchestration
	// ------------------------------------------------------------------

	/**
	 * Make sure tables are in place and up to date.
	 *
	 * Forward-only state machine driven by editoria11y_db_version.
	 * '-failed' suffixes are sticky — set BEFORE the destructive step, flipped
	 * to the success value AFTER it completes — so any partial DDL leaves the
	 * marker as a circuit breaker. retry_migration() clears it.
	 *
	 * Concurrency: the DDL body is gated by an ed11y_migration_lock
	 * `wp_cache_add`. On stock WP (no persistent object-cache drop-in)
	 * that cache is REQUEST-LOCAL, so this only prevents re-entry within
	 * one request — two concurrent admin requests can both enter the
	 * section. That is survivable by design rather than prevented: MySQL
	 * serializes DDL on the same table, every migrate step is idempotent
	 * per column, and the version markers converge because both writers
	 * walk the same forward-only ladder. With a persistent cache the lock
	 * IS cross-request (60s TTL — can expire under a very long ALTER),
	 * and the loser's contention branch returns "not functional yet" for
	 * pre-schema versions, which the Dashboard renders as the tables-
	 * missing panel for that one request.
	 */
	public static function check_tables(): bool {
		// Settings-side one-shot seed for `panel_no_cover`. Lives above the
		// 2.0 hot-path short-circuit so already-migrated sites still pick
		// up the seed on their next admin request. The method is itself a
		// fast no-op once the key is present in stored settings, so the
		// added cost on the hot path is one autoloaded option read.
		self::backfill_panel_no_cover();

		$version = (string) get_option( 'editoria11y_db_version', '' );

		// Hot path: already at the hardened v3 terminal shape — no lock
		// needed, no work needed. Note that '1.4' is NOT terminal here: it
		// indicates the schema is structurally complete but the v3
		// result_key translation may not have run, '2.0' pre-dates the
		// 2.1 hardening pass, and '2.1' pre-dates the 2.2 in_content
		// column — all fall through to the ladder below.
		if ( '2.2' === $version ) {
			return true;
		}

		// Legacy in-flight markers from the previously shipped 1.2 → 1.4
		// rehash line (pre-v3 state names). Without this mapping an
		// upgraded-mid-drain site wedges: '1.4-rehashing' and
		// '1.4-rehash-complete' have no ladder rung (schema_state() reads
		// 'pre-v3' forever), and '1.4-failed' falls into the -failed branch
		// below un-whitelisted, refusing writes on a site the old build
		// treated as functional. All three map onto the v3 cursor walk,
		// which idempotently repairs every population they can leave
		// behind: rows hashed under the old recipe get the key-only
		// translation, raw rows get the full rehash, and the narrow
		// re-runs at the end (giving the '1.4-failed' ALTER one fresh
		// attempt under the new worker before its circuit breaker can
		// re-trip as '2.0-failed').
		if ( in_array( $version, array( '1.4-rehashing', '1.4-rehash-complete', '1.4-failed' ), true ) ) {
			// Restart the walk from id 0: rows below the legacy cursor are
			// hashed but still carry camelCase result_keys, and only the
			// walk translates them.
			delete_option( 'editoria11y_rehash_cursor' );
			update_option( 'editoria11y_db_version', '2.0-migrating' );
			self::schedule_rehash();
			return true;
		}

		// Sticky -failed states. 1.2/1.3-failed mean the column shape is unknown
		// (refuse writes); 2.0-failed means only the type narrow is pending,
		// 2.1-failed means only the hardening pass is pending, and 2.2-failed
		// means only the in_content column add is pending — in those three the
		// schema is functional and we can return true.
		if ( '-failed' === substr( $version, -7 ) ) {
			return in_array( $version, array( '2.0-failed', '2.1-failed', '2.2-failed' ), true );
		}

		// '2.0-migrating' and '2.0-narrow-pending' are both functional; the
		// cron / inline UI handles the rest. The narrow step (full-scan
		// pre-flight + table-copy ALTER) deliberately does NOT run here:
		// check_tables() fires on editor page loads, and a table-copy ALTER
		// launched from a page request parks a metadata lock that queues
		// every other query on the dismissals table behind it. The locked,
		// network-throttled rehash worker runs the narrow instead.
		if ( '2.0-migrating' === $version || '2.0-narrow-pending' === $version ) {
			// Re-arm the background drain if it isn't queued (cheap no-op
			// otherwise). deactivate() unschedules the cron, and nothing on
			// the reactivation path rescheduled it — a deactivate/reactivate
			// cycle mid-migration stalled the rehash forever, with only the
			// settings-page AJAX stepper able to finish it.
			self::schedule_rehash();
			return true;
		}

		// Beyond this point we may run DDL or version-bumps. Gate the section
		// so two concurrent admin requests don't both try to migrate.
		if ( ! wp_cache_add( 'ed11y_migration_lock', 1, 'editoria11y', 60 ) ) {
			// Another request holds the lock; treat the schema as functional
			// for this request — the holder is doing the work. If they fail,
			// their -failed marker becomes visible to the next request.
			return '1.3' === $version || '1.4' === $version || '2.0' === $version || '2.1' === $version;
		}

		try {
			// Re-read inside the lock so we don't act on stale state.
			$version = (string) get_option( 'editoria11y_db_version', '' );

			// Settings-shape coercion. Done at the start of the migration zone
			// (before any DDL) because (a) it's the cheapest, lowest-risk write
			// in the routine — pure wp_options update — and (b) downstream
			// migration code reads these options, so getting them into the
			// canonical shape first removes a class of "stale type" surprises.
			// Idempotent and only writes when something actually changed.
			self::normalize_options();

			// Pre-1.2: lazy-create using the target (v3-equivalent) shape.
			if ( empty( $version ) || version_compare( $version, '1.2', '<' ) ) {
				update_option( 'editoria11y_db_version', '1.2-failed' );
				if ( ! self::create_database() ) {
					// A failed CREATE used to be stamped '1.2' anyway and
					// resurfaced later as a baffling ALTER-on-missing-table
					// '1.3-failed'; leave the '1.2-failed' marker so the
					// failure surfaces at the step that actually broke.
					return false;
				}
				update_option( 'editoria11y_db_version', '1.2' );
				$version = '1.2';
			}

			// 1.2: either fresh install (already v3 shape) or v2 site needing ALTERs.
			if ( '1.2' === $version && self::tables_already_v3() ) {
					// Fresh install short-circuit. ensure_pepper() must still run
					// here — the migrate_to_1_3() path (which also calls it)
					// is being skipped, but the JS shim still needs the pepper
					// to be present in the autoloaded options blob. No early
					// return: fall through to the 2.1 hardening block, which
					// is what actually verifies the hardened bits (a wiped
					// version option over pre-hardening tables lands here too).
					self::ensure_pepper();
					update_option( 'editoria11y_db_version', '2.0' );
					$version = '2.0';
			}

			if ( '1.2' === $version ) {
				update_option( 'editoria11y_db_version', '1.3-failed' );
				if ( ! self::migrate_to_1_3() ) {
					return false;
				}
				update_option( 'editoria11y_db_version', '1.3' );
				$version = '1.3';
			}

			// 1.3 -> 2.0-migrating: enqueue the cron drain. The rehash worker
			// translates result_keys + re-hashes element_ids against the new
			// keys per row.
			if ( '1.3' === $version ) {
				self::schedule_rehash();
				update_option( 'editoria11y_db_version', '2.0-migrating' );
				$version = '2.0-migrating';
			}

			// Legacy 1.4: pre-v3-translation site that completed the original
			// hash migration but never went through the result_key translation
			// step. Run a one-shot pass on both tables and advance to 2.0.
			// Element_ids are already hashed against the OLD keys here so the
			// linkDocument+pdf special case is unrecoverable for these sites
			// — the cursor walk applies the default `linkDocument` →
			// `QA_DOCUMENT` mapping uniformly.
			if ( '1.4' === $version ) {
				self::translate_results_keys();
				self::translate_dismissal_keys_only();
				update_option( 'editoria11y_db_version', '2.0' );
				$version = '2.0';
			}

			// 2.0 -> 2.1: hardening pass (user column width, unique page_url).
			// A failure here is sticky (2.1-failed) but NOT disabling — the
			// v3 shape is intact, so the schema stays functional and we
			// still return true; the migration panel surfaces Retry.
			if ( '2.0' === $version ) {
				update_option( 'editoria11y_db_version', '2.1-failed' );
				if ( self::migrate_to_2_1() ) {
					update_option( 'editoria11y_db_version', '2.1' );
					$version = '2.1';
				}
			}

			// 2.1 -> 2.2: add the dismissals in_content column (okAll
			// content/dev bucket). Shipped 2.1 sites already ran the 1.3
			// ADD COLUMN pass, so this column needs its own step — it can't
			// ride migrate_to_1_3(). A failure is sticky (2.2-failed) but not
			// disabling for reads; the migration panel surfaces Retry.
			if ( '2.1' === $version ) {
				update_option( 'editoria11y_db_version', '2.2-failed' );
				if ( self::migrate_to_2_2() ) {
					update_option( 'editoria11y_db_version', '2.2' );
				}
			}

			return true;
		} finally {
			wp_cache_delete( 'ed11y_migration_lock', 'editoria11y' );
		}
	}

	/**
	 * Returns the current schema-migration state.
	 *
	 * Branches in the read/write paths key off this rather than reading
	 * editoria11y_db_version directly so the version → state mapping stays
	 * in one place.
	 *
	 * Possible values:
	 *  - 'pre-v3'      : v2-shaped schema; check_tables() should migrate.
	 *  - 'dual'        : v3 columns present; legacy raw element_ids may still exist.
	 *  - 'hashed-only' : element_id is char(64); rehash complete.
	 *  - 'broken'      : a DDL step failed; do not write until fixed.
	 */
	public static function schema_state(): string {
		$version = (string) get_option( 'editoria11y_db_version', '' );
		// 1.2-failed and 1.3-failed = column shape is unknown; refuse writes.
		// 2.0-failed = columns exist, only the element_id type-narrow is pending; treat as 'dual'.
		if ( in_array( $version, array( '1.2-failed', '1.3-failed' ), true ) ) {
			return 'broken';
		}
		// '2.0' is the v3 terminal. '1.4' is structurally complete (element_id
		// is char(64)) but pre-v3-key-translation; from a runtime perspective
		// the schema is fully usable, so return 'hashed-only' — the
		// translation pass is the next thing check_tables() will do, but it
		// doesn't block reads/writes.
		// '2.1-failed' means only the hardening pass (column width / unique
		// key) is pending, and '2.2-failed' only the in_content column add —
		// the v3 shape is intact in both, so writers stay live.
		if ( in_array( $version, array( '1.4', '2.0', '2.1', '2.1-failed', '2.2', '2.2-failed' ), true ) ) {
			return 'hashed-only';
		}
		// The legacy 1.4-line in-flight markers are 'dual' too: element_ids
		// are mixed raw/hashed, and check_tables() maps them onto the
		// '2.0-migrating' walk on its next call. Listing them here keeps a
		// schema_state() read that races ahead of that mapping from
		// refusing writes ('1.4-failed') or reporting 'pre-v3'.
		if ( in_array( $version, array( '1.3', '2.0-migrating', '2.0-narrow-pending', '2.0-failed', '1.4-rehashing', '1.4-rehash-complete', '1.4-failed' ), true ) ) {
			return 'dual';
		}
		return 'pre-v3';
	}

	/**
	 * Coerce stored settings-option values to the canonical wire shape.
	 *
	 * Pre-3.0 the defaults array carried real PHP bools for typed fields whose
	 * settings-page form widget actually posts strings (`ed11y_checkvisibility`
	 * select: '' | 'true' | 'false'; the textareas `ed11y_checkRoots` /
	 * `ed11y_no_run` / `ed11y_link_ignore_strings` defaulted to `false`). The
	 * JS shim then coerced `checkVisible` with `=== 'true'`, so a real bool
	 * surviving on the wire became the wrong value. The mismatch was almost
	 * always a runtime artifact of the read-time `ed11y_get_settings()`
	 * empty-overlay rather than something written to `wp_options`, but
	 * round-tripping `ed11y_get_settings()` back to storage (or third-party
	 * code writing the option directly) could leak a real bool into the row.
	 *
	 * This pass rewrites any non-canonical typed value to the form's storage
	 * shape so post-3.0 readers (the static-settings getter, the JS shims, the
	 * sanitize callback) all see a consistent type. Idempotent — writes only
	 * when something actually changes — and called from `check_tables()`
	 * inside the migration lock before any DDL.
	 */
	public static function normalize_options(): void {
		$stored = get_option( 'ed11y_plugin_settings', null );
		if ( ! is_array( $stored ) ) {
			return;
		}

		$changed = false;

		// `ed11y_checkvisibility`: canonical storage is the form `<select>`'s
		// value set: '' | 'true' | 'false'. Any stored bool is almost certainly
		// a leaked theme-detection default rather than an explicit user choice
		// (the form posts strings, not bools), so map both bools to '' — the
		// "Theme default" sentinel — and let the static-settings getter
		// re-resolve via `ed11y_checkvisibility_theme_default()`.
		if ( array_key_exists( 'ed11y_checkvisibility', $stored ) ) {
			$value = $stored['ed11y_checkvisibility'];
			if ( ! is_string( $value ) || ! in_array( $value, array( '', 'true', 'false' ), true ) ) {
				$stored['ed11y_checkvisibility'] = '';
				$changed                         = true;
			}
		}

		// Textarea-backed keys whose pre-3.0 default was bool `false`. JS
		// shims tolerate the bool-to-string mismatch, but storing strings
		// keeps the sanitize callback / static getter / form widget all on
		// the same type contract.
		foreach ( array( 'ed11y_checkRoots', 'ed11y_no_run', 'ed11y_link_ignore_strings' ) as $key ) {
			if ( array_key_exists( $key, $stored ) && ! is_string( $stored[ $key ] ) ) {
				$stored[ $key ] = is_scalar( $stored[ $key ] ) ? (string) $stored[ $key ] : '';
				$changed        = true;
			}
		}

		if ( $changed ) {
			// Canonical write — must bypass the form validator, which is
			// attached when check_tables() runs from an admin page render
			// (Dashboard) and would rewrite tests_off for a blob with no
			// tests_enabled UI sub-array. See SettingsStorage.
			SettingsStorage::write_canonical( 'ed11y_plugin_settings', $stored );
		}
	}

	/**
	 * One-shot install seed for the `panel_no_cover` setting.
	 *
	 * Writes the Gutenberg sidebar selector into stored settings the first
	 * time we see a settings array that's missing the key. Once the key is
	 * present (with ANY value, including the empty string), the method is a
	 * no-op forever — so a user who later clears the field in the settings
	 * UI is not re-seeded on the next admin request.
	 *
	 * Why this exists rather than a read-time default in
	 * `ed11y_get_default_options()`: the empty-overlay in
	 * `ed11y_get_settings()` re-applies any non-empty default whenever the
	 * stored value is empty, so a non-empty default makes "delete" in the
	 * UI impossible — the value would snap back on every read. Moving the
	 * selector to a stored seed lets the user override OR delete it.
	 *
	 * Called from the top of `check_tables()` so existing v3 sites
	 * (already at db version 2.0, which short-circuits below) also get the
	 * backfill on their next admin request.
	 */
	public static function backfill_panel_no_cover(): void {
		$stored = get_option( 'ed11y_plugin_settings', null );
		if ( is_array( $stored ) && array_key_exists( 'panel_no_cover', $stored ) ) {
			return;
		}
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$stored['panel_no_cover'] = '.interface-interface-skeleton__sidebar';
		// Canonical write — see the normalize_options() note re: the form
		// validator being attached on the Dashboard→check_tables() path.
		SettingsStorage::write_canonical( 'ed11y_plugin_settings', $stored );
	}

	/**
	 * Add v3 columns to existing v1.2 tables. Idempotent per column.
	 *
	 * Returns false if any ALTER fails — caller leaves the '1.3-failed' marker
	 * in place, which short-circuits subsequent check_tables() calls until the
	 * next plugin release or an explicit retry_migration().
	 */
	public static function migrate_to_1_3(): bool {
		global $wpdb;

		// Ensure the pepper exists and is autoloaded. Must happen before any
		// code path could try to hash; safe to re-run (idempotent).
		self::ensure_pepper();

		$urls       = $wpdb->prefix . 'ed11y_urls';
		$results    = $wpdb->prefix . 'ed11y_results';
		$dismissals = $wpdb->prefix . 'ed11y_dismissals';

		$alters = array(
			$urls       => array(
				'dev_total' => 'ADD COLUMN dev_total int unsigned NOT NULL default 0',
			),
			$results    => array(
				'dev_count'   => 'ADD COLUMN dev_count int unsigned NOT NULL default 0',
				'result_name' => "ADD COLUMN result_name varchar(255) NOT NULL default ''",
			),
			$dismissals => array(
				'result_name' => "ADD COLUMN result_name varchar(255) NOT NULL default ''",
				'stale_date'  => 'ADD COLUMN stale_date datetime DEFAULT NULL',
			),
		);

		foreach ( $alters as $table => $columns ) {
			$existing = self::table_columns( $table );
			$clauses  = array();
			foreach ( $columns as $name => $clause ) {
				if ( ! in_array( $name, $existing, true ) ) {
					$clauses[] = $clause;
				}
			}
			if ( empty( $clauses ) ) {
				continue;
			}
			$sql = "ALTER TABLE $table " . implode( ', ', $clauses );
			if ( false === $wpdb->query( $sql ) ) { // phpcs:ignore
				return false;
			}
		}

		// Composite index on dismissals: covers the per-page reset DELETE, the
		// per-page stale-touch UPDATE, and the leftmost-prefix pid join used by
		// the read path. Prefix element_id to 64 to stay under InnoDB's
		// 767/3072-byte key limit on either utf8 or utf8mb4 (still selective
		// since the value is a 64-char hex hash).
		$indexes = self::table_indexes( $dismissals );
		if ( ! in_array( 'pid_result_key_element_id', $indexes, true ) ) {
			$wpdb->query( "ALTER TABLE $dismissals ADD INDEX pid_result_key_element_id (pid, result_key, element_id(64))" ); // phpcs:ignore
		}
		// Drop the now-redundant single-column indexes the composite supersedes.
		// The original schema named the pid index "page_url" by mistake; tolerate
		// either name. element_id was added by an earlier development iteration
		// of this same migration and is now redundant.
		foreach ( array( 'page_url', 'pid', 'element_id' ) as $stale_index ) {
			if ( in_array( $stale_index, $indexes, true ) ) {
				$wpdb->query( "ALTER TABLE $dismissals DROP INDEX $stale_index" ); // phpcs:ignore
			}
		}

		// v3 result_key translation on the results table. Single-pass batch
		// SQL (no cursor needed — ed11y_results carries no element_id, so
		// the rehash worker's per-row dance doesn't apply). Failure here
		// keeps the 1.3-failed circuit breaker the caller wrote.
		if ( ! self::translate_results_keys() ) {
			return false;
		}

		return true;
	}

	/**
	 * Read-only fast path for the per-site pepper.
	 *
	 * Falls back to ensure_pepper() when the option is missing (which both
	 * generates the pepper and writes the autoload-flag fixup). Use this from
	 * hot paths — every editor page load reads the pepper to pass to the JS,
	 * so we want to avoid the autoload-flag fixup write on each call.
	 */
	public static function get_pepper(): string {
		$pepper = (string) get_option( 'editoria11y_id_pepper', '' );
		if ( '' === $pepper ) {
			return self::ensure_pepper();
		}
		return $pepper;
	}

	/**
	 * Generate the per-site pepper if missing and ensure the option is
	 * autoloaded so editor page loads don't pay a per-request DB hit.
	 *
	 * Storing pepper as autoload=yes piggybacks on WP's standard alloptions
	 * cache — no separate caching layer to track, and the pepper is a tiny
	 * 32-byte string so the alloptions blob impact is negligible. Existing
	 * sites that picked it up before this version may have it as autoload=no;
	 * we flip them via a direct $wpdb->update (update_option short-circuits
	 * when the value is unchanged on some WP versions) and bust the
	 * alloptions cache.
	 */
	public static function ensure_pepper(): string {
		global $wpdb;
		$pepper = (string) get_option( 'editoria11y_id_pepper', '' );
		if ( '' === $pepper ) {
			// The miss may be a stale alloptions/notoptions cache: two
			// uncached first requests racing, or another process winning
			// the mint between our read and now. Re-read straight through
			// the cache before generating — add_option() uses INSERT ... ON
			// DUPLICATE KEY UPDATE, so blindly minting here would silently
			// OVERWRITE an already-stored pepper and orphan every
			// element_id hash minted from it.
			wp_cache_delete( 'editoria11y_id_pepper', 'options' );
			wp_cache_delete( 'notoptions', 'options' );
			wp_cache_delete( 'alloptions', 'options' );
			$pepper = (string) get_option( 'editoria11y_id_pepper', '' );
		}
		if ( '' === $pepper ) {
			$pepper = bin2hex( random_bytes( 16 ) );
			add_option( 'editoria11y_id_pepper', $pepper, '', 'yes' );
			return $pepper;
		}
		$wpdb->update( // phpcs:ignore
			$wpdb->options,
			array( 'autoload' => 'yes' ),
			array( 'option_name' => 'editoria11y_id_pepper' )
		);
		wp_cache_delete( 'alloptions', 'options' );
		return $pepper;
	}

	/**
	 * Pre-flight gate, then ALTER MODIFY element_id char(64).
	 *
	 * Called only from run_narrow_step() — inside the rehash worker's
	 * lock/throttle, never from a page-load path (the pre-flight is a full
	 * table scan; the ALTER is a table-copy rebuild).
	 *
	 * Returns true on success. On pre-flight failure (stragglers exist)
	 * rolls the version back to '2.0-migrating' with the cursor parked
	 * just below the first straggler and returns false — unless the same
	 * straggler count already survived a full walk, in which case the
	 * caller's sticky '2.0-failed' marker is left in place (stall
	 * circuit-breaker). On actual ALTER failure leaves '2.0-failed' and
	 * returns false.
	 */
	public static function narrow_element_id(): bool {
		global $wpdb;
		$dtable = $wpdb->prefix . 'ed11y_dismissals';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $dtable is $wpdb->prefix.literal; pre-flight COUNT and the ALTER MODIFY are worker-time DDL gated by the rehash lock so caching is irrelevant.
		$bad = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM $dtable
			WHERE CHAR_LENGTH(element_id) <> 64
				OR element_id NOT REGEXP '^[0-9a-f]{64}$'"
		);
		if ( $bad > 0 ) {
			$previous = get_option( 'editoria11y_rehash_stragglers', false );
			if ( false !== $previous && (int) $previous === $bad ) {
				// The exact same straggler count survived two full walks —
				// these are parked rows another walk cannot fix. Leave the
				// caller's sticky '2.0-failed' marker (schema stays
				// functional, column stays wide) and let the migration
				// panel surface the count with a Retry control instead of
				// re-walking the table forever.
				return false;
			}
			update_option( 'editoria11y_rehash_stragglers', $bad, false );
			// Re-open the walk AT the first straggler, not id 0 — every id
			// below it has already been verified hashed, and on a large
			// table the difference is days of pointless cursor batches.
			$restart = (int) $wpdb->get_var(
				"SELECT IFNULL(MIN(id), 1) - 1 FROM $dtable
				WHERE CHAR_LENGTH(element_id) <> 64
					OR element_id NOT REGEXP '^[0-9a-f]{64}$'"
			);
			update_option( 'editoria11y_rehash_cursor', max( 0, $restart ), false );
			update_option( 'editoria11y_db_version', '2.0-migrating' );
			self::schedule_rehash();
			return false;
		}

		// CHARACTER SET ascii matches what fresh installs get from create_database().
		$result = $wpdb->query( "ALTER TABLE $dtable MODIFY element_id char(64) CHARACTER SET ascii NOT NULL DEFAULT ''" );
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
		return false !== $result;
	}

	/**
	 * Hardening pass for the v3.0 → v3.1 schema step. Idempotent.
	 *
	 * 1. Widens dismissals.user from the legacy smallint(6) unsigned (which
	 *    silently clamps — or, in strict SQL mode, rejects — user IDs above
	 *    65,535) to bigint(20) unsigned, matching wp_users.ID.
	 * 2. Replaces the plain page_url KEY with a UNIQUE key: the read-then-
	 *    insert writers could fork one URL into two pid rows under concurrent
	 *    first-scans, splitting its results and dismissals. Rows already
	 *    forked are collapsed first — the newest (highest-pid, latest scan)
	 *    row wins and the losers' children follow via ON DELETE CASCADE.
	 *
	 * $wpdb never throws, so each step checks $wpdb->last_error; any error
	 * leaves the caller's sticky '2.1-failed' marker in place. That state
	 * is NOT disabling — the v3 shape is intact — it just resurfaces the
	 * Retry control.
	 *
	 * @return bool True when both hardened shapes are verified in place.
	 */
	public static function migrate_to_2_1(): bool {
		global $wpdb;
		$utable = $wpdb->prefix . 'ed11y_urls';
		$dtable = $wpdb->prefix . 'ed11y_dismissals';

		// Errors are handled via $wpdb->last_error below; suppress wpdb's
		// direct printing so an expected failure (e.g. a stale '2.0' version
		// option with the tables gone) doesn't corrupt admin output.
		$suppress = $wpdb->suppress_errors();
		try {
			return self::migrate_to_2_1_body( $utable, $dtable );
		} finally {
			$wpdb->suppress_errors( $suppress );
		}
	}

	/**
	 * DDL body for {@see migrate_to_2_1()}; split out so the error-output
	 * suppression wrapper stays try/finally-clean.
	 *
	 * @param string $utable Prefixed urls table name.
	 * @param string $dtable Prefixed dismissals table name.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Linear verify-then-alter ladder; every branch is an early-return error check on the preceding DDL statement.
	 */
	private static function migrate_to_2_1_body( string $utable, string $dtable ): bool {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table names are $wpdb->prefix.literal; migration-time DDL gated by check_tables().
		$user_column = $wpdb->get_row( "SHOW COLUMNS FROM $dtable LIKE 'user'", ARRAY_A );
		if ( ! is_array( $user_column ) ) {
			return false;
		}
		if ( false === stripos( (string) ( $user_column['Type'] ?? '' ), 'bigint' ) ) {
			$wpdb->last_error = '';
			$wpdb->query( "ALTER TABLE $dtable MODIFY user bigint(20) unsigned NOT NULL" );
			if ( '' !== $wpdb->last_error ) {
				return false;
			}
		}

		$already_unique = false;
		$has_plain_key  = false;
		foreach ( (array) $wpdb->get_results( "SHOW INDEX FROM $utable", ARRAY_A ) as $index ) {
			if ( 'page_url' === ( $index['Key_name'] ?? '' ) ) {
				if ( '0' === (string) $index['Non_unique'] ) {
					$already_unique = true;
				} else {
					$has_plain_key = true;
				}
			}
		}
		if ( $already_unique ) {
			return true;
		}

		$wpdb->last_error = '';
		$wpdb->query(
			"DELETE older FROM $utable AS older
			INNER JOIN $utable AS newer
				ON older.page_url = newer.page_url AND older.pid < newer.pid"
		);
		if ( '' !== $wpdb->last_error ) {
			return false;
		}

		if ( $has_plain_key ) {
			$wpdb->query( "ALTER TABLE $utable DROP KEY page_url" );
			if ( '' !== $wpdb->last_error ) {
				return false;
			}
		}
		$wpdb->query( "ALTER TABLE $utable ADD UNIQUE KEY page_url (page_url)" );
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
		return '' === $wpdb->last_error;
	}

	/**
	 * Add the dismissals in_content column: content (1) vs. developer (0)
	 * bucket for `okAll` rows, so the dashboard subtracts a global dismissal
	 * from the right column and a reset restores the right one. Defaults to
	 * 1 (content): harmless for the `ok`/`hide` rows that ignore it.
	 *
	 * Fresh installs get the column from create_database(); this step exists
	 * for sites that shipped at 2.1, whose migrate_to_1_3() ADD COLUMN pass
	 * has already run and will never run again. Idempotent per column, same
	 * as the other ALTER steps.
	 */
	public static function migrate_to_2_2(): bool {
		global $wpdb;
		$dismissals = $wpdb->prefix . 'ed11y_dismissals';

		if ( in_array( 'in_content', self::table_columns( $dismissals ), true ) ) {
			return true;
		}
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is $wpdb->prefix.literal; migration-time DDL gated by check_tables().
		return false !== $wpdb->query( "ALTER TABLE $dismissals ADD COLUMN in_content tinyint(1) NOT NULL default 1" );
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
	}

	/**
	 * Clear a -failed marker by stepping back to the most recent good state, then
	 * re-run check_tables(). Called by the admin "Retry migration" button.
	 */
	public static function retry_migration(): bool {
		$version = (string) get_option( 'editoria11y_db_version', '' );
		if ( '-failed' === substr( $version, -7 ) ) {
			// Reset the rehash worker's failure bookkeeping so a retry
			// gets fresh straggler cycles and fresh per-row attempts
			// (otherwise the stall detector would trip again immediately).
			delete_option( 'editoria11y_rehash_stragglers' );
			delete_option( 'editoria11y_rehash_retry' );
			$rollback = array(
				'1.2-failed' => '',
				'1.3-failed' => '1.2',
				// 2.0-failed: column-narrow already failed once. Roll back
				// to 2.0-narrow-pending so the next check_tables() retries
				// the ALTER MODIFY and, on success, advances to 2.0.
				'2.0-failed' => '2.0-narrow-pending',
				// 2.1-failed: only the hardening pass failed; the v3 shape
				// is intact. Roll back to 2.0 so check_tables() re-runs it.
				'2.1-failed' => '2.0',
				// 2.2-failed: only the in_content column add failed. Roll
				// back to 2.1 so check_tables() re-runs it.
				'2.2-failed' => '2.1',
			);
			update_option( 'editoria11y_db_version', $rollback[ $version ] ?? '' );
		}
		return self::check_tables();
	}

	// ------------------------------------------------------------------
	// Advisory locks (cross-request, DB-backed)
	// ------------------------------------------------------------------

	/**
	 * Atomically acquire a named advisory lock row in an options-shaped
	 * table. Returns the holder token on success, null when the lock is
	 * held by a live worker.
	 *
	 * Raw SQL on purpose, for two reasons the option API cannot satisfy:
	 *
	 *   1. Atomicity. `add_option()` runs INSERT ... ON DUPLICATE KEY
	 *      UPDATE, which silently OVERWRITES a concurrently-held lock (see
	 *      the ensure_pepper() note on the same hazard). The options
	 *      table's UNIQUE option_name key makes INSERT IGNORE a true
	 *      test-and-set: exactly one contender sees 1 affected row.
	 *   2. Cache coherency. alloptions/notoptions caches are per-request
	 *      on stock WP and per-blog under a persistent drop-in; a lock
	 *      read through them can be stale. These rows are never touched
	 *      through get_option()/update_option().
	 *
	 * Stale takeover: the token embeds the acquisition timestamp. A row
	 * older than REHASH_LOCK_TTL belongs to a worker that died mid-batch;
	 * takeover is DELETE-by-exact-value followed by a fresh INSERT IGNORE,
	 * so when several contenders race the takeover, at most one wins.
	 *
	 * @param string $table Fully-prefixed options table to lock in.
	 * @param string $name  Lock row option_name.
	 * @return string|null Holder token, or null when not acquired.
	 */
	private static function acquire_advisory_lock( string $table, string $name ): ?string {
		global $wpdb;
		$token = time() . ':' . bin2hex( random_bytes( 8 ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $table is $wpdb->prefix/base_prefix . 'options'; the lock must bypass the option caches (see docblock).
		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$table} (option_name, option_value, autoload) VALUES (%s, %s, 'no')",
				$name,
				$token
			)
		);
		if ( 1 === $inserted ) {
			return $token;
		}

		// Row exists: held, or abandoned by a killed worker.
		$current = $wpdb->get_var(
			$wpdb->prepare( "SELECT option_value FROM {$table} WHERE option_name = %s", $name )
		);
		if ( null === $current || (int) $current > time() - self::REHASH_LOCK_TTL ) {
			// Live holder (or released between our INSERT and SELECT —
			// the next tick will get it).
			return null;
		}
		$wpdb->query(
			$wpdb->prepare( "DELETE FROM {$table} WHERE option_name = %s AND option_value = %s", $name, $current )
		);
		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$table} (option_name, option_value, autoload) VALUES (%s, %s, 'no')",
				$name,
				$token
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		return 1 === $inserted ? $token : null;
	}

	/**
	 * Release an advisory lock — only when we still hold it (exact token
	 * match), so a worker that overran the TTL cannot free its successor's
	 * lock.
	 *
	 * @param string $table Fully-prefixed options table the lock lives in.
	 * @param string $name  Lock row option_name.
	 * @param string $token Token returned by acquire_advisory_lock().
	 */
	private static function release_advisory_lock( string $table, string $name, string $token ): void {
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- see acquire_advisory_lock().
		$wpdb->query(
			$wpdb->prepare( "DELETE FROM {$table} WHERE option_name = %s AND option_value = %s", $name, $token )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	}

	/**
	 * Acquire this site's rehash worker lock (current blog's options
	 * table, so the scope is per-site on multisite).
	 *
	 * @return string|null Holder token, or null when another worker is live.
	 */
	public static function acquire_rehash_lock(): ?string {
		global $wpdb;
		return self::acquire_advisory_lock( $wpdb->options, self::REHASH_LOCK_OPTION );
	}

	/**
	 * Release this site's rehash worker lock.
	 *
	 * @param string $token Token from acquire_rehash_lock().
	 */
	public static function release_rehash_lock( string $token ): void {
		global $wpdb;
		self::release_advisory_lock( $wpdb->options, self::REHASH_LOCK_OPTION, $token );
	}

	/**
	 * Claim one of the network-wide rehash concurrency slots.
	 *
	 * Slots live in the main site's options table (reachable from every
	 * blog via $wpdb->base_prefix, with the unique option_name key the
	 * atomic acquire needs). The starting slot is randomized so thousands
	 * of sites waking on the same cron boundary don't all hammer slot 0.
	 *
	 * Callable on single-site too (base_prefix === prefix there), but the
	 * worker only consults it under is_multisite().
	 *
	 * @return array|null array{name: string, token: string} or null when
	 *                    every slot is held.
	 */
	public static function acquire_network_slot(): ?array {
		global $wpdb;
		$table = $wpdb->base_prefix . 'options';

		/**
		 * Filters how many sites may run a rehash batch (or the element_id
		 * narrow DDL) concurrently across the network.
		 *
		 * @param int $slots Defaults to Installer::REHASH_NETWORK_SLOTS.
		 */
		$slots  = max( 1, (int) apply_filters( 'editoria11y_rehash_network_concurrency', self::REHASH_NETWORK_SLOTS ) );
		$offset = wp_rand( 0, $slots - 1 );
		for ( $i = 0; $i < $slots; $i++ ) {
			$name  = self::REHASH_NETWORK_SLOT_PREFIX . ( ( $offset + $i ) % $slots );
			$token = self::acquire_advisory_lock( $table, $name );
			if ( null !== $token ) {
				return array(
					'name'  => $name,
					'token' => $token,
				);
			}
		}
		return null;
	}

	/**
	 * Release a network concurrency slot.
	 *
	 * @param array $slot array{name: string, token: string} from acquire_network_slot().
	 */
	public static function release_network_slot( array $slot ): void {
		global $wpdb;
		self::release_advisory_lock( $wpdb->base_prefix . 'options', (string) $slot['name'], (string) $slot['token'] );
	}

	// ------------------------------------------------------------------
	// Rehash worker
	// ------------------------------------------------------------------

	/**
	 * Process up to REHASH_BATCH_SIZE rows from a cursor position.
	 *
	 * Returns ['processed' => int, 'remaining' => int]. Drives the cron worker
	 * AND the inline progress UI on the settings page — both call this same
	 * method so they're interchangeable.
	 *
	 * Cursor design: rather than `WHERE CHAR_LENGTH(element_id) <> 64`
	 * (non-sargable, full table scan every batch), we walk the table by id
	 * range and only rehash rows whose element_id isn't already a 64-char hex
	 * hash. The cursor is stored in the editoria11y_rehash_cursor option so
	 * progress survives restarts, and the SELECT becomes an indexed range
	 * scan bounded by LIMIT — O(batch_size) regardless of table size.
	 *
	 * Concurrency, two layers:
	 *
	 *   1. Per-site: a DB-backed advisory lock (see acquire_rehash_lock)
	 *      keeps cron and admin-AJAX from processing the same cursor
	 *      window. The UPDATEs are idempotent, but overlapping workers
	 *      contend on the same row locks and deadlock each other.
	 *   2. Network-wide (multisite): the worker must also claim one of
	 *      REHASH_NETWORK_SLOTS concurrency slots. Without the throttle, a
	 *      network update puts thousands of sites into the drain at once
	 *      and every 5-minute cron sweep fires thousands of concurrent
	 *      batches at one database server. A site that misses a slot
	 *      simply skips the tick; its recurring event retries in 5 min.
	 *
	 * When the cursor reaches the table's MAX(id), the worker runs the
	 * narrow step (pre-flight + ALTER) itself, in this same locked,
	 * throttled context — never from a page-load check_tables() call.
	 */
	public static function rehash_batch(): array {
		$token = self::acquire_rehash_lock();
		if ( null === $token ) {
			// Another worker is live on this site; report no progress.
			return array(
				'processed' => 0,
				'remaining' => self::rehash_remaining_estimate(),
			);
		}
		$slot = null;
		if ( is_multisite() ) {
			$slot = self::acquire_network_slot();
			if ( null === $slot ) {
				// Network concurrency ceiling reached; skip this tick.
				self::release_rehash_lock( $token );
				return array(
					'processed' => 0,
					'remaining' => self::rehash_remaining_estimate(),
				);
			}
		}

		try {
			return self::rehash_batch_locked();
		} finally {
			if ( null !== $slot ) {
				self::release_network_slot( $slot );
			}
			self::release_rehash_lock( $token );
		}
	}

	/**
	 * Inner rehash worker — assumes the caller holds the rehash lock (and,
	 * on multisite, a network concurrency slot).
	 */
	private static function rehash_batch_locked(): array {
		global $wpdb;
		$dtable  = $wpdb->prefix . 'ed11y_dismissals';
		$version = (string) get_option( 'editoria11y_db_version', '' );

		// Crash recovery / explicit retry: the walk already finished but
		// the narrow step didn't complete. Run it here, in the locked
		// worker context.
		if ( '2.0-narrow-pending' === $version ) {
			return self::run_narrow_step();
		}
		if ( '2.0-migrating' !== $version ) {
			// Stray cron event on a site that is not mid-drain (already
			// terminal, or wearing a sticky -failed marker). Walking the
			// table would be pure wasted I/O; drop the recurring event.
			self::unschedule_rehash();
			return array(
				'processed' => 0,
				'remaining' => 0,
			);
		}

		$cursor    = (int) get_option( 'editoria11y_rehash_cursor', 0 );
		$max_id    = (int) $wpdb->get_var( "SELECT IFNULL(MAX(id), 0) FROM $dtable" ); // phpcs:ignore
		$processed = 0;

		if ( $cursor >= $max_id ) {
			// All ids walked; hand off to the narrow step.
			delete_option( 'editoria11y_rehash_cursor' );
			update_option( 'editoria11y_db_version', '2.0-narrow-pending' );
			return self::run_narrow_step();
		}

		// Indexed range scan — O(batch_size) regardless of table size.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $dtable is $wpdb->prefix.'ed11y_dismissals' (literal, not user input); migration query needs to bypass object cache to drive forward state.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, result_key, element_id FROM $dtable WHERE id > %d ORDER BY id ASC LIMIT %d",
				$cursor,
				(int) self::REHASH_BATCH_SIZE
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter

		// $last_id tracks the last row this batch is DONE with (updated,
		// deleted, skipped-as-clean, or parked after repeated failures).
		// A row whose write fails under the retry cap stops the batch with
		// the cursor still BEFORE it, so the next tick retries the row
		// instead of walking past work that never happened.
		$last_id = $cursor;
		foreach ( $rows as $row ) {
			$row_id      = (int) $row->id;
			$current_key = (string) $row->result_key;
			$current_id  = (string) $row->element_id;
			// Lowercase-only, matching the narrow pre-flight's
			// `REGEXP '^[0-9a-f]{64}$'` exactly. ctype_xdigit() also
			// accepted uppercase hex, and on a case-sensitive (_bin)
			// collation the two predicates disagreeing meant an
			// uppercase-hex row was skipped here yet counted as a
			// straggler there — an endless narrow-pending ↔ migrating
			// ping-pong. All hashes we mint are lowercase bin2hex.
			$is_hashed = (bool) preg_match( '/^[0-9a-f]{64}$/', $current_id );

			// v3 key translation. The linkDocument+pdf special case relies on
			// the RAW element_id, so it only fires when $is_hashed is false.
			$new_key = self::translate_result_key( $current_key, $current_id );

			// Drop list: rows whose translated key falls into the
			// not migrate-able set (per Drupal's update_9011 drop list) are
			// removed entirely. The corresponding ed11y_results rows are
			// dropped in translate_results_keys().
			if ( in_array( $new_key, self::DROP_KEYS, true ) ) {
				$result = $wpdb->delete( $dtable, array( 'id' => $row_id ), array( '%d' ) ); // phpcs:ignore
			} elseif ( $is_hashed ) {
				// Already-hashed row: can't rehash element_id (the raw
				// input is gone), but the result_key still needs the v3
				// translation if it changed. Update the key column only.
				if ( $new_key === $current_key ) {
					$last_id = $row_id;
					continue;
				}
				$result = $wpdb->update( // phpcs:ignore
					$dtable,
					array( 'result_key' => $new_key ),
					array( 'id' => $row_id ),
					array( '%s' ),
					array( '%d' )
				);
			} else {
				// Raw element_id: hash against the NEW key so the digest
				// matches what the v3 JS library will compute at lookup
				// time. Update key and element_id in one row write.
				$result = $wpdb->update( // phpcs:ignore
					$dtable,
					array(
						'result_key' => $new_key,
						'element_id' => ed11y_hash_element_id( $new_key, $current_id ),
					),
					array( 'id' => $row_id ),
					array( '%s', '%s' ),
					array( '%d' )
				);
			}

			if ( false === $result ) {
				// Killed statement (deadlock victim, lock-wait timeout, …).
				// The old code advanced the cursor past silent failures;
				// the narrow pre-flight then found the straggler and
				// restarted the whole walk from id 0 — an unbounded
				// re-walk cycle under sustained contention.
				if ( ! self::record_row_failure( $row_id ) ) {
					// Under the retry cap: end the batch with the cursor
					// still before this row so the next tick retries it.
					break;
				}
				// Poison row: REHASH_MAX_ROW_RETRIES attempts spent. Park
				// it (walk past); the narrow pre-flight will count it and
				// the stall detector surfaces it to the admin instead of
				// re-walking forever.
			} else {
				++$processed;
			}
			$last_id = $row_id;
		}

		if ( $last_id > $cursor ) {
			update_option( 'editoria11y_rehash_cursor', $last_id, false );
		}

		if ( $last_id >= $max_id ) {
			delete_option( 'editoria11y_rehash_cursor' );
			update_option( 'editoria11y_db_version', '2.0-narrow-pending' );
			return self::run_narrow_step( $processed );
		}

		return array(
			'processed' => $processed,
			'remaining' => max( 0, $max_id - $last_id ),
		);
	}

	/**
	 * Bump the consecutive-failure counter for a row whose write failed.
	 *
	 * @param int $row_id Failing dismissals row id.
	 * @return bool True when the row has exhausted REHASH_MAX_ROW_RETRIES
	 *              and should be parked (walked past); false while retries
	 *              remain.
	 */
	private static function record_row_failure( int $row_id ): bool {
		$stored = explode( ':', (string) get_option( 'editoria11y_rehash_retry', '' ) );
		$count  = ( (int) $stored[0] === $row_id ) ? (int) ( $stored[1] ?? 0 ) : 0;
		++$count;
		if ( $count >= self::REHASH_MAX_ROW_RETRIES ) {
			delete_option( 'editoria11y_rehash_retry' );
			return true;
		}
		update_option( 'editoria11y_rehash_retry', $row_id . ':' . $count, false );
		return false;
	}

	/**
	 * Narrow step driver: pre-flight straggler scan + ALTER MODIFY, run
	 * from the locked background worker (cron tick or the settings-page
	 * AJAX stepper) — never from a page-load check_tables() call, because
	 * the pre-flight is a full table scan and the ALTER is a table-copy
	 * rebuild whose metadata lock can queue every other query on the
	 * table behind a stalled request.
	 *
	 * Assumes the caller holds the rehash lock and that the version is
	 * '2.0-narrow-pending'. Outcomes:
	 *
	 *   - success        → '2.0', worker state cleaned up, cron dropped.
	 *   - fresh stragglers → narrow_element_id() rolled back to
	 *     '2.0-migrating' with the cursor parked just below the first
	 *     straggler; the cron stays armed and the walk resumes there.
	 *   - stall / ALTER error → sticky '2.0-failed', cron dropped; the
	 *     migration panel surfaces Retry (retry_migration() re-opens the
	 *     cycle).
	 *
	 * @param int $processed Rows processed by the batch that led here,
	 *                       passed through to the caller's report.
	 */
	private static function run_narrow_step( int $processed = 0 ): array {
		update_option( 'editoria11y_db_version', '2.0-failed' );
		if ( self::narrow_element_id() ) {
			update_option( 'editoria11y_db_version', '2.0' );
			delete_option( 'editoria11y_rehash_cursor' );
			delete_option( 'editoria11y_rehash_stragglers' );
			delete_option( 'editoria11y_rehash_retry' );
			self::unschedule_rehash();
			return array(
				'processed' => $processed,
				'remaining' => 0,
			);
		}
		if ( '2.0-migrating' === (string) get_option( 'editoria11y_db_version', '' ) ) {
			// Pre-flight found a fresh straggler cycle and re-opened the
			// walk; the cron is re-armed and the cursor sits just below
			// the first straggler.
			return array(
				'processed' => $processed,
				'remaining' => self::rehash_remaining_estimate(),
			);
		}
		// Sticky '2.0-failed': a real ALTER error, or the stall detector
		// tripped (same stragglers after two full walks). Retrying every
		// five minutes would defeat the circuit breaker — drop the cron
		// and wait for an explicit retry_migration().
		self::unschedule_rehash();
		return array(
			'processed' => $processed,
			'remaining' => 0,
		);
	}

	/**
	 * Best-effort estimate of the number of rows still ahead of the cursor.
	 * Used when another worker holds the rehash lock so the UI still has a
	 * progress number to render.
	 */
	private static function rehash_remaining_estimate(): int {
		global $wpdb;
		$dtable = $wpdb->prefix . 'ed11y_dismissals';
		$cursor = (int) get_option( 'editoria11y_rehash_cursor', 0 );
		$max_id = (int) $wpdb->get_var( "SELECT IFNULL(MAX(id), 0) FROM $dtable" ); // phpcs:ignore
		return max( 0, $max_id - $cursor );
	}

	// ------------------------------------------------------------------
	// Cron lifecycle
	// ------------------------------------------------------------------

	/**
	 * Register a recurring rehash cron event if one isn't already queued.
	 *
	 * The first fire is jittered: a network-wide plugin update marches
	 * thousands of sites into '2.0-migrating' within the same cron sweep,
	 * and a fixed `time() + 60` start phase-locks every one of them onto
	 * the same five-minute boundaries forever. The random offset spreads
	 * the recurring fires across the interval.
	 */
	public static function schedule_rehash(): void {
		if ( ! wp_next_scheduled( self::REHASH_CRON_HOOK ) ) {
			wp_schedule_event( time() + 60 + wp_rand( 0, 240 ), self::REHASH_CRON_SCHEDULE, self::REHASH_CRON_HOOK );
		}
	}

	/**
	 * Drain every queued rehash event in one pass. Idempotent.
	 *
	 * Deliberately NOT a `while ( wp_next_scheduled() ) { wp_unschedule_event() }`
	 * loop: that retry-forever shape has no progress guarantee, and it hung a
	 * large-multisite production site — wp_unschedule_event() returns false
	 * whenever update_option('cron') fails or a `pre_unschedule_event` cron
	 * backend (Cavalcade et al.) refuses the event, while wp_next_scheduled()
	 * keeps reporting it, so the loop span forever inside the rehash worker's
	 * advisory lock immediately after the version option reached '2.0'.
	 * wp_unschedule_hook() clears every queued event for the hook in a single
	 * _set_cron_array() write and cannot loop.
	 */
	public static function unschedule_rehash(): void {
		wp_unschedule_hook( self::REHASH_CRON_HOOK );
	}

	/** WP-Cron callback: process one batch. */
	public static function run_rehash_cron(): void {
		self::rehash_batch();
		// A completed narrow lands at '2.0'; finish the (cheap) 2.1/2.2
		// hardening ladder right here so cron-only sites — subsites no
		// admin ever visits — still reach the terminal version.
		if ( '2.0' === (string) get_option( 'editoria11y_db_version', '' ) ) {
			self::check_tables();
		}
	}

	// ------------------------------------------------------------------
	// v3 key translation
	// ------------------------------------------------------------------

	/**
	 * Translate a legacy v2 `result_key` to its v3 equivalent.
	 *
	 * Two layers:
	 *
	 *   1. Direct map lookup in `UpdateHelpers::old_keys()` (camelCase →
	 *      UPPER_SNAKE).
	 *   2. The `linkDocument` + 'pdf' special case from Drupal's
	 *      `editoria11y_update_9011()` (around install line 408): a row
	 *      whose v2 key is `linkDocument` AND whose RAW element_id contains
	 *      the substring `pdf` becomes `QA_PDF` rather than the default
	 *      `QA_DOCUMENT`. The check is case-insensitive on the element_id
	 *      to match the JS-side selector matching.
	 *
	 * Note that the special case only fires when `$element_id` is the raw
	 * selector. Once a row's element_id has been hashed, the substring
	 * check is meaningless (a 64-char hex hash never contains 'pdf' as a
	 * meaningful token), so already-hashed rows always fall through to
	 * `QA_DOCUMENT`. That tradeoff is documented in the v3 migration plan
	 * — Drupal's update path catches the special case while element_ids
	 * are still raw, and so does our cursor walk, but a WP site that
	 * completed the existing 1.2 → 1.4 rehash before the v3 work landed
	 * has its `linkDocument` rows stranded as `QA_DOCUMENT`.
	 *
	 * @param string $current_key Current v2 result_key (camelCase).
	 * @param string $element_id  Current element_id (raw selector OR hashed).
	 * @return string The v3 result_key. Returns `$current_key` unchanged
	 *                when no translation applies (e.g. an UPPER_SNAKE key
	 *                already in v3 shape, or a custom key not in the
	 *                translation table).
	 */
	public static function translate_result_key( string $current_key, string $element_id ): string {
		if ( 'linkDocument' === $current_key && false !== stripos( $element_id, 'pdf' ) ) {
			return 'QA_PDF';
		}
		$map = UpdateHelpers::old_keys();
		return $map[ $current_key ] ?? $current_key;
	}

	/**
	 * One-shot batch translation of `ed11y_results.result_key` from v2
	 * camelCase to v3 UPPER_SNAKE.
	 *
	 * Unlike the dismissals table, ed11y_results doesn't carry an
	 * element_id — only `result_key` plus per-page counts — so the
	 * translation is a single SQL pass with no cursor walk. The
	 * `linkDocument` → `QA_DOCUMENT` mapping is applied uniformly here
	 * (the pdf special case requires an element_id, which doesn't exist
	 * in this table).
	 *
	 * Called once from `migrate_to_1_3()` as part of the additive
	 * migration. Idempotent: rows already at UPPER_SNAKE keys are
	 * untouched (the lookup returns the key unchanged for non-legacy
	 * values).
	 *
	 * @return bool True on success; false if any UPDATE failed (which
	 *              would be visible in the broader 1.3-failed circuit
	 *              breaker the caller writes).
	 */
	public static function translate_results_keys(): bool {
		global $wpdb;
		$table = $wpdb->prefix . 'ed11y_results';
		$map   = UpdateHelpers::old_keys();

		foreach ( $map as $old_key => $new_key ) {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $table is $wpdb->prefix.literal; placeholders carry the user-facing values.
			$result = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$table} SET result_key = %s WHERE result_key = %s",
					$new_key,
					$old_key
				)
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
			if ( false === $result ) {
				return false;
			}
		}

		// Drop the rows whose translated key falls into the not migrate-able
		// set; matching dismissals are deleted in the cursor walk.
		foreach ( self::DROP_KEYS as $drop_key ) {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->query(
				$wpdb->prepare( "DELETE FROM {$table} WHERE result_key = %s", $drop_key )
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
		}
		return true;
	}

	/**
	 * Key-only batch translation for legacy `'1.4'` dismissal rows.
	 *
	 * Used by the `1.4 → 2.0` transition path in `check_tables()`. The
	 * element_ids on these rows are already hashed against the old
	 * camelCase keys, so re-hashing isn't possible — the dismissals are
	 * functionally lost relative to incoming v3 JS dispatches, but
	 * preserving the row with the new UPPER_SNAKE result_key keeps the
	 * dashboard's per-test grouping coherent and gives admins a path to
	 * clean them up.
	 *
	 * The `linkDocument` + 'pdf' special case is NOT applied here (would
	 * require a raw element_id, which we no longer have); all
	 * `linkDocument` rows fall through to `QA_DOCUMENT`.
	 */
	public static function translate_dismissal_keys_only(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'ed11y_dismissals';
		$map   = UpdateHelpers::old_keys();

		foreach ( $map as $old_key => $new_key ) {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$table} SET result_key = %s WHERE result_key = %s",
					$new_key,
					$old_key
				)
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
		}

		foreach ( self::DROP_KEYS as $drop_key ) {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->query(
				$wpdb->prepare( "DELETE FROM {$table} WHERE result_key = %s", $drop_key )
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
		}
	}

	// ------------------------------------------------------------------
	// Internal helpers
	// ------------------------------------------------------------------

	/**
	 * List column names for a table. Helper for migrate_to_1_3() idempotency.
	 *
	 * @param string $table Fully prefixed table name.
	 */
	private static function table_columns( string $table ): array {
		global $wpdb;
		$names = array();
		foreach ( $wpdb->get_results( "DESC $table", ARRAY_A ) as $col ) { // phpcs:ignore
			$names[] = $col['Field'];
		}
		return $names;
	}

	/**
	 * List index names for a table. Helper for migrate_to_1_3() idempotency.
	 *
	 * @param string $table Fully prefixed table name.
	 */
	private static function table_indexes( string $table ): array {
		global $wpdb;
		$names = array();
		foreach ( $wpdb->get_results( "SHOW INDEX FROM $table", ARRAY_A ) as $idx ) { // phpcs:ignore
			$names[ $idx['Key_name'] ] = true;
		}
		return array_keys( $names );
	}
}
