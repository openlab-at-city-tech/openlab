<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

use ElementsKit_Lite\Config\Module_List;
use ElementsKit_Lite\Config\Widget_List;

defined( 'ABSPATH' ) || exit;

class Ajax {
	private $utils;

	public function __construct() {
		add_action( 'wp_ajax_ekit_admin_action', array( $this, 'elementskit_admin_action' ) );
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

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function return_json( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			return wp_json_encode( $data );
		} else {
			return $data;
		}
	}

}
