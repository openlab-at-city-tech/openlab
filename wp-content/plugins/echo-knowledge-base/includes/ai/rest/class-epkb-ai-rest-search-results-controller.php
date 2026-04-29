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
		if ( ! EPKB_AI_Utilities::is_ai_search_smart_enabled() ) {
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
		$kb_id = absint( $request->get_param( 'kb_id' ) );
		$collection_id = absint( $request->get_param( 'collection_id' ) );

		if ( empty( $kb_id ) ) {
			return $this->create_rest_response( array( 'section_id' => $section_id ), 400, new WP_Error( 'missing_kb_id', __( 'Knowledge Base ID is required', 'echo-knowledge-base' ) ) );
		}

		// Validate collection is configured (not 0)
		if ( empty( $collection_id ) ) {
			return $this->create_rest_response( array(
				'section_id' => $section_id,
				'has_content' => false,
				'error' => __( 'No AI data collection selected. Please select a collection in AI Settings.', 'echo-knowledge-base' ),
				'status' => 'error',
			), 400 );
		}

		$collection_access = $this->validate_public_collection_request( $request, $kb_id, $collection_id );
		if ( is_wp_error( $collection_access ) ) {
			return $this->create_rest_response( array( 'section_id' => $section_id ), 403, $collection_access );
		}

		/* $rate_limit_check = EPKB_AI_Security::check_search_rate_limit();
		if ( is_wp_error( $rate_limit_check ) ) {
			return $this->create_rest_response( array( 'section_id' => $section_id ), 429, $rate_limit_check );
		} */

		// Validate collection exists and has vector store - check BEFORE processing to fail fast
		$config_error = $this->get_collection_configuration_error( $collection_id );
		if ( $config_error !== null ) {
			return $this->create_rest_response( array(
				'section_id' => $section_id,
				'has_content' => false,
				'error' => $config_error['message'],
				'error_type' => $config_error['type'],
				'status' => 'error',
			), 400 );
		}

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
			define( 'ECKB_AI_USE_DEMO_DATA', false );   // for testing only
		if ( defined( 'EPCB_AI_USE_DEMO_DATA' ) && ECKB_AI_USE_DEMO_DATA && in_array( $section_id, ['ai-answer','matching-articles', 'contact-us', 'feedback'] ) ) {
			$section_data = EPKB_KB_Demo_Data::get_ai_search_results_section_demo_data( $section_id, $data );
		} else {
			// This allows ai-features-pro or other plugins to provide section content
			$section_data = apply_filters( 'epkb_ai_search_results_get_section', null, $section_id, $data );
		}

		// Validate response
		if ( empty( $section_data ) || ! is_array( $section_data ) ) {
			EPKB_AI_Log::add_log( 'No section handler found for: ' . $section_id, 'Available filters not registered' );
			return $this->create_rest_response( array( 'section_id' => $section_id ), 500, new WP_Error( 'no_handler', __( 'No handler registered for this section', 'echo-knowledge-base' ) ) );
		}

		// Validate section data structure
		if ( ! isset( $section_data['has_content'] ) ) {
			EPKB_AI_Log::add_log( 'Invalid section data structure for: ' . $section_id, print_r( $section_data, true ) );
			return $this->create_rest_response( array( 'section_id' => $section_id ), 500, new WP_Error( 'invalid_data', __( 'Section handler returned invalid data', 'echo-knowledge-base' ) ) );
		}

		// Build response data
		$response_data = array(
			'section_id' => $section_id,
			'has_content' => ! empty( $section_data['has_content'] ),
			'html' => isset( $section_data['html'] ) ? $section_data['html'] : '',
			'data' => isset( $section_data['data'] ) ? $section_data['data'] : array(),
			'title' => isset( $section_data['title'] ) ? $section_data['title'] : '',
		);

		// Include error message if present (e.g., provider mismatch, no vector store)
		if ( ! empty( $section_data['error'] ) ) {
			$response_data['error'] = $section_data['error'];
		}
		if ( ! empty( $section_data['error_type'] ) ) {
			$response_data['error_type'] = $section_data['error_type'];
		}
		if ( ! empty( $section_data['display_as_error'] ) ) {
			$response_data['display_as_error'] = true;
		}

		return $this->create_rest_response( $response_data );
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
				'collection_token' => array(
					'required'          => false,
					'type'              => 'string',
					'description'       => __( 'Access token for collection overrides', 'echo-knowledge-base' ),
					'sanitize_callback' => 'sanitize_text_field',
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

		/* $rate_limit_check = EPKB_AI_Security::check_rate_limit();
		if ( is_wp_error( $rate_limit_check ) ) {
			return $this->create_rest_response( array(), 429, $rate_limit_check );
		} */

		$chat_id = $request->get_param( 'chat_id' );
		$vote = sanitize_text_field( $request->get_param( 'vote' ) );
		$db = new EPKB_AI_Messages_DB();

		// Get conversation from database
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

		/* $rate_limit_check = EPKB_AI_Security::check_rate_limit();
		if ( is_wp_error( $rate_limit_check ) ) {
			return $this->create_rest_response( array(), 429, $rate_limit_check );
		} */

		$query = $request->get_param( 'query' );
		$name = sanitize_text_field( $request->get_param( 'name' ) );
		$email = $request->get_param( 'email' );
		$chat_id = $request->get_param( 'chat_id' );
		$kb_id = absint( $request->get_param( 'kb_id' ) );
		$collection_id = absint( $request->get_param( 'collection_id' ) );

		$collection_access = $this->validate_public_collection_request( $request, $kb_id, $collection_id );
		if ( is_wp_error( $collection_access ) ) {
			return $this->create_rest_response( array(), 403, $collection_access );
		}

		// Get destination email from config
		$destination_email = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_contact_support_email' );

		// Send email only if destination email is configured
		$email_sent = true;
		if ( ! empty( $destination_email ) ) {
			$subject = __( 'Search Results Submission', 'echo-knowledge-base' );
			// translators: %s is the submitter's name
			$message = sprintf( __( 'Name: %s', 'echo-knowledge-base' ), $name ) . "\n\n";
			// translators: %s is the submitter's email address
			$message .= sprintf( __( 'Email: %s', 'echo-knowledge-base' ), $email ) . "\n\n";
			// translators: %s is the search query
			$message .= sprintf( __( 'Search Query: %s', 'echo-knowledge-base' ), $query );

			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
			$email_sent = wp_mail( $destination_email, $subject, $message, $headers );

			if ( ! $email_sent ) {
				EPKB_AI_Log::add_log( 'Contact support email failed', $destination_email );
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
					'required'          => true,
					'type'              => 'integer',
					'description'       => __( 'AI Training Data Collection ID', 'echo-knowledge-base' ),
					'validate_callback' => function( $param ) {
						return is_numeric( $param ) && $param > 0;
					},
					'sanitize_callback' => 'absint',
				),
				'collection_token' => array(
					'required'          => false,
					'type'              => 'string',
					'description'       => __( 'Access token for collection overrides', 'echo-knowledge-base' ),
					'sanitize_callback' => 'sanitize_text_field',
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
	 * TODO: Rename to check_public_permission() - this is a public endpoint that does not check admin capability
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

		if ( ! EPKB_AI_Utilities::is_ai_search_smart_enabled() ) {
			return new WP_Error( 'ai_search_results_disabled', __( 'AI Search Results feature is not enabled', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Check if collection configuration is valid
	 *
	 * @param int $collection_id
	 * @return array|null Error info or null if no error
	 */
	private function get_collection_configuration_error( $collection_id ) {

		$is_admin = EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' );

		// Check for provider mismatch first
		$provider_mismatch = EPKB_AI_Training_Data_Config_Specs::get_active_and_selected_provider_if_mismatched( $collection_id );
		if ( $provider_mismatch !== null ) {
			return array(
				'type'    => 'provider_mismatch',
				// translators: %1$s is the collection provider, %2$s is the active provider
				'message' => $is_admin
					? sprintf(
						__( 'AI Search unavailable: Collection uses %1$s but active provider is %2$s.', 'echo-knowledge-base' ),
						$provider_mismatch['collection_provider'],
						$provider_mismatch['active_provider']
					)
					: __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
			);
		}

		// Check for collection/vector store issues
		$vector_store_result = EPKB_AI_Training_Data_Config_Specs::get_vector_store_id_by_collection( $collection_id );
		if ( is_wp_error( $vector_store_result ) ) {
			$error_code = $vector_store_result->get_error_code();

			// Provide helpful messages for specific error types
			if ( $error_code === 'collection_not_found' ) {
				return array(
					'type'    => $error_code,
					'message' => $is_admin
						? __( 'The selected AI data collection no longer exists. Please select a different collection in KB Configuration.', 'echo-knowledge-base' )
						: __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
				);
			}

			if ( $error_code === 'no_vector_store' ) {
				return array(
					'type'    => $error_code,
					'message' => $is_admin
						? __( 'The selected collection has not been synced yet. Please sync it in the Training Data settings.', 'echo-knowledge-base' )
						: __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
				);
			}

			// Fallback for other errors
			return array(
				'type'    => $error_code,
				'message' => $is_admin ? $vector_store_result->get_error_message() : __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
			);
		}

		return null;
	}
}
