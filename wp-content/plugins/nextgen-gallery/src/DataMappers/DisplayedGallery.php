<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataMapper\WPPostDriver;
use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\Settings\Settings;

class DisplayedGallery extends WPPostDriver {

	protected static $instance = null;

	public $model_class = 'Imagely\NGG\DataTypes\DisplayedGallery';

	public function __construct() {
		parent::__construct( 'displayed_gallery' );
	}

	/**
	 * @return DisplayedGallery
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new DisplayedGallery();
		}
		return self::$instance;
	}

	/**
	 * @param \Imagely\NGG\DataTypes\DisplayedGallery $entity
	 * @return null|\Imagely\NGG\DataTypes\DisplayType
	 */
	public function get_display_type( $entity ) {
		$mapper = DisplayTypeMapper::get_instance();
		return $mapper->find_by_name( $entity->display_type );
	}

	public function has_method( $name ) {
		return method_exists( $this, $name );
	}

	/**
	 * Sets defaults needed for the entity
	 *
	 * @param \Imagely\NGG\DataTypes\DisplayedGallery $entity
	 */
	public function set_defaults( $entity ) {
		// Ensure that we have a settings array.
		if ( ! isset( $entity->display_settings ) ) {
			$entity->display_settings = [];
		}

		// Default ordering.
		$settings = Settings::get_instance();
		$this->set_default_value( $entity, 'order_by', $settings->get( 'galSort' ) );
		$this->set_default_value( $entity, 'order_direction', $settings->get( 'galSortDir' ) );

		// Ensure we have an exclusions array.
		$this->set_default_value( $entity, 'exclusions', [] );

		// Ensure other properties exist.
		$this->set_default_value( $entity, 'container_ids', [] );
		$this->set_default_value( $entity, 'excluded_container_ids', [] );
		$this->set_default_value( $entity, 'sortorder', [] );
		$this->set_default_value( $entity, 'entity_ids', [] );
		$this->set_default_value( $entity, 'returns', 'included' );

		// Set maximum_entity_count.
		$this->set_default_value( $entity, 'maximum_entity_count', $settings->get( 'maximum_entity_count' ) );
	}
}
