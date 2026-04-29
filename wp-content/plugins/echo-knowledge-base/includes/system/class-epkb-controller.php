<?php if ( ! defined( 'ABSPATH' ) ) exit;
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
}
