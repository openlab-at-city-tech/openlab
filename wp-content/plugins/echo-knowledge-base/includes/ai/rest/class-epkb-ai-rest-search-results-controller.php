<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * REST API controller for AI Search Results sections
 * Handles all section endpoints and integrates with ai-features-pro via hooks
 *
 * @copyright   Copyright (C) 2024, Echo Plugins
 */
class EPKB_AI_REST_Search_Results_Controller extends EPKB_AI_REST_Base_Controller {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register REST routes for all sections
	 */
	public function register_routes() {

		// Only register routes if AI Search Results is enabled
		if ( ! EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			return;
		}

		$route_args = array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_section_content' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => $this->get_section_params(),
			)
		);

		// Explicitly register each section route
		register_rest_route( $this->public_namespace, '/ai-search-results/matching-articles', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/ai-answer', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/contact-us', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/glossary-terms', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/tips', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/steps', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/tasks-list', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/you-can-also-ask', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/related-keywords', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/custom-prompt', $route_args );
		register_rest_route( $this->public_namespace, '/ai-search-results/feedback', $route_args );

		// Register feedback recording endpoint
		register_rest_route( $this->public_namespace, '/ai-search-results/record-feedback', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'record_feedback' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => $this->get_feedback_params(),
			)
		) );

		// Register contact support submission endpoint
		register_rest_route( $this->public_namespace, '/ai-search-results/submit-contact-support', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'submit_contact_support' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => $this->get_contact_support_params(),
			)
		) );
	}

	/**
	 * Get section content
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_section_content( $request ) {

		// Extract section ID from route
		$route = $request->get_route();
		$section_id = str_replace( '/' . $this->public_namespace . '/ai-search-results/', '', $route );

		// Get parameters
		$query = $request->get_param( 'query' );
		$kb_id = $request->get_param( 'kb_id' );
		$collection_id = $request->get_param( 'collection_id' );
		$collection_id = empty( $collection_id ) ? EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID : $collection_id;

		// Validate query length
		if ( strlen( $query ) < 2 ) {
			return $this->create_rest_response( array( 'section_id' => $section_id ), 400, new WP_Error( 'query_too_short', __( 'Query must be at least 2 characters long', 'echo-knowledge-base' ) ) );
		}

		// Check if AI Features Pro is enabled
		if ( ! EPKB_AI_Utilities::is_ai_features_pro_enabled() ) {
			return $this->create_rest_response( array( 'section_id' => $section_id ), 503, new WP_Error( 'plugin_not_enabled', __( 'AI Features Pro plugin is not enabled', 'echo-knowledge-base' ) ) );
		}

		// Collection ID should always come from client
		$data = array(
			'query' => $query,
			'kb_id' => $kb_id,
			'collection_id' => $collection_id,
		);

		// Get section data via filter hook or demo data
		define( 'ECKB_AI_USE_DEMO_DATA', false );
		if ( defined( 'EPCB_AI_USE_DEMO_DATA' ) && ECKB_AI_USE_DEMO_DATA && in_array( $section_id, ['ai-answer','matching-articles', 'contact-us', 'feedback'] ) ) {
			$section_data = EPKB_KB_Demo_Data::get_ai_search_results_section_demo_data( $section_id, $data );
		} else {
			// This allows ai-features-pro or other plugins to provide section content
			$section_data = apply_filters( 'epkb_ai_search_results_get_section', null, $section_id, $data );
		}

		// Validate response
		if ( empty( $section_data ) || ! is_array( $section_data ) ) {
			EPKB_Logging::add_log( 'No section handler found for: ' . $section_id, 'Available filters not registered' );
			return $this->create_rest_response( array( 'section_id' => $section_id ), 500, new WP_Error( 'no_handler', __( 'No handler registered for this section', 'echo-knowledge-base' ) ) );
		}

		// Validate section data structure
		if ( ! isset( $section_data['has_content'] ) ) {
			EPKB_Logging::add_log( 'Invalid section data structure for: ' . $section_id, print_r( $section_data, true ) );
			return $this->create_rest_response( array( 'section_id' => $section_id ), 500, new WP_Error( 'invalid_data', __( 'Section handler returned invalid data', 'echo-knowledge-base' ) ) );
		}

		// Return success response
		return $this->create_rest_response( array(
			'section_id' => $section_id,
			'has_content' => ! empty( $section_data['has_content'] ),
			'html' => isset( $section_data['html'] ) ? $section_data['html'] : '',
			'data' => isset( $section_data['data'] ) ? $section_data['data'] : array(),
			'title' => isset( $section_data['title'] ) ? $section_data['title'] : '',
		) );
	}

	/**
	 * Get schema for section parameters
	 *
	 * @return array
	 */
	protected function get_section_params() {
		return array(
			'query' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'Search query', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_string( $param ) && strlen( trim( $param ) ) >= 2;
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
			'kb_id' => array(
				'required'          => true,
				'type'              => 'integer',
				'description'       => __( 'Knowledge Base ID', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_numeric( $param ) && $param > 0;
				},
				'sanitize_callback' => 'absint',
			),
			'collection_id' => array(
				'required'          => false,
				'type'              => 'integer',
				'description'       => __( 'AI Training Data Collection ID (optional, overrides KB default)', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_numeric( $param ) && $param > 0;
				},
				'sanitize_callback' => 'absint',
			),
		);
	}

	/**
	 * Record feedback vote
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function record_feedback( $request ) {

		$chat_id = $request->get_param( 'chat_id' );
		$vote = $request->get_param( 'vote' );

		// Get conversation from database
		$db = new EPKB_AI_Messages_DB();
		$conversation = $db->get_conversation_by_chat_id( $chat_id );
		if ( empty( $conversation ) ) {
			return $this->create_rest_response( array(), 404, new WP_Error( 'not_found', __( 'Conversation not found', 'echo-knowledge-base' ) ) );
		}

		// Update metadata with vote (merge handled in update_metadata method)
		$metadata = array( 'vote' => $vote );
		$result = $db->update_metadata( $conversation->get_id(), $metadata );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array(), 500, $result );
		}

		return $this->create_rest_response( array( 'success' => true, 'vote' => $vote ) );
	}
	
	/**
	 * Get schema for feedback parameters
	 *
	 * @return array
	 */
	protected function get_feedback_params() {
		return array(
			'chat_id' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'Chat ID for tracking', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_string( $param ) && ! empty( trim( $param ) );
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
			'vote' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'User vote (up or down)', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return in_array( $param, array( 'up', 'down' ), true );
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Submit contact support request
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function submit_contact_support( $request ) {

		$query = $request->get_param( 'query' );
		$name = $request->get_param( 'name' );
		$email = $request->get_param( 'email' );
		$chat_id = $request->get_param( 'chat_id' );

		// Get destination email from config
		$destination_email = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_contact_support_email' );

		// Send email only if destination email is configured
		$email_sent = true;
		if ( ! empty( $destination_email ) ) {
			$subject = __( 'Search Results Submission', 'echo-knowledge-base' );
			$message = sprintf( __( 'Name: %s', 'echo-knowledge-base' ), $name ) . "\n\n";
			$message .= sprintf( __( 'Email: %s', 'echo-knowledge-base' ), $email ) . "\n\n";
			$message .= sprintf( __( 'Search Query: %s', 'echo-knowledge-base' ), $query );

			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
			$email_sent = wp_mail( $destination_email, $subject, $message, $headers );

			if ( ! $email_sent ) {
				EPKB_Logging::add_log( 'Contact support email failed', $destination_email );
				return $this->create_rest_response( array(), 500, new WP_Error( 'email_failed', __( 'Failed to send email', 'echo-knowledge-base' ) ) );
			}
		}

		// Update database entry with submission data if chat_id provided
		if ( ! empty( $chat_id ) ) {
			$db = new EPKB_AI_Messages_DB();
			$conversation = $db->get_conversation_by_chat_id( $chat_id );
			if ( ! empty( $conversation ) ) {
				// Update metadata with contact submission (merge handled in update_metadata method)
				$metadata = array(
					'contact_submission' => array(
						'name' => $name,
						'email' => $email,
						'query' => $query,
						'submitted_at' => gmdate( 'Y-m-d H:i:s' )
					)
				);
				$db->update_metadata( $conversation->get_id(), $metadata );
			}
		}

		return $this->create_rest_response( array( 'success' => true ) );
	}

	/**
	 * Get schema for contact support parameters
	 *
	 * @return array
	 */
	protected function get_contact_support_params() {
		return array(
			'query' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'Search query', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_string( $param ) && ! empty( trim( $param ) );
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
			'name' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'User name', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_string( $param ) && ! empty( trim( $param ) );
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
			'email' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'User email', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_email( $param );
				},
				'sanitize_callback' => 'sanitize_email',
			),
			'chat_id' => array(
				'required'          => false,
				'type'              => 'string',
				'description'       => __( 'Chat ID for tracking', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_string( $param );
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Check REST nonce and feature enabled
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_admin_permission( $request ) {

		// Check nonce
		$nonce_check = EPKB_AI_Security::check_rest_nonce( $request );
		if ( is_wp_error( $nonce_check ) ) {
			return $nonce_check;
		}
		
		if ( ! EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			return new WP_Error( 'ai_search_results_disabled', __( 'AI Search Results feature is not enabled', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}

		return true;
	}
}
