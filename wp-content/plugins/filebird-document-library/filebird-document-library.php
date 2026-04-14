<?php
/**
 * Plugin Name:   		FileBird Document Library (Lite)
 * Plugin URI:    		https://ninjateam.org/wordpress-media-library-folders/
 * Description:   		Display your documents and files in customizable list and grid gallery.
 * Version:       		3.0.8
 * Author:        		Ninja Team
 * Author URI:    		https://ninjateam.org
 * Text Domain:   		filebird-dl
 * Domain Path:   		/languages/
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'FBV_DL_DIR' ) ) {
	define( 'FBV_DL_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'FBV_DL_URL' ) ) {
	define( 'FBV_DL_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'FBV_DL_VERSION' ) ) {
	define( 'FBV_DL_VERSION', '3.0.8' );
}

function fbv_dl_init() {
	if ( ! \defined( 'NJFB_VERSION' ) ) {
		add_action( 'admin_notices', 'fbv_dl_missing_notice' );
		return;
	}

	if ( \version_compare( NJFB_VERSION, '5.0', '<' ) ) {
		add_action( 'admin_notices', 'fbv_dl_not_supported' );
		return;
	}

	add_filter(
		'fbv_blocks',
		function( $blocks ) {
			$blocks[] = 'DocumentLibrary';
			return $blocks;
		}
	);
	require_once FBV_DL_DIR . '/includes/Helpers.php';
	require_once FBV_DL_DIR . '/includes/DocumentLibrary.php';
	require_once FBV_DL_DIR . '/includes/DocumentLibraryShortcode.php';
}

function fbv_dl_missing_notice() {
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'FileBird Document Library (Lite) requires FileBird plugin to be installed and active. Please download %1$s or %2$s.', 'filebird-dl' ), '<a href="https://wordpress.org/plugins/filebird" target="_blank">FileBird</a>', '<a href="https://1.envato.market/FileBird-Pro-Media-Library" target="_blank">FileBird Pro</a>' ) . '</strong></p></div>';
}

function fbv_dl_not_supported() {
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'FileBird Document Library (Lite) requires %1$s or %2$s %3$s or greater to be installed and active.', 'filebird-dl' ), '<a href="https://wordpress.org/plugins/filebird" target="_blank">FileBird</a>', '<a href="https://1.envato.market/FileBird-Pro-Media-Library" target="_blank">FileBird Pro</a>', '5.0' ) . '</strong></p></div>';
}


spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__; // project-specific namespace prefix
		$base_dir = __DIR__ . '/includes'; // base directory for the namespace prefix

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) { // does the class use the namespace prefix?
			return; // no, move to the next registered autoloader
		}

		$relative_class_name = substr( $class, $len );
		$file                = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

add_action( 'plugins_loaded', 'fbv_dl_init' );
