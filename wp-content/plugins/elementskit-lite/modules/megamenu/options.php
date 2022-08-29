<?php 
namespace ElementsKit_Lite\Modules\Megamenu;

use ElementsKit_Lite\Libs\Framework\Attr;

defined( 'ABSPATH' ) || exit;

class Options {
	private $dir;
	private $url;

	protected $current_menu_id = null;

	public function __construct() {
		// get current directory path
		$this->dir = dirname( __FILE__ ) . '/';

		// get current module's url
		$this->url = \ElementsKit_Lite::plugin_url() . 'modules/megamenu/';
		
		add_action( 'admin_footer', array( $this, 'options_menu_item' ) );
		add_action( 'admin_footer', array( $this, 'options_megamenu' ) );
		add_action( 'admin_head', array( $this, 'save_megamenu_options' ) );
	}

	public function current_menu_id() {

		if ( null !== $this->current_menu_id ) {
			return $this->current_menu_id;
		}

		$nav_menus            = wp_get_nav_menus( array( 'orderby' => 'name' ) );
		$menu_count           = count( $nav_menus );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are taking menu id from the URL. The method only can access admin. So nonce verification is not required.
		$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are taking menu id from the URL. The method only can access admin. So nonce verification is not required.
		$add_new_screen       = ( isset( $_GET['menu'] ) && 0 == $_GET['menu'] ) ? true : false;

		$this->current_menu_id = $nav_menu_selected_id;

		// If we have one theme location, and zero menus, we take them right into editing their first menu
		$page_count                  = wp_count_posts( 'page' );
		$one_theme_location_no_menus = ( 1 == count( get_registered_nav_menus() ) && ! $add_new_screen && empty( $nav_menus ) && ! empty( $page_count->publish ) ) ? true : false;

		// Get recently edited nav menu
		$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
		if ( empty( $recently_edited ) && is_nav_menu( $this->current_menu_id ) ) {
			$recently_edited = $this->current_menu_id;
		}

		// Use $recently_edited if none are selected
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are checking the menu id exist or not. The method only can access admin. So nonce verification is not required.
		if ( empty( $this->current_menu_id ) && ! isset( $_GET['menu'] ) && is_nav_menu( $recently_edited ) ) {
			$this->current_menu_id = $recently_edited;
		}

		// On deletion of menu, if another menu exists, show it
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are checking the post action type. The method only can access admin. So nonce verification is not required.
		if ( ! $add_new_screen && 0 < $menu_count && isset( $_GET['action'] ) && 'delete' == $_GET['action'] ) {
			$this->current_menu_id = $nav_menus[0]->term_id;
		}

		// Set $this->current_menu_id to 0 if no menus
		if ( $one_theme_location_no_menus ) {
			$this->current_menu_id = 0;
		} elseif ( empty( $this->current_menu_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
			// if we have no selection yet, and we have menus, set to the first one in the list
			$this->current_menu_id = $nav_menus[0]->term_id;
		}

		return $this->current_menu_id;
	}
	
	public static function get_icons() {
		return include \ElementsKit_Lite::module_dir() . 'elementskit-icon-pack/icon-list.php';
	}    

	function options_menu_item() {
		$screen = get_current_screen();
		if ( $screen->base != 'nav-menus' ) {
			return;
		}

		include 'views/options-menu-item.php';
	}

	function options_megamenu() {
		$screen = get_current_screen();
		if ( $screen->base != 'nav-menus' ) {
			return;
		}
		
		$menu_id = $this->current_menu_id();
		$data    = Attr::instance()->utils->get_option( Init::$megamenu_settings_key, array() );
		$data    = ( isset( $data[ 'menu_location_' . $menu_id ] ) ) ? $data[ 'menu_location_' . $menu_id ] : array();
		
		include 'views/options-megamenu.php';
	}

	public function save_megamenu_options() {
		$screen = get_current_screen();

		if ( $screen->base != 'nav-menus' || ! isset( $_POST['update-nav-menu-nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['update-nav-menu-nonce'] ) ), 'update-nav_menu') ) {
			return;
		}

		$menu_id    = isset( $_POST['menu'] ) ? wp_unslash(intval($_POST['menu'])) : 0;
		$is_enabled = isset( $_POST['is_enabled'] ) ? wp_unslash(intval($_POST['is_enabled'])) : 0;

		$data                                = Attr::instance()->utils->get_option( Init::$megamenu_settings_key, array() );
		$data[ 'menu_location_' . $menu_id ] = array(
			'is_enabled' => $is_enabled,
		);

		Attr::instance()->utils->save_option( Init::$megamenu_settings_key, $data );
	}
}
