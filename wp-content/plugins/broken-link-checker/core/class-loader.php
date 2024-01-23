<?php
/**
 * Class to boot up plugin.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DirectoryIterator;
use mysql_xdevapi\Exception;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use function file_exists;
use function is_dir;
use function is_null;
use function str_replace;
use function strtolower;
use function trailingslashit;

/**
 * Class WPMUDEV_BLC
 *
 * @package WPMUDEV_BLC\Core
 */
final class Loader extends Base {

	/**
	 * Scripts to be registered for frontend.
	 *
	 * @since 2.0.0
	 *
	 * @var array $scripts Front js scripts to be enqueued.
	 */
	public static $scripts = array();

	/**
	 * Scripts to be registered for backend.
	 *
	 * @since 2.0.0
	 *
	 * @var array $admin_scripts Admin js scripts to be enqueued.
	 */
	public static $admin_scripts = array();

	/**
	 * Styles to be registered for frontend.
	 *
	 * @since 2.0.0
	 *
	 * @var array $style Front end styles to be enqueued.
	 */
	public static $styles = array();

	/**
	 * Styles to be registered for backend.
	 *
	 * @since 2.0.0
	 *
	 * @var array $style Admin styles to be enqueued.
	 */
	public static $admin_styles = array();
	/**
	 * Settings helper class instance.
	 *
	 * @since  2.0.0
	 * @var object
	 *
	 */
	public $settings;

	/**
	 * Minimum supported php version.
	 *
	 * @since  2.0.0
	 * @var float
	 *
	 */
	public $php_version = '7.2';

	/**
	 * Minimum WordPress version.
	 *
	 * @since  2.0.0
	 * @var float
	 *
	 */
	public $wp_version = '5.2';

	/**
	 * Initialize functionality of the plugin.
	 *
	 * This is where we kick-start the plugin by defining
	 * everything required and register all hooks.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function __construct() {
		if ( ! $this->can_boot() ) {
			return;
		}

		$this->init();
	}

	/**
	 * Main condition that checks if plugin parts should conitnue loading.
	 *
	 * @return bool
	 */
	private function can_boot() {
		/**
		 * Checks
		 *  - PHP version
		 *  - WP Version
		 * If not then return.
		 */
		global $wp_version;

		return (
			version_compare( PHP_VERSION, $this->php_version, '>' ) &&
			version_compare( $wp_version, $this->wp_version, '>' )
		);
	}

	/**
	 * Register all the actions and filters.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function init() {
		// Initialize the core files and the app files.
		// Core files are the base files that the app classes can rely on.
		// Not all core files need to be initiated.
		$this->init_app();

		/*
		 * Setup plugin scripts
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'handle_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'handle_admin_scripts' ) );

		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpmudev_blc_after_core_init' );
	}

	/**
	 * Load all App modules.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function init_app() {
		// Load text domain.
		load_plugin_textdomain(
			'broken-link-checker',
			false,
			dirname( WPMUDEV_BLC_BASENAME ) . '/languages'
		);

		/*
		 * Load plugin components. (Admin pages, Shortcodes, Rest Endpoints etc).
		 * Structures that build the plugins features and ui.
		 */
		$this->load_components(
			apply_filters(
				'wpmudev_blc_load_components',
				array(
					'Action_Links',
					'Admin_Pages',
					'Admin_Notices',
					'Admin_Modals',
					'Rest_Endpoints',
					'Emails',
					'Webhooks',
					'Virtual_Posts',
					'Scheduled_Events',
					'Hub_Endpoints',
					'Options',
					//'Hooks',
				)
			)
		);
	}

	/**
	 * Loads components.
	 *
	 * @since 2.0.0
	 *
	 * @param array $components An array of components (root folder names).
	 */
	private function load_components( $components = array() ) {
		if ( ! empty( $components ) ) {
			array_map(
				array( $this, 'load_component' ),
				$components
			);
		}
	}

	/**
	 * Registers and enqueues plugin scripts and styles for backend
	 *
	 * @since 2.0.0
	 */
	public function handle_admin_scripts() {
		if ( ! empty( self::$admin_scripts ) ) {
			$this->handle_scripts( self::$admin_scripts );
		}

		if ( ! empty( self::$admin_styles ) ) {
			$this->handle_styles( self::$admin_styles );
		}
	}

	/**
	 * Registers and enqueues plugin styles for frontend
	 *
	 * @since 2.0.0
	 *
	 * @param array $scripts An array with all scripts to be enqueued.
	 *
	 */
	public function handle_scripts( $scripts = array() ) {
		if ( ! empty( $scripts ) ) {
			foreach ( $scripts as $handle => $script ) {
				$src             = $script['src'] ?? '';
				$deps            = $script['deps'] ?? array();
				$ver             = $script['ver'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
				$in_footer       = $script['in_footer'] ?? false;
				$has_translation = $script['translate'] ?? false;

				wp_register_script( $handle, $src, $deps, $ver, $in_footer );

				if ( isset( $script['localize'] ) ) {
					foreach ( $script['localize'] as $object_name => $localize_array ) {
						wp_localize_script( $handle, $object_name, $localize_array );
					}
				}

				wp_enqueue_script( $handle );

				if ( $has_translation ) {
					wp_set_script_translations(
						$handle,
						'broken-link-checker',
						plugin_dir_path( __FILE__ ) . 'languages'
					);
				}
			}
		}
	}

	/**
	 * Registers and enqueues plugin css files.
	 *
	 * @since 2.0.0
	 *
	 * @param array $styles An array with all styles to be enqueued.
	 *
	 */
	public function handle_styles( $styles = array() ) {
		if ( ! empty( $styles ) ) {
			foreach ( $styles as $handle => $style ) {
				$src   = $style['src'] ?? '';
				$deps  = $style['deps'] ?? array();
				$ver   = $style['ver'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
				$media = $style['media'] ?? 'all';

				wp_register_style( $handle, $src, $deps, $ver, $media );
				wp_enqueue_style( $handle );
			}
		}
	}

	/**
	 * Registers and enqueues plugin scripts and styles for backend
	 *
	 * @since 2.0.0
	 */
	public function handle_front_scripts() {
		if ( ! empty( self::$scripts ) ) {
			$this->handle_scripts( self::$scripts );
		}

		if ( ! empty( self::$styles ) ) {
			$this->handle_styles( self::$styles );
		}
	}

	/**
	 * Loads component's controller.
	 *
	 * @since 2.0.0
	 *
	 * @param string $component The component name which is the folder name that contains the component files (mvc etc).
	 * @param string $namespace The namespace where the component belongs to. Default is App which derives from the `plugin_path/app` main folder.
	 */
	private function load_component( $component = null, $namespace = 'App' ) {
		if ( ! is_null( $component ) ) {
			$component_path_part = str_replace( '_', '-', $component );
			$component_path      = trailingslashit( WPMUDEV_BLC_DIR ) . strtolower( trailingslashit( $namespace ) . trailingslashit( $component_path_part ) );

			if ( is_dir( $component_path ) ) {
				$component_dir = new DirectoryIterator( $component_path );

				foreach ( $component_dir as $fileinfo ) {

					if ( $fileinfo->isDir() && ! $fileinfo->isDot() ) {
						$component_item_dir = $fileinfo->getFilename();
						$component_item     = str_replace( '-', '_', $component_item_dir );

						if ( file_exists( trailingslashit( $component_path ) . trailingslashit( $component_item_dir ) . 'class-controller.php' ) ) {
							$component_item = "WPMUDEV_BLC\\{$namespace}\\{$component}\\{$component_item}\\Controller";

							try {
								if ( method_exists( $component_item::instance(), 'init' ) ) {
									$component_item::instance()->init();
								} else {
									throw new \Exception( 'Method init() is missing from class ' . get_class( $component_item::instance() ) );
								}
							} catch ( \Exception $e ) {
								error_log( $e->getMessage() );
							}

						}
					}
				}
			}
		}
	}

}
