<?php

namespace Imagely\NGG\DisplayType;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;

use Imagely\NGG\DataTypes\DisplayType;
use Imagely\NGG\Util\Transient;

/**
 * @deprecated
 * @TODO Remove this when get_pro_api_version() is at least 4.0
 */
class Installer {

	public function delete_duplicates( $name ) {
		$mapper  = DisplayTypeMapper::get_instance();
		$results = $mapper->find_all( [ 'name = %s', $name ] );
		if ( count( $results ) > 0 ) {
			array_pop( $results ); // the last should be the latest.
			foreach ( $results as $display_type ) {
				$mapper->destroy( $display_type );
			}
		}
		$mapper->flush_query_cache();
	}

	/**
	 * Installs a display type
	 *
	 * @param string $name
	 * @param array  $properties
	 */
	public function install_display_type( $name, $properties = [] ) {
		$this->delete_duplicates( $name );

		// Try to find the existing entity. If it doesn't exist, we'll create.
		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$mapper = \C_Display_Type_Mapper::get_instance();
		} else {
			$mapper = DisplayTypeMapper::get_instance();
		}

		$display_type = $mapper->find_by_name( $name );
		$mapper->flush_query_cache();

		if ( ! $display_type ) {
			if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
				$display_type = new \stdClass();
			} else {
				$display_type = new DisplayType();
			}
		}

		// Update the properties of the display type.
		$properties['name'] = $name;
		foreach ( $properties as $key => $val ) {
			$display_type->$key = $val;
		}

		// Save the entity.
		return $mapper->save( $display_type );
	}

	/**
	 * Uninstalls all display types
	 */
	public function uninstall_display_types() {
		$mapper = DisplayTypeMapper::get_instance();
		$mapper->delete()->run_query();
	}

	/**
	 * Installs displayed gallery sources
	 *
	 * @param bool $reset (optional) Unused
	 */
	public function install( $reset = false ) {
		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			return;
		}

		// Force Pro display types to register themselves.
		if ( class_exists( 'C_NextGen_Pro_Installer' ) ) {
			$pro_installer = new \C_NextGen_Pro_Installer();
			$pro_installer->install_display_types();
		} elseif ( class_exists( 'C_NextGen_Plus_Installer' ) ) {
			$plus_installer = new \C_NextGen_Plus_Installer();
			$plus_installer->install_display_types();
		} elseif ( class_exists( 'C_NextGen_Starter_Installer' ) ) {
			$plus_installer = new \C_NextGen_Starter_Installer();
			$plus_installer->install_display_types();
		}
	}

	/**
	 * Uninstalls this module
	 *
	 * @param bool $hard (optional) Unused
	 */
	public function uninstall( $hard = false ) {
		Transient::flush();
		$this->uninstall_display_types();
	}
}
