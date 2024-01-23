<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataTypes\Gallery as GalleryType;

use Imagely\NGG\DataMapper\TableDriver;
use Imagely\NGG\Display\I18N;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Transient;

class Gallery extends TableDriver {

	private static $instance = null;

	public $model_class = 'Imagely\NGG\DataTypes\Gallery';

	public $primary_key_column = 'gid';

	// Necessary for legacy compatibility.
	public $custom_post_name = 'mixin_nextgen_table_extras';

	public function __construct() {
		$this->define_column( 'author', 'INT', 0 );
		$this->define_column( 'extras_post_id', 'BIGINT', 0 );
		$this->define_column( 'galdesc', 'MEDIUMTEXT' );
		$this->define_column( 'gid', 'BIGINT', 0 );
		$this->define_column( 'name', 'VARCHAR(255)' );
		$this->define_column( 'pageid', 'INT', 0 );
		$this->define_column( 'path', 'TEXT' );
		$this->define_column( 'previewpic', 'INT', 0 );
		$this->define_column( 'slug', 'VARCHAR(255)' );
		$this->define_column( 'title', 'TEXT' );

		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$this->define_column( 'pricelist_id', 'BIGINT', 0, true );
		}

		apply_filters( 'ngg_gallery_mapper_columns', $this );

		parent::__construct( 'ngg_gallery' );
	}

	/**
	 * @return Gallery|\Imagely\NGGPro\Commerce\DataMappers\Gallery
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			$class          = apply_filters( 'ngg_datamapper_client_gallery', __CLASS__ );
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * @param int|GalleryType
	 * @return GalleryType|null
	 */
	public function find( $entity ) {
		/** @var GalleryType $result */
		$result = parent::find( $entity );
		return $result;
	}

	/**
	 * @param string $slug
	 * @return GalleryType|null
	 */
	public function get_by_slug( $slug ) {
		$sanitized_slug = sanitize_title( $slug );

		// Try finding the gallery by slug first; if nothing is found assume that the user passed a gallery id.
		$retval = $this->select()->where( [ 'slug = %s', $sanitized_slug ] )->limit( 1 )->run_query();

		// NextGen used to turn "This & That" into "this-&amp;-that" when assigning gallery slugs.
		if ( empty( $retval ) && strpos( $slug, '&' ) !== false ) {
			return $this->get_by_slug( str_replace( '&', '&amp;', $slug ) );
		}

		return reset( $retval );
	}

	public function set_preview_image( $gallery, $image, $only_if_empty = false ) {
		$retval = false;

		// We need the gallery object.
		if ( is_numeric( $gallery ) ) {
			$gallery = $this->find( $gallery );
		}

		// We need the image id.
		if ( ! is_numeric( $image ) ) {
			if ( method_exists( $image, 'id' ) ) {
				$image = $image->id();
			} else {
				$image = $image->{$image->id_field};
			}
		}

		if ( $gallery && $image ) {
			if ( ( $only_if_empty && ! $gallery->previewpic ) or ! $only_if_empty ) {
				$gallery->previewpic = $image;
				$retval              = $this->save( $gallery );
			}
		}

		return $retval;
	}

	/**
	 * Uses the title property as the post title when the Custom Post driver is used
	 *
	 * @param object $entity
	 * @return string
	 */
	public function get_post_title( $entity ) {
		return $entity->title;
	}

	public function save_entity( $entity ) {
		$storage = StorageManager::get_instance();

		// A bug in NGG 2.1.24 allowed galleries to be created with spaces in the directory name, unreplaced by dashes
		// This causes a few problems everywhere, so we here allow users a way to fix those galleries by just re-saving.
		if ( false !== strpos( $entity->path, ' ' ) ) {
			$abspath = $storage->get_gallery_abspath( $entity->{$entity->id_field} );

			$pre_path = $entity->path;

			$entity->path = str_replace( ' ', '-', $entity->path );

			$new_abspath = str_replace( $pre_path, $entity->path, $abspath );

			// Begin adding -1, -2, etc. until we have a safe target: rename() will overwrite existing directories.
			if ( @file_exists( $new_abspath ) ) {
				$max_count         = 100;
				$count             = 0;
				$corrected_abspath = $new_abspath;
				while ( @file_exists( $corrected_abspath ) && $count <= $max_count ) {
					++$count;
					$corrected_abspath = $new_abspath . '-' . $count;
				}
				$new_abspath  = $corrected_abspath;
				$entity->path = $entity->path . '-' . $count;
			}

			$wpfs = new \WP_Filesystem_Direct( null );
			$wpfs->move( $abspath, $new_abspath );
		}

		$slug = $entity->slug;

		$entity->slug = str_replace( ' ', '-', $entity->slug );
		$entity->slug = sanitize_title( $entity->slug );

		if ( $slug != $entity->slug ) {
			$entity->slug = \nggdb::get_unique_slug( $entity->slug, 'gallery' );
		}

		$retval = parent::save_entity( $entity );

		if ( $retval ) {
			$path = $storage->get_gallery_abspath( $entity );
			if ( ! file_exists( $path ) ) {
				wp_mkdir_p( $path );
				do_action( 'ngg_created_new_gallery', $entity->{$entity->id_field} );
			}
			Transient::flush( 'displayed_gallery_rendering' );
		}

		return $retval;
	}

	public function destroy( $entity, $with_dependencies = false ) {
		$retval = false;

		if ( $entity ) {
			if ( is_numeric( $entity ) ) {
				$gallery_id = $entity;
				$entity     = $this->find( $gallery_id );
			} else {
				$gallery_id = $entity->{$entity->id_field};
			}

			// TODO: Look into making this operation more efficient.
			if ( $with_dependencies ) {
				$image_mapper = ImageMapper::get_instance();

				// Delete the image files from the filesystem.
				$settings = Settings::get_instance();
				if ( $settings->deleteImg ) {
					$storage = StorageManager::get_instance();
					$storage->delete_gallery( $entity );
				}

				// Delete the image records from the DB.
				foreach ( $image_mapper->find_all_for_gallery( $gallery_id ) as $image ) {
					$image_mapper->destroy( $image );
				}

				$image_key   = $image_mapper->get_primary_key_column();
				$image_table = $image_mapper->get_table_name();

				// Delete tag associations no longer needed. The following SQL statement deletes all tag associates for
				// images that no longer exist.
				global $wpdb;

				// $wpdb->prepare() cannot be used just yet as it only supported the %i placeholder for column names as of
				// WordPress 6.2 which is newer than NextGEN's current minimum WordPress version.
				//
				// TODO: Once NextGEN's minimum WP version is 6.2 or higher use wpdb->prepare() here.
				//
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query(
					"
                    DELETE wptr.* FROM {$wpdb->term_relationships} wptr
                    INNER JOIN {$wpdb->term_taxonomy} wptt
                    ON wptt.term_taxonomy_id = wptr.term_taxonomy_id
                    WHERE wptt.term_taxonomy_id = wptr.term_taxonomy_id
                    AND wptt.taxonomy = 'ngg_tag'
                    AND wptr.object_id NOT IN (SELECT {$image_key} FROM {$image_table})"
				);
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}

			$retval = parent::destroy( $entity );

			if ( $retval ) {
				do_action( 'ngg_delete_gallery', $entity );
				Transient::flush( 'displayed_gallery_rendering' );
			}
		}

		return $retval;
	}

	/**
	 * @param GalleryType $entity
	 */
	public function set_defaults( $entity ) {
		// If author is missing, then set to the current user id.
		$this->set_default_value( $entity, 'author', get_current_user_id() );
		$this->set_default_value( $entity, 'pageid', 0 );
		$this->set_default_value( $entity, 'previewpic', 0 );

		if ( ! is_admin() && ! empty( $entity->{$entity->id_field} ) ) {
			if ( ! empty( $entity->title ) ) {
				$entity->title = I18N::translate( $entity->title, 'gallery_' . $entity->{$entity->id_field} . '_name' );
			}
			if ( ! empty( $entity->galdesc ) ) {
				$entity->galdesc = I18N::translate( $entity->galdesc, 'gallery_' . $entity->{$entity->id_field} . '_description' );
			}
		}
	}
}
