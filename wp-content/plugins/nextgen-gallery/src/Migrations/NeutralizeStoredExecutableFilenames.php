<?php
/**
 * Migration: rewrite image rows whose stored file name carries a PHP-executable segment.
 *
 * @package Imagely\NGG\Migrations
 */

namespace Imagely\NGG\Migrations;

use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DynamicThumbnails\Manager as DynamicThumbnails;
use Imagely\NGG\Util\Security;
use Imagely\NGG\Util\Serializable;

/**
 * Renames image files stored with a PHP-executable segment ("photo.php_.jpg") and rewrites their rows.
 *
 * Such a destination is refused by generate_image_size(), so these rows can never generate a size
 * again. Rows are selected on the narrow PHP pattern the refusal uses, but renamed with the wide
 * neutralizer, so they end up with the name a fresh upload would get.
 *
 * Idempotent and resumable. Not reversible.
 */
class NeutralizeStoredExecutableFilenames {

	const OPTION = 'imagely_executable_filenames_neutralized';
	const CURSOR = 'imagely_executable_filenames_cursor';
	const REPORT = 'imagely_executable_filenames_report';
	const FAIL   = 'imagely_executable_filenames_failures';

	const BATCH                = 50;
	const MAX_ROWS_PER_REQUEST = 5000;
	const TIME_BUDGET          = 20;
	const MAX_FAILURES         = 5;
	const FAIL_TTL             = DAY_IN_SECONDS;
	const MAX_REPORTED_DETAILS = 25;

	const DONE = 'done';

	/**
	 * Per-row outcomes. RETRY is a transient failure: the row keeps the cursor and the run does
	 * not complete, so the next pass tries it again. SKIPPED is terminal and does not block.
	 */
	const UNCHANGED = 'unchanged';
	const RENAMED   = 'renamed';
	const SKIPPED   = 'skipped';
	const RETRY     = 'retry';

	/**
	 * Per-file move outcomes. ALREADY and MISSING both mean the source is gone, but only ALREADY
	 * means the file is at the destination, so only ALREADY may rewrite a stored name.
	 */
	const MOVED   = 'moved';
	const ALREADY = 'already';
	const MISSING = 'missing';
	const FAILED  = 'failed';

	/**
	 * Runs the migration.
	 *
	 * @param bool $force Re-scan every row even if the migration is already marked complete.
	 * @return bool True when there is nothing left to do; false when deferred, incomplete or failed.
	 */
	public static function migrate( $force = false ) {
		/*
		 * No capability gate. The only caller is Settings\Installer::install(), reached from
		 * Util\Installer::update() while it holds the atomic upgrade lock, and that runs in
		 * whatever request detects the version change, frontend and REST included, where there is
		 * no logged-in user to check. The lock is the authorization.
		 */
		if ( ! $force && \get_option( self::OPTION ) ) {
			return true;
		}

		if ( $force ) {
			\delete_option( self::CURSOR );
			\delete_option( self::FAIL );
		}

		// Stops a persistent database error re-running the scan on every admin request.
		$failures = self::failure_count();

		if ( $failures >= self::MAX_FAILURES ) {
			return false;
		}

		global $wpdb;

		$table  = $wpdb->prefix . 'ngg_pictures';
		$cursor = \get_option( self::CURSOR, 0 );

		if ( self::DONE === $cursor ) {
			self::finish();
			return true;
		}

		// Nothing to migrate and nothing to keep retrying: without this the scan errors on every
		// run, climbs the failure budget, decays, and repeats for the life of the install.
		if ( ! self::table_exists( $table ) ) {
			self::finish();
			return true;
		}

		$budget   = self::MAX_ROWS_PER_REQUEST;
		$deadline = \microtime( true ) + self::TIME_BUDGET;
		$totals   = [
			self::RENAMED => 0,
			self::SKIPPED => 0,
			self::RETRY   => 0,
		];
		$details  = [];
		$deferred = false;

		// Runs to completion rather than one page per request: the caller holds the upgrade lock and
		// is reached once per version change, so there is no later request to resume in. The wall
		// clock bounds it, and an unfinished pass keeps its cursor for the next upgrade.
		while ( $budget > 0 && \microtime( true ) < $deadline ) {
			$rows = self::fetch_candidates( $table, (int) $cursor );

			if ( false === $rows ) {
				\update_option(
					self::FAIL,
					[
						'count' => $failures + 1,
						'time'  => \time(),
					],
					false
				);
				self::record( $totals, $details );
				return false;
			}

			// One lookup for the whole page rather than one per row: on a pre-4.4 table there is no
			// (galleryid, filename) key, so a per-row probe is a full scan run up to BATCH times.
			$taken = self::taken_names( $rows );

			foreach ( $rows as $row ) {
				$detail = '';
				// $taken is by reference: a name this page just claimed has to be visible to the
				// rows after it, or two candidates that neutralize alike both read "free".
				$result = self::neutralize_row( (int) $row->pid, $detail, $taken );

				// The cursor always advances. Holding it on a row that cannot be repaired blocks
				// every affected row behind it, and a row whose obstruction never clears blocks
				// them for good. A row needing another go is reported and left for the next pass.
				$cursor = (int) $row->pid;
				--$budget;

				if ( self::RETRY === $result ) {
					$deferred = true;
				}

				if ( self::UNCHANGED === $result ) {
					continue;
				}

				++$totals[ $result ];

				if ( count( $details ) < self::MAX_REPORTED_DETAILS ) {
					$details[] = sprintf( '#%1$d: %2$s', (int) $row->pid, $detail );
				}
			}

			// A short page means the filtered set is exhausted.
			if ( count( $rows ) < self::BATCH ) {
				$cursor = self::DONE;
				break;
			}
		}

		\update_option( self::CURSOR, $cursor, false );
		self::record( $totals, $details );

		\delete_option( self::FAIL );

		if ( self::DONE !== $cursor ) {
			return false;
		}

		/*
		 * The table was walked to the end but some rows still need another go. The marker is
		 * withheld and the cursor dropped, so the next upgrade rescans and finds exactly those
		 * rows: a repaired name no longer matches the prefilter, a deferred one still does. The
		 * report names them, so an obstruction that never clears stays visible instead of being
		 * silently retired.
		 */
		if ( $deferred ) {
			\delete_option( self::CURSOR );
			return false;
		}

		self::finish();
		return true;
	}

	/**
	 * Rewrites one row and renames its files.
	 *
	 * @param int        $pid    Image id.
	 * @param string     $detail Out-param. Human-readable reason, for the report.
	 * @param array|null $taken  Optional map of "galleryid\0filename" => pid for the whole page, so
	 *                           the collision check costs one query per page instead of one per row.
	 *                           Null falls back to a per-row query. Passed by reference: a name this
	 *                           row claims is added so later rows in the page can see it.
	 * @return string One of self::UNCHANGED, self::RENAMED, self::SKIPPED, self::RETRY.
	 */
	public static function neutralize_row( $pid, &$detail = null, &$taken = null ) {
		$detail  = '';
		$storage = StorageManager::get_instance();

		global $wpdb;

		$image = ImageMapper::get_instance()->find( (int) $pid );

		/*
		 * The mapper cannot tell a missing row from a failed query: run_query() reads
		 * $wpdb->last_result, which is an empty array either way, and nothing in that chain looks
		 * at last_error. Treating a failure as absence would retire the row under the completion
		 * marker, so the error is read here instead.
		 */
		if ( ! is_object( $image ) || empty( $image->pid ) ) {
			if ( $wpdb->last_error ) {
				$detail = 'the image row could not be read';
				return self::RETRY;
			}

			$detail = 'the image row could not be loaded';
			return self::SKIPPED;
		}

		$stored = isset( $image->filename ) ? (string) $image->filename : '';

		if ( '' === $stored ) {
			return self::UNCHANGED;
		}

		// A directory component is a different defect; renaming around it would guess its meaning.
		if ( \wp_basename( $stored ) !== $stored ) {
			$detail = 'the stored file name carries a directory component';
			return self::SKIPPED;
		}

		// Narrow pattern: the one the refusal tests.
		if ( ! Security::has_php_executable_extension( $stored ) ) {
			return self::UNCHANGED;
		}

		$gallery_path = $storage->get_gallery_abspath( $image->galleryid );

		if ( ! $gallery_path ) {
			$detail = 'the gallery directory could not be resolved';
			return self::SKIPPED;
		}

		$new_filename = Security::neutralize_executable_extensions( $stored );

		// A name that came back empty, unchanged or still executable would point at nothing.
		if ( '' === $new_filename || $new_filename === $stored || Security::has_php_executable_extension( $new_filename ) ) {
			$detail = sprintf( 'no inert name could be derived from "%s"', $stored );
			return self::SKIPPED;
		}

		// galleryid + filename is UNIQUE on some tables, and a duplicate is wrong on the rest.
		$conflict = self::filename_owner( $image->galleryid, $new_filename, (int) $image->pid, $taken );

		if ( -1 === $conflict ) {
			$detail = sprintf( 'could not check whether "%s" is already taken in this gallery', $new_filename );
			return self::RETRY;
		}

		if ( $conflict ) {
			$detail = sprintf( '"%1$s" is already taken in this gallery by image #%2$d', $new_filename, $conflict );
			return self::SKIPPED;
		}

		$plan = self::plan_renames( $image, $gallery_path, $stored, $new_filename );

		// Full size first and alone: the rest are derivatives generate_image_size() can rebuild.
		$primary = array_shift( $plan['moves'] );
		$result  = self::move_file( $primary[0], $primary[1], $gallery_path );

		if ( self::FAILED === $result ) {
			$detail = sprintf( 'renaming "%1$s" to "%2$s" failed', $stored, $new_filename );
			return self::RETRY;
		}

		/*
		 * Nothing is on disk under either name, so renaming the row would only move the breakage:
		 * get_image_url() substitutes the placeholder for an absent file ONLY while the stored name
		 * still reads as executable, so an inert name pointing at nothing renders as a broken icon
		 * instead. Left as it is, which also leaves a hand-renamed file discoverable.
		 */
		if ( self::MISSING === $result ) {
			$detail = sprintf( 'no file exists under "%1$s" or "%2$s", so the row is left alone', $stored, $new_filename );
			return self::SKIPPED;
		}

		/*
		 * The destination already holds a file. That is this row's own interrupted rename only if no
		 * other row owns the name, so it is re-probed live rather than trusted from the page map,
		 * which was built before any of this page's renames happened.
		 */
		if ( self::ALREADY === $result ) {
			$owner = self::filename_owner( $image->galleryid, $new_filename, (int) $image->pid, null );

			if ( -1 === $owner ) {
				$detail = sprintf( 'could not confirm who owns "%s"', $new_filename );
				return self::RETRY;
			}

			if ( $owner ) {
				$detail = sprintf( '"%1$s" already holds another image\'s file (#%2$d)', $new_filename, $owner );
				return self::SKIPPED;
			}
		}

		$done      = [ $primary ];
		$partial   = [];
		$meta_data = null;

		foreach ( $plan['moves'] as $move ) {
			$moved = self::move_file( $move[0], $move[1], $gallery_path );

			/*
			 * A meta-backed size whose move was refused must not be left behind: the size path is
			 * taken from meta_data in preference to anything derived from the row's filename, so a
			 * stale executable name there is refused on every render, for good, while the row reads
			 * as repaired. Retried instead of reported.
			 */
			if ( self::FAILED === $moved ) {
				$partial[] = \wp_basename( $move[0] );

				if ( null !== $move[2] ) {
					// Everything already moved goes back first. Returning with the full size moved
					// but the row unwritten would leave the row pointing at a name nothing holds,
					// and the row is retired once its attempts run out.
					self::undo( $done, $gallery_path );

					$detail = sprintf( 'could not rename the "%1$s" size of "%2$s"', $move[2], $stored );
					return self::RETRY;
				}

				continue;
			}

			if ( self::MISSING !== $moved ) {
				$done[] = $move;
			}

			/*
			 * $move[2] is the meta_data size this file belongs to, or null when the name is
			 * conventional and nothing in the row refers to it. MISSING is applied as well as
			 * MOVED and ALREADY: nothing is on disk under either name, so the inert name is the
			 * one the size should be generated under.
			 */
			if ( null !== $move[2] && isset( $plan['meta_names'][ $move[2] ] ) ) {
				$meta_data                         = null === $meta_data ? $plan['meta_data'] : $meta_data;
				$meta_data[ $move[2] ]['filename'] = $plan['meta_names'][ $move[2] ];
			}
		}

		if ( ! self::write_row( $image, $new_filename, $meta_data ) ) {
			// Files moved but the row still points at the old names. Put them back.
			$undone = self::undo( $done, $gallery_path );

			$detail = sprintf(
				'the database write for "%1$s" was refused; %2$s',
				$new_filename,
				$undone ? 'the renames were rolled back' : 'the roll-back did not fully succeed'
			);
			return self::RETRY;
		}

		// The name is now this row's, so later rows in the same page must see it as taken.
		if ( is_array( $taken ) ) {
			$taken[ (int) $image->galleryid . "\0" . $new_filename ] = (int) $image->pid;
		}

		foreach ( $plan['sizes'] as $size ) {
			$storage->flush_image_path_cache( $image->pid, $size );
		}

		$detail = sprintf( '"%1$s" renamed to "%2$s"', $stored, $new_filename );

		if ( $partial ) {
			$detail .= sprintf( ' (could not rename %s)', implode( ', ', $partial ) );
		}

		return self::RENAMED;
	}

	/**
	 * The next page of rows that could carry a PHP-executable segment.
	 *
	 * Prefilter only, on `filename` alone: per-size names in `meta_data` are derived from it and
	 * `meta_data` is not assignable anywhere. Two patterns because every matched segment begins
	 * "ph" except `hphp`.
	 *
	 * @param string $table  Fully-qualified pictures table.
	 * @param int    $cursor Highest pid already processed.
	 * @return array|false Rows, or false on a database error.
	 */
	private static function fetch_candidates( $table, $cursor ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name.
				"SELECT pid, galleryid, filename FROM `{$table}` WHERE pid > %d AND ( filename LIKE %s OR filename LIKE %s ) ORDER BY pid ASC LIMIT %d",
				$cursor,
				'%' . $wpdb->esc_like( '.ph' ) . '%',
				'%' . $wpdb->esc_like( '.hphp' ) . '%',
				self::BATCH
			)
		);

		// get_results() answers a failed query with an empty array, which would read as "no rows
		// left" and mark the migration complete. query() just cleared last_error, so it is ours.
		if ( $wpdb->last_error ) {
			return false;
		}

		return is_array( $rows ) ? $rows : [];
	}

	/**
	 * The pid of another row holding $filename in the same gallery, if there is one.
	 *
	 * @param int        $gallery_id Gallery id.
	 * @param string     $filename   File name to look for.
	 * @param int        $except_pid Row being renamed.
	 * @param array|null $taken      Optional pre-resolved map for the whole page.
	 * @return int Owning pid, 0 when the name is free, -1 when the query failed.
	 */
	private static function filename_owner( $gallery_id, $filename, $except_pid, $taken = null ) {
		global $wpdb;

		if ( is_array( $taken ) ) {
			$key   = (int) $gallery_id . "\0" . $filename;
			$owner = isset( $taken[ $key ] ) ? (int) $taken[ $key ] : 0;

			return $owner === (int) $except_pid ? 0 : $owner;
		}

		$table = $wpdb->prefix . 'ngg_pictures';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$owner = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name.
				"SELECT pid FROM `{$table}` WHERE galleryid = %d AND filename = %s AND pid <> %d LIMIT 1",
				(int) $gallery_id,
				$filename,
				(int) $except_pid
			)
		);

		// get_var() returns null on failure, which would read as "the name is free".
		if ( $wpdb->last_error ) {
			return -1;
		}

		return (int) $owner;
	}

	/**
	 * Builds the rename list and the rewritten meta_data for one row.
	 *
	 * Paths are derived the way get_computed_image_abspath() derives them: a derivative missed
	 * here keeps its executable name and is refused on the next render.
	 *
	 * @param object $image        Image entity.
	 * @param string $gallery_path Absolute gallery directory.
	 * @param string $old_filename Stored file name.
	 * @param string $new_filename Neutralized file name.
	 * @return array {moves: list of [from, to, meta size or null], meta_data: array|null,
	 *               meta_names: map of size => new name, sizes: array}
	 */
	private static function plan_renames( $image, $gallery_path, $old_filename, $new_filename ) {
		$storage = StorageManager::get_instance();

		$moves = [
			[ \path_join( $gallery_path, $old_filename ), \path_join( $gallery_path, $new_filename ), null ],
			// imgBackup writes "<filename>_backup" beside the image.
			[ \path_join( $gallery_path, $old_filename . '_backup' ), \path_join( $gallery_path, $new_filename . '_backup' ), null ],
		];

		$sizes      = [ 'full' ];
		$meta_names = [];
		$meta_data  = isset( $image->meta_data ) ? $image->meta_data : null;
		$meta_data  = is_object( $meta_data ) ? get_object_vars( $meta_data ) : $meta_data;

		$dynthumbs  = DynamicThumbnails::get_instance();
		$cache_path = $storage->get_cache_abspath( $image->galleryid );

		foreach ( $storage->get_image_sizes( $image ) as $size ) {
			if ( 'full' === $size ) {
				continue;
			}

			$sizes[] = $size;

			// 'thumbnail' lives in "thumbs/"; every other named size in a directory of its own name.
			$prefix     = 'thumbnail' === $size ? 'thumbs' : $size;
			$is_dynamic = $dynthumbs && $dynthumbs->is_size_dynamic( $size );
			$base       = $is_dynamic ? $cache_path : \path_join( $gallery_path, $prefix );

			if ( is_array( $meta_data ) && isset( $meta_data[ $size ]['filename'] ) ) {
				$stored_size_name = (string) $meta_data[ $size ]['filename'];

				// Same refusal as the row's own name.
				if ( \wp_basename( $stored_size_name ) !== $stored_size_name ) {
					continue;
				}

				$new_size_name = Security::neutralize_executable_extensions( $stored_size_name );

				if ( $new_size_name === $stored_size_name ) {
					continue;
				}

				// The name is recorded rather than applied: the caller rewrites it only if the
				// file really arrives at the new name.
				$moves[]             = [ \path_join( $base, $stored_size_name ), \path_join( $base, $new_size_name ), $size ];
				$meta_names[ $size ] = $new_size_name;
				continue;
			}

			if ( $is_dynamic ) {
				continue;
			}

			// No meta entry: conventional "<prefix>-<filename>" or pre-3.19 "<prefix>_<filename>".
			// Both are renamed, because the separator is a setting that can be flipped back.
			foreach ( [ '-', '_' ] as $separator ) {
				$moves[] = [
					\path_join( $base, $prefix . $separator . $old_filename ),
					\path_join( $base, $prefix . $separator . $new_filename ),
					null,
				];
			}
		}

		return [
			'moves'      => $moves,
			'meta_data'  => $meta_data,
			'meta_names' => $meta_names,
			'sizes'      => $sizes,
		];
	}

	/**
	 * Puts a row's completed moves back, newest first.
	 *
	 * @param array  $done Moves that landed, as [ from, to, meta size or null ].
	 * @param string $base Gallery directory.
	 * @return bool Whether every move was reversed.
	 */
	private static function undo( $done, $base ) {
		$ok = true;

		foreach ( array_reverse( $done ) as $move ) {
			$result = self::move_file( $move[1], $move[0], $base );

			if ( self::MOVED !== $result && self::MISSING !== $result ) {
				$ok = false;
			}
		}

		return $ok;
	}

	/**
	 * Renames one file inside a gallery directory.
	 *
	 * @param string $from Absolute source path.
	 * @param string $to   Absolute destination path.
	 * @param string $base Gallery directory both paths must resolve inside.
	 * @return string self::MOVED, self::ALREADY, self::MISSING or self::FAILED.
	 */
	private static function move_file( $from, $to, $base ) {
		$storage = StorageManager::get_instance();

		// Re-checked rather than trusted from the caller: this writes to the filesystem.
		if ( ! $storage->is_path_contained( $base, $from ) || ! $storage->is_path_contained( $base, $to ) ) {
			return self::FAILED;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$target = @file_exists( $to );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @file_exists( $from ) ) {
			// A destination that exists is this row's own earlier rename, so the stored name may
			// be rewritten. Neither existing means nothing is there to point at.
			return $target ? self::ALREADY : self::MISSING;
		}

		// Never overwrite: the file already there is the one the rewritten row will point at.
		if ( $target ) {
			return self::FAILED;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename, WordPress.PHP.NoSilencedErrors.Discouraged
		return @rename( $from, $to ) ? self::MOVED : self::FAILED;
	}

	/**
	 * Writes the neutralized name, and any rewritten size names, to the row.
	 *
	 * A two-column UPDATE rather than a mapper save(), which writes every column back and would
	 * re-sanitize `alttext` and `description` on rows nobody edited.
	 *
	 * @param object     $image        Image entity.
	 * @param string     $new_filename Neutralized file name.
	 * @param array|null $meta_data    Rewritten meta_data, or null when no size name changed.
	 * @return bool
	 */
	private static function write_row( $image, $new_filename, $meta_data ) {
		global $wpdb;

		$data = [ 'filename' => $new_filename ];

		if ( null !== $meta_data ) {
			$serialized = Serializable::serialize( $meta_data );

			/*
			 * Serializable::serialize() falls back to preg_replace() on the array itself when
			 * wp_json_encode() fails, which stringifies every nested size entry to the literal
			 * "Array". Legacy rows carrying invalid UTF-8 are exactly the ones that defeat the
			 * encoder and exactly this pass's population, and that shape fatals the size
			 * resolver. So the value is read back and the meta rewrite abandoned unless it
			 * survives the round trip. The filename fix still lands.
			 */
			if ( Serializable::unserialize( $serialized ) == $meta_data ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual -- Decoded arrays differ in key order and numeric type only.
				$data['meta_data'] = $serialized;
			}
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$updated = $wpdb->update( $wpdb->prefix . 'ngg_pictures', $data, [ 'pid' => (int) $image->pid ] );

		if ( false === $updated ) {
			return false;
		}

		$image->filename = $new_filename;

		if ( null !== $meta_data ) {
			$image->meta_data = $meta_data;
		}

		// The mapper memoises rows per request; a later reader would see the old name.
		ImageMapper::get_instance()->flush_query_cache();

		return true;
	}

	/**
	 * Marks the migration complete and clears the cursor.
	 *
	 * @return void
	 */
	private static function finish() {
		\delete_option( self::CURSOR );

		// Autoloaded, like the markers of the other migrations: migrate() reads it on every admin
		// request, and a non-autoloaded option costs its own query there.
		\update_option( self::OPTION, 1 );
	}

	/**
	 * Accumulates the run's counts and its first few per-row reasons.
	 *
	 * Logged as well as stored: the option is not surfaced in the UI.
	 *
	 * @param array $totals  Counts for this request.
	 * @param array $details Per-row reasons for this request.
	 * @return void
	 */
	private static function record( $totals, $details ) {
		if ( ! array_sum( $totals ) ) {
			return;
		}

		$stored = \get_option( self::REPORT, [] );
		$stored = is_array( $stored ) ? $stored : [];

		foreach ( $totals as $key => $count ) {
			$stored[ $key ] = ( isset( $stored[ $key ] ) ? (int) $stored[ $key ] : 0 ) + $count;
		}

		$previous          = isset( $stored['details'] ) && is_array( $stored['details'] ) ? $stored['details'] : [];
		$stored['details'] = array_slice( array_merge( $previous, $details ), 0, self::MAX_REPORTED_DETAILS );
		$stored['updated'] = \gmdate( 'Y-m-d H:i:s' );

		\update_option( self::REPORT, $stored, false );

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log(
			sprintf(
				'NextGEN Gallery: neutralized %1$d image file name(s) carrying a PHP script extension; %2$d left alone, %3$d will be retried. %4$s',
				(int) $totals[ self::RENAMED ],
				(int) $totals[ self::SKIPPED ],
				(int) $totals[ self::RETRY ],
				implode( ' | ', $details )
			)
		);
	}

	/**
	 * Resolves, in one query, which neutralized names are already taken in the page's galleries.
	 *
	 * @param array $rows Candidate rows carrying pid, galleryid and filename.
	 * @return array|null Map of "galleryid\0filename" => pid, or null when the lookup failed and
	 *                    the caller should fall back to a per-row probe.
	 */
	private static function taken_names( $rows ) {
		global $wpdb;

		$galleries = [];
		$names     = [];

		foreach ( $rows as $row ) {
			$stored = isset( $row->filename ) ? (string) $row->filename : '';

			if ( '' === $stored || \wp_basename( $stored ) !== $stored || ! Security::has_php_executable_extension( $stored ) ) {
				continue;
			}

			$new = Security::neutralize_executable_extensions( $stored );

			if ( '' === $new || $new === $stored ) {
				continue;
			}

			$galleries[ (int) $row->galleryid ] = true;
			$names[ $new ]                      = true;
		}

		if ( ! $names ) {
			return [];
		}

		$galleries = array_keys( $galleries );
		$names     = array_keys( $names );
		$table     = $wpdb->prefix . 'ngg_pictures';

		$placeholders = implode( ',', array_fill( 0, count( $galleries ), '%d' ) )
			. ') AND filename IN (' . implode( ',', array_fill( 0, count( $names ), '%s' ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$found = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Table name, and the placeholders are generated to match the argument count.
				"SELECT pid, galleryid, filename FROM `{$table}` WHERE galleryid IN ({$placeholders})",
				array_merge( $galleries, $names )
			)
		);

		// Fall back to the per-row probe, which reports its own failure, rather than reading a
		// failed lookup as "every name is free".
		if ( $wpdb->last_error ) {
			return null;
		}

		$map = [];

		foreach ( (array) $found as $row ) {
			$map[ (int) $row->galleryid . "\0" . $row->filename ] = (int) $row->pid;
		}

		return $map;
	}

	/**
	 * Whether the pictures table is actually there.
	 *
	 * @param string $table Fully-qualified table name.
	 * @return bool
	 */
	private static function table_exists( $table ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );

		// A failed probe is not an absent table, so the caller carries on and the scan's own error
		// handling decides.
		return $found === $table || (bool) $wpdb->last_error;
	}

	/**
	 * The current failure count, decayed once its window has passed.
	 *
	 * @return int
	 */
	private static function failure_count() {
		$state = \get_option( self::FAIL, [] );

		if ( ! is_array( $state ) || empty( $state['count'] ) ) {
			return 0;
		}

		if ( isset( $state['time'] ) && ( \time() - (int) $state['time'] ) > self::FAIL_TTL ) {
			\delete_option( self::FAIL );
			return 0;
		}

		return (int) $state['count'];
	}
}
