<?php

/**
 * Removes duplicate (galleryid, filename) rows from the pictures table, keeping the lowest
 * pid of each group. Required before adding a UNIQUE KEY on those columns, since ALTER TABLE
 * ADD UNIQUE INDEX fails on a table that already contains duplicates.
 *
 * Runs once per site on success, and is attempted at most twice in total. The key
 * this prepares for is now only created on a table the request just created (see
 * nggallery_record_missing_pictures_guard() for why an existing table is not migrated), so the
 * key-existence exit below can no longer be relied on to stop the work repeating -- on an
 * existing table it would never be satisfied, and the ADD INDEX, self-join DELETE and DROP INDEX
 * would re-run on every version bump and module-list change forever. What bounds it now is
 * ngg_pictures_dedupe_done on success plus a recorded attempt count on failure: a pass that
 * failed or whose request was killed mid-DELETE leaves the duplicate rows #781 created *and*
 * ngg_dedupe_tmp_idx behind, and nothing short of uninstall clears the done marker, so marking
 * done before the work would strand both permanently. One retry recovers an interrupted pass
 * (the leftover-index branch below is what makes a second pass viable) while still being
 * bounded, so a table that reliably fails cannot re-run the scan forever.
 *
 * The attempt is counted before the first statement that can fail, not somewhere in the middle.
 * Counting it after the ADD INDEX left the one branch that fails durably -- a restricted ALTER
 * grant, a read-only replica, a drop-in that does not implement the statement (#969) -- outside
 * the bound entirely, so on exactly those tables the failing ALTER and its two information_schema
 * lookups re-ran on every pass forever: the unbounded shape #935 closed, narrowed rather than
 * removed. Everything past that point is idempotent, so paying one attempt for a transient
 * failure is the cheaper mistake.
 *
 * It still runs, rather than being dropped with the migration, because removing the duplicate
 * rows #781's Scan Folder race created is worth doing on its own -- those rows are what a site
 * owner actually sees -- and 4.4.0/4.4.1 were already doing it. What is deferred to #941 is the
 * UNIQUE KEY, not the cleanup.
 *
 * @param string $nggpictures Fully prefixed table name.
 */
function nggallery_dedupe_pictures_table( $nggpictures ) {
	global $wpdb;

	// Attempts, not a single shot: the success marker is written at the very end, so a failed or
	// interrupted pass is retried. This bound is what keeps that from becoming the unbounded
	// re-scan #935 closed.
	$attempts     = (int) get_option( 'ngg_pictures_dedupe_attempts', 0 );
	$max_attempts = 2;

	if ( $attempts >= $max_attempts ) {
		// Giving up is itself an outcome that has to be cleaned up after and reported. Returning
		// bare here left a temporary index from a killed pass on the table permanently -- this
		// gate sits above the leftover-index branch below, so DROP INDEX became unreachable --
		// with the duplicate rows still in place and, for a request that was killed rather than
		// failing, nothing in ngg_upgrade_error to say so. That is the stranded state 3aa9e008
		// removed, reached by a different route.
		nggallery_abandon_pictures_dedupe( $nggpictures, $attempts );
		return;
	}

	// Counted here, before the first statement that can fail, so every failure branch below is
	// inside the bound -- see the docblock. Before this, the ADD INDEX failure path returned
	// without counting, which made the bound fictional on the tables most likely to hit it.
	update_option( 'ngg_pictures_dedupe_attempts', $attempts + 1, false );

	// A prior run already added the UNIQUE KEY nggallery_add_pictures_unique_key() creates
	// further down in nggallery_install() -- once that's true the table can no longer contain
	// duplicates, so skip re-scanning it on every request instead of unconditionally re-running
	// this every time nggallery_install() executes.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-off information_schema lookup during install; it must read the live schema, so no cache.
	$unique_key_exists = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = %s AND index_name = %s',
			$nggpictures,
			'unique_gallery_filename'
		)
	);

	if ( $unique_key_exists ) {
		// Nothing to do, and nothing left to decide -- a table carrying the key cannot hold
		// duplicates. Mark it so the information_schema lookup above is not repeated either.
		update_option( 'ngg_pictures_dedupe_done', 1, false );
		delete_option( 'ngg_pictures_dedupe_attempts' );
		return;
	}

	// $wpdb->prepare() has no placeholder for table/column identifiers, and $nggpictures is a
	// fully-prefixed table name built by the caller, never user input.
	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	// The self-join below matches rows on (galleryid, filename), but nothing indexes those
	// columns yet at this point -- without one, MySQL/MariaDB has to fall back to an unindexed
	// nested-loop scan whose cost grows with the square of the row count, which is what turns
	// this into a multi-minute, lock-heavy operation on tables with tens of thousands of rows.
	// A plain (non-unique) index lets the join use an index lookup instead; it's dropped again
	// once the dedupe is done, since the real UNIQUE KEY covering the same columns is added by
	// nggallery_add_pictures_unique_key() right after the schema pass.
	//
	// filename is VARCHAR(255); at utf8mb4 (4 bytes/char) that alone is 1020 bytes, already over
	// MyISAM's 1000-byte max key length before galleryid (8 bytes) is even added. A plain index
	// has no workaround for that on MyISAM (unlike a UNIQUE key, where MariaDB transparently
	// falls back to a hash index for over-length keys -- which is why the full-column
	// unique_gallery_filename key released in 4.4.0 survived on MariaDB MyISAM alone).
	//
	// Named here, rather than inlined as a bare literal, because this exact line has already
	// been rewritten three times in three weeks (#781, #934, and this fix) -- the next edit
	// should only need to preserve the byte-budget margin below, not re-derive it from the
	// column's charset and MyISAM's key-length limit from scratch.
	$dedupe_tmp_idx_filename_prefix = 100; // 100 * 4 bytes (utf8mb4) + 8 bytes (galleryid) = 408 bytes, safely under MyISAM's 1000-byte limit; real filenames are far shorter than 100 characters, so the prefix still discriminates rows the same as a full-column index would.
	$index_added                    = $wpdb->query( "ALTER TABLE `{$nggpictures}` ADD INDEX `ngg_dedupe_tmp_idx` (galleryid, filename({$dedupe_tmp_idx_filename_prefix}))" );

	if ( false === $index_added ) {
		// Read the error string now, before the information_schema lookup below runs:
		// wpdb::query() calls flush(), which clears last_error, so reading it after that lookup
		// yields an empty string and the notice renders with nothing after its colon -- exactly
		// what issue #960 reports ("could not add a temporary index before deduplicating the
		// pictures table:"). This is the same guard already applied to the DELETE path further
		// down; the ADD INDEX path was missed.
		$index_error = $wpdb->last_error;

		// A request that died before the DROP INDEX further down ran leaves ngg_dedupe_tmp_idx
		// behind; the next pass's ADD INDEX then fails with "duplicate key name" even though the
		// index this dedupe needs already exists. Treat "already present" as success instead of
		// bailing out and permanently skipping the dedupe.
		$tmp_index_exists = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = %s AND index_name = %s',
				$nggpictures,
				'ngg_dedupe_tmp_idx'
			)
		);

		if ( ! $tmp_index_exists ) {
			// Without the index, the self-join below falls back to an unindexed nested-loop scan
			// -- exactly the multi-minute, lock-heavy operation this function exists to avoid.
			// Surface the failure via ngg_upgrade_error (not ngg_init_check -- that option is
			// unconditionally cleared by Installer::set_role_caps() on every successful run,
			// which would wipe this before an admin ever sees it) and skip the dedupe rather
			// than silently running it the slow way.
			//
			// record_upgrade_error() owns the message shape -- a summary that stands on its own,
			// with the error appended only when there is one. See issue #960: a dropped
			// connection returns false with last_error empty, and every open-coded version of
			// this message emitted a sentence that trailed off after its colon.
			\Imagely\NGG\Util\Installer::record_upgrade_error(
				'NextGEN Gallery: could not add a temporary index before deduplicating the pictures table.',
				$index_error
			);
			return;
		}
	}

	// The attempt was already counted at the top of the function, so a request that dies here
	// cannot leave the next one free to start the same scan again. The index is in place by this
	// point, so what follows is the bounded, indexed form #939 measured -- not the unindexed
	// nested-loop scan that took 566s on ~40k MyISAM rows before that fix.
	$deleted = $wpdb->query(
		"DELETE p1 FROM `{$nggpictures}` p1
		INNER JOIN `{$nggpictures}` p2
			ON p1.galleryid = p2.galleryid
			AND p1.filename = p2.filename
			AND p1.pid > p2.pid"
	);

	// $wpdb->query() resets $wpdb->last_error at the start of every call, so the DROP INDEX
	// below would otherwise wipe out a DELETE failure's error before the check after it runs.
	$delete_error = $wpdb->last_error;

	$index_dropped = $wpdb->query( "ALTER TABLE `{$nggpictures}` DROP INDEX `ngg_dedupe_tmp_idx`" );
	$drop_error    = $wpdb->last_error;
	// phpcs:enable

	if ( false === $index_dropped ) {
		// The last unchecked write in this function, and the most misleading one to leave
		// unchecked: the success marker two blocks down is what stops this function ever running
		// again, so a failed drop was latched as a success and the temporary prefix index stayed
		// on the pictures table for the life of the install -- write amplification on every
		// image insert and Scan Folder run, recorded nowhere. Reported rather than fatal: the
		// duplicates are gone, and the abandonment path retries the drop on a later pass.
		\Imagely\NGG\Util\Installer::record_upgrade_error(
			'NextGEN Gallery: could not remove the temporary index it added to the pictures table.',
			$drop_error
		);
	}

	// If the dedupe query itself failed, the caller's ALTER TABLE ADD UNIQUE INDEX can fail the
	// same silent way #781 did -- surface it via ngg_upgrade_error, not ngg_init_check (that
	// option is unconditionally cleared by Installer::set_role_caps() on every successful run,
	// which would wipe this before an admin ever sees it), instead of letting the upgrade
	// proceed as if dedupe had succeeded. false === $deleted is the failure signal on its own;
	// a dropped connection or reconnect mid-statement can return false with last_error left
	// empty, so the error string is only optional detail, not a precondition for reporting the
	// failure at all.
	if ( false === $deleted ) {
		\Imagely\NGG\Util\Installer::record_upgrade_error(
			'NextGEN Gallery: could not deduplicate the pictures table before adding a unique index.',
			$delete_error
		);
		return;
	}

	// Only now: the duplicates are gone and the temporary index has been dropped, so there is
	// nothing left for a later pass to finish. Marking this any earlier is what would strand an
	// interrupted pass's leftovers forever -- see the docblock.
	update_option( 'ngg_pictures_dedupe_done', 1, false );
	delete_option( 'ngg_pictures_dedupe_attempts' );
}

/**
 * Cleans up after a dedupe that has used up its attempts, and says so.
 *
 * Reached only from the attempt-cap gate, which sits above the leftover-index recovery inside
 * nggallery_dedupe_pictures_table() -- so without this the temporary index a killed pass left on
 * the table could never be dropped again.
 *
 * The message is re-recorded on every pass rather than latched behind an option, deliberately:
 * Installer::update() deletes ngg_upgrade_error at the start of every upgrade pass that wins the
 * lock, so a "already reported" flag outlives the thing it suppressed and the site ends up
 * reporting itself healthy while the duplicates are still there. record_upgrade_error() drops an
 * identical message instead of repeating it, so re-recording cannot grow the option.
 *
 * The lookup for a leftover index is unconditional rather than gated behind a marker option:
 * this path is only reached on a site whose dedupe has already failed twice, so it is not on the
 * healthy-site query budget, and a marker written by this release could not find the index a
 * 4.4.0/4.4.1 pass stranded -- which is the population most likely to be carrying one.
 *
 * @param string $nggpictures Fully prefixed table name.
 * @param int    $attempts    How many attempts were spent.
 * @return void
 */
function nggallery_abandon_pictures_dedupe( $nggpictures, $attempts ) {
	global $wpdb;

	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$tmp_index_exists = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = %s AND index_name = %s',
			$nggpictures,
			'ngg_dedupe_tmp_idx'
		)
	);

	if ( $tmp_index_exists ) {
		$dropped    = $wpdb->query( "ALTER TABLE `{$nggpictures}` DROP INDEX `ngg_dedupe_tmp_idx`" );
		$drop_error = $wpdb->last_error;

		if ( false === $dropped ) {
			\Imagely\NGG\Util\Installer::record_upgrade_error(
				'NextGEN Gallery: could not remove the temporary index it added to the pictures table.',
				$drop_error
			);
		}
	}
	// phpcs:enable

	\Imagely\NGG\Util\Installer::record_upgrade_error(
		sprintf(
			/* translators: %d: how many times the cleanup was attempted. */
			__( 'NextGEN Gallery: gave up removing duplicate images from the pictures table after %d attempts, so some galleries may still show the same image twice.', 'nggallery' ),
			(int) $attempts
		)
	);
}

/**
 * Reports whether a table exists.
 *
 * Three-state on purpose. get_var() returns null both for "no rows" and for a query that
 * FAILED -- a lost connection whose reconnect fails, $wpdb->ready === false, a statement killed
 * by a timeout policy, a proxy rejecting SHOW -- so casting to bool reads "the database could
 * not answer" as proof the table is absent. Every caller here acts on that answer: it decides
 * whether the duplicate-image guard is created, and whether an admin is told a table could not
 * be created. Asserting absence from a failed probe is the same conflation the runtime check in
 * nggallery.php:check_schema_tables() already refuses to make.
 *
 * @param string $table Fully prefixed table name.
 * @return bool|null True/false when the database answered, null when it could not.
 */
function nggallery_table_exists( $table ) {
	global $wpdb;

	// last_error is not sufficient on its own: wpdb::query() returns false *before* it calls
	// flush() when $wpdb->ready is false, so that query never records an error and last_error
	// still holds whatever the previous one left -- an empty string after any success. The probe
	// would then answer "absent" about a query that was never executed.
	if ( ! $wpdb->ready ) {
		return null;
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
	$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', [ $wpdb->esc_like( $table ) ] ) );

	if ( ! empty( $wpdb->last_error ) || ! $wpdb->ready ) {
		return null;
	}

	return (bool) $found;
}

/**
 * Reports which of a CREATE TABLE statement's declared columns are absent from the live table.
 *
 * The schema pass cannot be verified from $wpdb->last_error alone. upgrade_schema() calls
 * dbDelta(), which issues many statements per call -- DESCRIBE, SHOW INDEX, then one ALTER per
 * missing column or index -- and each goes through wpdb::query(), which clears last_error on
 * entry. Only the final statement's error survives to be read afterwards, so on an existing
 * table a failed ADD COLUMN followed by a successful ADD INDEX leaves no trace at all. dbDelta()
 * cannot fill the gap either: it appends its "Added column ..." descriptions without checking
 * whether the query succeeded. Comparing the declared columns against the table is the one check
 * that does not depend on catching the error as it happens.
 *
 * Three-state like nggallery_table_exists(), and for the same reason: null means "could not be
 * read", never "nothing is missing". $wpdb->ready === false, a non-empty last_error, and an empty
 * column list all produce it -- the last one because information_schema.columns returns rows only
 * for tables the connecting account holds a privilege on, so a restricted grant yields zero rows
 * and no error, permanently. Callers have to test for null explicitly.
 *
 * @param string $table Fully prefixed table name.
 * @param string $sql   The CREATE TABLE statement handed to upgrade_schema().
 * @return array|null Declared column names missing from the table, or null if it could not be read.
 */
function nggallery_missing_columns( $table, $sql ) {
	global $wpdb;

	if ( ! $wpdb->ready ) {
		return null;
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
	$columns = $wpdb->get_col( $wpdb->prepare( 'SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = %s', $table ) );

	if ( ! empty( $wpdb->last_error ) || ! $wpdb->ready || empty( $columns ) ) {
		return null;
	}

	$present = array_map( 'strtolower', $columns );
	$missing = [];

	// One declaration per line in the statements above, each starting with the column name. The
	// key lines (PRIMARY KEY, KEY, UNIQUE KEY) are skipped -- an index is not a column, and
	// dbDelta's index handling is verified separately.
	foreach ( preg_split( '/\r\n|\r|\n/', $sql ) as $line ) {
		if ( ! preg_match( '/^\s*([a-z_][a-z0-9_]*)\s+[a-z]/i', $line, $match ) ) {
			continue;
		}

		$column = strtolower( $match[1] );

		if ( in_array( $column, [ 'primary', 'key', 'unique', 'create', 'index' ], true ) ) {
			continue;
		}

		if ( ! in_array( $column, $present, true ) ) {
			$missing[] = $column;
		}
	}

	return $missing;
}

/**
 * Records that an existing pictures table has no duplicate-image guard, and does not migrate it.
 *
 * The guard and the dedupe that prepares for it are one decision, not two. The dedupe's only
 * early exit is the key existing, so running it on a table that will never receive the key
 * re-runs ADD INDEX, the self-join DELETE and DROP INDEX on every version bump and module-list
 * change, forever, for a guard that never arrives (#935 added that exit expressly so the work
 * happens once per site). So on an existing table neither step runs.
 *
 * Creating the key here instead was considered and rejected. #941 deferred exactly this
 * migration on purpose, pending 4.4.0 telemetry that has not been collected, and it names a
 * hash-column design as the preferred shape over a 191-character prefix -- shipping the prefix
 * migration now pre-empts that decision. The cost side is unmeasured in the direction that
 * matters: #939 timed the *temp* index at 0.213s on ~40k MyISAM rows, but no issue in this
 * lineage has ever timed ADD UNIQUE KEY on a populated table, and #934 is a site-down report
 * whose mechanism is precisely a long ALTER holding a table-level lock inline from
 * Installer::update() on an ordinary page request. An unmeasured bound is not a bound.
 *
 * What this does instead is make the skipped population visible: the reason is stored locally
 * and reported on the weekly check-in (UsageTracking::get_data()), so #941 can size what was
 * left behind rather than infer it. Sites whose engine accepted the full-column key keep it --
 * dbDelta() never drops an index.
 *
 * @param string $nggpictures Fully prefixed table name.
 */
function nggallery_record_missing_pictures_guard( $nggpictures ) {
	global $wpdb;

	if ( ! $wpdb->ready || get_option( 'ngg_pictures_guard_deferred' ) ) {
		return;
	}

	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$key_exists = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = %s AND index_name = %s',
			$nggpictures,
			'unique_gallery_filename'
		)
	);

	// A failed lookup is not an absent key -- record nothing rather than mislabel a guarded
	// table as deferred.
	if ( ! empty( $wpdb->last_error ) || ! $wpdb->ready || $key_exists ) {
		return;
	}

	// TABLE_ROWS, not COUNT(*): this is a telemetry bucket for #941, and an exact count on a
	// large InnoDB table costs a full index scan on a page request to no benefit. It is
	// approximate on InnoDB and exact on MyISAM; either is enough to size a population.
	$rows = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT TABLE_ROWS FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = %s',
			$nggpictures
		)
	);
	// phpcs:enable

	if ( ! empty( $wpdb->last_error ) || ! $wpdb->ready ) {
		return;
	}

	update_option( 'ngg_pictures_guard_deferred', 'rows:' . (int) $rows, false );
}

/**
 * Adds the duplicate-image guard to the pictures table.
 *
 * Only ever called on a table the current request just created. Creating it on a populated
 * table is the #934 lock-storm migration #941 deferred, and is not this function's job -- see
 * nggallery_record_missing_pictures_guard().
 *
 * The key indexes a filename *prefix* rather than the whole column, because filename is
 * VARCHAR(255) and at utf8mb4 that is 1020 bytes -- past InnoDB's 767-byte per-column prefix
 * limit under ROW_FORMAT=COMPACT and, with galleryid's 8 bytes, past MyISAM's 1000-byte total
 * key limit. 191 is the largest prefix that fits both: 191 * 4 = 764 < 767, and 8 + 764 = 772
 * < 1000. It is also at or above the 185-character cap DataTypes\Image::validation() enforces on
 * new uploads, so for anything this plugin will store going forward the prefix discriminates
 * rows identically to a full-column key.
 *
 * @param string $nggpictures Fully prefixed table name.
 */
function nggallery_add_pictures_unique_key( $nggpictures ) {
	global $wpdb;

	// Named rather than inlined because the surrounding byte budget, not the number, is what a
	// future edit has to preserve -- see the docblock above.
	$unique_key_filename_prefix = 191;

	// $wpdb->prepare() has no placeholder for table or column identifiers; $nggpictures is a
	// fully-prefixed table name built by the caller and the prefix is an integer literal
	// assigned above, neither of them user input.
	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$key_added = $wpdb->query( "ALTER TABLE `{$nggpictures}` ADD UNIQUE KEY `unique_gallery_filename` (galleryid, filename({$unique_key_filename_prefix}))" );
	$key_error = $wpdb->last_error;
	// phpcs:enable

	if ( false === $key_added ) {
		// A fresh, empty table rules out the two failures this key is prone to -- 1071/1709 on
		// key length is what the prefix exists to avoid, and 1062 needs rows to collide -- so
		// anything reaching here is environmental (a restricted ALTER privilege, a dropped
		// connection). Report it rather than leaving the site silently unguarded, which is the
		// shape that let #781 go unnoticed.
		\Imagely\NGG\Util\Installer::record_upgrade_error(
			'NextGEN Gallery: the images table was created but the duplicate-image guard could not be added to it.',
			$key_error
		);
	}
}

/**
 * Creates all tables for the gallery called during register_activation hook
 */
function nggallery_install( $installer ) {
	global $wpdb;

	$nggpictures = $wpdb->prefix . 'ngg_pictures';
	$nggallery   = $wpdb->prefix . 'ngg_gallery';
	$nggalbum    = $wpdb->prefix . 'ngg_album';

	// Whether the pictures table exists *before* this request touches the schema. dbDelta() may
	// create it below, so anything downstream that depends on "was this table already here"
	// has to be answered now. null means the database could not answer -- no decision is safe
	// to make on a guess, so they all skip.
	$pictures_table_existed = nggallery_table_exists( $nggpictures );

	// An existing table is neither deduped nor given the key. The two are one decision: the
	// dedupe's only early exit is that key existing (#935 added it so the work happens once per
	// site), so deduping a table that will never receive the key re-runs an index build and a
	// self-join DELETE on every version bump forever. Migrating instead is what #941 deferred on
	// purpose. The absence is recorded for #941's telemetry rather than left silent.
	//
	// The dedupe therefore runs on no existing table at all, which also settles the #960
	// symptom: on a missing table every statement in it failed, and the first failure recorded
	// "could not add a temporary index before deduplicating the pictures table", whose real
	// cause was simply that the table was absent.
	if ( true === $pictures_table_existed ) {
		nggallery_record_missing_pictures_guard( $nggpictures );

		// Clean up the duplicate rows #781's race created, once per site. The UNIQUE KEY that
		// would stop them coming back is a separate, deferred decision -- see the function's
		// docblock.
		if ( ! get_option( 'ngg_pictures_dedupe_done' ) ) {
			nggallery_dedupe_pictures_table( $nggpictures );
		}
	}

	// Create pictures table.
	//
	// unique_gallery_filename is deliberately NOT part of the schema handed to dbDelta(), and
	// this is the subtle part of issue #989 -- getting it wrong either way breaks a different
	// population of sites.
	//
	// filename is VARCHAR(255), which at utf8mb4 is 1020 bytes on its own -- past InnoDB's
	// 767-byte per-column index prefix limit under ROW_FORMAT=COMPACT, and past MyISAM's
	// 1000-byte total key limit once galleryid's 8 bytes are added. A full-column key therefore
	// cannot be created at all on those servers, and because dbDelta() puts the key inside the
	// CREATE TABLE it fails the *entire* statement: the site ends up with no pictures table, no
	// gallery can hold an image, every request issues ~26 doomed schema queries, and the upload
	// error blames the filename's length instead (#989, a regression from the duplicate-image
	// guard added in 4.4.0 for #781).
	//
	// Simply prefixing the key in this string fixes that and breaks something worse. dbDelta()
	// diffs existing tables too, so on the population that has no key yet -- the same at-risk
	// engines -- it would emit ALTER TABLE ... ADD UNIQUE KEY, and a legal 772-byte key makes
	// that ALTER *succeed* where it used to be rejected instantly at validation. That instant
	// rejection was accidental protection: the ALTER becomes a real index build holding a
	// table-level lock on MyISAM, running inline from Installer::update() on an ordinary page
	// request. That is the mechanism of #934, a site-down report reproduced at 165k rows, and
	// #941 deferred exactly this migration on purpose pending 4.4.0 telemetry.
	//
	// So the key is created explicitly after the schema pass, and only on a table this request
	// just created (see nggallery_add_pictures_unique_key() below). An existing table without the
	// key keeps today's behaviour -- no guard, no migration, no lock -- and records
	// ngg_pictures_guard_deferred so #941 can size and target it. Existing tables that already
	// carry the full-length key are untouched either way: dbDelta() never drops an index, and it
	// matches indices ignoring sub-parts (wp-admin/includes/upgrade.php,
	// $indices_without_subparts).
	$sql = 'CREATE TABLE ' . $nggpictures . " (
        pid BIGINT(20) NOT NULL AUTO_INCREMENT ,
        image_slug VARCHAR(255) NOT NULL ,
        post_id BIGINT(20) DEFAULT '0' NOT NULL ,
        galleryid BIGINT(20) DEFAULT '0' NOT NULL ,
        filename VARCHAR(255) NOT NULL ,
        description MEDIUMTEXT NULL ,
        alttext MEDIUMTEXT NULL ,
        imagedate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        exclude TINYINT NULL DEFAULT '0' ,
        sortorder BIGINT(20) DEFAULT '0' NOT NULL ,
        meta_data LONGTEXT,
        extras_post_id BIGINT(20) DEFAULT '0' NOT NULL,
        PRIMARY KEY  (pid),
        KEY extras_post_id_key (extras_post_id)
	);";
	$installer->upgrade_schema( $sql );

	// dbDelta() reports nothing when a statement fails, so the only trace of a failed CREATE
	// TABLE is wpdb::last_error -- and every later query clears it, including the remaining
	// upgrade_schema() calls, the guard's ALTER below, and the existence checks at the end of
	// this function. Each table's error therefore has to be captured the instant its own
	// statement returns, keyed by table, so the verification at the end can name the actual
	// database error rather than only reporting that a table is absent. That is what left #989's
	// real cause ("Specified key was too long") visible nowhere on the affected site.
	$schema_errors                 = [];
	$schema_errors[ $nggpictures ] = $wpdb->last_error;

	// Kept so the verification at the end can compare each table against the columns its own
	// statement declared. last_error cannot carry that on its own: dbDelta() issues one ALTER
	// per missing column and index, each through wpdb::query(), which clears last_error on
	// entry -- so on an existing table a failed ADD COLUMN followed by a successful ADD INDEX
	// leaves the sample below empty. dbDelta()'s return value does not help either; it appends
	// "Added column ..." without checking whether the query succeeded.
	$schema_sql                 = [];
	$schema_sql[ $nggpictures ] = $sql;

	// Install the duplicate-image guard, and only on a table this request just created. There the
	// index build is instant and cannot hit a duplicate-entry error, because there are no rows
	// yet -- which is also why this placement sidesteps the prefix-versus-dedupe mismatch
	// entirely (the dedupe compares whole filenames, this key compares 191 characters, and on an
	// empty table the two cannot disagree). Both conditions must be positive answers: null from
	// either probe means the database could not say, and creating the key on a table that turns
	// out to be populated is the migration this PR deliberately does not perform.
	if ( false === $pictures_table_existed && true === nggallery_table_exists( $nggpictures ) ) {
		nggallery_add_pictures_unique_key( $nggpictures );
	}

	// Create gallery table.
	$sql = 'CREATE TABLE ' . $nggallery . " (
        gid BIGINT(20) NOT NULL AUTO_INCREMENT ,
        name VARCHAR(255) NOT NULL ,
        slug VARCHAR(255) NOT NULL ,
        path MEDIUMTEXT NULL ,
        title MEDIUMTEXT NULL ,
        galdesc MEDIUMTEXT NULL ,
        pageid BIGINT(20) DEFAULT '0' NOT NULL ,
        previewpic BIGINT(20) DEFAULT '0' NOT NULL ,
        author BIGINT(20) DEFAULT '0' NOT NULL  ,
        extras_post_id BIGINT(20) DEFAULT '0' NOT NULL,
        date_created DATETIME NULL,
        date_modified DATETIME NULL,
        PRIMARY KEY  (gid),
        KEY extras_post_id_key (extras_post_id)
	)";
	$installer->upgrade_schema( $sql );
	$schema_errors[ $nggallery ] = $wpdb->last_error;
	$schema_sql[ $nggallery ]    = $sql;

	// Create albums table.
	$sql = 'CREATE TABLE ' . $nggalbum . " (
        id BIGINT(20) NOT NULL AUTO_INCREMENT ,
        name VARCHAR(255) NOT NULL ,
        slug VARCHAR(255) NOT NULL ,
        previewpic BIGINT(20) DEFAULT '0' NOT NULL ,
        albumdesc MEDIUMTEXT NULL ,
        sortorder LONGTEXT NOT NULL,
        pageid BIGINT(20) DEFAULT '0' NOT NULL,
        extras_post_id BIGINT(20) DEFAULT '0' NOT NULL,
        date_created DATETIME NULL,
        date_modified DATETIME NULL,
        PRIMARY KEY  (id),
        KEY extras_post_id_key (extras_post_id)
	)";
	$installer->upgrade_schema( $sql );
	$schema_errors[ $nggalbum ] = $wpdb->last_error;
	$schema_sql[ $nggalbum ]    = $sql;

	// Verify every table this function is responsible for, not just the pictures table.
	//
	// This check used to cover the pictures table alone, which made a failed gallery or album
	// CREATE TABLE completely silent: upgrade_schema() returns dbDelta()'s diagnostic array and
	// nggallery_install() discards it at all three call sites, so nothing else would notice.
	// Installer::update() then records ngg_plugin_version regardless of what the handlers
	// achieved, after which $do_upgrade is false on every later request and this function is
	// never reached again until the next plugin version -- a deactivate/reactivate does not help.
	// A site whose gallery table failed to create therefore has no galleries at all, no notice,
	// and no way back.
	//
	// It also used to write ngg_init_check, which could never be seen: Installer::update() calls
	// set_role_caps() immediately after the install handlers return, and that unconditionally
	// delete_option()s ngg_init_check -- in the same request this check runs in. So the one
	// notice that would have named the problem was guaranteed to be erased before admin_notices
	// ever fired, which is a second reason #989's site reported nothing. ngg_schema_error is a
	// separate option nothing else clears.
	//
	// A table that survived its statement but whose error is non-empty is reported too. dbDelta()
	// emits ALTERs rather than a CREATE for an existing table, so a column it could not add
	// leaves the table in place, half-migrated, with nothing recorded -- and since
	// Installer::update() stamps ngg_plugin_version regardless, this function is unreachable
	// again until the next release. That goes to ngg_upgrade_error rather than ngg_schema_error:
	// the table exists, so the self-clearing condition on ngg_schema_error would erase it
	// immediately.
	$missing        = [];
	$missing_tables = [];
	$outdated       = [];
	$unanswered     = false;
	$tables         = [
		$nggpictures => [
			__( 'NextGEN Gallery: the images table could not be created, so no gallery can hold an image.', 'nggallery' ),
			__( 'NextGEN Gallery: the images table exists but could not be brought up to date.', 'nggallery' ),
		],
		$nggallery   => [
			__( 'NextGEN Gallery: the galleries table could not be created, so no gallery can be saved.', 'nggallery' ),
			__( 'NextGEN Gallery: the galleries table exists but could not be brought up to date.', 'nggallery' ),
		],
		$nggalbum    => [
			__( 'NextGEN Gallery: the albums table could not be created, so no album can be saved.', 'nggallery' ),
			__( 'NextGEN Gallery: the albums table exists but could not be brought up to date.', 'nggallery' ),
		],
	];

	foreach ( $tables as $table => $summaries ) {
		list( $missing_summary, $outdated_summary ) = $summaries;

		$exists = nggallery_table_exists( $table );
		$error  = ! empty( $schema_errors[ $table ] ) ? $schema_errors[ $table ] : '';

		if ( '' !== $error ) {
			/* translators: %s: the error reported by the database server. */
			$detail = ' ' . sprintf( __( 'The database reported: %s', 'nggallery' ), $error );
		} else {
			$detail = '';
		}

		// The probe failed, so whether this table exists is unknown. Don't claim it is missing,
		// and don't let the clearing branch below act as if every table had answered.
		if ( null === $exists ) {
			$unanswered = true;

			if ( '' !== $error ) {
				$outdated[] = $outdated_summary . $detail;
			}

			continue;
		}

		if ( $exists ) {
			// The error sample is only half the check -- see the note on $schema_sql above. Ask
			// the table which of its declared columns are actually there, which is the only way
			// a failed ALTER in the middle of dbDelta's sequence leaves a trace.
			$missing_columns = nggallery_missing_columns( $table, $schema_sql[ $table ] );

			// null is "the column list could not be read", not "nothing is missing": a lost
			// connection, $wpdb->ready === false, or a grant that exposes no rows in
			// information_schema.columns for this table all land here. Collapsing them into
			// "complete" reports a half-finished upgrade as healthy and -- because
			// Installer::update() stamps ngg_plugin_version regardless -- keeps it that way
			// until the next release. Treated like a null from nggallery_table_exists() above:
			// claim nothing, and do not let the clearing branch below act as if every table had
			// answered.
			if ( null === $missing_columns ) {
				$unanswered = true;

				if ( '' !== $detail ) {
					$outdated[] = $outdated_summary . $detail;
				}

				continue;
			}

			if ( ! empty( $missing_columns ) ) {
				/* translators: %s: comma-separated list of database column names. */
				$detail .= ' ' . sprintf( __( 'Missing columns: %s.', 'nggallery' ), implode( ', ', $missing_columns ) );
			}

			if ( '' !== $detail ) {
				$outdated[] = $outdated_summary . $detail;
			}

			continue;
		}

		$missing[]        = $missing_summary . $detail;
		$missing_tables[] = $table;
	}

	if ( $outdated ) {
		// record_upgrade_error() appends, so this does not discard a dedupe or guard failure
		// recorded earlier in the same request -- ngg_upgrade_error holds one string.
		\Imagely\NGG\Util\Installer::record_upgrade_error( implode( ' ', $outdated ) );
	}

	if ( $missing ) {
		$missing[] = __( 'Please check your database settings.', 'nggallery' );
		update_option( 'ngg_schema_error', implode( ' ', $missing ) );
		// Which tables this message is about, so the runtime check in nggallery.php can tell
		// whether it still describes the tables that are missing later, without having to match
		// its own differently-worded summaries against this text.
		update_option( 'ngg_schema_error_tables', $missing_tables, false );
	} elseif ( ! $unanswered && get_option( 'ngg_schema_error' ) ) {
		// Every table is present now, so whatever this reported has been resolved. Unlike the
		// ngg_upgrade_error messages above -- which record that a one-off upgrade step failed --
		// this option describes a condition that is either true right now or is not, so it has
		// to clear itself or it would outlive the problem forever. Clearing it is only correct
		// because the loop above checked all three tables: while this check was pictures-only it
		// would have cleared a notice about a table it never looked at.
		delete_option( 'ngg_schema_error' );
		delete_option( 'ngg_schema_error_tables' );
	}
}

/**
 * Removes a capability from classic roles.
 *
 * @param string $capability name of the capability which should be de-registered
 */
function ngg_remove_capability( $capability ) {
	// this function remove the $capability only from the classic roles.
	$check_order = [ 'subscriber', 'contributor', 'author', 'editor', 'administrator' ];

	foreach ( $check_order as $role ) {
		$role = get_role( $role );
		if ( ! is_null( $role ) ) {
			$role->remove_cap( $capability );
		}
	}
}
