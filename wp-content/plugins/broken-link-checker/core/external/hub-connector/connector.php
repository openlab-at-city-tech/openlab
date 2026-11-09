<?php
/**
 * WPMUDEV Hub Connector module for free plugins.
 *
 * Used by wordpress.org hosted plugins to connect with the Hub.
 *
 * @since   1.0.0
 * @author  Joel James
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub;

// Base file.
if ( ! defined( '\WPMUDEV_HUB_CONNECTOR_FILE' ) ) {
	define( 'WPMUDEV_HUB_CONNECTOR_FILE', __FILE__ );
}

// Module version.
if ( ! defined( '\WPMUDEV_HUB_CONNECTOR_VERSION' ) ) {
	define( 'WPMUDEV_HUB_CONNECTOR_VERSION', '1.1.1' );
}

// SUI version.
if ( ! defined( '\WPMUDEV_HUB_CONNECTOR_SUI_VERSION' ) ) {
	define( 'WPMUDEV_HUB_CONNECTOR_SUI_VERSION', 'sui-2-12-24' );
}

if ( ! class_exists( '\WPMUDEV\Hub\Connector' ) ) {
	/**
	 * Class Connector.
	 */
	final class Connector {

		/**
		 * Instance holder.
		 *
		 * @var Connector $instance
		 */
		private static $instance;

		/**
		 * Instance obtaining method.
		 *
		 * @since 1.0.0
		 *
		 * @return static Called class instance.
		 */
		public static function get(): Connector {
			// Only if not already exist.
			if ( ! self::$instance instanceof Connector ) {
				self::$instance = new Connector();
			}

			return self::$instance;
		}

		/**
		 * Initialize the module.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		protected function __construct() {
			// Auto loader.
			spl_autoload_register( array( $this, 'autoload' ) );

			// Dashboard is active, bail.
			if ( class_exists( 'WPMUDEV_Dashboard' ) ) {
				return;
			}

			// WPMU DEV Hosting should use WPMU DEV Dashboard, bail.
			if ( ! empty( Connector\Data::get()->get_full_wpmu_dev_hosting_id() ) ) {
				return;
			}

			require_once __DIR__ . '/lib/vendor/autoload.php';

			// Init classes.
			Connector\Rest::get();
			Connector\Admin::get();
			Connector\Remote::get();
			Connector\Actions::get();
		}

		/**
		 * Set options for plugin.
		 *
		 * @since 1.0.0
		 *
		 * @param string $plugin  Plugin identifier.
		 * @param array  $options Options.
		 *
		 * @return void
		 */
		public function set_options( string $plugin, array $options = array() ) {
			Connector\Admin::get()->set_plugin_options( $plugin, $options );
		}

		/**
		 * Autoload class files for the module.
		 *
		 * @since 1.0.0
		 *
		 * @param string $class_name Class name to autoload.
		 */
		public function autoload( string $class_name ) {
			// Project-specific namespace prefix.
			$prefix = 'WPMUDEV\\Hub\\Connector\\';

			// Does the class use the namespace prefix?
			$len = strlen( $prefix );

			// Get the relative class name.
			$relative_class = substr( $class_name, $len );

			if ( ! empty( $relative_class ) ) {
				$path = explode( '\\', strtolower( str_replace( '_', '-', $relative_class ) ) );
				$file = array_pop( $path );
				$file = __DIR__ . '/inc/class-' . $file . '.php';

				// If the file exists, require it.
				if ( file_exists( $file ) ) {
					require_once $file;
				}
			}
		}
	}
}
