<?php
/**
 * Schema lookups shared by the installer, the runtime table check and the migrations.
 *
 * @package Imagely\NGG\Util
 */

namespace Imagely\NGG\Util;

/**
 * Answers "does this table exist" without scanning the whole schema.
 */
class Schema {

	/**
	 * Reports whether a table exists.
	 *
	 * Three-state on purpose. A failed query is not an absent table: a lost connection whose
	 * reconnect fails, $wpdb->ready === false, a statement killed by a timeout policy or a proxy
	 * rejecting the statement all come back as "no rows", and every caller acts on the answer
	 * (whether the duplicate-image guard is created, whether an administrator is told a table is
	 * missing, whether a migration is marked done). null means the database could not answer.
	 *
	 * The table is asked about directly first, with DESCRIBE, the statement dbDelta() itself uses
	 * to tell whether a table exists. SHOW TABLES LIKE cannot be used for that step. MySQL
	 * evaluates its pattern against every table in the schema whether or not it contains a
	 * wildcard, so on a large multisite network each call reads the whole data dictionary (8.x)
	 * or lists the whole database directory (5.7, MariaDB). A 10,000-site network with ~510,000
	 * tables measured about 5 seconds per probe, and the installer, the runtime check and the
	 * migrations issue several per site on every upgrade (#1059). DESCRIBE reads one table's
	 * definition, so its cost does not grow with the number of tables. It is also routed like
	 * any other query on that table by drop-ins that split a network across databases
	 * (HyperDB, LudicrousDB), which an information_schema lookup against DATABASE() is not.
	 *
	 * A DESCRIBE that answers proves the table exists. One that fails is either an absent table
	 * or a database that did not answer, and only the server's error number tells them apart:
	 * 1146 (ER_NO_SUCH_TABLE, the same number on MySQL and MariaDB) is a definite "absent".
	 * wpdb keeps that number on its mysqli handle; a drop-in whose handle is not mysqli (SQLite,
	 * some proxies) does not expose it, and any other error is not proof of absence. Those cases
	 * fall back to SHOW TABLES LIKE, which returns no rows rather than an error for an absent
	 * table. On a stock MySQL/MariaDB connection neither an existing nor an absent table reaches
	 * the fallback, so a brand-new subsite on a large network does not pay for the scan either.
	 *
	 * @param string $table Fully prefixed table name. Must be a trusted identifier: it is
	 *                      interpolated into the DESCRIBE, since prepare() has no identifier
	 *                      placeholder before WordPress 6.2.
	 * @return bool|null True/false when the database answered, null when it could not.
	 */
	public static function table_exists( $table ) {
		global $wpdb;

		// wpdb::query() returns false *before* it calls flush() when $wpdb->ready is false, so
		// that query never records an error and last_error still holds whatever the previous one
		// left -- an empty string after any success. The probe would then answer "absent" about a
		// query that was never executed.
		if ( ! $wpdb->ready ) {
			return null;
		}

		// Suppressed because a missing table is an expected answer here, not an error to write to
		// the PHP error log -- the same reason dbDelta() suppresses its own DESCRIBE.
		$suppress = $wpdb->suppress_errors();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- see the @param note on $table.
		$described = $wpdb->query( "DESCRIBE `{$table}`" );
		$wpdb->suppress_errors( $suppress );

		if ( false !== $described && empty( $wpdb->last_error ) && $described > 0 ) {
			return true;
		}

		if ( ! $wpdb->ready ) {
			return null;
		}

		if ( self::last_error_is_no_such_table() ) {
			// The 1146 is the answer, not a failure. Leave last_error as the SHOW TABLES LIKE this
			// replaces left it for an absent table (empty), so callers that test it after a
			// "false" behave exactly as before.
			$wpdb->last_error = '';
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', [ $wpdb->esc_like( $table ) ] ) );

		if ( ! empty( $wpdb->last_error ) || ! $wpdb->ready ) {
			return null;
		}

		return (bool) $found;
	}

	/**
	 * Whether the last statement failed because its table does not exist.
	 *
	 * Read straight after the DESCRIBE, before any other statement can replace the error on the
	 * handle. wpdb exposes its protected $dbh through __get(); HyperDB and LudicrousDB set it to
	 * the connection the statement ran on. Anything that is not a mysqli handle answers false,
	 * which sends the caller to the SHOW TABLES LIKE fallback rather than to a guess.
	 *
	 * @return bool
	 */
	private static function last_error_is_no_such_table() {
		global $wpdb;

		// isset() goes through wpdb::__isset(), so a drop-in without the property is not a warning.
		$dbh = isset( $wpdb->dbh ) ? $wpdb->dbh : null;

		if ( ! $dbh instanceof \mysqli ) {
			return false;
		}

		// A handle closed under us throws on PHP 8 instead of returning an error number.
		try {
			// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysqli_errno -- wpdb has no API for the error number.
			return 1146 === mysqli_errno( $dbh );
		} catch ( \Throwable $e ) {
			return false;
		}
	}
}
