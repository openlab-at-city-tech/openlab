<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataTypes\Gallery as GalleryType;
use Imagely\NGG\DataTypes\Image as ImageType;

use Imagely\NGG\DataMapper\TableDriver;
use Imagely\NGG\Display\I18N;
use Imagely\NGG\Util\Transient;

class Image extends TableDriver {

	private static $instance = null;

	public $model_class = 'Imagely\NGG\DataTypes\Image';

	public $primary_key_column = 'pid';

	// Necessary for legacy compatibility.
	public $custom_post_name = 'mixin_nextgen_table_extras';

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
	 * @return Image|\Imagely\NGGPro\Commerce\DataMappers\Image
	 */
	static function get_instance() {
		if ( is_null( self::$instance ) ) {
			$class          = apply_filters( 'ngg_datamapper_client_image', __CLASS__ );
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * @param int|ImageType $entity
	 * @return ImageType
	 */
	public function find( $entity ) {
		/** @var ImageType $result */
		$result = parent::find( $entity );
		return $result;
	}

	/**
	 * @param GalleryType $gallery
	 * @param bool        $model
	 *
	 * @return ImageType[]
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
	 * @param ImageType $image
	 * @return bool
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
	 * @param ImageType $entity
	 * @return bool
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
	 * @param ImageType $entity
	 * @return bool|TableDriver
	 */
	public function save_entity( $entity ) {
		$entity->updated_at = time();

		$retval = parent::save_entity( $entity );

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
	 * @param ImageType $entity
	 * @return string
	 */
	public function get_post_title( $entity ) {
		return $entity->alttext;
	}

	public function set_defaults( $entity ) {
		$this->set_default_value( $entity, 'post_id', 0 );
		$this->set_default_value( $entity, 'exclude', 0 );
		$this->set_default_value( $entity, 'sortorder', 0 );

		$this->set_default_value( $entity, 'description', '' );
		$this->set_default_value( $entity, 'alttext', '' );

		if ( ( ! isset( $entity->imagedate ) ) || $entity->imagedate == '0000-00-00 00:00:00' ) {
			$entity->imagedate = date( 'Y-m-d H:i:s' );
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
	 * @param string $value
	 * @return mixed|null
	 * @throws \Exception
	 * @deprecated
	 * @todo Remove this when the minimum Pro API level is 4.0 or higher.
	 */
	public function unserialize( string $value ) {
		return \Imagely\NGG\Util\Serializable::unserialize( $value );
	}
}
