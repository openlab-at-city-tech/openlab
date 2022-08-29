<?php
namespace ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;

/**
 * ElementsKit_Lite autoloader.
 * Handles dynamically loading classes only when needed.
 *
 * @since 1.0.0
 */
class Autoloader {
	
	/**
	 * Run autoloader.
	 * Register a function as `__autoload()` implementation.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function run() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}
	
	/**
	 * Autoload.
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $class Class name.
	 */
	private static function autoload( $class_name ) {

		// If the class being requested does not start with our prefix
		// we know it's not one in our project.
		if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
			return;
		}
		
		$file_name = strtolower(
			preg_replace(
				array( '/\b' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
				array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
				$class_name
			)
		);

		// Compile our path from the corosponding location.
		$file = \ElementsKit_Lite::plugin_dir() . $file_name . '.php';
		
		// If a file is found.
		if ( file_exists( $file ) ) {
			// Then load it up!
			require_once $file;
		}
	}
}
