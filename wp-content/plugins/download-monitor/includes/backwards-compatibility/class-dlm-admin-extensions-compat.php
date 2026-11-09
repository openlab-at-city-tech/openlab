<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'DLM_Admin_Extensions' ) ) {

	class DLM_Admin_Extensions {

		/**
		 * @var DLM_Admin_Extensions
		 */
		private static $instance;

		/**
		 * Populated externally (e.g. DLM_Product_Manager::get()->get_products()) by callers
		 * that still expect this legacy property.
		 *
		 * @var array
		 */
		public $installed_extensions = array();

		/**
		 * @var array
		 */
		private $licensed_extensions = array();

		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Admin_Extensions ) ) {
				self::$instance = new DLM_Admin_Extensions();
			}

			return self::$instance;
		}

		private function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_notices', array( $this, 'outdated_pro_notice' ) );
			}
			$this->set_licensed_extensions();
		}

		/**
		 * Scans the legacy '{product_id}-license' options for an active status.
		 *
		 * @return void
		 */
		private function set_licensed_extensions() {
			global $wpdb;

			if ( ! DLM_Admin_Helper::is_dlm_admin_page() ) {
				return;
			}

			$extensions = $wpdb->get_results( $wpdb->prepare( "SELECT `option_name`, `option_value` FROM {$wpdb->prefix}options WHERE `option_name` LIKE %s AND `option_name` LIKE %s;", $wpdb->esc_like( 'dlm-' ) . '%', '%' . $wpdb->esc_like( '-license' ) ), ARRAY_A );

			foreach ( $extensions as $extension ) {
				$extension_name = str_replace( '-license', '', $extension['option_name'] );
				$value          = maybe_unserialize( $extension['option_value'] );

				if ( isset( $value['status'] ) && 'active' === $value['status'] ) {
					$this->licensed_extensions[] = $extension_name;
				}
			}
		}

		/**
		 * @return array
		 */
		public function get_licensed_extensions() {
			return $this->licensed_extensions;
		}

		public function outdated_pro_notice() {
			if ( ! current_user_can( 'manage_options' ) || ! class_exists( 'WPChill_Notifications' ) ) {
				return;
			}

			$current = defined( 'DLM_PRO_VERSION' ) ? DLM_PRO_VERSION : '?';

			WPChill_Notifications::add_notification(
				'dlm-pro-outdated',
				array(
					'title'   => esc_html__( 'DLM Pro needs an update', 'download-monitor' ),
					'message' => sprintf(
						/* translators: 1: currently installed DLM Pro version, 2: minimum required version */
						esc_html__( "You're running an outdated version of DLM Pro (%1\$s). Please update it to version %2\$s or later to keep using Download Monitor extensions.", 'download-monitor' ),
						esc_html( $current ),
						esc_html( DLM_Pro_Compat::MIN_VERSION )
					),
					'status'  => 'warning',
					'source'  => array(
						'slug' => 'download-monitor',
						'name' => 'Download Monitor',
					),
					'actions' => array(
						array(
							'label'   => esc_html__( 'Go to Plugins', 'download-monitor' ),
							'id'      => 'dlm-pro-outdated-plugins',
							'url'     => esc_url( admin_url( 'plugins.php' ) ),
							'variant' => 'primary',
						),
					),
				)
			);
		}

		/**
		 * Shared message, escaped except for the Plugins-page link.
		 *
		 * @return string
		 */
		private function outdated_pro_message() {
			$current = defined( 'DLM_PRO_VERSION' ) ? DLM_PRO_VERSION : '?';

			return sprintf(
				/* translators: 1: currently installed DLM Pro version, 2: minimum required version, 3: opening link tag to the Plugins page, 4: closing link tag */
				esc_html__( 'Download Monitor: you\'re running an outdated version of DLM Pro (%1$s). Please update it to version %2$s or later to keep using Download Monitor extensions. %3$sGo to Plugins%4$s', 'download-monitor' ),
				esc_html( $current ),
				esc_html( DLM_Pro_Compat::MIN_VERSION ),
				'<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">',
				'</a>'
			);
		}

		public function get_json() {
			return array(
				'success' => false,
				'message' => '<div class="notice notice-warning inline"><p>' . $this->outdated_pro_message() . '</p></div>',
			);
		}

		public function get_response() {
			return (object) array( 'message' => '' );
		}

		public function get_tabs() {
			return array();
		}

		public function get_available_extensions() {
			return array();
		}

		public function get_free_extensions() {
			return array();
		}

		public function get_installed_extensions() {
			return array();
		}

		public function get_extensions_package() {
			return array();
		}
	}
}
