<?php
/**
 * Plugin Name: NextGEN Gallery
 * Description: The most popular gallery plugin for WordPress and one of the most popular plugins of all time with over 30 million downloads.
 * Version: 4.2.0
 * Author: Imagely
 * Plugin URI: https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/?utm_source=ngglite&utm_medium=pluginlist&utm_campaign=pluginuri
 * Author URI: https://www.imagely.com/?utm_source=ngglite&utm_medium=pluginlist&utm_campaign=authoruri
 * License: GPLv3
 * Text Domain: nggallery
 * Domain Path: /static/I18N/
 * Requires PHP: 7.0
 *
 * @package Nextgen Gallery
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Imagely\NGG\Admin\AMNotifications;
use Imagely\NGG\Admin\About;
use Imagely\NGG\Admin\MenuNudge;
use Imagely\NGG\Admin\Ecommerce_Preview;
use Imagely\NGG\Admin\Shortcode_Preview;
use Imagely\NGG\Admin\Onboarding_Wizard;
use Imagely\NGG\Admin\App;
use Imagely\NGG\Migrations\AddGalleryDates;

/**
 * Clean Exit Exception for graceful shutdown.
 *
 * @deprecated This class is deprecated and should not be used.
 */
class E_Clean_Exit extends RuntimeException {}

/**
 * NGG Error Exception for general NextGEN Gallery errors.
 *
 * @deprecated This class is deprecated and should not be used.
 */
class E_NggErrorException extends RuntimeException {} // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

/**
 * Thrown when a datamapper entity does not exist.
 *
 * @deprecated This class is deprecated and should not be used.
 */
class E_EntityNotFoundException extends RuntimeException {} // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

/**
 * Columns Not Defined Exception for database schema issues.
 *
 * @deprecated This class is deprecated and should not be used.
 */
class E_ColumnsNotDefinedException extends RuntimeException {} // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

/**
 * Thrown when an invalid data type is used as an entity.
 *
 * This exception is thrown when an invalid data type is used as an entity,
 * such as an associative array which is not supported.
 */
class E_InvalidEntityException extends RuntimeException {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Constructor.
	 *
	 * @param string|false $message Error message.
	 * @param int          $code Error code.
	 */
	public function __construct( $message = false, $code = 0 ) {
		// If no message was provided, create a default message.
		if ( ! $message ) {
			$message = 'Invalid data type used for entity. Please use stdClass or a subclass of \Imagely\NGG\DataMapper\Model';
		}
		parent::__construct( $message, $code );
	}
}

/**
 * Upload Exception for file upload failures.
 *
 * Thrown when a file upload operation fails during image processing
 * or gallery management operations.
 */
class E_UploadException extends RuntimeException {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Constructor.
	 *
	 * @param string    $message Error message.
	 * @param int       $code Error code.
	 * @param Exception $previous Previous exception.
	 */
	public function __construct( $message = '', $code = 0, $previous = null ) {
		if ( ! $message ) {
			$message = 'There was a problem uploading the file.';
		}
		parent::__construct( $message, $code, $previous );
	}
}

/**
 * Insufficient Write Access Exception for permission errors.
 *
 * Thrown when a file cannot be written due to insufficient filesystem
 * permissions or directory access restrictions.
 */
class E_InsufficientWriteAccessException extends RuntimeException {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Constructor.
	 *
	 * @param string|false $message Error message.
	 * @param string|null  $filename Filename that couldn't be written to.
	 * @param int          $code Error code.
	 * @param Exception    $previous Previous exception.
	 */
	public function __construct( $message = false, $filename = null, $code = 0, $previous = null ) {
		if ( ! $message ) {
			$message = 'Could not write to file. Please check filesystem permissions.';
		}
		if ( $filename ) {
			$message .= " Filename: {$filename}";
		}
		parent::__construct( $message, $code, $previous );
	}
}

/**
 * No Space Available Exception for storage capacity issues.
 *
 * Thrown when storage capacity has been exceeded during file operations
 * or when disk space quota limits are reached.
 */
class E_NoSpaceAvailableException extends RuntimeException {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Constructor.
	 *
	 * @param string    $message Error message.
	 * @param int       $code Error code.
	 * @param Exception $previous Previous exception.
	 */
	public function __construct( $message = '', $code = 0, $previous = null ) {
		if ( ! $message ) {
			$message = 'You have exceeded your storage capacity. Please remove some files and try again.';
		}
		parent::__construct( $message, $code, $previous );
	}
}

/**
 * No Image Library Exception for missing image processing capabilities.
 *
 * Thrown when the GD Image library is not available or when required
 * image processing extensions are missing from the PHP installation.
 */
class E_No_Image_Library_Exception extends RuntimeException {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Constructor.
	 *
	 * @param string    $message Error message.
	 * @param int       $code Error code.
	 * @param Exception $previous Previous exception.
	 */
	public function __construct( $message = '', $code = 0, $previous = null ) {
		if ( ! $message ) {
			$message = 'The site does not support the GD Image library. Please ask your hosting provider to enable it.';
		}
		parent::__construct( $message, $code, $previous );
	}
}

/**
 * Bootstrap class for NextGEN Gallery plugin initialization.
 *
 * Initializes all plugin hooks, actions, namespace autoloader, and defines constants
 * required for the NextGEN Gallery plugin to function properly.
 */
class C_NextGEN_Bootstrap {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Whether POPE library has been loaded.
	 *
	 * @var bool
	 */
	protected static $pope_loaded = false;

	/**
	 * Debug mode flag.
	 *
	 * @var bool
	 */
	public static $debug = false;

	/**
	 * Minimum required NGG Pro version.
	 *
	 * @var string
	 */
	public $minimum_ngg_pro_version = '2.0.5';

	/**
	 * Minimum required NGG Plus version.
	 *
	 * @var string
	 */
	public $minimum_ngg_plus_version = '1.0.1';

	/**
	 * Holds Notifications Object
	 *
	 * @var AMNotifications|null
	 */
	public $notifications = null;

	/**
	 * Constructor.
	 *
	 * Initializes the plugin, sets up autoloader, loads dependencies, and registers hooks.
	 */
	public function __construct() {

		if ( defined( 'NGG_ENABLE_SHUTDOWN_EXCEPTION_HANDLER' ) && NGG_ENABLE_SHUTDOWN_EXCEPTION_HANDLER ) {
			set_exception_handler( __CLASS__ . '::shutdown' );
		} elseif ( self::get_pro_api_version() < 4.0 && ( ! defined( 'NGG_DISABLE_SHUTDOWN_EXCEPTION_HANDLER' ) || ! NGG_DISABLE_SHUTDOWN_EXCEPTION_HANDLER ) ) { // NextGEN Pro still throws E_Clean_Exit rather than calling exit(), which must be handled by the shutdown handler.
			set_exception_handler( __CLASS__ . '::shutdown' );
		}

		spl_autoload_register( [ $this, 'autoloader' ] );

		// Allow Composer dependencies to be found and loaded.
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

		// If another plugin or theme with the POPE library (such as the legacy Photocrati theme) is active during.
		// the NextGEN activation process, it may produce warnings that can stop this plugin from activating.
		if ( ! $this->is_activating() && ! $this->is_topscorer_request() ) {
			$this->define_constants();
			$this->add_legacy_pro_compat();
			$this->register_hooks();

			include_once NGG_PLUGIN_DIR . '/src/Functions/deprecated.php';
			include_once NGG_PLUGIN_DIR . '/src/Functions/admin.php';
			include_once NGG_PLUGIN_DIR . '/src/Functions/compat.php';

			// Legacy NGG 1x code was not namespaced and must be loaded manually before POPE.
			include_once NGG_PLUGIN_DIR . '/src/Legacy/nggallery.php';

			$this->notifications = new AMNotifications();
			$this->notifications->hooks();

			if ( is_admin() ) {
				( new Onboarding_Wizard() )->hooks();
				( new About() )->hooks();
				( new MenuNudge() )->hooks();
				( new Ecommerce_Preview() )->hooks();
				AddGalleryDates::migrate( false );
			}
			// TODO maybe move back to only admin after all is done.
			( new App() )->hooks();
			Shortcode_Preview::hooks();

			$this->load_pope();

			do_action( 'ngg_initialized' );
		}
	}

	/**
	 * Shutdown handler for exceptions.
	 *
	 * This class will be removed if there's no complaints made after defaulting to the NGG_ENABLE_SHUTDOWN_EXCEPTION_HANDLER constant.
	 *
	 * @deprecated
	 * @param Exception $exception Optional exception to handle during shutdown.
	 */
	public static function shutdown( $exception = null ) {
		if ( is_null( $exception ) ) {
			$name = php_sapi_name();
			if ( false === strpos( $name, 'cgi' )
			&& version_compare( PHP_VERSION, '5.3.3' ) >= 0 ) {
				$status = session_status();
				if ( in_array( $status, [ PHP_SESSION_DISABLED, PHP_SESSION_NONE ], true ) ) {
					session_write_close();
				}
				fastcgi_finish_request();
			} else {
				exit();
			}
		} elseif ( ! ( $exception instanceof E_Clean_Exit ) ) {
			if ( ob_get_level() > 0 ) {
				ob_end_clean();
			}
			self::print_exception( $exception );
		}
	}

	/**
	 * Prints exception details for debugging purposes.
	 *
	 * @param Exception $exception The exception to print.
	 */
	public static function print_exception( $exception ) {
		$klass = get_class( $exception );
		echo '<h1>' . esc_html( $klass ) . ' thrown</h1>';
		echo '<p>' . esc_html( $exception->getMessage() ) . '</p>';
		if ( self::$debug || ( defined( 'NGG_DEBUG' ) && true == NGG_DEBUG ) ) {
			echo '<h3>Where:</h3>';
			echo '<p>On line <strong>' . esc_html( $exception->getLine() ) . '</strong> of <strong>' . esc_html( $exception->getFile() ) . '</strong></p>';
			echo '<h3>Trace:</h3>';
			echo '<pre>' . esc_html( $exception->getTraceAsString() ) . '</pre>';
			if ( method_exists( $exception, 'getPrevious' ) ) {
				$previous = $exception->getPrevious();
				if ( $previous ) {
					self::print_exception( $previous );
				}
			}
		}
	}

	/**
	 * PSR-4 autoloader for Imagely\NGG namespace.
	 *
	 * @param string $class_name Fully qualified class name.
	 */
	public function autoloader( $class_name ) {
		$prefix = 'Imagely\\NGG\\';

		$base_dir = __DIR__ . '/src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class_name, $len );

		$file = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	/**
	 * Checks if the current request is from the Top Scorer plugin.
	 *
	 * @return bool True if request is from Top Scorer.
	 */
	public function is_topscorer_request() {
		// Nonce verification is not necessary here: NextGEN skips loading certain components based on the URL in order
		// to maintain compatibility with the Top Scorer plugin.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return isset( $_SERVER['REQUEST_URI'] ) && strpos( wp_unslash( $_SERVER['REQUEST_URI'] ), 'topscorer/v1' ) !== false;
	}

	/**
	 * Checks if WordPress is currently activating a plugin.
	 *
	 * @return bool True if a plugin is being activated.
	 */
	public function is_activating(): bool {
		// Nonce verification is not necessary here: NextGEN will not load various components if the current URL
		// indicates that WordPress is in the process of activating a plugin.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		$retval = strpos( $request_uri, 'plugins.php' ) !== false && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], [ 'activate-selected' ] );

		if ( ! $retval && strpos( $request_uri, 'update.php' ) !== false && isset( $_REQUEST['action'] ) && 'install-plugin' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) && strpos( wp_unslash( $_REQUEST['plugin'] ), 'nextgen-gallery' ) === 0 ) {
			$retval = true;
		}

		if ( ! $retval && strpos( $request_uri, 'update.php' ) !== false && isset( $_REQUEST['action'] ) && 'activate-plugin' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) && strpos( wp_unslash( $_REQUEST['plugin'] ), 'nextgen-gallery' ) === 0 ) {
			$retval = true;
		}

		return $retval;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Adds legacy class aliases for NextGEN Pro compatibility.
	 *
	 * These methods will be removed over time once the announcement of their deprecation has been made public. In
	 * order to allow third party code to remain compatible this is not being placed behind a compatibility level
	 * check.
	 */
	public function add_legacy_pro_compat() {
		// Ensure classes are loaded before creating aliases.
		$aliases = [
			'\Imagely\NGG\Admin\FormManager'               => 'C_Form_Manager',
			'\Imagely\NGG\Admin\Notifications\Manager'     => 'C_Admin_Notification_Manager',
			'\Imagely\NGG\Admin\RequirementsManager'       => 'C_Admin_Requirements_Manager',
			'\Imagely\NGG\DataMappers\Album'               => 'C_Album_Mapper',
			'\Imagely\NGG\DataMappers\Gallery'             => 'C_Gallery_Mapper',
			'\Imagely\NGG\DataMappers\Image'               => 'C_Image_Mapper',
			'\Imagely\NGG\DataStorage\MetaData'            => 'C_NextGen_Metadata',
			'\Imagely\NGG\DataTypes\LegacyImage'           => 'C_Image_Wrapper',
			'\Imagely\NGG\DataTypes\Lightbox'              => 'C_NGG_Lightbox',
			'\Imagely\NGG\DisplayType\Installer'           => 'C_Display_Type_Installer',
			'\Imagely\NGG\DisplayType\InstallerProxy'      => 'C_Gallery_Display_Installer',
			'\Imagely\NGG\DisplayTypes\Taxonomy'           => 'C_Taxonomy_Controller',
			'\Imagely\NGG\Display\I18N'                    => 'M_I18N',
			'\Imagely\NGG\Display\Shortcodes'              => 'C_NextGen_Shortcode_Manager',
			'\Imagely\NGG\DisplayedGallery\Renderer'       => 'C_Displayed_Gallery_Renderer',
			'\Imagely\NGG\DisplayedGallery\TriggerManager' => 'C_Displayed_Gallery_Trigger_Manager',
			'\Imagely\NGG\DynamicStylesheets\Manager'      => 'C_Dynamic_Stylesheet_Controller',
			'\Imagely\NGG\DynamicThumbnails\Manager'       => 'C_Dynamic_Thumbnails_Manager',
			'\Imagely\NGG\IGW\ATPManager'                  => 'M_Attach_To_Post',
			'\Imagely\NGG\IGW\Controller'                  => 'C_Attach_Controller',
			'\Imagely\NGG\Settings\Settings'               => [ 'C_NextGen_Settings', 'C_Photocrati_Settings_Manager' ],
			'\Imagely\NGG\Util\Filesystem'                 => 'C_Fs',
			'\Imagely\NGG\Util\Installer'                  => 'C_Photocrati_Installer',
			'\Imagely\NGG\Util\Router'                     => 'C_Router',
			'\Imagely\NGG\Util\RoutingApp'                 => 'C_Routing_App',
			'\Imagely\NGG\Util\Serializable'               => 'C_NextGen_Serializable',
			'\Imagely\NGG\Util\Transient'                  => 'C_Photocrati_Transient_Manager',
		];

		foreach ( $aliases as $class => $alias_names ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}
			foreach ( (array) $alias_names as $alias ) {
				if ( class_exists( $alias, false ) || interface_exists( $alias, false ) || trait_exists( $alias, false ) ) {
					continue;
				}
				class_alias( $class, $alias );
			}
		}
	}

	/**
	 * Ensures NextGEN Gallery loads after NextGEN Pro/Plus if they are active.
	 */
	public function fix_loading_order() {
		// If a plugin wasn't activated/deactivated siliently, we can listen for these things.
		if ( did_action( 'activate_plugin' ) || did_action( 'deactivate_plugin' ) ) {
			return;
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} elseif ( isset( $_SERVER['REQUEST_URI'] ) && strpos( wp_unslash( $_SERVER['REQUEST_URI'] ), 'plugins' ) !== false ) {
			return;
		} elseif ( ! $this->is_page_request() ) {
			return;
		}

		$plugins = get_option( 'active_plugins' );

		// Remove NGG from the list.
		$ngg   = basename( __DIR__ ) . '/' . basename( __FILE__ );
		$order = [];
		foreach ( $plugins as $plugin ) {
			if ( $ngg !== $plugin ) {
				$order[] = $plugin;
			}
		}

		// Get the position of either NGG Pro or NGG Plus.
		$insert_at   = false;
		$order_count = count( $order );
		for ( $i = 0; $i < $order_count; $i++ ) {
			$plugin = $order[ $i ];
			if ( strpos( $plugin, 'nggallery-pro' ) !== false ) {
				$insert_at = $i + 1;
			} elseif ( strpos( $plugin, 'ngg-plus' ) !== false ) {
				$insert_at = $i + 1;
			}
		}

		// Re-insert NGG after Pro or Plus.
		if ( false === $insert_at || count( $order ) === $insert_at ) {
			$order[] = $ngg;
		} elseif ( 0 === $insert_at ) {
			array_unshift( $order, $ngg );
		} else {
			array_splice( $order, $insert_at, 0, [ $ngg ] );
		}

		if ( $order !== $plugins ) {
			$order = array_filter( $order );
			update_option( 'active_plugins', $order );
		}
	}

	/**
	 * Gets the directory path for a legacy module by its ID.
	 *
	 * @param string $module_id The module identifier.
	 * @return string|null The module directory path or null if not found.
	 */
	public static function get_legacy_module_directory( $module_id ) {
		// POPE is not loaded; return the result from a manual mapping of id -> directory name.
		$module_dir = null;

		if ( 'photocrati-nextgen_pagination' === $module_id ) {
			$module_dir = 'nextgen_pagination';
		}

		if ( 'photocrati-attach_to_post' === $module_id ) {
			$module_dir = 'attach_to_post';
		}
		if ( 'photocrati-cache' === $module_id ) {
			$module_dir = 'cache';
		}
		if ( 'photocrati-dynamic_stylesheet' === $module_id ) {
			$module_dir = 'dynamic_stylesheet';
		}
		if ( 'photocrati-dynamic_thumbnails' === $module_id ) {
			$module_dir = 'dynamic_thumbnails';
		}
		if ( 'photocrati-frame_communication' === $module_id ) {
			$module_dir = 'frame_communication';
		}
		if ( 'photocrati-fs' === $module_id ) {
			$module_dir = 'fs';
		}
		if ( 'photocrati-i18n' === $module_id ) {
			$module_dir = 'i18n';
		}
		if ( 'photocrati-lightbox' === $module_id ) {
			$module_dir = 'lightbox';
		}
		if ( 'photocrati-nextgen-data' === $module_id ) {
			$module_dir = 'nextgen_data';
		}
		if ( 'photocrati-nextgen-legacy' === $module_id ) {
			$module_dir = 'ngglegacy';
		}
		if ( 'photocrati-nextgen_basic_gallery' === $module_id ) {
			$module_dir = 'nextgen_basic_gallery';
		}
		if ( 'photocrati-nextgen_basic_thumbnails' === $module_id ) {
			$module_dir = 'nextgen_basic_gallery';
		}
		if ( 'photocrati-nextgen_basic_slideshow' === $module_id ) {
			$module_dir = 'nextgen_basic_gallery';
		}
		if ( 'photocrati-nextgen_basic_imagebrowser' === $module_id ) {
			$module_dir = 'nextgen_basic_imagebrowser';
		}
		if ( 'photocrati-nextgen_basic_singlepic' === $module_id ) {
			$module_dir = 'nextgen_basic_singlepic';
		}
		if ( 'photocrati-nextgen_basic_tagcloud' === $module_id ) {
			$module_dir = 'nextgen_basic_tagcloud';
		}
		if ( 'photocrati-nextgen_basic_templates' === $module_id ) {
			$module_dir = 'nextgen_basic_templates';
		}
		if ( 'photocrati-nextgen_block' === $module_id ) {
			$module_dir = 'nextgen_block';
		}
		if ( 'photocrati-nextgen_gallery_display' === $module_id ) {
			$module_dir = 'nextgen_gallery_display';
		}
		if ( 'photocrati-ajax' === $module_id ) {
			$module_dir = 'ajax';
		}

		if ( $module_dir ) {
			return NGG_MODULE_DIR . DIRECTORY_SEPARATOR . $module_dir;
		}

		if ( defined( 'NGG_PRO_MODULES_DIR' ) ) {
			// This is a bit of a hack, but it's easiest to just dump all of these here in one place.
			// The following are all Starter/Plus/Pro modules.
			if ( 'photocrati-nextgen_pro_captions' === $module_id ) {
				$module_dir = 'nextgen_pro_captions';
			}
			if ( 'photocrati-nextgen_pro_ecommerce' === $module_id ) {
				$module_dir = 'ecommerce';
			}
			if ( 'photocrati-auto_update' === $module_id ) {
				$module_dir = 'autoupdate';
			}
			if ( 'photocrati-auto_update-admin' === $module_id ) {
				$module_dir = 'autoupdate_admin';
			}
			if ( 'imagely-braintree' === $module_id ) {
				$module_dir = 'braintree';
			}
			if ( 'photocrati-cheque' === $module_id ) {
				$module_dir = 'cheque';
			}
			if ( 'photocrati-comments' === $module_id ) {
				$module_dir = 'comments';
			}
			if ( 'photocrati-coupons' === $module_id ) {
				$module_dir = 'coupons';
			}
			if ( 'photocrati-free_gateway' === $module_id ) {
				$module_dir = 'free_gateway';
			}
			if ( 'photocrati-galleria' === $module_id ) {
				$module_dir = 'galleria';
			}
			if ( 'photocrati-image_protection' === $module_id ) {
				$module_dir = 'image_protection';
			}
			if ( 'imagely-licensing' === $module_id ) {
				$module_dir = 'licensing';
			}
			if ( 'photocrati-nextgen_pro_albums' === $module_id ) {
				$module_dir = 'nextgen_pro_albums';
			}
			if ( 'photocrati-nextgen_pro_blog_gallery' === $module_id ) {
				$module_dir = 'nextgen_pro_blog_gallery';
			}
			if ( 'photocrati-nextgen_pro_film' === $module_id ) {
				$module_dir = 'nextgen_pro_film';
			}
			if ( 'photocrati-nextgen_pro_horizontal_filmstrip' === $module_id ) {
				$module_dir = 'nextgen_pro_horizontal_filmstrip';
			}
			if ( 'photocrati-nextgen_pro_i18n' === $module_id ) {
				$module_dir = 'nextgen_pro_i18n';
			}
			if ( 'photocrati-nextgen_pro_imagebrowser' === $module_id ) {
				$module_dir = 'nextgen_pro_imagebrowser';
			}
			if ( 'photocrati-nextgen_pro_lightbox' === $module_id ) {
				$module_dir = 'nextgen_pro_lightbox';
			}
			if ( 'photocrati-nextgen_pro_marketing' === $module_id ) {
				$module_dir = 'nextgen_pro_marketing';
			}
			if ( 'photocrati-nextgen_pro_masonry' === $module_id ) {
				$module_dir = 'nextgen_pro_masonry';
			}
			if ( 'photocrati-nextgen_pro_mosaic' === $module_id ) {
				$module_dir = 'nextgen_pro_mosaic';
			}
			if ( 'photocrati-nextgen_pro_proofing' === $module_id ) {
				$module_dir = 'nextgen_pro_proofing';
			}
			if ( 'photocrati-nextgen_pro_settings' === $module_id ) {
				$module_dir = 'nextgen_pro_settings';
			}
			if ( 'photocrati-nextgen_pro_sidescroll' === $module_id ) {
				$module_dir = 'nextgen_pro_sidescroll';
			}
			if ( 'photocrati-nextgen_pro_slideshow' === $module_id ) {
				$module_dir = 'nextgen_pro_slideshow';
			}
			if ( 'photocrati-nextgen_pro_thumbnail_grid' === $module_id ) {
				$module_dir = 'nextgen_pro_thumbnail_grid';
			}
			if ( 'photocrati-nextgen_pro_tile' === $module_id ) {
				$module_dir = 'nextgen_pro_tile';
			}
			if ( 'photocrati-pro-wpcli' === $module_id ) {
				$module_dir = 'nextgen_pro_wpcli';
			}
			if ( 'photocrati-paypal_checkout' === $module_id ) {
				$module_dir = 'paypal_checkout';
			}
			if ( 'photocrati-paypal_express_checkout' === $module_id ) {
				$module_dir = 'paypal_express_checkout';
			}
			if ( 'photocrati-paypal_standard' === $module_id ) {
				$module_dir = 'paypal_standard';
			}
			if ( 'photocrati-nextgen_picturefill' === $module_id ) {
				$module_dir = 'picturefill';
			}
			if ( 'imagely-pro-search' === $module_id ) {
				$module_dir = 'search';
			}
			if ( 'photocrati-stripe' === $module_id ) {
				$module_dir = 'stripe';
			}
			if ( 'photocrati-test_gateway' === $module_id ) {
				$module_dir = 'test_gateway';
			}

			if ( $module_dir ) {
				return NGG_PRO_MODULES_DIR . DIRECTORY_SEPARATOR . $module_dir;
			}
		}

		// The module was not in the list above, consult POPE if possible.
		if ( self::$pope_loaded ) {
			return \C_Component_Registry::get_instance()->get_module_dir( $module_id );
		}

		return $module_dir;
	}

	/**
	 * Loads the POPE framework (Plugin Object Polymorphic Extensibility).
	 *
	 * @param bool $force Force loading even if conditions don't require it.
	 */
	public function load_pope( $force = false ) {
		// We allow POPE to load if the requested URL/POST includes photocrati_ajax as it is still used by a few NextGEN
		// modules for XHR such as uploading images.
		//
		// Third-party image optimization plugins that require legacy C_Gallery_Storage class access:
		// - EWWW Image Optimizer: uses I_Gallery_Storage interface
		// - WP Smush: uses I_Gallery_Storage interface
		// - Imagify: adds their own mixin to C_Gallery_Storage
		// - ShortPixel: uses C_Gallery_Storage (detected via SHORTPIXEL_IMAGE_OPTIMISER_VERSION)
		//
		// Nonce verification is not necessary here.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $force
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only request parameter for routing
			&& empty( $_REQUEST['photocrati_ajax'] )
			&& ( self::get_pro_api_version() >= 4.0 && ! is_admin() )
			&& ! defined( 'EWWW_IMAGE_OPTIMIZER_VERSION' ) // EWWW uses I_Gallery_Storage.
			&& ! defined( 'WP_SMUSH_VERSION' ) // WP_SMUSH_VERSION uses I_Gallery_Storage.
			&& ! defined( 'IMAGIFY_VERSION' ) // Imagify adds their own mixin to C_Gallery_Storage.
			&& ! defined( 'SHORTPIXEL_IMAGE_OPTIMISER_VERSION' ) // ShortPixel uses C_Gallery_Storage.
			|| self::$pope_loaded ) {
			return;
		}

		// Pope requires a higher limit.
		$tmp = ini_get( 'xdebug.max_nesting_level' );
		if ( $tmp && (int) $tmp <= 300 ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.IniSet.Risky
			@ini_set( 'xdebug.max_nesting_level', 300 );
		}

		// Enforce interfaces.
		if ( property_exists( 'ExtensibleObject', 'enforce_interfaces' ) ) {
			ExtensibleObject::$enforce_interfaces = EXTENSIBLE_OBJECT_ENFORCE_INTERFACES;
		}

		// Get the component registry.
		$registry = C_Component_Registry::get_instance();

		// Add the default Pope factory utility, C_Component_Factory.
		$registry->add_utility( 'I_Component_Factory', 'C_Component_Factory' );

		// Load embedded products. Each product is expected to load any modules required.
		$registry->add_module_path( NGG_PRODUCT_DIR, 2 );
		$registry->load_all_products();

		// Give third-party plugins that opportunity to include their own products and modules.
		do_action( 'load_nextgen_gallery_modules', $registry );

		// Initializes all loaded modules.
		$registry->initialize_all_modules();

		self::$pope_loaded = true;
	}

	/**
	 * Checks if the installed NextGEN Pro version is compatible.
	 *
	 * @return bool True if compatible or not installed, false if incompatible.
	 */
	public function is_pro_compatible() {
		$retval = true;

		if ( defined( 'NEXTGEN_GALLERY_PRO_VERSION' ) ) {
			$retval = false;
		}
		if ( defined( 'NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME' ) && ! defined( 'NGG_PRO_PLUGIN_VERSION' ) ) {
			$retval = false; // 1.0 - 1.0.6
		}
		if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) && version_compare( NGG_PRO_PLUGIN_VERSION, $this->minimum_ngg_pro_version ) < 0 ) {
			$retval = false;
		}
		if ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) && version_compare( NGG_PLUS_PLUGIN_VERSION, $this->minimum_ngg_plus_version ) < 0 ) {
			$retval = false;
		}

		return $retval;
	}

	/**
	 * Renders a warning notice about NextGEN Pro incompatibility.
	 */
	public function render_incompatibility_warning() {
		echo '<div class="updated error"><p>';
		echo esc_html(
			sprintf(
				/* translators: %1$s: current NextGEN Gallery version, %2$s: minimum required NextGEN Pro version */
				__(
					'NextGEN Gallery %1$s is incompatible with this version of NextGEN Pro. Please update NextGEN Pro to version %2$s or higher to restore NextGEN Pro functionality.',
					'nggallery'
				),
				NGG_PLUGIN_VERSION,
				$this->minimum_ngg_pro_version
			)
		);
		echo '</p></div>';
	}

	/**
	 * Renders a warning notice about jQuery compatibility issues with WordPress 5.5 and Pro/Plus versions.
	 */
	public function render_jquery_wp_55_warning() {
		$render = false;
		global $wp_version;

		$message     = '';
		$account_msg = '';

		if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) && version_compare( NGG_PRO_PLUGIN_VERSION, '3.1' ) < 0 ) {
			$render      = true;
			$message     = __( 'Your version of NextGEN Pro is known to have some issues with NextGEN Gallery 3.4 and later.', 'nggallery' );
			$account_msg = preg_match( '#photocrati#i', wp_get_theme()->get( 'Name' ) )
				/* translators: %s: account URL */
				? sprintf( __( "Please download the latest version of NextGEN Pro from your <a href='%s' target='_blank'>account area</a>", 'nggallery' ), 'https://members.photocrati.com/account/' )
				/* translators: %s: account URL */
				: sprintf( __( "Please download the latest version of NextGEN Pro from your <a href='%s' target='_blank'>account area</a>", 'nggallery' ), 'https://www.imagely.com/account/' );
		}

		if ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) && version_compare( NGG_PLUS_PLUGIN_VERSION, '1.7' ) < 0 ) {
			$render      = true;
			$message     = __( 'Your version of NextGEN Plus is known to have some issues with NextGEN Gallery 3.4 and later.', 'nggallery' );
			$account_msg = preg_match( '#photocrati#i', wp_get_theme()->get( 'Name' ) )
				/* translators: %s: account URL */
				? sprintf( __( "Please download the latest version of NextGEN Plus from your <a href='%s' target='_blank'>account area</a>", 'nggallery' ), 'https://members.photocrati.com/account/' )
				/* translators: %s: account URL */
				: sprintf( __( "Please download the latest version of NextGEN Plus from your <a href='%s' target='_blank'>account area</a>", 'nggallery' ), 'https://www.imagely.com/account/' );
		}

		if ( ! $render ) {
			return;
		}

		print '<div class="updated error"><p>';
		print wp_kses_post( $message );
		print ' ';
		print wp_kses_post( $account_msg );

		if ( version_compare( $wp_version, '5.5', '>=' ) && version_compare( $wp_version, '5.5.9', '<=' ) ) {
			$note = __( "NOTE: The autoupdater doesn't work on the version of WordPress you have installed.", 'nggallery' );
			print "<div style='font-weight: bold;'>";
			print esc_html( $note );
			print '</div>';
		}
		print '</p></div>';
	}

	/**
	 * Registers all WordPress hooks for the plugin.
	 */
	public function register_hooks() {
		// The core installers must be registered before the rest of the initialization is processed.
		\Imagely\NGG\Util\Installer::add_handler( 'legacy-core', 'C_NGG_Legacy_Installer' );
		\Imagely\NGG\Util\Installer::add_handler( 'settings', '\Imagely\NGG\Settings\Installer' );
		\Imagely\NGG\Util\Installer::add_handler( 'pro_display_types', '\Imagely\NGG\DisplayType\Installer' );

		// Register the (de)activation routines.
		add_action( 'deactivate_' . NGG_PLUGIN_BASENAME, [ $this, 'deactivate' ] );
		add_action( 'activate_' . NGG_PLUGIN_BASENAME, [ $this, 'activate' ], -10 );

		// Handle activation redirect to overview page.
		add_action( 'admin_init', [ $this, 'handle_activation_redirect' ] );

		// Ensure that settings manager is saved as an array.
		add_filter( 'pre_update_option_ngg_options', [ $this, 'persist_settings' ] );
		add_filter( 'pre_update_site_option_ngg_options', [ $this, 'persist_settings' ] );

		// Delete displayed gallery transients periodically.
		if ( NGG_CRON_ENABLED ) {
			// phpcs:ignore WordPress.WP.CronInterval.ChangeDetected -- Interval is defined by NGG_CRON_SCHEDULE constant.
			add_filter( 'cron_schedules', [ $this, 'add_ngg_schedule' ] );
			add_action( 'ngg_delete_expired_transients', [ $this, 'delete_expired_transients' ] );
			add_action( 'wp', [ $this, 'schedule_cron_jobs' ] );
		}

		// Start the plugin!
		add_action( 'init', [ $this, 'update' ], ( PHP_INT_MAX - 2 ) );
		add_action( 'init', [ $this, 'route' ], 5 );

		// NGG extension plugins should be loaded in a specific order.
		add_action( 'shutdown', [ $this, 'fix_loading_order' ] );

		if ( ! $this->is_pro_compatible() ) {
			add_action( 'all_admin_notices', [ $this, 'render_incompatibility_warning' ] );
		}

		add_action( 'admin_init', [ '\Imagely\NGG\Admin\RequirementsManager', 'register_requirements' ], -20 );
		add_action( 'all_admin_notices', [ $this, 'render_jquery_wp_55_warning' ] );

		// Necessary hack for the datamapper to order query results.
		add_filter( 'posts_orderby', [ '\Imagely\NGG\DataStorage\Manager', 'wp_query_order_by' ], 10, 2 );
		add_filter( 'init', [ '\Imagely\NGG\DataStorage\Manager', 'register_custom_post_types' ] );

		add_action( 'rest_api_init', [ '\Imagely\NGG\REST\Manager', 'rest_api_init' ] );

		// Widgets registration.
		add_action(
			'widgets_init',
			function () {
				register_widget( '\Imagely\NGG\Widget\Gallery' );
				register_widget( '\Imagely\NGG\Widget\Slideshow' );
				register_widget( '\Imagely\NGG\Widget\MediaRSS' );
			}
		);

		add_action( 'wp_enqueue_scripts', [ \Imagely\NGG\Display\LightboxManager::get_instance(), 'maybe_enqueue' ] );

		add_action(
			'ngg_delete_image',
			function () {
				\Imagely\NGG\Util\Transient::flush( 'random_widget_gallery_ids' );
			}
		);

		add_filter( 'xmlrpc_methods', [ '\Imagely\NGG\XMLRPC\Manager', 'add_methods' ] );

		// Nonce verification is not necessary here: it is performed by the notification manager's serve_ajax_request() method.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		add_action(
			'init',
			function () {
				if ( isset( $_REQUEST['ngg_dismiss_notice'] ) ) {
					// Admin notification handlers are registered in the nextgen_admin module.
					$this->load_pope( true );
					\Imagely\NGG\Admin\Notifications\Manager::get_instance()->serve_ajax_request();
				}
			}
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// The following is a hack: we cannot move the Lightroom XHR requests to the REST API because the URL are coded
		// into the lightroom plugin and have no autodiscovery. To avoid dependencies on the deprecated ajax POPE module
		// we only add the 'init' hook here if the URL matches.
		$lightroom_actions = [
			'enqueue_nextgen_api_task_list',
			'get_nextgen_api_path_list',
			'execute_nextgen_api_task_list',
			'get_nextgen_api_token',
		];

		// Nonce verification is not necessary here: authentication is handled by the Lightroom\Controller class.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $_REQUEST['photocrati_ajax'] )
		&& ! empty( $_REQUEST['action'] )
		&& '1' === (string) ( wp_unslash( $_REQUEST['photocrati_ajax'] ) )
		&& in_array( $_REQUEST['action'], $lightroom_actions, true ) ) {
			add_action(
				'init',
				function () {
					$this->register_taxonomy();
					( new \Imagely\NGG\Lightroom\Controller() )->run();
				},
				0
			);
		} else {
			add_action( 'init', [ $this, 'register_taxonomy' ], 9 );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\Imagely\NGG\WPCLI\Manager::register();
		}

		// This is necessary because NextGEN can insert singlepicture images as pre-rendered HTML without a shortcode.
		// This requires we inspect the site content and enqueue the display type's CSS if necessary.
		add_filter(
			'the_content',
			function ( $content ) {
				if ( preg_match( '#<img.*ngg-singlepic.*>#', $content, $matches ) ) {
					wp_enqueue_style(
						'nextgen_basic_singlepic_style',
						\Imagely\NGG\Display\StaticAssets::get_url(
							'SinglePicture/nextgen_basic_singlepic.css',
							'photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic.css'
						),
						[],
						NGG_SCRIPT_VERSION
					);
				}
				return $content;
			},
			PHP_INT_MAX,
			1
		);

		// Component registration.
		\Imagely\NGG\DataMapper\Manager::register_hooks();
		\Imagely\NGG\DisplayType\Manager::register();
		\Imagely\NGG\Display\DisplayManager::register_hooks();
		\Imagely\NGG\Display\I18N::get_instance()->register_hooks();
		\Imagely\NGG\Display\ResourceManager::init();
		\Imagely\NGG\Display\Shortcodes::get_instance()->register_hooks();
		\Imagely\NGG\IGW\ATPManager::get_instance()->register_hooks();
		\Imagely\NGG\IGW\BlockManager::get_instance()->register_hooks();
		\Imagely\NGG\IGW\EventPublisher::get_instance()->register_hooks();
		\Imagely\NGG\Util\Router::get_instance()->register_hooks();
		\Imagely\NGG\Util\ThirdPartyCompatibility::get_instance()->register_hooks();
		( new \Imagely\NGG\Util\UsageTracking() )->hooks();
	}

	/**
	 * Registers the NextGEN taxonomy.
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		// Register the NextGEN taxonomy.
		$args = [
			'label'    => __( 'Picture tag', 'nggallery' ),
			'template' => __( 'Picture tag: %2$l.', 'nggallery' ),
			'helps'    => __( 'Separate picture tags with commas.', 'nggallery' ),
			'sort'     => true,
			'args'     => [ 'orderby' => 'term_order' ],
		];

		register_taxonomy( 'ngg_tag', 'nggallery', $args );
	}

	/**
	 * Handles redirect after plugin activation.
	 */
	public function handle_activation_redirect() {
		// Check if it is new install or not.
		$envira_display_welcome = get_option( 'ngg_wizard' );

		if ( get_transient( 'ngg-activated' ) ) {

			if ( ! $envira_display_welcome ) {
				// New install.
				update_option( 'ngg_wizard', 'yes' );
				wp_safe_redirect( admin_url( '/index.php?page=nextgen-gallery-setup-wizard' ) );
			} else {
				// Existing install.
				update_option( 'ngg_wizard', 'no' );
				wp_safe_redirect( admin_url( '/admin.php?page=imagely&tab=general' ) );
			}
			delete_transient( 'ngg-activated' );
			exit;

		}
	}

	/**
	 * Schedules cron jobs for the plugin.
	 */
	public function schedule_cron_jobs() {
		if ( ! wp_next_scheduled( 'ngg_delete_expired_transients' ) ) {
			wp_schedule_event( time(), 'ngg_custom', 'ngg_delete_expired_transients' );
		}
	}

	/**
	 * Adds custom cron schedule interval.
	 *
	 * @param array $schedules Existing WordPress cron schedules.
	 * @return array Modified schedules array.
	 */
	public function add_ngg_schedule( $schedules ) {
		// phpcs:ignore Universal.Arrays.DuplicateArrayKey.Found -- False positive: checking if key exists before accessing nested value.
		if ( ! isset( $schedules['ngg_custom'] ) || $schedules['ngg_custom']['interval'] !== NGG_CRON_SCHEDULE ) {
			$schedules['ngg_custom'] = [
				'interval' => NGG_CRON_SCHEDULE,
				/* translators: %d: NGG_CRON_SCHEDULE constant -- do not translate. */
				'display'  => sprintf( esc_html__( 'Every %d seconds', 'nggallery' ), NGG_CRON_SCHEDULE ),
			];
		}

		return $schedules;
	}

	/**
	 * Deletes expired transients from the database.
	 */
	public function delete_expired_transients() {
		\Imagely\NGG\Util\Transient::get_instance()->flush_expired();
	}

	/**
	 * Ensure that settings are persisted as an array
	 *
	 * @param Imagely\NGG\Settings\Settings|array $settings Settings object or array to persist.
	 * @return array
	 */
	public function persist_settings( $settings = [] ) {
		if ( $settings instanceof \Imagely\NGG\Settings\ManagerBase ) {
			$settings = $settings->to_array();
		}
		return $settings;
	}

	/**
	 * Runs database updates if necessary.
	 */
	public function update() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Nonce verification is not necessary here.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['doing_wp_cron'] ) ) {
			return;
		}

		$this->load_pope();
		\Imagely\NGG\Util\Installer::update();
	}

	/**
	 * Sets up routing for dynamic image and stylesheet generation.
	 */
	public function route() {
		$this->load_pope();
		$router = \Imagely\NGG\Util\Router::get_instance();

		// Set context to path if subdirectory install.
		$parts     = wp_parse_url( $router->get_base_url( false ) );
		$siteparts = wp_parse_url( get_option( 'home' ) );

		if ( isset( $parts['path'] ) && isset( $siteparts['path'] ) ) {
			if ( strpos( $parts['path'], '/index.php' ) === false ) {
				$router->context = $siteparts['path'];
			} else {
				$new_parts = explode( '/index.php', $parts['path'] );
				if ( ! empty( $new_parts[0] ) && $siteparts['path'] === $new_parts[0] ) {
					$router->context = array_shift( $new_parts );
				}
			}
		}

		$dynamic_thumbs_app = $router->create_app( '/nextgen-image' );
		$dynamic_thumbs_app->rewrite( '/{*}', '/' );
		$dynamic_thumbs_app->route(
			[ '/' ],
			[
				'controller' => '\Imagely\NGG\DynamicThumbnails\Controller',
				'action'     => 'index_action',
			]
		);

		$dynamic_styles_app = $router->create_app( '/nextgen-dcss' );
		$dynamic_styles_app->rewrite( '/{\d}/{*}', '/index--{1}/data--{2}' );
		$dynamic_styles_app->route(
			[ '/' ],
			[
				'controller' => '\Imagely\NGG\DynamicStylesheets\Controller',
				'action'     => 'index_action',
			]
		);

		$igw_app = $router->create_app( '/' . NGG_ATTACH_TO_POST_SLUG );
		$igw_app->rewrite( '/preview/{id}', '/preview/id--{id}' );
		$igw_app->rewrite( '/display_tab_js/{id}', '/display_tab_js/id--{id}' );
		$igw_app->route(
			[ '/' ],
			[
				'controller' => '\Imagely\NGG\IGW\Controller',
				'action'     => 'index_action',
			]
		);

		// Provide a means for modules/third-parties to configure routes.
		do_action_ref_array( 'ngg_routes', [ $router ] );

		// Serve the routes.
		if ( ! $router->serve_request() && $router->has_parameter_segments() ) {
			$router->passthru();
		}
	}

	/**
	 * Checks if the current request is a page request (not AJAX, cron, or internal).
	 *
	 * @return bool True if this is a regular page request.
	 */
	public function is_page_request() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && ! ( defined( 'DOING_CRON' ) && DOING_CRON ) && ! ( defined( 'NGG_AJAX_SLUG' ) && isset( $_SERVER['REQUEST_URI'] ) && strpos( wp_unslash( $_SERVER['REQUEST_URI'] ), NGG_AJAX_SLUG ) !== false );
	}

	/**
	 * Handles plugin deactivation tasks.
	 */
	public function deactivate() {
		include_once 'products/photocrati_nextgen/class.nextgen_product_installer.php';
		\Imagely\NGG\Util\Installer::add_handler( NGG_PLUGIN_BASENAME, 'C_NextGen_Product_Installer' );
		\Imagely\NGG\Util\Installer::uninstall( NGG_PLUGIN_BASENAME );
	}

	/**
	 * Handles plugin activation tasks.
	 */
	public function activate() {
		\Imagely\NGG\Util\Installer::set_role_caps();
		set_transient( 'ngg-activated', time(), 120 );

		$over_time = get_option( 'nextgen_over_time', [] );
		if ( empty( $over_time['installed_lite'] ) ) {
			$over_time['installed_lite'] = wp_date( 'U' );
			update_option( 'nextgen_over_time', $over_time );
		}
	}

	/**
	 * Defines all plugin constants.
	 */
	public function define_constants() {
		define( 'NGG_PLUGIN', basename( $this->directory_path() ) );
		define( 'NGG_PLUGIN_SLUG', 'nextgen-gallery' );
		define( 'NGG_PLUGIN_FILE', __FILE__ );
		define( 'NGG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'NGG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'NGG_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
		define( 'NGG_PRODUCT_DIR', implode( DIRECTORY_SEPARATOR, [ rtrim( NGG_PLUGIN_DIR, '/\\' ), 'products' ] ) );
		define( 'NGG_MODULE_DIR', implode( DIRECTORY_SEPARATOR, [ rtrim( NGG_PRODUCT_DIR, '/\\' ), 'photocrati_nextgen', 'modules' ] ) );
		define( 'NGG_PLUGIN_STARTED_AT', microtime() );
		define( 'NGG_PLUGIN_VERSION', '4.2.0' );

		$random_version = function_exists( 'wp_rand' ) ? wp_rand( 0, mt_getrandmax() ) : mt_rand( 0, mt_getrandmax() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		define( 'NGG_SCRIPT_VERSION', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? (string) $random_version : NGG_PLUGIN_VERSION );

		// Should we display NGG debugging information?
		if ( ! defined( 'NGG_DEBUG' ) ) {
			define( 'NGG_DEBUG', false );
		}
		self::$debug = NGG_DEBUG;

		// User definable constants.
		if ( ! defined( 'NGG_IMPORT_ROOT' ) ) {
			$path = WP_CONTENT_DIR;
			if ( defined( 'NEXTGEN_GALLERY_IMPORT_ROOT' ) ) {
				$path = NEXTGEN_GALLERY_IMPORT_ROOT;
			}
			define( 'NGG_IMPORT_ROOT', $path );
		}

		// Should the Photocrati cache be enabled.
		if ( ! defined( 'PHOTOCRATI_CACHE' ) ) {
			define( 'PHOTOCRATI_CACHE', true );
		}
		if ( ! defined( 'PHOTOCRATI_CACHE_TTL' ) ) {
			define( 'PHOTOCRATI_CACHE_TTL', 1800 );
		}

		// Cron job.
		if ( ! defined( 'NGG_CRON_SCHEDULE' ) ) {
			define( 'NGG_CRON_SCHEDULE', 900 );
		}

		if ( ! defined( 'NGG_CRON_ENABLED' ) ) {
			define( 'NGG_CRON_ENABLED', true );
		}

		// Don't enforce interfaces.
		if ( ! defined( 'EXTENSIBLE_OBJECT_ENFORCE_INTERFACES' ) ) {
			define( 'EXTENSIBLE_OBJECT_ENFORCE_INTERFACES', false );
		}

		// Where are galleries restricted to?
		if ( ! defined( 'NGG_GALLERY_ROOT_TYPE' ) ) {
			define( 'NGG_GALLERY_ROOT_TYPE', 'site' ); // "content" is the other possible value.
		}

		// Define what file extensions and mime are accepted, with optional WebP.
		$default_extensions_list = 'jpeg,jpg,png,gif';
		$default_mime_list       = 'image/gif,image/jpg,image/jpeg,image/pjpeg,image/png';
		if ( function_exists( 'imagewebp' ) ) {
			$default_extensions_list .= ',webp';
			$default_mime_list       .= ',image/webp';
		}

		if ( ! defined( 'NGG_DEFAULT_ALLOWED_FILE_TYPES' ) ) {
			define( 'NGG_DEFAULT_ALLOWED_FILE_TYPES', $default_extensions_list );
		}

		if ( ! defined( 'NGG_DEFAULT_ALLOWED_MIME_TYPES' ) ) {
			define( 'NGG_DEFAULT_ALLOWED_MIME_TYPES', $default_mime_list );
		}

		add_filter(
			'ngg_allowed_file_types',
			function ( $file_types ) {
				return explode( ',', $file_types );
			},
			-10
		);      add_filter(
			'ngg_allowed_mime_types',
			function ( $mime_types ) {
				return explode( ',', $mime_types );
			},
			-10
		);
		define( 'NGG_LIGHTBOX_OPTIONS_SLUG', 'ngg_lightbox_options' );
		define( 'NGG_ATTACH_TO_POST_SLUG', 'nextgen-attach_to_post' );

		define( 'NGG_DISPLAY_SETTINGS_SLUG', 'ngg_display_settings' );
		define( 'NGG_DISPLAY_PRIORITY_BASE', 10000 );
		define( 'NGG_DISPLAY_PRIORITY_STEP', 2000 );

		define( 'NGG_BASIC_IMAGEBROWSER', 'photocrati-nextgen_basic_imagebrowser' );
		define( 'NGG_BASIC_SINGLEPIC', 'photocrati-nextgen_basic_singlepic' );
		define( 'NGG_BASIC_TAGCLOUD', 'photocrati-nextgen_basic_tagcloud' );
		define( 'NGG_BASIC_THUMBNAILS', 'photocrati-nextgen_basic_thumbnails' );
		define( 'NGG_BASIC_SLIDESHOW', 'photocrati-nextgen_basic_slideshow' );
		define( 'NGG_BASIC_COMPACT_ALBUM', 'photocrati-nextgen_basic_compact_album' );
		define( 'NGG_BASIC_EXTENDED_ALBUM', 'photocrati-nextgen_basic_extended_album' );
		define( 'NGG_BASIC_ALBUM', 'photocrati-nextgen_basic_album' );

		if ( ! defined( 'NGG_RENDERING_CACHE_TTL' ) ) {
			define( 'NGG_RENDERING_CACHE_TTL', PHOTOCRATI_CACHE_TTL );
		}
		if ( ! defined( 'NGG_DISPLAYED_GALLERY_CACHE_TTL' ) ) {
			define( 'NGG_DISPLAYED_GALLERY_CACHE_TTL', PHOTOCRATI_CACHE_TTL );
		}
		if ( ! defined( 'NGG_RENDERING_CACHE_ENABLED' ) ) {
			define( 'NGG_RENDERING_CACHE_ENABLED', PHOTOCRATI_CACHE );
		}
		if ( ! defined( 'NGG_SHOW_DISPLAYED_GALLERY_ERRORS' ) ) {
			define( 'NGG_SHOW_DISPLAYED_GALLERY_ERRORS', NGG_DEBUG );
		}

		define( 'NGG_AJAX_SLUG', 'photocrati_ajax' );
	}

	/**
	 * Returns the path to a file within the plugin root folder
	 *
	 * @param string $file_name Optional file name to append to the plugin path.
	 * @return string
	 */
	public function file_path( $file_name = null ) {
		$path = __DIR__;
		if ( null !== $file_name ) {
			$path .= '/' . $file_name;
		}

		return str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $path );
	}

	/**
	 * Gets the directory path used by the plugin
	 *
	 * @param string|null $dir (optional)
	 * @return string
	 */
	public function directory_path( $dir = null ) {
		return $this->file_path( $dir );
	}

	/**
	 * Returns the level of support for the POPE removal that Pro contains if active. Zero means Pro has not been
	 * updated at all and must rely on hacks in NextGEN for basic features. As POPE is removed, the level of support
	 * will rise, with the oldest checks in NextGEN being phased out the first.
	 *
	 * @return int
	 */
	public static function get_pro_api_version() {
		if ( ! defined( 'NGG_PRO_API_VERSION' ) && ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) || defined( 'NGG_STARTER_PLUGIN_VERSION' ) || defined( 'NGG_PRO_PLUGIN_VERSION' ) ) ) {
			return 3;
		}

		if ( defined( 'NGG_PRO_API_VERSION' ) ) {
			return NGG_PRO_API_VERSION;
		}

		return 10000;
	}
}

new C_NextGEN_Bootstrap();
