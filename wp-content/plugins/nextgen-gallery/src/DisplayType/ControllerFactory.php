<?php

namespace Imagely\NGG\DisplayType;

/**
 * Controller Factory for Display Types
 *
 * Manages registration and instantiation of display type controllers.
 */
class ControllerFactory {

	/**
	 * Array of registered controllers
	 *
	 * @var array
	 */
	protected static $registration = [];

	/**
	 * Array of controller handlers
	 *
	 * @var array
	 */
	protected static $handlers = [];

	/**
	 * Array of controller instances
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Array mapping display type names to aliases
	 *
	 * @var array
	 */
	protected static $mapping = [];

	/**
	 * Registered display type modules.
	 *
	 * @var array
	 */
	private static $registered_modules = [];

	/**
	 * Registers a display type controller
	 *
	 * @param string $id The controller ID.
	 * @param string $class_name The controller class name.
	 * @param array  $aliases Array of aliases for the controller.
	 * @return void
	 */
	public static function register_controller( $id, $class_name, $aliases = [] ) {
		self::$registration[ $id ] = $class_name;
		self::$handlers[ $id ]     = $class_name;
		self::$mapping[ $id ]      = $aliases;

		if ( is_array( $aliases ) ) {
			foreach ( $aliases as $alias ) {
				self::$handlers[ $alias ] = $class_name;
			}
		}

		\Imagely\NGG\Util\Installer::add_handler( $id, $class_name );

		self::$registered_modules[ $id ] = $class_name;
	}

	/**
	 * Gets all registered controllers
	 *
	 * @return array
	 */
	public static function get_registered() {
		return self::$registration;
	}

	public static function get_registered_modules() {
		return self::$registered_modules;
	}

	/**
	 * Checks if a controller is registered
	 *
	 * @param string $id The controller ID.
	 * @return bool
	 */
	public static function has_controller( $id ) {
		return isset( self::$handlers[ $id ] );
	}

	/**
	 * Gets a controller instance by ID
	 *
	 * @param string $id The controller ID.
	 * @return Controller|void
	 */
	public static function get_controller( $id ) {
		if ( ! self::has_controller( $id ) ) {
			return;
		}

		if ( ! isset( self::$instances[ $id ] ) ) {
			self::$instances[ $id ] = new self::$handlers[ $id ]();
		}

		return self::$instances[ $id ];
	}

	/**
	 * Gets the display type ID from name or alias
	 *
	 * @param string $name_or_alias The display type name or alias.
	 * @return string|null
	 */
	public static function get_display_type_id( $name_or_alias ) {
		if ( isset( self::$mapping[ $name_or_alias ] ) ) {
			return $name_or_alias;
		}

		foreach ( self::$mapping as $id => $ids ) {
			if ( in_array( $name_or_alias, $ids, true ) ) {
				return $id;
			}
		}
	}
}
