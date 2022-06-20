<?php

namespace ElementsKit_Lite\Traits;

/**
 * Trait for making singleton instance
 * This is a factory singleton
 *
 * @package ElementsKit_Lite\Traits
 */
trait Singleton {
	private static $instances = array();

	public static function instance() {
		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new $class();
		}
		return self::$instances[ $class ];
	}
}
