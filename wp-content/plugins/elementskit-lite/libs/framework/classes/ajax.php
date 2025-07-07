<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

use ElementsKit_Lite\Config\Module_List;
use ElementsKit_Lite\Config\Widget_List;

defined( 'ABSPATH' ) || exit;

class Ajax {
	private $utils;

	public function __construct() {
		add_action( 'wp_ajax_ekit_admin_action', array( $this, 'elementskit_admin_action' ) );
		add_action( 'wp_ajax_ekit_onboard_plugins', array( $this, 'elementskit_onboard_plugins' ) );
		$this->utils = Utils::instance();
	}

	public function elementskit_admin_action() {
		// Check for nonce security
		if (!isset($_POST['nonce']) || ! wp_verify_nonce( sanitize_key(wp_unslash($_POST['nonce'])), 'ajax-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['widget_list'] ) ) {
			$widget_list          = Widget_List::instance()->get_list();
			$widget_list_input    = ! is_array( $_POST['widget_list'] ) ? array() : map_deep( wp_unslash( $_POST['widget_list'] ) , 'sanitize_text_field' );
			$widget_prepared_list = array();

			foreach ( $widget_list as $widget_slug => $widget ) {
				if ( isset( $widget['package'] ) && $widget['package'] == 'pro-disabled' ) {
					continue;
				}

				$widget['status'] = ( in_array( $widget_slug, $widget_list_input ) ? 'active' : 'inactive' );

				$widget_prepared_list[ $widget_slug ] = $widget;
			}

			$this->utils->save_option( 'widget_list', $widget_prepared_list );
		}

		if ( isset( $_POST['module_list'] ) ) {
			$module_list          = Module_List::instance()->get_list( 'optional' );
			$module_list_input    = ! is_array( $_POST['module_list'] ) ? array() : map_deep( wp_unslash( $_POST['module_list'] ) , 'sanitize_text_field' );
			$module_prepared_list = array();

			foreach ( $module_list as $module_slug => $module ) {
				if ( isset( $module['package'] ) && $module['package'] == 'pro-disabled' ) {
					continue;
				}

				$module['status'] = ( in_array( $module_slug, $module_list_input ) ? 'active' : 'inactive' );

				$module_prepared_list[ $module_slug ] = $module;
			}

			$this->utils->save_option( 'module_list', $module_prepared_list );
		}

		if ( isset( $_POST['user_data'] ) ) {
			$this->utils->save_option( 'user_data', empty( $_POST['user_data'] ) ? array() : map_deep( wp_unslash( $_POST['user_data'] ) , 'wp_filter_nohtml_kses' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- It will sanitize by wp_filter_nohtml_kses function
		}

		if ( isset( $_POST['settings'] ) ) {
			$this->utils->save_settings( empty( $_POST['settings'] ) ? array() : map_deep( wp_unslash( $_POST['settings'] ) , 'sanitize_text_field' )  ); 
		}

		do_action( 'elementskit/admin/after_save' );

		$response = array(
			'message' => self::plugin_activate_message( 'setup_configurations' )
		);

		$plugins = !empty($_POST['our_plugins']) && is_array($_POST['our_plugins']) ? $_POST['our_plugins'] : [];
		if($plugins) {
			$total_plugins = count($plugins);
			$total_steps   = 1 + $total_plugins;
			$percentage = ($total_steps > 0) ? (1 / $total_steps) * 100 : 100;
			$percentage = round($percentage);

			$response['progress'] = $percentage;
			$response['plugins'] = $plugins;
		}

		wp_send_json($response);

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function elementskit_onboard_plugins() {
		// Check for nonce security
		if (!isset($_POST['nonce']) || ! wp_verify_nonce( sanitize_key(wp_unslash($_POST['nonce'])), 'ajax-nonce' ) ) {
			return;
		}

		$plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_slug'] ) ) : '';
		if ( isset( $plugin_slug ) && current_user_can('install_plugins') ) {
			$status = \ElementsKit_Lite\Libs\Framework\Classes\Plugin_Installer::single_install_and_activate( $plugin_slug );
			if ( is_wp_error( $status ) ) {
				wp_send_json_error( array( 'status' => false ) );
			} else {
				wp_send_json_success(
					array(
						'message' => self::plugin_activate_message( $plugin_slug )
					)
				);
			}
		}
	}

	public static function plugin_activate_message($plugin_slug) {
		$plugins_message = [
			'setup_configurations' => esc_html__('Setup Configurations', 'elementskit-lite'),
			'elementskit-lite/elementskit-lite.php' => esc_html__('Page Builder Elements Installed', 'elementskit-lite'),
			'getgenie/getgenie.php' => esc_html__('AI Content & SEO Tool Installed', 'elementskit-lite'),
			'shopengine/shopengine.php' => esc_html__('WooCommerce Builder Installed', 'elementskit-lite'),
			'metform/metform.php' => esc_html__('Form Builder Installed', 'elementskit-lite'),
			'emailkit/EmailKit.php' => esc_html__('Email Customizer Installed', 'elementskit-lite'),
			'wp-social/wp-social.php' => esc_html__('Social Integration Installed', 'elementskit-lite'),
			'wp-ultimate-review/wp-ultimate-review.php' => esc_html__('Review Management Installed', 'elementskit-lite'),
			'wp-fundraising-donation/wp-fundraising.php' => esc_html__('Fundraising & Donations', 'elementskit-lite'),
			'gutenkit-blocks-addon/gutenkit-blocks-addon.php' => esc_html__('Page Builder Blocks Installed', 'elementskit-lite'),
			'popup-builder-block/popup-builder-block.php' => esc_html__('Popup Builder Installed', 'elementskit-lite'),
			'table-builder-block/table-builder-block.php' => esc_html__('Table Builder Installed', 'elementskit-lite'),
		];

		if ( array_key_exists( $plugin_slug, $plugins_message ) ) {
			return esc_html( $plugins_message[$plugin_slug] );
		} else {
			return esc_html__( 'Plugin Installed', 'elementskit-lite' );
		}
	}

	public function return_json( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			return wp_json_encode( $data );
		} else {
			return $data;
		}
	}
}
