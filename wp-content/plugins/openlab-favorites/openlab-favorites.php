<?php
/**
 * Plugin Name:       OpenLab Favorites
 * Plugin URI:        https://openlab.citytech.cuny.edu/
 * Description:       A favorites tool for OpenLab groups
 * Version:           1.0.0
 * Requires at least: 5.4
 * Requires PHP:      5.6
 * Author:            OpenLab
 * Author URI:        https://openlab.citytech.cuny.edu/
 * icense:            GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       openlab-attributions
 * Domain Path:       /languages
 */

namespace OpenLab\Favorites;

const ROOT_DIR   = __DIR__;
const ROOT_FILE  = __FILE__;
const PLUGIN_VER = '1.0.0';

spl_autoload_register(
	function( $class ) {
		$prefix   = 'OpenLab\\Favorites\\';
		$base_dir = __DIR__ . '/src/';

		// Does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		// Get the relative class name.
		$relative_class = substr( $class, $len );

		// Swap directory separators and namespace to create filename.
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

add_action( 'bp_include', [ '\OpenLab\Favorites\App', 'init' ] );
