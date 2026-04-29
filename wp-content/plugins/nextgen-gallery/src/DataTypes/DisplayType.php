<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\WPModel;
use Imagely\NGG\DataMappers\DisplayType as Mapper;

/**
 * Display Type data type class.
 *
 * Represents a display type entity with all its properties and configuration.
 */
class DisplayType extends WPModel {

	/**
	 * Display type aliases.
	 *
	 * @var array
	 */
	public $aliases = [];

	/**
	 * Default source for the display type.
	 *
	 * @var string
	 */
	public $default_source = '';

	/**
	 * Supported entity types.
	 *
	 * @var array
	 */
	public $entity_types;

	/**
	 * Extra post ID reference.
	 *
	 * @var int
	 */
	public $extras_post_id;

	/**
	 * Display type filter.
	 *
	 * @var string
	 */
	public $filter;

	/**
	 * Whether hidden from IGW.
	 *
	 * @var bool
	 */
	public $hidden_from_igw;

	/**
	 * Whether hidden from UI.
	 *
	 * @var bool
	 */
	public $hidden_from_ui;

	/**
	 * ID field name.
	 *
	 * @var string
	 */
	public $id_field;

	/**
	 * Version when installed.
	 *
	 * @var string
	 */
	public $installed_at_version;

	/**
	 * Meta ID.
	 *
	 * @var int
	 */
	public $meta_id;

	/**
	 * Meta robots value (legacy field from older NGG installations).
	 *
	 * @var string|null
	 */
	public $meta_robots;

	/**
	 * Meta key.
	 *
	 * @var string
	 */
	public $meta_key;

	/**
	 * Meta value.
	 *
	 * @var mixed
	 */
	public $meta_value;

	/**
	 * Module ID.
	 *
	 * @var string
	 */
	public $module_id;

	/**
	 * Display type name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Preview image relative path.
	 *
	 * @var string
	 */
	public $preview_image_relpath = '';

	/**
	 * Preview image URL.
	 *
	 * @var string
	 */
	public $preview_image_url;

	/**
	 * Display type settings.
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Display type title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * View order priority.
	 *
	 * @var int
	 */
	public $view_order;

	/**
	 * Gets the mapper instance for this model.
	 *
	 * @return Mapper The display type mapper instance.
	 */
	public function get_mapper() {
		return Mapper::get_instance();
	}

	/**
	 * Gets the display order priority.
	 *
	 * @return int The display order priority.
	 */
	public function get_order() {
		return NGG_DISPLAY_PRIORITY_BASE;
	}

	/**
	 * Validates the display type data.
	 *
	 * @return bool|array True if valid, array of errors if invalid.
	 */
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
