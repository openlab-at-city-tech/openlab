<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle saving feature settings.
 */
class EPKB_Debug_Controller {

	const EPKB_DEBUG = 'epkb_debug';
	const EPKB_ADVANCED_SEARCH_DEBUG = '_epkb_advanced_search_debug_activated';
	const EPKB_SHOW_LOGS = 'epkb_debug_show_logs';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_debug_info' ) );
		add_action( 'admin_init', array( $this, 'download_ai_config' ) );

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

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_strip_all_tags removes all HTML/scripts
		echo wp_strip_all_tags( wp_specialchars_decode( $output, ENT_QUOTES ) );

		die();
	}

	/**
	 * Generates an AI configuration export file.
	 */
	public function download_ai_config() {

		if ( EPKB_Utilities::post( 'action' ) != 'epkb_download_ai_config' ) {
			return;
		}

		// check wpnonce
		$wp_nonce = EPKB_Utilities::post( '_wpnonce_epkb_ajax_action' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epkb_ajax_action' ) ) {
			wp_die( esc_html__( 'You do not have permission to export AI config', 'echo-knowledge-base' ) . ' (E02)' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export AI config', 'echo-knowledge-base' ) . ' (E03)' );
		}

		$ai_config = get_option( EPKB_AI_Config_Specs::OPTION_NAME, array() );
		$training_data_config = get_option( EPKB_AI_Training_Data_Config_Specs::OPTION_NAME, array() );
		$widget_configs = array();
		$widget_ids = isset( $ai_config['ai_chat_widgets'] ) && is_array( $ai_config['ai_chat_widgets'] ) ? $ai_config['ai_chat_widgets'] : array( EPKB_AI_Chat_Widget_Config_Specs::DEFAULT_WIDGET_ID );
		$widget_ids = array_unique( array_filter( array_map( 'absint', $widget_ids ) ) );
		if ( ! in_array( EPKB_AI_Chat_Widget_Config_Specs::DEFAULT_WIDGET_ID, $widget_ids, true ) ) {
			array_unshift( $widget_ids, EPKB_AI_Chat_Widget_Config_Specs::DEFAULT_WIDGET_ID );
		}

		foreach ( $widget_ids as $widget_id ) {
			$widget_configs[ $widget_id ] = get_option(
				EPKB_AI_Chat_Widget_Config_Specs::OPTION_NAME_PREFIX . $widget_id,
				EPKB_AI_Chat_Widget_Config_Specs::get_default_config()
			);
		}

		$kb_collection_mapping = array();
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $kb_id => $kb_config ) {
			$kb_collection_mapping[] = array(
				'kb_id' => absint( $kb_id ),
				'kb_name' => empty( $kb_config['kb_name'] ) ? 'KB ' . $kb_id : $kb_config['kb_name'],
				'kb_ai_collection_id' => empty( $kb_config['kb_ai_collection_id'] ) ? 0 : absint( $kb_config['kb_ai_collection_id'] ),
			);
		}

		$export_data = array(
			'generated_at' => current_time( 'c' ),
			'plugin_version' => Echo_Knowledge_Base::$version,
			'option_names' => array(
				'ai_configuration' => EPKB_AI_Config_Specs::OPTION_NAME,
				'ai_training_data_configuration' => EPKB_AI_Training_Data_Config_Specs::OPTION_NAME,
				'ai_widget_configuration_prefix' => EPKB_AI_Chat_Widget_Config_Specs::OPTION_NAME_PREFIX,
			),
			'ai_configuration' => is_array( $ai_config ) ? $ai_config : array(),
			'ai_training_data_configuration' => is_array( $training_data_config ) ? $training_data_config : array(),
			'ai_widget_configurations' => $widget_configs,
			'kb_collection_mapping' => $kb_collection_mapping,
		);

		nocache_headers();

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="epkb-ai-config-' . gmdate( 'Y-m-d-H-i-s' ) . '.json"' );

		echo wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		die();
	}
}
