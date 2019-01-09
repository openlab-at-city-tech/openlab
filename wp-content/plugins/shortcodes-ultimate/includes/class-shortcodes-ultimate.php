<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization and hooks.
 *
 * @since        5.0.0
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */
class Shortcodes_Ultimate {

	/**
	 * The path to the main plugin file.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string      $plugin_file   The path to the main plugin file.
	 */
	private $plugin_file;

	/**
	 * The current version of the plugin.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string      $plugin_version   The current version of the plugin.
	 */
	private $plugin_version;

	/**
	 * The path to the plugin folder.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string      $plugin_path   The path to the plugin folder.
	 */
	private $plugin_path;

	/**
	 * The prefix of the plugin.
	 *
	 * @since    5.0.8
	 * @access   private
	 * @var      string      $plugin_prefix   The prefix of the plugin.
	 */
	private $plugin_prefix;

	/**
	 * Class instance.
	 *
	 * @since  5.1.0
	 * @access private
	 * @var    null      The single class instance.
	 */
	private static $instance;

	/**
	 * Upgrader class instance.
	 *
	 * @since  5.1.0
	 * @var    Shortcodes_Ultimate_Upgrade  Upgrader class instance.
	 */
	public $upgrade;

	/**
	 * Menu classes instances.
	 *
	 * @since  5.1.0
	 */
	public $top_level_menu;
	public $shortcodes_menu;
	public $settings_menu;
	public $addons_menu;

	/**
	 * Notices classes instances.
	 *
	 * @since  5.1.0
	 */
	public $rate_notice;

	/**
	 * Get class instance.
	 *
	 * @since  5.1.0
	 * @return Shortcodes_Ultimate
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since   5.0.0
	 * @param string  $plugin_file    The path to the main plugin file.
	 * @param string  $plugin_version The current version of the plugin.
	 * @param string  $plugin_prefix  The prefix of the plugin.
	 */
	public function __construct( $plugin_file, $plugin_version, $plugin_prefix ) {

		$this->plugin_file    = $plugin_file;
		$this->plugin_version = $plugin_version;
		$this->plugin_path    = plugin_dir_path( $plugin_file );
		$this->plugin_prefix  = $plugin_prefix;

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_common_hooks();

		self::$instance = $this;

	}

	/**
	 * Load the required dependencies for the plugin.
	 *
	 * @since    5.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for adding, storing and accessing shortcodes data.
		 */
		require_once $this->plugin_path . 'includes/class-shortcodes-ultimate-shortcodes.php';

		/**
		 * The class responsible for plugin upgrades.
		 */
		require_once $this->plugin_path . 'includes/class-shortcodes-ultimate-upgrade.php';

		/**
		 * Classes responsible for defining admin menus.
		 */
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-admin.php';
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-admin-top-level.php';
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-admin-shortcodes.php';
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-admin-settings.php';
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-admin-addons.php';

		/**
		 * Classes responsible for displaying admin notices.
		 */
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-notice.php';
		require_once $this->plugin_path . 'admin/class-shortcodes-ultimate-notice-rate.php';

		/**
		 * Filters.
		 */
		require_once $this->plugin_path . 'includes/filters.php';

		/**
		 * Functions.
		 */
		require_once $this->plugin_path . 'includes/functions-helpers.php';
		require_once $this->plugin_path . 'includes/functions-html.php';
		require_once $this->plugin_path . 'includes/functions-shortcodes.php';
		require_once $this->plugin_path . 'includes/functions-galleries.php';

		/**
		 * Deprecated stuff.
		 */
		require_once $this->plugin_path . 'includes/deprecated/class-su-data.php';
		require_once $this->plugin_path . 'includes/deprecated/class-su-tools.php';
		require_once $this->plugin_path . 'includes/deprecated/functions.php';

		/**
		 * Shortcodes.
		 */
		require_once $this->plugin_path . 'includes/shortcodes/0-all.php';

		// phpcs:disable
		// foreach ( glob( $this->plugin_path . 'includes/shortcodes/*.php' ) as $shortcode_file ) {
		//  require_once $shortcode_file;
		// }
		// phpcs:enable

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the
	 * plugin.
	 *
	 * @since    5.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		/**
		 * Upgrades.
		 */
		$this->upgrade = new Shortcodes_Ultimate_Upgrade( $this->plugin_file, $this->plugin_version );

		add_action( 'admin_init', array( $this->upgrade, 'maybe_upgrade' ) );


		/**
		 * Top-level menu: Shortcodes
		 * admin.php?page=shortcodes-ultimate
		 */
		$this->top_level_menu = new Shortcodes_Ultimate_Admin_Top_Level(
			$this->plugin_file,
			$this->plugin_version,
			$this->plugin_prefix
		);

		add_action( 'admin_menu', array( $this->top_level_menu, 'add_menu_pages' ), 5 );


		/**
		 * Submenu: Available shortcodes
		 * admin.php?page=shortcodes-ultimate
		 */
		$this->shortcodes_menu = new Shortcodes_Ultimate_Admin_Shortcodes(
			$this->plugin_file,
			$this->plugin_version,
			$this->plugin_prefix
		);

		add_action( 'admin_menu',            array( $this->shortcodes_menu, 'add_menu_pages' ), 5 );
		add_action( 'current_screen',        array( $this->shortcodes_menu, 'add_help_tabs' )     );
		add_action( 'admin_enqueue_scripts', array( $this->shortcodes_menu, 'enqueue_scripts' )   );
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->plugin_file ),
			array( $this->shortcodes_menu, 'add_action_links' ),
			20,
			1
		);


		/**
		 * Submenu: Settings
		 * admin.php?page=shortcodes-ultimate-settings
		 */
		$this->settings_menu = new Shortcodes_Ultimate_Admin_Settings(
			$this->plugin_file,
			$this->plugin_version,
			$this->plugin_prefix
		);

		add_action( 'admin_menu',     array( $this->settings_menu, 'add_menu_pages' ), 20 );
		add_action( 'admin_init',     array( $this->settings_menu, 'add_settings' )       );
		add_action( 'current_screen', array( $this->settings_menu, 'add_help_tabs' )      );
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->plugin_file ),
			array( $this->settings_menu, 'add_action_links' ),
			10,
			1
		);


		/**
		 * Submenu: Add-ons
		 * admin.php?page=shortcodes-ultimate-addons
		 */
		$this->addons_menu = new Shortcodes_Ultimate_Admin_Addons(
			$this->plugin_file,
			$this->plugin_version,
			$this->plugin_prefix
		);

		add_action( 'admin_menu',            array( $this->addons_menu, 'add_menu_pages' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this->addons_menu, 'enqueue_scripts' )    );
		add_action( 'current_screen',        array( $this->addons_menu, 'add_help_tabs' )      );


		/**
		 * Notice: Rate plugin
		 */
		$this->rate_notice = new Shortcodes_Ultimate_Notice_Rate(
			'rate',
			$this->plugin_path . 'admin/partials/notices/rate.php'
		);

		add_action( 'load-plugins.php',             array( $this->rate_notice, 'defer_first_time' ) );
		add_action( 'admin_notices',                array( $this->rate_notice, 'display_notice' )   );
		add_action( 'admin_post_su_dismiss_notice', array( $this->rate_notice, 'dismiss_notice' )   );


		/**
		 * Add/Save 'Slide link' field on attachment page.
		 */
		add_filter( 'attachment_fields_to_edit', 'su_slide_link_input', 10, 2 );
		add_filter( 'attachment_fields_to_save', 'su_slide_link_save', 10, 2 );

	}

	/**
	 * Register all of the hooks related to both admin area and public part of
	 * the plugin.
	 *
	 * @since    5.0.4
	 * @access   private
	 */
	private function define_common_hooks() {

		/**
		 * Register available shortcodes.
		 */
		add_action( 'init', array( 'Shortcodes_Ultimate_Shortcodes', 'register' ) );

		/**
		 * Disable wptexturize filter for nestable shortcodes.
		 */
		add_filter( 'no_texturize_shortcodes', 'su_filter_disable_wptexturize', 10 );

		/**
		 * Enable shortcodes in text widgets and category descriptions.
		 */
		$enable_shortcodes_in = (array) get_option( 'su_option_enable_shortcodes_in' );

		if ( in_array( 'category_description', $enable_shortcodes_in, true ) ) {
			add_filter( 'category_description', 'do_shortcode' );
		}

		if ( in_array( 'widget_text', $enable_shortcodes_in, true ) ) {
			add_filter( 'widget_text', 'do_shortcode' );
		}

		/**
		 * Enable custom formatting.
		 */
		if ( get_option( 'su_option_custom-formatting' ) === 'on' ) {
			add_filter( 'the_content', 'su_filter_custom_formatting' );
		}

	}

}
