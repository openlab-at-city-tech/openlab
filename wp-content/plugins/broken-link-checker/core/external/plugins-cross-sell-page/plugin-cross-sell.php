<?php
/**
 * WPMUDEV Plugin Cross-Sell module for free plugins.
 *
 * Used in free plugins to get a glimpse of other plugins offered by WPMU DEV.
 *
 * @since   1.0.0
 * @author  Panos Lyrakis
 * @link    https://wpmudev.com
 * @package WPMUDEV\Plugin_Cross_Sell
 */

namespace WPMUDEV\Modules;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Sub-module version.
if ( ! defined( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_VERSION' ) ) {
	define( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_VERSION', '1.0.0' );
}

// Sub-module directory.
if ( ! defined( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR' ) ) {
	define( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR', plugin_dir_path( __FILE__ ) );
}

// Sub-module url.
if ( ! defined( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_URL' ) ) {
	define( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_URL', plugin_dir_url( __FILE__ ) );
}

// Sub-module Assets url.
if ( ! defined( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_ASSETS_URL' ) ) {
	define( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_ASSETS_URL', untrailingslashit( WPMUDEV_MODULE_PLUGIN_CROSS_SELL_URL ) . '/assets' );
}

// Shared UI Version.
if ( ! defined( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_SUI_VERSION' ) ) {
	define( 'WPMUDEV_MODULE_PLUGIN_CROSS_SELL_SUI_VERSION', '2.12.24' );
}

/**
 * Sub-module Cross-Sell class.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'WPMUDEV\Modules\Plugin_Cross_Sell' ) ) {
	/**
	 * Module main class.
	 */
	final class Plugin_Cross_Sell {
		/**
		 * The DI container.
		 *
		 * @var Plugin_Cross_Sell\Container
		 */
		private $container = null;

		/**
		 * Initialize the module.
		 *
		 * @param array $props Module properties.
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct( array $props = array() ) {
			// Prepare the translation directory.
			$dir                      = ! empty( $props['translation_dir'] ) ? realpath( $props['translation_dir'] ) : false;
			$props['translation_dir'] = $dir ? wp_normalize_path( $dir ) : WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR . 'languages/';

			// Self-initialization of DI container.
			$this->container = new Plugin_Cross_Sell\Container();
			$this->container->set( 'submenu_data', $props );
			$this->container->set( 'utilities', new Plugin_Cross_Sell\Utilities() );

			$this->load();
		}

		/**
		 * Class initializer.
		 */
		public function load(): void {
			$submenu_params  = $this->container->get( 'submenu_data' );
			$translation_dir = ! empty( $submenu_params['translation_dir'] ) ? $submenu_params['translation_dir'] : WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR . 'languages/';

			load_plugin_textdomain(
				'plugin-cross-sell-textdomain',
				false,
				$translation_dir
			);

			// Create a new Loader instance and pass the DI container.
			$loader = new Plugin_Cross_Sell\Loader( $this->container );
			$loader->init();
		}
	}
}
