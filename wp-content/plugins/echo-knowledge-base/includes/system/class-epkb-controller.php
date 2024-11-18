<?php
/**
 * KB Controller
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Controller {

	public function __construct() {
		add_action( 'wp_ajax_epkb_create_kb_demo_data', array( $this, 'create_kb_demo_data' ) );
		add_action( 'wp_ajax_nopriv_epkb_create_kb_demo_data', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Create demo data for KB
	 */
	public function create_kb_demo_data() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		// retrieve current KB id
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ){
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 420 ) );
		}

		// retrieve current KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );

		// create demo data for the current KB if no categories exist yet
		EPKB_KB_Demo_Data::create_sample_categories_and_articles( $kb_id, $kb_config['kb_main_page_layout'] );

		// we are done here
		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Demo categories and articles have been created. The page will reload.', 'echo-knowledge-base' ) );
	}

	/**
	 * Handle submission of admin error
	 */
	public static function handle_report_admin_error() {
		global $wp_version;

		// die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$active_theme = wp_get_theme();
		$theme_info = $active_theme->get( 'Name' ) . ' ' . $active_theme->get( 'Version' );

		$current_user = EPKB_Utilities::get_current_user();

		$error = EPKB_Utilities::post( 'admin_error' );
		$error = empty($error) ? '[Error details are missing]' : substr( $error, 0, 5000 );

		$editor_type = EPKB_Utilities::post( 'editor_type' );
		if ( ! empty( $editor_type ) ) {
			$error .= PHP_EOL . 'Editor type: ' . $editor_type;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );

		// send feedback
		$api_params = array(
			'epkb_action' => 'epkb_report_error',
			'plugin_name' => EPKB_Utilities::is_amag_on() ? 'Access Manager' : 'EPKB',
			'plugin_version' => class_exists( 'Echo_Knowledge_Base' ) ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version' => $kb_config['first_plugin_version'],
			'wp_version' => $wp_version,
			'theme_info' => $theme_info,
			'email' => empty( $current_user->user_email ) ? '' : $current_user->user_email,
			'first_name' => empty( $current_user->display_name ) ? '' : $current_user->display_name,
			'editor_error' => $error,
			'kb_main_page' => $kb_main_page_url
		);

		// Call the API
		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout' => 15,
				'body' => $api_params,
				'sslverify' => false
			)
		);

		// let user know if it succeeded
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			wp_send_json_success( esc_html__( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ) );
		} else {
			wp_send_json_error( EPKB_Utilities::report_generic_error( 1230, esc_html__( 'Error occurred', 'echo-knowledge-base' ) ) );
		}
	}
}
