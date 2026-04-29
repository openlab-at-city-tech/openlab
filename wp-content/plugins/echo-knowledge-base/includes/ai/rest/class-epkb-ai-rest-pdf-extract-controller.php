<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * REST API Controller for AI-based PDF text extraction.
 * Used by Training Data JS (Feature B) when client-side PDF.js returns empty text.
 */
class EPKB_AI_REST_PDF_Extract_Controller extends EPKB_AI_REST_Base_Controller {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register routes
	 */
	public function register_routes() {

		register_rest_route( $this->admin_namespace, '/pdf-extract-text', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'extract_text' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'pdf_base64' => array(
					'required' => true,
					'type'     => 'string',
				),
				'file_name' => array(
					'required' => true,
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_file_name',
				),
			),
		) );

		register_rest_route( $this->admin_namespace, '/pdf-format-content', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'format_content' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'raw_text' => array(
					'type'     => 'string',
				),
				'pdf_base64' => array(
					'type' => 'string',
				),
				'file_name' => array(
					'type' => 'string',
					'sanitize_callback' => 'sanitize_file_name',
				),
				'format_mode' => array(
					'type'    => 'string',
					'enum'    => array( 'none', 'basic', 'ai' ),
					'default' => 'basic',
				),
			),
		) );
	}

	/**
	 * Extract text from a base64-encoded PDF via AI.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function extract_text( $request ) {

		EPKB_PDF_Utilities::setup_timeout_error_handling( 'rest' );

		$pdf_data = EPKB_PDF_Utilities::decode_base64_pdf(
			$request->get_param( 'pdf_base64' ),
			$request->get_param( 'file_name' )
		);
		if ( is_wp_error( $pdf_data ) ) {
			return $this->create_rest_response( array(), 400, $pdf_data );
		}

		$result = EPKB_AI_PDF_Extractor::extract_text_from_pdf( $pdf_data['binary'], $pdf_data['file_name'] );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array(), 500, $result );
		}

		return $this->create_rest_response( array( 'success' => true, 'text' => $result['text'] ) );
	}

	/**
	 * Format extracted PDF text into display content.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function format_content( $request ) {

		EPKB_PDF_Utilities::setup_timeout_error_handling( 'rest' );

		$format_mode = $request->get_param( 'format_mode' );
		if ( $format_mode === 'ai' ) {
			$pdf_data = EPKB_PDF_Utilities::decode_base64_pdf(
				$request->get_param( 'pdf_base64' ),
				$request->get_param( 'file_name' ) ?: 'document.pdf'
			);
			if ( is_wp_error( $pdf_data ) ) {
				return $this->create_rest_response( array(), 400, $pdf_data );
			}

			$content = EPKB_PDF_Utilities::format_pdf_for_display( $pdf_data['binary'], $pdf_data['file_name'], 'ai' );
		} else {
			$raw_text = $request->get_param( 'raw_text' );
			$content = EPKB_PDF_Utilities::format_text_for_display( $raw_text, $format_mode );
		}

		if ( is_wp_error( $content ) ) {
			return $this->create_rest_response( array(), 400, $content );
		}

		$response = array(
			'success' => true,
			'content' => $content,
		);

		$ai_debug = EPKB_PDF_Utilities::get_last_ai_debug();
		if ( $ai_debug ) {
			$response['ai_debug'] = $ai_debug;
		}

		return $this->create_rest_response( $response );
	}

	/**
	 * Check admin permission
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_admin_permission( $request ) {

		$nonce_check = EPKB_AI_Security::check_rest_nonce( $request );
		if ( is_wp_error( $nonce_check ) ) {
			return $nonce_check;
		}

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission.', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}

		return true;
	}
}
