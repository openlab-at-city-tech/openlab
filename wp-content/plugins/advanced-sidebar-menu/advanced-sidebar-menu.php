<?php
/**
 * Plugin Name: Advanced Sidebar Menu
 * Plugin URI: https://onpointplugins.com/advanced-sidebar-menu/
 * Description: Creates dynamic menus based on parent/child relationship of your pages or categories.
 * Author: OnPoint Plugins
 * Version: 9.6.2
 * Author URI: https://onpointplugins.com
 * Text Domain: advanced-sidebar-menu
 * Domain Path: /languages/
 * Network: false
 * Requires at least: 6.2.0
 * Requires PHP: 7.4.0
 *
 * @package advanced-sidebar-menu
 */

if ( defined( 'ADVANCED_SIDEBAR_BASIC_VERSION' ) ) {
	return;
}

define( 'ADVANCED_SIDEBAR_MENU_BASIC_VERSION', '9.6.2' );
define( 'ADVANCED_SIDEBAR_MENU_REQUIRED_PRO_VERSION', '9.4.0' );
define( 'ADVANCED_SIDEBAR_MENU_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_MENU_URL', plugin_dir_url( __FILE__ ) );

use Advanced_Sidebar_Menu\Blocks\Categories;
use Advanced_Sidebar_Menu\Blocks\Pages;
use Advanced_Sidebar_Menu\Cache;
use Advanced_Sidebar_Menu\Core;
use Advanced_Sidebar_Menu\Debug;
use Advanced_Sidebar_Menu\Notice;
use Advanced_Sidebar_Menu\Scripts;

/**
 * Load the plugin
 *
 * @return void
 */
function advanced_sidebar_menu_load() {
	load_plugin_textdomain( 'advanced-sidebar-menu', false, 'advanced-sidebar-menu/languages' );

	Core::init();
	// Blocks.
	Categories::init();
	Pages::init();

	Cache::init();
	Debug::init();
	Notice::init();
	Scripts::init();

	if ( Notice::instance()->is_conflicting_pro_version() ) {
		remove_action( 'plugins_loaded', 'advanced_sidebar_menu_pro_init', 11 );
	}
}

add_action( 'plugins_loaded', 'advanced_sidebar_menu_load' );

/**
 * Autoload classes from PSR4 src directory.
 *
 * @param string $class_name - class being loaded.
 *
 * @return void
 */
function advanced_sidebar_menu_autoload( $class_name ) {
	$parts = \explode( '\\', $class_name );
	if ( 'Advanced_Sidebar_Menu' === \array_shift( $parts ) && \file_exists( __DIR__ . '/src/' . \implode( DIRECTORY_SEPARATOR, $parts ) . '.php' ) ) {
		require __DIR__ . '/src/' . \implode( DIRECTORY_SEPARATOR, $parts ) . '.php';
	}
}

spl_autoload_register( 'advanced_sidebar_menu_autoload' );

/**
 * Cleanup any caches on deactivation.
 */
register_deactivation_hook( __FILE__, function() {
	Cache::instance()->clear_cache_group();
} );
