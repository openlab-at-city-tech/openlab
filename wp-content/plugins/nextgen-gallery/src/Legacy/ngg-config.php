<?php
/**
 * Bootstrap file for getting the ABSPATH constant to wp-load.php
 * This is requried when a plugin requires access not via the admin screen.
 *
 * If the wp-load.php file is not found, then an error will be displayed
 *
 * @package WordPress
 * @since Version 2.6
 */

/** Define the server path to the file wp-config here, if you placed WP-CONTENT outside the classic file structure */

$path = ''; // It should be end with a trailing slash

/** That's all, stop editing from here */

if ( ! defined( 'WP_LOAD_PATH' ) ) {

	$legacy_root = dirname( dirname( dirname( __DIR__ ) ) ) . '/';
	$legacy_root = dirname( dirname( $legacy_root ) ) . '/';

	if ( file_exists( $legacy_root . 'wp-load.php' ) ) {
		define( 'WP_LOAD_PATH', $legacy_root );
		define( 'WP_ADMIN', true );
	} elseif ( file_exists( $path . 'wp-load.php' ) ) {
			define( 'WP_LOAD_PATH', $path );
	} else {
		throw new RuntimeException( 'Could not find wp-load.php' );
	}
}

require_once WP_LOAD_PATH . 'wp-load.php';
