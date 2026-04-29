<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataMappers\Image as Mapper;
use Imagely\NGG\DataStorage\Sanitizer;

/**
 * Image data type class.
 *
 * Represents an image entity with all its properties and methods.
 */
class Image extends Model {

	/**
	 * Image alt text.
	 *
	 * @var string
	 */
	public $alttext;

	/**
	 * Image description.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Whether the image is excluded from display.
	 *
	 * @var int
	 */
	public $exclude;

	/**
	 * Extra post ID reference.
	 *
	 * @var int
	 */
	public $extras_post_id;

	/**
	 * Image filename.
	 *
	 * @var string
	 */
	public $filename;

	/**
	 * Gallery ID this image belongs to.
	 *
	 * @var int
	 */
	public $galleryid;

	/**
	 * Primary key field name.
	 *
	 * @var string
	 */
	public $id_field = 'pid';

	/**
	 * Image slug.
	 *
	 * @var string
	 */
	public $image_slug;

	/**
	 * Image date.
	 *
	 * @var string
	 */
	public $imagedate;

	/**
	 * Image metadata array.
	 *
	 * @var array
	 */
	public $meta_data = [];

	/**
	 * Image primary ID.
	 *
	 * @var int
	 */
	public $pid;

	/**
	 * WordPress post ID reference.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Sort order for the image.
	 *
	 * @var int
	 */
	public $sortorder;

	/**
	 * Image tags.
	 *
	 * @var string
	 */
	public $tags;

	/**
	 * Last update timestamp.
	 *
	 * @var string
	 */
	public $updated_at;

	/**
	 * Legacy items array for Pro compatibility.
	 *
	 * TODO: remove this when get_pro_compat_level() >= 1.
	 *
	 * @var array
	 */
	public $items = [];

	/**
	 * Pricelist ID for Pro version.
	 *
	 * @var int
	 */
	public $pricelist_id;

	/**
	 * Image title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Gets the primary key column name.
	 *
	 * @return string The primary key column name.
	 */
	public function get_primary_key_column() {
		return 'pid';
	}

	/**
	 * Gets the mapper instance for this model.
	 *
	 * @return Mapper The image mapper instance.
	 */
	public function get_mapper() {
		return Mapper::get_instance();
	}

	/**
	 * Returns the model representing the gallery associated with this image.
	 *
	 * @param object|false $model (optional)
	 * @return Gallery
	 */
	public function get_gallery( $model = false ) {
		return \Imagely\NGG\DataMappers\Gallery::get_instance()->find( $this->galleryid, $model );
	}

	/**
	 * Validates the image data.
	 *
	 * @return array Array of validation errors, empty if valid.
	 */
	public function validation() {
		if ( isset( $this->description ) ) {
			$this->description = Sanitizer::strip_html( $this->description, true );
		}

		if ( isset( $this->alttext ) ) {
			$this->alttext = Sanitizer::strip_html( $this->alttext, true );
		}

		$errors = array_merge(
			[],
			$this->validates_presence_of( 'galleryid' ),
			$this->validates_presence_of( 'filename' ),
			$this->validates_presence_of( 'alttext' ),
			$this->validates_presence_of( 'exclude' ),
			$this->validates_presence_of( 'sortorder' ),
			$this->validates_presence_of( 'imagedate' ),
			$this->validates_numericality_of( 'galleryid' ),
			$this->validates_numericality_of( 'pid' ),
			$this->validates_numericality_of( 'sortorder' ),
			$this->validates_length_of(
				'filename',
				185,
				'<=',
				__( 'Image filenames may not be longer than 185 characters in length', 'nggallery' )
			)
		);

		return empty( $errors ) ? true : $errors;
	}
}
