<?php defined( 'ABSPATH' ) || exit();

/**
 * REST API Controller for AI Admin functions
 */
class EPKB_AI_REST_Admin_Controller extends EPKB_AI_REST_Base_Controller {

	/**
	 * Register routes
	 */
	public function register_routes() {

		// Save AI settings
		register_rest_route( $this->admin_namespace, '/ai/settings', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_settings' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			)
		) );

		// Load AI tab data on demand (currently used for dashboard)
		register_rest_route( $this->admin_namespace, '/ai/(?P<tab>[a-z0-9\\-]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_tab_data' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'tab' => array(
						'description'       => __( 'AI admin tab key.', 'echo-knowledge-base' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		) );
	}

	/**
	 * Check admin permission
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

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission.', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Get AI tab data
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_tab_data( $request ) {

		$tab_key = sanitize_key( $request->get_param( 'tab' ) );

		if ( $tab_key !== 'dashboard' ) {
			return $this->create_rest_response( array(
				'success' => false,
				'error' => 'invalid_tab',
				'message' => __( 'This tab is not available.', 'echo-knowledge-base' )
			), 400 );
		}

		$tab_data = EPKB_AI_Dashboard_Tab::get_tab_config();

		return $this->create_rest_response( array(
			'success' => true,
			'data' => $tab_data,
		) );
	}

	/**
	 * Save AI settings
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function save_settings( $request ) {

		$settings = $request->get_param( 'settings' );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return $this->create_rest_response( array( 'error' => 'invalid_input', 'message' => __( 'Invalid settings data provided.', 'echo-knowledge-base' ) ), 400 );
		}

		// Extract widget settings if present
		$widget_settings = $request->get_param( 'widget_settings' );
		$widget_id = $request->get_param( 'widget_id' );

		// Get tab context for validation (optional parameter: 'chat', 'search', etc.)
		$tab_context = $request->get_param( 'tab_context' );

		// Prepare and sanitize settings based on specifications
		$specs = EPKB_AI_Config_Specs::get_config_fields_specifications();

		$new_config = array();
		foreach ( $settings as $field_name => $field_value ) {
			// Check if field exists in specs
			if ( ! isset( $specs[$field_name] ) ) {
				continue;
			}

			// Get field spec and populate dynamic options if needed
			$field_spec = $specs[$field_name];
			if ( $field_spec['type'] === EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT && empty( $field_spec['options'] ) ) {
				$field_spec['options'] = EPKB_AI_Config_Specs::get_field_options( $field_name );
			}

			// Validate and sanitize based on field type
			$new_config[$field_name] = EPKB_AI_Config_Base::sanitize_field_value( $field_value, $field_spec );
		}

		$provider = isset( $new_config['ai_provider'] ) ? $new_config['ai_provider'] : EPKB_AI_Provider::get_active_provider();
		$selected_provider = EPKB_AI_Provider::normalize_provider( $provider );

		// Hidden inputs from the inactive provider can contain stale browser or password-manager values.
		// Drop them so partial updates preserve the saved inactive-provider settings.
		if ( $selected_provider === EPKB_AI_Provider::PROVIDER_GEMINI ) {
			unset( $new_config['ai_chatgpt_key'], $new_config['ai_organization_id'] );
		} else {
			unset( $new_config['ai_gemini_key'] );
		}

		// Handle provider-specific API keys - process both fields
		$api_key_fields = array(
			'ai_chatgpt_key' => EPKB_AI_Provider::PROVIDER_CHATGPT,
			'ai_gemini_key' => EPKB_AI_Provider::PROVIDER_GEMINI
		);

		$key_changed_needs_resync = false;

		foreach ( $api_key_fields as $key_field => $key_provider ) {
			if ( ! empty( $new_config[$key_field] ) ) {
				// If the value is our placeholder, preserve the existing encrypted key from database
				if ( $new_config[$key_field] == '********' ) {
					$new_config[$key_field] = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( $key_provider );
				} else {
					// First validate the format for this provider
					if ( ! EPKB_AI_Validation::validate_api_key_format( $new_config[$key_field], $key_provider ) ) {
						return $this->create_rest_response( array(
							'error' => 'invalid_api_key_format',
							'message' => __( 'Invalid API key format for', 'echo-knowledge-base' ) . ' ' . EPKB_AI_Provider::get_provider_label( $key_provider )
						), 400 );
					}

					// Test the API key against the AI provider before saving
					$encrypted_test_key = EPKB_Utilities::encrypt_data( $new_config[$key_field] );
					$old_key = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( $key_provider );

					// Temporarily set the new key to test it
					EPKB_AI_Config_Specs::update_ai_config_value( $key_field, $encrypted_test_key );

					$client = EPKB_AI_Provider::get_client( $key_provider );
					$test_result = $client->test_connection();

					// Restore old key if test fails
					if ( is_wp_error( $test_result ) ) {
						EPKB_AI_Config_Specs::update_ai_config_value( $key_field, $old_key );
						return $this->create_rest_response( array( 'error' => $test_result->get_error_code(), 'message' => $test_result->get_error_message() ), 400 );
					}

					$new_config[$key_field] = $encrypted_test_key;

					// Detect key change for the selected provider — existing training data will need re-sync
					if ( $old_key !== $encrypted_test_key && $key_provider === $selected_provider ) {
						$collection_ids = EPKB_AI_Training_Data_Config_Specs::get_collection_ids_by_provider( $key_provider );
						if ( ! empty( $collection_ids ) && EPKB_AI_Training_Data_DB::count_synced_data( $collection_ids ) > 0 ) {
							$key_changed_needs_resync = true;
							EPKB_AI_Log::add_log( 'API key changed for provider ' . EPKB_AI_Provider::get_provider_label( $key_provider ) . ', existing training data needs to be re-synced' );
						}
					}
				}
			}
		}

		// Validate AI Chat collection IDs if saving from AI Chat tab
		// Note: AI Search collection validation is handled in KB settings controller
		if ( $tab_context === 'chat' ) {
			$chat_collection_fields = array(
				'ai_chat_display_collection',
				'ai_chat_display_collection_2',
				'ai_chat_display_collection_3',
				'ai_chat_display_collection_4',
				'ai_chat_display_collection_5'
			);

			foreach ( $chat_collection_fields as $field ) {
				if ( ! empty( $new_config[$field] ) ) {
					$collection_id = absint( $new_config[$field] );
					$validation_error = EPKB_AI_Validation::validate_collection_has_vector_store( $collection_id, 'chat' );
					if ( is_wp_error( $validation_error ) ) {
						return $this->create_rest_response( array(
							'success' => false,
							'error' => $validation_error->get_error_code(),
							'message' => $validation_error->get_error_message()
						), 400 );
					}
				}
			}
		}

		// Update only the provided fields (partial update)
		$orig_config = EPKB_AI_Config_Specs::get_ai_config();
		$result = EPKB_AI_Config_Specs::update_ai_config( $orig_config, $new_config );
		if ( is_wp_error( $result ) ) {
			$status_code = $result->get_error_code() == 'validation_failed' ? 400 : 500;
			// For settings save errors, use simple error message format without technical details
			return $this->create_rest_response( array(
				'success' => false,
				'error' => $result->get_error_code(),
				// translators: %s is the error message
				'message' => sprintf( __( 'Error saving settings: %s', 'echo-knowledge-base' ), $result->get_error_message() )
			), $status_code );
		}


		// Handle widget settings if provided
		$widget_config = null;
		if ( ! empty( $widget_settings ) && is_array( $widget_settings ) ) {
			$widget_id = empty( $widget_id ) ? EPKB_AI_Chat_Widget_Config_Specs::DEFAULT_WIDGET_ID : absint( $widget_id );

			// Update widget configuration
			$widget_result = EPKB_AI_Chat_Widget_Config_Specs::update_widget_config( $widget_id, $widget_settings );
			if ( is_wp_error( $widget_result ) ) {
				$error_code = $widget_result->get_error_code();
				$status_code = $error_code == 'validation_failed' ? 400 : 500;

				return $this->create_rest_response( array(
					'success' => false,
					'error' => $error_code,
					// translators: %s is the error message
					'message' => sprintf( __( 'Error saving settings: %s', 'echo-knowledge-base' ), $widget_result->get_error_message() )
				), $status_code );
			}

			$widget_config = EPKB_AI_Chat_Widget_Config_Specs::get_widget_config( $widget_id );
		}

		// Handle KB collection mappings if provided
		if ( ! empty( $settings['kb_collection_mappings'] ) && is_array( $settings['kb_collection_mappings'] ) ) {
			$kb_config_db = new EPKB_KB_Config_DB();

			foreach ( $settings['kb_collection_mappings'] as $kb_id => $collection_id ) {
				$kb_id = absint( $kb_id );
				$collection_id = absint( $collection_id );

				if ( $kb_id === 0 ) {
					continue;
				}

				// Get current KB config
				$kb_config = $kb_config_db->get_kb_config( $kb_id );
				if ( is_wp_error( $kb_config ) ) {
					continue;
				}

				// Only update if the collection ID has changed
				$current_collection_id = isset( $kb_config['kb_ai_collection_id'] ) ? absint( $kb_config['kb_ai_collection_id'] ) : 0;
				if ( $current_collection_id !== $collection_id ) {
					$kb_config_db->set_value( $kb_id, 'kb_ai_collection_id', $collection_id );
				}
			}
		}

		$updated_ai_config = EPKB_AI_Config_Specs::get_ai_config();

		$response_data = array(
			'success' => true,
			'message' => __( 'Settings saved successfully.', 'echo-knowledge-base' ),
			'settings' => $updated_ai_config,
			'ai_config' => $updated_ai_config,
		);
		
		if ( $widget_config !== null ) {
			$response_data['widget_config'] = $widget_config;
		}

		if ( $key_changed_needs_resync ) {
			$response_data['key_changed_warning'] = __( 'Your API key has changed. Please re-sync your training data so AI Chat and AI Search can access the new data store.', 'echo-knowledge-base' );
		}

		return $this->create_rest_response( $response_data );
	}
}
