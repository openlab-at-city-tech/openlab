<?php
/**
 * Low-level plugin class.
 *
 * @link https://ewww.io
 * @package EWWW_Image_Optimizer
 */

namespace EWWW;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The kitchen sink, for everything that doesn't fit somewhere else.
 * Ideally, these are things like plugin initialization, setting defaults, and checking compatibility. We'll see how that plays out!
 */
final class Plugin extends Base {
	/* Singleton */

	/**
	 * The one and only true EWWW\Plugin
	 *
	 * @var object|EWWW\Plugin $instance
	 */
	private static $instance;

	/**
	 * Helpscout Beacon object.
	 *
	 * @var object|EWWW\HS_Beacon $hs_beacon
	 */
	public $hs_beacon;

	/**
	 * EWWW\Local object for handling local optimization tools/functions.
	 *
	 * @var object|EWWW\Local $local
	 */
	public $local;

	/**
	 * EWWW\Tracking object for anonymous usage tracking.
	 *
	 * @var object|EWWW\Tracking $tracking
	 */
	public $tracking;

	/**
	 * Whether the plugin is using the API or local tools.
	 *
	 * @var bool $cloud_mode
	 */
	public $cloud_mode = false;

	/**
	 * Did we already run tool_init()?
	 *
	 * @var bool $tools_initialized
	 */
	public $tools_initialized = false;

	/**
	 * Main EWWW_Plugin instance.
	 *
	 * Ensures that only one instance of EWWW_Plugin exists in memory at any given time.
	 *
	 * @static
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Plugin ) ) {
			// Setup custom $wpdb attribute for our image-tracking table.
			global $wpdb;
			if ( ! isset( $wpdb->ewwwio_images ) ) {
				$wpdb->ewwwio_images = $wpdb->prefix . 'ewwwio_images';
			}
			if ( ! isset( $wpdb->ewwwio_queue ) ) {
				$wpdb->ewwwio_queue = $wpdb->prefix . 'ewwwio_queue';
			}

			self::$instance = new Plugin( true );
			self::$instance->debug_message( '<b>' . __METHOD__ . '()</b>' );
			// TODO: self::$instance->setup_constants()?

			// For classes we need everywhere, front-end and back-end. Others are only included on admin_init (below).
			self::$instance->requires();
			self::$instance->load_children();
			// Initializes the plugin for admin interactions, like saving network settings and scheduling cron jobs.
			\add_action( 'admin_init', array( self::$instance, 'admin_init' ) );
			// We run this early, and then double-check after admin_init, once network settings have been saved/updated.
			self::$instance->cloud_init();

			// AJAX action hook to dismiss the exec notice and other related notices.
			\add_action( 'wp_ajax_ewww_dismiss_exec_notice', array( self::$instance, 'dismiss_exec_notice' ) );

			// TODO: check PHP and WP compat here.
			// TODO: setup anything that needs to run on init/plugins_loaded.
			// TODO: add any custom option/setting hooks here (actions that need to be taken when certain settings are saved/updated).
			\add_action( 'update_option_ewww_image_optimizer_cloud_key', array( self::$instance, 'updated_cloud_key' ), 10, 2 );
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		\_doing_it_wrong( __METHOD__, \esc_html__( 'Cannot clone core object.', 'ewww-image-optimizer' ), \esc_html( EWWW_IMAGE_OPTIMIZER_VERSION ) );
	}

	/**
	 * Disable unserializing of the class.
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		\_doing_it_wrong( __METHOD__, \esc_html__( 'Cannot unserialize (wakeup) the core object.', 'ewww-image-optimizer' ), \esc_html( EWWW_IMAGE_OPTIMIZER_VERSION ) );
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 */
	private function requires() {
		// Fall-back and convenience functions.
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'functions.php' );
		// The various class extensions for background optimization.
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-ewwwio-media-background-process.php' );
		// EWWW_Image class for working with queued images and image records from the database.
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-ewww-image.php' );
		// EWWW\Backup class for managing image backups.
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-backup.php' );
		// EWWW\HS_Beacon class for integrated help/docs.
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-hs-beacon.php' );
		// EWWW\Tracking class for reporting anonymous site data.
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-tracking.php' );
		if ( 'done' !== get_option( 'ewww_image_optimizer_relative_migration_status' ) ) {
			require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-ewwwio-relative-migration.php' );
		}
		// Used for manipulating exif info.
		if ( ! class_exists( '\lsolesen\pel\PelJpeg' ) ) {
			require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'vendor/autoload.php' );
		}
	}

	/**
	 * Setup mandatory child classes.
	 */
	function load_children() {
		self::$instance->local    = new Local();
		self::$instance->tracking = new Tracking();
	}

	/**
	 * Check to see if we are running in "cloud" mode. That is, using the API and no local tools.
	 */
	function cloud_init() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		if (
			$this->get_option( 'ewww_image_optimizer_cloud_key' ) &&
			$this->get_option( 'ewww_image_optimizer_jpg_level' ) > 10 &&
			$this->get_option( 'ewww_image_optimizer_png_level' ) > 10
		) {
			$this->cloud_mode = true;
		}
	}

	/**
	 * Initializes settings for the local tools, and runs the checks for tools on select pages.
	 */
	function exec_init() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		global $exactdn;
		// If cloud is fully enabled, we're going to skip all the checks related to the bundled tools.
		if ( $this->cloud_mode ) {
			$this->debug_message( 'cloud options enabled, shutting off binaries' );
			$this->local->skip_tools();
			return;
		}
		if (
			\defined( 'WPCOMSH_VERSION' ) ||
			! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ||
			\defined( 'WPE_PLUGIN_VERSION' ) ||
			\defined( 'FLYWHEEL_CONFIG_DIR' ) ||
			\defined( 'KINSTAMU_VERSION' ) ||
			\defined( 'WPNET_INIT_PLUGIN_VERSION' )
		) {
			if (
				! $this->get_option( 'ewww_image_optimizer_cloud_key' ) &&
				! \ewww_image_optimizer_easy_active() &&
				$this->get_option( 'ewww_image_optimizer_wizard_complete' )
			) {
				\add_action( 'network_admin_notices', array( $this, 'notice_hosting_requires_api' ) );
				\add_action( 'admin_notices', array( $this, 'notice_hosting_requires_api' ) );
			}
			$this->debug_message( 'WPE/wp.com/pantheon/flywheel site, disabling tools' );
			return;
		}
		// If they haven't completed the wizard yet, only display stuff on the bulk page, and short-circuit the rest of the checks elsewhere.
		if ( ! ewww_image_optimizer_get_option( 'ewww_image_optimizer_wizard_complete' ) ) {
			// Check if this is a supported OS (Linux, Mac OS, FreeBSD, or Windows).
			if (
				! $this->get_option( 'ewww_image_optimizer_cloud_key' ) &&
				! \ewww_image_optimizer_easy_active() &&
				$this->local->os_supported()
			) {
				\add_action( 'load-media_page_ewww-image-optimizer-bulk', array( $this, 'tool_init' ) );
			}
			return;
		}
		if ( ! $this->local->os_supported() ) {
			// Register the function to display a notice.
			\add_action( 'network_admin_notices', array( $this, 'notice_os' ) );
			\add_action( 'admin_notices', array( $this, 'notice_os' ) );
			// Turn off all the tools.
			$this->debug_message( 'unsupported OS, disabling tools: ' . PHP_OS );
			$this->local->skip_tools();
			return;
		}
		\add_action( 'load-upload.php', array( $this, 'tool_init' ), 9 );
		\add_action( 'load-media-new.php', array( $this, 'tool_init' ) );
		\add_action( 'load-media_page_ewww-image-optimizer-bulk', array( $this, 'tool_init' ) );
		\add_action( 'load-settings_page_ewww-image-optimizer-options', array( $this, 'tool_init' ) );
		\add_action( 'load-plugins.php', array( $this, 'tool_init' ) );
	}

	/**
	 * Check for binary installation and availability.
	 */
	function tool_init() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		$this->tools_initialized = true;
		// Make sure the bundled tools are installed.
		if ( ! $this->get_option( 'ewww_image_optimizer_skip_bundle' ) ) {
			$this->local->install_tools();
		}
		if ( $this->cloud_mode ) {
			$this->debug_message( 'cloud options enabled, shutting off binaries' );
			$this->local->skip_tools();
			return;
		}
		// Check for optimization utilities and display a notice if something is missing.
		\add_action( 'network_admin_notices', array( $this, 'notice_utils' ) );
		\add_action( 'admin_notices', array( $this, 'notice_utils' ) );
	}

	/**
	 * Setup plugin for wp-admin.
	 */
	function admin_init() {
		$this->hs_beacon = new HS_Beacon();
		/**
		 * Require the file that does the bulk processing.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'bulk.php' );
		/**
		 * Require the files that contain functions for the images table and bulk processing images outside the library.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'aux-optimize.php' );
		/**
		 * Require the files that migrate WebP images from extension replacement to extension appending.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'mwebp.php' );
		\ewww_image_optimizer_upgrade();

		// Do settings validation for multi-site.
		\ewww_image_optimizer_save_network_settings();

		$this->register_settings();
		$this->cloud_init();
		$this->exec_init();
		\ewww_image_optimizer_cron_setup( 'ewww_image_optimizer_auto' );

		// Adds scripts to ajaxify the one-click actions on the media library, and register tooltips for conversion links.
		\add_action( 'admin_enqueue_scripts', 'ewww_image_optimizer_media_scripts' );
		// Adds scripts for the EWWW IO settings page.
		\add_action( 'admin_enqueue_scripts', 'ewww_image_optimizer_settings_script' );
		// Queue the function that contains custom styling for our progressbars.
		\add_action( 'admin_enqueue_scripts', 'ewww_image_optimizer_progressbar_style' );

		if ( false !== \strpos( \add_query_arg( '', '' ), 'site-new.php' ) ) {
			if ( \is_multisite() && \is_network_admin() && isset( $_GET['update'] ) && 'added' === $_GET['update'] && ! empty( $_GET['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				\add_action( 'network_admin_notices', 'ewww_image_optimizer_easyio_site_initialized' );
				\add_action( 'admin_notices', 'ewww_image_optimizer_easyio_site_initialized' );
			}
		}
		if ( \defined( 'EWWW_IMAGE_OPTIMIZER_CLOUD_KEY' ) && \get_option( 'ewww_image_optimizer_cloud_key_invalid' ) ) {
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_notice_invalid_key' );
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_invalid_key' );
		}
		if ( $this->get_option( 'ewww_image_optimizer_webp_enabled' ) ) {
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_webp_bulk' );
			if ( \ewww_image_optimizer_cloud_based_media() ) {
				\ewww_image_optimizer_set_option( 'ewww_image_optimizer_webp_force', true );
			}
		}
		if ( $this->get_option( 'ewww_image_optimizer_auto' ) && ! \ewww_image_optimizer_background_mode_enabled() ) {
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_notice_schedule_noasync' );
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_schedule_noasync' );
		}
		if ( $this->get_option( 'ewww_image_optimizer_webp_force' ) && $this->get_option( 'ewww_image_optimizer_force_gif2webp' ) && ! $this->get_option( 'ewww_image_optimizer_cloud_key' ) ) {
			$this->set_option( 'ewww_image_optimizer_force_gif2webp', false );
		}
		// Prevent ShortPixel AIO messiness.
		\remove_action( 'admin_notices', 'autoptimizeMain::notice_plug_imgopt' );
		if ( \class_exists( '\autoptimizeExtra' ) || \defined( 'AUTOPTIMIZE_PLUGIN_VERSION' ) ) {
			$ao_extra = \get_option( 'autoptimize_imgopt_settings' );
			if ( $this->get_option( 'ewww_image_optimizer_exactdn' ) && ! empty( $ao_extra['autoptimize_imgopt_checkbox_field_1'] ) ) {
				$this->debug_message( 'detected ExactDN + SP conflict' );
				$ao_extra['autoptimize_imgopt_checkbox_field_1'] = 0;
				\update_option( 'autoptimize_imgopt_settings', $ao_extra );
				\add_action( 'admin_notices', 'ewww_image_optimizer_notice_exactdn_sp_conflict' );
			}
		}
		if (
			! $this->get_option( 'ewww_image_optimizer_ludicrous_mode' ) &&
			! $this->get_option( 'ewww_image_optimizer_cloud_key' ) &&
			\ewww_image_optimizer_easy_active()
		) {
			// Suppress the custom column in the media library if local mode is disabled and easy mode is active.
			\remove_filter( 'manage_media_columns', 'ewww_image_optimizer_columns' );
		} else {
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_media_listmode' );
		}
		if ( \ewww_image_optimizer_easy_active() ) {
			$this->set_option( 'ewww_image_optimizer_webp', false );
			$this->set_option( 'ewww_image_optimizer_webp_force', false );
		}

		// Alert user if multiple re-optimizations detected.
		if ( false && ! \defined( 'EWWWIO_DISABLE_REOPT_NOTICE' ) ) {
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_notice_reoptimization' );
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_reoptimization' );
		}
		// Let the admin know a db upgrade is needed.
		if ( \is_super_admin() && \get_transient( 'ewww_image_optimizer_620_upgrade_needed' ) ) {
			\add_action( 'admin_notices', 'ewww_image_optimizer_620_upgrade_needed' );
		}
		if (
			\is_super_admin() &&
			$this->get_option( 'ewww_image_optimizer_review_time' ) &&
			$this->get_option( 'ewww_image_optimizer_review_time' ) < \time() &&
			! $this->get_option( 'ewww_image_optimizer_dismiss_review_notice' )
		) {
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_review' );
			\add_action( 'admin_footer', 'ewww_image_optimizer_notice_review_script' );
		}
		if ( ! empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( 'regenerate-thumbnails' === $_GET['page'] // phpcs:ignore WordPress.Security.NonceVerification
				|| 'force-regenerate-thumbnails' === $_GET['page'] // phpcs:ignore WordPress.Security.NonceVerification
				|| 'ajax-thumbnail-rebuild' === $_GET['page'] // phpcs:ignore WordPress.Security.NonceVerification
				|| 'regenerate_thumbnails_advanced' === $_GET['page'] // phpcs:ignore WordPress.Security.NonceVerification
				|| 'rta_generate_thumbnails' === $_GET['page'] // phpcs:ignore WordPress.Security.NonceVerification
			) {
				// Add a notice for thumb regeneration.
				\add_action( 'admin_notices', 'ewww_image_optimizer_thumbnail_regen_notice' );
			}
		}
		if ( ! empty( $_GET['ewww_pngout'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			\add_action( 'admin_notices', 'ewww_image_optimizer_pngout_installed' );
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_pngout_installed' );
		}
		if ( ! empty( $_GET['ewww_svgcleaner'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			\add_action( 'admin_notices', 'ewww_image_optimizer_svgcleaner_installed' );
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_svgcleaner_installed' );
		}
		if ( ! \defined( 'EIO_PHPUNIT' ) && ( ! \defined( 'WP_CLI' ) || ! WP_CLI ) ) {
			\ewww_image_optimizer_privacy_policy_content();
			\ewww_image_optimizer_ajax_compat_check();
		}
		if ( \class_exists( '\WooCommerce' ) && $this->get_option( 'ewww_image_optimizer_wc_regen' ) ) {
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_wc_regen' );
			\add_action( 'admin_footer', 'ewww_image_optimizer_wc_regen_script' );
		}
		if ( \class_exists( '\Meow_WPLR_Sync_Core' ) && $this->get_option( 'ewww_image_optimizer_lr_sync' ) ) {
			\add_action( 'admin_notices', 'ewww_image_optimizer_notice_lr_sync' );
			\add_action( 'admin_footer', 'ewww_image_optimizer_lr_sync_script' );
		}
		// Increase the version when the next bump is coming.
		if ( \defined( 'PHP_VERSION_ID' ) && PHP_VERSION_ID < 70200 ) {
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_php72_warning' );
			\add_action( 'admin_notices', 'ewww_image_optimizer_php72_warning' );
		}
		if ( \get_option( 'ewww_image_optimizer_debug' ) ) {
			\add_action( 'admin_notices', 'ewww_image_optimizer_debug_enabled_notice' );
		} elseif ( \get_site_option( 'ewww_image_optimizer_debug' ) && \is_network_admin() ) {
			\add_action( 'network_admin_notices', 'ewww_image_optimizer_debug_enabled_notice' );
		}
	}

	/**
	 * Register all our options and santiation functions.
	 */
	function register_settings() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		// Register all the common EWWW IO settings and their sanitation functions.
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_debug', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_metadata_remove', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_jpg_level', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_png_level', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_gif_level', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_pdf_level', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_svg_level', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_backup_files', 'sanitize_text_field' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_enable_cloudinary', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_sharpen', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_jpg_quality', 'ewww_image_optimizer_jpg_quality' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_webp_quality', 'ewww_image_optimizer_webp_quality' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_avif_quality', 'ewww_image_optimizer_avif_quality' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_auto', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_include_media_paths', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_include_originals', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_aux_paths', 'ewww_image_optimizer_aux_paths_sanitize' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_exclude_paths', array( $this, 'exclude_paths_sanitize' ) );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_allow_tracking', array( $this->tracking, 'check_for_settings_optin' ) );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_enable_help', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'exactdn_all_the_things', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'exactdn_lossy', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'exactdn_exclude', array( $this, 'exclude_paths_sanitize' ) );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_add_missing_dims', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_lazy_load', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_ll_autoscale', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_use_lqip', 'boolval' );
		// Using sanitize_text_field instead of textarea on purpose.
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_ll_all_things', 'sanitize_text_field' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_ll_exclude', array( $this, 'exclude_paths_sanitize' ) );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_resize_detection', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_maxmediawidth', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_maxmediaheight', 'intval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_resize_existing', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_resize_other_existing', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_disable_resizes', 'ewww_image_optimizer_disable_resizes_sanitize' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_disable_resizes_opt', 'ewww_image_optimizer_disable_resizes_sanitize' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_disable_convert_links', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_delete_originals', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_jpg_to_png', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_png_to_jpg', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_gif_to_png', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_jpg_background', 'ewww_image_optimizer_jpg_background' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_webp', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_webp_force', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_webp_paths', 'ewww_image_optimizer_webp_paths_sanitize' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_webp_for_cdn', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_picture_webp', 'boolval' );
		register_setting( 'ewww_image_optimizer_options', 'ewww_image_optimizer_webp_rewrite_exclude', array( $this, 'exclude_paths_sanitize' ) );
	}

	/**
	 * Set some default option values.
	 */
	function set_defaults() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		// Set defaults for all options that need to be autoloaded.
		\add_option( 'ewww_image_optimizer_background_optimization', false );
		\add_option( 'ewww_image_optimizer_noauto', false );
		\add_option( 'ewww_image_optimizer_disable_editor', false );
		\add_option( 'ewww_image_optimizer_debug', false );
		\add_option( 'ewww_image_optimizer_metadata_remove', true );
		\add_option( 'ewww_image_optimizer_jpg_level', '10' );
		\add_option( 'ewww_image_optimizer_png_level', '10' );
		\add_option( 'ewww_image_optimizer_gif_level', '10' );
		\add_option( 'ewww_image_optimizer_pdf_level', '0' );
		\add_option( 'ewww_image_optimizer_svg_level', '0' );
		\add_option( 'ewww_image_optimizer_jpg_quality', '' );
		\add_option( 'ewww_image_optimizer_webp_quality', '' );
		\add_option( 'ewww_image_optimizer_backup_files', '' );
		\add_option( 'ewww_image_optimizer_resize_existing', true );
		\add_option( 'ewww_image_optimizer_exactdn', false );
		\add_option( 'ewww_image_optimizer_exactdn_plan_id', 0 );
		\add_option( 'exactdn_all_the_things', true );
		\add_option( 'exactdn_lossy', true );
		\add_option( 'exactdn_exclude', '' );
		\add_option( 'exactdn_sub_folder', false );
		\add_option( 'exactdn_prevent_db_queries', true );
		\add_option( 'ewww_image_optimizer_lazy_load', false );
		\add_option( 'ewww_image_optimizer_use_siip', false );
		\add_option( 'ewww_image_optimizer_use_lqip', false );
		\add_option( 'ewww_image_optimizer_ll_exclude', '' );
		\add_option( 'ewww_image_optimizer_ll_all_things', '' );
		\add_option( 'ewww_image_optimizer_disable_pngout', true );
		\add_option( 'ewww_image_optimizer_disable_svgcleaner', true );
		\add_option( 'ewww_image_optimizer_optipng_level', 2 );
		\add_option( 'ewww_image_optimizer_pngout_level', 2 );
		\add_option( 'ewww_image_optimizer_webp_for_cdn', false );
		\add_option( 'ewww_image_optimizer_force_gif2webp', false );
		\add_option( 'ewww_image_optimizer_picture_webp', false );
		\add_option( 'ewww_image_optimizer_webp_rewrite_exclude', '' );

		// Set network defaults.
		\add_site_option( 'ewww_image_optimizer_background_optimization', false );
		\add_site_option( 'ewww_image_optimizer_metadata_remove', true );
		\add_site_option( 'ewww_image_optimizer_jpg_level', '10' );
		\add_site_option( 'ewww_image_optimizer_png_level', '10' );
		\add_site_option( 'ewww_image_optimizer_gif_level', '10' );
		\add_site_option( 'ewww_image_optimizer_pdf_level', '0' );
		\add_site_option( 'ewww_image_optimizer_svg_level', '0' );
		\add_site_option( 'ewww_image_optimizer_jpg_quality', '' );
		\add_site_option( 'ewww_image_optimizer_webp_quality', '' );
		\add_site_option( 'ewww_image_optimizer_backup_files', '' );
		\add_site_option( 'ewww_image_optimizer_resize_existing', true );
		\add_site_option( 'ewww_image_optimizer_disable_pngout', true );
		\add_site_option( 'ewww_image_optimizer_disable_svgcleaner', true );
		\add_site_option( 'ewww_image_optimizer_optipng_level', 2 );
		\add_site_option( 'ewww_image_optimizer_pngout_level', 2 );
		\add_site_option( 'exactdn_all_the_things', true );
		\add_site_option( 'exactdn_lossy', true );
		\add_site_option( 'exactdn_sub_folder', false );
		\add_site_option( 'exactdn_prevent_db_queries', true );
		\add_site_option( 'ewww_image_optimizer_ll_autoscale', true );
	}

	/**
	 * Checks for exec() and availability of local optimizers, then displays an error if needed.
	 *
	 * @param string $quiet Optional. Use 'quiet' to suppress output.
	 */
	function notice_utils( $quiet = null ) {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		// Check if exec is disabled.
		if ( ! $this->local->exec_check() ) {
			// Don't bother if we're in quiet mode, or they already dismissed the notice.
			if ( 'quiet' !== $quiet && ! $this->get_option( 'ewww_image_optimizer_dismiss_exec_notice' ) ) {
				\ob_start();
				// Display a warning if exec() is disabled, can't run local tools without it.
				if ( \ewww_image_optimizer_easy_active() ) {
					echo "<div id='ewww-image-optimizer-warning-exec' class='notice notice-info is-dismissible'><p>";
					\esc_html_e( 'Free compression of local images cannot be done on your site without an API key. Since Easy IO is already automatically optimizing your site, you may dismiss this notice unless you need to save storage space.', 'ewww-image-optimizer' );
				} else {
					echo "<div id='ewww-image-optimizer-warning-exec' class='notice notice-warning is-dismissible'><p>";
					\printf(
						/* translators: %s: link to 'start your premium trial' */
						\esc_html__( 'Your web server does not meet the requirements for free server-based compression with EWWW Image Optimizer. You may %s for 5x more compression, PNG/GIF/PDF compression, and more. Otherwise, continue with free cloud-based JPG compression.', 'ewww-image-optimizer' ),
						"<a href='https://ewww.io/plans/'>" . \esc_html__( 'start your premium trial', 'ewww-image-optimizer' ) . '</a>'
					);
				}
				\ewwwio_help_link( 'https://docs.ewww.io/article/29-what-is-exec-and-why-do-i-need-it', '592dd12d0428634b4a338c39' );
				echo '</p></div>';
				echo
					"<script>\n" .
					"jQuery(document).on('click', '#ewww-image-optimizer-warning-exec .notice-dismiss', function() {\n" .
						"\tvar ewww_dismiss_exec_data = {\n" .
							"\t\taction: 'ewww_dismiss_exec_notice',\n" .
						"\t};\n" .
						"\tjQuery.post(ajaxurl, ewww_dismiss_exec_data, function(response) {\n" .
							"\t\tif (response) {\n" .
								"\t\t\tconsole.log(response);\n" .
							"\t\t}\n" .
						"\t});\n" .
					"});\n" .
					"</script>\n";
				if (
					\ewww_image_optimizer_easy_active() &&
					! $this->get_option( 'ewww_image_optimizer_ludicrous_mode' )
				) {
					\ob_end_clean();
				} else {
					\ob_end_flush();
				}
				$this->debug_message( 'exec disabled, alerting user' );
			}
			return;
		}

		$tools   = ewwwio()->local->check_all_tools();
		$missing = array();
		// Go through each of the required tools.
		foreach ( $tools as $tool => $info ) {
			// If a tool is needed, but wasn't found, add it to the $missing so we can display that info to the user.
			if ( $info['enabled'] && empty( $info['path'] ) ) {
				if ( 'cwebp' === $tool && ( $this->imagick_supports_webp() || $this->gd_supports_webp() ) ) {
					continue;
				}
				$missing[] = $tool;
			}
		}
		// If there is a message, display the warning.
		if ( ! empty( $missing ) && 'quiet' !== $quiet ) {
			if ( ! \is_dir( $this->content_dir ) ) {
				$this->tool_folder_notice();
			} elseif ( ! \is_writable( $this->content_dir ) || ! is_readable( $this->content_dir ) ) {
				$this->tool_folder_permissions_notice();
			} elseif ( ! \is_executable( $this->content_dir ) && PHP_OS !== 'WINNT' ) {
				$this->tool_folder_permissions_notice();
			}
			if ( \in_array( 'pngout', $missing, true ) ) {
				// Display a separate notice for pngout with an install option, and then suppress it from the latter notice.
				$key = \array_search( 'pngout', $missing, true );
				if ( false !== $key ) {
					unset( $missing[ $key ] );
				}
				$pngout_install_url = \admin_url( 'admin.php?action=ewww_image_optimizer_install_pngout' );
				echo "<div id='ewww-image-optimizer-warning-opt-missing' class='notice notice-warning'><p>" .
				\sprintf(
					/* translators: 1: automatically (link) 2: manually (link) */
					\esc_html__( 'EWWW Image Optimizer is missing pngout. Install %1$s or %2$s.', 'ewww-image-optimizer' ),
					"<a href='" . \esc_url( $pngout_install_url ) . "'>" . \esc_html__( 'automatically', 'ewww-image-optimizer' ) . '</a>',
					'<a href="https://docs.ewww.io/article/13-installing-pngout" data-beacon-article="5854531bc697912ffd6c1afa">' . \esc_html__( 'manually', 'ewww-image-optimizer' ) . '</a>'
				) .
				'</p></div>';
			}
			if ( \in_array( 'svgcleaner', $missing, true ) ) {
				$key = array_search( 'svgcleaner', $missing, true );
				if ( false !== $key ) {
					unset( $missing[ $key ] );
				}
				$svgcleaner_install_url = \admin_url( 'admin.php?action=ewww_image_optimizer_install_svgcleaner' );
				echo "<div id='ewww-image-optimizer-warning-opt-missing' class='notice notice-warning'><p>" .
				\sprintf(
					/* translators: 1: automatically (link) 2: manually (link) */
					\esc_html__( 'EWWW Image Optimizer is missing svgleaner. Install %1$s or %2$s.', 'ewww-image-optimizer' ),
					"<a href='" . \esc_url( $svgcleaner_install_url ) . "'>" . \esc_html__( 'automatically', 'ewww-image-optimizer' ) . '</a>',
					'<a href="https://docs.ewww.io/article/95-installing-svgcleaner" data-beacon-article="5f7921c9cff47e001a58adbc">' . \esc_html__( 'manually', 'ewww-image-optimizer' ) . '</a>'
				) .
				'</p></div>';
			}
			if ( ! empty( $missing ) && ! $this->get_option( 'ewww_image_optimizer_dismiss_exec_notice' ) ) {
				$dismissible = false;
				// If all the tools are missing, make it dismissible.
				if (
					\in_array( 'jpegtran', $missing, true ) &&
					\in_array( 'optipng', $missing, true ) &&
					\in_array( 'gifsicle', $missing, true )
				) {
					$dismissible = true;
				}
				// If they are missing tools, but not jpegtran, make it dismissible, since they can effectively do locally what we would offer in free-cloud mode.
				if ( ! \in_array( 'jpegtran', $missing, true ) ) {
					$dismissible = true;
				}
				// Expand the missing utilities list for use in the error message.
				$msg = \implode( ', ', $missing );
				echo "<div id='ewww-image-optimizer-warning-opt-missing' class='notice notice-warning" . ( $dismissible ? ' is-dismissible' : '' ) . "'><p>" .
				\sprintf(
					/* translators: 1: comma-separated list of missing tools 2: Installation Instructions (link) */
					\esc_html__( 'EWWW Image Optimizer uses open-source tools to enable free mode, but your server is missing these: %1$s. Please install via the %2$s to continue in free mode.', 'ewww-image-optimizer' ),
					\esc_html( $msg ),
					"<a href='https://docs.ewww.io/article/6-the-plugin-says-i-m-missing-something' data-beacon-article='585371e3c697912ffd6c0ba1' target='_blank'>" . \esc_html__( 'Installation Instructions', 'ewww-image-optimizer' ) . '</a>'
				) .
				'</p></div>';
				?>
	<script>
		jQuery(document).on('click', '#ewww-image-optimizer-warning-opt-missing .notice-dismiss', function() {
			var ewww_dismiss_exec_data = {
				action: 'ewww_dismiss_exec_notice',
			};
			jQuery.post(ajaxurl, ewww_dismiss_exec_data, function(response) {
				if (response) {
					console.log(response);
				}
			});
		});
	</script>
				<?php
			}
		}
	}

	/**
	 * Let the user know the plugin requires API/ExactDN to operate at their webhost.
	 */
	function notice_hosting_requires_api() {
		if ( ! \function_exists( '\is_plugin_active_for_network' ) && \is_multisite() ) {
			// Need to include the plugin library for the is_plugin_active function.
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( \is_multisite() && \is_plugin_active_for_network( EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE_REL ) ) {
			$settings_url = \network_admin_url( 'settings.php?page=ewww-image-optimizer-options' );
		} else {
			$settings_url = \admin_url( 'options-general.php?page=ewww-image-optimizer-options' );
		}
		if ( \defined( 'WPCOMSH_VERSION' ) ) {
			$webhost = 'WordPress.com';
		} elseif ( ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
			$webhost = 'Pantheon';
		} elseif ( \defined( 'WPE_PLUGIN_VERSION' ) ) {
			$webhost = 'WP Engine';
		} elseif ( \defined( 'FLYWHEEL_CONFIG_DIR' ) ) {
			$webhost = 'Flywheel';
		} elseif ( \defined( 'KINSTAMU_VERSION' ) ) {
			$webhost = 'Kinsta';
		} elseif ( \defined( 'WPNET_INIT_PLUGIN_VERSION' ) ) {
			$webhost = 'WP NET';
		} else {
			return;
		}
		if ( $this->get_option( 'ewww_image_optimizer_dismiss_exec_notice' ) ) {
			return;
		}
		echo "<div id='ewww-image-optimizer-warning-exec' class='notice notice-warning is-dismissible'><p>" .
			/* translators: %s: Name of a web host, like WordPress.com or Pantheon. */
			\sprintf( \esc_html__( 'EWWW Image Optimizer cannot use server-based optimization on %s sites. Activate our premium service for 5x more compression, PNG/GIF/PDF compression, and image backups.', 'ewww-image-optimizer' ), \esc_html( $webhost ) ) .
			'<br><strong>' .
			/* translators: %s: link to 'start your free trial' */
			\sprintf( \esc_html__( 'Dismiss this notice to continue with free cloud-based JPG compression or %s.', 'ewww-image-optimizer' ), "<a href='https://ewww.io/plans/'>" . \esc_html__( 'start your premium trial', 'ewww-image-optimizer' ) . '</a>' );
		\ewwwio_help_link( 'https://docs.ewww.io/article/29-what-is-exec-and-why-do-i-need-it', '592dd12d0428634b4a338c39' );
		echo '</strong></p></div>';
		?>
	<script>
		jQuery(document).on('click', '#ewww-image-optimizer-warning-exec .notice-dismiss', function() {
			var ewww_dismiss_exec_data = {
				action: 'ewww_dismiss_exec_notice',
			};
			jQuery.post(ajaxurl, ewww_dismiss_exec_data, function(response) {
				if (response) {
					console.log(response);
				}
			});
		});
	</script>
		<?php
	}

	/**
	 * Tells the user they are on an unsupported operating system.
	 */
	function notice_os() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		if ( $this->get_option( 'ewww_image_optimizer_dismiss_exec_notice' ) ) {
			return;
		}
		// If they are already using our services, or haven't gone through the wizard, exit now!
		if (
			$this->get_option( 'ewww_image_optimizer_cloud_key' ) ||
			\ewww_image_optimizer_easy_active() ||
			! $this->get_option( 'ewww_image_optimizer_wizard_complete' )
		) {
			return;
		}
		echo "<div id='ewww-image-optimizer-warning-exec' class='notice notice-warning is-dismissible'><p>" .
			\esc_html__( 'Free server-based compression with EWWW Image Optimizer is only supported on Linux, FreeBSD, Mac OSX, and Windows.', 'ewww-image-optimizer' ) .
			'<br><strong>' .
			/* translators: %s: link to 'start your free trial' */
			\sprintf( \esc_html__( 'Dismiss this notice to continue with free cloud-based JPG compression or %s.', 'ewww-image-optimizer' ), "<a href='https://ewww.io/plans/'>" . \esc_html__( 'start your premium trial', 'ewww-image-optimizer' ) . '</a>' );
		\ewwwio_help_link( 'https://docs.ewww.io/article/29-what-is-exec-and-why-do-i-need-it', '592dd12d0428634b4a338c39' );
		echo '</strong></p></div>';
		?>
	<script>
		jQuery(document).on('click', '#ewww-image-optimizer-warning-exec .notice-dismiss', function() {
			var ewww_dismiss_exec_data = {
				action: 'ewww_dismiss_exec_notice',
			};
			jQuery.post(ajaxurl, ewww_dismiss_exec_data, function(response) {
				if (response) {
					console.log(response);
				}
			});
		});
	</script>
		<?php
	}

	/**
	 * Alert the user when the tool folder could not be created.
	 */
	function tool_folder_notice() {
		echo "<div id='ewww-image-optimizer-warning-tool-folder-create' class='notice notice-error'><p><strong>" . \esc_html__( 'EWWW Image Optimizer could not create the tool folder', 'ewww-image-optimizer' ) . ': ' . \esc_html( $this->content_dir ) . '.</strong> ' . \esc_html__( 'Please adjust permissions or create the folder', 'ewww-image-optimizer' ) . '.</p></div>';
	}

	/**
	 * Alert the user when permissions on the tool folder are insufficient.
	 */
	function tool_folder_permissions_notice() {
		echo "<div id='ewww-image-optimizer-warning-tool-folder-permissions' class='notice notice-error'><p><strong>" .
			/* translators: %s: Folder location where executables should be installed */
			\sprintf( \esc_html__( 'EWWW Image Optimizer could not install tools in %s', 'ewww-image-optimizer' ), \esc_html( $this->content_dir ) ) . '.</strong> ' .
			\esc_html__( 'Please adjust permissions on the folder. If you have installed the tools elsewhere, use the override to skip the bundled tools.', 'ewww-image-optimizer' ) . ' ' .
			/* translators: s: Installation Instructions (link) */
			\sprintf( \esc_html__( 'For more details, see the %s.', 'ewww-image-optimizer' ), "<a href='https://docs.ewww.io/article/6-the-plugin-says-i-m-missing-something'>" . \esc_html__( 'Installation Instructions', 'ewww-image-optimizer' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Disables local compression when exec notice is dismissed.
	 */
	function dismiss_exec_notice() {
		$this->ob_clean();
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		// Verify that the user is properly authorized.
		if ( ! \current_user_can( \apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
			\wp_die( \esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
		}
		$this->enable_free_exec();
		die();
	}

	/**
	 * Sync the cloud_mode property with the cloud_key option.
	 *
	 * @param mixed $old_setting The old value.
	 * @param mixed $new_setting The new value.
	 */
	function updated_cloud_key( $old_setting, $new_setting ) {
		$this->cloud_mode = ! empty( $new_setting );
	}

	/**
	 * Put site in "free exec" mode with JPG-only API compression, and suppress the exec() notice.
	 */
	function enable_free_exec() {
		$this->debug_message( '<b>' . __METHOD__ . '()</b>' );
		\update_option( 'ewww_image_optimizer_jpg_level', 10 );
		\update_option( 'ewww_image_optimizer_png_level', 0 );
		\update_option( 'ewww_image_optimizer_gif_level', 0 );
		\update_option( 'ewww_image_optimizer_pdf_level', 0 );
		\update_option( 'ewww_image_optimizer_svg_level', 0 );
		\update_option( 'ewww_image_optimizer_dismiss_exec_notice', 1 );
		\update_site_option( 'ewww_image_optimizer_dismiss_exec_notice', 1 );
	}
}
