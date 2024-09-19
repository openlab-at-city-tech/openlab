<?php 
namespace ElementsKit_Lite\Libs\Framework;

use ElementsKit_Lite\Libs\Framework\Classes\Utils;

defined( 'ABSPATH' ) || exit;

class Attr {

	use \ElementsKit_Lite\Traits\Singleton;
	
	public $utils;

	public static function get_dir() {
		return \ElementsKit_Lite::lib_dir() . 'framework/';
	}

	public static function get_url() {
		return \ElementsKit_Lite::lib_url() . 'framework/';
	}

	public static function key() {
		return 'elementskit';
	}

	public function __construct() {
		$this->utils = Classes\Utils::instance();
		new Classes\Ajax();

		// register admin menus
		add_action( 'admin_menu', array( $this, 'register_settings_menus' ) );
		// add_action('admin_menu', [$this, 'register_support_menu'], 999);

		// register js/ css
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// whitelist styles
		add_filter( 'mailpoet_conflict_resolver_whitelist_style', array( $this, 'whitelisted_styles' ) );

		add_action( 'elementskit-lite/pro_awareness/before_grid_contents', array( $this, 'user_consent_for_banner' ) );
	}

	public function whitelisted_styles( $styles ) {
		$styles[] = 'admin-global.css';
		return $styles;
	}

	public function include_files() {
	}

	public function enqueue_scripts() {
		wp_register_style( 'elementskit-admin-global', \ElementsKit_Lite::lib_url() . 'framework/assets/css/admin-global.css', false, \ElementsKit_Lite::version() );
		wp_enqueue_style( 'elementskit-admin-global' );
	}

	public function register_settings_menus() {

		// dashboard, main menu
		add_menu_page(
			esc_html__( 'ElementsKit Settings', 'elementskit-lite' ),
			'ElementsKit', // esc_html__( 'ElementsKit', 'elementskit-lite' ),
			'manage_options',
			self::key(),
			array( $this, 'register_settings_contents__settings' ),
			self::get_url() . 'assets/images/ekit_icon.svg',
			'58.6'
		);
	}


	public function register_settings_contents__settings() {
		include self::get_dir() . 'views/init.php';
	}

	public function user_consent_for_banner() {
		include self::get_dir() . 'views/layout-user-consent-for-banner.php';
	}

}
