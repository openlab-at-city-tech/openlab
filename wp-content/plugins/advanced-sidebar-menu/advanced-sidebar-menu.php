<?php
/**
 * Plugin Name: Advanced Sidebar Menu
 * Plugin URI: https://onpointplugins.com/advanced-sidebar-menu/
 * Description: Creates dynamic menus based on parent/child relationship of your pages or categories.
 * Author: OnPoint Plugins
 * Version: 8.2.0
 * Author URI: https://onpointplugins.com
 * Text Domain: advanced-sidebar-menu
 *
 * @package advanced-sidebar-menu
 */

if ( defined( 'ADVANCED_SIDEBAR_BASIC_VERSION' ) ) {
	return;
}

define( 'ADVANCED_SIDEBAR_BASIC_VERSION', '8.2.0' );
define( 'ADVANCED_SIDEBAR_MENU_REQUIRED_PRO_VERSION', '8.2.0' );
define( 'ADVANCED_SIDEBAR_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_MENU_URL', plugin_dir_url( __FILE__ ) );

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

		// Core.
		Cache::class           => 'Cache.php',
		Core::class            => 'Core.php',
		Debug::class           => 'Debug.php',
		List_Pages::class      => 'List_Pages.php',
		Notice::class          => 'Notice.php',
		Scripts::class         => 'Scripts.php',

		// Menus.
		Category::class        => 'Menus/Category.php',
		Menu_Abstract::class   => 'Menus/Menu_Abstract.php',
		Page::class            => 'Menus/Page.php',

		// Traits.
		Memoize::class         => 'Traits/Memoize.php',
		Singleton::class       => 'Traits/Singleton.php',

		// Walkers.
		Page_Walker::class     => 'Walkers/Page_Walker.php',

	];
	if ( isset( $classes[ $class ] ) ) {
		require __DIR__ . '/src/' . $classes[ $class ];
	}
}

spl_autoload_register( 'advanced_sidebar_menu_autoload' );

add_action( 'plugins_loaded', 'advanced_sidebar_menu_translate' );
/**
 * Load translations
 *
 * @return void
 */
function advanced_sidebar_menu_translate() {
	load_plugin_textdomain( 'advanced-sidebar-menu', false, 'advanced-sidebar-menu/languages' );
}

add_action( 'advanced-sidebar-menu/widget/page/after-form', 'advanced_sidebar_menu_widget_docs', 99, 2 );
add_action( 'advanced-sidebar-menu/widget/category/after-form', 'advanced_sidebar_menu_widget_docs', 99, 2 );

/**
 * Add a link to widget docs inside the widget.
 *
 * @param array     $instance - Widget settings.
 * @param WP_Widget $widget   - Current widget.
 */
function advanced_sidebar_menu_widget_docs( $instance, WP_Widget $widget ) {
	$anchor = Widget_Category::NAME === $widget->id_base ? 'categories-menu' : 'pages-menu';
	?>
	<p style="text-align: right">
		<a
			href="https://onpointplugins.com/advanced-sidebar-menu/#advanced-sidebar-<?php echo esc_attr( $anchor ); ?>"
			target="_blank"
			rel="noopener noreferrer">
			<?php esc_html_e( 'widget documentation', 'advanced-sidebar-menu' ); ?>
		</a>
	</p>
	<?php
}
