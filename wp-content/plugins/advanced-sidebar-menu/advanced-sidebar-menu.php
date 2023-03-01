<?php
/**
 * Plugin Name: Advanced Sidebar Menu
 * Plugin URI: https://onpointplugins.com/advanced-sidebar-menu/
 * Description: Creates dynamic menus based on parent/child relationship of your pages or categories.
 * Author: OnPoint Plugins
 * Version: 9.0.5
 * Author URI: https://onpointplugins.com
 * Text Domain: advanced-sidebar-menu
 * Domain Path: /languages/
 * Network: false
 * Requires at least: 5.8.0
 * Requires PHP: 7.0.0
 *
 * @package advanced-sidebar-menu
 */

if ( defined( 'ADVANCED_SIDEBAR_BASIC_VERSION' ) ) {
	return;
}

define( 'ADVANCED_SIDEBAR_MENU_BASIC_VERSION', '9.0.5' );
define( 'ADVANCED_SIDEBAR_MENU_REQUIRED_PRO_VERSION', '9.0.0' );
define( 'ADVANCED_SIDEBAR_MENU_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_MENU_URL', plugin_dir_url( __FILE__ ) );

// @todo Remove once require PRO version 9.1.2+.
define( 'ADVANCED_SIDEBAR_BASIC_VERSION', ADVANCED_SIDEBAR_MENU_BASIC_VERSION ); //phpcs:ignore
define( 'ADVANCED_SIDEBAR_DIR', plugin_dir_path( __FILE__ ) ); //phpcs:ignore

use Advanced_Sidebar_Menu\Blocks\Block_Abstract;
use Advanced_Sidebar_Menu\Blocks\Categories;
use Advanced_Sidebar_Menu\Blocks\Pages;
use Advanced_Sidebar_Menu\Cache;
use Advanced_Sidebar_Menu\Core;
use Advanced_Sidebar_Menu\Debug;
use Advanced_Sidebar_Menu\List_Pages;
use Advanced_Sidebar_Menu\Menus\Category;
use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Menus\Page;
use Advanced_Sidebar_Menu\Notice;
use Advanced_Sidebar_Menu\Scripts;
use Advanced_Sidebar_Menu\Traits\Memoize;
use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Utils;
use Advanced_Sidebar_Menu\Walkers\Category_Walker;
use Advanced_Sidebar_Menu\Walkers\Page_Walker;
use Advanced_Sidebar_Menu\Widget\Category as Widget_Category;
use Advanced_Sidebar_Menu\Widget\Page as Widget_Page;
use Advanced_Sidebar_Menu\Widget\Widget_Abstract;

/**
 * Load the plugin
 *
 * @return void
 */
function advanced_sidebar_menu_load() {
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

	load_plugin_textdomain( 'advanced-sidebar-menu', false, 'advanced-sidebar-menu/languages' );
}

add_action( 'plugins_loaded', 'advanced_sidebar_menu_load' );

/**
 * Autoload classes from PSR4 src directory
 * Mirrored after Composer dump-autoload for performance
 *
 * @param string $class - class being loaded.
 *
 * @return void
 */
function advanced_sidebar_menu_autoload( $class ) {
	$classes = [
		// Widgets.
		Widget_Abstract::class => 'Widget/Widget_Abstract.php',
		Widget_Page::class     => 'Widget/Page.php',
		Widget_Category::class => 'Widget/Category.php',

		// Blocks.
		Block_Abstract::class  => 'Blocks/Block_Abstract.php',
		Categories::class      => 'Blocks/Categories.php',
		Pages::class           => 'Blocks/Pages.php',

		// Core.
		Cache::class           => 'Cache.php',
		Core::class            => 'Core.php',
		Debug::class           => 'Debug.php',
		List_Pages::class      => 'List_Pages.php',
		Notice::class          => 'Notice.php',
		Scripts::class         => 'Scripts.php',
		Utils::class           => 'Utils.php',

		// Menus.
		Category::class        => 'Menus/Category.php',
		Menu_Abstract::class   => 'Menus/Menu_Abstract.php',
		Page::class            => 'Menus/Page.php',

		// Traits.
		Memoize::class         => 'Traits/Memoize.php',
		Singleton::class       => 'Traits/Singleton.php',

		// Walkers.
		Category_Walker::class => 'Walkers/Category_Walker.php',
		Page_Walker::class     => 'Walkers/Page_Walker.php',

	];
	if ( isset( $classes[ $class ] ) ) {
		require __DIR__ . '/src/' . $classes[ $class ];
	}
}

spl_autoload_register( 'advanced_sidebar_menu_autoload' );
