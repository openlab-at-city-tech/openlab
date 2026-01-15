<?php defined( 'ABSPATH' ) || exit();

/**
 * Base REST API Controller for AI functionality
 * Provides common functionality for all AI REST endpoints
 * 
 * Currently provides:
 * - Common REST response creation with token refresh
 * - Error handling utilities
 * - Collection parameter schemas
 * 
 * Future considerations:
 * - If multiple AI services need sessions, consider creating a shared session endpoint here
 * - Common authentication/authorization logic could be added here
 * - Shared rate limiting logic could be centralized here
 */
abstract class EPKB_AI_REST_Base_Controller extends WP_REST_Controller {

	protected $admin_namespace = 'epkb-admin/v1';
	protected $public_namespace = 'epkb-public/v1';
	
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes') );
	}

	/**
	 * Helper method to create REST responses with automatic token refresh
	 *
	 * @param array $data The response data
	 * @param int $http_status_code HTTP status code
	 * @param WP_Error|null $wp_error Optional WP_Error object to process
	 * @return WP_REST_Response
	 */
	protected function create_rest_response( $data, $http_status_code=200, $wp_error=null ) {

		// If WP_Error is provided, process it and merge into data
		if ( $wp_error instanceof WP_Error ) {
			$error_result = EPKB_AI_Log::rest_process_wp_error( $wp_error, $http_status_code );
			$data = array_merge( $error_result['data'], $data );
			$http_status_code = $error_result['status'];

		} elseif ( isset( $data['error'] ) && ! isset( $data['status'] ) ) {

			// If error is provided in data but status is not, add it
			$data['status'] = 'error';
			
			EPKB_AI_Log::add_log( __( 'Error is provided in data but status is not, adding it', 'echo-knowledge-base' ), $data );

			// Use status mapping if status is still 200
			if ( $http_status_code === 200 ) {
				$http_status_code = EPKB_AI_Log::get_error_status_code( $data['error'] );
			}
		}

		// Always check if we need to provide a new nonce
		$current_nonce = epkb_get_instance()->security_obj->get_nonce();
		$request_nonce = isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? $_SERVER['HTTP_X_WP_NONCE'] : null;

		// Check if nonce is approaching expiration (WordPress nonces last 12-24 hours)
		// We'll refresh if the nonce is older than 10 hours to be safe
		$should_refresh = false;
		if ( $request_nonce ) {
			// Verify if the nonce is still valid but getting old
			$verify = wp_verify_nonce( $request_nonce, 'wp_rest' );
			if ( $verify === 2 ) {
				// Nonce is valid but was generated 12-24 hours ago
				$should_refresh = true;
			}
		}

		// If the nonce has changed or should be refreshed, include the new one
		if ( $should_refresh || ( $request_nonce && $current_nonce !== $request_nonce ) ) {
			$data['new_token'] = $current_nonce;
		}

		return new WP_REST_Response( $data, $http_status_code );
	}

	/**
	 * Get standard collection parameters for list endpoints
	 * 
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'page' => array(
				'description'       => __( 'Current page of the collection.', 'echo-knowledge-base' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'echo-knowledge-base' ),
				'type'              => 'integer',
				'default'           => 10,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
				'maximum'           => 100,
			),
			'search' => array(
				'description'       => __( 'Limit results to those matching a string.', 'echo-knowledge-base' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'orderby' => array(
				'description'       => __( 'Sort collection by object attribute.', 'echo-knowledge-base' ),
				'type'              => 'string',
				'default'           => 'created',
				'enum'              => array( 'id', 'created', 'modified' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'order' => array(
				'description'       => __( 'Order sort attribute ascending or descending.', 'echo-knowledge-base' ),
				'type'              => 'string',
				'default'           => 'desc',
				'enum'              => array( 'asc', 'desc' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}