<?php
namespace ElementsKit_Lite\Modules\Megamenu;

use ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;

class Init {

	public $dir;
	
	public $url;
		
	public static $menuitem_settings_key = 'elementskit_menuitem_settings';
	public static $megamenu_settings_key = 'megamenu_settings';
	
	public function __construct() {

		// get current directory path
		$this->dir = dirname( __FILE__ ) . '/';

		// get current module's url
		$this->url = \ElementsKit_Lite::plugin_url() . 'modules/megamenu/';
		
		// enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// include all necessary files
		$this->include_files();

		new Options();
	}
	
	public function include_files() {
		include $this->dir . 'api.php';
		include $this->dir . 'walker-nav-menu.php';

		$backward_class_file = ElementsKit_Lite::plugin_dir() . 'compatibility/backward/walker-nav-menu-backward-compatiblity.php';
		if ( file_exists( $backward_class_file ) ) {
			include $backward_class_file;
		}
	}

	public function enqueue_styles() {
		$screen = get_current_screen();
		if ( $screen->base == 'nav-menus' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'fonticonpicker', $this->url . 'assets/css/jquery.fonticonpicker.css', false, \ElementsKit_Lite::version() );
			wp_enqueue_style( 'elementskit-menu-admin-style', $this->url . 'assets/css/admin-style.css', false, \ElementsKit_Lite::version() );
		}
	}

	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( $screen->base == 'nav-menus' ) {
			wp_enqueue_script( 'fonticonpicker', $this->url . 'assets/js/jquery.fonticonpicker.min.js', array( 'jquery' ), \ElementsKit_Lite::version(), true );
			wp_enqueue_script( 'elementskit-menu-admin-script', $this->url . 'assets/js/admin-script.js', array( 'jquery', 'wp-color-picker' ), \ElementsKit_Lite::version(), true );
		}
	}
}
