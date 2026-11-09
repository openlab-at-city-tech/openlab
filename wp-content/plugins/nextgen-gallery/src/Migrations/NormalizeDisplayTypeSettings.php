<?php
/**
 * Migration: normalize per-entity display type settings.
 *
 * @package Imagely\NGG\Migrations
 */

namespace Imagely\NGG\Migrations;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\Util\Serializable;

/**
 * Removes auto-copied default values from per-gallery / per-album display type settings, leaving
 * only genuine customizations so uncustomized types inherit the current global settings at render.
 *
 * Older versions baked a full snapshot of every display type into each entity; this strips, once
 * per display type, any stored value equal to what an uncustomized entity would inherit at render
 * (the stored global for that key, falling back to the controller default). Values that differ from
 * the inherited value are genuine customizations and are kept.
 *
 * Two properties keep this safe on real installs:
 *  - Incremental by type. The completion marker stores the list of display types already normalized.
 *    When the registered controller set grows (typically NextGEN Pro being activated after the run
 *    completed with only lite controllers), only the NEW types are normalized. Already-normalized
 *    types are never re-stripped, because after the first pass a stored default-equal value is a
 *    deliberate user choice written through the REST path, not a baked copy (issue #767).
 *  - Resumable. Work is budgeted per request and each table keeps a durable cursor (a row id, or a
 *    DONE marker once finished) so a large install spreads across admin_init requests without a
 *    finished table being rescanned while the other catches up.
 *
 * It must run after every display-type controller is registered (Pro registers its controllers on
 * `ngg_initialized`), so it is dispatched on `admin_init` and defers while no controllers exist.
 */
class NormalizeDisplayTypeSettings {

	const OPTION               = 'imagely_display_type_settings_normalized';
	const RUN_OPTION           = 'imagely_dts_normalize_run';
	const FAIL_OPTION          = 'imagely_dts_normalize_failures';
	const CURSOR_PREFIX        = 'imagely_dts_normalize_cursor_';
	const BATCH                = 200;
	const MAX_ROWS_PER_REQUEST = 5000;
	const MAX_FAILURES         = 5;
	const FAIL_TTL             = DAY_IN_SECONDS;

	const ID_FIELDS = [ 'gid', 'id' ];

	const DONE    = 'done';
	const PARTIAL = 'partial';
	const FAILED  = 'failed';

	/**
	 * Runs the migration. Resumable, idempotent, and incremental by display type.
	 *
	 * @param bool $force Re-normalize every registered type even if already recorded as done.
	 * @return bool True when nothing is left to normalize; false when deferred, incomplete, or failed.
	 */
	public static function migrate( $force = false ) {
		// admin_init also fires on admin-ajax.php, before core authenticates the request, so an
		// unauthenticated visitor would otherwise reach the schema work below. Cron has no user.
		if ( \wp_doing_ajax() || \wp_doing_cron() || ! \current_user_can( 'manage_options' ) ) {
			return false;
		}

		$current = self::registered_type_names();

		// No controllers registered yet (dispatched too early, or Pro still loading). Retry later.
		if ( empty( $current ) ) {
			return false;
		}

		$done_types = self::normalized_types();

		// Only normalize display types not already normalized. A shrinking set (e.g. Pro deactivated)
		// yields an empty diff and is a no-op, never a destructive rescan of deliberate values.
		$todo = $force ? $current : array_values( array_diff( $current, $done_types ) );
		if ( empty( $todo ) ) {
			return true;
		}

		$defaults = self::get_controller_defaults( $todo );
		if ( empty( $defaults ) ) {
			return false;
		}

		// The value an uncustomized entity inherits at render is the stored global, falling back to the
		// controller default. Stripping compares against this, not the bare default, so a value equal to
		// the default but differing from the global is preserved instead of silently following the global.
		$globals = self::get_global_settings( $todo );

		if ( $force ) {
			\delete_option( self::FAIL_OPTION );
		}

		// Per-table cursors — including the durable DONE marker — are only valid for the exact type
		// set they were scanned against. If that set changed since a previous partial run (e.g. Pro
		// activated mid-run, growing $todo), discard the stale cursors so an already-"done" table is
		// rescanned for the newly-added types instead of being skipped and left un-normalized.
		$run_signature = md5( implode( ',', $todo ) );
		if ( \get_option( self::RUN_OPTION ) !== $run_signature ) {
			self::clear_cursors();
			// A changed type set is a fresh attempt, so reset the failure budget.
			\delete_option( self::FAIL_OPTION );
			\update_option( self::RUN_OPTION, $run_signature );
		}

		// Bound the retries so a persistent failure cannot query, and log, on every request. The
		// budget carries the time of its last failure and decays, so a burst of transient errors
		// delays the migration rather than stopping it for good.
		$count = self::failure_count();
		if ( $count >= self::MAX_FAILURES ) {
			return false;
		}

		global $wpdb;
		$budget       = self::MAX_ROWS_PER_REQUEST;
		$gallery_stat = self::normalize_table( $wpdb->prefix . 'ngg_gallery', 'gid', $defaults, $globals, $budget );
		$album_stat   = self::normalize_table( $wpdb->prefix . 'ngg_album', 'id', $defaults, $globals, $budget );

		// A DB error leaves the marker unchanged so the run retries; per-table cursors preserve
		// progress, and the failure count bounds the retries.
		if ( self::FAILED === $gallery_stat || self::FAILED === $album_stat ) {
			\update_option(
				self::FAIL_OPTION,
				[
					'count' => $count + 1,
					'time'  => \time(),
				]
			);
			return false;
		}

		\delete_option( self::FAIL_OPTION );

		// Record the newly-normalized types (unioned with prior) only once both tables finished. A
		// partial run (budget exhausted) resumes on the next request.
		if ( self::DONE === $gallery_stat && self::DONE === $album_stat ) {
			self::clear_cursors();
			\delete_option( self::RUN_OPTION );
			$union = array_values( array_unique( array_merge( $done_types, $todo ) ) );
			sort( $union );
			\update_option( self::OPTION, \wp_json_encode( $union ) );
			return true;
		}

		return false;
	}

	/**
	 * Sorted list of display type names that currently have a registered controller.
	 *
	 * @return array
	 */
	private static function registered_type_names() {
		$names = [];

		foreach ( DisplayTypeMapper::get_instance()->find_all() as $display_type ) {
			if ( ! empty( $display_type->name ) && ControllerFactory::has_controller( $display_type->name ) ) {
				$names[] = $display_type->name;
			}
		}

		sort( $names );
		return $names;
	}

	/**
	 * The display type names already normalized by a previous completed run.
	 *
	 * @return array
	 */
	private static function normalized_types() {
		$stored = json_decode( (string) \get_option( self::OPTION, '' ), true );
		return is_array( $stored ) ? $stored : [];
	}

	/**
	 * Builds a map of display type name => controller default settings.
	 *
	 * @param array $only_types Restrict to these display type names.
	 * @return array
	 */
	private static function get_controller_defaults( $only_types ) {
		$defaults = [];

		foreach ( DisplayTypeMapper::get_instance()->find_all() as $display_type ) {
			if ( empty( $display_type->name ) || ! ControllerFactory::has_controller( $display_type->name ) ) {
				continue;
			}

			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ! in_array( $display_type->name, $only_types, true ) ) {
				continue;
			}

			$controller = ControllerFactory::get_controller( $display_type->name );
			if ( \method_exists( $controller, 'get_default_settings' ) ) {
				$defaults[ $display_type->name ] = (array) $controller->get_default_settings();
			}
		}

		return $defaults;
	}

	/**
	 * Builds a map of display type name => stored global settings — the settings an uncustomized entity
	 * inherits at render (the same global row the renderer merges over the controller defaults).
	 *
	 * @param array $only_types Restrict to these display type names.
	 * @return array
	 */
	private static function get_global_settings( $only_types ) {
		$globals = [];

		foreach ( DisplayTypeMapper::get_instance()->find_all() as $display_type ) {
			if ( empty( $display_type->name ) || ! ControllerFactory::has_controller( $display_type->name ) ) {
				continue;
			}

			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ! in_array( $display_type->name, $only_types, true ) ) {
				continue;
			}

			$globals[ $display_type->name ] = (array) $display_type->settings;
		}

		return $globals;
	}

	/**
	 * Strips default-equal values from one table's display_type_settings column.
	 *
	 * Keyset pagination (WHERE id > cursor) with a durable per-table cursor: an integer row id while in
	 * progress, or self::DONE once finished so the table is not rescanned while the other one catches up.
	 *
	 * @param string $table    Fully-qualified table name.
	 * @param string $id_field Primary key column.
	 * @param array  $defaults Map of display type name => default settings.
	 * @param array  $globals  Map of display type name => stored global settings.
	 * @param int    $budget   Remaining rows this request may process, passed by reference.
	 * @return string One of self::DONE, self::PARTIAL, self::FAILED.
	 */
	private static function normalize_table( $table, $id_field, $defaults, $globals, &$budget ) {
		global $wpdb;

		$cursor_option = self::CURSOR_PREFIX . $id_field;
		$cursor        = \get_option( $cursor_option, 0 );

		// Already finished this run; don't rescan while the other table catches up.
		if ( self::DONE === $cursor ) {
			return self::DONE;
		}

		if ( $budget <= 0 ) {
			return self::PARTIAL;
		}

		// The column is not in the table's CREATE TABLE; the data mapper adds it lazily on first
		// construction. Until that happens, querying it raises a database error, so resolve it from
		// the live schema. SHOW TABLES runs first because SHOW COLUMNS errors on a missing table.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$table_exists = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );

		// A failed schema query returns the same empty result as genuine absence, so it has to be
		// ruled out before the result is read that way. Retrying is bounded by the failure count.
		if ( '' !== $wpdb->last_error ) {
			return self::FAILED;
		}

		if ( ! $table_exists ) {
			\update_option( $cursor_option, self::DONE );
			return self::DONE;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$columns = $wpdb->get_col( 'SHOW COLUMNS FROM `' . \esc_sql( $table ) . '`' );

		if ( '' !== $wpdb->last_error ) {
			return self::FAILED;
		}

		if ( ! in_array( 'display_type_settings', $columns, true ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$added = $wpdb->query( 'ALTER TABLE `' . \esc_sql( $table ) . '` ADD COLUMN `display_type_settings` MEDIUMTEXT' );

			// Same reasoning as the probes above: a failed ALTER must not be recorded as success.
			// It also covers losing a race to add the column, which the next run resolves normally.
			if ( false === $added || '' !== $wpdb->last_error ) {
				return self::FAILED;
			}

			// A missing column means no stored settings, and a new one is empty, so nothing to do.
			\update_option( $cursor_option, self::DONE );
			return self::DONE;
		}

		$last_id = (int) $cursor;

		do {
			$limit = (int) min( self::BATCH, $budget );
			if ( $limit <= 0 ) {
				return self::PARTIAL;
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT `' . \esc_sql( $id_field ) . '` AS id, `display_type_settings` AS settings FROM `'
					. \esc_sql( $table ) . '` WHERE `display_type_settings` IS NOT NULL AND `display_type_settings` != %s '
					. 'AND `' . \esc_sql( $id_field ) . '` > %d ORDER BY `' . \esc_sql( $id_field ) . '` ASC LIMIT %d',
					'',
					$last_id,
					$limit
				)
			);

			// get_results() returns an empty array both for a failed query and for a genuine
			// end-of-table, so last_error is the only reliable signal. The next query clears it.
			if ( '' !== $wpdb->last_error ) {
				return self::FAILED;
			}

			$count = count( $rows );

			foreach ( $rows as $row ) {
				$last_id = (int) $row->id;
				--$budget;

				$settings = Serializable::unserialize( $row->settings );
				if ( ! is_array( $settings ) || empty( $settings ) ) {
					continue;
				}

				if ( ! self::strip_defaults( $settings, $defaults, $globals ) ) {
					continue;
				}

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$result = $wpdb->update(
					$table,
					[ 'display_type_settings' => Serializable::serialize( $settings ) ],
					[ $id_field => $row->id ]
				);

				if ( false === $result ) {
					\update_option( $cursor_option, (string) $last_id );
					return self::FAILED;
				}
			}

			\update_option( $cursor_option, (string) $last_id );
		} while ( $count === $limit && $budget > 0 );

		if ( $count < $limit ) {
			\update_option( $cursor_option, self::DONE );
			return self::DONE;
		}

		return self::PARTIAL;
	}

	/**
	 * The current failure count, discarding a budget whose last failure has aged out.
	 *
	 * @return int
	 */
	private static function failure_count() {
		$failures = \get_option( self::FAIL_OPTION, [] );
		$count    = is_array( $failures ) && isset( $failures['count'] ) ? (int) $failures['count'] : 0;
		$since    = is_array( $failures ) && isset( $failures['time'] ) ? (int) $failures['time'] : 0;

		if ( $count > 0 && ( ! $since || ( \time() - $since ) > self::FAIL_TTL ) ) {
			\delete_option( self::FAIL_OPTION );
			return 0;
		}

		return $count;
	}

	/**
	 * Clears every per-table cursor. Called once a run fully completes.
	 *
	 * @return void
	 */
	private static function clear_cursors() {
		foreach ( self::ID_FIELDS as $id_field ) {
			\delete_option( self::CURSOR_PREFIX . $id_field );
		}
	}

	/**
	 * Removes stored values equal to what an uncustomized entity would inherit at render.
	 *
	 * The inherited value is the stored global for that key, falling back to the controller default when
	 * the global row has no such key. Comparing against the inherited value (not the bare default) keeps
	 * the strip lossless at migration time: a removed key resolves to the same value at render right after
	 * the run. A stored value that differs from the inherited value is a real customization and is kept.
	 *
	 * Note the "lossless" guarantee is for the moment of migration only. A stored value equal to the
	 * current global is removed and will therefore track the global on any later global change, rather
	 * than staying pinned to its original value. That is the intended cleanup behavior (uncustomized
	 * entities follow the global); only a value that differs from the global stays pinned.
	 *
	 * @param array $settings Per-entity display type settings, passed by reference.
	 * @param array $defaults Map of display type name => default settings.
	 * @param array $globals  Map of display type name => stored global settings.
	 * @return bool True if anything was removed.
	 */
	private static function strip_defaults( &$settings, $defaults, $globals ) {
		$changed = false;

		foreach ( $settings as $type_name => $type_settings ) {
			if ( ! isset( $defaults[ $type_name ] ) || ! is_array( $type_settings ) ) {
				continue;
			}

			$type_defaults = $defaults[ $type_name ];
			$type_global   = isset( $globals[ $type_name ] ) && is_array( $globals[ $type_name ] ) ? $globals[ $type_name ] : [];

			foreach ( $type_settings as $key => $value ) {
				if ( ! \array_key_exists( $key, $type_defaults ) ) {
					continue;
				}

				// What an uncustomized entity inherits: the stored global for this key, else the default.
				$inherited = \array_key_exists( $key, $type_global ) ? $type_global[ $key ] : $type_defaults[ $key ];

				// The old save path baked booleans as ints, so normalize a boolean inherited value the
				// same way before comparing (e.g. false -> 0 so it matches a baked '0').
				$inherited = is_bool( $inherited ) ? (int) $inherited : $inherited;

				if ( (string) $inherited === (string) $value ) {
					unset( $settings[ $type_name ][ $key ] );
					$changed = true;
				}
			}

			if ( empty( $settings[ $type_name ] ) ) {
				unset( $settings[ $type_name ] );
				$changed = true;
			}
		}

		return $changed;
	}
}
