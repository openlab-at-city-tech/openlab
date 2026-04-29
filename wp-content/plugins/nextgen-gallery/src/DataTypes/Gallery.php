<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMappers\Gallery as Mapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataStorage\Sanitizer;
use Imagely\NGG\Util\Filesystem;

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
}
