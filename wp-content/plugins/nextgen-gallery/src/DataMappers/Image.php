<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataTypes\Gallery as GalleryType;
use Imagely\NGG\DataTypes\Image as ImageType;

use Imagely\NGG\DataMapper\TableDriver;
use Imagely\NGG\Display\I18N;
use Imagely\NGG\Util\Transient;

/**
 * Image data mapper class.
 *
 * Handles database operations for image entities, including CRUD operations
 * and image-specific functionality.
 */
class Image extends TableDriver {

	/**
	 * Seconds to wait for the sortorder seeding lock before giving up on it.
	 *
	 * @var int
	 */
	const SORTORDER_LOCK_TIMEOUT = 5;

	/**
	 * Transient prefix throttling the seeding diagnostics on sites without WP_DEBUG.
	 *
	 * @var string
	 */
	const SORTORDER_LOG_THROTTLE_PREFIX = 'ngg_sortorder_notice_';

	/**
	 * This request's next sortorder per gallery, with the row it was derived from.
	 *
	 * Keyed by table and gallery so a switch_to_blog() mid-request keeps blogs apart.
	 *
	 * @var array<string, array{next: int, pid: int}>
	 */
	private static $sortorder_memo = [];

	/**
	 * Singleton instance of the Image mapper.
	 *
	 * @var Image|\Imagely\NGGPro\Commerce\DataMappers\Image|null
	 */
	private static $instance = null;

	/**
	 * The model class this mapper handles.
	 *
	 * @var string
	 */
	public $model_class = 'Imagely\NGG\DataTypes\Image';

	/**
	 * The primary key column name.
	 *
	 * @var string
	 */
	public $primary_key_column = 'pid';

	/**
	 * Custom post name for legacy compatibility.
	 *
	 * @var string
	 */
	public $custom_post_name = 'mixin_nextgen_table_extras';

	/**
	 * WP object cache group.
	 *
	 * @var string
	 */
	protected $object_cache_group = 'ngg_images';

	/**
	 * TTL in seconds for WP object cache entries.
	 *
	 * @var int
	 */
	protected $object_cache_ttl = HOUR_IN_SECONDS;

	/**
	 * Constructor.
	 *
	 * Defines the database table structure and initializes the mapper.
	 */
	public function __construct() {
		$this->define_column( 'alttext', 'TEXT' );
		$this->define_column( 'description', 'TEXT' );
		$this->define_column( 'exclude', 'INT', 0 );
		$this->define_column( 'filename', 'VARCHAR(255)' );
		$this->define_column( 'galleryid', 'BIGINT', 0 );
		$this->define_column( 'image_slug', 'VARCHAR(255)' );
		$this->define_column( 'imagedate', 'DATETIME' );
		$this->define_column( 'meta_data', 'TEXT' );
		$this->define_column( 'pid', 'BIGINT', 0 );
		$this->define_column( 'post_id', 'BIGINT', 0 );
		$this->define_column( 'sortorder', 'BIGINT', 0 );
		$this->define_column( 'updated_at', 'BIGINT' );
		$this->define_column( 'extras_post_id', 'BIGINT', 0 );

		$this->add_serialized_column( 'meta_data' );

		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$this->define_column( 'pricelist_id', 'BIGINT', 0, true );
		}

		parent::__construct( 'ngg_pictures' );
	}

	/**
	 * Gets the singleton instance of the Image mapper.
	 *
	 * @return Image|\Imagely\NGGPro\Commerce\DataMappers\Image The Image mapper instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			$class          = apply_filters( 'ngg_datamapper_client_image', __CLASS__ );
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * Finds an image by ID or entity.
	 *
	 * @param int|ImageType $entity The image ID or image entity to find.
	 * @return ImageType The image entity.
	 */
	public function find( $entity ) {
		/**
		 * Image result.
		 *
		 * @var ImageType $result
		 */
		$result = parent::find( $entity );
		return $result;
	}

	/**
	 * Finds all images for a gallery.
	 *
	 * @param GalleryType $gallery The gallery entity or gallery ID.
	 * @param bool        $model   Whether to return model objects. Default true.
	 *
	 * @return ImageType[] Array of image entities.
	 */
	public function find_all_for_gallery( $gallery, $model = true ) {
		$retval     = [];
		$gallery_id = 0;

		if ( is_object( $gallery ) ) {
			if ( isset( $gallery->id_field ) ) {
				$gallery_id = $gallery->{$gallery->id_field};
			} else {
				$key = $this->get_primary_key_column();
				if ( isset( $gallery->$key ) ) {
					$gallery_id = $gallery->$key;
				}
			}
		} elseif ( is_numeric( $gallery ) ) {
			$gallery_id = $gallery;
		}

		if ( $gallery_id ) {
			$retval = $this->select()->where( [ 'galleryid = %s', $gallery_id ] )->run_query( false, $model );
		}

		return $retval;
	}

	/**
	 * Reimports metadata for an image.
	 *
	 * @param int|ImageType $image_or_id The image ID or image entity.
	 * @return mixed The result of the save operation.
	 */
	public function reimport_metadata( $image_or_id ) {
		if ( is_int( $image_or_id ) ) {
			$image = $this->find( $image_or_id );
		} else {
			$image = $image_or_id;
		}

		// Reset all image details that would have normally been imported.
		if ( is_array( $image->meta_data ) ) {
			unset( $image->meta_data['saved'] );
		}
		\nggAdmin::import_MetaData( $image );

		return $this->save( $image );
	}

	/**
	 * Gets the ID of an image entity.
	 *
	 * @param ImageType $image The image entity.
	 * @return bool|int The image ID or false if not found.
	 */
	public function get_id( $image ) {
		$retval = false;

		if ( $image instanceof \stdClass ) {
			if ( isset( $image->id_field ) ) {
				$retval = $image->{$image->id_field};
			}
		} else {
			$retval = $image->id();
		}

		// If we still don't have an id then we find the primary key and try fetching it manually.
		if ( ! $retval ) {
			$key    = $this->get_primary_key_column();
			$retval = $image->$key;
		}

		return $retval;
	}

	/**
	 * Destroys an image entity.
	 *
	 * @param ImageType $entity The image entity to destroy.
	 * @return bool True if the image was successfully destroyed, false otherwise.
	 */
	public function destroy( $entity ) {
		$retval = parent::destroy( $entity );

		// Delete tag associations with the image.
		if ( ! is_numeric( $entity ) ) {
			$entity = $entity->{$entity->id_field};
		}

		\wp_delete_object_term_relationships( $entity, 'ngg_tag' );

		Transient::flush( 'displayed_gallery_rendering' );
		return $retval;
	}

	/**
	 * Saves an image entity to the database.
	 *
	 * @param ImageType $entity The image entity to save.
	 * @return bool|TableDriver The result of the save operation.
	 */
	public function save_entity( $entity ) {
		$entity->updated_at = time();

		// Seed sortorder on new images so appended uploads land at the end of the
		// gallery instead of joining a tied group at sortorder 0. Only fires for a
		// fresh insert (no pid) that has a gallery and no explicit order set, so
		// drag-drop moves and imports that assign their own order are untouched.
		$lock   = null;
		$seeded = false;

		if ( empty( $entity->pid ) && empty( $entity->sortorder ) && ! empty( $entity->galleryid ) ) {
			$gallery_id = (int) $entity->galleryid;

			// Reading the gallery's last position and writing the new row have to be one step
			// against another insert: the uploader sends one request per file with several in
			// flight, so otherwise two of them read the same value and both keep it. This
			// serialises inserts against each other only - the reorder endpoints and the legacy
			// sort screen write the same column without taking this lock, so an upload racing a
			// drag or a renumber is still not serialised against it.
			$lock      = $this->lock_sortorder_seed( $gallery_id );
			$sortorder = $this->get_next_sortorder( $gallery_id );

			// A failed read is indistinguishable from an empty gallery, and both would seed 1 -
			// which on a populated gallery ties the new image to its first row and moves it to
			// the front, the symptom this seeding exists to prevent. Leaving the default alone
			// is the smaller wrong, and get_next_sortorder() has already logged why.
			if ( null !== $sortorder ) {
				$entity->sortorder = $sortorder;
				$seeded            = true;
			}
		}

		try {
			$retval = parent::save_entity( $entity );
		} finally {
			if ( null !== $lock ) {
				$this->release_sortorder_seed( $lock );
			}
		}

		if ( $retval && $seeded ) {
			$this->remember_sortorder( (int) $entity->galleryid, (int) $entity->sortorder, (int) $this->get_id( $entity ) );
		}

		if ( $retval ) {
			include_once NGGALLERY_ABSPATH . '/admin/functions.php';
			$image_id = $this->get_id( $entity );
			if ( ! isset( $entity->meta_data['saved'] ) ) {
				\nggAdmin::import_MetaData( $image_id );
			}
			Transient::flush( 'displayed_gallery_rendering' );
		}
		return $retval;
	}

	/**
	 * Returns the next sortorder value for a gallery, appending after its last row.
	 *
	 * @param int $gallery_id The gallery to compute the next sortorder for.
	 * @return int|null The next sortorder value (1 for an empty gallery), or null when the
	 *                  gallery's current order could not be read.
	 */
	protected function get_next_sortorder( $gallery_id ) {
		$carried = $this->carried_sortorder( $gallery_id );

		if ( null !== $carried ) {
			return $carried;
		}

		$wpdb  = $this->_wpdb();
		$table = $this->get_table_name();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$stats = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) AS ngg_count, MAX(`sortorder`) AS ngg_max FROM `{$table}` WHERE `galleryid` = %d",
				$gallery_id
			)
		);

		// Columns of our own, and the row has to carry them, because wpdb hands back the previous
		// statement's result on the paths where query() returns before flushing - and the lock
		// taken just above leaves a one-column row holding 1 behind. Read as a bare value that
		// was indistinguishable from "this gallery's last position is 1", which then seeded 2
		// onto a populated gallery and carried on from there. A row without these columns is not
		// an answer, and neither is no row at all. Seeding 1 onto a populated gallery would tie
		// the new image to its first row and move it to the front, so not seeding is the smaller
		// wrong.
		if ( ! is_object( $stats ) || ! property_exists( $stats, 'ngg_count' ) || ! $wpdb->ready || ! empty( $wpdb->last_error ) ) {
			$this->log_sortorder_notice(
				'could not read a gallery\'s image order, so the new image keeps the default position',
				'read',
				$wpdb->last_error
			);
			return null;
		}

		// An empty gallery is the one legitimate reason for a null maximum.
		if ( 0 === (int) $stats->ngg_count ) {
			return 1;
		}

		return (int) $stats->ngg_max + 1;
	}

	/**
	 * This request's next sortorder for a gallery, when it is provably still current.
	 *
	 * The sortorder column is not indexed on the images table, and galleryid is indexed only on
	 * installs whose table was created rather than upgraded, so MAX(sortorder) reads the whole
	 * gallery or the whole table. Paying that per image would make a single-request bulk import - a folder
	 * or zip import, the Lightroom plugin - markedly slower than reading it once, which is what
	 * this request already did for its own last insert. That counter is still correct as long as
	 * nothing has been inserted since the row it came from, and the primary key answers that for
	 * free. Deliberately conservative: an insert into any gallery sends us back to the table.
	 *
	 * @param int $gallery_id The gallery being seeded.
	 * @return int|null The carried value, or null when it has to be re-read.
	 */
	private function carried_sortorder( $gallery_id ) {
		$wpdb  = $this->_wpdb();
		$table = $this->get_table_name();
		$key   = $table . '|' . $gallery_id;

		if ( ! isset( self::$sortorder_memo[ $key ] ) ) {
			return null;
		}

		// EXISTS so the answer is always exactly one row carrying a column of our own. "No newer
		// row" is the answer that keeps the carried value, so a read that did not actually run
		// must not be able to look like one - and on the paths where wpdb returns before
		// flushing, what is left behind is the previous statement's result.
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$probe = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT EXISTS( SELECT 1 FROM `{$table}` WHERE `pid` > %d LIMIT 1 ) AS ngg_newer",
				self::$sortorder_memo[ $key ]['pid']
			)
		);

		if ( ! is_object( $probe ) || ! property_exists( $probe, 'ngg_newer' ) || ! $wpdb->ready || ! empty( $wpdb->last_error ) ) {
			return null;
		}

		if ( 0 !== (int) $probe->ngg_newer ) {
			return null;
		}

		return self::$sortorder_memo[ $key ]['next'];
	}

	/**
	 * Records the position this request just wrote, so the next insert can carry it forward.
	 *
	 * @param int $gallery_id The gallery that was written to.
	 * @param int $sortorder  The position the new row received.
	 * @param int $pid        The id of the row that was created.
	 * @return void
	 */
	private function remember_sortorder( $gallery_id, $sortorder, $pid ) {
		$key = $this->get_table_name() . '|' . $gallery_id;

		// Without the new row's id there is nothing to prove the counter is current against.
		if ( $pid <= 0 ) {
			unset( self::$sortorder_memo[ $key ] );
			return;
		}

		self::$sortorder_memo[ $key ] = [
			'next' => $sortorder + 1,
			'pid'  => $pid,
		];
	}

	/**
	 * Takes a short advisory lock covering sortorder seeding for one gallery.
	 *
	 * Keyed to the gallery, so imports into different galleries never wait on each
	 * other, and to the database and table, so two installs sharing a MySQL server do
	 * not either. Returns null when the lock was not granted, which leaves seeding
	 * exactly as unserialized as it was before rather than refusing the upload.
	 *
	 * @param int $gallery_id The gallery being seeded.
	 * @return string|null The lock name to release, or null when no lock is held.
	 */
	protected function lock_sortorder_seed( $gallery_id ) {
		$wpdb = $this->_wpdb();
		$name = 'ngg_so_' . md5( $wpdb->dbname . '|' . $this->get_table_name() . '|' . (int) $gallery_id );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$granted = $wpdb->get_var(
			$wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $name, self::SORTORDER_LOCK_TIMEOUT )
		);

		if ( 1 === (int) $granted ) {
			return $name;
		}

		// Both answers end the same way - seeding runs unserialised and two uploads can share a
		// position - so both are reported, in separate throttle buckets so neither hides the
		// other. NULL means the lock is unavailable rather than busy: a database that carries no
		// session locks, or one reached through a proxy that routes them elsewhere. A 0 is the
		// timeout, and at this budget that is not ordinary contention either, because the section
		// it guards is one read and one insert.
		if ( null === $granted || ! $wpdb->ready || ! empty( $wpdb->last_error ) ) {
			$this->log_sortorder_notice(
				'the image-ordering lock is unavailable on this database, so simultaneous uploads to one gallery may still share a position',
				'lock_unavailable',
				$wpdb->last_error
			);
		} else {
			$this->log_sortorder_notice(
				sprintf(
					'waited %ds for the image-ordering lock on one gallery and gave up, so that upload was not serialised against the others',
					self::SORTORDER_LOCK_TIMEOUT
				),
				'lock_timeout'
			);
		}

		return null;
	}

	/**
	 * Releases the advisory lock taken by lock_sortorder_seed().
	 *
	 * @param string $name The lock name returned by lock_sortorder_seed().
	 * @return void
	 */
	protected function release_sortorder_seed( $name ) {
		$wpdb = $this->_wpdb();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$released = $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $name ) );

		// 1 is the only healthy answer, and the unhealthy ones are worth a line precisely because
		// they should be unreachable: anything else means this connection no longer held the lock
		// it had taken. wpdb reconnects a dropped link without telling the caller, and the new
		// session holds nothing - so the insert ran unserialised while this code believed it was
		// protected, and this is the only trace of it.
		if ( 1 !== (int) $released ) {
			$this->log_sortorder_notice(
				'the image-ordering lock was lost before the image was written, so that insert was not serialised',
				'release',
				$wpdb->last_error
			);
		}
	}

	/**
	 * Logs a seeding problem, throttled off-debug.
	 *
	 * Every one of these sits on the image-insert path, so an install where the condition is
	 * permanent - no session locks, a restricted grant - would append a line per uploaded image.
	 * WP_DEBUG logs every occurrence; without it one line per kind per hour is enough to find the
	 * problem without growing the log.
	 *
	 * @param string $summary What went wrong, in a form support can search for.
	 * @param string $kind    Throttle bucket: read, lock or release.
	 * @param string $error   The database error, when there was one.
	 * @return void
	 */
	private function log_sortorder_notice( $summary, $kind, $error = '' ) {
		$message = 'NextGEN Gallery: ' . $summary;

		if ( ! empty( $error ) ) {
			$message .= ' (' . $error . ')';
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $message );
			return;
		}

		$throttle = self::SORTORDER_LOG_THROTTLE_PREFIX . $kind;

		if ( get_transient( $throttle ) ) {
			return;
		}

		// Claimed only when it is true. Transients are unavailable on some installs, and the
		// database failure behind a 'read' notice is itself a reason the write below fails, so a
		// line promising an hour's silence while logging every occurrence would be the more
		// misleading of the two.
		$throttled = set_transient( $throttle, 1, HOUR_IN_SECONDS );

		if ( $throttled ) {
			$message .= ' [further notices of this kind suppressed for one hour - enable WP_DEBUG to log every occurrence]';
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $message );
	}

	/**
	 * Gets the post title for an image entity.
	 *
	 * @param ImageType $entity The image entity.
	 * @return string The post title (alttext).
	 */
	public function get_post_title( $entity ) {
		return $entity->alttext;
	}

	/**
	 * Sets default values for an image entity.
	 *
	 * @param ImageType $entity The image entity to set defaults for.
	 */
	public function set_defaults( $entity ) {
		$this->set_default_value( $entity, 'post_id', 0 );
		$this->set_default_value( $entity, 'exclude', 0 );
		$this->set_default_value( $entity, 'sortorder', 0 );

		$this->set_default_value( $entity, 'description', '' );
		$this->set_default_value( $entity, 'alttext', '' );

		if ( ( ! isset( $entity->imagedate ) ) || $entity->imagedate == '0000-00-00 00:00:00' ) {
			$entity->imagedate = current_time( 'mysql' );
		}

		// If a filename is set and no 'alttext' is set; then set the 'alttext' to the basename of the filename.
		if ( isset( $entity->filename ) ) {
			$path_parts = I18N::mb_pathinfo( $entity->filename );
			$alttext    = ( ! isset( $path_parts['filename'] ) ) ?
				substr( $path_parts['basename'], 0, strpos( $path_parts['basename'], '.' ) )
				:
				$path_parts['filename'];
			$this->set_default_value( $entity, 'alttext', $alttext );
		}

		if ( ! empty( $entity->alttext ) && empty( $entity->image_slug ) ) {
			$entity->image_slug = \nggdb::get_unique_slug( \sanitize_title_with_dashes( $entity->alttext ), 'image' );
		}

		// Ensure that the exclude parameter is an integer or boolean-evaluated value.
		if ( is_string( $entity->exclude ) ) {
			$entity->exclude = intval( $entity->exclude );
		}

		$entity->description = trim( $entity->description );
		$entity->alttext     = trim( $entity->alttext );

		if ( ! is_admin() && ! empty( $entity->{$entity->id_field} ) ) {
			if ( ! empty( $entity->description ) ) {
				$entity->description = I18N::translate( $entity->description, 'pic_' . $entity->{$entity->id_field} . '_description' );
			}
			if ( ! empty( $entity->alttext ) ) {
				$entity->alttext = I18N::translate( $entity->alttext, 'pic_' . $entity->{$entity->id_field} . '_alttext' );
			}
		}
	}

	/**
	 * Unserializes a value.
	 *
	 * @param string $value The serialized value to unserialize.
	 * @return mixed|null The unserialized value or null on failure.
	 * @throws \Exception When unserialization fails.
	 * @deprecated This method is deprecated and should not be used.
	 * @todo Remove this when the minimum Pro API level is 4.0 or higher.
	 */
	public function unserialize( string $value ) {
		return \Imagely\NGG\Util\Serializable::unserialize( $value );
	}
}
