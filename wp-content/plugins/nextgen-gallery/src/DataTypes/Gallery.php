<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMappers\Gallery as Mapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataStorage\Sanitizer;
use Imagely\NGG\Util\Filesystem;

class Gallery extends Model {

	public $author;
	public $extras_post_id;
	public $galdesc;
	public $gid;
	public $id_field = 'gid';
	public $name;
	public $pageid;
	public $path;
	public $previewpic;
	public $slug;
	public $title;

	// TODO: remove this when get_pro_compat_level() >= 1.
	public $pricelist_id;

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
			$not_directly_in['document root'] = $_SERVER['DOCUMENT_ROOT'];
		}

		foreach ( $not_directly_in as $label => $dir ) {
			if ( $abspath == $dir ) {
				$retval['gallerypath'][] = sprintf(
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
