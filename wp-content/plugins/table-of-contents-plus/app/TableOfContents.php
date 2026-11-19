<?php
namespace AIOSEO\TableOfContents {
	// Exit if accessed directly.

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * The main TableOfContents class.
	 *
	 * @since 1.0.0
	 */
	final class TableOfContents {
		/**
		 * Holds the instance of the plugin currently in use.
		 *
		 * @since 1.0.0
		 *
		 * @var TableOfContents
		 */
		private static $instance;

		/**
		 * Plugin version for enqueueing, etc.
		 * The value is retrieved from the AIOSEO_TABLE_OF_CONTENTS_VERSION constant.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $version = '';

		/**
		 * Whether we're in a dev environment.
		 *
		 * @since 1.0.0
		 *
		 * @var bool
		 */
		public $isDev = false;

		/**
		 * Core class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Core\Core
		 */
		public $core;

		/**
		 * Database Schema class instance.
		 *
		 * @since 202607
		 *
		 * @var Db\Schema
		 */
		public $dbSchema;

		/**
		 * InternalOptions class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Options\InternalOptions
		 */
		public $internalOptions;

		/**
		 * Pre updates class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Main\PreUpdates
		 */
		public $preUpdates;

		/**
		 * Helpers class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Utils\Helpers
		 */
		public $helpers;

		/**
		 * Options class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Options\Options
		 */
		public $options;

		/**
		 * Updates class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Main\Updates
		 */
		public $updates;

		/**
		 * Action scheduler class.
		 *
		 * @since 1.0.0
		 *
		 * @var Utils\ActionScheduler
		 */
		public $actionScheduler;

		/**
		 * Access class.
		 *
		 * @since 1.0.0
		 *
		 * @var Utils\Access
		 */
		public $access;

		/**
		 * Main class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Main\Main
		 */
		public $main;

		/**
		 * API class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Api\Api
		 */
		public $api;

		/**
		 * Standalone class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Standalone\Standalone
		 */
		public $standalone;

		/**
		 * Notifications class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Admin\Notifications
		 */
		public $notifications;

		/**
		 * VueSettings class instance.
		 *
		 * @since 1.1.0
		 *
		 * @var Utils\VueSettings
		 */
		public $vueSettings;

		/**
		 * Admin class instance.
		 *
		 * @since 1.2.0
		 *
		 * @var Admin\Admin
		 */
		public $admin;

		/**
		 * The main TableOfContents Instance.
		 *
		 * Insures that only one instance of TableOfContents exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @return TableOfContents The Table of Contents Plus instance.
		 */
		public static function instance() {
			if ( null === self::$instance || ! self::$instance instanceof self ) {
				self::$instance = new self();

				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Initialize Table of Contents Plus!
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function init() {
			$this->constants();
			$this->includes();
			$this->preLoad();
			$this->load();
		}

		/**
		 * Setup plugin constants.
		 * All the path/URL related constants are defined in main plugin file.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function constants() {
			$defaultHeaders = [
				'name'    => 'Table of Contents Plus',
				'version' => 'Version',
			];

			$pluginData = get_file_data( AIOSEO_TABLE_OF_CONTENTS_FILE, $defaultHeaders );

			$constants = [
				'AIOSEO_TABLE_OF_CONTENTS_PLUGIN_BASENAME'  => plugin_basename( AIOSEO_TABLE_OF_CONTENTS_FILE ),
				'AIOSEO_TABLE_OF_CONTENTS_PLUGIN_NAME'      => 'Table of Contents Plus',
				'AIOSEO_TABLE_OF_CONTENTS_PLUGIN_URL'       => plugin_dir_url( AIOSEO_TABLE_OF_CONTENTS_FILE ),
				'AIOSEO_TABLE_OF_CONTENTS_VERSION'          => $pluginData['version'],
				'AIOSEO_TABLE_OF_CONTENTS_MARKETING_URL'    => 'https://aioseo.com/',
				'AIOSEO_TABLE_OF_CONTENTS_MARKETING_DOMAIN' => 'aioseo.com'
			];

			foreach ( $constants as $constant => $value ) {
				if ( ! defined( $constant ) ) {
					define( $constant, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound
				}
			}

			$this->version = AIOSEO_TABLE_OF_CONTENTS_VERSION;
		}

		/**
		 * Including the new files with PHP 5.3 style.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function includes() {
			$dependencies = [
				'/vendor/autoload.php',
				'/vendor/woocommerce/action-scheduler/action-scheduler.php'
			];

			foreach ( $dependencies as $path ) {
				if ( ! file_exists( AIOSEO_TABLE_OF_CONTENTS_DIR . $path ) ) {
					// Something is not right.
					status_header( 500 );
					wp_die( esc_html__( 'Plugin is missing required dependencies. Please contact support for more information.', 'table-of-contents-plus' ) );
				}
				require_once AIOSEO_TABLE_OF_CONTENTS_DIR . $path;
			}

			$this->loadVersion();
		}

		/**
		 * Load the version of the plugin we are currently using.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function loadVersion() {
			if (
				! class_exists( '\Dotenv\Dotenv' ) ||
				! file_exists( AIOSEO_TABLE_OF_CONTENTS_DIR . '/build/.env' )
			) {
				return;
			}

			$dotenv = \Dotenv\Dotenv::createUnsafeImmutable( AIOSEO_TABLE_OF_CONTENTS_DIR, '/build/.env' );
			$dotenv->load();

			$devPort = strtolower( getenv( 'VITE_AIOSEO_TABLE_OF_CONTENTS_DEV_PORT' ) );
			if ( ! empty( $devPort ) ) {
				$this->isDev = true;

				// Fix SSL certificate invalid in our local environments.
				add_filter( 'https_ssl_verify', '__return_false' );
			}
		}

		/**
		 * Runs before we load the plugin.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function preLoad() {
			$this->core            = new Core\Core();
			$this->helpers         = new Utils\Helpers();
			$this->dbSchema        = new Db\Schema();
			$this->internalOptions = new Options\InternalOptions();
			$this->preUpdates      = new Main\PreUpdates();
		}

		/**
		 * Load our classes.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load() {
			$this->options         = new Options\Options();
			$this->updates         = new Main\Updates();
			$this->actionScheduler = new Utils\ActionScheduler();
			$this->access          = new Utils\Access();
			$this->main            = new Main\Main();
			$this->api             = new Api\Api();
			$this->standalone      = new Standalone\Standalone();
			$this->notifications   = new Admin\Notifications();
			$this->admin           = new Admin\Admin();

			add_action( 'init', [ $this, 'loadInit' ], 999 );
		}

		/**
		 * Things that need to load after init.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function loadInit() {
			$this->vueSettings = new Utils\VueSettings( '_aioseo_table_of_contents_settings' );
		}
	}
}

namespace {
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * The function which returns the one AIOSEO instance.
	 *
	 * @since 1.0.0
	 *
	 * @return AIOSEO\TableOfContents\TableOfContents The instance.
	 */
	function aioseoTableOfContents() {
		return AIOSEO\TableOfContents\TableOfContents::instance();
	}
}