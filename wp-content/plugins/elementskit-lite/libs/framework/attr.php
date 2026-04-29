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
		add_filter('admin_footer_text', [$this, 'elementskit_admin_footer_text']);
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

	public function elementskit_admin_footer_text($text) {
		// Only show on ElementsKit admin pages
		if ( ! $this->is_elementskit_page() ) {
			return $text;
		}

		$plugin_name = '<strong>' . esc_html__( 'ElementsKit', 'elementskit-lite' ) . '</strong>';
		$review_url  = esc_url( 'https://wordpress.org/support/plugin/elementskit-lite/reviews/?filter=5');

		$text = sprintf(
			'<span class="elementskit-footer-text"><i>%1$s</i></span>',
			sprintf(
				/* translators: 1: Plugin name, 2: Review URL */
				__( 'Enjoying %1$s? Please consider leaving us a <a href="%2$s" target="_blank" rel="noopener noreferrer">★★★★★</a> review. Your support means a lot to our team!', 'elementskit-lite' ),
				$plugin_name,
				$review_url
			)
		);
		return $text;
	}

	/**
	 * Check if current page is an ElementsKit admin page
	 *
	 * @return bool
	 */
	private function is_elementskit_page() {
		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return false;
		}

		// List of allowed pages (from ?page parameter)
		$allowed_pages = array(
			'elementskit',
			'elementskit-license',
			'forms',
			'elementskit-lite_wpmet_plugins',
			'elementskit-lite_get_help',
		);

		// List of allowed post types (from ?post_type parameter)
		$allowed_post_types = array(
			'elementskit_template',
			'elementskit_widget',
		);

		// Check by post_type (for custom post type listings)
		if ( ! empty( $current_screen->post_type ) && in_array( $current_screen->post_type, $allowed_post_types, true ) ) {
			return true;
		}

		// Check by page parameter (for admin pages)
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		if ( $page && in_array( $page, $allowed_pages, true ) ) {
			return true;
		}

		return false;
	}

	public function register_settings_contents__settings() {
		include self::get_dir() . 'views/init.php';
	}

	public function user_consent_for_banner() {
		include self::get_dir() . 'views/layout-user-consent-for-banner.php';
	}

}
