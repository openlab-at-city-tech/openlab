<?php
/**
 * Migration moving image originals out of the web-served gallery directories.
 *
 * @package Imagely\NGG\Migrations
 */

namespace Imagely\NGG\Migrations;

use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

/**
 * Relocates existing "*_backup" originals into the originals root (#1107).
 *
 * Until this runs, every original written before the relocation is still sitting beside the image
 * it belongs to, inside a web-served directory, at a path anyone can derive from a thumbnail URL
 * printed on a public gallery page. Moving them is therefore the half of #1107 that fixes the
 * sites that already have the problem; the code change only fixes the ones yet to upload.
 *
 * Runs in batches across admin requests rather than in one pass: a mature library holds tens of
 * thousands of originals, and a single pass would exceed the request time limit and leave the move
 * half-done with no record of where it stopped.
 */
class RelocateImageBackups {

	/**
	 * Marks the migration as finished.
	 */
	const DONE_OPTION = 'ngg_originals_relocated';

	/**
	 * Holds the gallery ids whose originals could not be moved.
	 *
	 * The migration is not finished while this is non-empty, and the ids are retried on the next
	 * pass. Without it a gallery whose files could not be moved - a permissions mismatch after a
	 * host migration is the realistic case - would be recorded as processed, the cursor would walk
	 * past it, and the masters would stay in the web-served directory with the migration latched
	 * as complete and nothing ever revisiting them.
	 */
	const FAILED_OPTION = 'ngg_originals_relocation_failed';

	/**
	 * How many full passes a gallery may fail before the migration gives up on it.
	 *
	 * Without a bound the retry is perpetual: a permanently unmovable original - a root-owned
	 * file left by a host migration is the realistic case - makes every pass end with failures,
	 * so the cursor rewinds forever and every admin page load re-walks the library. Giving up
	 * records the galleries in INCOMPLETE_OPTION and logs them, which is strictly better than
	 * retrying in silence.
	 */
	const MAX_PASSES = 3;

	/**
	 * Counts completed passes that ended with failures.
	 */
	const PASS_OPTION = 'ngg_originals_relocation_passes';

	/**
	 * Gallery ids whose originals could not be moved, after MAX_PASSES attempts.
	 *
	 * The migration is finished when this is set; it is the record that some masters are still in
	 * a web-served directory, so the condition is reportable rather than silent.
	 */
	const INCOMPLETE_OPTION = 'ngg_originals_relocation_incomplete';

	/**
	 * Holds the id of the last gallery processed, so the next batch resumes after it.
	 */
	const CURSOR_OPTION = 'ngg_originals_relocation_cursor';

	/**
	 * Last gallery directory name processed by the orphan-directory phase.
	 *
	 * Its own cursor, separate from CURSOR_OPTION: that one walks gallery ids, and this phase
	 * walks directory names in the gallery base, which is a different ordering over a different
	 * set. The phase runs after the id walk is exhausted.
	 *
	 * @var string
	 */
	const DIRS_CURSOR_OPTION = 'ngg_originals_relocation_dirs_cursor';

	/**
	 * Name of the directory holding originals swept out of folders with no gallery row.
	 *
	 * A sibling of the per-gallery id directories under the originals root. It cannot collide with
	 * one of those: those are named by integer id.
	 *
	 * @var string
	 */
	const ORPHAN_DIRNAME = 'orphans';

	/**
	 * Galleries processed per batch.
	 *
	 * Per gallery rather than per image: the work is dominated by the rename() calls, and a gallery
	 * is the unit that lets the sweep for originals with no image row (below) run once per
	 * directory instead of once per file.
	 */
	const BATCH_SIZE = 25;

	/**
	 * Moves the next batch of originals.
	 *
	 * @param bool $force Run even when the migration is already marked as finished.
	 * @return bool True once every gallery has been processed.
	 */
	public static function migrate( $force = false ) {
		if ( ! $force && get_option( self::DONE_OPTION ) ) {
			return true;
		}

		$storage = StorageManager::get_instance();

		// Harden the root up front: it is what every original is about to be moved into, and on a
		// host that honours .htaccess that rule is what protects them if the random path segment
		// ever leaks.
		$storage->harden_originals_root();

		$cursor    = (int) get_option( self::CURSOR_OPTION, 0 );
		$galleries = self::find_galleries_after( $cursor, self::BATCH_SIZE );
		$failed    = array_map( 'intval', (array) get_option( self::FAILED_OPTION, [] ) );

		if ( empty( $galleries ) ) {
			// A pass that left failures behind is not finished. Rewind to retry just those, and
			// only latch the migration as done once a full pass clears without any.
			if ( ! empty( $failed ) ) {
				$passes = (int) get_option( self::PASS_OPTION, 0 ) + 1;

				if ( $passes < self::MAX_PASSES ) {
					update_option( self::PASS_OPTION, $passes, false );
					update_option( self::CURSOR_OPTION, max( 0, min( $failed ) - 1 ), false );
					update_option( self::FAILED_OPTION, [], false );

					return false;
				}

				// Out of attempts. Record and log which galleries kept their originals in a
				// web-served directory, then stop - a perpetual retry would do the same work on
				// every admin request forever and tell nobody.
				update_option( self::INCOMPLETE_OPTION, array_values( $failed ), false );
				// REFUSAL_LEGACY_BACKUP, not REFUSAL_ORIGINALS_DIR: the latter is hourly-throttled
				// and the prepare_originals_dir() failures that cause a give-up have usually
				// consumed the hour's one line already, so the message that actually matters was
				// the one being dropped.
				StorageManager::get_instance()->log_path_refusal(
					sprintf(
						'NextGEN Gallery: gave up relocating image originals for gallery ids %s after %d passes; their "*_backup" masters are still in a web-served directory (#1107).',
						implode( ',', $failed ),
						self::MAX_PASSES
					),
					StorageManager::REFUSAL_LEGACY_BACKUP
				);
			}

			// The id walk only ever visits a directory some gallery row points at. A gallery
			// deleted while "delete images" was off leaves its folder, and every "*_backup" master
			// in it, exactly where it was - still at the derivable URL, with no row left to reach
			// it from. Those folders are swept once the rows are done, in batches of their own.
			if ( ! self::sweep_orphan_directories( $storage ) ) {
				return false;
			}

			update_option( self::DONE_OPTION, 1, false );
			delete_option( self::CURSOR_OPTION );
			delete_option( self::FAILED_OPTION );
			delete_option( self::PASS_OPTION );
			delete_option( self::DIRS_CURSOR_OPTION );

			// Read back rather than trusting the write: if DONE did not persist, the cursor has
			// just been deleted and the next request would re-walk the whole library from zero.
			if ( ! get_option( self::DONE_OPTION ) ) {
				StorageManager::get_instance()->log_path_refusal(
					'NextGEN Gallery: could not record the image-originals relocation as complete; it will run again.',
					StorageManager::REFUSAL_ORIGINALS_DIR
				);

				return false;
			}

			return true;
		}

		$image_mapper = ImageMapper::get_instance();

		foreach ( $galleries as $gallery_id ) {
			$ok = true;

			foreach ( $image_mapper->find_all_for_gallery( $gallery_id ) as $image ) {
				if ( ! self::relocate_one( $storage, $image ) ) {
					$ok = false;
				}
			}

			// Originals with no image row pointing at them are not reachable through the loop
			// above, and they are fetchable just the same, so the directory itself is swept.
			if ( ! self::sweep_orphans( $storage, $gallery_id ) ) {
				$ok = false;
			}

			// Deny the "*_backup" names in the gallery folder itself. Defence in depth for
			// everything the move cannot reach: a gallery given up on after MAX_PASSES, an orphan
			// in a shared or keep-location directory, a name the sweep does not recognise, and
			// siblings that reappear later from a downgrade or a files-only restore. The rule
			// persists in the file, so it keeps covering that folder after the migration finishes.
			$storage->protect_gallery_directory( $gallery_id );

			if ( ! $ok ) {
				$failed[] = (int) $gallery_id;

				// Persisted here, before the cursor moves past this gallery. The end-of-batch
				// write below is not enough on its own: a timeout or memory fatal mid-batch would
				// keep every cursor advance written so far and lose the whole failed list, so no
				// later pass would revisit these galleries, DONE would latch, and their originals
				// would stay publicly downloadable with INCOMPLETE empty.
				update_option( self::FAILED_OPTION, array_values( array_unique( $failed ) ), false );
			}

			// Advances regardless, so one unmovable gallery cannot stall the whole library; the
			// failed list is what brings it back on the next pass.
			update_option( self::CURSOR_OPTION, (int) $gallery_id, false );
		}

		update_option( self::FAILED_OPTION, array_values( array_unique( $failed ) ), false );

		return false;
	}

	/**
	 * Moves one image's original, if it is still in the legacy location.
	 *
	 * @param StorageManager $storage Storage manager.
	 * @param object         $image   Image entity.
	 * @return bool Whether a file was moved.
	 */
	private static function relocate_one( $storage, $image ) {
		$legacy = $storage->get_legacy_backup_abspath( $image );

		// Nothing in the legacy location is the success case, not a failure: most images will
		// already have been written to the new location, and treating that as a failure would
		// keep the migration retrying the whole library forever.
		if ( empty( $legacy ) || ! @file_exists( $legacy ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return true;
		}

		$target = $storage->get_backup_write_abspath( $image );

		if ( empty( $target ) ) {
			return false;
		}

		// Already moved on a previous batch that was interrupted after the rename but before the
		// cursor was written. Drop the legacy copy, which is the exposed one.
		if ( @file_exists( $target ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			// Returned, not discarded: when the legacy sibling cannot be removed the master is
			// still at the derivable URL, so this gallery has not finished migrating even though
			// the relocated copy is in place.
			return $storage->discard_legacy_backup( $image );
		}

		return self::move_file( $legacy, $target );
	}

	/**
	 * Moves "*_backup" files in a gallery directory that no image row accounts for.
	 *
	 * @param StorageManager $storage    Storage manager.
	 * @param int            $gallery_id Gallery id.
	 * @return bool False when an orphan was found and could not be moved.
	 */
	private static function sweep_orphans( $storage, $gallery_id ) {
		$gallery_abspath = $storage->get_gallery_abspath( $gallery_id );
		$originals_dir   = $storage->get_originals_abspath( $gallery_id );

		if ( empty( $gallery_abspath ) || ! @is_dir( $gallery_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			// No directory to sweep: nothing is exposed, so nothing to retry.
			return true;
		}

		$orphans = glob( \path_join( $gallery_abspath, '*_backup' ) );

		if ( empty( $orphans ) ) {
			return true;
		}

		// Never sweep a directory that is one of the shared bases rather than a gallery's own
		// folder: a "keep original location" import can point a gallery at the uploads root, where
		// a "*_backup" file may belong to something else entirely. Logged rather than passed over
		// in silence, because the files are still anonymously fetchable and this is the only
		// record that the relocation did not cover them.
		if ( ! $storage->is_gallery_directory_deletable( $gallery_abspath ) ) {
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: left %d "*_backup" original(s) in the shared directory %s for gallery #%s; they stay web-readable because the directory is not a dedicated gallery folder (#1107).',
					count( $orphans ),
					$gallery_abspath,
					$gallery_id
				),
				StorageManager::REFUSAL_LEGACY_BACKUP
			);

			return true;
		}

		if ( empty( $originals_dir )
			|| ( ! @is_dir( $originals_dir ) && ! wp_mkdir_p( $originals_dir ) ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return false;
		}

		$ok      = true;
		$skipped = [];

		foreach ( $orphans as $orphan ) {
			if ( ! @is_file( $orphan ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				continue;
			}

			// Moved when the name under the "_backup" suffix looks like an image, so a site
			// owner's unrelated "notes.txt_backup" is left alone. Deliberately a broader list
			// than is_removable_image_path(): that one is the configurable upload allow-list, so
			// it omits the legacy types a long-lived library holds (.jpe, .jfif, .tif, .bmp) and
			// omits webp entirely on a host without imagewebp(). Skipping those would leave real
			// masters anonymously fetchable, which is the bug this migration exists to fix.
			if ( ! self::looks_like_image_original( $orphan ) ) {
				$skipped[] = basename( $orphan );
				continue;
			}

			$target = \path_join( $originals_dir, basename( $orphan ) );

			if ( @file_exists( $target ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				continue;
			}

			if ( ! self::move_file( $orphan, $target ) ) {
				$ok = false;
			}
		}

		// Reported, not counted as failure: a file this never recognises would otherwise make the
		// gallery fail every pass. Naming it is what lets someone decide whether it matters.
		if ( ! empty( $skipped ) ) {
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: left %s in the web-served directory for gallery #%s; the name does not look like an image original, so it was not relocated (#1107).',
					implode( ', ', $skipped ),
					$gallery_id
				),
				StorageManager::REFUSAL_ORIGINALS_DIR
			);
		}

		return $ok;
	}

	/**
	 * Moves "*_backup" files out of gallery folders that no gallery row points at.
	 *
	 * NextGEN's gallery deletion asks whether to delete the images too, and with that off the
	 * folder stays on disk with every original in it. The row is gone, so relocate_one() and
	 * sweep_orphans() never see those files, and they keep answering the same anonymously
	 * derivable URL. This phase walks the gallery base directly instead, in batches, and skips
	 * every folder a live gallery still points at - those belong to the id walk, which resolves
	 * each original to its own gallery's keyed directory.
	 *
	 * @param StorageManager $storage Storage manager.
	 * @return bool True once every directory has been processed.
	 */
	private static function sweep_orphan_directories( $storage ) {
		$base = $storage->get_upload_abspath();

		if ( empty( $base ) || ! @is_dir( $base ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return true;
		}

		$cursor = (string) get_option( self::DIRS_CURSOR_OPTION, '' );

		// glob() returns its matches sorted, so "greater than the cursor" is a stable walk over a
		// set that does not change under us in any way that could skip an entry: a directory
		// created during the migration sorts wherever its name falls, and one created behind the
		// cursor holds nothing this phase put there.
		$all = glob( \path_join( $base, '*' ), GLOB_ONLYDIR );

		if ( empty( $all ) ) {
			return true;
		}

		$batch = [];

		foreach ( $all as $dir ) {
			if ( strcmp( basename( $dir ), $cursor ) <= 0 ) {
				continue;
			}

			$batch[] = $dir;

			if ( count( $batch ) >= self::BATCH_SIZE ) {
				break;
			}
		}

		if ( empty( $batch ) ) {
			return true;
		}

		$known = self::known_gallery_dirnames();

		foreach ( $batch as $dir ) {
			$name = basename( $dir );

			// Advanced first, and for every directory whatever happens below. A directory this
			// cannot finish must not be re-attempted forever: the deny rule written into it is
			// the standing mitigation, and the log line is what says so.
			update_option( self::DIRS_CURSOR_OPTION, $name, false );

			// A folder a gallery row still points at is the id walk's to sweep; moving its
			// originals into the orphan bucket would put them where that gallery's own code will
			// not look for them.
			if ( isset( $known[ $name ] ) ) {
				continue;
			}

			// The originals root itself, if the gallery base has been configured to sit over it.
			// Sweeping it would move relocated masters into a subdirectory of themselves.
			if ( $storage->is_path_within_originals_root( $dir ) ) {
				continue;
			}

			self::sweep_orphan_directory( $storage, $dir );
		}

		return false;
	}

	/**
	 * Moves the "*_backup" files in one row-less gallery folder.
	 *
	 * @param StorageManager $storage Storage manager.
	 * @param string         $dir     Absolute path to the folder.
	 * @return void
	 */
	private static function sweep_orphan_directory( $storage, $dir ) {
		$orphans = glob( \path_join( $dir, '*_backup' ) );

		// The guard goes in whether or not anything is there now: it is what covers a master that
		// reappears later from a files-only restore, and a name the move below does not recognise.
		// Logged on failure, because the lines below name it as this phase's standing mitigation
		// for anything the sweep cannot move, and the cursor advances either way.
		if ( ! $storage->protect_directory( $dir ) ) {
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: could not write the "*_backup" deny rule into %s, a gallery folder with no gallery row (#1107).',
					$dir
				),
				StorageManager::REFUSAL_LEGACY_BACKUP
			);
		}

		if ( empty( $orphans ) ) {
			return;
		}

		// Same boundary the per-gallery sweep applies: never treat one of the shared bases as a
		// gallery's own folder.
		if ( ! $storage->is_gallery_directory_deletable( $dir ) ) {
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: left %d "*_backup" original(s) in the shared directory %s; they stay web-readable because the directory is not a dedicated gallery folder (#1107).',
					count( $orphans ),
					$dir
				),
				StorageManager::REFUSAL_LEGACY_BACKUP
			);

			return;
		}

		$root = $storage->get_originals_root();

		if ( empty( $root ) ) {
			return;
		}

		// Keyed by the folder's own name, so two deleted galleries that held a file of the same
		// name do not land on each other.
		$target_dir = \path_join(
			\path_join( $root, self::ORPHAN_DIRNAME ),
			sanitize_file_name( basename( $dir ) )
		);

		if ( ! @is_dir( $target_dir ) && ! wp_mkdir_p( $target_dir ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			// The target path is deliberately absent from the message: it carries the originals
			// key, which is the only control on a server that ignores .htaccess, and debug.log is
			// itself web-served. REFUSAL_LEGACY_BACKUP, not REFUSAL_ORIGINALS_DIR, for the reason
			// given in migrate(): the hourly throttle on that kind is usually already spent by the
			// same unwritable-uploads condition, and this is the one failure nothing else reports -
			// the cursor has advanced, no retry is queued, and these masters stay downloadable.
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: could not create the originals directory, so %d "*_backup" original(s) stay web-readable in %s (#1107).',
					count( $orphans ),
					$dir
				),
				StorageManager::REFUSAL_LEGACY_BACKUP
			);

			return;
		}

		$skipped = [];
		$failed  = 0;

		foreach ( $orphans as $orphan ) {
			if ( ! @is_file( $orphan ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				continue;
			}

			if ( ! self::looks_like_image_original( $orphan ) ) {
				$skipped[] = basename( $orphan );
				continue;
			}

			$target = \path_join( $target_dir, basename( $orphan ) );

			if ( @file_exists( $target ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				continue;
			}

			if ( ! self::move_file( $orphan, $target ) ) {
				++$failed;
			}
		}

		if ( $failed > 0 ) {
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: could not relocate %d "*_backup" original(s) out of %s, a gallery folder with no gallery row; they stay web-readable (#1107).',
					$failed,
					$dir
				),
				StorageManager::REFUSAL_LEGACY_BACKUP
			);
		}

		if ( ! empty( $skipped ) ) {
			$storage->log_path_refusal(
				sprintf(
					'NextGEN Gallery: left %s in %s, a gallery folder with no gallery row; the name does not look like an image original, so it was not relocated (#1107).',
					implode( ', ', $skipped ),
					$dir
				),
				StorageManager::REFUSAL_LEGACY_BACKUP
			);
		}
	}

	/**
	 * Directory names in the gallery base that a gallery row still points at.
	 *
	 * The stored path column rather than get_gallery_abspath() per row: resolving an abspath
	 * hydrates the gallery entity, and the whole point of the id walk's bounded query is not to
	 * load the library into memory. The basename is enough to decide ownership, because gallery
	 * folder names are unique within the base - NextGEN de-duplicates them on creation.
	 *
	 * @return array<string,true> Keyed by directory name.
	 */
	private static function known_gallery_dirnames() {
		global $wpdb;

		$table = $wpdb->prefix . 'ngg_gallery';

		// phpcs:disable WordPress.DB.DirectDatabaseQuery -- No caching layer for a one-off migration sweep.
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is built from $wpdb->prefix.
		$paths = $wpdb->get_col( "SELECT path FROM {$table}" );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery

		$names = [];

		foreach ( (array) $paths as $path ) {
			$name = basename( untrailingslashit( wp_normalize_path( (string) $path ) ) );

			if ( '' !== $name ) {
				$names[ $name ] = true;
			}
		}

		return $names;
	}

	/**
	 * Whether a "*_backup" filename is an image original this migration should relocate.
	 *
	 * Matches on the extension underneath the suffix against a fixed list of raster formats
	 * NextGEN has accepted at any point, rather than the configurable upload allow-list, for the
	 * reason given at the call site.
	 *
	 * @param string $path Absolute path ending in "_backup".
	 * @return bool
	 */
	private static function looks_like_image_original( $path ) {
		$base = preg_replace( '#_backup$#', '', basename( (string) $path ) );

		$extension = strtolower( (string) pathinfo( $base, PATHINFO_EXTENSION ) );

		return in_array(
			$extension,
			[ 'jpg', 'jpeg', 'jpe', 'jfif', 'png', 'gif', 'webp', 'tif', 'tiff', 'bmp', 'avif', 'heic', 'heif' ],
			true
		);
	}

	/**
	 * Moves a file, preferring a rename so that no readable copy is left behind.
	 *
	 * A copy followed by a failed unlink would leave the master readable at its old web-served
	 * path while the code had already started reading the new one, which is the one outcome worth
	 * going out of the way to avoid: nobody would notice, because everything would work. So on any
	 * failure after the copy has written something, the new file is removed and the original is
	 * left where it was - still exposed, but retried on the next pass - and the reason is logged.
	 * That applies to a part-written copy as much as to a failed unlink: a truncated file at the
	 * target reads as a finished move to both callers.
	 *
	 * @param string $from Source path.
	 * @param string $to   Destination path.
	 * @return bool
	 */
	private static function move_file( $from, $to ) {
		// A sidecar left behind by an earlier killed worker. Removed before either strategy runs,
		// so it cannot accumulate in the originals directory (where it would also stop
		// delete_originals_directory() from removing the folder) or be confused for anything.
		@unlink( $to . '.part' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink

		if ( @rename( $from, $to ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.rename_rename
			return true;
		}

		// rename() fails across filesystem boundaries, which a wp-content mounted separately from
		// the uploads directory can produce, so fall back to copy and unlink.
		//
		// Copied to a sidecar and renamed into place, never written straight onto the target. A
		// direct copy that is interrupted rather than failed - the worker killed by an FPM
		// request_terminate_timeout, a hard timeout or the OOM killer, which a batch with no time
		// budget invites - leaves a truncated file at the final path with no error for anyone to
		// check. relocate_one() reads an existing target as a finished move and unlinks the real
		// master, and resolve_backup_abspath() is already serving the truncated file by then. A
		// rename within one directory is atomic, so the target either does not exist or is the
		// whole file.
		$part = $to . '.part';

		if ( ! @copy( $from, $part ) || ! @rename( $part, $to ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.rename_rename
			// The sidecar is ours by construction, whatever state it is in, so it always goes.
			@unlink( $part ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink

			// The target is only ours to remove while the source is still there. "This function
			// created whatever is at the target" holds only under an exclusive lock, and two
			// concurrent batches can reach the same pair: if the other request renamed this file a
			// moment ago, the calls above failed because the source is gone, and the file now at
			// the target is its completed move. Unlinking it there would destroy the master.
			if ( @file_exists( $from ) && @file_exists( $to ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@unlink( $to ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			}

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'NextGEN Gallery: could not relocate the original at ' . $from . ' (copy failed); it stays where it is.' );

			return false;
		}

		if ( @unlink( $from ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			return true;
		}

		@unlink( $to ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'NextGEN Gallery: could not relocate the original at ' . $from . ' (it is not removable); it stays where it is and remains web-readable.' );

		return false;
	}

	/**
	 * Returns the next batch of gallery ids.
	 *
	 * @param int $after Only galleries with a higher id.
	 * @param int $limit Maximum number of ids.
	 * @return int[]
	 */
	private static function find_galleries_after( $after, $limit ) {
		global $wpdb;

		// Ids only, bounded in SQL. find_all() hydrates every gallery entity in the library on
		// every batch, which on a few thousand galleries is the whole table loaded per request to
		// then discard all but 25 of them.
		$table = $wpdb->prefix . 'ngg_gallery';

		// phpcs:disable WordPress.DB.DirectDatabaseQuery -- No caching layer for a one-off migration sweep.
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is built from $wpdb->prefix.
				"SELECT gid FROM {$table} WHERE gid > %d ORDER BY gid ASC LIMIT %d",
				(int) $after,
				(int) $limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery

		return array_map( 'intval', (array) $ids );
	}
}
