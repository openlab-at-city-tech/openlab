<?php

/*
Plugin Name: OpenLab Badges
Description: BuddyPress group badges, for the City Tech OpenLab.
Author: OpenLab at City Tech
Version: 0.1
Text Domain: openlab-badges
*/

define( 'OLBADGES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OLBADGES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register( function( $class ) {
	$prefix = 'OpenLab\\Badges\\';
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
} );

add_action( 'bp_include', function() {
	\OpenLab\Badges\App::init();
} );
