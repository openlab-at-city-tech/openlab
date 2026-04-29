<?php

/**
 * Base AI Handler
 *
 * Abstract base class providing common functionality for AI handlers.
 * Supports multiple AI providers (ChatGPT, Gemini) via provider factory.
 */
abstract class EPKB_AI_Base_Handler {

	/**
	 * AI client for current provider
	 * @var EPKB_ChatGPT_Client|EPKB_Gemini_Client
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
		$this->ai_client = EPKB_AI_Provider::get_client();
		$this->messages_db = new EPKB_AI_Messages_DB();
	}

	/**
	 * Get the collection validation context for handler-specific error messages.
	 *
	 * @return string
	 */
	protected function get_collection_validation_context() {
		return '';
	}

	/**
	 * Call AI API with messages
	 *
	 * @param String $message
	 * @param String $model
	 * @param null $previous_response_id
	 * @param int|null $collection_id Training data collection ID to use for search
	 * @param array $conversation_history Optional conversation history for Gemini (which is stateless)
	 * @return array|WP_Error AI response or error
	 */
	protected function get_ai_response( $message, $model, $previous_response_id, $collection_id, $conversation_history = array() ) {

		// Validate model exists for current provider, fallback to default if not
		$model = EPKB_AI_Provider::resolve_model_name( $model );

		// Get vector store ID - required for both providers
		$vector_store_id = EPKB_AI_Training_Data_Config_Specs::get_active_provider_vector_store_id_by_collection( $collection_id );
		if ( is_wp_error( $vector_store_id ) ) {
			return EPKB_AI_Validation::translate_collection_error_for_context(
				$vector_store_id,
				$collection_id,
				$this->get_collection_validation_context()
			);
		}

		// Get context-specific parameters
		$params = $this->get_model_parameters( $model );

		// Use provider-specific API call
		$provider = EPKB_AI_Provider::get_active_provider();
		if ( $provider === EPKB_AI_Provider::PROVIDER_GEMINI ) {
			$response = $this->get_gemini_response( $message, $model, $vector_store_id, $params, $conversation_history );
		} else {
			$response = $this->get_chatgpt_response( $message, $model, $vector_store_id, $params, $previous_response_id );
		}

		if ( is_wp_error( $response ) ) {
			if ( $response->get_error_code() === 'authentication_failed' ) {
				// Distinguish between API key issues and store access issues
				$error_msg = $response->get_error_message();
				if ( stripos( $error_msg, 'file search store' ) !== false || stripos( $error_msg, 'fileSearchStore' ) !== false || stripos( $error_msg, 'vector_store' ) !== false ) {
					return new WP_Error( 'store_access_denied', __( 'The AI data store is no longer accessible. This usually happens when the API key is changed. Please re-sync your training data to create a new data store.', 'echo-knowledge-base' ) );
				}
				return new WP_Error( 'authentication_failed', __( 'AI service authentication failed. Please check your API key in the AI settings.', 'echo-knowledge-base' ) );
			}
			return $response;
		}

		// Extract content from response
		$content = EPKB_AI_Provider::extract_response_content( $response );
		if ( EPKB_AI_Utilities::is_empty_ai_answer( $content ) ) {
			EPKB_AI_Log::add_log( 'Warning: AI did not return a response. Check the training data and request to see why no response was returned.', array(
				'provider'          => $provider,
				'model'             => $model,
				'collection_id'     => $collection_id,
				'request_preview'   => $message,
				'request_endpoint'  => $provider === EPKB_AI_Provider::PROVIDER_GEMINI ? '/models/' . $model . ':generateContent' : '/responses',
				'response_id'       => isset( $response['id'] ) ? $response['id'] : '',
				'output_items'      => isset( $response['output'] ) && is_array( $response['output'] ) ? count( $response['output'] ) : 0,
				'candidate_count'   => isset( $response['candidates'] ) && is_array( $response['candidates'] ) ? count( $response['candidates'] ) : 0,
				'raw_response_body' => wp_json_encode( $response )
			) );
			return new WP_Error( 'empty_response', __( 'AI did not return a response. Please try again.', 'echo-knowledge-base' ) );
		}

		// Extract source references from grounding metadata
		$sources = EPKB_AI_Provider::extract_response_sources( $response );

		return array(
			'content'           => $content,
			'response_id'       => isset( $response['id'] ) ? $response['id'] : '',
			'sources'           => $sources,
			'thought_signature' => $provider === EPKB_AI_Provider::PROVIDER_GEMINI ? EPKB_Gemini_Client::extract_thought_signature( $response ) : '',
			'response_parts'    => $provider === EPKB_AI_Provider::PROVIDER_GEMINI ? EPKB_Gemini_Client::extract_response_parts( $response ) : array()
		);
	}

	/**
	 * Get AI response from ChatGPT using Responses API
	 *
	 * @param string $message User message
	 * @param string $model Model name
	 * @param string $vector_store_id Vector store ID
	 * @param array $params Model parameters
	 * @param string|null $previous_response_id Previous response ID for conversation continuity
	 * @return array|WP_Error
	 */
	private function get_chatgpt_response( $message, $model, $vector_store_id, $params, $previous_response_id = null ) {

		$request = array(
			'model'        => $model,
			'instructions' => $this->get_instructions(),
			'input'        => array(
				array(
					'role'    => 'user',
					'content' => $message
				)
			),
		);

		$request = EPKB_AI_Provider::apply_model_parameters( $request, $model, $params, EPKB_AI_Provider::PROVIDER_CHATGPT );

		if ( ! empty( $previous_response_id ) ) {
			$request['previous_response_id'] = $previous_response_id;
		}

		$request['tools'] = array(
			array(
				'type'             => 'file_search',
				'vector_store_ids' => array( $vector_store_id ),
				'max_num_results'  => EPKB_AI_Provider::get_default_max_results( EPKB_AI_Provider::PROVIDER_CHATGPT )
			)
		);

		return $this->ai_client->request( '/responses', $request );
	}

	/**
	 * Get AI response from Gemini using generateContent with file_search tool
	 *
	 * @param string $message User message
	 * @param string $model Model name
	 * @param string $vector_store_id File Search Store ID
	 * @param array $params Model parameters
	 * @param array $conversation_history Previous messages in the conversation
	 * @return array|WP_Error
	 */
	private function get_gemini_response( $message, $model, $vector_store_id, $params, $conversation_history = array() ) {

		$options = array(
			'system_instruction' => $this->get_instructions(),
			'model_parameters'   => $params,
		);

		if ( ! empty( $conversation_history ) ) {
			$options['conversation_history'] = $conversation_history;
		}

		return $this->ai_client->generate_content_with_file_search( $model, $message, $vector_store_id, $options );
	}

	/**
	 * Get model parameters based on context (chat or search)
	 *
	 * @param string $model The model being used
	 * @return array Parameters array with temperature, max_output_tokens, etc.
	 */
	protected function get_model_parameters( $model ) {

		$use_case = $this instanceof EPKB_AI_Search_Handler ? 'search' : 'chat';
		$runtime_profile = EPKB_AI_Provider::get_runtime_profile( $use_case );
		$model_spec = EPKB_AI_Provider::get_models_and_default_params( $model );
		$params = empty( $runtime_profile['params'] ) ? array() : $runtime_profile['params'];
		$max_limit = isset( $model_spec['max_output_tokens_limit'] ) ? intval( $model_spec['max_output_tokens_limit'] ) : 16384;

		if ( isset( $params['max_output_tokens'] ) ) {
			$params['max_output_tokens'] = intval( $params['max_output_tokens'] );
			if ( $params['max_output_tokens'] <= 0 ) {
				unset( $params['max_output_tokens'] );
			} else {
				$params['max_output_tokens'] = min( $params['max_output_tokens'], $max_limit );
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
	 * Extract content from API response
	 *
	 * @param array $response
	 * @return string
	 */
	private function extract_response_content( $response ) {
		return EPKB_AI_Provider::extract_response_content( $response );
	}
} 
