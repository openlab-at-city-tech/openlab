<?php
/*
  Plugin Name: DK PDF Generator
  Version: 1.5.0
  Plugin URI: http://wp.dinamiko.com/demos/dkpdf-generator
  Description: Create PDF documents with your selected posts, pages and custom post types.
  Author: Emili Castells
  Author URI: http://www.dinamiko.com
  Text Domain: dkpdfg
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DKPDFG' ) ) {

	final class DKPDFG {

		private static $instance;

		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DKPDFG ) ) {

				self::$instance = new DKPDFG;

				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'dkpdfg_load_textdomain' ) );

				self::$instance->includes();

			}

			return self::$instance;

		}

		public function dkpdfg_load_textdomain() {

			load_plugin_textdomain( 'dkpdfg', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		}

		private function setup_constants() {

			if ( ! defined( 'DKPDFG_VERSION' ) ) { define( 'DKPDFG_VERSION', '1.5.0' ); }
			if ( ! defined( 'DKPDFG_PLUGIN_DIR' ) ) { define( 'DKPDFG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); }
			if ( ! defined( 'DKPDFG_PLUGIN_URL' ) ) { define( 'DKPDFGPLUGIN_URL', plugin_dir_url( __FILE__ ) ); }
			if ( ! defined( 'DKPDFG_PLUGIN_FILE' ) ) { define( 'DKPDFG_PLUGIN_FILE', __FILE__ ); }

		}

		private function includes() {

			if ( is_admin() ) {

				/**
				* TODO add a better way for DK PDF plugin dependency
				* remove tgmp and use our custom logic (admin messages...)
				*/
				// Include the TGM_Plugin_Activation class
				//require_once DKPDFG_PLUGIN_DIR . 'includes/class-tgm-plugin-activation.php';

				// settings
				require_once DKPDFG_PLUGIN_DIR . 'includes/class-dkpdfg-settings.php';
				$settings = new DKPDFG_Settings( $this );

				require_once DKPDFG_PLUGIN_DIR . 'includes/class-dkpdfg-admin-api.php';
				$this->admin = new DKPDFG_Admin_API();

			}

			// css / js
			require_once DKPDFG_PLUGIN_DIR . 'includes/dkpdfg-load-js-css.php';

			// functions
			require_once DKPDFG_PLUGIN_DIR . 'includes/dkpdfg-functions.php';

			// templates
			require_once DKPDFG_PLUGIN_DIR . 'includes/class-dkpdfg-template-loader.php';

			// shortcodes
			require_once DKPDFG_PLUGIN_DIR . 'includes/dkpdfg-shortcodes.php';

		}

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dkpdfg' ), DKPDFG_VERSION );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dkpdfg' ), DKPDFG_VERSION );
		}

	}

}

/**
* Creates an instance of DK PDF Generator
*/
function DKPDFG() {

	if ( version_compare( phpversion(), '5.4.0', '<' ) ) {

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		deactivate_plugins( '/dk-pdf-generator/dk-pdf-generator.php' );

		wp_die(
			'<p>' . 'DK PDF Generator can not be activated because it requires at least PHP version 5.4.0. ' . '</p>'
			. '<a href="' . admin_url( 'plugins.php' ) . '">' . esc_attr__( 'Back', 'dkpdf' ) . '</a>'
		);
	} else {

		return DKPDFG::instance();
	}
}

DKPDFG();
