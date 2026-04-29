<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataMappers\Album as Mapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

/**
 * Album Data Type
 *
 * Represents an album containing galleries in NextGEN Gallery.
 */
class Album extends Model {

	/**
	 * Album description
	 *
	 * @var string
	 */
	public $albumdesc;

	/**
	 * Galleries to exclude
	 *
	 * @var array
	 */
	public $exclude;

	/**
	 * WordPress post ID for extras
	 *
	 * @var int
	 */
	public $extras_post_id;

	/**
	 * Album ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ID field name
	 *
	 * @var string
	 */
	public $id_field = 'id';

	/**
	 * Album name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * WordPress page ID
	 *
	 * @var int
	 */
	public $pageid;

	/**
	 * Preview picture ID
	 *
	 * @var int
	 */
	public $previewpic;

	/**
	 * Album slug
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Sort order of galleries in album
	 *
	 * @var array
	 */
	public $sortorder = [];

	/**
	 * Date when the album was created.
	 *
	 * @var string
	 */
	public $date_created;

	/**
	 * Date when the album was last modified.
	 *
	 * @var string
	 */
	public $date_modified;

	/**
	 * Display type for the album.
	 *
	 * @var string
	 */
	public $display_type = 'photocrati-nextgen_basic_compact_album';

	/**
	 * Settings for the display type.
	 *
	 * @var array
	 */
	public $display_type_settings = [];

	/**
	 * Gets the primary key column name.
	 *
	 * @return string
	 */
	public function get_primary_key_column() {
		return 'id';
	}

	/**
	 * Gets the data mapper instance.
	 *
	 * @return Mapper
	 */
	public function get_mapper() {
		return Mapper::get_instance();
	}

	/**
	 * Gets all galleries in this album.
	 *
	 * @param bool $models Unused.
	 * @return array
	 * @TODO Remove $models attribute when Pro has reached the first stage of POPE removal compatibility
	 */
	public function get_galleries( $models = false ) {
		$mapper      = GalleryMapper::get_instance();
		$gallery_key = $mapper->get_primary_key_column();
		return $mapper->find_all( [ "{$gallery_key} IN %s", $this->sortorder ] );
	}

	/**
	 * Validates album data.
	 *
	 * @return bool|array True if valid, array of errors otherwise.
	 */
	public function validation() {
		$errors = array_merge(
			[],
			$this->validates_presence_of( 'name' ),
			$this->validates_numericality_of( 'previewpic' )
		);

		return empty( $errors ) ? true : $errors;
	}
}
