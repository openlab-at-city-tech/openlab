<?php

/**
 * Base AI Handler
 * 
 * Abstract base class providing common functionality for AI handlers
 */
abstract class EPKB_AI_Base_Handler {
	
	/**
	 * OpenAI client
	 * @var EPKB_OpenAI_Client
	 */
	private $ai_client;
		
	/**
	 * Messages database
	 * @var EPKB_AI_Messages_DB
	 */
	protected $messages_db;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->ai_client = new EPKB_OpenAI_Client();
		$this->messages_db = new EPKB_AI_Messages_DB();
	}

	/**
	 * Call AI API with messages
	 *
	 * @param String $message
	 * @param String $model
	 * @param null $previous_response_id
	 * @param int|null $collection_id Training data collection ID to use for search
	 * @return array|WP_Error AI response or error
	 */
	protected function get_ai_response( $message, $model, $previous_response_id , $collection_id ) {

		// Build request for Responses API
		$request = array(
			'model'             => $model,
			'instructions'      => $this->get_instructions(),
			'input'             => array(
				array(
					'role'    => 'user',
					'content' => $message
				)
			),
		);

		// Get context-specific parameters and apply them
		$params = $this->get_model_parameters( $model );
		$request = EPKB_OpenAI_Client::apply_model_parameters( $request, $model, $params );

		// Add previous response ID for continuing conversation
		if ( ! empty( $previous_response_id ) ) {
			$request['previous_response_id'] = $previous_response_id;
		}

		// Add file search tool if vector store is available
		$vector_store_id = EPKB_AI_Training_Data_Config_Specs::get_vector_store_id_by_collection( $collection_id );
		if ( empty( $vector_store_id ) ) {
			return new WP_Error( 'invalid_vector_store_id', 'Invalid Vector Store ID' );
		}

		$request['tools'] = array(
			array(
				'type' => 'file_search',
				'vector_store_ids' => array( $vector_store_id ),
				'max_num_results' => EPKB_OpenAI_Client::DEFAULT_MAX_NUM_RESULTS
			)
		);

		// Make API call to Responses endpoint
		$response = $this->ai_client->request( '/responses', $request );
		if ( is_wp_error( $response ) ) {
			// Check if it's an authentication error
			if ( $response->get_error_code() === 'authentication_failed' ) {
				return new WP_Error(  'authentication_failed', __( 'AI service authentication failed. Please check your API key in the AI settings.', 'echo-knowledge-base' )
				);
			}
			return $response;
		}

		// Extract content from response
		$content = $this->extract_response_content( $response );
		if ( empty( $content ) ) {
			return new WP_Error( 'empty_response', __( 'Received empty response from AI', 'echo-knowledge-base' ) );
		}

		return array(
			'content' => $content,
			'response_id' => isset( $response['id'] ) ? $response['id'] : '',
			'usage' => isset( $response['usage'] ) ? $response['usage'] : array()
		);
	}

	/**
	 * Get model parameters based on context (chat or search)
	 *
	 * @param string $model The model being used
	 * @return array Parameters array with temperature, max_output_tokens, etc.
	 */
	protected function get_model_parameters( $model ) {
		
		// Determine context - search or chat
		$is_search = $this instanceof EPKB_AI_Search_Handler;
		$prefix = $is_search ? 'ai_search_' : 'ai_chat_';
		
		// Get model specifications to determine which parameters are applicable
		$model_spec = EPKB_OpenAI_Client::get_models_and_default_params( $model );
		
		$params = array();
		
		// Add max_output_tokens
		$max_output_tokens_key = $prefix . 'max_output_tokens';
		$max_output_tokens = EPKB_AI_Config_Specs::get_ai_config_value( $max_output_tokens_key );
		if ( ! empty( $max_output_tokens ) ) {
			$max_output_tokens = intval( $max_output_tokens );
			// Validate against model limits
			$max_limit = isset( $model_spec['max_output_tokens_limit'] ) ? $model_spec['max_output_tokens_limit'] : 16384;
			if ( $max_output_tokens > 0 && $max_output_tokens <= $max_limit ) {
				// Use appropriate key based on model requirements
				$params['max_output_tokens'] = $max_output_tokens;
			}
		}
		
		// Add temperature for models that support it
		if ( $model_spec['supports_temperature'] ) {
			$temperature_key = $prefix . 'temperature';
			$temperature = EPKB_AI_Config_Specs::get_ai_config_value( $temperature_key );
			if ( $temperature !== null ) {
				$temperature = floatval( $temperature );
				if ( $temperature >= 0.0 && $temperature <= 2.0 ) {
					$params['temperature'] = $temperature;
				}
			}
		}
		
		// Add top_p for models that support it (alternative to temperature)
		if ( $model_spec['supports_top_p'] && ! isset( $params['temperature'] ) ) {
			$top_p_key = $prefix . 'top_p';
			$top_p = EPKB_AI_Config_Specs::get_ai_config_value( $top_p_key );
			if ( $top_p !== null ) {
				$top_p = floatval( $top_p );
				if ( $top_p >= 0.0 && $top_p <= 1.0 ) {
					$params['top_p'] = $top_p;
				}
			}
		}
		
		// Add verbosity for models that support it
		if ( $model_spec['supports_verbosity'] ) {
			$verbosity_key = $prefix . 'verbosity';
			$verbosity = EPKB_AI_Config_Specs::get_ai_config_value( $verbosity_key );
			if ( ! empty( $verbosity ) ) {
				$params['verbosity'] = $verbosity;
			}
		}
		
		// Add reasoning for models that support it
		if ( $model_spec['supports_reasoning'] ) {
			$reasoning_key = $prefix . 'reasoning';
			$reasoning = EPKB_AI_Config_Specs::get_ai_config_value( $reasoning_key );
			if ( ! empty( $reasoning ) ) {
				$params['reasoning'] = $reasoning;
			}
		}
		
		return $params;
	}

	/**
	 * Get AI instructions for chat
	 *
	 * @return string
	 */
	private function get_instructions() {
		if ( $this instanceof EPKB_AI_Search_Handler ) {
			$instructions = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_instructions' );
			return apply_filters( 'epkb_ai_search_instructions', $instructions );
		} else {
			$instructions = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_instructions' );
			return apply_filters( 'epkb_ai_chat_instructions', $instructions );
		}
	}

	/**
	 * Record API usage for tracking
	 *
	 * @param array $usage Usage data from API response
	 * @return void
	 */
	protected function record_usage( $usage ) {

		if ( empty( $usage ) ) {
			return;
		}
		
		// Get current month's usage
		$month_key = 'epkb_ai_usage_' . gmdate( 'Y_m' );
		$monthly_usage = get_option( $month_key, array(
			'prompt_tokens' => 0,
			'completion_tokens' => 0,
			'total_tokens' => 0,
			'requests' => 0
		) );
		
		// Update usage
		if ( isset( $usage['prompt_tokens'] ) ) {
			$monthly_usage['prompt_tokens'] += intval( $usage['prompt_tokens'] );
		}
		if ( isset( $usage['completion_tokens'] ) ) {
			$monthly_usage['completion_tokens'] += intval( $usage['completion_tokens'] );
		}
		if ( isset( $usage['total_tokens'] ) ) {
			$monthly_usage['total_tokens'] += intval( $usage['total_tokens'] );
		}
		$monthly_usage['requests']++;
		
		// Save updated usage
		update_option( $month_key, $monthly_usage, false );
	}

	/**
	 * Extract content from API response
	 *
	 * @param array $response
	 * @return string
	 */
	private function extract_response_content( $response ) {

		if ( empty( $response['output'] ) || ! is_array( $response['output'] ) ) {
			return '';
		}

		// Primary structure for Responses API - output array with content array
		$last_output = end( $response['output'] );
		if ( empty( $last_output['content'] ) || ! is_array( $last_output['content'] ) ) {
			return '';
		}

		$content = empty( $last_output['content'][0] ) ? '' : $last_output['content'][0];

		// If content is an object with a 'text' property (from newer OpenAI API), extract it
		if ( is_array( $content ) && isset( $content['text'] ) ) {
			$content = $content['text'];
		}

		// If content is an object/array, convert to string
		if ( is_array( $content ) || is_object( $content ) ) {
			$content = json_encode( $content );
		}

		// Convert kb_article patterns to links with article names
		// This is done in the OpenAI handler since it created these file names
		$content = EPKB_AI_OpenAI_Handler::convert_kb_article_references_to_links( $content );

		return $content;
	}
} 