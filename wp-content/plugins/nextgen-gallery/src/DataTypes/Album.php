<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataMappers\Album as Mapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

class Album extends Model {

	public $albumdesc;
	public $exclude;
	public $extras_post_id;
	public $id;
	public $id_field = 'id';
	public $name;
	public $pageid;
	public $previewpic;
	public $slug;
	public $sortorder = [];

	public function get_primary_key_column() {
		return 'id';
	}

	public function get_mapper() {
		return Mapper::get_instance();
	}

	/**
	 * @param bool $models Unused
	 * @return array
	 * @TODO Remove $models attribute when Pro has reached the first stage of POPE removal compatibility
	 */
	public function get_galleries( $models = false ) {
		$mapper      = GalleryMapper::get_instance();
		$gallery_key = $mapper->get_primary_key_column();
		return $mapper->find_all( [ "{$gallery_key} IN %s", $this->sortorder ] );
	}

	public function validation() {
		$errors = array_merge(
			[],
			$this->validates_presence_of( 'name' ),
			$this->validates_numericality_of( 'previewpic' )
		);

		return empty( $errors ) ? true : $errors;
	}
}
