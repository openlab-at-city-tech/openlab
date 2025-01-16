<?php

/**
 * Handle saving feature settings.
 */
class EPKB_Debug_Controller {

	const EPKB_DEBUG = 'epkb_debug';
	const EPKB_ADVANCED_SEARCH_DEBUG = '_epkb_advanced_search_debug_activated';
	const EPKB_SHOW_LOGS = 'epkb_debug_show_logs';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_debug_info' ) );

		add_action( 'wp_ajax_epkb_toggle_debug', array( $this, 'toggle_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_toggle_debug', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_show_logs', array( $this, 'show_logs' ) );
		add_action( 'wp_ajax_nopriv_epkb_show_logs', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_reset_logs', array( $this, 'reset_logs' ) );
		add_action( 'wp_ajax_nopriv_epkb_reset_logs', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_enable_advanced_search_debug', array( $this, 'enable_advanced_search_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_enable_advanced_search_debug', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user clicks to toggle debug.
	 */
	public function toggle_debug() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$is_debug_on = get_transient( EPKB_Debug_Controller::EPKB_DEBUG );

		if ( empty( $is_debug_on ) ) {
			set_transient( EPKB_Debug_Controller::EPKB_DEBUG, true, DAY_IN_SECONDS );
		} else {
			delete_transient( EPKB_Debug_Controller::EPKB_DEBUG );
		}

		EPKB_Utilities::ajax_show_info_die();
	}

	/**
	 * Triggered when user clicks to show logs.
	 */
	public function show_logs() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		set_transient( EPKB_Debug_Controller::EPKB_SHOW_LOGS, true, HOUR_IN_SECONDS );

		EPKB_Utilities::ajax_show_info_die();
	}

	/**
	 * Triggered when user clicks to show logs.
	 */
	public function reset_logs() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		EPKB_Logging::reset_logs();

		EPKB_Utilities::ajax_show_info_die();
	}

	/**
	 * Triggered when user clicks to toggle Advanced Search debug.
	 */
	public function enable_advanced_search_debug() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$is_debug_on = get_transient( EPKB_Debug_Controller::EPKB_ADVANCED_SEARCH_DEBUG );

		if ( empty( $is_debug_on ) ) {
			set_transient( EPKB_Debug_Controller::EPKB_ADVANCED_SEARCH_DEBUG, true, DAY_IN_SECONDS );
		} else {
			delete_transient( EPKB_Debug_Controller::EPKB_ADVANCED_SEARCH_DEBUG );
		}

		EPKB_Utilities::ajax_show_info_die();
	}

	/**
	 * Generates a System Info download file
	 */
	public function download_debug_info() {

		if ( EPKB_Utilities::post( 'action' ) != 'epkb_download_debug_info' ) {
			return;
		}

		$debug_box = EPKB_Utilities::post( 'epkb_debug_box' );
		if ( empty( $debug_box ) ) {
			return;
		}

		// check wpnonce
		$wp_nonce = EPKB_Utilities::post( '_wpnonce_epkb_ajax_action' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epkb_ajax_action' ) ) {
			wp_die( esc_html__( 'You do not have permission to get debug info', 'echo-knowledge-base' ) . ' (E01)'  );
		}

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="echo-debug-info.txt"' );

		$output = '';
		if ( $debug_box == 'main' ) {
			$output = EPKB_Config_Tools_Page::display_debug_data();
		}
		if ( $debug_box == 'asea' ) {
			$output = EPKB_Config_Tools_Page::display_asea_debug_data();
		}

		echo esc_html( wp_strip_all_tags( $output ) );

		die();
	}
}