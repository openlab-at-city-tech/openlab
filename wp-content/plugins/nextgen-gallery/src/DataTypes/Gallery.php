<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMappers\Gallery as Mapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataStorage\Sanitizer;
use Imagely\NGG\Util\Filesystem;
use Imagely\NGG\Util\Security;

/**
 * Gallery data type.
 */
class Gallery extends Model {

	/**
	 * Gallery author.
	 *
	 * @var int|string
	 */
	public $author;

	/**
	 * Extras post ID.
	 *
	 * @var int
	 */
	public $extras_post_id;

	/**
	 * Gallery description.
	 *
	 * @var string
	 */
	public $galdesc;

	/**
	 * Gallery ID.
	 *
	 * @var int|string
	 */
	public $gid;

	/**
	 * ID field name.
	 *
	 * @var string
	 */
	public $id_field = 'gid';

	/**
	 * Gallery name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Page ID.
	 *
	 * @var int
	 */
	public $pageid;

	/**
	 * Gallery path.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Preview picture ID.
	 *
	 * @var int
	 */
	public $previewpic;

	/**
	 * Gallery slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Gallery title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Date when the gallery was created.
	 *
	 * @var string
	 */
	public $date_created;

	/**
	 * Date when the gallery was last modified.
	 *
	 * @var string
	 */
	public $date_modified;

	/**
	 * Display type for the gallery.
	 *
	 * @var string
	 */
	public $display_type = 'photocrati-nextgen_basic_thumbnails';

	/**
	 * Settings for the display type.
	 *
	 * @var array
	 */
	public $display_type_settings = [];

	/**
	 * Whether the gallery is private.
	 *
	 * @var bool
	 */
	public $is_private = false;

	/**
	 * External source
	 *
	 * @var array
	 */
	public $external_source = [];

	/**
	 * Whether ecommerce is enabled for this gallery.
	 *
	 * @var bool
	 */
	public $is_ecommerce_enabled = false;

	// TODO: remove this when get_pro_compat_level() >= 1.
	/**
	 * Price list ID.
	 *
	 * @var int
	 */
	public $pricelist_id;

	/**
	 * Closed postboxes nonce.
	 *
	 * @var string
	 */
	public $closedpostboxesnonce;

	/**
	 * Parent ID.
	 *
	 * @var int
	 */
	public $parent_id;

	/**
	 * Post paged.
	 *
	 * @var int
	 */
	public $post_paged;

 // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
 // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
 // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
 // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
 // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
	/**
	 * Bulk action.
	 *
	 * @var string
	 */
	public $bulkaction;

	/**
	 * Images array.
	 *
	 * @var array
	 */
	public $images = [];

	/**
	 * Update pictures flag.
	 *
	 * @var bool
	 */
	public $updatepictures;

	/**
	 * Attach to post flag.
	 *
	 * @var bool
	 */
	public $attach_to_post;

	/**
	 * Gallery counter.
	 *
	 * @var int
	 */
	public $counter;


	public function get_primary_key_column() {
		return 'gid';
	}

	public function get_mapper() {
		return Mapper::get_instance();
	}

	public function get_images() {
		return ImageMapper::get_instance()
			->select()
			->where( [ 'galleryid = %d', $this->gid ] )
			->order_by( 'sortorder' )
			->run_query();
	}

	public function validation() {
		$retval = [];

		// If a title is present, we can automatically populate some other properties.
		if ( ( $this->title ) ) {
			// Strip html.
			$this->title     = Sanitizer::strip_html( $this->title, true );
			$sanitized_title = str_replace( ' ', '-', $this->title );

			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
				$sanitized_title = \remove_accents( $sanitized_title );
			}

			// If no name is present, use the title to generate one.
			if ( ! ( $this->name ) ) {
				$this->name = \apply_filters( 'ngg_gallery_name', \sanitize_file_name( $sanitized_title ) );
			}

			// Assign a slug; possibly updating the current slug if it was conceived by a method other than sanitize_title()
			// NextGen 3.2.19 and older used a method adopted from esc_url() which would convert ampersands to "&amp;"
			// and allow slashes in gallery slugs which breaks their ability to be linked to as children of albums.
			$sanitized_slug = \sanitize_title( $sanitized_title );
			if ( empty( $this->slug ) || $this->slug !== $sanitized_slug ) {
				$this->slug = $sanitized_slug;
				$this->slug = \nggdb::get_unique_slug( $this->slug, 'gallery' );
			}
		}

		// Set what will be the path to the gallery.
		$storage = StorageManager::get_instance();
		if ( ! ( $this->path ) ) {
			$this->path = $storage->get_gallery_relpath( $this );
		}

		// Ensure that the gallery path is restricted to $fs->get_document_root('galleries').
		$fs   = Filesystem::get_instance();
		$root = $fs->get_document_root( 'galleries' );
		$storage->flush_gallery_path_cache( $this );
		$gallery_abspath = $storage->get_gallery_abspath( $this );
		if ( strpos( $gallery_abspath, $root ) === false ) {
			/* translators: %s: root directory path */
			$retval['gallerypath'][] = sprintf( __( 'Gallery path must be located in %s', 'nggallery' ), $root );
			$this->path              = $storage->get_upload_relpath( $this );
		}

		$this->path = trailingslashit( $this->path );

		// Check for '..' in the path.
		$sections = explode( DIRECTORY_SEPARATOR, trim( $this->path, '/\\' ) );
		if ( in_array( '..', $sections, true ) ) {
			$retval['gallerypath'][] = __( "Gallery paths may not use '..' to access parent directories)", 'nggallery' );
		}

		// Establish some rules on where galleries can go.
		$abspath = $storage->get_gallery_abspath( $this );

		// Collapse "." / ".." segments so the location rules below cannot be evaded by a path that
		// resolves to a forbidden directory without matching it literally (e.g. a stored path of
		// "." resolves to the WordPress root but the raw string is not equal to it).
		$abspath = \wp_normalize_path( (string) $abspath );
		// wp_normalize_path() preserves a leading "//" for UNC/network-share paths; keep it so the
		// prefix comparisons below still match the constants (which also keep their "//").
		$is_unc      = ( 0 === strpos( $abspath, '//' ) );
		$is_absolute = ( '' !== $abspath && '/' === $abspath[0] );
		$normalized  = [];
		foreach ( explode( '/', $abspath ) as $segment ) {
			if ( '' === $segment || '.' === $segment ) {
				continue;
			}
			if ( '..' === $segment ) {
				array_pop( $normalized );
				continue;
			}
			$normalized[] = $segment;
		}
		$prefix  = $is_unc ? '//' : ( $is_absolute ? '/' : '' );
		$abspath = $prefix . implode( '/', $normalized );

		// Galleries should at least be a sub-folder, not directly in WP_CONTENT.
		$not_directly_in = [
			'content'        => \wp_normalize_path( WP_CONTENT_DIR ),
			'wordpress root' => $fs->get_document_root(),
		];

		if ( ! empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
			$not_directly_in['document root'] = sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) );
		}

		foreach ( $not_directly_in as $label => $dir ) {
			if ( $abspath == $dir ) {
				$retval['gallerypath'][] = sprintf(
					/* translators: %s: directory label */
					__( 'Gallery path must be a sub-directory under the %s directory', 'nggallery' ),
					$label
				);
			}
		}

		$ABSPATH = \wp_normalize_path( ABSPATH );

		// Disallow galleries from being under these directories at all.
		$not_ever_in = [
			'plugins'          => \wp_normalize_path( WP_PLUGIN_DIR ),
			'must use plugins' => \wp_normalize_path( WPMU_PLUGIN_DIR ),
			'wp-admin'         => $fs->join_paths( $ABSPATH, 'wp-admin' ),
			'wp-includes'      => $fs->join_paths( $ABSPATH, 'wp-includes' ),
			'themes'           => \get_theme_root(),
		];

		foreach ( $not_ever_in as $label => $dir ) {
			if ( strpos( $abspath, $dir ) === 0 ) {
				$retval['gallerypath'][] = sprintf(
					/* translators: %s: directory label */
					__( 'Gallery path cannot be under %s directory', 'nggallery' ),
					$label
				);
			}
		}

		// A gallery's directory is the boundary every file deletion NextGEN performs for that
		// gallery's images is measured against (DataStorage\Manager::get_deletion_boundary()), so
		// whoever can move the directory chooses where those unlinks land. Point a gallery at
		// "wp-content/uploads/2026/10", scan the folder into it, then delete the rows, and that
		// month's media-library files go one at a time (#1085). The deletion side now refuses a
		// boundary outside the dedicated NextGEN gallery folder on its own, and this closes the
		// writer: the legacy Manage Gallery screen is the only one that reaches the column, the
		// REST and XML-RPC property bags already deny it (Util\MassAssignment).
		//
		// Only a *relocation* is restricted, and only for a gallery that already exists. A new
		// gallery's path is never caller-supplied - no create endpoint accepts the key - and is set
		// by the server, which includes the "keep original location" folder import that adopts a
		// directory outside the gallery base on purpose. An unchanged value is likewise left alone,
		// so such a gallery stays editable instead of failing every save made from the Manage
		// Gallery screen, a title edit included.
		$gallery_base = Security::canonicalize_path( $storage->get_upload_abspath() );

		if ( '' !== $gallery_base ) {
			$candidate = Security::canonicalize_path( $abspath );

			$is_inside = '' !== $candidate
				&& rtrim( $candidate, '/' ) !== rtrim( $gallery_base, '/' )
				&& Security::path_is_within( $candidate, $gallery_base );

			// Containment is settled before path_is_relocation() is consulted, so the query that
			// reads the stored value only runs for the save that is actually about to store a path
			// outside the base - not on every gallery save.
			if ( ! $is_inside && $this->path_is_relocation() ) {
				$retval['gallerypath'][] = sprintf(
					/* translators: %s: the NextGEN Gallery base directory path */
					__( 'Gallery path must be a sub-directory under %s', 'nggallery' ),
					$gallery_base
				);
			}
		}

		// Inside the gallery base is not the same as unclaimed. Two galleries may hold the same
		// `path`, and gallery deletion authorizes on the deleter's own row only, so a gallery
		// pointed at another author's folder turns a delete of your own gallery into a delete of
		// theirs. Refused here as well as on the deletion side, so the state cannot be written in
		// the first place rather than only being declined later.
		//
		// Two narrowings, both of which the first revision of this check got wrong:
		//
		// 1. An unchanged re-save is exempt. Otherwise a row that already holds a nested path - one
		//    an earlier keep-location import created - could no longer be saved at all, so editing
		//    its title failed on a collision it did not introduce.
		// 2. Nesting only counts inside the dedicated gallery base. A recursive keep-location
		//    import points the parent gallery at a directory that contains its children's, by
		//    design: import_gallery_from_fs() saves each sub-folder's gallery before the parent, so
		//    refusing the parent made the whole import return false and report "no images found"
		//    after the sub-galleries had been created. Inside the base NextGEN names directories
		//    per slug, so nesting there is not something a legitimate flow produces.
		//
		// The exact-directory refusal, which is the takeover vector, applies in every case, and the
		// deletion side still refuses nesting in both directions regardless of what was stored.
		// path_is_relocation() answers false for a gallery with no id yet, so a new row has to be
		// admitted explicitly: the keep-location import creates one, and it must not be able to
		// adopt a directory an existing gallery already holds.
		$is_new_gallery = empty( $this->gid );

		// A candidate that resolves to the gallery base itself, or to a shared base, is not a claim
		// on anything: it is what a not-yet-named new gallery resolves to before the mapper derives
		// its directory. Treating it as a claim made the *second* gallery created in a request
		// collide with the first and fail to save, which broke gallery creation outright.
		$claim_candidate = '' !== (string) $abspath
			&& ! $storage->is_shared_gallery_location( $abspath );

		if ( $claim_candidate && ( $is_new_gallery || $this->path_is_relocation() ) ) {
			// A server-set new row never counts nesting. Keying it on $is_inside alone still broke
			// the recursive keep-location import on multisite, where the import root *is* the
			// gallery base (ImageREST::get_import_root()): the children save first, then the parent
			// whose directory contains them is refused, and the import reports "no images found"
			// after the sub-galleries exist. Only the nesting test is skipped - the exact-directory
			// refusal still applies to new rows, so an import cannot adopt a directory another
			// gallery already holds, and the deletion side still refuses nesting in both
			// directions whatever ended up stored.
			//
			// A new in-base row is still refused when it sits strictly *inside* another gallery's
			// directory. Skipping nesting entirely for new rows let a keep-location import of
			// "gallery/<victim>/thumbs" adopt the victim's thumbnails as its own images; on delete,
			// per-file ownership then awarded each one to the deeper directory, the importer's, and
			// they were unlinked. Only the containing direction is admitted for new rows.
			$in_base = isset( $is_inside ) && (bool) $is_inside;

			if ( $is_new_gallery ) {
				$nesting = $in_base ? 'inside' : false;
			} else {
				$nesting = $in_base;
			}

			$claimed = $storage->directory_belongs_to_another_gallery( $abspath, $this->gid, $nesting );

			if ( $claimed ) {
				$retval['gallerypath'][] = __( 'Gallery path is already used by another gallery.', 'nggallery' );
			}
		}

		// Regardless of where they are just don't let the path end in any of these.
		$never_named = [
			'wp-admin',
			'wp-includes',
			'wp-content',
		];
		foreach ( $never_named as $name ) {
			if ( $name === end( $sections ) ) {
				$retval['gallerypath'][] = sprintf(
					/* translators: %s: directory name */
					__( 'Gallery path cannot end with a directory named %s', 'nggallery' ),
					$name
				);
			}
		}

		unset( $storage );

		$retval = array_merge(
			$retval,
			$this->validates_presence_of( 'title' ),
			$this->validates_presence_of( 'name' ),
			$this->validates_uniqueness_of( 'slug' ),
			$this->validates_numericality_of( 'author' )
		);

		return empty( $retval ) ? true : $retval;
	}

	/**
	 * Whether this save moves an existing gallery's directory, as opposed to setting the path of a
	 * new gallery or re-saving the one already stored.
	 *
	 * The stored value is read straight from the table rather than through the mapper. On a
	 * non-persistent object cache WordPress hands objects back by reference, so a find() here can
	 * return the very instance being validated - its `path` already overwritten by the caller - and
	 * report "unchanged" for a value that did change.
	 *
	 * Anything that makes the stored value unreadable answers *true*. This is the only writer-side
	 * control on the deletion boundary, so it has to fail closed: a guard that reads "not a
	 * relocation" from a failed query is a guard that switches itself off exactly when the database
	 * is in the state an attacker would want it in.
	 *
	 * @return bool
	 */
	private function path_is_relocation() {
		global $wpdb;

		if ( empty( $this->gid ) ) {
			return false;
		}

		if ( ! isset( $wpdb ) || ! $wpdb->ready ) {
			return true;
		}

		// Resolved from the mapper rather than $wpdb->nggallery. Legacy\nggallery::define_tables()
		// binds that property once at plugin load and switch_to_blog() never updates it, while the
		// mapper derives its table from the live $wpdb->prefix - so on a switched blog the property
		// names a different site's table than the one this save is about to write.
		$table = Mapper::get_instance()->get_table_name();

		$wpdb->last_error = '';

		// get_row() rather than get_var(), so an absent row is distinguishable from a row whose
		// `path` column is SQL NULL. The latter is not "no stored path to move from" - it is a
		// stored value that differs from whatever is being written, which is a relocation.
		// The table name is resolved from the mapper, never from input, so interpolating it is the
		// only way to name a table whose prefix is only known at runtime; the one value is prepared.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT `path` FROM `{$table}` WHERE `gid` = %d", (int) $this->gid ) );

		if ( null === $row ) {
			// get_row() returns null both for an absent row and for a failed query. Only the first
			// means "a new gallery being saved with an id already set"; the second must refuse.
			return '' !== (string) $wpdb->last_error;
		}

		$stored = isset( $row->path ) ? (string) $row->path : '';

		return \trailingslashit( \wp_normalize_path( $stored ) )
			!== \trailingslashit( \wp_normalize_path( (string) $this->path ) );
	}
}
