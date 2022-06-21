<?php 
namespace ElementsKit_Lite\Modules\Controls;

defined( 'ABSPATH' ) || exit;

class Init {

	// instance of all control's base class
	public static function get_url() {
		return \ElementsKit_Lite::module_url() . 'controls/';
	}
	public static function get_dir() {
		return \ElementsKit_Lite::module_dir() . 'controls/';
	}

	public function __construct() {

		// Includes necessary files
		$this->include_files();
		
		// Initilizating control hooks
		add_action( 'elementor/controls/register', array( $this, 'image_choose' ), 11 );
		add_action( 'elementor/controls/register', array( $this, 'ajax_select2' ), 11 );
		add_action( 'elementor/controls/register', array( $this, 'widgetarea' ), 11 );

		// Initilizating control scripts
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'widgetarea_enqueue_styles_editor' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'widgetarea_enqueue_scripts_editor' ), 22 );

		// Initilizating control classes
		$widget_area_utils = new Widget_Area_Utils();
		$widget_area_utils->init();
	}

	private function include_files() {
		// Controls_Manager
		include_once self::get_dir() . 'control-manager.php';

		// image choose
		include_once self::get_dir() . 'image-choose.php';

		// ajax select2
		include_once self::get_dir() . 'ajax-select2.php';
		include_once self::get_dir() . 'ajax-select2-api.php';

		// widgetarea
		include_once self::get_dir() . 'widget-area-utils.php';
		include_once self::get_dir() . 'widget-area.php';
	}

	public function image_choose( $controls_manager ) {
		$controls_manager->register( new \ElementsKit_Lite\Modules\Controls\Image_Choose() );
	}

	public function ajax_select2( $controls_manager ) {
		$controls_manager->register( new \ElementsKit_Lite\Modules\Controls\Ajax_Select2() );
	}

	public function widgetarea( $controls_manager ) {
		$controls_manager->register( new \ElementsKit_Lite\Modules\Controls\Widget_Area() );
	}
	
	public function widgetarea_enqueue_scripts_editor() {
		/**
		 * widgetarea-editor.js
		 * 
		 * Use dependencies ['jquery', 'elementor-frontend']
		 * Using ['jquery', 'elementor-editor'] causes Advanced Widgets content editor window not to open
		 */
		if ( is_preview() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_script( 'elementskit-js-widgetarea-control-editor', self::get_url() . 'assets/js/widgetarea-editor.js', array( 'jquery', 'elementor-frontend' ), \ElementsKit_Lite::version(), true );
		}
	}
	
	public function widgetarea_enqueue_styles_editor() {
		if ( is_preview() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_style( 'elementskit-css-widgetarea-control-editor', self::get_url() . 'assets/css/widgetarea-editor.css', array(), \ElementsKit_Lite::version() );
		}
	}

}
