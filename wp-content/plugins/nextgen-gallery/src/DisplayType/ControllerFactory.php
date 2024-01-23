<?php

namespace Imagely\NGG\DisplayType;

class ControllerFactory {

	protected static $registration = [];
	protected static $handlers     = [];
	protected static $instances    = [];

	protected static $mapping = [];

	/**
	 * @param string $id
	 * @param string $class_name
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
	}

	public static function get_registered() {
		return self::$registration;
	}

	/**
	 * @param string $id
	 * @return bool
	 */
	public static function has_controller( $id ) {
		return isset( self::$handlers[ $id ] );
	}

	/**
	 * @param string $id
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
