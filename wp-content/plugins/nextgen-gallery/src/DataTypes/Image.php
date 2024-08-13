<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataMappers\Image as Mapper;
use Imagely\NGG\DataStorage\Sanitizer;

class Image extends Model {

	public $alttext;
	public $description;
	public $exclude;
	public $extras_post_id;
	public $filename;
	public $galleryid;
	public $id_field = 'pid';
	public $image_slug;
	public $imagedate;
	public $meta_data = [];
	public $pid;
	public $post_id;
	public $sortorder;
	public $tags;
	public $updated_at;

	// TODO: remove this when get_pro_compat_level() >= 1.
	public $items = [];
	public $pricelist_id;
	public $title;

    public function get_primary_key_column() {
		return 'pid';
	}

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
				__( 'Image filenames may not be longer than 185 characters in length', 'nextgen-gallery' )
			)
		);

		return empty( $errors ) ? true : $errors;
	}
}
