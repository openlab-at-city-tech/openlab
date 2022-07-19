<?php
namespace ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;


/**
 * ElementsKit - the God class.
 * Initiate all necessary classes, hooks, configs.
 *
 * @since 1.0.0
 */
class Plugin {


	/**
	 * The plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Construct the plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// check on-boarding status
		Libs\Framework\Classes\Onboard_Status::instance()->onboard();
		// Enqueue frontend scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		// migrate old settings db to new format
		new Compatibility\Data_Migration\Settings_Db();

		// Enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );

		// Enqueue inline scripts
		Core\Build_Inline_Scripts::instance();

		// Register plugin settings pages
		Libs\Framework\Attr::instance();

		// Register default widgets
		Core\Build_Widgets::instance();

		// Register default modules
		Core\Build_Modules::instance();

		// register plugin activation actions
		( new Core\Activation_Actions() )->init();

		add_action( 'wp_head', array( $this, 'add_meta_for_search_excluded' ) );
		
		// Register ElementsKit supported widgets to Elementor from 3rd party plugins.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 1050 );

		// Register wpml compatibility
		Compatibility\Wpml\Init::instance();

		// Compatibility issues
		Compatibility\Conflicts\Init::instance();

		$is_pro_active = in_array( 'elementskit/elementskit.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		
		if ( is_admin() && Libs\Framework\Classes\Utils::instance()->get_settings( 'ekit_user_consent_for_banner', 'yes' ) == 'yes' ) {
			$filter_string = \ElementsKit_Lite::active_plugins();
			/**
			 * Show WPMET stories widget in dashboard
			 */
			\Wpmet\Libs\Stories::instance( 'elementskit-lite' )
			// ->is_test(true)
			->set_filter( $filter_string )
			->set_plugin( 'ElementsKit', 'https://wpmet.com/plugin/elementskit/' )
			->set_api_url( 'https://api.wpmet.com/public/stories/' )
			->call();

			/**
			 * Show WPMET banner (codename: jhanda)
			 */
			\Wpmet\Libs\Banner::instance( 'elementskit-lite' )
			// ->is_test(true)
			->set_filter( ltrim( $filter_string, ',' ) )
			->set_api_url( 'https://api.wpmet.com/public/jhanda' )
			->set_plugin_screens( 'edit-elementskit_template' )
			->set_plugin_screens( 'toplevel_page_elementskit' )
			->call();

			/**
			 *  Ask for rating
			 *  A rating notice will appear depends on 
			 *  @set_first_appear_day methods 
			 */
			\Wpmet\Libs\Rating::instance( 'elementskit-lite' )
			->set_plugin( 'ElementsKit', 'https://wpmet.com/wordpress.org/rating/elementskit' )
			->set_plugin_logo( 'https://ps.w.org/elementskit-lite/assets/icon-128x128.gif', 'width:150px !important' )
			->set_allowed_screens( 'edit-elementskit_template' )
			->set_allowed_screens( 'toplevel_page_elementskit' )
			->set_allowed_screens( 'elementskit_page_elementskit-lite_get_help' )
			->set_priority( 10 )
			->set_first_appear_day( 7 )
			->set_condition( true )
			->call();

		}

		/**
		 * Show go Premium menu
		 */
		\Wpmet\Libs\Pro_Awareness::instance( 'elementskit-lite' )
			->set_parent_menu_slug( 'elementskit' )
			->set_plugin_file( 'elementskit-lite/elementskit-lite.php' )
			->set_pro_link(
				( ( \ElementsKit_Lite::package_type() != 'free' ) ? '' : 'https://wpmet.com/elementskit-pricing' )
			)
			->set_default_grid_thumbnail( \ElementsKit_Lite::lib_url() . 'pro-awareness/assets/support.png' )

			->set_page_grid(
				array(
					'url'       => 'https://wpmet.com/fb-group',
					'title'     => 'Join the Community',
					'thumbnail' => \ElementsKit_Lite::lib_url() . 'pro-awareness/assets/community.png',
				)
			)
			->set_page_grid(
				array(
					'url'       => 'https://www.youtube.com/playlist?list=PL3t2OjZ6gY8MVnyA4OLB6qXb77-roJOuY',
					'title'     => 'Video Tutorials',
					'thumbnail' => \ElementsKit_Lite::lib_url() . 'pro-awareness/assets/videos.png',
				)
			)
			->set_page_grid(
				array(
					'url'       => 'https://wpmet.com/plugin/elementskit/roadmaps#ideas',
					'title'     => 'Request a feature',
					'thumbnail' => \ElementsKit_Lite::lib_url() . 'pro-awareness/assets/request.png',
				)
			)
			->set_plugin_row_meta( 'Documentation', 'https://wpmet.com/elementskit-docs', array( 'target' => '_blank' ) )
			->set_plugin_row_meta( 'Facebook Community', 'https://wpmet.com/fb-group', array( 'target' => '_blank' ) )
			->set_plugin_row_meta( 'Rate the plugin ★★★★★', 'https://wordpress.org/support/plugin/elementskit-lite/reviews/#new-post', array( 'target' => '_blank' ) )
			->set_plugin_action_link( 'Settings', admin_url() . 'admin.php?page=elementskit' )
			->set_plugin_action_link(
				( $is_pro_active ? '' : 'Go Premium' ),
				'https://wpmet.com/plugin/elementskit',
				array(
					'target' => '_blank',
					'style'  => 'color: #FCB214; font-weight: bold;',
				) 
			)
			->call();

		// Adding pro lebel
		if ( \ElementsKit_Lite::package_type() == 'free' ) {
			new Libs\Pro_Label\Init();
		}
	}

	/**
	 * Check the admin screen and show the rating notice if eligible
	 *
	 * @access private
	 * @return boolean
	 */
	private function should_show_rating_notice() {

		if ( \ElementsKit_Lite::package_type() == 'free' ) {
			return true;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$current_screen     = ( get_current_screen() )->base;
		$current_post_type  = ( get_current_screen() )->post_type;
		$eligible_post_type = array( 'elementskit_template' );
		$eligible_screens   = array( 'plugins', 'dashboard', 'elementskit', 'themes' );

		if ( in_array( $current_post_type, $eligible_post_type ) ) {
			return true;
		}

		if ( in_array( $current_screen, $eligible_screens ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueue scripts
	 *
	 * Enqueue js and css to frontend.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_frontend() {
		wp_enqueue_script( 'elementskit-framework-js-frontend', \ElementsKit_Lite::lib_url() . 'framework/assets/js/frontend-script.js', array( 'jquery' ), \ElementsKit_Lite::version(), true );
	}

	/**
	 * Enqueue scripts
	 *
	 * Enqueue js and css to admin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_admin() {
		$screen = get_current_screen();

		if ( ! in_array( $screen->id, array( 'nav-menus', 'toplevel_page_elementskit', 'edit-elementskit_template', 'elementskit_page_elementskit-license', 'elementskit_page_elementskit-lite_get_help' ) ) ) {
			return;
		}

		wp_register_style( 'fontawesome', \ElementsKit_Lite::widget_url() . 'init/assets/css/font-awesome.min.css', false, \ElementsKit_Lite::version() );
		wp_register_style( 'elementskit-font-css-admin', \ElementsKit_Lite::module_url() . 'elementskit-icon-pack/assets/css/ekiticons.css', false, \ElementsKit_Lite::version() );
		wp_register_style( 'elementskit-init-css-admin', \ElementsKit_Lite::lib_url() . 'framework/assets/css/admin-style.css', false, \ElementsKit_Lite::version() );

		wp_enqueue_style( 'fontawesome' );
		wp_enqueue_style( 'elementskit-font-css-admin' );
		wp_enqueue_style( 'elementskit-init-css-admin' );
		
		wp_enqueue_script( 'ekit-admin-core', \ElementsKit_Lite::lib_url() . 'framework/assets/js/ekit-admin-core.js', array( 'jquery' ), \ElementsKit_Lite::version(), true );

		$data['rest_url'] = get_rest_url();
		$data['nonce']    = wp_create_nonce( 'wp_rest' );

		wp_localize_script( 'ekit-admin-core', 'rest_config', $data );

		wp_localize_script(
			'ekit-admin-core',
			'ekit_ajax_var',
			array(
				'nonce' => wp_create_nonce( 'ajax-nonce' ),
			)
		);
	}

	/**
	 * Control registrar.
	 *
	 * Register the custom controls for Elementor
	 * using `elementskit/widgets/widgets_registered` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_control( $widgets_manager ) {
		do_action( 'elementskit/widgets/widgets_registered', $widgets_manager );
	}


	/**
	 * Widget registrar.
	 *
	 * Retrieve all the registered widgets
	 * using `elementor/widgets/register` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets( $widgets_manager ) {
		do_action( 'elementskit/widgets/widgets_registered', $widgets_manager );
	}

	/**
	 * Excluding ElementsKit template and megamenu content from search engine.
	 * See - https://wordpress.org/support/topic/google-is-indexing-elementskit-content-as-separate-pages/
	 *
	 * @since 1.4.5
	 * @access public
	 */
	public function add_meta_for_search_excluded() {
		if ( in_array(
			get_post_type(),
			array( 'elementskit_widget', 'elementskit_template', 'elementskit_content' )
		)
			) {
			echo '<meta name="robots" content="noindex,nofollow" />', "\n";
		}
	}

	/**
	 * Autoloader.
	 *
	 * ElementsKit autoloader loads all the classes needed to run the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	public static function registrar_autoloader() {
		require_once \ElementsKit_Lite::plugin_dir() . '/autoloader.php';
		Autoloader::run();
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {

			do_action( 'elementskit_lite/before_loaded' );

			// Fire when ElementsKit instance.
			self::$instance = new self();

			do_action( 'elementskit/loaded' ); // legacy support
			do_action( 'elementskit_lite/after_loaded' );
		}

		return self::$instance;
	}
}
