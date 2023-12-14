<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\WPModel;
use Imagely\NGG\DataMappers\DisplayType as Mapper;

class DisplayType extends WPModel {

	public $aliases        = [];
	public $default_source = '';
	public $entity_types;
	public $extras_post_id;
	public $filter;
	public $hidden_from_igw;
	public $hidden_from_ui;
	public $id_field;
	public $installed_at_version;
	public $meta_id;
	public $meta_key;
	public $meta_value;
	public $module_id;
	public $name                  = '';
	public $preview_image_relpath = '';
	public $preview_image_url;
	public $settings = [];
	public $title    = '';
	public $view_order;

	public function get_mapper() {
		return Mapper::get_instance();
	}

	public function get_order() {
		return NGG_DISPLAY_PRIORITY_BASE;
	}

	public function validation() {
		$errors = array_merge(
			[],
			$this->validates_presence_of( 'entity_types' ),
			$this->validates_presence_of( 'name' ),
			$this->validates_presence_of( 'title' )
		);

		return empty( $errors ) ? true : $errors;
	}
}
