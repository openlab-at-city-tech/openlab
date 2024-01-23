<?php
namespace ElementsKit_Lite\Modules\Onepage_Scroll;

defined( 'ABSPATH' ) || exit;

class Init {
	private $dir;
	private $url;

	public function __construct() {

		// get current directory path
		$this->dir = dirname( __FILE__ ) . '/';

		// get current module's url
		$this->url = \ElementsKit_Lite::plugin_url() . 'modules/onepage-scroll/';
		
		// enqueue styles and scripts
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'load_styles' ) );
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'editor_scripts' ) );

		// include all necessary files
		$this->include_files();

		// calling the sticky controls
		new \Elementor\ElementsKit_Extend_Onepage_Scroll();

		if ( \ElementsKit_Lite::package_type() === 'pro' ) :
			new \Elementor\ElementsKit_Pro_Extend_Onepage_Scroll();
		endif;
	}
	
	public function include_files() {
		include $this->dir . 'extend-controls.php';
		include $this->dir . 'extend-controls-pro.php';
	}

	public function load_styles() {
		if ( self::get_page_data_setting( 'ekit_onepagescroll' ) ) :
			wp_enqueue_style( 'one-page-scroll', $this->url . 'assets/css/one-page-scroll.min.css', array(), \ElementsKit_Lite::version() );
		endif;
	}

	public function load_scripts() {
		if ( self::get_page_data_setting( 'ekit_onepagescroll' ) ) :
			wp_enqueue_script( 'one-page-scroll', $this->url . 'assets/js/one-page-scroll.js', array( 'jquery', 'elementor-frontend' ), \ElementsKit_Lite::version(), true );
		endif;
	}

	public function editor_scripts() {
		// todo: has some conflicts with dependency.
		// wp_enqueue_script( 'ekit-onepage-scroll-editor', $this->url . 'assets/js/editor.js', ['jquery', 'elementor-editor', 'elementor-frontend'], \ElementsKit_Lite::version(), true );
	}

	public static function get_settings_model() {
		$post_id = get_the_ID();
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		return $page_settings_manager->get_model( $post_id );
	}

	public static function get_page_data_setting( $id ) {
		$page_settings = self::get_settings_model()->get_data('settings');
		return isset($page_settings[$id]) ? $page_settings[$id] : false;
	}

	public static function get_page_setting( $id ) {
		$settings_model = self::get_settings_model();
		return $settings_model->get_settings( $id );
	}
}
