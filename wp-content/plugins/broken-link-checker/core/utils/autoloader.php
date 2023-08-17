<?php
/**
 * The core autoloader class.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC/Core/Utils
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

// Only if required.
if ( ! function_exists( 'wpmudev_blc_autoloader' ) ) {
	/**
	 * The autoload function being registered. If null, then the default implementation of spl_autoload() will be registered.
	 *
	 * @param string $class_name The fully-qualified name of the class to load.
	 *
	 * @since 2.0.0
	 */
	function wpmudev_blc_autoloader( $class_name ) {
		// If the specified $class_name does not include our namespace, duck out.
		if ( false === strpos( $class_name, 'WPMUDEV_BLC' ) ) {
			return;
		}

		// Split the class name into an array to read the namespace and class.
		$file_parts = explode( '\\', $class_name );

		$filepath = '';
		$filename = '';

		$namespace_parts = array_map(
			function ( $element ) {
				return strtolower( str_replace( '_', '-', $element ) );
			},
			$file_parts
		);
		$file_part       = str_replace( '_', '-', array_pop( $namespace_parts ) );

		array_shift( $namespace_parts );

		$namespace_path = implode( DIRECTORY_SEPARATOR, $namespace_parts );

		if ( in_array( 'Interfaces', $file_parts ) ) {
			$filename = "interface-{$file_part}.php";
		} elseif ( in_array( 'Traits', $file_parts ) ) {
			$filename = "trait-{$file_part}.php";
		} else {
			$filename = "class-{$file_part}.php";
		}

		$plugin_path    = rtrim( plugin_dir_path( dirname( __DIR__ ) ) );
		$namespace_path = path_join( $plugin_path, $namespace_path );
		$filepath       = wp_normalize_path( path_join( $namespace_path, $filename ) );

		if ( file_exists( $filepath ) ) {
			include_once $filepath;
		}
	}
}

/**
 * Register autoloader callback.
 */
spl_autoload_register( 'wpmudev_blc_autoloader' );
