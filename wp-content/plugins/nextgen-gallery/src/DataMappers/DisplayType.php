<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataMapper\WPPostDriver;
use Imagely\NGG\DisplayType\ControllerFactory;

class DisplayType extends WPPostDriver {

	public static $instance;

	public $model_class = 'Imagely\NGG\DataTypes\DisplayType';

	public function __construct() {
		// Define columns.
		$this->define_column( 'ID', 'BIGINT', 0 );
		$this->define_column( 'default_source', 'VARCHAR(255)' );
		$this->define_column( 'name', 'VARCHAR(255)' );
		$this->define_column( 'preview_image_relpath', 'VARCHAR(255)' );
		$this->define_column( 'title', 'VARCHAR(255)' );
		$this->define_column( 'view_order', 'BIGINT', NGG_DISPLAY_PRIORITY_BASE );
		$this->define_column( 'settings', 'MEDIUMTEXT' );

		$this->add_serialized_column( 'settings' );
		$this->add_serialized_column( 'entity_types' );

		parent::__construct( 'display_type' );
	}

	/**
	 * @return DisplayType|\Imagely\NGGPro\DataMappers\DisplayType
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$class          = apply_filters( 'ngg_datamapper_client_display_type', __CLASS__ );
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * @param string $name
	 * @return null|\Imagely\NGG\DataTypes\DisplayType
	 */
	public function find_by_name( $name ) {
		$retval = null;
		$this->select();
		$this->where( [ 'name = %s', $name ] );

		$results = $this->run_query();

		if ( ! $results ) {
			foreach ( $this->find_all() as $entity ) {
				if ( $entity->name == $name || ( isset( $entity->aliases ) && is_array( $entity->aliases ) && in_array( $name, $entity->aliases ) ) ) {
					$retval = $entity;
					break;
				}
			}
		} else {
			$retval = $results[0];
		}

		return $retval;
	}

	/**
	 * @param string|array $entity_type e.g. image, gallery, album
	 * @return null|\Imagely\NGG\DataTypes\DisplayType[]
	 */
	public function find_by_entity_type( $entity_type ) {
		$find_entity_types = is_array( $entity_type ) ? $entity_type : [ $entity_type ];

		$retval = null;
		foreach ( $this->find_all() as $display_type ) {
			foreach ( $find_entity_types as $entity_type ) {
				if ( isset( $display_type->entity_types ) && in_array( $entity_type, $display_type->entity_types ) ) {
					$retval[] = $display_type;
					break;
				}
			}
		}

		return $retval;
	}

	/**
	 * @param \Imagely\NGG\DataTypes\DisplayType $entity
	 * @return string
	 */
	public function get_post_title( $entity ) {
		return $entity->title;
	}

	/**
	 * @param \Imagely\NGG\DataTypes\DisplayType $entity
	 */
	public function set_defaults( $entity ) {
		if ( ! isset( $entity->settings ) ) {
			$entity->settings = [];
		}

		$this->set_default_value( $entity, 'aliases', [] );
		$this->set_default_value( $entity, 'default_source', '' );
		$this->set_default_value( $entity, 'hidden_from_igw', false );
		$this->set_default_value( $entity, 'hidden_from_ui', false ); // TODO: remove.
		$this->set_default_value( $entity, 'preview_image_relpath', '' );
		$this->set_default_value( $entity, 'settings', 'use_lightbox_effect', true );
		$this->set_default_value( $entity, 'view_order', NGG_DISPLAY_PRIORITY_BASE );

		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$this->set_default_value( $entity, 'settings', 'is_ecommerce_enabled', false );
		}

		// Ensure that no display settings are ever missing if the controller provides defaults.
		if ( ControllerFactory::has_controller( $entity->name ) ) {
			$controller = ControllerFactory::get_controller( $entity->name );
			if ( ! method_exists( $controller, 'get_default_settings' ) ) {
				return;
			}
			$entity->settings = array_merge( $controller->get_default_settings(), $entity->settings );
		}
	}
}
