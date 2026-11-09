<?php

namespace Imagely\NGG\DataStorage;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;

use Imagely\NGG\DataTypes\{ Gallery, Image, LegacyThumbnail };
use Imagely\NGG\Display\I18N;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\IGW\EventPublisher;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\{ Filesystem, Router, Sanitization, Security };

/**
 * Data storage manager.
 */
class Manager {

	/**
	 * Instance cache.
	 *
	 * @var Manager|null
	 */
	public static $instance = null;

	/**
	 * Gallery mapper instance.
	 *
	 * @var object
	 */
	protected $gallery_mapper;

	/**
	 * Image mapper instance.
	 *
	 * @var object
	 */
	protected $image_mapper;

	/**
	 * Transient key prefix that rate-limits refusal logging when WP_DEBUG is off.
	 *
	 * One pair of keys per refusal kind - see log_path_refusal(). The kinds are the constants
	 * below, so the key space is bounded by the number of call sites, not by anything a request
	 * can influence.
	 */
	const REFUSAL_LOG_THROTTLE_PREFIX = 'ngg_refusal_logged_';

	/**
	 * Refusal kinds. Each throttles independently, so a frequent benign one cannot consume the
	 * hour's single line and hide a rare serious one.
	 */
	const REFUSAL_STORED_PATH     = 'stored_path';
	const REFUSAL_CLONE_WRITE     = 'clone_write';
	const REFUSAL_GENERATE_SIZE   = 'generate_size';
	const REFUSAL_GENERATED_CLONE = 'generated_clone';
	const REFUSAL_TEMPLATE_PATH   = 'template_path';
	const REFUSAL_TRIGGER_HANDLER = 'trigger_handler';
	const REFUSAL_GALLERY_OUTSIDE = 'gallery_outside_locations';
	const REFUSAL_MASS_ASSIGNMENT = 'mass_assignment';
	const REFUSAL_DIRS_DEGENERATE = 'deletion_dirs_degenerate';
	const REFUSAL_URL_UNMAPPED    = 'url_unmapped';
	const REFUSAL_ATTACH_IMAGE    = 'attach_image';

	/**
	 * Refusal text shared by both copies of generate_image_size(), so the mirrored pair cannot
	 * drift. Takes the basename.
	 */
	const REFUSAL_PHP_EXTENSION_NOTICE = 'the stored file name "%s" carries a PHP script extension. Migrations\NeutralizeStoredExecutableFilenames renames these when the plugin upgrades, so a refusal that persists means that rename was declined - usually because the neutralized name is already taken in the same gallery - and the file needs renaming in the gallery folder by hand';

	/**
	 * The refusal kinds that are rate-limited when WP_DEBUG is off.
	 *
	 * Only the front-end hot paths belong here: those run once per image per request, over the
	 * unauthenticated /nextgen-image/ URL, so an ungated line would grow the log on every visitor
	 * request. Everything else - a refused mass assignment is an authenticated, low-frequency
	 * XML-RPC or Lightroom edit - is logged every time, because for those the log is the only
	 * channel that reaches anybody and one line per hour would hide the 2nd..Nth refusal.
	 */
	const THROTTLED_REFUSAL_KINDS = [
		self::REFUSAL_STORED_PATH,
		self::REFUSAL_CLONE_WRITE,
		self::REFUSAL_GENERATE_SIZE,
		self::REFUSAL_GENERATED_CLONE,
		self::REFUSAL_TEMPLATE_PATH,
		self::REFUSAL_TRIGGER_HANDLER,
		self::REFUSAL_GALLERY_OUTSIDE,
		self::REFUSAL_DIRS_DEGENERATE,
		self::REFUSAL_URL_UNMAPPED,
	];

	/**
	 * Extensions a generated derivative may never carry, whatever the source was: anything the
	 * server may execute, plus the markup and config types that are dangerous to serve from a
	 * gallery directory. See is_safe_generated_image_path().
	 */
	const UNSAFE_GENERATED_EXTENSIONS = [
		'php',
		'php3',
		'php4',
		'php5',
		'php6',
		'php7',
		'php8',
		'phps',
		'pht',
		'phtm',
		'phtml',
		'phar',
		'shtml',
		'shtm',
		'cgi',
		'pl',
		'py',
		'rb',
		'sh',
		'bash',
		'asp',
		'aspx',
		'jsp',
		'jspx',
		'htaccess',
		'htpasswd',
		'ini',
		'html',
		'htm',
		'svg',
		'svgz',
		'xml',
		'xhtml',
		'js',
		'exe',
		'so',
	];

	/**
	 * Image extensions a generated derivative may carry even when the (conditional, filterable)
	 * upload allow-list does not list them. See is_safe_generated_image_path().
	 */
	const GENERATED_IMAGE_EXTENSIONS = [
		'jpg',
		'jpeg',
		'jpe',
		'jfif',
		'png',
		'gif',
		'webp',
		'avif',
		'bmp',
		'tif',
		'tiff',
		'ico',
		'heic',
		'heif',
	];

	/**
	 * Basenames of the entries the extension allow-list refused during the most recent
	 * extract_zip() call, so upload_zip() can report what was dropped.
	 *
	 * @var string[]
	 */
	protected $skipped_zip_entries = [];

	/**
	 * Image mapper instance (deprecated).
	 *
	 * @deprecated
	 * @var object
	 */
	public $_image_mapper;

	/**
	 * Object instance (deprecated).
	 *
	 * @deprecated
	 * @var object
	 */
	public $object;

	/**
	 * Gallery absolute path cache.
	 *
	 * @var array
	 */
	protected static $gallery_abspath_cache = [];

	/**
	 * Image absolute path cache.
	 *
	 * @var array
	 */
	protected static $image_abspath_cache = [];

	/**
	 * Canonicalized deletion-boundary directories (gallery location anchors and protected
	 * directories), keyed by blog id. These are install constants for the current site, so they are
	 * resolved once instead of on every per-size deletion check.
	 *
	 * @var array
	 */
	protected static $deletion_dirs_cache = [];

	/**
	 * Image URL cache.
	 *
	 * @var array
	 */
	protected static $image_url_cache = [];

	/**
	 * Directories whose public URL is known, canonicalized and keyed by the set of directories
	 * they were resolved from, so the realpath() walk runs once per configuration rather than
	 * once per image per size.
	 *
	 * @var array
	 */
	protected static $url_anchors_cache = [];

	public function __construct() {
		$this->gallery_mapper = GalleryMapper::get_instance();
		$this->image_mapper   = ImageMapper::get_instance();

		/**
		 * TODO comment for Imagify compatibility fix.
		 *
		 * @TODO Remove in a later release - this fixes an issue with Imagify at the time of 3.50's release.
		 */
		$this->object        = $this;
		$this->_image_mapper = $this->image_mapper;
	}

	/**
	 * Gets an instance of the manager.
	 *
	 * @return Manager
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Manager();
		}
		return self::$instance;
	}

	public function has_method( $name ) {
		return method_exists( $this, $name );
	}

	/**
	 * Magic method for delegating calls.
	 *
	 * @TODO: Remove this 'magic' method so that our code is always understandable without needing deep context
	 * @param string $method
	 * @param array  $args
	 * @return mixed
	 * @throws \BadMethodCallException When neither this class nor the image helpers define the method.
	 */
	public function __call( $method, $args ) {
		if ( preg_match( '/^get_(\w+)_(abspath|url|dimensions|html|size_params)$/', $method, $match ) ) {
			if ( isset( $match[1] ) && isset( $match[2] ) && ! method_exists( $this, $method ) ) {
				$method = 'get_image_' . $match[2];
				$args[] = $match[1];
				return $this->$method( $args );
			}
		}

		// Calling a method this class does not have would only re-enter __call() until PHP runs
		// out of stack, killing the request instead of reporting the bad call.
		if ( ! method_exists( $this, $method ) ) {
			throw new \BadMethodCallException( \sprintf( 'Call to undefined method %s::%s()', __CLASS__, esc_html( $method ) ) );
		}

		return $this->$method( $args );
	}

	/**
	 * Remove after Pro attains level 1 compatibility with the POPE removal
	 */
	public function get_wrapped_instance() {
		return $this;
	}

	/**
	 * Remove after Pro attains level 1 compatibility with the POPE removal
	 */
	public function add_mixin( $unused = '' ) {}

	/**
	 * Backs up an image file
	 *
	 * @param int|object $image
	 * @param bool       $save
	 * @return bool
	 */
	public function backup_image( $image, $save = true ) {
		$retval     = false;
		$image_path = $this->get_image_abspath( $image );

		if ( $image_path && @file_exists( $image_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$retval = copy( $image_path, $this->get_backup_abspath( $image ) );

			// Store the dimensions of the image.
			if ( function_exists( 'getimagesize' ) ) {
				$mapper = ImageMapper::get_instance();
				if ( ! is_object( $image ) ) {
					$image = $mapper->find( $image );
				}
				if ( $image ) {
					if ( empty( $image->meta_data ) || ! is_array( $image->meta_data ) ) {
						$image->meta_data = [];
					}
					$dimensions                 = getimagesize( $image_path );
					$image->meta_data['backup'] = [
						'filename'  => basename( $image_path ),
						'width'     => $dimensions[0],
						'height'    => $dimensions[1],
						'generated' => microtime(),
					];
					if ( $save ) {
						$mapper->save( $image );
					}
				}
			}
		}

		return $retval;
	}

	/**
	 * Extracts a zip file.
	 *
	 * Entries whose extension is not in the upload allow-list are not extracted. Their names are
	 * collected in $skipped so the caller can tell the uploader what was dropped: the gate is
	 * silent here, and both callers report the extraction as a success, so without this a zip
	 * holding e.g. .tif or .bmp alongside jpegs imports partially with no message at all.
	 *
	 * @param string        $zipfile
	 * @param string        $dest_path
	 * @param string[]|null $skipped Out: basenames of the entries the allow-list refused.
	 * @return bool false on failure
	 */
	public function extract_zip( $zipfile, $dest_path, &$skipped = null ) {
		wp_mkdir_p( $dest_path );

		/*
		 * Every entry the extension gate turns away is recorded rather than dropped.
		 * The gate returned true for every filename until the loop-variable shadowing in
		 * is_allowed_image_extension() was repaired, so these `continue`s were dead code;
		 * live, they silently discard entries -- a `.webp` archive on a host without GD
		 * WebP support (nggallery.php gates that extension on imagewebp()) extracts
		 * nothing at all and still reported success. upload_zip() puts this list on its
		 * response -- see its docblock for what does and does not reach the uploader.
		 */
		$this->reset_skipped_zip_entries();
		$skipped = [];

		if ( class_exists( 'ZipArchive', false ) && apply_filters( 'unzip_file_use_ziparchive', true ) ) {
			$zipObj = new \ZipArchive();
			if ( $zipObj->open( $zipfile ) === false ) {
				return false;
			}

			for ( $i = 0; $i < $zipObj->numFiles; $i++ ) {
				$filename = $zipObj->getNameIndex( $i );

				/*
				 * Directory entries are entries like any other and carry no extension, so
				 * without this they are refused and reported: every ZIP made by compressing
				 * a folder would name its own directory as a rejected file type. The PclZip
				 * branch below has always skipped them via $zipItem['folder'].
				 */
				if ( $this->is_zip_directory_entry( $filename ) ) {
					continue;
				}

				/*
				 * A dot-prefixed path segment defeats the glob() size check in upload_zip()
				 * that decides where the archive lands: glob() does not match dotfiles, so a
				 * dotfile-only (or dot-directory) payload counts as zero bytes and the whole
				 * archive is re-extracted under a public wp-content/uploads path (see #962).
				 * This also covers macOS AppleDouble forks (__MACOSX/._photo.jpg). Testing
				 * every segment, not just basename(), catches a dot-directory like
				 * .photos/img.jpg. Skip silently, as the #962 fix and the PclZip branch below
				 * do; routing these to the refusal channel would mislabel resource forks as
				 * "not an accepted image format".
				 */
				foreach ( explode( '/', $filename ) as $zip_path_segment ) {
					if ( strpos( $zip_path_segment, '.' ) === 0 ) {
						continue 2;
					}
				}

				// extractTo() collapses traversal itself, but core guards both of its own
				// branches this way and an entry shaped like one is worth naming.
				if ( 0 !== validate_file( $filename ) ) {
					$this->collect_skipped_zip_entry( $filename );
					continue;
				}

				if ( ! $this->is_allowed_image_extension( $filename ) ) {
					$this->collect_skipped_zip_entry( $filename );
					continue;
				}
				$zipObj->extractTo( $dest_path, [ $zipObj->getNameIndex( $i ) ] );
			}
		} else {
			require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
			$zipObj           = new \PclZip( $zipfile );
			$zipContent       = $zipObj->listContent();
			$indexesToExtract = [];

			foreach ( $zipContent as $zipItem ) {
				if ( $zipItem['folder'] ) {
					continue;
				}
				$basename = basename( $zipItem['stored_filename'] );

				// Reject hidden (dot) files and files without an allowed image extension.
				if ( strpos( $basename, '.' ) === 0 ) {
					continue;
				}

				// PclZip reduces no traversal of its own, so an entry named "../x.jpg" would
				// be written above $dest_path. The extension gate does not catch it.
				if ( 0 !== validate_file( $zipItem['stored_filename'] ) ) {
					$this->collect_skipped_zip_entry( $zipItem['stored_filename'] );
					continue;
				}

				if ( ! $this->is_allowed_image_extension( $zipItem['stored_filename'] ) ) {
					$this->collect_skipped_zip_entry( $zipItem['stored_filename'] );
					continue;
				}
				$indexesToExtract[] = $zipItem['index'];
			}

			$skipped = $this->get_skipped_zip_entries();

			/*
			 * An empty index list must not reach extractByIndex(): on PHP 7.4, which
			 * readme.txt still declares supported, '' is read as index 0 and that entry is
			 * extracted whatever its extension. $skipped is assigned above first, so the
			 * caller still learns why nothing came out of the archive.
			 */
			if ( ! $indexesToExtract ) {
				$this->log_extraction_refusals( $zipfile );
				return false;
			}

			/*
			 * Option form, not the two-argument shorthand: the restriction is only parsed when
			 * the first variadic argument is an option constant. Second guard behind the
			 * per-entry check above, so a name that slips past it still cannot escape.
			 */
			$extracted = $zipObj->extractByIndex(
				implode( ',', $indexesToExtract ),
				PCLZIP_OPT_PATH,
				$dest_path,
				PCLZIP_OPT_EXTRACT_DIR_RESTRICTION,
				$dest_path
			);

			if ( ! $extracted ) {
				return false;
			}
		}

		$skipped = $this->get_skipped_zip_entries();
		$this->log_extraction_refusals( $zipfile );

		return true;
	}

	/**
	 * Records one archive entry the extension gate refused, so upload_zip() can name it.
	 *
	 * Accumulated on this singleton rather than through a by-reference argument because the
	 * legacy C_Gallery_Storage twin reaches extract_zip() through the pope mixin dispatcher,
	 * which forwards arguments with call_user_func_array() and so drops references.
	 *
	 * MERGE NOTE (#932 + #933): #933 named these methods and #932 wrote the body; both are
	 * kept, on one store. The body is #932's because #933's recorded basenames and skipped
	 * every dot-prefixed entry, which drops the two cases worth reporting - two archives can
	 * carry the same basename in different folders, and ".shell.php" is precisely the shape
	 * of entry this gate exists to refuse, so it must be named rather than filtered out as
	 * archiver noise. Directory entries and real archiver bookkeeping are still skipped, by
	 * name rather than by leading dot.
	 *
	 * @param string $filename Entry name as stored in the archive.
	 * @return void
	 */
	public function collect_skipped_zip_entry( $filename ) {
		$filename = is_string( $filename ) ? $filename : '';

		if ( '' === $filename || in_array( $filename, $this->skipped_zip_entries, true ) ) {
			return;
		}

		if ( $this->is_zip_directory_entry( $filename ) ) {
			return;
		}

		// Nothing an archiver adds by itself is worth telling the uploader about. A
		// dotfile that is not on this list is still reported: ".shell.php" is the
		// shape of the report the extension gate exists to refuse.
		if ( $this->is_zip_metadata_entry( $filename ) ) {
			return;
		}

		$this->skipped_zip_entries[] = $filename;
	}

	/**
	 * Clears the record of refused zip entries. Called when extraction starts.
	 *
	 * @return void
	 */
	public function reset_skipped_zip_entries() {
		$this->skipped_zip_entries = [];
	}

	/**
	 * Returns the entries the extension allow-list refused during the most recent extraction.
	 *
	 * @return string[] Entry names, empty when every entry was extracted.
	 */
	public function get_skipped_zip_entries() {
		return $this->skipped_zip_entries;
	}

	/**
	 * Whether an archive entry name denotes a directory rather than a file.
	 *
	 * @param string $filename Entry name as stored in the archive.
	 * @return bool
	 */
	protected function is_zip_directory_entry( $filename ) {
		return is_string( $filename ) && '' !== $filename && in_array( substr( $filename, -1 ), [ '/', '\\' ], true );
	}

	/**
	 * Whether an archive entry is bookkeeping added by an archiver or file manager.
	 *
	 * These were never importable in the first place: import_gallery_from_fs() skips
	 * "__MACOSX" and dot-prefixed directories when it walks the extracted tree. So they
	 * must not be reported to the uploader as refused files either.
	 *
	 * @param string $filename Entry name as stored in the archive.
	 * @return bool
	 */
	protected function is_zip_metadata_entry( $filename ) {
		$segments = explode( '/', str_replace( '\\', '/', (string) $filename ) );
		$basename = (string) array_pop( $segments );

		foreach ( $segments as $segment ) {
			if ( '__MACOSX' === strtoupper( $segment ) ) {
				return true;
			}
		}

		return in_array(
			strtolower( $basename ),
			[ '.ds_store', 'thumbs.db', 'desktop.ini', '.directory' ],
			true
		);
	}

	/**
	 * Logs the entries refused during the most recent extraction, once per archive.
	 *
	 * @param string $zipfile Archive that was extracted.
	 * @return void
	 */
	public function log_extraction_refusals( $zipfile ) {
		if ( ! $this->skipped_zip_entries ) {
			return;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log(
				sprintf(
					'NextGEN Gallery: %1$d entr%2$s in "%3$s" were not extracted, their file type is not an allowed image type: %4$s',
					count( $this->skipped_zip_entries ),
					1 === count( $this->skipped_zip_entries ) ? 'y' : 'ies',
					wp_basename( (string) $zipfile ),
					// Entry names come from the archive and may carry CR/LF.
					wp_strip_all_tags( implode( ', ', $this->skipped_zip_entries ), true )
				)
			);
		}
	}

	/**
	 * Emits a diagnostic for a path the containment or extension checks refused.
	 *
	 * Callers of the refusing methods only see null/false, so without a log line a refused row
	 * renders as a blank slot with nothing for support to search for. An ungated error_log() is
	 * not the answer either: /nextgen-image/ is one request per thumbnail, so a single refused
	 * row - or a repeated attack request - would append a line on every visitor request.
	 *
	 * So: with WP_DEBUG on, every occurrence is logged, which is the debugging channel. With it
	 * off, at most one line per hour is logged, bounded by one transient holding a single flag
	 * whose expiry IS the rate limit. That is enough for a mass refusal - a gallery whose stored
	 * path sits outside the recognised gallery locations refuses every size of every image - to be
	 * discoverable on a production site, without the log growth that made the ungated version
	 * unacceptable.
	 *
	 * @param string $message Already-composed log line.
	 * @return void
	 */
	public function log_path_refusal( $message, $kind = self::REFUSAL_STORED_PATH ) {
		$kind = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $kind ) );

		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ! in_array( $kind, self::THROTTLED_REFUSAL_KINDS, true ) ) {
			error_log( $message );
			return;
		}

		// Throttled per kind, not globally. A single broken image row refused on every front-end
		// render would otherwise take the hour's only line every time and hide a rarer, more
		// serious refusal - an attempted clone write outside the source directory - for the whole
		// window. Nothing is counted or persisted on the suppressed path: it is the hot path, and a
		// count written there would turn every visitor request into a wp_options write while being
		// unreportable anyway, since the line that would carry it is exactly the line the throttle
		// is suppressing.
		$throttle_key = self::REFUSAL_LOG_THROTTLE_PREFIX . $kind;

		if ( get_transient( $throttle_key ) ) {
			return;
		}

		set_transient( $throttle_key, 1, HOUR_IN_SECONDS );

		error_log( $message . sprintf( ' [further "%s" refusals suppressed for one hour - enable WP_DEBUG to log every occurrence]', $kind ) );
	}

	/**
	 * Gets the id of a gallery, regardless of whether an integer or object was passed as an argument
	 *
	 * @param mixed $gallery_obj_or_id
	 * @return null|int
	 */
	public function get_gallery_id( $gallery_obj_or_id ) {
		$retval      = null;
		$gallery_key = $this->gallery_mapper->get_primary_key_column();

		if ( is_object( $gallery_obj_or_id ) ) {
			if ( isset( $gallery_obj_or_id->$gallery_key ) ) {
				$retval = $gallery_obj_or_id->$gallery_key;
			}
		} elseif ( is_numeric( $gallery_obj_or_id ) ) {
			$retval = $gallery_obj_or_id;
		}

		return $retval;
	}

	/**
	 * Empties the gallery cache directory of content
	 *
	 * @param object $gallery
	 */
	public function flush_cache( $gallery ) {
		$fs = Filesystem::get_instance();
		$fs->flush_directory( $this->get_cache_abspath( $gallery ) );
	}

	/**
	 * Flushes the cache for the gallery that contains the given image.
	 *
	 * @param int|object $image Image ID or image object.
	 */
	public function flush_image_cache( $image ) {
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( ! $image ) {
			return;
		}

		$this->flush_cache( $image->galleryid );
	}

	/**
	 * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
	 *
	 * @param object|int $image Image ID or an image object
	 * @param string     $size
	 * @param array      $params
	 * @param bool       $skip_defaults
	 * @return bool|array
	 */
	public function calculate_image_size_dimensions( $image, $size, $params = null, $skip_defaults = false ) {
		$retval = false;

		// Get the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have a valid image.
		if ( $image ) {
			$params = $this->get_image_size_params( $image, $size, $params, $skip_defaults );

			// Get the image filename.
			$image_path = $this->get_image_abspath( $image, 'full', true );
			$clone_path = $this->get_image_abspath( $image, $size );

			$retval = $this->calculate_image_clone_dimensions( $image_path, $clone_path, $params );
		}

		return $retval;
	}

	/**
	 * Generates a "clone" for an existing image, the clone can be altered using the $params array
	 *
	 * @param string $image_path
	 * @param string $clone_path
	 * @param array  $params
	 * @return null|object
	 */
	public function generate_image_clone( $image_path, $clone_path, $params ) {
		$crop       = isset( $params['crop'] ) ? $params['crop'] : null;
		$watermark  = isset( $params['watermark'] ) ? $params['watermark'] : null;
		$reflection = isset( $params['reflection'] ) ? $params['reflection'] : null;
		$rotation   = isset( $params['rotation'] ) ? $params['rotation'] : null;
		$flip       = isset( $params['flip'] ) ? $params['flip'] : '';
		$destpath   = null;
		$thumbnail  = null;

		$result = $this->calculate_image_clone_result( $image_path, $clone_path, $params );

		// XXX this should maybe be removed and extra settings go into $params?
		$settings = apply_filters( 'ngg_settings_during_image_generation', Settings::get_instance()->to_array() );

		// Ensure we have a valid image.
		if ( $image_path && @file_exists( $image_path ) && null != $result && ! isset( $result['error'] ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$image_dir    = dirname( $image_path );
			$clone_path   = $result['clone_path'];
			$clone_dir    = $result['clone_directory'];
			$clone_format = $result['clone_format'];
			$format_list  = $this->get_image_format_list();

			// Refuse to write before anything is written. Every caller clones either in place or
			// into a subdirectory of the source image's directory (thumbs/, cache/) - which is the
			// same invariant the clone_dir creation below already assumes - so a clone path that
			// escapes that directory, or that is not an image file, means the path was influenced
			// by a stored filename rather than computed. Checking here rather than after the save
			// means a poisoned path never reaches the filesystem.
			// Bounded by the source image's own directory - the boundary this function's clone_dir
			// handling already assumes - rather than by the gallery record, so the base is a value
			// this code computed rather than one read from a writable column.
			if ( ! $this->is_path_contained( $image_dir, $clone_path )
				|| ! $this->is_safe_generated_image_path( $clone_path, $image_path ) ) {
				$this->log_path_refusal(
					sprintf(
						'NextGEN Gallery: refused to write image clone to "%s" - outside the source image directory (%s) or not an allowed image type',
						$clone_path,
						$image_dir
					),
					self::REFUSAL_CLONE_WRITE
				);
				return null;
			}

			// Ensure target directory exists, but only create 1 subdirectory.
			if ( ! @file_exists( $clone_dir ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( strtolower( realpath( $image_dir ) ) != strtolower( realpath( $clone_dir ) ) ) {
					if ( strtolower( realpath( $image_dir ) ) == strtolower( realpath( dirname( $clone_dir ) ) ) ) {
						wp_mkdir_p( $clone_dir );
					}
				}
			}

			$method  = $result['method'];
			$width   = $result['width'];
			$height  = $result['height'];
			$quality = $result['quality'];

			if ( null === $quality ) {
				$quality = 100;
			}

			// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
			if ( $method == 'wordpress' ) {
				$original = wp_get_image_editor( $image_path );
				$destpath = $clone_path;
				if ( ! is_wp_error( $original ) ) {
					$original->resize( $width, $height, $crop );
					$original->set_quality( $quality );
					$original->save( $clone_path );
				}
			} elseif ( $method == 'nextgen' ) {
				$destpath  = $clone_path;
				$thumbnail = new LegacyThumbnail( $image_path, true );
				if ( ! $thumbnail->error ) {
					if ( $crop ) {
						$crop_area   = $result['crop_area'];
						$crop_x      = $crop_area['x'];
						$crop_y      = $crop_area['y'];
						$crop_width  = $crop_area['width'];
						$crop_height = $crop_area['height'];

						$thumbnail->crop( $crop_x, $crop_y, $crop_width, $crop_height );
					}

					$thumbnail->resize( $width, $height );
				} else {
					$thumbnail = null;
				}
			}

			// We successfully generated the thumbnail.
			if ( is_string( $destpath ) && ( @file_exists( $destpath ) || $thumbnail != null ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( $clone_format != null ) {
					if ( isset( $format_list[ $clone_format ] ) ) {
						$clone_format_extension     = $format_list[ $clone_format ];
						$clone_format_extension_str = null;

						if ( $clone_format_extension != null ) {
							$clone_format_extension_str = '.' . $clone_format_extension;
						}

						$destpath_info      = I18N::mb_pathinfo( $destpath );
						$destpath_extension = $destpath_info['extension'];

						if ( strtolower( $destpath_extension ) != strtolower( $clone_format_extension ) ) {
							$destpath_dir      = $destpath_info['dirname'];
							$destpath_basename = $destpath_info['filename'];
							$destpath_new      = $destpath_dir . DIRECTORY_SEPARATOR . $destpath_basename . $clone_format_extension_str;

							if ( ( @file_exists( $destpath ) && rename( $destpath, $destpath_new ) ) || $thumbnail != null ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename, WordPress.PHP.NoSilencedErrors.Discouraged
								$destpath = $destpath_new;
							}
						}
					}
				}

				if ( is_null( $thumbnail ) ) {
					$thumbnail = new LegacyThumbnail( $destpath, true );

					if ( $thumbnail->error ) {
						$thumbnail = null;

						return null;
					}
				} else {
					$thumbnail->fileName = $destpath;
				}

				// This is quite odd, when watermark equals int(0) it seems all statements below ($watermark == 'image') and ($watermark == 'text') both evaluate as true
				// so we set it at null if it evaluates to any null-like value.
				if ( null === $watermark ) {
					$watermark = null;
				}

				if ( 1 == $watermark || true === $watermark ) {
					$watermark_setting_keys = [
						'wmFont',
						'wmType',
						'wmPos',
						'wmXpos',
						'wmYpos',
						'wmPath',
						'wmText',
						'wmOpaque',
						'wmFont',
						'wmSize',
						'wmColor',
					];
					foreach ( $watermark_setting_keys as $watermark_key ) {
						if ( ! isset( $params[ $watermark_key ] ) ) {
							$params[ $watermark_key ] = $settings[ $watermark_key ];
						}
					}

					if ( in_array( strval( $params['wmType'] ), [ 'image', 'text' ], true ) ) {
						$watermark = $params['wmType'];
					} else {
						$watermark = 'text';
					}
				}

				$watermark = strval( $watermark );

				if ( $watermark == 'image' ) {
					$thumbnail->watermarkImgPath = $params['wmPath'];
					$thumbnail->watermarkImage( $params['wmPos'], $params['wmXpos'], $params['wmYpos'] );
				} elseif ( $watermark == 'text' ) {
					$thumbnail->watermarkText = $params['wmText'];
					$thumbnail->watermarkCreateText( $params['wmColor'], $params['wmFont'], $params['wmSize'], $params['wmOpaque'] );
					$thumbnail->watermarkImage( $params['wmPos'], $params['wmXpos'], $params['wmYpos'] );
				}

				if ( $rotation && in_array( abs( $rotation ), [ 90, 180, 270 ], true ) ) {
					$thumbnail->rotateImageAngle( $rotation );
				}

				$flip = strtolower( $flip );

				if ( $flip && in_array( $flip, [ 'h', 'v', 'hv' ], true ) ) {
					$flip_h = in_array( $flip, [ 'h', 'hv' ], true );
					$flip_v = in_array( $flip, [ 'v', 'hv' ], true );

					$thumbnail->flipImage( $flip_h, $flip_v );
				}

				if ( $reflection ) {
					$thumbnail->createReflection( 40, 40, 50, false, '#a4a4a4' );
				}

				// Force format.
				if ( $clone_format != null && isset( $format_list[ $clone_format ] ) ) {
					$thumbnail->format = strtoupper( $format_list[ $clone_format ] );
				}

				$thumbnail = apply_filters( 'ngg_before_save_thumbnail', $thumbnail );

				// Always retrieve metadata from the backup when possible.
				$backup_path  = $image_path . '_backup';
				$exif_abspath = @file_exists( $backup_path ) ? $backup_path : $image_path; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

				$exif_iptc = EXIFWriter::read_metadata( $exif_abspath );

				// LegacyThumbnail::$error is sticky - checkMemoryForData() can latch it during
				// resize() (called above, before any of this) whether or not this specific
				// save() write goes on to succeed, and save() has no way to tell the two apart
				// on its own. Only trust "not saved" as this call's own failure when nothing had
				// already set the flag before we got here.
				$errored_before_save = $thumbnail->error;
				$saved               = $thumbnail->save( $destpath, $quality );

				if ( ! $saved && ! $errored_before_save ) {
					// A failure that appeared during this save() call - a genuine write failure
					// (e.g. a disk-full ImageJpeg()/ImagePng() call) - don't report an object
					// whose file was never actually written as a successfully generated clone.
					$thumbnail->destruct();
					$thumbnail = null;
				} else {
					// Either it saved fine, or the flag predates this call and the file it wrote
					// (unresized/unrotated, but real) is still on disk either way.
					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					@EXIFWriter::write_metadata( $destpath, $exif_iptc );
				}
			}
		}

		return $thumbnail;
	}

	/**
	 * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
	 *
	 * @param string $image_path
	 * @param string $clone_path
	 * @param array  $params
	 * @return null|array
	 */
	public function calculate_image_clone_dimensions( $image_path, $clone_path, $params ) {
		$retval = null;
		$result = $this->calculate_image_clone_result( $image_path, $clone_path, $params );

		if ( $result != null ) {
			$retval = [
				'width'       => $result['width'],
				'height'      => $result['height'],
				'real_width'  => $result['real_width'],
				'real_height' => $result['real_height'],
			];
		}

		return $retval;
	}

	/**
	 * Returns an array of properties of a resulting clone image if and when generated
	 *
	 * @param string $image_path
	 * @param string $clone_path
	 * @param array  $params
	 * @return null|array
	 */
	public function calculate_image_clone_result( $image_path, $clone_path, $params ) {
		$width      = isset( $params['width'] ) ? $params['width'] : null;
		$height     = isset( $params['height'] ) ? $params['height'] : null;
		$quality    = isset( $params['quality'] ) ? $params['quality'] : null;
		$type       = isset( $params['type'] ) ? $params['type'] : null;
		$crop       = isset( $params['crop'] ) ? $params['crop'] : null;
		$watermark  = isset( $params['watermark'] ) ? $params['watermark'] : null;
		$rotation   = isset( $params['rotation'] ) ? $params['rotation'] : null;
		$reflection = isset( $params['reflection'] ) ? $params['reflection'] : null;
		$crop_frame = isset( $params['crop_frame'] ) ? $params['crop_frame'] : null;
		$result     = null;

		// Ensure we have a valid image.
		if ( $image_path && @file_exists( $image_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			// Ensure target directory exists, but only create 1 subdirectory.
			$image_dir           = dirname( $image_path );
			$clone_dir           = dirname( $clone_path );
			$image_extension     = I18N::mb_pathinfo( $image_path, PATHINFO_EXTENSION );
			$image_extension_str = null;
			$clone_extension     = I18N::mb_pathinfo( $clone_path, PATHINFO_EXTENSION );
			$clone_extension_str = null;

			if ( $image_extension != null ) {
				$image_extension_str = '.' . $image_extension;
			}

			if ( $clone_extension != null ) {
				$clone_extension_str = '.' . $clone_extension;
			}

			$image_basename = I18N::mb_basename( $image_path );
			$clone_basename = I18N::mb_basename( $clone_path );
			// We use a default suffix as passing in null as the suffix will make WordPress use a default.
			$clone_suffix = null;
			$format_list  = $this->get_image_format_list();
			$clone_format = null; // format is determined below and based on $type otherwise left to null.

			// suffix is only used to reconstruct paths for image_resize function.
			if ( strpos( $clone_basename, $image_basename ) === 0 ) {
				$clone_suffix = substr( $clone_basename, strlen( $image_basename ) );
			}

			if ( $clone_suffix != null && $clone_suffix[0] == '-' ) {
				// WordPress adds '-' on its own.
				$clone_suffix = substr( $clone_suffix, 1 );
			}

			// Get original image dimensions.
			$dimensions = getimagesize( $image_path );

			if ( $width == null && $height == null ) {
				if ( $dimensions != null ) {

					if ( $width == null ) {
						$width = $dimensions[0];
					}

					if ( $height == null ) {
						$height = $dimensions[1];
					}
				} else {
					// XXX Don't think there's any other option here but to fail miserably...use some hard-coded defaults maybe?
					return null;
				}
			}

			if ( $dimensions != null ) {
				$dimensions_ratio = $dimensions[0] / $dimensions[1];

				if ( $width == null ) {
					$width = (int) round( $height * $dimensions_ratio );

					if ( $width == ( $dimensions[0] - 1 ) ) {
						$width = $dimensions[0];
					}
				} elseif ( $height == null ) {
					$height = (int) round( $width / $dimensions_ratio );

					if ( $height == ( $dimensions[1] - 1 ) ) {
						$height = $dimensions[1];
					}
				}

				if ( $width > $dimensions[0] ) {
					$width = $dimensions[0];
				}

				if ( $height > $dimensions[1] ) {
					$height = $dimensions[1];
				}

				$image_format = $dimensions[2];

				if ( $type != null ) {
					if ( is_string( $type ) ) {
						$type = strtolower( $type );

						// Indexes in the $format_list array correspond to IMAGETYPE_XXX values appropriately.
					// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						$index = array_search( $type, $format_list );
						if ( $index !== false ) {
							$type = $index;

							if ( $type != $image_format ) {
								// Note: this only changes the FORMAT of the image but not the extension.
								$clone_format = $type;
							}
						}
					}
				}
			}

			if ( $width == null || $height == null ) {
				// Something went wrong...
				return null;
			}

			// We now need to estimate the 'quality' or level of compression applied to the original JPEG: *IF* the
			// original image has a quality lower than the $quality parameter we will end up generating a new image
			// that is MUCH larger than the original. 'Quality' as an EXIF or IPTC property is quite unreliable
			// and not all software honors or treats it the same way. This calculation is simple: just compare the size
			// that our image could become to what it currently is. '3' is important here as JPEG uses 3 bytes per pixel.
			//
			// First we attempt to use ImageMagick if we can; it has a more robust method of calculation.
			// Note: Some hosting environments have ImageMagick installed but without JPEG support,
			// which causes "NoDecodeDelegateForThisImageFormat" errors. This code handles that gracefully.
			if ( ! empty( $dimensions['mime'] ) && $dimensions['mime'] == 'image/jpeg' ) {
				$possible_quality = null;
				$try_image_magick = true;

				if ( ( defined( 'NGG_DISABLE_IMAGICK' ) && NGG_DISABLE_IMAGICK )
				|| ( function_exists( 'is_wpe' ) && ( $dimensions[0] >= 8000 || $dimensions[1] >= 8000 ) )
				|| ! apply_filters( 'ngg_use_imagick_for_quality', true ) ) {
					$try_image_magick = false;
				}

				if ( $try_image_magick && extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
					// Check if ImageMagick supports JPEG before attempting to use it
					if ( $this->imagick_supports_jpeg() ) {
						try {
							$img = new \Imagick( $image_path );
							if ( method_exists( $img, 'getImageCompressionQuality' ) ) {
								$possible_quality = $img->getImageCompressionQuality();
							}
							// Clean up the Imagick object
							if ( isset( $img ) ) {
								$img->clear();
								$img->destroy();
							}
						} catch ( \ImagickException $e ) {
							// ImageMagick doesn't support this image format, fall back to GD calculation
							error_log( 'NextGEN Gallery: ImageMagick JPEG support not available, falling back to GD: ' . $e->getMessage() );
							$try_image_magick = false;
						} catch ( \Exception $e ) {
							// Any other ImageMagick error, fall back to GD calculation
							error_log( 'NextGEN Gallery: ImageMagick error, falling back to GD: ' . $e->getMessage() );
							$try_image_magick = false;
						}
					} else {
						// ImageMagick doesn't support JPEG, skip to GD calculation
						$try_image_magick = false;
					}
				}

				// ImageMagick wasn't available or quality is zero so we guess it from the dimensions and filesize.
				if ( null === $possible_quality || 0 === $possible_quality ) {
					$filesize         = filesize( $image_path );
					$possible_quality = ( 101 - ( ( $width * $height ) * 3 ) / $filesize );
					$possible_quality = (int) $possible_quality;

					// An estimate at or below zero isn't a meaningful quality signal (e.g. a HiDPI
					// clone with far more pixels than the source filesize implies) — treat it as
					// unusable and keep the caller-supplied quality instead of degrading the image
					// to near-blank output. See #302.
					$possible_quality = ( $possible_quality <= 0 ) ? null : min( 100, $possible_quality );
				}

				if ( $possible_quality !== null && $possible_quality < $quality ) {
					$quality = $possible_quality;
				}
			}

			$result['clone_path']      = $clone_path;
			$result['clone_directory'] = $clone_dir;
			$result['clone_suffix']    = $clone_suffix;
			$result['clone_format']    = $clone_format;
			$result['base_width']      = $dimensions[0];
			$result['base_height']     = $dimensions[1];

			// image_resize() has limitations:
			// - no easy crop frame support
			// - fails if the dimensions are unchanged
			// - doesn't support filename prefix, only suffix so names like thumbs_original_name.jpg for $clone_path are not supported
			// also suffix cannot be null as that will make WordPress use a default suffix...we could use an object that returns empty string from __toString() but for now just fallback to ngg generator.
		// phpcs:ignore Generic.CodeAnalysis.UnconditionalIfStatement.Found
			if ( false ) {
				// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
				$result['method'] = 'wordpress';
				$new_dims         = image_resize_dimensions( $dimensions[0], $dimensions[1], $width, $height, $crop );

				if ( $new_dims ) {
					list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $new_dims;

					$width  = $dst_w;
					$height = $dst_h;
				} else {
					$result['error'] = new \WP_Error( 'error_getting_dimensions', __( 'Could not calculate resized image dimensions', 'nggallery' ) );
				}
			} else {
				$result['method'] = 'nextgen';
				$original_width   = $dimensions[0];
				$original_height  = $dimensions[1];
				$aspect_ratio     = $width / $height;

				$orig_ratio_x = $original_width / $width;
				$orig_ratio_y = $original_height / $height;

				if ( $crop ) {
					$algo = 'shrink'; // either 'adapt' or 'shrink'.

					if ( $crop_frame != null ) {
						$crop_x            = (int) round( $crop_frame['x'] );
						$crop_y            = (int) round( $crop_frame['y'] );
						$crop_width        = (int) round( $crop_frame['width'] );
						$crop_height       = (int) round( $crop_frame['height'] );
						$crop_final_width  = (int) round( $crop_frame['final_width'] );
						$crop_final_height = (int) round( $crop_frame['final_height'] );

						$crop_width_orig  = $crop_width;
						$crop_height_orig = $crop_height;

						$crop_factor_x = $crop_width / $crop_final_width;
						$crop_factor_y = $crop_height / $crop_final_height;

						$crop_ratio_x = $crop_width / $width;
						$crop_ratio_y = $crop_height / $height;

						// XXX not sure about this...don't use for now
						// The 'adapt' algorithm is not implemented yet.
						if ( $algo == 'shrink' ) {
							if ( $crop_ratio_x < $crop_ratio_y ) {
								$crop_width  = max( $crop_width, $width );
								$crop_height = (int) round( $crop_width / $aspect_ratio );
							} else {
								$crop_height = max( $crop_height, $height );
								$crop_width  = (int) round( $crop_height * $aspect_ratio );
							}

							if ( $crop_width == ( $crop_width_orig - 1 ) ) {
								$crop_width = $crop_width_orig;
							}

							if ( $crop_height == ( $crop_height_orig - 1 ) ) {
								$crop_height = $crop_height_orig;
							}
						}

						$crop_diff_x = (int) round( ( $crop_width_orig - $crop_width ) / 2 );
						$crop_diff_y = (int) round( ( $crop_height_orig - $crop_height ) / 2 );

						$crop_x += $crop_diff_x;
						$crop_y += $crop_diff_y;

						$crop_max_x = ( $crop_x + $crop_width );
						$crop_max_y = ( $crop_y + $crop_height );

						// Check if we're overflowing borders.
						//
						if ( $crop_x < 0 ) {
							$crop_x = 0;
						} elseif ( $crop_max_x > $original_width ) {
							$crop_x -= ( $crop_max_x - $original_width );
						}

						if ( $crop_y < 0 ) {
							$crop_y = 0;
						} elseif ( $crop_max_y > $original_height ) {
							$crop_y -= ( $crop_max_y - $original_height );
						}
					} else {
						if ( $orig_ratio_x < $orig_ratio_y ) {
							$crop_width  = $original_width;
							$crop_height = (int) round( $height * $orig_ratio_x );

						} else {
							$crop_height = $original_height;
							$crop_width  = (int) round( $width * $orig_ratio_y );
						}

						if ( $crop_width == ( $width - 1 ) ) {
							$crop_width = $width;
						}

						if ( $crop_height == ( $height - 1 ) ) {
							$crop_height = $height;
						}

						$crop_x = (int) round( ( $original_width - $crop_width ) / 2 );
						$crop_y = (int) round( ( $original_height - $crop_height ) / 2 );
					}

					$result['crop_area'] = [
						'x'      => $crop_x,
						'y'      => $crop_y,
						'width'  => $crop_width,
						'height' => $crop_height,
					];
				} else {
					// Just constraint dimensions to ensure there's no stretching or deformations.
					list($width, $height) = wp_constrain_dimensions( $original_width, $original_height, $width, $height );
				}
			}

			$result['width']   = $width;
			$result['height']  = $height;
			$result['quality'] = $quality;

			$real_width  = $width;
			$real_height = $height;

			if ( $rotation && in_array( abs( $rotation ), [ 90, 270 ], true ) ) {
				$real_width  = $height;
				$real_height = $width;
			}

			if ( $reflection ) {
				// default for nextgen was 40%, this is used in generate_image_clone as well.
				$reflection_amount = 40;
				// Note, round() would probably be best here but using the same code that LegacyThumbnail uses for compatibility.
				$reflection_height = intval( $real_height * ( $reflection_amount / 100 ) );
				$real_height       = $real_height + $reflection_height;
			}

			$result['real_width']  = $real_width;
			$result['real_height'] = $real_height;
		}

		return $result;
	}

	/**
	 * Resizes the full-size image in place to the configured maximum dimensions.
	 *
	 * @param Image $image
	 * @param bool  $save Whether to persist the resulting dimension metadata.
	 * @return bool False only when a needed resize failed to write.
	 */
	public function generate_resized_image( $image, $save = true ) {
		$image_abspath = $this->get_image_abspath( $image, 'full' );
		$params        = $this->get_image_size_params( $image, 'full' );

		// Re-encoding at unchanged dimensions only costs JPEG quality, so leave the pixels untouched.
		if ( ! $this->is_resize_needed( $image_abspath, $params ) ) {
			$this->sync_unresized_image_metadata( $image_abspath );
			if ( $save ) {
				$this->update_image_dimension_metadata( $image, $image_abspath );
			}
			return true;
		}

		$generated = $this->generate_image_clone( $image_abspath, $image_abspath, $params );

		if ( $generated && $save ) {
			$this->update_image_dimension_metadata( $image, $image_abspath );
		}

		if ( $generated ) {
			$generated->destruct();
		}

		return (bool) $generated;
	}

	/**
	 * Whether cloning an image with the given params would change it.
	 *
	 * The clone never upscales, so an image already within the requested bounds only changes when
	 * another transform is requested alongside the resize, or a JPEG quality below the source's own.
	 *
	 * @param string $image_abspath
	 * @param array  $params
	 * @return bool
	 */
	public function is_resize_needed( $image_abspath, $params ) {
		foreach ( [ 'crop', 'crop_frame', 'watermark', 'reflection', 'rotation', 'flip', 'type' ] as $transform ) {
			if ( ! empty( $params[ $transform ] ) ) {
				return true;
			}
		}

		$dimensions = $image_abspath ? @getimagesize( $image_abspath ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! $dimensions ) {
			return true;
		}

		$width  = empty( $params['width'] ) ? 0 : (int) $params['width'];
		$height = empty( $params['height'] ) ? 0 : (int) $params['height'];

		if ( ( $width && $dimensions[0] > $width ) || ( $height && $dimensions[1] > $height ) ) {
			return true;
		}

		// Only JPEG saves honour the quality setting. The clone encodes at min( requested, estimated
		// source quality ), so a re-encode changes compression only when the requested quality is lower.
		$requested_quality = isset( $params['quality'] ) ? (int) $params['quality'] : 100;
		if ( $requested_quality < 100 && isset( $dimensions['mime'] ) && 'image/jpeg' === $dimensions['mime'] ) {
			$probe          = $this->calculate_image_clone_result( $image_abspath, $image_abspath, array_merge( $params, [ 'quality' => 100 ] ) );
			$source_quality = ( $probe && isset( $probe['quality'] ) ) ? (int) $probe['quality'] : 100;

			return $requested_quality < $source_quality;
		}

		return false;
	}

	/**
	 * Applies the metadata step of generate_image_clone() to an image whose re-encode was skipped.
	 *
	 * The clone always rewrote EXIF/IPTC from the backup and forced Orientation to 1, which is the
	 * only correction orientations correct_exif_rotation() leaves alone (mirrored/transposed) get.
	 * PEL rewrites only the metadata segments, so the compressed image data is kept as-is.
	 *
	 * @param string $image_abspath
	 */
	public function sync_unresized_image_metadata( $image_abspath ) {
		if ( ! $image_abspath || ! EXIFWriter::is_jpeg_file( $image_abspath ) ) {
			return;
		}

		$backup_path  = $image_abspath . '_backup';
		$exif_abspath = @file_exists( $backup_path ) ? $backup_path : $image_abspath; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@EXIFWriter::write_metadata( $image_abspath, EXIFWriter::read_metadata( $exif_abspath ) );
		clearstatcache( true, $image_abspath );
	}

	public function update_image_dimension_metadata( $image, $image_abspath ) {
		// Ensure that fullsize dimensions are added to metadata array.
		$dimensions = getimagesize( $image_abspath );
		$full_meta  = [
			'width'  => $dimensions[0],
			'height' => $dimensions[1],
			'md5'    => $this->get_image_checksum( $image, 'full' ),
		];

		if ( ! isset( $image->meta_data ) || ( is_string( $image->meta_data ) && strlen( $image->meta_data ) == 0 ) || is_bool( $image->meta_data ) ) {
			$image->meta_data = [];
		}

		$image->meta_data         = array_merge( $image->meta_data, $full_meta );
		$image->meta_data['full'] = $full_meta;

		// Don't forget to append the 'full' entry in meta_data in the db.
		$this->image_mapper->save( $image );
	}

	/**
	 * Apply EXIF orientation using ImageMagick when available to avoid legacy GD imagerotate() memory issues.
	 *
	 * @param string $image_abspath Absolute path to JPEG.
	 * @return bool Whether the file was re-oriented successfully.
	 */
	private function correct_exif_rotation_with_imagick( $image_abspath ) {
		if ( defined( 'NGG_DISABLE_IMAGICK' ) && NGG_DISABLE_IMAGICK ) {
			return false;
		}

		static $imagick_supports_jpeg = null;

		if ( null === $imagick_supports_jpeg ) {
			$imagick_supports_jpeg = $this->imagick_supports_jpeg();
		}

		if ( ! extension_loaded( 'imagick' ) || ! class_exists( 'Imagick' ) || ! $imagick_supports_jpeg ) {
			return false;
		}

		$backup_path    = $image_abspath . '_backup';
		$exif_from_path = @file_exists( $backup_path ) ? $backup_path : $image_abspath; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$exif_iptc      = EXIFWriter::read_metadata( $exif_from_path );

		try {
			$imagick = new \Imagick( $image_abspath );
			if ( ! method_exists( $imagick, 'autoOrient' ) ) {
				return false;
			}
			$imagick->autoOrient();
			$imagick->writeImage( $image_abspath );
		} catch ( \Exception $e ) {
			error_log( 'NextGEN Gallery: Imagick EXIF auto-orient failed: ' . $e->getMessage() );
			return false;
		} finally {
			if ( isset( $imagick ) && $imagick instanceof \Imagick ) {
				$imagick->clear();
				$imagick->destroy();
			}
		}

		if ( ! empty( $exif_iptc ) && is_array( $exif_iptc ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@EXIFWriter::write_metadata( $image_abspath, $exif_iptc );
		}

		return true;
	}

	/**
	 * Most major browsers do not honor the Orientation meta found in EXIF. To prevent display issues we inspect
	 * the EXIF data and rotate the image so that the EXIF field is not necessary to display the image correctly.
	 * Note: generate_image_clone() will handle the removal of the Orientation tag inside the image EXIF.
	 * Note: This only handles single-dimension rotation; at the time this method was written there are no known
	 * camera manufacturers that both rotate and flip images.
	 *
	 * @param int|object $image
	 * @param bool       $save
	 * @return bool True if no rotation was needed, or a needed rotation was applied; false only
	 *              when a rotation was attempted and the write failed.
	 */
	public function correct_exif_rotation( $image, $save = true ) {
		$image_abspath = $this->get_image_abspath( $image, 'full' );

		if ( ! EXIFWriter::is_jpeg_file( $image_abspath ) ) {
			return true;
		}

		// This method is necessary.
		if ( ! function_exists( 'exif_read_data' ) ) {
			return true;
		}

		// We only need to continue if the Orientation tag is set.
		$exif = @exif_read_data( $image_abspath, 'exif' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( empty( $exif['Orientation'] ) || 1 === (int) $exif['Orientation'] ) {
			return true;
		}

		// Only orientations 3/6/8 are pure rotations. Others (2, 4, 5, 7) involve flips
		// which we don't support; skip them to avoid unnecessary image cloning.
		$orientation = (int) $exif['Orientation'];
		if ( ! in_array( $orientation, [ 3, 6, 8 ], true ) ) {
			return true;
		}

		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			wp_raise_memory_limit( 'image' );
		}

		if ( $this->correct_exif_rotation_with_imagick( $image_abspath ) ) {
			if ( $save ) {
				$this->update_image_dimension_metadata( $image, $image_abspath );
			}
			return true;
		}

		$degree = 0;
		if ( 3 === $orientation ) {
			$degree = 180;
		}
		if ( 6 === $orientation ) {
			$degree = 90;
		}
		if ( 8 === $orientation ) {
			$degree = 270;
		}

		$parameters = [ 'rotation' => $degree ];

		$generated = $this->generate_image_clone(
			$image_abspath,
			$image_abspath,
			$this->get_image_size_params( $image, 'full', $parameters ),
			$parameters
		);

		if ( $generated && $save ) {
			$this->update_image_dimension_metadata( $image, $image_abspath );
		}

		if ( $generated ) {
			$generated->destruct();
		}

		return (bool) $generated;
	}

	/**
	 * Flushes the cache we use for path/url calculation for galleries
	 */
	public function flush_gallery_path_cache( $gallery ) {
		$gallery = is_numeric( $gallery ) ? $gallery : $gallery->gid;
		unset( self::$gallery_abspath_cache[ $gallery ] );
	}

	/**
	 * Returns the absolute path to the cache directory of a gallery.
	 *
	 * Without the gallery parameter the legacy (pre 2.0) shared directory is returned.
	 *
	 * @param int|object|false|Gallery $gallery (optional)
	 * @return string Absolute path to cache directory
	 */
	public function get_cache_abspath( $gallery = false ) {
		return path_join( $this->get_gallery_abspath( $gallery ), 'cache' );
	}

	/**
	 * Gets the absolute path where the full-sized image is stored
	 *
	 * @param int|object $image
	 * @return null|string
	 */
	public function get_full_abspath( $image ) {
		return $this->get_image_abspath( $image, 'full' );
	}

	/**
	 * Alias to get_image_dimensions()
	 *
	 * @param int|object $image
	 * @return array
	 */
	public function get_full_dimensions( $image ) {
		return $this->get_image_dimensions( $image, 'full' );
	}

	/**
	 * Alias for get_original_url()
	 *
	 * @param Image $image
	 * @return string
	 */
	public function get_full_url( $image ) {
		return $this->get_image_url( $image, 'full' );
	}

	public function get_gallery_root() {
		return wp_normalize_path( Filesystem::get_instance()->get_document_root( 'galleries' ) );
	}

	public function get_computed_gallery_abspath( $gallery ) {
		$retval       = null;
		$gallery_root = $this->get_gallery_root();

		// Get the gallery entity from the database.
		if ( $gallery ) {
			if ( is_numeric( $gallery ) ) {
				$gallery = $this->gallery_mapper->find( $gallery );
			}
		}

		// It just doesn't exist.
		if ( ! $gallery ) {
			return $retval;
		}

		// We we have a gallery, determine it's path.
		if ( $gallery ) {
			if ( isset( $gallery->path ) ) {
				$retval = $gallery->path;
			} elseif ( isset( $gallery->slug ) ) {
				$basepath = wp_normalize_path( Settings::get_instance()->gallerypath );
				$retval   = path_join( $basepath, $this->sanitize_directory_name( sanitize_title( $gallery->slug ) ) );
			}

			// Normalize the gallery path. If the gallery path starts with /wp-content, and
			// NGG_GALLERY_ROOT_TYPE is set to 'content', then we need to strip out the /wp-content
			// from the start of the gallery path.
			if ( NGG_GALLERY_ROOT_TYPE === 'content' ) {
				$retval = preg_replace( '#^/?wp-content#', '', $retval );
			}

			// Ensure that the path is absolute. Tested on whole path segments, so a directory
			// that merely starts with the root's name is not mistaken for the root itself.
			if ( ! $this->is_absolute_gallery_path( $retval ) ) {

				// path_join() behaves funny - if the second argument starts with a slash,
				// it won't join the two paths together.
				$retval = preg_replace( '#^/#', '', $retval );
				$retval = path_join( $gallery_root, $retval );
			}

			$retval = wp_normalize_path( $retval );
		}

		return $retval;
	}

	/**
	 * Get the abspath to the gallery folder for the given gallery
	 * The gallery may or may not already be persisted
	 *
	 * @param int|object|Gallery $gallery
	 *
	 * @return string
	 */
	public function get_gallery_abspath( $gallery ) {
		$gallery_id = is_numeric( $gallery ) ? $gallery : ( is_object( $gallery ) && isset( $gallery->gid ) ? $gallery->gid : null );

		if ( ! $gallery_id || ! isset( self::$gallery_abspath_cache[ $gallery_id ] ) ) {
			self::$gallery_abspath_cache[ $gallery_id ] = $this->get_computed_gallery_abspath( $gallery );
		}

		return self::$gallery_abspath_cache[ $gallery_id ];
	}

	public function get_gallery_relpath( $gallery ) {
		// Special hack for home.pl: their document root is just '/'.
		$root = $this->get_gallery_root();
		if ( $root === '/' ) {
			return $this->get_gallery_abspath( $gallery );
		}

		$abspath = $this->get_gallery_abspath( $gallery );

		// Only strip the root off a whole path segment; an unanchored replace cut into the first
		// folder name of a sibling directory. A path outside the root is returned as it stands.
		if ( 0 === strpos( (string) $abspath, trailingslashit( $root ) ) ) {
			return substr( $abspath, strlen( $root ) );
		}

		return $abspath;
	}

	/**
	 * Whether a stored gallery path already points inside a known gallery location, and so needs
	 * no joining onto the gallery root.
	 *
	 * @param string $path A gallery path as stored, absolute or relative.
	 * @return bool
	 */
	protected function is_absolute_gallery_path( $path ) {
		$path = wp_normalize_path( (string) $path );

		// Absoluteness is a lexical question, so it is answered lexically. canonicalize_path()
		// returns '' both for a relative path and for a failed realpath(), and reading the
		// second as "relative" would join the root onto a path that already has one - on
		// exactly the open_basedir / chroot / WP VIP / IIS hosts is_path_contained() documents
		// a lexical fallback for.
		if ( '' === $path || ( '/' !== $path[0] && ! preg_match( '#^[A-Za-z]:/#', $path ) ) ) {
			return false;
		}

		// Under the gallery root, on a path boundary. Through path_contains() rather than a
		// fourth inline copy of it: that one is already purely lexical, so it costs no
		// filesystem access here, and it carries the degenerate-base guard (96fe6832) that an
		// inline test would drop on a host whose document root is "/".
		if ( $this->path_contains( wp_normalize_path( $this->get_gallery_root() ), $path ) ) {
			return true;
		}

		// Otherwise it has to be a known gallery location, which is what admits a path stored
		// beside the root. Same lexical fallback as is_path_contained() for the hosts above.
		$canonical = $this->canonicalize_path( $path );
		if ( '' === $canonical ) {
			$canonical = $this->collapse_path_traversal( $path );
		}

		return '' !== $canonical && $this->is_within_gallery_locations( rtrim( $canonical, '/' ) );
	}

	/**
	 * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image.
	 *
	 * @param int|object $image
	 * @param string     $size (optional) Default = full
	 * @return string
	 */
	public function get_computed_image_abspath( $image, $size = 'full', $check_existance = false ) {
		$retval           = null;
		$containment_base = null;

		// If we have the id, get the actual image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have the image entity - user could have passed in an incorrect id.
		if ( is_object( $image ) ) {
			$gallery_path     = $this->get_gallery_abspath( $image->galleryid );
			$containment_base = $gallery_path;
			if ( $gallery_path ) {
				$folder = $size;
				$prefix = $size;
				switch ( $size ) {

					// Images are stored in the associated gallery folder.
					case 'full':
						$retval = \path_join( $gallery_path, $image->filename );
						break;

					case 'backup':
						$retval = \path_join( $gallery_path, $image->filename . '_backup' );
						if ( ! @file_exists( $retval ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
							$retval = \path_join( $gallery_path, $image->filename );
						}
						break;

					case 'thumbnail':
						$size   = 'thumbnail';
						$folder = 'thumbs';
						$prefix = 'thumbs';
						// deliberately no break here.
					default:
						// NGG 2.0 stores relative filenames in the meta data of
						// an image. It does this because it uses filenames
						// that follow conventional WordPress naming scheme.
						$image_path = null;
						$dynthumbs  = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
						if ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) && isset( $image->meta_data[ $size ]['filename'] ) ) {
							if ( $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
								$image_path = \path_join( $this->get_cache_abspath( $image->galleryid ), $image->meta_data[ $size ]['filename'] );
							} else {
								$image_path = \path_join( $gallery_path, $folder );
								$image_path = \path_join( $image_path, $image->meta_data[ $size ]['filename'] );
							}
						} elseif ( $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
							// Filename not found in meta, but is dynamic.
							$params     = $dynthumbs->get_params_from_name( $size, true );
							$image_path = \path_join( $this->get_cache_abspath( $image->galleryid ), $dynthumbs->get_image_name( $image, $params ) );

							// Filename is not found in meta, nor dynamic.
						} else {
							$settings = Settings::get_instance();

							// This next bit is annoying but necessary for legacy reasons. NextGEN until 3.19 stored thumbnails
							// with a filename of "thumbs_(whatever.jpg)" which Google indexes as "thumbswhatever.jpg" which is
							// not good for SEO. From 3.19 on the default setting is "thumbs-" but we must account for legacy
							// sites.
							$image_path     = \path_join( $gallery_path, $folder );
							$new_thumb_path = \path_join( $image_path, "{$prefix}-{$image->filename}" );
							$old_thumb_path = \path_join( $image_path, "{$prefix}_{$image->filename}" );

							if ( $settings->get( 'dynamic_image_filename_separator_use_dash', false ) ) {
								// Check for thumbs- first.
								if ( file_exists( $new_thumb_path ) ) {
									$image_path = $new_thumb_path;
								} elseif ( file_exists( $old_thumb_path ) ) {
									// Check for thumbs_ as a fallback.
									$image_path = $old_thumb_path;
								} else { // The thumbnail file does not exist, default to thumbs-.
									$image_path = $new_thumb_path;
								}
							} elseif ( file_exists( $old_thumb_path ) ) {
								// Reversed: the option is disabled so check for thumbs_.
								$image_path = $old_thumb_path;
							} elseif ( file_exists( $new_thumb_path ) ) {
								// In case the user has switched back and forth, check for thumbs-.
								$image_path = $new_thumb_path;
							} else { // Default to thumbs_ per the site setting.
								$image_path = $old_thumb_path;
							}
						}                       $retval = $image_path;
						break;
				}

				// The filename components joined above are attacker-influenced: an image's
				// 'filename' column and the per-size 'filename' entries under 'meta_data' are both
				// writable through editing endpoints, and path_join() happily accepts "../".
				// Anything that does not resolve inside the gallery directory (which contains both
				// the thumbs and cache subdirectories) is refused, so a traversal filename can
				// neither be read out through render_image() nor written to by
				// generate_image_size().
				//
				// Logged, because callers only see null: get_image_abspath() memoises it,
				// get_computed_image_url() returns null and get_image_html() emits an empty src, so
				// a refused row renders as a blank slot with nothing for support to search for.
				// Through log_path_refusal(): every occurrence under WP_DEBUG, at most one line per
				// hour without it. This runs once per image per request, so an ungated line would
				// grow the log on every visitor request.
				if ( $retval && ! $this->is_path_within_gallery_dir( $gallery_path, $retval ) ) {
					$this->log_path_refusal(
						sprintf(
							'NextGEN Gallery: refused stored path for image #%s (gallery #%s, size "%s") - %s does not resolve inside the gallery directory (%s)',
							isset( $image->pid ) ? $image->pid : '?',
							isset( $image->galleryid ) ? $image->galleryid : '?',
							$size,
							$retval,
							$gallery_path
						),
						self::REFUSAL_STORED_PATH
					);
					$retval = null;
				}
			}
		}

		/*
		 * Every branch above path_join()s values that came out of the database — the
		 * image filename, or a filename stored in the size's meta_data — onto the
		 * gallery directory, and path_join() will happily join "../../../wp-config.php".
		 * Any path that leaves the gallery directory is therefore not a path to one of
		 * this gallery's images, whatever the database says, so it is refused here
		 * rather than at each of the readfile()/copy() sinks downstream.
		 */
		if ( $retval && $containment_base ) {
			$contained = Security::contain_path( $retval, $containment_base );

			// Gated: this resolves on every front-end gallery render, once per image per
			// named size, so logging unconditionally would let an unauthenticated visitor
			// drive unbounded writes into the host's error log.
			if ( null === $contained && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log(
					sprintf(
						'NextGEN Gallery: refused an image path outside its gallery directory (image %s, size %s).',
						is_object( $image ) && isset( $image->pid ) ? (string) $image->pid : 'unknown',
						(string) $size
					)
				);
			}

			$retval = $contained;
		}

		if ( $retval && $check_existance && ! @file_exists( $retval ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$retval = null;
		}
		return $retval;
	}

	/**
	 * Whether a derivative may be written to $target_path.
	 *
	 * The upload allow-list is deliberately not reused verbatim here. It permits only jpeg/jpg/png/
	 * gif (plus webp where GD supports it), while long-lived galleries and folder imports hold
	 * files predating that list - .jpe, .jfif, .tif, .bmp - whose thumbnails have always generated.
	 * Gating generation on the upload list would silently stop producing thumbnails for those, with
	 * a missing image as the only symptom. So a derivative is also accepted when it carries the same
	 * extension as the source image it is derived from, which is a value this code computed rather
	 * than one a caller supplied. A target extension that matches neither is refused, which is what
	 * keeps a ".php" derivative from ever being written.
	 *
	 * @param string $target_path Absolute path the derivative would be written to.
	 * @param string $source_path Absolute path of the full-size image it derives from.
	 * @return bool
	 */
	public function is_safe_generated_image_path( $target_path, $source_path ) {
		$target_extension = strtolower( pathinfo( (string) $target_path, PATHINFO_EXTENSION ) );
		$source_extension = strtolower( pathinfo( (string) $source_path, PATHINFO_EXTENSION ) );

		// Refused unconditionally: the point of the gate is that a poisoned filename must not turn
		// derivative generation into a write the server will later execute or serve as markup.
		// Checked first so no allowance below can re-admit one.
		if ( in_array( $target_extension, self::UNSAFE_GENERATED_EXTENSIONS, true ) ) {
			return false;
		}

		if ( $this->is_allowed_image_extension( $target_path ) ) {
			return true;
		}

		// Same extension as the source it is generated from - including no extension at all on
		// either side. import_image_file() admits names whose extension has no dot ("holidaypng"),
		// so requiring a non-empty extension here would stop every size from generating for rows
		// that already exist.
		if ( $target_extension === $source_extension ) {
			return true;
		}

		// A known image extension that the upload allow-list does not currently contain. The
		// upload list is conditional - NGG_DEFAULT_ALLOWED_FILE_TYPES carries webp only when
		// imagewebp() exists (#834) - and it is filterable, so gating derivative writes on it
		// alone refused legitimate targets: a jpeg source producing a .webp derivative on a host
		// without GD WebP support, and the .tif/.jpe/.jfif/.bmp galleries that predate the list.
		return in_array( $target_extension, self::GENERATED_IMAGE_EXTENSIONS, true );
	}

	/**
	 * Whether an image path resolves inside its gallery directory.
	 *
	 * @param string $gallery_abspath Gallery directory the image belongs to.
	 * @param string $abspath         Candidate image path.
	 * @return bool
	 */
	public function is_path_within_gallery_dir( $gallery_abspath, $abspath ) {
		if ( empty( $gallery_abspath ) || empty( $abspath ) || ! is_string( $abspath ) ) {
			return false;
		}

		$gallery = $this->canonicalize_path( $gallery_abspath );

		// The base has to be validated too, not just the target. A gallery's stored `path` is
		// writable through import and admin paths, so a tampered base moves the containment
		// boundary instead of tripping it: a path of "." resolves to the WordPress root and every
		// file under it - wp-config.php included - then counts as "inside the gallery", and the
		// containment check below cannot see the tamper because canonicalize_path() has already
		// collapsed the "..". So the base must be a real gallery location and never a
		// shared/system directory - the same pair of checks is_deletion_path_allowed() applies to
		// this value, for the same reason.
		//
		// This is deliberately fail-closed. An earlier revision demoted the gallery-location half
		// to a log line, on the theory that hosts whose gallery directory resolves outside the
		// anchors are real and would have every image blanked. They are not: the anchors are the
		// storage root, the NGG upload base and the WordPress uploads base, so a gallery under any
		// configured storage root is inside one by construction - #153's WP VIP path sits under
		// the uploads anchor, and #460 is about a PHP upload temp file that never reaches this
		// function. Dropping the check bought nothing and admitted "..", /etc and
		// wp-content/plugins/<x> as containment bases, readable through the unauthenticated
		// /nextgen-image/ route. The blank-image diagnosability that motivated it is provided by
		// the refusal log instead, which is what the log was added for.
		//
		// Skipped entirely when the base could not be canonicalized: the host-environment case
		// handled in is_path_contained(), whose traversal check still applies.
		if ( '' !== $gallery
			&& ( ! $this->is_within_gallery_locations( $gallery ) || $this->is_protected_directory( $gallery ) ) ) {
			$this->log_path_refusal(
				sprintf(
					'NextGEN Gallery: refused gallery directory %s as a containment base - it is not inside a known gallery location, or it is a shared/system directory',
					$gallery
				),
				self::REFUSAL_GALLERY_OUTSIDE
			);

			return false;
		}

		return $this->is_path_contained( $gallery_abspath, $abspath );
	}

	/**
	 * Whether $abspath resolves strictly inside $base_abspath.
	 *
	 * Used for the boundaries this class computes itself (a source image's own directory), where
	 * the gallery-location checks in is_path_within_gallery_dir() do not apply because the base was
	 * not read from a writable column.
	 *
	 * @param string $base_abspath Directory that bounds the path.
	 * @param string $abspath      Candidate path.
	 * @return bool
	 */
	public function is_path_contained( $base_abspath, $abspath ) {
		if ( empty( $base_abspath ) || empty( $abspath ) || ! is_string( $abspath ) ) {
			return false;
		}

		$base   = $this->canonicalize_path( $base_abspath );
		$target = $this->canonicalize_path( $abspath );

		// canonicalize_path() returns '' when realpath() cannot resolve the deepest existing
		// component, which is a normal outcome under open_basedir, a restrictive chroot, WP VIP, or
		// IIS-style path forms - hosts this layer already has a support history on. Failing closed
		// there would null every size of every image site-wide with no message. Fall back to a
		// purely lexical comparison: it still collapses "../" and so still refuses the traversal
		// this check exists to stop, it just cannot additionally resolve symlinks. That is strictly
		// more protection than these hosts had before.
		if ( '' === $base || '' === $target ) {
			$base   = $this->collapse_path_traversal( wp_normalize_path( (string) $base_abspath ) );
			$target = $this->collapse_path_traversal( wp_normalize_path( $abspath ) );
		}

		if ( '' === $base || '' === $target ) {
			return false;
		}

		// The directory itself is not a file path, only something below it is.
		if ( $target === rtrim( $base, '/' ) ) {
			return false;
		}

		return $this->path_contains( $base, $target );
	}

	public function get_image_checksum( $image, $size = 'full' ) {
		$retval        = null;
		$image_abspath = $this->get_image_abspath( $image, $size, true );
		if ( $image_abspath ) {
			$retval = md5_file( $image_abspath );
		}
		return $retval;
	}

	/**
	 * Gets the dimensions for a particular-sized image
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @return null|array
	 */
	public function get_image_dimensions( $image, $size = 'full' ) {
		$retval = null;

		// If an image id was provided, get the entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have a valid image.
		if ( $image ) {

			$size = $this->normalize_image_size_name( $size );
			if ( ! $size ) {
				$size = 'full';
			}

			// Image dimensions are stored in the $image->meta_data
			// property for all implementations.
			// is_array() guards against corrupt stored meta (e.g. the literal string "Array"
			// left by a non-serialization-safe migration); fall through to recompute from disk.
			if ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) && is_array( $image->meta_data[ $size ] ) ) {
				$retval = $image->meta_data[ $size ];
			} else {
				// Didn't exist for meta data. We'll have to compute
				// dimensions in the meta_data after computing? This is most likely
				// due to a dynamic image size being calculated for the first time.
				$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
				$abspath   = $this->get_image_abspath( $image, $size, true );
				if ( $abspath ) {
					$dims = @getimagesize( $abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					if ( $dims ) {
						$retval['width']  = $dims[0];
						$retval['height'] = $dims[1];
					}
				} elseif ( $size == 'backup' ) {
					$retval = $this->get_image_dimensions( $image, 'full' );
				}

				if ( ! $retval && $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
					$new_dims = $this->calculate_image_size_dimensions( $image, $size );
					// Prevent a possible PHP warning if the sizes weren't calculated.
					if ( isset( $new_dims['real_width'] ) && isset( $new_dims['real_height'] ) ) {
						$retval = [
							'width'  => $new_dims['real_width'],
							'height' => $new_dims['real_height'],
						];
					} else {
						// CDN/offload: local file absent, so getimagesize() cannot run. Derive dimensions
						// from stored full-size meta instead so dynamic-thumbnail displays (e.g. Pro Mosaic)
						// don't collapse to 0×0. Mirrors get_computed_image_url()'s meta-derived fallback.
						$retval = $this->calculate_dimensions_from_full_meta( $image, $size );
					}
				}
			}
		}

		return $retval;
	}

	/**
	 * Gets the dimensions for a particular-sized image, falling back to the dimensions that size
	 * is configured to render at.
	 *
	 * A null return from get_image_dimensions() is legitimate - the stored meta has no entry for
	 * the size and the sized file cannot be measured - which leaves display templates with
	 * nothing to print. Callers that need a width and a height should use this instead of
	 * guarding the null themselves.
	 *
	 * @param int|object $image Image entity or image id.
	 * @param string     $size  Named image size.
	 * @return null|array Array with 'width' and 'height' keys, or null when nothing resolves them.
	 */
	public function get_image_dimensions_or_default( $image, $size = 'full' ) {
		$dimensions = $this->get_image_dimensions( $image, $size );

		if ( is_array( $dimensions ) && ! empty( $dimensions['width'] ) && ! empty( $dimensions['height'] ) ) {
			return $dimensions;
		}

		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( ! $image ) {
			return null;
		}

		$size = $this->normalize_image_size_name( $size );
		if ( ! $size ) {
			$size = 'full';
		}

		list( $original_width, $original_height ) = $this->get_stored_original_dimensions( $image );

		// The original size needs no fitting, since it is the original.
		if ( 'full' === $size ) {
			return $original_width && $original_height
				? [
					'width'  => $original_width,
					'height' => $original_height,
				]
				: null;
		}

		$params = $this->get_image_size_params( $image, $size );
		$box    = [
			'width'  => ! empty( $params['width'] ) ? (int) $params['width'] : 0,
			'height' => ! empty( $params['height'] ) ? (int) $params['height'] : 0,
		];

		// The generator never upscales - calculate_image_clone_result() clamps the requested
		// size down to the original before cropping or constraining - so the configured size is
		// only an upper bound on what was actually written out.
		if ( $original_width && $box['width'] ) {
			$box['width'] = min( $box['width'], $original_width );
		}

		if ( $original_height && $box['height'] ) {
			$box['height'] = min( $box['height'], $original_height );
		}

		// A cropped size is written out at exactly the dimensions configured for it.
		if ( ! empty( $params['crop'] ) && $box['width'] && $box['height'] ) {
			return $box;
		}

		// Only the original's ratio can say what an uncropped size renders at, so with no stored
		// dimensions the answer stays unknown. The configured box would be a guess at the ratio,
		// and a template's width attribute is not overridden by the stylesheet the way its
		// height is, so a wrong guess would stretch the image rather than just mis-reserve space.
		return $this->fit_dimensions_to_box( $original_width, $original_height, $box['width'], $box['height'] );
	}

	/**
	 * Reads the image's original dimensions out of its stored meta.
	 *
	 * They are recorded twice by update_image_dimension_metadata(), under 'full' and at the top
	 * level, and records written by older versions or left behind by a migration can carry only
	 * one of the two.
	 *
	 * @param object $image Image entity.
	 * @return array{0: int, 1: int} Width and height, each 0 when unknown.
	 */
	private function get_stored_original_dimensions( $image ) {
		if ( isset( $image->meta_data['full']['width'], $image->meta_data['full']['height'] ) ) {
			return [ (int) $image->meta_data['full']['width'], (int) $image->meta_data['full']['height'] ];
		}

		if ( isset( $image->meta_data['width'], $image->meta_data['height'] ) ) {
			return [ (int) $image->meta_data['width'], (int) $image->meta_data['height'] ];
		}

		return [ 0, 0 ];
	}

	/**
	 * Works out what a named size renders at from the image's stored full-size meta.
	 *
	 * Used when the sized file itself cannot be measured, so the original dimensions and the
	 * parameters configured for the size are all there is to go on.
	 *
	 * @param object $image Image entity.
	 * @param string $size  Named image size.
	 * @return null|array Array with 'width' and 'height' keys, or null when the original size is unknown.
	 */
	private function calculate_dimensions_from_full_meta( $image, $size ) {
		if ( ! isset( $image->meta_data['full']['width'], $image->meta_data['full']['height'] ) ) {
			return null;
		}

		$params = $this->get_image_size_params( $image, $size );
		$box_w  = ! empty( $params['width'] ) ? (int) $params['width'] : 0;
		$box_h  = ! empty( $params['height'] ) ? (int) $params['height'] : 0;

		if ( ! empty( $params['crop'] ) && $box_w && $box_h ) {
			return [
				'width'  => $box_w,
				'height' => $box_h,
			];
		}

		return $this->fit_dimensions_to_box(
			(int) $image->meta_data['full']['width'],
			(int) $image->meta_data['full']['height'],
			$box_w,
			$box_h
		);
	}

	/**
	 * Scales the given dimensions to fit inside a box, keeping their ratio.
	 *
	 * Either bound may be zero, meaning unconstrained; with neither bound set the dimensions
	 * are returned as they came in.
	 *
	 * @param int $width  Source width.
	 * @param int $height Source height.
	 * @param int $box_w  Width bound, or 0.
	 * @param int $box_h  Height bound, or 0.
	 * @return null|array Array with 'width' and 'height' keys, or null when the source has no size.
	 */
	private function fit_dimensions_to_box( $width, $height, $box_w, $box_h ) {
		if ( ! $width || ! $height ) {
			return null;
		}

		$ratio = $width / $height;

		if ( $box_w && $box_h ) {
			$w = $box_w;
			$h = (int) round( $box_w / $ratio );
			if ( $h > $box_h ) {
				$h = $box_h;
				$w = (int) round( $box_h * $ratio );
			}
		} elseif ( $box_w ) {
			$w = $box_w;
			$h = (int) round( $box_w / $ratio );
		} elseif ( $box_h ) {
			$h = $box_h;
			$w = (int) round( $box_h * $ratio );
		} else {
			$w = $width;
			$h = $height;
		}

		return [
			'width'  => $w,
			'height' => $h,
		];
	}

	public function get_image_format_list() {
		$format_list = [
			IMAGETYPE_GIF  => 'gif',
			IMAGETYPE_JPEG => 'jpg',
			IMAGETYPE_PNG  => 'png',
		];
		if ( defined( 'IMAGETYPE_WEBP' ) ) {
			$format_list[ IMAGETYPE_WEBP ] = 'webp'; // phpcs:ignore PHPCompatibility.Constants.NewConstants.imagetype_webpFound
		}

		return $format_list;
	}

	/**
	 * Gets the HTML for an image
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @param array      $attributes (optional)
	 * @return string
	 */
	public function get_image_html( $image, $size = 'full', $attributes = [] ) {
		$retval = '';

		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( $image ) {

			// Set alt text if not already specified.
			if ( ! isset( $attributes['alttext'] ) ) {
				$attributes['alt'] = esc_attr( $image->alttext );
			}

			// Set the title if not already set.
			if ( ! isset( $attributes['title'] ) ) {
				$attributes['title'] = esc_attr( $image->alttext );
			}

			// Set the dimensions if not set already.
			if ( ! isset( $attributes['width'] ) || ! isset( $attributes['height'] ) ) {
				$dimensions = $this->get_image_dimensions( $image, $size );
				if ( ! isset( $attributes['width'] ) ) {
					$attributes['width'] = $dimensions['width'];
				}
				if ( ! isset( $attributes['height'] ) ) {
					$attributes['height'] = $dimensions['height'];
				}
			}

			// Set the url if not already specified. This is an <img src> context, so it
			// gets the cache-buster; get_image_url() itself stays canonical.
			if ( ! isset( $attributes['src'] ) ) {
				$attributes['src'] = $this->get_cache_busted_image_url( $image, $size );
			}

			// Format attributes.
			$attribs = [];
			foreach ( $attributes as $attrib => $value ) {
				$attribs[] = "{$attrib}=\"{$value}\"";
			}
			$attribs = implode( ' ', $attribs );

			// Return HTML string.
			$retval = "<img {$attribs} />";
		}

		return $retval;
	}

	public function get_computed_image_url( $image, $size = 'full' ) {
		$retval    = null;
		$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();

		// Get the image abspath.
		$image_abspath = $this->get_image_abspath( $image, $size );
		if ( $dynthumbs->is_size_dynamic( $size ) && $image_abspath && ! file_exists( $image_abspath ) ) {
			if ( defined( 'NGG_DISABLE_DYNAMIC_IMG_URLS' ) && constant( 'NGG_DISABLE_DYNAMIC_IMG_URLS' ) ) {
				$params = [
					'watermark'  => false,
					'reflection' => false,
					'crop'       => true,
				];
				$result = $this->generate_image_size( $image, $size, $params );
				if ( $result ) {
					$image_abspath = $this->get_image_abspath( $image, $size );
				}
			} else {
				$full_abspath = $this->get_image_abspath( $image, 'full' );
				if ( $full_abspath && file_exists( $full_abspath ) ) {
					return null;
				}
				// Only fall back to a metadata-derived gallery URL when a CDN/offload plugin
				// has claimed this image. Without this gate, non-CDN sites with a missing
				// full-size file would cache a broken URL instead of reaching the dynamic
				// router. Imagely CDN marks images via _envira_cdn_id; other offload plugins
				// can hook ngg_use_offloaded_image_url to opt in (tracked envira-image-cdn#63).
				$is_offloaded = ! empty( $image->meta_data['_envira_cdn_id'] );
				if ( ! apply_filters( 'ngg_use_offloaded_image_url', $is_offloaded, $image ) ) {
					return null;
				}
				$size          = 'full';
				$image_abspath = $full_abspath;
			}
		}

		// Assuming we have an abspath, we can translate that to a url.
		if ( $image_abspath ) {
			$retval = $this->image_abspath_to_url( $image_abspath );
		}

		return $retval;
	}

	/**
	 * Turns an absolute image path into a public URL.
	 *
	 * The path is made relative to a directory whose URL is known, matched on whole path segments.
	 * An unanchored strip cut into the first folder name whenever a directory sat beside the
	 * gallery root under a name beginning with it: "/x/wp" off "/x/wp-content/uploads/..." left
	 * "-content/uploads/...". Reachable because image paths are canonicalized before they get
	 * here, so a gallery stored as "../wp-content/uploads/<slug>/" - which long-lived installs
	 * still hold, from before ".." was rejected in gallery paths - resolves outside the root.
	 *
	 * @param string $image_abspath Absolute path of an image file.
	 * @return string|null Public URL, or null when $image_abspath is empty.
	 */
	protected function image_abspath_to_url( $image_abspath ) {
		$path = wp_normalize_path( (string) $image_abspath );

		if ( '' === $path ) {
			return null;
		}

		// The path arrives only lexically canonicalized, so it is compared in both forms against
		// anchors that carry both - a symlinked uploads or WordPress directory matches one of them.
		$candidates = [ $path ];
		$resolved   = $this->canonicalize_path( $path );
		if ( '' !== $resolved && $resolved !== $path ) {
			$candidates[] = $resolved;
		}

		foreach ( $this->get_url_anchors() as $dir => $base_url ) {
			foreach ( $candidates as $candidate ) {
				if ( 0 !== strpos( $candidate, $dir ) ) {
					continue;
				}

				// Url encode each uri segment.
				$segments = array_map( 'rawurlencode', explode( '/', substr( $candidate, strlen( $dir ) ) ) );
				$url      = $base_url . implode( '/', $segments );

				return is_ssl() ? str_replace( 'http:', 'https:', $url ) : $url;
			}
		}

		// Throttled, because this runs per image per size on every front-end render. Logged at
		// all because the fallback below can only produce a working URL by luck, and a wrong
		// image URL leaves nothing for support to search for. Tags stripped with the newlines
		// too: the path carries a filename read from the database.
		$this->log_path_refusal(
			sprintf(
				'NextGEN Gallery: %s is not inside any directory whose URL is known; falling back to the gallery root URL',
				wp_strip_all_tags( $path, true )
			),
			self::REFUSAL_URL_UNMAPPED
		);

		/*
		 * Last resort: what this function did before it matched on path boundaries - strip the
		 * root where it is a literal prefix and hang the rest off the gallery root URL.
		 *
		 * Only reached by a path no known directory contains: a stream-wrapped uploads base
		 * (s3://, iu://), or a document root of "/" whose anchor the degenerate-base guard
		 * drops. Returning null instead would be a new behaviour for those installs - it would
		 * blank images that currently render, and hand null to the offload filters above and to
		 * the placeholder substitution in resolve_image_url(), both of which expect a string.
		 */
		$root      = wp_normalize_path( rtrim( $this->get_gallery_root(), '/' ) );
		$remainder = ( '' !== $root && 0 === strpos( $path, $root ) ) ? substr( $path, strlen( $root ) ) : $path;
		$segments  = array_map( 'rawurlencode', explode( '/', ltrim( $remainder, '/' ) ) );
		$base_url  = trailingslashit( NGG_GALLERY_ROOT_TYPE === 'site' ? site_url() : WP_CONTENT_URL );
		$url       = $base_url . implode( '/', $segments );

		return is_ssl() ? str_replace( 'http:', 'https:', $url ) : $url;
	}

	/**
	 * Directories whose public URL is known, as a map of trailing-slashed canonical directory to
	 * trailing-slashed base URL.
	 *
	 * The gallery root comes first so a gallery under the configured storage root keeps resolving
	 * through the gallery root URL exactly as before. Uploads comes before WP_CONTENT_DIR because
	 * "upload_path" can move uploads out of wp-content, which then does not contain it.
	 *
	 * @return array
	 */
	protected function get_url_anchors() {
		$uploads = wp_get_upload_dir();
		$pairs   = [
			[
				$this->get_gallery_root(),
				NGG_GALLERY_ROOT_TYPE === 'site' ? site_url() : WP_CONTENT_URL,
			],
			[
				isset( $uploads['basedir'] ) ? $uploads['basedir'] : '',
				isset( $uploads['baseurl'] ) ? $uploads['baseurl'] : '',
			],
			[ WP_CONTENT_DIR, WP_CONTENT_URL ],
			[ ABSPATH, site_url() ],
		];

		// Keyed on the directories themselves: the uploads base is filterable and switchable at
		// runtime. Resolving the pairs is cheap; what the cache saves is the realpath() walk.
		$key = md5( (string) wp_json_encode( $pairs ) );

		if ( isset( self::$url_anchors_cache[ $key ] ) ) {
			return self::$url_anchors_cache[ $key ];
		}

		$anchors = [];

		foreach ( $pairs as $pair ) {
			list( $dir, $url ) = $pair;

			if ( ! is_string( $dir ) || ! is_string( $url ) || '' === trim( $dir ) || '' === trim( $url ) ) {
				continue;
			}

			// Three forms per directory. WordPress hands back "upload_path" unresolved
			// (".../wordpress/../wp-content/uploads"), hence the two canonical ones; the raw form
			// is what makes a stream-wrapped base ("s3://bucket/uploads") matchable at all, since
			// both canonicalizers mangle or reject a wrapper while the image path keeps it.
			foreach ( [ wp_normalize_path( $dir ), Security::canonicalize_path( $dir ), $this->canonicalize_path( $dir ) ] as $canonical ) {
				if ( ! is_string( $canonical ) || '' === $canonical ) {
					continue;
				}

				$canonical = trailingslashit( $canonical );

				// Same guard and prefix pattern as Security::path_is_within(): a directory that
				// reduces to the filesystem root prefixes every path and would answer for all.
				$body = preg_replace( '#^(?://[^/]+/|[a-zA-Z]:/|/)#', '', $canonical );
				if ( '' === rtrim( (string) $body, '/' ) ) {
					continue;
				}

				// First anchor to claim a directory keeps it, so the ordering above holds.
				if ( ! isset( $anchors[ $canonical ] ) ) {
					$anchors[ $canonical ] = trailingslashit( $url );
				}
			}
		}

		self::$url_anchors_cache[ $key ] = $anchors;

		return $anchors;
	}

	public function normalize_image_size_name( $size = 'full' ) {
		switch ( $size ) {
			case 'full':
			case 'image':
			case 'orig':
			case 'original':
			case 'resized':
				$size = 'full';
				break;
			case 'thumb':
			case 'thumbnail':
			case 'thumbnails':
			case 'thumbs':
				$size = 'thumbnail';
				break;
		}
		return $size;
	}

	/**
	 * Returns the named sizes available for images
	 *
	 * @return array
	 */
	public function get_image_sizes( $image = false ) {
		$retval = [ 'full', 'thumbnail' ];

		if ( is_numeric( $image ) ) {
			$image = ImageMapper::get_instance()->find( $image );
		}

		if ( $image ) {
			if ( $image->meta_data ) {
				$meta_data = is_object( $image->meta_data ) ? get_object_vars( $image->meta_data ) : $image->meta_data;
				foreach ( $meta_data as $key => $value ) {
					// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					if ( is_array( $value ) && isset( $value['width'] ) && ! in_array( $key, $retval ) ) {
						$retval[] = $key;
					}
				}
			}
		}

		return $retval;
	}

	public function get_image_size_params( $image, $size, $params = [], $skip_defaults = false ) {
		// Callers may pass corrupt stored meta (a string) as $params — PHP 8 fatals on string-offset writes.
		if ( ! is_array( $params ) ) {
			$params = [];
		}

		// Get the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
		if ( $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
			$named_params = $dynthumbs->get_params_from_name( $size, true );
			if ( ! $params ) {
				$params = [];
			}
			$params = array_merge( $params, $named_params );
		}

		$params = apply_filters( 'ngg_get_image_size_params', $params, $size, $image );

		// Ensure we have a valid image.
		if ( $image ) {
			$settings = Settings::get_instance();

			if ( ! $skip_defaults ) {
				// Get default settings.
				if ( $size == 'full' ) {
					if ( ! isset( $params['quality'] ) ) {
						$params['quality'] = $settings->get( 'imgQuality' );
					}
				} else {
					if ( ! isset( $params['crop'] ) ) {
						$params['crop'] = $settings->get( 'thumbfix' );
					}

					if ( ! isset( $params['quality'] ) ) {
						$params['quality'] = $settings->get( 'thumbquality' );
					}
				}
			}

			// width and height when omitted make generate_image_clone create a clone with original size, so try find defaults regardless of $skip_defaults.
			if ( ! isset( $params['width'] ) || ! isset( $params['height'] ) ) {

				// First test if this is a "known" image size, i.e. if we store these sizes somewhere when users re-generate these sizes from the UI...this is required to be compatible with legacy.
				// try the 2 default built-in sizes, first thumbnail...
				if ( $size == 'thumbnail' ) {
					if ( ! isset( $params['width'] ) ) {
						$params['width'] = $settings->thumbwidth;
					}

					if ( ! isset( $params['height'] ) ) {
						$params['height'] = $settings->thumbheight;
					}
				} elseif ( $size == 'full' ) {
					// ...and then full, which is the size specified in the global resize options.
					if ( ! isset( $params['width'] ) ) {
						if ( $settings->imgAutoResize ) {
							$params['width'] = $settings->imgWidth;
						}
					}

					if ( ! isset( $params['height'] ) ) {
						if ( $settings->imgAutoResize ) {
							$params['height'] = $settings->imgHeight;
						}
					}
				} elseif ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) ) {
					// Only re-use old sizes as last resort.
					$dimensions = $image->meta_data[ $size ];

					if ( ! isset( $params['width'] ) ) {
						$params['width'] = $dimensions['width'];
					}

					if ( ! isset( $params['height'] ) ) {
						$params['height'] = $dimensions['height'];
					}
				}
			}

			if ( ! isset( $params['crop_frame'] ) ) {
				$crop_frame_size_name = 'thumbnail';

				if ( isset( $image->meta_data[ $size ]['crop_frame'] ) ) {
					$crop_frame_size_name = $size;
				}

				if ( isset( $image->meta_data[ $crop_frame_size_name ]['crop_frame'] ) ) {
					$params['crop_frame'] = $image->meta_data[ $crop_frame_size_name ]['crop_frame'];

					if ( ! isset( $params['crop_frame']['final_width'] ) ) {
						$params['crop_frame']['final_width'] = $image->meta_data[ $crop_frame_size_name ]['width'];
					}

					if ( ! isset( $params['crop_frame']['final_height'] ) ) {
						$params['crop_frame']['final_height'] = $image->meta_data[ $crop_frame_size_name ]['height'];
					}
				}
			} else {
				if ( ! isset( $params['crop_frame']['final_width'] ) ) {
					$params['crop_frame']['final_width'] = $params['width'];
				}

				if ( ! isset( $params['crop_frame']['final_height'] ) ) {
					$params['crop_frame']['final_height'] = $params['height'];
				}
			}
		}

		return $params;
	}

	/**
	 * Alias to get_image_dimensions()
	 *
	 * @param int|object $image
	 * @return array
	 */
	public function get_original_dimensions( $image ) {
		return $this->get_image_dimensions( $image, 'full' );
	}

	/**
	 * Gets the upload absolute path.
	 *
	 * @param object|bool $gallery (optional)
	 * @return string
	 */
	public function get_upload_abspath( $gallery = false ) {
		// Base upload path.
		$retval = Settings::get_instance()->get( 'gallerypath' );
		$fs     = Filesystem::get_instance();

		// Append the slug if a gallery has been specified.
		if ( $gallery ) {
			$retval = $this->get_gallery_abspath( $gallery );
		}

		// We need to make this an absolute path.
		if ( ! empty( $retval ) && strpos( $retval, $fs->get_document_root( 'gallery' ) ) !== 0 ) {
			$retval = rtrim( $fs->join_paths( $fs->get_document_root( 'gallery' ), $retval ), '/\\' );
		}

		// Convert slashes.
		return wp_normalize_path( $retval );
	}

	/**
	 * Gets the upload path, optionally for a particular gallery
	 *
	 * @param int|Gallery|object|false $gallery (optional)
	 *
	 * @return string
	 */
	public function get_upload_relpath( $gallery = false ) {
		$fs = Filesystem::get_instance();

		$retval = str_replace(
			$fs->get_document_root( 'gallery' ),
			'',
			$this->get_upload_abspath( $gallery )
		);

		return '/' . wp_normalize_path( ltrim( $retval, '/' ) );
	}

	public function delete_gallery_directory( $abspath ) {
		// Remove all image files and purge all empty directories left over.
		$iterator = new \DirectoryIterator( $abspath );

		// Only delete image files! Other files may be stored incorrectly but it's not our place to delete them.
		$removable_extensions = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );
		if ( ! is_array( $removable_extensions ) ) {
			$removable_extensions = array_filter( array_map( 'trim', explode( ',', (string) $removable_extensions ) ) );
		}
		$backup_extensions = [];
		foreach ( $removable_extensions as $ext ) {
			$backup_extensions[] = $ext . '_backup';
		}
		$removable_extensions = array_merge( $removable_extensions, $backup_extensions );

		foreach ( $iterator as $file ) {
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( in_array( $file->getBasename(), [ '.', '..' ] ) ) {
				continue;

			} elseif ( $file->isFile() || $file->isLink() ) {
				$extension = strtolower( pathinfo( $file->getPathname(), PATHINFO_EXTENSION ) );
				if ( in_array( $extension, $removable_extensions, true ) ) {
					@unlink( $file->getPathname() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
				}
			} elseif ( $file->isDir() ) {
				$this->delete_gallery_directory( $file->getPathname() );
			}
		}

		// DO NOT remove directories that still have files in them. Note: '.' and '..' are included with getSize().
		$empty = true;
		foreach ( $iterator as $file ) {
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( in_array( $file->getBasename(), [ '.', '..' ] ) ) {
				continue;
			}
			$empty = false;
		}
		if ( $empty ) {
			@rmdir( $iterator->getPath() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir, WordPress.PHP.NoSilencedErrors.Discouraged
		}
	}

	/**
	 * Copies images to another gallery.
	 *
	 * @param Image[]     $images
	 * @param Gallery|int $dst_gallery
	 *
	 * @return int[] New image id, keyed by the id of the source image that produced it. A
	 *               source id that failed to copy is simply absent as a key - callers that
	 *               need to know what succeeded must check by source id, not by position,
	 *               since a skipped source would otherwise shift every later id's index.
	 */
	public function copy_images( $images, $dst_gallery ) {
		$retval = [];

		// Ensure that the image ids we have are valid.
		$image_mapper = ImageMapper::get_instance();
		foreach ( $images as $image ) {
			if ( is_numeric( $image ) ) {
				$image = $image_mapper->find( $image );
			}

			$backup_abspath = $this->get_image_abspath( $image, 'backup' );
			$image_abspath  = $backup_abspath ? $backup_abspath : $this->get_image_abspath( $image );

			if ( $image_abspath ) {
				try {
					// Import the image; this will copy the main file.
					$new_image_id = $this->import_image_file( $dst_gallery, $image_abspath, $image->filename );

					if ( $new_image_id ) {
						// Copy the properties of the old image.
						$new_image = $image_mapper->find( $new_image_id );
						foreach ( get_object_vars( $image ) as $key => $value ) {
							// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							if ( in_array( $key, [ 'pid', 'galleryid', 'meta_data', 'filename', 'sortorder', 'extras_post_id' ] ) ) {
								continue;
							}
							$new_image->$key = $value;
						}
						$image_mapper->save( $new_image );

						// Copy tags.
						$tags = wp_get_object_terms( $image->pid, 'ngg_tag', 'fields=ids' );
						$tags = array_map( 'intval', $tags );
						wp_set_object_terms( $new_image_id, $tags, 'ngg_tag', true );

						// Copy all of the generated versions (resized versions, watermarks, etc).
						foreach ( $this->get_image_sizes( $image ) as $named_size ) {
							// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							if ( in_array( $named_size, [ 'full', 'thumbnail' ] ) ) {
								continue;
							}
							$old_abspath = $this->get_image_abspath( $image, $named_size );
							$new_abspath = $this->get_image_abspath( $new_image, $named_size );
							if ( is_array( @stat( $old_abspath ) ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
								$new_dir = dirname( $new_abspath );
								// Ensure the target directory exists.
								if ( @stat( $new_dir ) === false ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
									wp_mkdir_p( $new_dir );
								}
								@copy( $old_abspath, $new_abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
							}
						}

						// Mark as done. Keyed by the source id so a caller can tell which
						// specific images copied without relying on position.
						$retval[ $image->pid ] = $new_image_id;
					}
				} catch ( \Exception $e ) {
					// One image failing to copy must not discard images already copied earlier
					// in this same bulk operation, nor abort move_images() before it deletes the
					// sources of the ones that did copy.
					error_log( 'NextGEN Gallery: could not copy image #' . $image->pid . ' to gallery: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}
			}
		}

		return $retval;
	}

	/**
	 * Moves images from to another gallery
	 *
	 * @param array      $images
	 * @param int|object $gallery
	 * @return int[]
	 */
	public function move_images( $images, $gallery ) {
		$retval = $this->copy_images( $images, $gallery );

		if ( $images ) {
			foreach ( $images as $image ) {
				$pid = is_numeric( $image ) ? $image : $image->pid;

				// A source that isn't a key here failed to copy - deleting it now would lose
				// the photo with no copy anywhere. Only delete what actually made it across.
				if ( isset( $retval[ $pid ] ) ) {
					$this->delete_image( $image );
				}
			}
		}

		return $retval;
	}

	/**
	 * Deletes a directory.
	 *
	 * @param string $abspath
	 * @return bool
	 */
	public function delete_directory( $abspath ) {
		$retval = false;

		if ( @file_exists( $abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$files = scandir( $abspath );
			array_shift( $files );
			array_shift( $files );
			foreach ( $files as $file ) {
				$file_abspath = implode( DIRECTORY_SEPARATOR, [ rtrim( $abspath, '/\\' ), $file ] );
				if ( is_dir( $file_abspath ) ) {
					$this->delete_directory( $file_abspath );
				} else {
					unlink( $file_abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
				}
			}
			rmdir( $abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
			$retval = @file_exists( $abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		return $retval;
	}

	public function delete_gallery( $gallery ) {
		$abspath = $this->get_gallery_abspath( $gallery );
		if ( empty( $abspath ) ) {
			return;
		}

		// Delete the gallery's own image files, each bounded to the gallery directory, rather than
		// recursively removing the directory tree. This clears the gallery's images (including a
		// "keep original location" import that lives under the uploads base) without touching
		// unrelated media that may share the directory, so a tampered gallery path cannot wipe the
		// media library.
		$gallery_id = is_numeric( $gallery )
			? (int) $gallery
			: ( is_object( $gallery ) && isset( $gallery->gid ) ? $gallery->gid : null );

		if ( $gallery_id ) {
			foreach ( $this->image_mapper->find_all_for_gallery( $gallery_id ) as $image ) {
				foreach ( $this->get_image_sizes( $image ) as $named_size ) {
					$image_abspath = $this->get_image_abspath( $image, $named_size );
					if ( empty( $image_abspath )
						|| ! $this->is_image_path_within_gallery( $image, $image_abspath )
						|| ! $this->is_removable_image_path( $image_abspath ) ) {
						continue;
					}
					if ( @file_exists( $image_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						@unlink( $image_abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
					}
				}
			}
		}

		// Only recursively remove the directory tree itself when it is a dedicated NextGEN gallery
		// folder (never a shared or "keep original location" import location), to clean up leftover
		// empty sub-directories and backups.
		if ( @file_exists( $abspath ) && $this->is_gallery_directory_deletable( $abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$this->delete_gallery_directory( $abspath );
		}
	}

	/**
	 * Deletes an image.
	 *
	 * @param Image        $image
	 * @param string|false $size
	 * @return bool
	 */
	public function delete_image( $image, $size = false ) {
		$retval = false;

		// Ensure that we have the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( $image ) {
			$image_id = $image->{$image->id_field};

			// The gallery directory that bounds every unlink for this image. When it cannot be
			// resolved (e.g. the gallery row is gone), there is nothing to bound the file to: skip the
			// unlinks but still let the record be removed, rather than leaving an orphaned image
			// permanently undeletable.
			$gallery_abspath   = $this->get_gallery_abspath( $image->galleryid );
			$bounded_deletable = ! empty( $gallery_abspath );

			// Delete only a particular image size.
			if ( $size ) {
				$abspath = $this->get_image_abspath( $image, $size );

				// An unresolvable path (e.g. an offloaded or orphaned image with no local file) is
				// not a deletion target: skip the unlink but still update the metadata. A path that
				// resolves outside the gallery directory is refused.
				if ( $bounded_deletable && ! empty( $abspath ) ) {
					if ( ! $this->is_deletion_path_allowed( $gallery_abspath, $abspath ) ) {
						return false;
					}
					do_action( 'ngg_delete_image', $image_id, $size );
					if ( @file_exists( $abspath ) && $this->is_removable_image_path( $abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						@unlink( $abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
					}
				} else {
					do_action( 'ngg_delete_image', $image_id, $size );
				}
				if ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) ) {
					unset( $image->meta_data[ $size ] );
					$this->image_mapper->save( $image );
				}
			} else {
				// Validate every resolvable path before deleting any, so an out-of-bounds size
				// cannot cause a partial delete. Unresolvable sizes (offloaded/orphaned) are skipped
				// rather than aborting the delete, so those images stay deletable.
				$abspaths = [];
				if ( $bounded_deletable ) {
					foreach ( $this->get_image_sizes( $image ) as $named_size ) {
						$image_abspath = $this->get_image_abspath( $image, $named_size );
						if ( empty( $image_abspath ) ) {
							continue;
						}
						if ( ! $this->is_deletion_path_allowed( $gallery_abspath, $image_abspath ) ) {
							return false;
						}
						if ( ! $this->is_removable_image_path( $image_abspath ) ) {
							continue;
						}
						$abspaths[] = $image_abspath;
					}
				}

				do_action( 'ngg_delete_image', $image_id, $size );

				// Delete all sizes of the image.
				foreach ( $abspaths as $image_abspath ) {
					if ( @file_exists( $image_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						@unlink( $image_abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
					}
				}

				// Delete the entity.
				$this->image_mapper->destroy( $image );
			}
			$retval = true;
		}

		return $retval;
	}

	/**
	 * Whether the given path is safe to delete for the given image.
	 *
	 * @param Image  $image   The image entity whose gallery bounds the deletion.
	 * @param string $abspath The absolute path that is about to be deleted.
	 * @return bool
	 */
	protected function is_image_path_within_gallery( $image, $abspath ) {
		return $this->is_deletion_path_allowed( $this->get_gallery_abspath( $image->galleryid ), $abspath );
	}

	/**
	 * Whether a file is safe to delete: it must resolve inside the given gallery directory, and
	 * that gallery directory must itself be a legitimate gallery location.
	 *
	 * Filenames and gallery paths are both writable through some endpoints, so either may contain
	 * "../". The gallery is required to sit under one of the configured gallery bases (the NextGEN
	 * gallery base or the WordPress uploads base) and not be a shared/system directory, so a
	 * tampered gallery path (e.g. "." which resolves to the WordPress root) cannot move the boundary
	 * to let files such as wp-config.php count as in-gallery. Those bases are canonicalized the same
	 * way the gallery is, so an install whose media tree is reached through a symlink resolves
	 * consistently instead of being excluded. realpath() canonicalization resolves both traversal
	 * sequences and symlinks.
	 *
	 * @param string $gallery_abspath Gallery directory that bounds the deletion.
	 * @param string $abspath         File that is about to be deleted.
	 * @return bool
	 */
	public function is_deletion_path_allowed( $gallery_abspath, $abspath ) {
		if ( empty( $abspath ) || ! is_string( $abspath ) || empty( $gallery_abspath ) ) {
			return false;
		}

		$gallery = $this->canonicalize_path( $gallery_abspath );

		if ( '' === $gallery ) {
			return false;
		}

		// The gallery directory must be a legitimate gallery location, never one of the shared
		// bases itself or another shared/system directory.
		if ( ! $this->is_within_gallery_locations( $gallery ) || $this->is_protected_directory( $gallery ) ) {
			return false;
		}

		// The target must resolve inside the gallery directory.
		$target = $this->canonicalize_path( $abspath );

		return '' !== $target && $this->path_contains( $gallery, $target );
	}

	/**
	 * Whether a gallery directory is safe to recursively delete.
	 *
	 * A gallery's filesystem path is writable through XML-RPC/REST, so it could be pointed at a
	 * shared directory. Only a directory strictly inside the dedicated NextGEN gallery base may be
	 * recursively removed. Anything in the wider uploads tree (e.g. "wp-content/uploads/2026") is
	 * refused, so a tampered path cannot trigger a recursive delete of the media library. A gallery
	 * kept at its original import location under the uploads base is not removed here (its shared
	 * folder may hold unrelated media); its image files are deleted per image instead.
	 *
	 * @param string $abspath Absolute path to the gallery directory that would be deleted.
	 * @return bool
	 */
	public function is_gallery_directory_deletable( $abspath ) {
		if ( empty( $abspath ) || ! is_string( $abspath ) ) {
			return false;
		}

		$dir  = $this->canonicalize_path( $abspath );
		$base = $this->canonicalize_path( $this->get_upload_abspath() );

		if ( '' === $dir || '' === $base ) {
			return false;
		}

		// Must be strictly inside the NextGEN gallery base, never the base itself.
		if ( $dir === rtrim( $base, '/' ) || ! $this->path_contains( $base, $dir ) ) {
			return false;
		}

		// Never recursively delete a shared/known directory.
		return ! $this->is_protected_directory( $dir );
	}

	/**
	 * Whether a file path is an image-type file that NextGEN may delete. Mirrors the extension
	 * allowlist used by delete_gallery_directory() (the configured image types plus their "_backup"
	 * companions), so per-image unlinks never remove a non-image file (an export, backup or document)
	 * that happens to share a directory a gallery points at.
	 *
	 * @param string $abspath Absolute file path.
	 * @return bool
	 */
	public function is_removable_image_path( $abspath ) {
		$removable = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );
		if ( ! is_array( $removable ) ) {
			$removable = array_filter( array_map( 'trim', explode( ',', (string) $removable ) ) );
		}
		$backup_exts = [];
		foreach ( $removable as $ext ) {
			$backup_exts[] = $ext . '_backup';
		}
		$removable = array_merge( $removable, $backup_exts );
		$extension = strtolower( pathinfo( (string) $abspath, PATHINFO_EXTENSION ) );
		return in_array( $extension, $removable, true );
	}

	/**
	 * Resolves and caches the deletion-boundary directories for the current blog: the gallery
	 * location anchors (the dedicated NextGEN gallery base and the WordPress uploads base, the
	 * latter covering "keep original location" imports) and the shared/protected directories. All
	 * are install constants for the site, so they are canonicalized once per blog rather than on
	 * every per-size deletion check. Every entry is canonicalized so a symlinked media tree (NFS,
	 * bind mount, symlinked wp-content) resolves the same way a gallery path does.
	 *
	 * @return array{anchors: string[], protected: string[], protected_trees: string[]}
	 */
	private function get_deletion_dirs() {
		$blog = function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0;
		if ( isset( self::$deletion_dirs_cache[ $blog ] ) ) {
			return self::$deletion_dirs_cache[ $blog ];
		}

		$fs        = Filesystem::get_instance();
		$wp_upload = wp_get_upload_dir();
		$abspath   = wp_normalize_path( ABSPATH );

		$drop_empty = static function ( $dir ) {
			return '' !== $dir;
		};

		// Distinguishes "this location is not configured on this install" from "this location is
		// configured but did not resolve". The first is normal - a blank gallerypath leaves the NGG
		// upload base unset, and WPMU_PLUGIN_DIR need not exist - and must not be treated as
		// degeneracy, or the completeness guard below would permanently defeat caching on an
		// ordinary site. The second is the transient realpath() failure the guard exists for.
		$unresolved = 0;
		$resolve    = function ( $raw ) use ( &$unresolved ) {
			$raw = is_string( $raw ) ? $raw : '';

			if ( '' === trim( $raw ) ) {
				return '';
			}

			$canonical = $this->canonicalize_path( $raw );

			if ( '' === $canonical ) {
				++$unresolved;
			}

			return $canonical;
		};

		// A gallery may legitimately live anywhere under the storage root (galleries created under a
		// previous Gallery Path value, or on an NGG_GALLERY_ROOT_TYPE='content' install, are still
		// under it) or under the uploads base (keep-original-location imports, and symlinked media
		// trees whose realpath leaves the storage root). Anchoring on the storage root rather than the
		// current, mutable gallerypath option keeps the boundary correct across those configurations.
		$uploads  = $resolve( isset( $wp_upload['basedir'] ) ? $wp_upload['basedir'] : '' );
		$ngg_base = $resolve( $this->get_upload_abspath() );
		$root     = $resolve( $this->get_gallery_root() );

		$anchors = array_values( array_filter( [ $root, $ngg_base, $uploads ], $drop_empty ) );

		$protected = array_values(
			array_filter(
				[
					$root,
					$resolve( $fs->get_document_root( 'content' ) ),
					$resolve( $fs->get_document_root() ),
					$ngg_base,
					$uploads,
				],
				$drop_empty
			)
		);

		// Code directories are refused as a whole subtree, not just at their root: a gallery
		// directory of "wp-content/plugins/<slug>/assets" is no more legitimate than
		// "wp-content/plugins", and an exact-match-only test accepted every one of those
		// subdirectories as a containment base. The list above stays exact-match, because those
		// are the parents galleries legitimately live *under*.
		$protected_trees = array_values(
			array_filter(
				[
					$resolve( $fs->get_document_root( 'plugins' ) ),
					$resolve( $fs->get_document_root( 'plugins_mu' ) ),
					$resolve( $fs->get_document_root( 'templates' ) ),
					$resolve( $fs->get_document_root( 'stylesheets' ) ),
					$resolve( $fs->join_paths( $abspath, 'wp-admin' ) ),
					$resolve( $fs->join_paths( $abspath, 'wp-includes' ) ),
					$resolve( get_theme_root() ),
				],
				$drop_empty
			)
		);

		$dirs = [
			'anchors'         => $anchors,
			'protected'       => $protected,
			'protected_trees' => $protected_trees,
		];

		// Two degenerate conditions, in both directions - an empty anchor list fails closed and
		// refuses legitimate paths, and a short protected list fails OPEN and removes protection
		// for the rest of the request. $unresolved counts only locations that were configured but
		// did not resolve, so an install that simply does not use one (a blank gallerypath, no
		// mu-plugins directory) is not degraded at all.
		$complete = ! empty( $anchors ) && 0 === $unresolved;

		// A degenerate set is still returned and used for this request - refusing outright would
		// blank every image - so the degradation has to be logged. It fails open on the protected
		// lists (a subtree that did not resolve stops being refused), and that is the one direction
		// nothing else can report: the missing refusal has no refusal to log. The empty-anchors
		// direction is already visible through REFUSAL_GALLERY_OUTSIDE.
		if ( ! $complete ) {
			$this->log_path_refusal(
				sprintf(
					'NextGEN Gallery: gallery boundary set is incomplete (%d configured location(s) did not resolve, %d anchor(s) found) - containment and protected-directory checks are degraded for this request',
					$unresolved,
					count( $anchors )
				),
				self::REFUSAL_DIRS_DEGENERATE
			);
		}

		// Cached whether or not the set is complete. The static is per-request, and every input is
		// an install constant for the blog, so the condition that produced a degraded set cannot
		// change before the request ends - there is nothing for a stale entry to outlive.
		//
		// Skipping the cache for a degraded set was worse in two ways. This set is now read on the
		// render path (is_path_within_gallery_dir() consults it twice per image per size, once for
		// the anchors and once for the protected trees), so a degraded install paid a dozen
		// realpath walks per image per size and gallery pages could time out - and it degrades
		// precisely on cloud-offload installs, where wp_normalize_path() keeps the s3:// / iu://
		// wrapper and canonicalize_path() then fails closed on a path with no leading slash, so
		// $unresolved is pinned at 1 for the whole request. It also meant the degradation was
		// logged on every call rather than once, which the throttle then had to absorb.
		self::$deletion_dirs_cache[ $blog ] = $dirs;

		return $dirs;
	}

	/**
	 * Whether $dir sits strictly inside one of the legitimate gallery location anchors (and is not
	 * one of the anchors itself).
	 *
	 * @param string $dir Canonicalized directory.
	 * @return bool
	 */
	private function is_within_gallery_locations( $dir ) {
		foreach ( $this->get_deletion_dirs()['anchors'] as $anchor ) {
			if ( $dir !== rtrim( $anchor, '/' ) && $this->path_contains( $anchor, $dir ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether $dir is one of the shared/system directories that must never be treated as a
	 * gallery: the storage root, the WordPress and NextGEN upload bases, the plugin/mu-plugin/theme
	 * roots, wp-content and the document root.
	 *
	 * @param string $dir Canonicalized directory.
	 * @return bool
	 */
	private function is_protected_directory( $dir ) {
		if ( '' === $dir ) {
			return true;
		}

		$dirs = $this->get_deletion_dirs();

		foreach ( $dirs['protected'] as $protected_dir ) {
			if ( $dir === rtrim( $protected_dir, '/' ) ) {
				return true;
			}
		}

		// Code directories are protected as whole subtrees - see get_deletion_dirs().
		//
		// Unconditional, deliberately. An earlier revision exempted directories inside the site's
		// own storage root, so that a Gallery Path pointing into a theme or plugin directory would
		// keep rendering. That exemption was both dead and dangerous: get_gallery_root() is derived
		// from the NGG_GALLERY_ROOT_TYPE *constant* and is therefore always WP_CONTENT_DIR or the
		// document root - never a code directory - so it could not fire; and widening it to the
		// `gallerypath` setting, which is the value that can actually point into a theme, would
		// have handed anyone with the NextGEN settings capability a read of every plugin and theme
		// file through the unauthenticated /nextgen-image/ route. A gallery inside a code directory
		// is refused here and the refusal is logged.
		foreach ( $dirs['protected_trees'] as $protected_tree ) {
			if ( $dir === rtrim( $protected_tree, '/' ) || $this->path_contains( $protected_tree, $dir ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Canonicalizes a path by realpath()-ing its deepest existing ancestor, then re-appending any
	 * missing tail (e.g. a thumbnail that was never generated) so the check does not require the
	 * file itself to exist.
	 *
	 * The existence walk runs on the raw path, so realpath() resolves symlinks in the existing
	 * portion before comparison — a symlinked directory followed by ".." cannot slip through. Any
	 * ".." left in the non-existent tail is collapsed lexically afterwards.
	 *
	 * @param string $path A filesystem path.
	 * @return string The canonicalized forward-slash path, or '' when it cannot be resolved.
	 */
	protected function canonicalize_path( $path ) {
		$path = wp_normalize_path( (string) $path );
		if ( '' === $path ) {
			return '';
		}

		// Fail closed for any path that is not absolute. A relative or driveless path would otherwise
		// be resolved by realpath() against the process working directory, producing a misleading
		// absolute path. Every caller passes an absolute path (a leading "/", including UNC "//", or a
		// drive letter on Windows).
		if ( '/' !== $path[0] && ! preg_match( '#^[A-Za-z]:/#', $path ) ) {
			return '';
		}

		$tail    = [];
		$current = $path;

		// Walk up to the deepest existing component so realpath() can resolve it (symlinks included).
		while ( '' !== $current && ! @file_exists( $current ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$slash = strrpos( $current, '/' );
			if ( false === $slash ) {
				$tail[]  = $current;
				$current = '';
				break;
			}
			$tail[]  = substr( $current, $slash + 1 );
			$current = substr( $current, 0, $slash );
			if ( '' === $current ) {
				// Reached the filesystem root: stop so realpath() below resolves it (or fails
				// closed). Without this, a root that @file_exists() reports missing (e.g. an
				// open_basedir that excludes "/") would loop forever.
				$current = '/';
				break;
			}
		}

		if ( '' === $current ) {
			return '';
		}

		$real = @realpath( $current ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( false === $real ) {
			return '';
		}

		$real = wp_normalize_path( $real );
		if ( $tail ) {
			$real = rtrim( $real, '/' ) . '/' . implode( '/', array_reverse( $tail ) );
		}

		// Collapse any ".." that survived in the non-existent tail.
		return $this->collapse_path_traversal( $real );
	}

	/**
	 * Whether $path equals, or is contained within, the directory $base.
	 *
	 * @param string $base Base directory (canonicalized).
	 * @param string $path Path to test (canonicalized).
	 * @return bool
	 */
	protected function path_contains( $base, $path ) {
		$base = rtrim( $base, '/' );

		// A base that reduces to the filesystem root ("/") is too wide to be a boundary: every
		// absolute path would match. Treat it as no containment.
		if ( '' === $base ) {
			return false;
		}

		return $path === $base || strpos( $path, $base . '/' ) === 0;
	}

	/**
	 * Collapses "." and ".." segments in a "/" normalized path.
	 *
	 * Works on "/" (the separator wp_normalize_path() always produces) instead of the platform
	 * DIRECTORY_SEPARATOR, so traversal is resolved on Windows too.
	 *
	 * @param string $path A wp_normalize_path()'d path.
	 * @return string
	 */
	protected function collapse_path_traversal( $path ) {
		// wp_normalize_path() preserves a leading "//" for UNC/network-share paths; keep it so the
		// collapsed form still matches paths compared against it (and a UNC path stays distinct from
		// its single-slash counterpart).
		$is_unc      = ( 0 === strpos( $path, '//' ) );
		$is_absolute = ( '' !== $path && '/' === $path[0] );
		$segments    = [];

		foreach ( explode( '/', $path ) as $segment ) {
			if ( '' === $segment || '.' === $segment ) {
				continue;
			}
			if ( '..' === $segment ) {
				array_pop( $segments );
				continue;
			}
			$segments[] = $segment;
		}

		$prefix = $is_unc ? '//' : ( $is_absolute ? '/' : '' );

		return $prefix . implode( '/', $segments );
	}

	/**
	 * Outputs/renders an image
	 *
	 * @param Image $image
	 * @return bool
	 */
	public function render_image( $image, $size = false ) {
		$format_list = $this->get_image_format_list();
		$abspath     = $this->get_image_abspath( $image, $size, true );

		if ( $abspath == null ) {
			$thumbnail = $this->generate_image_size( $image, $size );

			if ( $thumbnail != null ) {
				$abspath = $thumbnail->fileName;

				$thumbnail->destruct();
			}
		}

		if ( $abspath != null ) {
			$data   = @getimagesize( $abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$format = 'jpg';

			if ( $data != null && is_array( $data ) && isset( $format_list[ $data[2] ] ) ) {
				$format = $format_list[ $data[2] ];
			}

			// Clear output.
			while ( ob_get_level() > 0 ) {
				ob_end_clean();
			}

			$format = strtolower( $format );

			// output image and headers.
			header( 'Content-type: image/' . $format );
			readfile( $abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile

			return true;
		}

		return false;
	}

	/**
	 * Recover image from backup copy and reprocess it
	 *
	 * @param Image $image
	 * @return bool|string result code
	 */
	public function recover_image( $image ) {
		$retval = false;

		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( $image ) {
			$full_abspath   = $this->get_image_abspath( $image );
			$backup_abspath = $this->get_image_abspath( $image, 'backup' );

			if ( $backup_abspath != $full_abspath && @file_exists( $backup_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( \wp_is_writable( $full_abspath ) && \wp_is_writable( dirname( $full_abspath ) ) ) {
					// Copy the backup.
					if ( @copy( $backup_abspath, $full_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						// Backup images are not altered at all; we must re-correct the EXIF/Orientation tag.
						$this->correct_exif_rotation( $image, true );

						// Re-create non-fullsize image sizes.
						foreach ( $this->get_image_sizes( $image ) as $named_size ) {
							// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							if ( in_array( $named_size, [ 'full', 'backup' ] ) ) {
								continue;
							}

							// Reset thumbnail cropping set by 'Edit thumb' dialog.
							if ( $named_size === 'thumbnail' ) {
								unset( $image->meta_data[ $named_size ]['crop_frame'] );
							}

							$thumbnail = $this->generate_image_clone(
								$full_abspath,
								$this->get_image_abspath( $image, $named_size ),
								$this->get_image_size_params( $image, $named_size )
							);
							if ( $thumbnail ) {
								$thumbnail->destruct();
							}
						}

						do_action( 'ngg_recovered_image', $image );

						// Reimport all metadata.
						$retval = $this->image_mapper->reimport_metadata( $image );
					}
				}
			}
		}

		return $retval;
	}

	/**
	 * Copies a NGG image to the media library and returns the attachment_id
	 *
	 * @param Image $image
	 * @return false|int attachment_id
	 */
	public function copy_to_media_library( $image ) {
		$retval = false;

		// Resolve the image unless an entity was handed over already. A non-scalar is refused
		// rather than cast: casting an object to int warns and evaluates to 1, which would answer
		// the authorization question below about a row the caller did not name.
		if ( ! $image instanceof Image ) {
			$image = is_scalar( $image )
				? ImageMapper::get_instance()->find( (int) $image )
				: null;
		}

		if ( ! $image ) {
			return $retval;
		}

		// This is the sink: it copies the gallery file into wp-content/uploads and inserts an
		// attachment row, which republishes it at a public URL. The check lives here rather than
		// only in set_post_thumbnail() because the deprecated POPE shim C_Gallery_Storage exposes
		// this method directly to third-party code, so gating only the caller left a way around
		// it.
		if ( ! self::current_user_can_attach_image( $image ) ) {
			return $retval;
		}

		if ( $image ) {
			$subdir = apply_filters( 'ngg_import_to_media_library_subdir', 'nggallery_import' );

			$wordpress_upload_dir = wp_upload_dir();
			$path                 = $wordpress_upload_dir['path'] . DIRECTORY_SEPARATOR . $subdir;

			if ( ! file_exists( $path ) ) {
				wp_mkdir_p( $path );
			}

			$image_abspath = $this->get_image_abspath( $image, 'full' );
			$new_file_path = $path . DIRECTORY_SEPARATOR . $image->filename;

			$image_data    = getimagesize( $image_abspath );
			$new_file_mime = $image_data['mime'];

			$i = 1;
			while ( file_exists( $new_file_path ) ) {
				++$i;
				$new_file_path = $path . DIRECTORY_SEPARATOR . $i . '-' . $image->filename;
			}

			if ( @copy( $image_abspath, $new_file_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$upload_id = wp_insert_attachment(
					[
						'guid'           => $new_file_path,
						'post_mime_type' => $new_file_mime,
						'post_title'     => preg_replace( '/\.[^.]+$/', '', $image->alttext ),
						'post_content'   => '',
						'post_status'    => 'inherit',
					],
					$new_file_path
				);

				update_post_meta( $upload_id, '_ngg_image_id', intval( $image->pid ) );

				// wp_generate_attachment_metadata() comes from this file.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				$image_meta = wp_generate_attachment_metadata( $upload_id, $new_file_path );

				// Generate and save the attachment metas into the database.
				wp_update_attachment_metadata( $upload_id, $image_meta );

				$retval = $upload_id;
			}
		}

		return $retval;
	}

	/**
	 * Delete the given NGG image from the media library
	 *
	 * @var int|stdClass $imageId
	 */
	public function delete_from_media_library( $imageId ) {
		// Get the image.
		if ( ! is_int( $imageId ) ) {
			$image   = $imageId;
			$imageId = $image->pid;
		}

		$postId = $this->is_in_media_library( $imageId );
		if ( $postId ) {
			wp_delete_post( $postId );
		}
	}

	/**
	 * Determines if the given NGG image id has been uploaded to the media library
	 *
	 * @param integer $imageId
	 * @return false|int attachment_id
	 */
	public function is_in_media_library( $imageId ) {
		$retval = false;

		// Get the image.
		if ( is_object( $imageId ) ) {
			$image   = $imageId;
			$imageId = $image->pid;
		}

		// Try to find an attachment for the given image_id.
		if ( $imageId ) {
			$query = new \WP_Query(
				[
					'post_type'      => 'attachment',
					'meta_key'       => '_ngg_image_id',
					'meta_value_num' => $imageId,
				]
			);

			foreach ( $query->get_posts() as $post ) {
				$retval = $post->ID;
			}
		}

		return $retval;
	}

	/**
	 * Checks if the image extension is allowed.
	 *
	 * @param string $filename
	 * @return bool
	 */
	public function is_allowed_image_extension( $filename ) {
		$extension = strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) );

		/*
		 * The loop variable inside normalize_allowed_extensions() must not be named
		 * $extension: the original reused it, overwriting the extension under test with the
		 * last accepted one, so in_array() compared an accepted value against a list
		 * containing it and this gate returned true for every filename, "pwn.php" included.
		 * It is the only extension check on the entries extract_zip() pulls out of an
		 * uploaded archive.
		 */
		if ( '' === $extension ) {
			return false;
		}

		$allowed = self::normalize_allowed_extensions(
			apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES )
		);

		return in_array( $extension, $allowed, true );
	}

	/**
	 * Normalizes the result of the ngg_allowed_file_types filter into a lowercase list of
	 * extensions plus their "_backup" companions.
	 *
	 * NGG_DEFAULT_ALLOWED_FILE_TYPES is a comma-separated string; it only arrives here as an array
	 * because nggallery.php registers a filter at priority -10 that explodes it. A filter running
	 * earlier than that, or a call made before it is registered, still hands us the string - so the
	 * string form is split here rather than cast. Casting it would produce a single element holding
	 * the whole list, which no real extension can match, silently rejecting every file.
	 *
	 * @param mixed $allowed_extensions Filter result: array of extensions, or a comma-separated string.
	 * @return string[]
	 */
	private static function normalize_allowed_extensions( $allowed_extensions ) {
		if ( is_string( $allowed_extensions ) ) {
			$allowed_extensions = explode( ',', $allowed_extensions );
		} elseif ( ! is_array( $allowed_extensions ) ) {
			$allowed_extensions = [];
		}

		$allowed = [];

		// The loop variable must not be named $extension: the original reused it and so overwrote
		// the extension being tested with the last allowed one, making every filename - including
		// "shell.php" - compare equal and this check pass unconditionally.
		foreach ( $allowed_extensions as $allowed_extension ) {
			if ( ! is_string( $allowed_extension ) && ! is_numeric( $allowed_extension ) ) {
				continue;
			}
			$allowed_extension = strtolower( trim( (string) $allowed_extension ) );
			if ( '' === $allowed_extension ) {
				continue;
			}
			$allowed[] = $allowed_extension;
			$allowed[] = $allowed_extension . '_backup';
		}

		return $allowed;
	}

	public function is_current_user_over_quota() {
		$retval   = false;
		$settings = Settings::get_instance();

		if ( ( is_multisite() ) && $settings->get( 'wpmuQuotaCheck' ) ) {
			require_once ABSPATH . 'wp-admin/includes/ms.php';
			$retval = upload_is_user_over_quota( false );
		}

		return $retval;
	}

	/**
	 * Checks if the file is an image file.
	 *
	 * @param string? $filename
	 * @return bool
	 */
	public function is_image_file( $filename = null ): bool {
		$retval = false;

		// Security::verify_nonce() is a wrapper to wp_verify_nonce().
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens below
		if ( ! $filename
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens below
			&& isset( $_FILES['file']['error'] )
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens below
			&& isset( $_FILES['file']['tmp_name'] )
			&& 0 === $_FILES['file']['error'] ) {

			// Windows' use of backslash characters for file paths means wp_unslash() here is destructive.
			if ( 0 === strncasecmp( PHP_OS, 'WIN', 3 ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$filename = sanitize_text_field( $_FILES['file']['tmp_name'] );
			} else {
				$filename = sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$allowed_mime = apply_filters( 'ngg_allowed_mime_types', NGG_DEFAULT_ALLOWED_MIME_TYPES );

		// If we can, we'll verify the mime type.
		if ( function_exists( 'exif_imagetype' ) ) {
			$image_type = @exif_imagetype( $filename ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( $image_type !== false ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				$retval = in_array( image_type_to_mime_type( $image_type ), $allowed_mime );
			}
		} else {
			$file_info = @getimagesize( $filename ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( isset( $file_info[2] ) ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				$retval = in_array( image_type_to_mime_type( $file_info[2] ), $allowed_mime );
			}
		}

		return $retval;
	}

	public function is_zip( bool $skip_nonce_check = false ): bool {
		$retval = false;

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_FILES['file']['error'] ) && 0 === $_FILES['file']['error'] ) {
			// Check nonce only if not skipping (for non-REST calls)
			if ( ! $skip_nonce_check ) {
				if ( ! isset( $_REQUEST['nonce'] )
					|| ! Security::verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'nextgen_upload_image' ) ) {
					return false;
				}
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File data cannot be sanitized
			$file_info = $_FILES['file'];

			if ( isset( $file_info['type'] ) ) {
				$type       = $file_info['type'];
				$type_parts = explode( '/', $type );

				if ( strtolower( $type_parts[0] ) == 'application' ) {
					$spec       = $type_parts[1];
					$spec_parts = explode( '-', $spec );
					$spec_parts = array_map( 'strtolower', $spec_parts );

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					if ( in_array( $spec, [ 'zip', 'octet-stream' ] ) || in_array( 'zip', $spec_parts ) ) {
						$retval = true;
					}
				}
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $retval;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- false positive, no user input here
	public function get_unique_abspath( $file_abspath ) {
		$filename    = basename( $file_abspath );
		$dir_abspath = dirname( $file_abspath );
		$num         = 1;

		$pattern = path_join( $dir_abspath, "*_{$filename}" );
		$found   = glob( $pattern );
		if ( $found ) {
			natsort( $found );
			$last = array_pop( $found );
			$last = basename( $last );
			if ( preg_match( '/^(\d+)_/', $last, $match ) ) {
				$num = intval( $match[1] ) + 1;
			}
		}

		return path_join( $dir_abspath, "{$num}_{$filename}" );
	}

	/**
	 * Determines whether a WebP image is animated which GD does not support.
	 *
	 * @see https://developers.google.com/speed/webp/docs/riff_container
	 * @param string $filename
	 * @return bool
	 */
	public function is_animated_webp( $filename ) {
		$retval = false;
		$handle = fopen( $filename, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fseek( $handle, 12 );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		if ( fread( $handle, 4 ) === 'VP8X' ) {
			fseek( $handle, 20 );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
			$flag   = fread( $handle, 1 );
			$retval = (bool) ( ( ( ord( $flag ) >> 1 ) & 1 ) );
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		fclose( $handle );

		return $retval;
	}

	public function import_image_file( $dst_gallery, $image_abspath, $filename = null, $image = false, $override = false, $move = false ) {
		$image_abspath = wp_normalize_path( $image_abspath );

		if ( $this->is_current_user_over_quota() ) {
			$message = sprintf( __( 'Sorry, you have used your space allocation. Please delete some files to upload more files.', 'nggallery' ) );
			throw new \E_NoSpaceAvailableException( esc_html( $message ) );
		}

		// Do we have a gallery to import to?
		if ( $dst_gallery ) {
			// Get the gallery abspath. This is where we will put the image files.
			$gallery_abspath = $this->get_gallery_abspath( $dst_gallery );

			// If we can't write to the directory, then there's no point in continuing.
			if ( ! @file_exists( $gallery_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@wp_mkdir_p( $gallery_abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}
			if ( ! \wp_is_writable( $gallery_abspath ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Path is escaped in exception constructor
				throw new \E_InsufficientWriteAccessException( false, esc_html( $gallery_abspath ), false );
			}

			// Sanitize the filename for storing in the DB.
			$filename = $this->sanitize_filename_for_db( $filename );

			// Ensure that the filename is valid. This uses the same allow-list as ZIP extraction and
			// thumbnail generation rather than its own pattern. The pattern it replaces appended a
			// bare "_backup" alternative instead of per-extension "jpg_backup"/"png_backup", and was
			// anchored only at the end with no separating dot - so "shell.php_backup" matched the
			// bare alternative and "evilpng" matched "png", and both were copied into the gallery
			// directory below.
			if ( ! $this->is_allowed_image_extension( $filename ) ) {
				throw new \E_UploadException(
					esc_html(
						sprintf(
							/* translators: %s: comma-separated list of accepted image formats, e.g. "JPEG, JPG, PNG, GIF, WEBP". */
							__( 'Invalid image file. Acceptable formats: %s.', 'nggallery' ),
							ngg_get_allowed_formats_label()
						)
					)
				);
			}
			// GD does not support animated WebP and will generate a fatal error when we try to create thumbnails or resize.
			if ( $this->is_animated_webp( $image_abspath ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Translated string is safe
				throw new \E_UploadException( __( 'Animated WebP images are not supported.', 'nggallery' ) );
			}

			// Compute the destination folder.
			$new_image_abspath = path_join( $gallery_abspath, $filename );

			// Are the src and dst the same? If so, we don't have to copy or move files.
			// Recorded for the rollback below: a "keep files in place" import points the gallery
			// directory at the customer's own folder, so src and dst already match here and this
			// call never copies anything in - the full-size file on disk is the customer's own.
			$copied_full_size = ( $image_abspath != $new_image_abspath );
			if ( $copied_full_size ) {
				// If we're not to override, ensure that the filename is unique.
				if ( ! $override && @file_exists( $new_image_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					$new_image_abspath = $this->get_unique_abspath( $new_image_abspath );
					$filename          = $this->sanitize_filename_for_db( basename( $new_image_abspath ) );
				}

				// Try storing the file.
				$copied = copy( $image_abspath, $new_image_abspath );
				if ( $copied && $move ) {
					unlink( $image_abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
				}

				// Ensure that we're not vulerable to CVE-2017-2416 exploit.
				$dimensions = getimagesize( $new_image_abspath );
				if ( $dimensions !== false ) {
					if ( ( isset( $dimensions[0] ) && intval( $dimensions[0] ) > 30000 )
						|| ( isset( $dimensions[1] ) && intval( $dimensions[1] ) > 30000 ) ) {
						unlink( $new_image_abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
						throw new \E_UploadException( esc_html( __( 'Image file too large. Maximum image dimensions supported are 30k x 30k.', 'nggallery' ) ) );
					}
				}
			}

			// Save the image in the DB.
			$image_mapper            = ImageMapper::get_instance();
			$image_mapper->use_cache = false;
			if ( $image ) {
				if ( is_numeric( $image ) ) {
					$image = $image_mapper->find( $image );
				}
			}

			// A re-publish sends a filename with no id. Where ngg_pictures carries the UNIQUE
			// index unique_gallery_filename (galleryid, filename) the insert is then refused by
			// the database ("Duplicate entry '<gid>-<file>'"), which is what made a Lightroom
			// re-publish fail the whole job - so adopt the row it would collide with and update
			// it instead.
			//
			// Gated on $override, which is what "replace this file" means: only a caller that
			// asked to override should update an existing row rather than insert. Callers that
			// pass false (Scan Folder, the pro Dropbox/Video importers) keep their old
			// behaviour, which matters because the index is not present everywhere - #941 defers
			// the migration on already-populated tables, and such a table can legitimately hold
			// several rows with the same (galleryid, filename).
			//
			// Ordered by pid so the adopted row is deterministic on those tables; find_first()
			// has no ORDER BY and would return whichever row MySQL surfaced first.
			if ( ! $image && $override ) {
				$gallery_id_for_lookup = is_numeric( $dst_gallery ) ? (int) $dst_gallery : (int) $dst_gallery->gid;
				$existing              = $image_mapper->select()
					->where_and(
						[
							[ 'filename = %s', $filename ],
							[ 'galleryid = %d', $gallery_id_for_lookup ],
						]
					)
					->order_by( 'pid', 'ASC' )
					->limit( 1, 0 )
					->run_query();

				if ( ! empty( $existing[0] ) ) {
					$image = $image_mapper->convert_to_model( $existing[0] );
				}
			}

			$is_new = ! $image;
			if ( $is_new ) {
				$image = $image_mapper->create();
			}
			// Seed alttext/slug for new images only; replacing an existing image (e.g. a Lightroom republish) must not reset user-entered values (#976).
			if ( $is_new || empty( $image->alttext ) ) {
				$image->alttext = preg_replace( '#\.\w{2,4}$#', '', $filename );
			}
			if ( $is_new || empty( $image->image_slug ) ) {
				$image->image_slug = \nggdb::get_unique_slug( sanitize_title_with_dashes( $image->alttext ), 'image' );
			}
			$image->galleryid = is_numeric( $dst_gallery ) ? $dst_gallery : $dst_gallery->gid;
			$image->filename  = $filename;
			$image_id         = $image_mapper->save( $image );

			if ( ! $image_id ) {
				$exception  = '';
				$validation = $image->validation();
				if ( is_array( $validation ) ) {
					foreach ( $validation as $field => $errors ) {
						foreach ( $errors as $error ) {
							if ( ! empty( $exception ) ) {
								$exception .= '<br/>';
							}
							/* translators: 1: filename, 2: error message */
							$exception .= sprintf( __( 'Error while uploading %1$s: %2$s', 'nggallery' ), esc_html( $filename ), esc_html( $error ) );
						}
					}
					// Exception message contains HTML for formatting and is handled by WordPress error handlers.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.Security.EscapeOutput.ExceptionNotEscaped
					throw new \E_UploadException( $exception );
				}

				// A falsy save with no validation errors is still a refused write, not a no-op:
				// save_entity() resolves the 0-affected-rows case itself and returns the pid.
				// It used to fall through to find( false ) below, which returned null and left
				// backup_image() and the size generation dereferencing it - the request died on
				// `Attempt to read property "pid" on null` and the job reported
				// ERR_JOB_NOT_ADDED with nothing naming the real cause.
				throw new \E_UploadException(
					esc_html(
						sprintf(
							/* translators: %s: image filename */
							__( 'Could not save image %s.', 'nggallery' ),
							$filename
						)
					)
				);
			}           // Important: do not remove this line. The image mapper's save() routine imports metadata
			// meaning we must re-acquire a new $image object after saving it above; if we do not our
			// existing $image object will lose any metadata retrieved during said save() method.
			$image = $image_mapper->find( $image_id );

			$image_mapper->use_cache = true;
			$settings                = Settings::get_instance();

			// Only backup is treated as fatal: it's a plain file copy, whose failure means
			// disk-full or permissions. Rotation, resize and thumbnail all construct a
			// LegacyThumbnail and share one memory-limit cliff (#711/#994), and resize runs
			// first, so making any of them fatal would reject the whole image for a derivative
			// that didn't render - which is the #711 shape: a valid, very large photo whose
			// full size imported fine and only its thumbnail failed.
			//
			// This is about what stays on disk, not what the caller is told. The row and file
			// are kept; reporting is the caller's business, and callers do surface it - e.g.
			// ImageREST::upload_image() sets $retval['error'] for a missing thumbnail, which
			// becomes an HTTP 500 there. What it does not do is undo the import, and neither
			// does this.
			$post_save_failure = null;

			// Whether a _backup file was already sitting there before this call ran. A "keep
			// files in place" folder import points the gallery directory at the customer's own
			// folder, which can already hold photo.jpg_backup from an earlier resize - the
			// import loop branches on exactly that name - and with imgBackup off nothing in this
			// request would ever touch it. The rollback must not delete a file this call didn't
			// create, so it needs to know the difference before backup_image() can blur it.
			$backup_abspath     = $this->get_backup_abspath( $image );
			$backup_preexisting = $backup_abspath && @file_exists( $backup_abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			try {
				// Backup the image.
				if ( $settings->get( 'imgBackup', false ) && ! $this->backup_image( $image, true ) ) {
					$post_save_failure = __( 'Could not back up the original image.', 'nggallery' );
				}

				// Most browsers do not honor EXIF's Orientation header: rotate the image to prevent display issues.
				if ( null === $post_save_failure && ! $this->correct_exif_rotation( $image, true ) ) {
					error_log( 'NextGEN Gallery: could not correct EXIF rotation for image #' . $image_id ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}

				// Create resized version of image.
				if ( null === $post_save_failure && $settings->get( 'imgAutoResize', false ) && ! $this->generate_resized_image( $image, true ) ) {
					error_log( 'NextGEN Gallery: could not resize image #' . $image_id ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}

				// Generate a thumbnail for the image.
				if ( null === $post_save_failure && ! $this->generate_thumbnail( $image ) ) {
					error_log( 'NextGEN Gallery: could not generate a thumbnail for image #' . $image_id ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}
			} catch ( \Exception $e ) {
				// Resolved through Sanitization::exception_message() rather than getMessage():
				// E_EntityNotFoundException is an empty class thrown bare, so its message is ''
				// and the exception below would read "Could not finish importing x:" with no
				// reason after the colon.
				$post_save_failure = Sanitization::exception_message( $e, __( 'The image could not be imported.', 'nggallery' ) );
				error_log( 'NextGEN Gallery: import post-processing failed for image #' . $image_id . ': ' . $post_save_failure ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}

			if ( null !== $post_save_failure ) {
				// A pre-existing row - a Lightroom re-publish, or a "keep files in place" import
				// that adopted a row via $override - holds data (other sizes, captions, a source
				// file this call never copied) that this failure didn't create and has no business
				// erasing. Only a row this call created itself is rolled back.
				if ( $is_new ) {
					$rollback_ok = true;

					// Deliberately not gated on backup_image() having returned true: a failed
					// copy() can still leave a truncated destination behind (ENOSPC is exactly
					// that), and that failure is itself the usual reason we are here. It *is*
					// gated on the file not having pre-existed this call, which is the ownership
					// question - the same one $copied_full_size answers for the full size.
					if ( ! $backup_preexisting && $backup_abspath && @file_exists( $backup_abspath ) && $this->is_removable_image_path( $backup_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						@unlink( $backup_abspath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
						$rollback_ok = ! @file_exists( $backup_abspath ) && $rollback_ok; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					}

					// Keyed on the meta generate_image_size() persists for this row, rather than
					// on a local flag: the flag could only be set on the no-failure branch, so it
					// was always false by the time this ran and the thumbnail a throw left behind
					// was never cleaned up. The meta is also the pid-safe discriminator - without
					// it, resolving this size falls back to a filesystem probe keyed on (gallery,
					// filename) and can return a *different*, live row's thumbnail.
					if ( ! empty( $image->meta_data['thumbnail']['filename'] ) ) {
						$thumbnail_abspath = $this->get_image_abspath( $image, 'thumbnail', true );
						$rollback_ok       = $this->delete_image( $image, 'thumbnail' ) && $rollback_ok;
						$rollback_ok       = ( ! $thumbnail_abspath || ! @file_exists( $thumbnail_abspath ) ) && $rollback_ok; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					}

					// The full-size file is only this call's to remove when this call is the one
					// that copied it in - see $copied_full_size above. A "keep files in place"
					// folder import points the gallery directory at the customer's own folder and
					// never copies, so unlinking the full size here would delete their own photo.
					if ( $copied_full_size ) {
						$full_abspath = $this->get_image_abspath( $image, 'full', true );
						$rollback_ok  = $this->delete_image( $image, 'full' ) && $rollback_ok;
						$rollback_ok  = ( ! $full_abspath || ! @file_exists( $full_abspath ) ) && $rollback_ok; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					}

					if ( ! $rollback_ok ) {
						// A file survived one of the unlinks above (delete_image() doesn't check
						// unlink()'s own return, so its true/false can't be trusted alone) or the
						// gallery directory falls outside the bounds it will act within at all - a
						// supported configuration. Leaving the row in place keeps the orphan visible
						// in Manage Gallery for a manual look, rather than destroying the only
						// record that it exists while its files remain on disk.
						error_log( 'NextGEN Gallery: could not fully clean up files for image #' . $image_id . ' after a failed import - leaving the record in place so the orphan stays visible.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					} elseif ( $image_mapper->destroy( $image ) ) {
						// Fired only after the row is actually gone (delete_image() fires it
						// per-size above) so third-party listeners still get the "this image is
						// gone" signal the no-size call used to send. Firing it before destroy()
						// meant a failed destroy told listeners the image was deleted while the
						// row survived.
						do_action( 'ngg_delete_image', $image_id, false );
					} else {
						error_log( 'NextGEN Gallery: removed the files for image #' . $image_id . ' after a failed import but could not remove its record - it may need manual cleanup.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					}
				}
				throw new \E_UploadException(
					esc_html(
						sprintf(
							/* translators: 1: image filename, 2: reason the import could not be completed */
							__( 'Could not finish importing %1$s: %2$s', 'nggallery' ),
							$filename,
							$post_save_failure
						)
					)
				);
			}

			// Set gallery preview image if missing.
			GalleryMapper::get_instance()->set_preview_image( $dst_gallery, $image_id, true );

			// Automatically watermark the main image if requested.
			if ( $settings->get( 'watermark_automatically_at_upload', 0 ) ) {
				$image_abspath = $this->get_image_abspath( $image, 'full' );
				$this->generate_image_clone( $image_abspath, $image_abspath, [ 'watermark' => true ] );
			}

			// Ensure the Pope framework is loaded before this action fires. Third-party plugins
			// such as ShortPixel hook here and instantiate legacy Pope classes (e.g. C_Gallery_Storage).
			// On REST upload requests Pope may not have loaded yet because the constructor-time
			// SHORTPIXEL_IMAGE_OPTIMISER_VERSION check runs before sibling plugins are included.
			\C_NextGEN_Bootstrap::load_pope( true );

			// Notify other plugins that an image has been added.
			do_action( 'ngg_added_new_image', $image );

			// delete dirsize after adding new images.
			delete_transient( 'dirsize_cache' );

			// Seems redundant to above hook. Maintaining for legacy purposes.
			do_action(
				'ngg_after_new_images_added',
				is_numeric( $dst_gallery ) ? $dst_gallery : $dst_gallery->gid,
				[ $image_id ]
			);

			return $image_id;

		} else {
			throw new \E_EntityNotFoundException();
		}

		return null;
	}

	/**
	 * Generates a specific size for an image
	 *
	 * @param Image      $image
	 * @param string     $size
	 * @param array|null $params (optional)
	 * @param bool       $skip_defaults (optional)
	 * @return bool|object
	 */
	public function generate_image_size( $image, $size, $params = null, $skip_defaults = false ) {
		$retval = false;

		// Get the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have a valid image.
		if ( $image ) {
			$params   = $this->get_image_size_params( $image, $size, $params, $skip_defaults );
			$settings = Settings::get_instance();

			// Get the image filename.
			$filename = $this->get_image_abspath( $image, 'full' );

			if ( ! $filename || ! @file_exists( $filename ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				return false; // bail out if the file doesn't exist.
			}

			$thumbnail = null;

			if ( $size == 'full' && $settings->get( 'imgBackup' ) == 1 ) {
				$backup_path = $this->get_backup_abspath( $image );

				if ( ! @file_exists( $backup_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					@copy( $filename, $backup_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				}
			}

			// Generate the thumbnail using WordPress.
			$existing_image_abpath = $this->get_image_abspath( $image, $size );

			/*
			 * The destination is built from database values, so it is checked here as
			 * well as in get_computed_image_abspath(): this is the write half of the
			 * pipeline, and it creates directories before it writes. An escaping path
			 * would let a caller who can influence a stored filename choose where a
			 * file lands and what it is called, which is materially worse than the read
			 * it would get out of render_image().
			 *
			 * MERGE NOTE (#932 + #933): both gates run, because neither subsumes the other.
			 *
			 * #933's is_safe_generated_image_path() tests the target's *final* extension
			 * against the unsafe list, the upload allow-list, the source's own extension and
			 * the known-image list. That last pair is what keeps legitimate images
			 * generating: `webp` is only in the upload list when the server has GD WebP
			 * support, and import_image_file() admits names whose accepted extension has no
			 * dot at all ("holidaypng"), so a strict allow-list alone refused real images.
			 *
			 * #932's has_php_executable_extension() tests *every* dot-delimited segment, and
			 * is the only one of the two that catches a legacy "photo.php_.jpg" - whose final
			 * extension is "jpg", so the extension gate admits it. It is narrowed to
			 * PHP_EXTENSION_PATTERN rather than the wide EXECUTABLE_EXTENSION_PATTERN, the
			 * same choice LegacyTemplateLocator makes: the wide pattern matches "pl", "py",
			 * "rb", "sh", "ini", "cgi" and "asp", which are plausible fragments in ordinary
			 * photo names ("brochure.pl.jpg", "menu.py.jpg", "config.ini.jpg").
			 *
			 * The containment half of #932's check is dropped here as redundant rather than
			 * lost: is_safe_generated_image_path() is reached only for a non-null
			 * $existing_image_abpath, and #933 made get_computed_image_abspath() return null
			 * unless the path resolves inside the gallery directory - a stricter test than
			 * Security::contain_path() against get_gallery_abspath(), since it validates the
			 * containment base as well as the target.
			 *
			 * A refusal is no longer a silently broken image: DynamicThumbnails\Controller
			 * answers a failed render with a 404 plus the placeholder, and get_image_url()
			 * substitutes the placeholder for the static URL of a name this refuses.
			 */

			/*
			 * The reason is resolved rather than folded into one condition: the causes want
			 * different actions from a site owner - a missing destination is a broken row, an
			 * escaping or non-image destination is a poisoned one, and a script extension in
			 * the stored filename is fixed by renaming the file. A log line that only said
			 * "an unsafe destination" did not point at any of them.
			 */
			$refusal_reason = null;

			if ( ! $existing_image_abpath ) {
				$refusal_reason = 'the destination path could not be computed or resolves outside the gallery directory';
			} elseif ( ! $this->is_safe_generated_image_path( $existing_image_abpath, $filename ) ) {
				$refusal_reason = sprintf( 'the destination "%s" is not a safe image target', $existing_image_abpath );
			} elseif ( Security::has_php_executable_extension( $existing_image_abpath ) ) {
				$refusal_reason = sprintf( self::REFUSAL_PHP_EXTENSION_NOTICE, wp_basename( $existing_image_abpath ) );
			}

			if ( null !== $refusal_reason ) {
				/*
				 * Through log_path_refusal(): a refused destination is reachable from an
				 * unauthenticated front-end render, and /nextgen-image/ is one request per
				 * thumbnail, so a single refused row - or a repeated attack request - would
				 * otherwise append a line for every visitor request. Every occurrence is
				 * logged under WP_DEBUG; without it, at most one line per hour, which is
				 * enough for support to find a mass refusal on a production site.
				 */
				$this->log_path_refusal(
					sprintf(
						'NextGEN Gallery: refused to generate size "%1$s" for image #%2$s - %3$s',
						(string) $size,
						isset( $image->pid ) ? (string) $image->pid : '?',
						$refusal_reason
					),
					self::REFUSAL_GENERATE_SIZE
				);
				return false;
			}

			$existing_image_dir = dirname( $existing_image_abpath );

			// Ensure directory exists with proper error handling
			if ( ! \wp_mkdir_p( $existing_image_dir ) ) {
				// Fallback: try to create directory with different permissions
				if ( ! @mkdir( $existing_image_dir, 0755, true ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir, WordPress.PHP.NoSilencedErrors.Discouraged
					error_log( 'NextGEN Gallery: Failed to create thumbnail directory: ' . $existing_image_dir );
					return false;
				}
			}

			// Verify directory is writable
			if ( ! \wp_is_writable( $existing_image_dir ) ) {
				// Try to fix permissions
				@chmod( $existing_image_dir, 0755 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod, WordPress.PHP.NoSilencedErrors.Discouraged
				if ( ! \wp_is_writable( $existing_image_dir ) ) {
					error_log( 'NextGEN Gallery: Thumbnail directory not writable: ' . $existing_image_dir );
					return false;
				}
			}

			$clone_path = $existing_image_abpath;

			// A full size already within its bounds is kept rather than re-encoded. It is decided here,
			// after the destination gates, so the skip shares their refusals and the save/error
			// contract below. Callers then get true instead of a thumbnail object.
			if ( 'full' === $size && ! $this->is_resize_needed( $filename, $params ) ) {
				$this->sync_unresized_image_metadata( $filename );
				$thumbnail = true;
			} else {
				$thumbnail = $this->generate_image_clone( $filename, $clone_path, $params );
			}

			// We successfully generated the thumbnail.
			if ( $thumbnail ) {
				if ( is_object( $thumbnail ) ) {
					$clone_path = $thumbnail->fileName;
				}

				// The clone may carry a different extension than the path requested above (the
				// "type" size parameter), so re-check the file actually written. This is a belt
				// check - the path it derives from was already validated before the write - so
				// reaching it means something unexpected changed the target, which is worth a log
				// line. The written file is deliberately NOT unlinked: if the path really did
				// escape the gallery it now names a file this code has no business deleting, and
				// removing it would turn a containment failure into data loss.
				if ( ! $this->is_path_within_gallery_dir( $this->get_gallery_abspath( $image->galleryid ), $clone_path )
					|| ! $this->is_safe_generated_image_path( $clone_path, $filename ) ) {
					$this->log_path_refusal(
						sprintf(
							'NextGEN Gallery: discarded generated size "%s" for image #%s - the written file (%s) is outside the gallery directory or not an allowed image type; it was left in place rather than deleted',
							$size,
							isset( $image->pid ) ? $image->pid : '?',
							$clone_path
						),
						self::REFUSAL_GENERATED_CLONE
					);
					if ( is_object( $thumbnail ) ) {
						$thumbnail->destruct();
					}
					return false;
				}

				if ( function_exists( 'getimagesize' ) ) {
					$dimensions = getimagesize( $clone_path );
				} else {
					$dimensions = [ $params['width'], $params['height'] ];
				}

				if ( ! isset( $image->meta_data ) ) {
					$image->meta_data = [];
				}

				$size_meta = [
					'width'     => $dimensions[0],
					'height'    => $dimensions[1],
					'filename'  => I18N::mb_basename( $clone_path ),
					'generated' => microtime(),
				];

				if ( isset( $params['crop_frame'] ) ) {
					$size_meta['crop_frame'] = $params['crop_frame'];
				}

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
				$image->meta_data[ $size ] = $size_meta;

				if ( $size == 'full' ) {
					$image->meta_data['width']  = $size_meta['width'];
					$image->meta_data['height'] = $size_meta['height'];
				}

				$retval = $this->image_mapper->save( $image );

				\do_action( 'ngg_generated_image', $image, $size, $params );

				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- false positive, no user input here
				if ( $retval == 0 ) {
					$retval = false;
				}

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
				if ( $retval ) {
					$retval = $thumbnail;
				}
			}
		}

		return $retval;
	}

	/**
	 * Generates a thumbnail for an image
	 *
	 * @param Image $image
	 * @return bool
	 */
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- false positive, no user input here
	public function generate_thumbnail( $image, $params = null, $skip_defaults = false ) {
		static $generating = [];

		// Get image ID for recursion tracking
		$image_id = is_numeric( $image ) ? $image : ( isset( $image->pid ) ? $image->pid : 0 );

		// Prevent infinite recursion
		if ( isset( $generating[ $image_id ] ) ) {
			return false;
		}

		$generating[ $image_id ] = true;

		$sized_image = $this->generate_image_size( $image, 'thumbnail', $params, $skip_defaults );
		$retval      = false;

		if ( $sized_image != null ) {
			$retval = true;
			$sized_image->destruct();
		}

		if ( is_admin() ) {
			$image = ImageMapper::get_instance()->find( $image );
			if ( $image ) {
				$app = Router::get_instance()->get_routed_app();

				$image->thumb_url = $app->set_parameter_value(
					'timestamp',
					time(),
					null,
					$this->get_image_url( $image, 'thumb' ),
					$app->get_routed_url( true )
				);

				$event            = new \stdClass();
				$event->pid       = $image->{$image->id_field};
				$event->id_field  = $image->id_field;
				$event->thumb_url = $image->thumb_url;

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
				EventPublisher::get_instance()->add_event(
					[
						'event' => 'thumbnail_modified',
						'image' => $event,
					]
				);
			}
		}

		unset( $generating[ $image_id ] );

		return $retval;
	}

	/**
	 * Gets the absolute path of the backup of an original image
	 *
	 * @param object|string $image
	 * @return null|string
	 */
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- false positive, no user input here
	public function get_backup_abspath( $image ) {
		$retval = null;

		$image_path = $this->get_image_abspath( $image );
		if ( $image_path ) {
			$retval = $image_path . '_backup';
		}

		return $retval;
	}

	/**
	 * Replaces an imported image's backup with the attachment's untouched original.
	 *
	 * WordPress downscales uploads above big_image_size_threshold and serves the
	 * derivative as the attached file. Importing that derivative keeps thumbnail
	 * generation cheap, but it must not be what the gallery keeps as its original,
	 * because the backup is what digital downloads are rendered from.
	 *
	 * Copying on disk rather than importing the original directly matters: a large
	 * original costs its full raster in memory once an image editor opens it, which
	 * is enough to exhaust a modest memory limit.
	 *
	 * Mutates the image's backup metadata; the caller is expected to save it.
	 *
	 * @param Image $image         The freshly imported image.
	 * @param int   $attachment_id The attachment it was imported from.
	 * @return bool Whether the backup was replaced.
	 */
	public function store_attachment_original_as_backup( $image, $attachment_id ) {
		// The caller is expected to save the entity, so it must hand over the entity
		// itself rather than an id -- otherwise the metadata written here is dropped.
		if ( ! is_object( $image ) || ! Settings::get_instance()->get( 'imgBackup', false ) ) {
			return false;
		}

		$original = Filesystem::get_attachment_original_abspath( $attachment_id );
		$attached = get_attached_file( $attachment_id );

		// Nothing to promote when the upload was never downscaled.
		if ( empty( $original ) || $original === $attached || ! @file_exists( $original ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return false;
		}

		$backup_abspath = $this->get_backup_abspath( $image );

		if ( empty( $backup_abspath ) || ! @copy( $original, $backup_abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return false;
		}

		$dimensions = @getimagesize( $backup_abspath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		if ( ! $dimensions ) {
			return false;
		}

		if ( empty( $image->meta_data ) || ! is_array( $image->meta_data ) ) {
			$image->meta_data = [];
		}

		$image->meta_data['backup'] = [
			'filename'  => basename( $backup_abspath ),
			'width'     => $dimensions[0],
			'height'    => $dimensions[1],
			'generated' => microtime(),
		];

		return true;
	}

	public function get_backup_dimensions( $image ) {
		return $this->get_image_dimensions( $image, 'backup' );
	}

	/**
	 * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image
	 *
	 * @param int|object $image
	 * @param string     $size (optional) Default = full
	 * @param bool       $check_existence (optional) Default = false
	 * @return string
	 */
	public function get_image_abspath( $image, $size = 'full', $check_existence = false ) {
		$image_id = is_numeric( $image ) ? $image : $image->pid;
		$size     = $this->normalize_image_size_name( $size );
		$key      = strval( $image_id ) . $size;

		if ( $check_existence || ! isset( self::$image_abspath_cache[ $key ] ) ) {
			self::$image_abspath_cache[ $key ] = $this->get_computed_image_abspath( $image, $size, $check_existence );
		}

		return self::$image_abspath_cache[ $key ];
	}

	/**
	 * Gets the canonical url of a particular-sized image.
	 *
	 * This is the crawler-facing URL: no cache-busting parameter, so links, sitemaps,
	 * feeds and XML-RPC stay stable (#829). Display sinks want
	 * get_cache_busted_image_url() instead.
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @return string
	 */
	public function get_image_url( $image, $size = 'full' ) {
		return apply_filters( 'ngg_get_image_url', $this->resolve_image_url( $image, $size ), $image, $size );
	}

	/**
	 * Resolves an image url, before the ngg_get_image_url filter runs.
	 *
	 * Both public accessors share this so each can decide where the cache-buster goes
	 * relative to the filter.
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @return string|null
	 */
	private function resolve_image_url( $image, $size = 'full' ) {
		$retval   = null;
		$image_id = is_numeric( $image ) ? $image : $image->pid;
		$key      = strval( $image_id ) . $size;
		$success  = true;

		if ( ! isset( self::$image_url_cache[ $key ] ) ) {
			$url = $this->get_computed_image_url( $image, $size );
			if ( $url ) {
				self::$image_url_cache[ $key ] = $url;
				$success                       = true;
			} else {
				$success = false;
			}
		}
		if ( $success ) {
			$retval = self::$image_url_cache[ $key ];
		} else {
			$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
			if ( $dynthumbs->is_size_dynamic( $size ) ) {
				$params = $dynthumbs->get_params_from_name( $size );
				$retval = Router::get_instance()->get_url(
					$dynthumbs->get_image_uri( $image, $params ),
					false,
					'root'
				);
			}
		}

		// Fallback: If thumbnail URL is still null and we're requesting a thumbnail,
		// try to generate it on-the-fly (useful for fresh installations)
		if ( ! $retval && ( $size === 'thumbnail' || $size === 'thumb' ) ) {
			// Attempt to generate the thumbnail
			if ( $this->generate_thumbnail( $image ) ) {
				// Clear the cache and try again
				unset( self::$image_url_cache[ $key ] );
				$url = $this->get_computed_image_url( $image, $size );
				if ( $url ) {
					self::$image_url_cache[ $key ] = $url;
					$retval                        = $url;
				}
			}
		}

		/*
		 * A size whose destination is permanently refused never gets a file, and for a
		 * named size get_computed_image_url() still hands back the static gallery URL it
		 * would have had. That URL 404s as the site's HTML error page, so the markup
		 * carries an <img> the browser can only draw as a broken-image icon - the visible
		 * symptom behind #1009.
		 *
		 * The placeholder is substituted here rather than left to the request, because
		 * nothing serves that path through PHP: it is a static file that does not exist.
		 * The dynamic route answers its own refusals in DynamicThumbnails\Controller;
		 * this is the same outcome for the static half, so both populations readme.txt
		 * tells to expect a placeholder actually get one.
		 *
		 * Deliberately narrow: only a refused *executable-extension* destination whose
		 * file is genuinely absent is substituted. An ordinary not-yet-generated size is
		 * untouched, and so is any size already on disk.
		 */
		if ( $retval ) {
			$candidate = $this->get_image_abspath( $image, $size );

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( $candidate && ! @file_exists( $candidate ) && Security::has_php_executable_extension( $candidate ) ) {
				return StaticAssets::get_url( 'DynamicThumbnails/invalid_image.png' );
			}
		}

		return $retval;
	}

	/**
	 * Gets the url of a particular-sized image with a cache-busting query parameter appended.
	 *
	 * For display sinks: an <img src>, or a lightbox data-src / data-thumbnail attribute, on
	 * either side of the admin boundary. NGG overwrites images in place, so a visitor whose
	 * browser already cached the file needs a changed URL to refetch it after an editor
	 * rotates, crops or recovers the image.
	 *
	 * Crawler-facing output must keep using get_image_url(): a query string that changes on
	 * every save gets crawled and indexed as a separate URL, polluting sitemaps and Search
	 * Console (#829). That means <a href>, the XML sitemap, feeds and XML-RPC stay canonical.
	 *
	 * @param int|object $image Image ID or entity. A cache-buster is only added for entities.
	 * @param string     $size  (optional) Default = full.
	 * @return string|null
	 */
	public function get_cache_busted_image_url( $image, $size = 'full' ) {
		$retval = $this->resolve_image_url( $image, $size );

		if ( $retval && is_object( $image ) && ! empty( $image->updated_at ) ) {
			$retval = \add_query_arg( 't', $image->updated_at, $retval );
		}

		// Bust before filtering, as this did prior to #829: a filter that replaces the URL
		// wholesale (the Envira CDN, Pro's Instagram/Dribbble) keeps control of its own output.
		return apply_filters( 'ngg_get_image_url', $retval, $image, $size );
	}

	/**
	 * Flushes the cache we use for path/url calculation for images
	 */
	public function flush_image_path_cache( $image, $size ) {
		$image = is_numeric( $image ) ? $image : $image->pid;
		$key   = strval( $image ) . $size;

		unset( self::$image_abspath_cache[ $key ] );
		unset( self::$image_url_cache[ $key ] );
	}

	/**
	 * Check if ImageMagick supports JPEG format
	 *
	 * @return bool
	 */
	private function imagick_supports_jpeg() {
		static $supports_jpeg = null;

		if ( $supports_jpeg !== null ) {
			return $supports_jpeg;
		}

		$supports_jpeg = false;

		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			try {
				$imagick       = new \Imagick();
				$formats       = $imagick->queryFormats( 'JPEG' );
				$supports_jpeg = ! empty( $formats );
				$imagick->clear();
				$imagick->destroy();
			} catch ( \Exception $e ) {
				// ImageMagick error, assume no JPEG support
				error_log( 'NextGEN Gallery: ImageMagick JPEG support check failed: ' . $e->getMessage() );
				$supports_jpeg = false;
			}
		}

		return (bool) $supports_jpeg;
	}

	/**
	 * Imports images from a directory path.
	 *
	 * @param string        $abspath The absolute path to the directory.
	 * @param int           $gallery_id The gallery ID to import to.
	 * @param bool          $create_new_gallerypath Whether to create a new gallery path.
	 * @param null|string   $gallery_title The gallery title.
	 * @param array[string] $filenames Array of filenames to import.
	 * @return array|bool false on failure
	 */
	public function import_gallery_from_fs( $abspath, $gallery_id = null, $create_new_gallerypath = true, $gallery_title = null, $filenames = [] ) {
		if ( ! @file_exists( $abspath ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return false;
		}

		$fs = Filesystem::get_instance();

		$retval = [ 'image_ids' => [] ];

		// Ensure that this folder has images.
		$files       = [];
		$directories = [];
		foreach ( scandir( $abspath ) as $file ) {
			if ( $file == '.' || $file == '..' || strtoupper( $file ) == '__MACOSX' ) {
				continue;
			}

			$file_abspath = $fs->join_paths( $abspath, $file );

			// Omit 'hidden' directories prefixed with a period.
			if ( is_dir( $file_abspath ) && strpos( $file, '.' ) !== 0 ) {
				$directories[] = $file_abspath;
			} elseif ( $this->is_image_file( $file_abspath ) ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( $filenames && array_search( $file_abspath, $filenames ) !== false ) {
					$files[] = $file_abspath;
				} elseif ( ! $filenames ) {
					$files[] = $file_abspath;
				}
			}
		}

		if ( empty( $files ) && empty( $directories ) ) {
			return false;
		}

		// Get needed utilities.
		$gallery_mapper = GalleryMapper::get_instance();

		// Recurse through the directory and pull in all of the valid images we find.
		if ( ! empty( $directories ) ) {
			foreach ( $directories as $dir ) {
				$subImport = $this->import_gallery_from_fs( $dir, $gallery_id, $create_new_gallerypath, $gallery_title, $filenames );
				if ( $subImport ) {
					$retval['image_ids'] = array_merge( $retval['image_ids'], $subImport['image_ids'] );

					// Carried up with the ids, or a file skipped inside a subdirectory would be
					// reported by the recursive call and then dropped by its parent.
					if ( ! empty( $subImport['skipped_files'] ) ) {
						$retval['skipped_files'] = array_merge(
							isset( $retval['skipped_files'] ) ? $retval['skipped_files'] : [],
							$subImport['skipped_files']
						);
					}
					if ( ! empty( $subImport['skipped_reasons'] ) ) {
						$retval['skipped_reasons'] = array_merge(
							isset( $retval['skipped_reasons'] ) ? $retval['skipped_reasons'] : [],
							$subImport['skipped_reasons']
						);
					}

					// Each subdirectory makes its own gallery, so a caller that has to undo a
					// failed import needs the whole set, not just the one this call returns.
					if ( ! empty( $subImport['created_gallery_ids'] ) ) {
						$retval['created_gallery_ids'] = array_merge(
							isset( $retval['created_gallery_ids'] ) ? $retval['created_gallery_ids'] : [],
							$subImport['created_gallery_ids']
						);
					}
					if ( ! empty( $subImport['created_gallery_dir_ids'] ) ) {
						$retval['created_gallery_dir_ids'] = array_merge(
							isset( $retval['created_gallery_dir_ids'] ) ? $retval['created_gallery_dir_ids'] : [],
							$subImport['created_gallery_dir_ids']
						);
					}
				}
			}
		}

		// If no gallery has been specified, then use the directory name as the gallery name.
		if ( ! $gallery_id ) {
			// Create the gallery.
			$gallery = $gallery_mapper->create(
				[
					'title' => $gallery_title ? $gallery_title : I18N::mb_basename( $abspath ),
				]
			);

			if ( ! $create_new_gallerypath ) {
				$gallery_root  = $fs->get_document_root( 'gallery' );
				$gallery->path = str_ireplace( $gallery_root, '', $abspath );
			}

			// save_entity() only makes the directory when it is absent, firing this action;
			// an existing one (FTP'd, or left by a renamed gallery) is silently adopted, and
			// the rollback may only sweep a directory this request actually created.
			$dirs_made_before = did_action( 'ngg_created_new_gallery' );

			if ( $gallery->save() ) {
				$gallery_id                      = $gallery->id();
				$retval['created_gallery_ids'][] = $gallery_id;

				if ( did_action( 'ngg_created_new_gallery' ) > $dirs_made_before ) {
					$retval['created_gallery_dir_ids'][] = $gallery_id;
				}
			}
		}

		// Ensure that we have a gallery id.
		if ( ! $gallery_id ) {
			return false;
		} else {
			$retval['gallery_id'] = $gallery_id;
		}

		// Remove full sized image if backup is included.
		$files_to_import = [];
		foreach ( $files as $file_abspath ) {

			if ( preg_match( '#_backup$#', $file_abspath ) ) {
				$files_to_import[] = $file_abspath;
				continue;
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			} elseif ( in_array( [ $file_abspath . '_backup', 'thumbs_' . $file_abspath, 'thumbs-' . $file_abspath ], $files ) ) {
				continue;
			}

			$files_to_import[] = $file_abspath;
		}

		foreach ( $files_to_import as $file_abspath ) {
			$basename = preg_replace( '#_backup$#', '', pathinfo( $file_abspath, PATHINFO_BASENAME ) );
			if ( $this->is_image_file( $file_abspath ) ) {
				try {
					$image_id = $this->import_image_file( $gallery_id, $file_abspath, $basename, false, false, false );
					if ( $image_id ) {
						$retval['image_ids'][] = $image_id;
					}
				} catch ( \Exception $e ) {
					// One file failing (disk-full, unreadable image, etc.) must not discard the
					// files already imported earlier in this same folder/ZIP batch. Reported
					// through the same skipped-entries channel the ZIP extension allow-list uses,
					// so upload_zip()'s existing warning/skipped_files reporting picks it up.
					$reason = Sanitization::exception_message( $e, __( 'The image could not be imported.', 'nggallery' ) );
					error_log( 'NextGEN Gallery: skipped ' . $file_abspath . ' during folder import: ' . $reason ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					$this->collect_skipped_zip_entry( I18N::mb_basename( $file_abspath ) );

					// Also returned directly, not only recorded on the singleton: a caller that
					// doesn't know to ask get_skipped_zip_entries() (the legacy Import a Folder
					// adapter does not) would otherwise report a failed file as a plain success,
					// because the exception that used to reach it is now handled here.
					$skipped_basename = I18N::mb_basename( $file_abspath );

					$retval['skipped_files'][]                      = $skipped_basename;
					$retval['skipped_reasons'][ $skipped_basename ] = $reason;
				}
			}
		}

		// Add the gallery name to the result.
		if ( ! isset( $gallery ) ) {
			$gallery = $gallery_mapper->find( $gallery_id );
		}

		$retval['gallery_name'] = $gallery->title;
		return $retval;
	}

	public function maybe_base64_decode( $data ) {
		$decoded = base64_decode( $data );
		if ( $decoded === false ) {
			return $data;
		} elseif ( base64_encode( $decoded ) == $data ) {
			return base64_decode( $data );
		}
		return $data;
	}

	public static function register_custom_post_types() {
		$types = [
			'ngg_album'    => 'NextGEN Gallery - Album',
			'ngg_gallery'  => 'NextGEN Gallery - Gallery',
			'ngg_pictures' => 'NextGEN Gallery - Image',
		];

		foreach ( $types as $type => $label ) {
			\register_post_type(
				$type,
				[
					'label'               => $label,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
				]
			);
		}
	}

	/**
	 * Sanitizes a directory path, replacing whitespace with dashes.
	 *
	 * Taken from WP' sanitize_file_name() and modified to not act on file extensions.
	 *
	 * Removes special characters that are illegal in filenames on certain
	 * operating systems and special characters requiring special escaping
	 * to manipulate at the command line. Replaces spaces and consecutive
	 * dashes with a single dash. Trims period, dash and underscore from beginning
	 * and end of filename. It is not guaranteed that this function will return a
	 * filename that is allowed to be uploaded.
	 *
	 * @param string $dirname The directory name to be sanitized
	 * @return string The sanitized directory name
	 */
	public function sanitize_directory_name( $dirname ) {
		$dirname_raw   = $dirname;
		$special_chars = [ '?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '%', '+', chr( 0 ) ];
		$special_chars = apply_filters( 'sanitize_file_name_chars', $special_chars, $dirname_raw );
		$dirname       = preg_replace( "#\x{00a0}#siu", ' ', $dirname );
		$dirname       = str_replace( $special_chars, '', $dirname );
		$dirname       = str_replace( [ '%20', '+' ], '-', $dirname );
		$dirname       = preg_replace( '/[\r\n\t -]+/', '-', $dirname );
		$dirname       = trim( $dirname, '.-_' );
		return $dirname;
	}

	public function sanitize_filename_for_db( $filename = null ) {
		$filename = $filename ? $filename : uniqid( 'nextgen-gallery' );
		$filename = preg_replace( '#^/#', '', $filename );
		$filename = sanitize_file_name( $filename );
		if ( preg_match( '/\-(png|jpg|gif|jpeg|jpg_backup)$/i', $filename, $match ) ) {
			$filename = str_replace( $match[0], '.' . $match[1], $filename );
		}

		/*
		 * sanitize_file_name() defuses a double extension by appending an underscore
		 * ("payload.php.jpg" becomes "payload.php_.jpg") rather than removing it, which
		 * still leaves a ".php" segment in the stored name. Anything that later matches
		 * on ".php" as a substring — the legacy template locator historically did —
		 * would treat such a file as a PHP template. Demote those segments so the name
		 * cannot be read as an executable extension by any consumer.
		 */
		return Security::neutralize_executable_extensions( $filename );
	}

	/**
	 * Whether the current user may publish a NextGEN image into the media library.
	 *
	 * Copying a gallery image into wp-content/uploads and giving it an attachment row makes it
	 * reachable at a public URL, so the caller has to be authorized for the gallery the image came
	 * from. Ownership is not authorization on its own, which is why this goes through
	 * Security::can_manage_owned() rather than comparing the author directly - that helper tests
	 * the live capability first, so a delegated manager whose capability was revoked stops
	 * passing for galleries they once authored.
	 *
	 * Uses can_manage_owned()'s default required capability, matching every other gallery gate in
	 * the tree. It deliberately does *not* require 'NextGEN Attach Interface' here: that is the
	 * capability gating the attach *surfaces*, and each of them checks it at its own entry point
	 * (BlockManager, XMLRPC\Controller, nggPostThumbnail). Requiring it in the sink as well made
	 * this the only gate in the tree whose required capability disagreed with its route, which is
	 * the divergence can_manage_owned() was introduced to remove, and refused a delegated manager
	 * holding 'NextGEN Manage gallery' for a gallery they authored themselves.
	 *
	 * A gallery that no longer exists leaves no author to compare against, so it is passed as
	 * author 0, which only the cross-author capability can satisfy.
	 *
	 * @param Image $image Resolved image entity.
	 * @return bool
	 */
	protected static function current_user_can_attach_image( $image ) {
		if ( ! $image instanceof Image ) {
			return false;
		}

		$gallery = GalleryMapper::get_instance()->find( (int) $image->galleryid );

		$allowed = Security::can_manage_owned( $gallery ? $gallery->author : 0 );

		if ( ! $allowed ) {
			// Callers only see false, which is byte-identical to "no such image" and "copy
			// failed", so without this line a refusal is indistinguishable from a broken file and
			// leaves support nothing to grep. Deliberately not throttled: these are authenticated,
			// low-frequency operations, and one line per hour would hide the 2nd..Nth refusal.
			//
			// Note this also fires with no current user at all - WP-CLI, cron, a programmatic add -
			// because can_manage_owned() resolves the capability through wp_get_current_user().
			// Third-party code calling the deprecated C_Gallery_Storage shim from such a context
			// now gets false where it used to get an attachment id, and this is the only notice it
			// will get.
			self::get_instance()->log_path_refusal(
				sprintf(
					'NextGEN Gallery: refused to publish image #%1$d from gallery #%2$d into the media library - user #%3$d lacks the gallery-management capability or does not own it.',
					(int) $image->pid,
					$gallery ? (int) $gallery->{$gallery->id_field} : 0,
					(int) \get_current_user_id()
				),
				self::REFUSAL_ATTACH_IMAGE
			);
		}

		return $allowed;
	}

	/**
	 * Sets a NGG image as a post thumbnail for the given post
	 *
	 * @param int   $postId
	 * @param Image $image
	 * @param bool  $only_create_attachment
	 * @return int
	 */
	public function set_post_thumbnail( $postId, $image, $only_create_attachment = false ) {
		$retval = false;

		// Get the post ID.
		if ( is_object( $postId ) ) {
			$post   = $postId;
			$postId = isset( $post->ID ) ? $post->ID : $post->post_id;
		}

		// Resolve the image through the mapper unless an entity was handed over already.
		//
		// Was `is_int( $image )`, which only caught a literal integer. The block-editor hook
		// reads this id out of a JSON body, so it arrives as a numeric *string* - and nothing
		// stopped a body from sending an object instead, whose attacker-chosen `pid` then flowed
		// straight into the copy below.
		//
		// An id is the only other thing accepted. A non-scalar is refused outright rather than
		// cast: casting an object to int emits a conversion warning and evaluates to 1, so the
		// request would be answered about image 1 - refused today only because that row rarely
		// exists, which is not a check.
		if ( ! $image instanceof Image ) {
			$image = is_scalar( $image )
				? ImageMapper::get_instance()->find( (int) $image )
				: null;
		}

		if ( ! $image ) {
			return $retval;
		}

		// Refused early so the caller gets false before any lookup work. The real guarantee is in
		// copy_to_media_library(), which is the sink that actually publishes the file.
		if ( ! self::current_user_can_attach_image( $image ) ) {
			return $retval;
		}

		if ( $postId ) {
			$attachment_id = $this->is_in_media_library( $image->pid );

			if ( $attachment_id === false ) {
				$attachment_id = $this->copy_to_media_library( $image );
			}

			if ( $attachment_id ) {
				if ( ! $only_create_attachment ) {
					set_post_thumbnail( $postId, $attachment_id );
				}
				$retval = $attachment_id;
			}
		}

		return $retval;
	}

	/**
	 * Uploads an image for a particular gallery
	 *
	 * @param int|object|Gallery $gallery
	 * @param string|bool        $filename (optional) Specifies the name of the file
	 * @param string|bool        $data (optional) If specified, expects base64 encoded string of data
	 *
	 * @return array|array[]|bool|int $image
	 * @throws \E_UploadException When file upload fails or invalid file format
	 */
	public function upload_image( $gallery, $filename = false, $data = false ) {

		// Ensure that we have the data present that we require.
		//
		// Security::verify_nonce() is a wrapper to wp_verify_nonce().
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens below
		if ( isset( $_FILES['file'] )
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens below
			&& isset( $_FILES['file']['error'] )
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens below
			&& 0 === $_FILES['file']['error']
			&& isset( $_FILES['file']['tmp_name'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File data cannot be sanitized
			$file = $_FILES['file'];

			if ( $this->is_zip() ) {
				$retval = $this->upload_zip( $gallery );
			} elseif ( $this->is_image_file() ) {
				$retval = $this->import_image_file(
					$gallery,
					$file['tmp_name'],
					$filename ? $filename : ( isset( $file['name'] ) ? $file['name'] : false ),
					false,
					false,
					true
				);
			} else {
				// Remove the non-valid (and potentially insecure) file from the PHP upload directory.
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
				if ( isset( $_FILES['file']['tmp_name'] ) ) {
					$filename = sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) );
					@unlink( $filename ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
				}
				throw new \E_UploadException(
					esc_html(
						sprintf(
							/* translators: %s: comma-separated list of accepted image formats, e.g. "JPEG, JPG, PNG, GIF, WEBP". */
							__( 'Invalid image file. Acceptable formats: %s.', 'nggallery' ),
							ngg_get_allowed_formats_label()
						)
					)
				);
			}
		} elseif ( $data ) {
			$retval = $this->upload_base64_image(
				$gallery,
				$data,
				$filename
			);
		} else {
			throw new \E_UploadException();
		}

		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $retval;
	}

	/**
	 * Uploads base64 file to a gallery
	 *
	 * @param int|\stdClass|Gallery   $gallery
	 * @param string                  $data base64-encoded string of data representing the image
	 * @param string|false (optional) $filename specifies the name of the file
	 * @param int|false               $image_id (optional)
	 * @param bool                    $override (optional)
	 *
	 * @return bool|int
	 */
	public function upload_base64_image( $gallery, $data, $filename = false, $image_id = false, $override = false, $move = false ) {
		$temp_abspath = tempnam( sys_get_temp_dir(), '' );

		// Try writing the image.
		$fp = fopen( $temp_abspath, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fwrite( $fp, $this->maybe_base64_decode( $data ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		fclose( $fp );

		return $this->import_image_file( $gallery, $temp_abspath, $filename, $image_id, $override, $move );
	}

	/**
	 * Uploads a zip file.
	 *
	 * @param int  $gallery_id
	 * @param bool $skip_nonce_check Whether to skip nonce verification (true for REST API calls)
	 * @return array|bool
	 * @throws \E_UploadException When every entry in the zip was refused by the extension allow-list.
	 */
	public function upload_zip( $gallery_id, $skip_nonce_check = false ) {
		if ( ! $this->is_zip( $skip_nonce_check ) ) {
			return false;
		}

		// Skip nonce check for REST API calls (they have their own authentication via permission callbacks)
		// For traditional form posts, verify the nonce
		if ( ! $skip_nonce_check ) {
			// Security::verify_nonce() is a wrapper to wp_verify_nonce().
			//
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['nonce'] )
				|| ! Security::verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'nextgen_upload_image' ) ) {
				return false;
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		$retval = false;

		if ( ! extension_loaded( 'suhosin' ) ) {
			wp_raise_memory_limit();
		}

		$fs = Filesystem::get_instance();

		// Uses the WordPress ZIP abstraction API.
		include_once $fs->join_paths( ABSPATH, 'wp-admin', 'includes', 'file.php' );
		WP_Filesystem( false, get_temp_dir(), true );

		// Ensure that we truly have the gallery id.
		$gallery_id = $this->get_gallery_id( $gallery_id );

		// The nonce was already checked above, by Security::verify_nonce(). Also PHP-CS still flags this particular
		// line when using phpcs:ignore, thus the disable/enable pairing found here.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
		$zipfile = isset( $_FILES['file']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$dest_path = implode(
			DIRECTORY_SEPARATOR,
			[
				rtrim( get_temp_dir(), '/\\' ),
				'unpacked-' . I18N::mb_basename( $zipfile ),
			]
		);

		// Attempt to extract the zip file into the normal system directory.
		$extracted = $this->extract_zip( $zipfile, $dest_path );

		// Now verify it worked. get_temp_dir() will check each of the following directories to ensure they are
		// a directory and against wp_is_writable(). Should ALL of those options fail we will fallback to wp_upload_dir().
		$size  = 0;
		$files = glob( $dest_path . DIRECTORY_SEPARATOR . '*' );
		foreach ( $files as $file ) {
			if ( is_array( stat( $file ) ) ) {
				$size += filesize( $file );
			}
		}

		// Extraction failed; attempt again with wp_upload_dir().
		if ( $size == 0 ) {
			// Remove the empty directory we may have possibly created but could not write to.
			$this->delete_directory( $dest_path );

			$destination      = wp_upload_dir();
			$destination_path = $destination['basedir'];
			$dest_path        = implode(
				DIRECTORY_SEPARATOR,
				[
					rtrim( $destination_path, '/\\' ),
					wp_rand(),
					'unpacked-' . I18N::mb_basename( $zipfile ),
				]
			);

			$extracted = $this->extract_zip( $zipfile, $dest_path );
		}

		try {
			if ( $extracted ) {
				$retval = $this->import_gallery_from_fs( $dest_path, $gallery_id );
			}
		} finally {
			// Always clean up extracted files, even if import_image_file() throws.
			$this->delete_directory( $dest_path );
		}

		$this->delete_directory( $dest_path );

		/*
		 * MERGE NOTE (#932 + #933): #933's channel is used, and it closes the gap #932 filed
		 * as #1010. #932 put the refusal on the response as `refused_files` / `warning` and
		 * noted no uploader read either key; #933 added the readers - adminApp's
		 * ImageUploader (warning + skipped_files) and the legacy upload_images template - so
		 * the refusal now reaches the admin UI rather than only the API response and the
		 * WP_DEBUG log.
		 *
		 * The split #932 insisted on is preserved: a hard failure is only for an import that
		 * produced nothing. Both callers turn a failure into an HTTP 500 - ImageREST also
		 * skips its Transient::flush( 'rest_galleries' ) on any non-200, and the adminApp
		 * uploader throws and discards the imported ids - so reporting a *partial* import
		 * that way would mark a successful upload as failed and invite the user to upload
		 * the same archive again. That was the folder-structured-ZIP regression #932's
		 * review rounds found; the `empty( $retval['image_ids'] )` condition below is what
		 * keeps it fixed.
		 */
		// The extension gate in extract_zip() is silent and $extracted is true either way, so a
		// refused entry has to be named here or the upload reports plain success for a zip that
		// was only partly imported. Read through the getter rather than an out-parameter so the
		// legacy twin, which goes through the pope dispatcher, can use the identical shape.
		$skipped = $this->get_skipped_zip_entries();

		if ( ! empty( $skipped ) ) {
			$skipped_label = implode( ', ', $skipped );

			if ( ! is_array( $retval ) || empty( $retval['image_ids'] ) ) {
				// Nothing was imported at all: this is an outright rejection, so it is reported the
				// same way import_image_file() reports a single refused upload. The list is no
				// longer only extension refusals - a processing failure (disk-full, unreadable
				// image) lands here too now - so the message no longer names a specific cause.
				throw new \E_UploadException(
					esc_html(
						sprintf(
							/* translators: %s: comma-separated list of file names that could not be imported. */
							__( 'No images were imported. These files could not be imported: %s.', 'nggallery' ),
							$skipped_label
						)
					)
				);
			}

			// Some images did import: reported as a warning rather than an error so the successful
			// part of the upload is still recorded, but the dropped names still reach the user.
			$retval['skipped_files'] = array_map( 'esc_html', $skipped );
			// Escaped like the sibling throw path: the uploader surfaces append this message
			// into markup, and the names come from the uploaded archive.
			$retval['warning'] = esc_html(
				sprintf(
					/* translators: %s: comma-separated list of file names that could not be imported. */
					__( 'These files were skipped and could not be imported: %s.', 'nggallery' ),
					$skipped_label
				)
			);
		}

		return $retval;
	}

	public static function wp_query_order_by( $order_by, $wp_query ) {
		if ( $wp_query->get( 'datamapper_attachment' ) ) {
			$order_parts = explode( ' ', $order_by );
			$order_name  = array_shift( $order_parts );
			$order_by    = 'ABS(' . $order_name . ') ' . implode( ' ', $order_parts ) . ', ' . $order_by;
		}

		return $order_by;
	}


	/**
	 * Gets the id of an image, regardless of whether an integer or object was passed as an argument.
	 *
	 * This method is, as of 3.50's release, used by EWWW and WP-SmushIt
	 *
	 * @param object|int $image_obj_or_id
	 * @return null|int
	 * @deprecated
	 */
	private function _get_image_id( $image_obj_or_id ) {
		$retval = null;

		$image_key = $this->_image_mapper->get_primary_key_column();
		if ( is_object( $image_obj_or_id ) ) {
			if ( isset( $image_obj_or_id->$image_key ) ) {
				$retval = $image_obj_or_id->$image_key;
			}
		} elseif ( is_numeric( $image_obj_or_id ) ) {
			$retval = $image_obj_or_id;
		}

		return $retval;
	}
}
