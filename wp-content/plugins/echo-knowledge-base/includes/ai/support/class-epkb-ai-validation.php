<?php

/**
 * AI Validation Utility
 * 
 * Centralized validation for all AI-related inputs
 * Provides consistent validation rules and error messages
 */
class EPKB_AI_Validation {
	
	const MAX_METADATA_KEYS = 100;
	const MAX_METADATA_KEY_LENGTH = 64;
	const MAX_METADATA_VALUE_LENGTH = 255;

	/**
	 * Validate and sanitize chat message
	 *
	 * @param string $message
	 * @return string|WP_Error Sanitized message or error
	 */
	public static function validate_message( $message ) {
		
		// Check if empty
		if ( empty( $message ) ) {
			return new WP_Error( 'empty_message', __( 'Please enter a message.', 'echo-knowledge-base' ) );
		}
		
		// Check message length
		$max_length = apply_filters( 'epkb_ai_chat_max_message_length', 5000 );
		if ( strlen( $message ) > $max_length ) {
			return new WP_Error(
				'message_too_long',
				// translators: %d is the maximum allowed character count
				sprintf( __( 'Message is too long. Please keep it under %d characters.', 'echo-knowledge-base' ), $max_length )
			);
		}
		
		// Basic XSS prevention - strip all HTML tags without encoding entities
		$message = wp_strip_all_tags( $message );
		
		// Check for malicious patterns
		$blocked_patterns = apply_filters( 'epkb_ai_chat_blocked_patterns', array(
			'/\<script/i',
			'/javascript:/i',
			'/on\w+\s*=/i', // onclick, onload, etc.
			'/data:text\/html/i',
			'/vbscript:/i'
		) );
		
		foreach ( $blocked_patterns as $pattern ) {
			if ( preg_match( $pattern, $message ) ) {
				return new WP_Error( 'invalid_content', __( 'Invalid content detected.', 'echo-knowledge-base' ) );
			}
		}
		
		return sanitize_textarea_field( $message );
	}

	/**
	 * Validate UUID v4 format
	 *
	 * @param string $uuid UUID to validate
	 * @return bool|WP_Error True if valid, WP_Error on failure
	 */
	public static function validate_uuid( $uuid ) {
		if ( empty( $uuid ) ) {
			return new WP_Error( 'empty_uuid', __( 'UUID is empty', 'echo-knowledge-base' ) );
		}

		$uuid = trim( $uuid );

		// Regular expression to validate UUID v4 format
		$pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
		if ( preg_match( $pattern, $uuid ) !== 1 ) {
			// translators: %s is the invalid UUID string
			return new WP_Error( 'invalid_uuid', sprintf( __( 'Invalid UUID format: %s', 'echo-knowledge-base' ), $uuid ) );
		}

		return true;
	}

	/**
	 * Validate idempotency key
	 *
	 * @param string $key
	 * @return string|WP_Error Validated key or error
	 */
	public static function validate_idempotency_key( $key ) {
		
		// Check if empty
		if ( empty( $key ) ) {
			return new WP_Error( 
				'empty_idempotency_key', 
				__( 'Idempotency key is required', 'echo-knowledge-base' ) 
			);
		}
		
		// Sanitize
		$key = sanitize_text_field( $key );
		
		// Check length
		if ( strlen( $key ) > 64 ) {
			return new WP_Error( 
				'invalid_idempotency_key', 
				__( 'Idempotency key is too long', 'echo-knowledge-base' ) 
			);
		}
		
		// Check format - should be UUID or similar
		if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $key ) ) {
			return new WP_Error( 
				'invalid_idempotency_key', 
				__( 'Invalid idempotency key format', 'echo-knowledge-base' ) 
			);
		}
		
		return $key;
	}
	
	/**
	 * Validate widget ID
	 *
	 * @param int|string $widget_id
	 * @return int Validated widget ID or error
	 */
	public static function validate_widget_id( $widget_id ) {
		
		$widget_id = absint( $widget_id );
		
		// Check range
		if ( $widget_id < 1 || $widget_id > 500 ) {
			$widget_id = 1;
		}
		
		return $widget_id;
	}

	/**
	 * Validate language code
	 *
	 * @param string $language
	 * @return string Validated language or error
	 */
	public static function validate_language( $language ) {
		$language = sanitize_text_field( $language );
		// Basic validation for language codes (e.g., en, en_US, en-US)
		if ( ! preg_match( '/^[a-z]{2}([_-][A-Z]{2})?$/', $language ) || strlen( $language ) > 10 ) {
			return '';
		}
		return $language;
	}
	
	/**
	 * Validate conversation title
	 *
	 * @param string $title
	 * @return string Validated and truncated title
	 */
	public static function validate_title( $title ) {
		
		$title = sanitize_text_field( $title );
		
		if ( strlen( $title ) > 255 ) {
			$title = substr( $title, 0, 252 ) . '...';
		}
		
		return $title;
	}

	/**
	 * Batch validate multiple fields
	 *
	 * @param array $fields Array of field_name => value pairs
	 * @param array $rules Array of field_name => validation_method pairs
	 * @return array|WP_Error Array of validated values or first error encountered
	 */
	public static function validate_fields( $fields, $rules ) {
		
		$validated = array();
		
		foreach ( $rules as $field_name => $validation_method ) {
			// Skip if field not provided
			if ( ! isset( $fields[$field_name] ) ) {
				continue;
			}
			
			// Validate using specified method
			if ( method_exists( __CLASS__, $validation_method ) ) {
				$result = self::$validation_method( $fields[$field_name] );
				
				if ( is_wp_error( $result ) ) {
					return $result;
				}
				
				$validated[$field_name] = $result;
			}
		}
		
		return $validated;
	}
	
	/**
	 * Validate metadata according to AI limits
	 *
	 * @param array $metadata
	 * @return array
	 */
	public static function validate_metadata( $metadata ) {

		$validated = array();
		$count = 0;
		foreach ( $metadata as $key => $value ) {
			// Limit to max keys
			if ( $count >= self::MAX_METADATA_KEYS ) {
				break;
			}

			// Validate key
			$key = sanitize_key( substr( $key, 0, self::MAX_METADATA_KEY_LENGTH ) );
			if ( empty( $key ) ) {
				continue;
			}

			// Convert boolean values to string representation
			if ( is_bool( $value ) ) {
				$value = $value ? 'true' : 'false';
			}

			// Convert other non-string types to string
			if ( ! is_string( $value ) ) {
				$value = strval( $value );
			}

			// Validate value
			$value = substr( sanitize_text_field( $value ), 0, self::MAX_METADATA_VALUE_LENGTH );
			if ( empty( $value ) && $value !== '0' ) {
				continue;
			}

			$validated[ $key ] = $value;
			$count++;
		}

		return $validated;
	}
	
	/**
	 * Validate session ID format
	 * 
	 * @param string $session_id
	 * @return bool|WP_Error True if valid, WP_Error if invalid
	 */
	public static function validate_session( $session_id ) {
		
		// Check if empty
		if ( empty( $session_id ) ) {
			return new WP_Error( 'empty_session', __( 'Session ID is required', 'echo-knowledge-base' ), array( 'status' => 400 ) );
		}
		
		// Allow special cases
		$special_cases = array( 'wp-cron', 'wp-cli' );
		if ( in_array( $session_id, $special_cases, true ) ) {
			return true;
		}
		
		// Validate format: 32 character hexadecimal
		if ( ! preg_match( '/^[a-f0-9]{32}$/', $session_id ) ) {
			return new WP_Error( 'invalid_session', __( 'Invalid session format', 'echo-knowledge-base' ), array( 'status' => 400 ) );
		}
		
		return true;
	}
		
	/**
	 * Validate API key format
	 *
	 * @param string $api_key
	 * @return bool
	 */
	public static function validate_api_key_format( $api_key, $provider = null ) {

		if ( empty( $api_key ) || ! is_string( $api_key ) ) {
			return false;
		}

		$provider = $provider ?: EPKB_AI_Provider::get_active_provider();
		if ( $provider === EPKB_AI_Provider::PROVIDER_GEMINI ) {
			// Gemini API keys can start with AIza and are alphanumeric with length similar to Google API keys
			return preg_match( '/^[A-Za-z0-9_\-]{20,500}$/', $api_key );
		}

		// OpenAI format
		if ( ! preg_match( '/^sk-[\w\-]+$/i', $api_key ) ) {
			return false;
		}

		$key_length = strlen( $api_key );
		return $key_length >= 20 && $key_length <= 500;
	}

	/**
	 * Validate collection ID
	 *
	 * @param mixed $collection_id Collection ID to validate
	 * @return int|WP_Error Valid collection ID or error
	 */
	public static function validate_collection_id( $collection_id ) {

		if ( empty( $collection_id ) ) {
			return new WP_Error( 'missing_collection_id', __( 'Collection ID is required', 'echo-knowledge-base' ) );
		}

		// Ensure it's a positive integer
		$collection_id = intval( $collection_id );
		if ( $collection_id <= 0 ) {
			return new WP_Error( 'invalid_collection_id', __( 'Collection ID must be a positive number', 'echo-knowledge-base' ) );
		}

		// Verify collection exists in configuration
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		return $collection_id;
	}

	/**
	 * Convert collection configuration errors into context-aware messages.
	 *
	 * @param WP_Error $collection_error Collection error to translate.
	 * @param int      $collection_id Collection ID related to the error.
	 * @param string   $context Optional context for custom error messages: 'chat' or empty for generic.
	 * @return WP_Error
	 */
	public static function translate_collection_error_for_context( $collection_error, $collection_id, $context = '' ) {

		if ( ! is_wp_error( $collection_error ) ) {
			return $collection_error;
		}

		$error_code = $collection_error->get_error_code();
		$error_data = $collection_error->get_error_data();

		if ( $error_code === 'provider_mismatch' ) {
			$message = $context === 'chat'
				? __( 'The selected AI Chat collection belongs to a different AI provider. Go to AI Chat Settings and select a Training Data Collection for the active provider.', 'echo-knowledge-base' )
				: $collection_error->get_error_message();
			return new WP_Error( 'provider_mismatch', $message, $error_data );
		}

		if ( $error_code === 'collection_not_found' ) {
			// translators: %d is the collection ID
			$message = $context === 'chat'
				? __( 'The selected AI Chat collection no longer exists. Go to AI Chat Settings and select a Training Data Collection, or create one first in Training Data.', 'echo-knowledge-base' )
				: sprintf( __( 'Collection %d does not exist. Please create the collection first in Training Data.', 'echo-knowledge-base' ), $collection_id );
			return new WP_Error( 'collection_not_found', $message, $error_data );
		}

		if ( $error_code === 'no_vector_store' ) {
			// translators: %d is the collection ID
			$message = $context === 'chat'
				? __( 'The selected AI Chat collection has not been synced yet. Go to Training Data and sync the collection before using AI Chat.', 'echo-knowledge-base' )
				: sprintf( __( 'Collection %d does not have a vector store configured. Please sync content in Training Data first.', 'echo-knowledge-base' ), $collection_id );
			return new WP_Error( 'no_vector_store', $message, $error_data );
		}

		return $collection_error;
	}

	/**
	 * Validate that a collection exists and has a vector store with valid content
	 * Consolidated method used by both AI Chat and KB Config validation
	 *
	 * @param int $collection_id Collection ID to check
	 * @param string $context Optional context for custom error messages: 'chat', 'kb_config', or empty for generic
	 * @return true|WP_Error True if valid, WP_Error otherwise
	 */
	public static function validate_collection_has_vector_store( $collection_id, $context = '' ) {

		// Check if collection exists and has vector store
		$vector_store_id = EPKB_AI_Training_Data_Config_Specs::get_active_provider_vector_store_id_by_collection( $collection_id );
		if ( is_wp_error( $vector_store_id ) ) {
			return self::translate_collection_error_for_context( $vector_store_id, $collection_id, $context );
		}

		// Get vector store info to check if it has content
		$vector_store = EPKB_AI_Provider::get_vector_store_handler();
		$store_info = $vector_store->get_vector_store_info_by_id( $vector_store_id );

		if ( is_wp_error( $store_info ) ) {
			// translators: %s is the AI provider name, or %d is the collection ID
			$message = $context === 'chat'
				? sprintf( __( 'The selected collection could not be found in %s. Please re-sync the collection in the Training Data tab.', 'echo-knowledge-base' ), EPKB_AI_Provider::get_provider_label() )
				: sprintf( __( 'Collection %d vector store does not exist for the configured AI provider. Please sync content in Training Data.', 'echo-knowledge-base' ), $collection_id );
			return new WP_Error( 'vector_store_not_found', $message );
		}

		// Check if vector store has any files
		$file_count = isset( $store_info['file_counts']['total'] ) ? $store_info['file_counts']['total'] : 0;
		if ( $file_count === 0 ) {
			// translators: %d is the collection ID
			$message = $context === 'chat'
				? __( 'The selected collection is empty. Please add content to the collection in the Training Data tab before using it for chat.', 'echo-knowledge-base' )
				: sprintf( __( 'Collection %d vector store is empty. Please sync content in Training Data first.', 'echo-knowledge-base' ), $collection_id );
			return new WP_Error( 'vector_store_empty', $message );
		}

		return true;
	}
}
