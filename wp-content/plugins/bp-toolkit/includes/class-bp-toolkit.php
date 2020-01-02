<?php

/**
* The file that defines the core plugin class
*
* A class definition that includes attributes and functions used across both the
* public-facing side of the site and the admin area.
*
* @link       https://www.therealbenroberts.com
* @since      1.0.0
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
*/

/**
* The core plugin class.
*
* This is used to define internationalization, admin-specific hooks, and
* public-facing site hooks.
*
* Also maintains the unique identifier of this plugin as well as the current
* version of the plugin.
*
* @since      1.0.0
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BP_Toolkit {

	/**
	* The loader that's responsible for maintaining and registering all hooks that power
	* the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      BP_Toolkit_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $loader;

	/**
	* The unique identifier of this plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      string    $bp_toolkit    The string used to uniquely identify this plugin.
	*/
	protected $bp_toolkit;

	/**
	* The current version of the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      string    $version    The current version of the plugin.
	*/
	protected $version;

	/**
	* Define the core functionality of the plugin.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the admin area and
	* the public-facing side of the site.
	*
	* @since    1.0.0
	*/

	public function __construct() {
		if ( defined( 'BP_TOOLKIT_VERSION' ) ) {
			$this->version = BP_TOOLKIT_VERSION;
		} else {
			$this->version = '2.0.2';
		}
		$this->bp_toolkit = 'bp_toolkit';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	* Initialise the distinct parts of the toolkit.
	*
	* This function might allow for the switching off of various parts in a future release.
	*
	* @since    1.0.0
	*/

	public function bptk_init() {

		// Initialise our 'block' function
		$this->block_init();

		// Initialise our 'suspend' function
		$this->suspend_init();

		// Initialise our 'report' function
		$this->report_init();
	}


	/**
	* Load the required dependencies for this plugin.
	*
	* Include the following files that make up the plugin:
	*
	* - BP_Toolkit_Loader. Orchestrates the hooks of the plugin.
	* - BP_Toolkit_i18n. Defines internationalization functionality.
	* - BP_Toolkit_Admin. Defines all hooks for the admin area.
	* - BP_Toolkit_Public. Defines all hooks for the public side of the site.
	*
	* Create an instance of the loader which will be used to register the hooks
	* with WordPress.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function load_dependencies() {

		/**
		* The class responsible for orchestrating the actions and filters of the
		* core plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-toolkit-loader.php';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-toolkit-admin.php';

		/**
		* The class responsible for blank slate functionality
		* of the plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-toolkit-blank-slate.php';


		/**
		* The class responsible for defining all actions that occur in the public-facing
		* side of the site.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-toolkit-public.php';;


		/**
		* The class responsible for defining the Toolkit's block functionality
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-toolkit-block.php';

		/**
		* The class responsible for defining the Toolkit's suspend functionality
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-toolkit-suspend.php';

		/**
		* The class responsible for defining the Toolkit's report functionality
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-toolkit-report.php';


		$this->loader = new BP_Toolkit_Loader();

	}

	/**
	* Define the locale for this plugin for internationalization.
	*
	* Uses the BP_Toolkit_i18n class in order to set the domain and to register the hook
	* with WordPress.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function set_locale() {

		load_plugin_textdomain(
			'bp-toolkit',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	* Register all of the hooks related to the admin area functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function define_admin_hooks() {

		$plugin_admin = new BP_Toolkit_Admin( $this->get_bp_toolkit(), $this->get_version() );

		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'setup_report_post_type' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu', 9 );
		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'add_custom_header' );
		$this->loader->add_action( 'parent_file', $plugin_admin, 'prefix_highlight_taxonomy_parent_menu' );
		$this->loader->add_action( 'save_post_report', $plugin_admin, 'save_report', 10, 3 );
		$this->loader->add_action( 'current_screen', $plugin_admin, 'blank_slate' );

		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_rate_us' );
		$this->loader->add_filter( 'manage_report_posts_columns', $plugin_admin, 'set_report_columns' );
		$this->loader->add_filter( 'manage_report_posts_custom_column', $plugin_admin, 'add_report_columns', 10, 2 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_block_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_suspend_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_report_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_styling_settings' );
	}

	/**
	* Register all of the hooks related to the public-facing functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function define_public_hooks() {

		$plugin_public = new BP_Toolkit_Public( $this->get_bp_toolkit(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'bptk_add_custom_styles' );


	}

	/**
	* Instantiate block functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function block_init() {

		$block = new BPTK_Block( $this->get_bp_toolkit(), $this->get_version() );

	}

	/**
	* Instantiate suspend functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function suspend_init() {

		$suspend = new BPTK_Suspend( $this->get_bp_toolkit(), $this->get_version() );

	}

	/**
	* Instantiate report functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	public function report_init() {

		$report = new BPTK_Report( $this->get_bp_toolkit(), $this->get_version() );

	}

	/**
	* Run the loader to execute all of the hooks with WordPress.
	*
	* @since    1.0.0
	*/
	public function run() {
		$this->loader->run();
	}

	/**
	* The name of the plugin used to uniquely identify it within the context of
	* WordPress and to define internationalization functionality.
	*
	* @since     1.0.0
	* @return    string    The name of the plugin.
	*/
	public function get_bp_toolkit() {
		return $this->bp_toolkit;
	}

	/**
	* The reference to the class that orchestrates the hooks with the plugin.
	*
	* @since     1.0.0
	* @return    BP_Toolkit_Loader    Orchestrates the hooks of the plugin.
	*/
	public function get_loader() {
		return $this->loader;
	}

	/**
	* Retrieve the version number of the plugin.
	*
	* @since     1.0.0
	* @return    string    The version number of the plugin.
	*/
	public function get_version() {
		return $this->version;
	}

}
