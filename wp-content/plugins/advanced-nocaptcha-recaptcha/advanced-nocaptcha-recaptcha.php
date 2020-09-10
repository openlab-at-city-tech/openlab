<?php
/*
Plugin Name: Advanced noCaptcha & invisible Captcha
Plugin URI: https://www.shamimsplugins.com/contact-us/
Description: Show noCaptcha or invisible captcha in Comment Form, bbPress, BuddyPress, WooCommerce, CF7, Login, Register, Lost Password, Reset Password. Also can implement in any other form easily.
Version: 6.1.1
Author: Shamim Hasan
Author URI: https://www.shamimsplugins.com/contact-us/
Text Domain: advanced-nocaptcha-recaptcha
License: GPLv2 or later
WC tested up to: 4.0.1
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once ABSPATH . '/wp-admin/includes/plugin.php';

class ANR {

	private static $instance;

	private function __construct() {
		if ( function_exists( 'anr_get_option' ) ) {
			if ( ! function_exists( 'deactivate_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			deactivate_plugins( 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php' );
			return;
		}
		$this->constants();
		$this->includes();
		$this->actions();
		// $this->filters();
	}

	public static function init() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function constants() {
		define( 'ANR_PLUGIN_VERSION', '6.1.1' );
		define( 'ANR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'ANR_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
		define( 'ANR_PLUGIN_FILE', __FILE__ );
	}

	private function includes() {
		require_once ANR_PLUGIN_DIR . 'functions.php';
	}

	private function actions() {
		add_action( 'after_setup_theme', 'anr_include_require_files' );
		add_action( 'init', 'anr_translation' );
		add_action( 'login_enqueue_scripts', 'anr_login_enqueue_scripts' );

		//cleanup after uninstall
		anr_fs()->add_action('after_uninstall', 'anr_fs_uninstall_cleanup');
		//Support fourm link in admin dashboard sidebar
		anr_fs()->add_filter( 'support_forum_url', 'anr_fs_support_forum_url' );
	}
} //END Class


if ( function_exists( 'anr_fs' ) ) {
	anr_fs()->set_basename( false, __FILE__ );
} else {
	// DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
	if ( ! function_exists( 'anr_fs' ) ) {
		// Create a helper function for easy SDK access.
		function anr_fs() {
			global $anr_fs;
			$for_network = is_plugin_active_for_network( plugin_basename( __FILE__ ) );
	
			if ( ! isset( $anr_fs ) ) {
				// Activate multisite network integration.
				if ( $for_network && ! defined( 'WP_FS__PRODUCT_5860_MULTISITE' ) ) {
					define( 'WP_FS__PRODUCT_5860_MULTISITE', true );
				}
				// Include Freemius SDK.
				require_once dirname(__FILE__) . '/freemius/start.php';
	
				$anr_fs = fs_dynamic_init( array(
					'id'                  => '5860',
					'slug'                => 'advanced-nocaptcha-recaptcha',
					'premium_slug'        => 'advanced-nocaptcha-and-invisible-captcha-pro',
					'type'                => 'plugin',
					'public_key'          => 'pk_8758a9fa397c3760defbec41e2e35',
					'is_premium'          => false,
					'premium_suffix'      => 'PRO',
					// If your plugin is a serviceware, set this option to false.
					'has_premium_version' => true,
					'has_addons'          => false,
					'has_paid_plans'      => true,
					'anonymous_mode'      => true,
					'is_live'             => true,
					'menu'                => array(
						'slug'           => 'anr-admin-settings',
						'contact'        => false,
						'network'        => $for_network,
						'parent'         => array(
							'slug' => $for_network ? 'settings.php' : 'options-general.php',
						),
					),
				) );
			}
	
			return $anr_fs;
		}
	
		// Init Freemius.
		anr_fs();
		// Signal that SDK was initiated.
		do_action( 'anr_fs_loaded' );
	}

	// ... Your plugin's main file logic ...
	ANR::init();
}

