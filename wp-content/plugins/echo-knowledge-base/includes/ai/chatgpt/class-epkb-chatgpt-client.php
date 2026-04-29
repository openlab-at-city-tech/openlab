<?php

/**
 * ChatGPT API Client
 *
 * Handles all HTTP communication with ChatGPT/OpenAI API endpoints.
 * Implements retry logic, rate limiting, and error handling.
 */
class EPKB_ChatGPT_Client {

	const API_BASE_URL = 'https://api.openai.com';
	const API_VERSION = 'v1';
	// TODO AI PRO LEGACY: Remove after AI PRO and other add-ons stop reading EPKB_ChatGPT_Client::DEFAULT_MODEL directly.
	const DEFAULT_MODEL = 'gpt-5.4'; // Keep in sync with EPKB_ChatGPT_Model_Catalog::DEFAULT_MODEL.
	const DEFAULT_MAX_RETRIES = 3;
	const DEFAULT_CONVERSATION_EXPIRY_DAYS = 29; // 29 days
	const MAX_FILE_SIZE = 51380224; // 49 MB.

	/**
	 * Make a request to the ChatGPT API with automatic retry logic
	 *
	 * Retry behavior:
	 * - Insufficient quota errors (429 with insufficient_quota): No retry (billing issue)
	 * - Rate limit errors (429 with rate_limit_exceeded): Retry with exponential backoff
	 * - Other client errors (4xx): No retry
	 * - Server errors (5xx): Retry up to 3 times with exponential backoff
	 * - Network/timeout errors: Retry up to 3 times with exponential backoff
	 *
	 * @param string $endpoint
	 * @param array $data
	 * @param string $method
	 * @param string $purpose Purpose of the request (e.g., 'content_analysis', 'chat', 'search', 'general') - used for logging and timeout determination
	 * @return array|WP_Error
	 */
	public function request( $endpoint, $data = array(), $method = 'POST', $purpose = 'general' ) {

		$api_key_check = $this->check_api_key();
		if ( is_wp_error( $api_key_check ) ) {
			return $api_key_check;
		}

		// TODO AI PRO LEGACY: Remove after legacy add-ons stop sending old ChatGPT model IDs through the client directly.
		if ( ! empty( $data['model'] ) && is_string( $data['model'] ) ) {
			$data['model'] = EPKB_AI_Provider::resolve_model_name( $data['model'], EPKB_AI_Provider::PROVIDER_CHATGPT );
		}

		$last_error = null;
		for ( $attempt = 0; $attempt <= self::DEFAULT_MAX_RETRIES; $attempt++ ) {

			if ( $attempt > 0 && $last_error ) {
				$delay_seconds = EPKB_AI_Utilities::calculate_backoff_delay( $attempt - 1, 1, 60, $last_error );
				EPKB_AI_Utilities::safe_sleep( $delay_seconds );
			}

			// 1. Execute request with short retry mechanism
			$request_start_time = microtime( true );
			$response = $this->execute_request( $endpoint, $method, $data, $purpose );
			$request_duration = microtime( true ) - $request_start_time;

			// 2. Parse response and check for errors (handles all HTTP status codes)
			$parsed = $this->parse_response( $response );

			// 3. Request succeeded, parse final response
			if ( ! is_wp_error( $parsed ) ) {
				$parsed['_timing'] = array( 'elapsed_seconds' => round( $request_duration, 3 ) );
				EPKB_AI_Log::add_log( 'API request completed', array(
					'purpose'          => $purpose,
					'request_endpoint' => $endpoint,
					'model'            => isset( $data['model'] ) ? $data['model'] : $purpose,
					'elapsed_seconds'  => round( $request_duration, 3 ),
					'attempt'          => $attempt + 1
				) );
				return $parsed;
			}

			// 4. Handle error response

			// log error details
			$log_context = $parsed->get_error_data();
			$log_context = is_array( $log_context ) ? $log_context : array();
			$log_context['purpose'] = $purpose;
			$log_context['request_endpoint'] = $endpoint;
			$log_context['model'] = isset( $data['model'] ) ? $data['model'] : $purpose;
			$log_context['elapsed_seconds'] = round( $request_duration, 3 );
			$log_context['request_contents_count'] = isset( $data['contents'] ) && is_array( $data['contents'] ) ? count( $data['contents'] ) : 0;
			EPKB_AI_Log::add_log( 'API request error: ' . $parsed->get_error_message(), $log_context );

			// Warn if execution time limit is too low
			$current_limit = ini_get( 'max_execution_time' );
			if ( $current_limit < EPKB_AI_Utilities::DEFAULT_TIMEOUT ) {
				EPKB_AI_Log::add_log( 'PHP execution time limit is too low for AI operations', array( 'current_limit' => $current_limit, 'minimum_required' => EPKB_AI_Utilities::DEFAULT_TIMEOUT) );
			}

			// 5. Determine if we should retry based on error type
			if ( ! EPKB_AI_Utilities::is_retryable_error( $parsed ) ) {
				return $parsed;
			}

			// 6. Check if we should do a short retry e.g., for transient network errors
			$last_error = $request_duration < 5 && $attempt < self::DEFAULT_MAX_RETRIES ? null : $parsed;

		} // end for()

		return new WP_Error( 'max_retries_exceeded', __( 'Maximum retries exceeded', 'echo-knowledge-base' ), ( is_wp_error( $last_error ) ? $last_error->get_error_data() : $data ) );
	}


	/********************************************************************
	 *          Request Functions
	 ********************************************************************/

	/**
	 * Execute the HTTP request
	 *
	 * @param string $endpoint
	 * @param string $method
	 * @param array $data
	 * @param string $purpose Purpose of the request (e.g., 'content_analysis', 'chat', 'search', 'general')
	 * @return array|WP_Error
	 */
	private function execute_request( $endpoint, $method, $data, $purpose ) {

		$headers = $this->build_headers( $endpoint );
		$body = null;
		if ( $purpose === 'file_storage_upload' ) {
			$boundary = wp_generate_password( 24, false, false );
			$content_type = isset( $data['file_content_type'] ) ? $data['file_content_type'] : 'text/plain; charset=utf-8';
			$body = $this->build_multipart_body( $boundary, ['purpose' => $data['file_purpose']], $data['file_content'], $data['file_name'], $content_type );
			$headers['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
		}

		$args = array(
			'method'  => $method,
			'headers' => $headers,
			'timeout' => EPKB_AI_Utilities::get_timeout_for_purpose( $purpose ),
			'sslverify' => true
		);

		if ( ! empty( $body ) ) {
			$args['body'] = $body;
		} elseif ( ! empty( $data ) ) {
			if ( $method === 'GET' ) {
				$endpoint = add_query_arg( $data, $endpoint );
			} else {
				$json_body = json_encode( $data );
				if ( $json_body === false ) {
					EPKB_AI_Log::add_log( 'JSON ENCODE ERROR: Failed to encode request data: ' . json_last_error_msg() );
					return new WP_Error( 'json_encode_error', 'JSON ENCODE ERROR: Failed to encode request data: ' . json_last_error_msg(), $data );
				}
				$args['body'] = $json_body;
			}
		}

		$response = wp_remote_request( self::API_BASE_URL . '/' . self::API_VERSION . $endpoint, $args );

		return $response;
	}

	/**
	 * Build request headers
	 * @return array
	 */
	private function build_headers( $endpoint ) {

		$headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . self::get_api_key(),
			'User-Agent'    => 'Echo-Knowledge-Base/' . \Echo_Knowledge_Base::$version
		);

		if ( strpos( $endpoint, EPKB_AI_ChatGPT_Vector_Store::VECTOR_STORES_ENDPOINT ) === 0 ) {
			$headers['OpenAI-Beta'] = 'assistants=v2';
		}

		// Add organization ID if configured
		if ( ! empty( $this->organization_id ) ) {
			$headers['OpenAI-Organization'] = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_organization_id' );
		}

		return $headers;
	}

	/**
	 * Build multipart form data body
	 *
	 * @param string $boundary
	 * @param array $fields
	 * @param string $file_content
	 * @param string $filename
	 * @param string $content_type MIME type for the file part
	 * @return string
	 */
	private function build_multipart_body( $boundary, $fields, $file_content, $filename, $content_type = 'text/plain; charset=utf-8' ) {

		$eol = "\r\n";
		$body = '';

		// purpose field
		foreach ( $fields as $name => $value ) {
			$body .= '--' . $boundary . "\r\n";
			$body .= 'Content-Disposition: form-data; name="' . $name . '"' . $eol . $eol;
			$body .= $value . $eol;
		}

		// file field
		$body .= '--' . $boundary . $eol;
		$body .= 'Content-Disposition: form-data; name="file"; filename="' . $filename . '"' . $eol;
		$body .= 'Content-Type: ' . $content_type . $eol;
		$body .= $eol;
		$body .= $file_content . $eol;

		// closing boundary
		$body .= '--' . $boundary . '--' .$eol;

		return $body;
	}


	/**********************************************************************
	 *          Response Functions
	 ********************************************************************/

	/**
	 * Parse API response
	 *
	 * @param array|WP_Error $response
	 * @return array|WP_Error
	 */
	private function parse_response( $response ) {

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		$rate_limit_info = $this->extract_rate_limit_headers( $response );

		// Handle WP_Error response
		if ( is_wp_error( $response ) ) {
			$error_data = $this->build_error_data( $status_code, $response_message, $body, '', $rate_limit_info );
			$response->add_data( $error_data );
			return $response;
		}

		// Try to decode JSON response
		$data = json_decode( $body, true );
		$is_json = json_last_error() === JSON_ERROR_NONE;

		// Handle success responses
		if ( $status_code >= 200 && $status_code < 300 ) {
			if ( ! $is_json ) {
				$error_data = $this->build_error_data( $status_code, $response_message, $body, '', $rate_limit_info );
				return new WP_Error( 'invalid_json', 'AI ERROR: Invalid JSON in success response', $error_data );
			}

			// Check for incomplete_details - treat as error
			if ( isset( $data['incomplete_details'] ) && ! empty( $data['incomplete_details']['reason'] ) ) {
				$reason = $data['incomplete_details']['reason'];
				$error_data = $this->build_error_data( $status_code, $response_message, $body, '', $rate_limit_info, array(
					'incomplete_reason' => $reason,
					'response_data' => $data
				) );
				// translators: %s is the reason for incomplete response
				return new WP_Error( 'response_incomplete', sprintf( __( 'CHATGPT ERROR: Response incomplete: %s', 'echo-knowledge-base' ), $reason ), $error_data );
			}

			return $data;
		}

		// Extract error message and code from JSON if available
		$error_message = '';
		$error_code = '';
		if ( $is_json && isset( $data['error'] ) ) {
			if ( isset( $data['error']['message'] ) ) {
				$error_message = 'AI ERROR: ' . $data['error']['message'];
			} elseif ( is_string( $data['error'] ) ) {
				$error_message = 'AI ERROR: ' . $data['error'];
			}

			if ( isset( $data['error']['code'] ) ) {
				$error_code = $data['error']['code'];
			}
		}

		// Fallback to plain text error body or HTTP message
		if ( empty( $error_message ) ) {
			if ( ! empty( $body ) ) {
				// Use the raw body as error message (e.g., "upstream connect error...")
				$error_message = 'AI ERROR: ' . ( strlen( $body ) > 200 ? substr( $body, 0, 200 ) . '...' : $body );
			} else {
				$error_message = 'AI ERROR: HTTP ' . $status_code . ' ' . $response_message;
			}
		}

		// Handle specific error types
		$error_data = $this->build_error_data( $status_code, $response_message, $body, $error_code, $rate_limit_info );
		switch ( $status_code ) {
			case 400:
				return new WP_Error( 'bad_request', $error_message, $error_data );

			case 401:
			case 403:
				return new WP_Error( 'authentication_failed', $error_message, $error_data );

			case 429:
				$retry_after = wp_remote_retrieve_header( $response, 'retry-after' );

				// Check the specific error code to distinguish between rate limit and insufficient quota
				$wp_error_code = 'unknown_x03'; // Default
				if ( $error_code === 'insufficient_quota' ) {
					$wp_error_code = 'insufficient_quota';
				} elseif ( $error_code === 'rate_limit_exceeded' ) {
					$wp_error_code = 'rate_limit_exceeded';
				}

				$error_data = $this->build_error_data( $status_code, $response_message, $body, $error_code, $rate_limit_info, array( 'retry_after' => $retry_after ) );
				return new WP_Error( $wp_error_code, $error_message, $error_data );

			case 404:
				return new WP_Error( 'not_found', $error_message, $error_data );

			case 500:
			case 502:
			case 503:
				return new WP_Error( 'server_error', $error_message, $error_data );

			default:
				return new WP_Error( 'api_error', $error_message, $error_data );
		}
	}

	/**
	 * Build consistent error data structure
	 *
	 * @param int $status_code HTTP status code
	 * @param string $response_message HTTP response message
	 * @param string $body Raw response body
	 * @param string $error_code ChatGPT error code
	 * @param array $rate_limit_info Rate limit information
	 * @param array $additional_data Additional error-specific data
	 * @return array Error data array
	 */
	private function build_error_data( $status_code, $response_message, $body, $error_code = '', $rate_limit_info = array(), $additional_data = array() ) {

		$error_data = array(
			'status_code' => $status_code,
			'response'    => array( 'code' => $status_code, 'message' => $response_message ),
		);

		// Add error code if available
		if ( ! empty( $error_code ) ) {
			$error_data['error_code'] = $error_code;
		}

		// Include raw body for debugging (truncated if too long)
		if ( ! empty( $body ) ) {
			$error_data['raw_body'] = strlen( $body ) > 500 ? substr( $body, 0, 500 ) . '...' : $body;
		}

		// Include rate limit headers if available
		if ( ! empty( $rate_limit_info ) ) {
			$error_data['rate_limit'] = $rate_limit_info;
		}

		// Merge additional error-specific data
		if ( ! empty( $additional_data ) ) {
			$error_data = array_merge( $error_data, $additional_data );
		}

		return $error_data;
	}


	/********************************************************************
	 *          Utility Functions
	 ********************************************************************/

	/**
	 * Extract rate limit headers from response
	 *
	 * @param array|WP_Error $response Response
	 * @return array Rate limit information
	 */
	private function extract_rate_limit_headers( $response ) {

		$headers = wp_remote_retrieve_headers( $response );

		$rate_limit_info = array();

		// Check for request-based rate limits
		if ( isset( $headers['x-ratelimit-limit-requests'] ) ) {
			$rate_limit_info['limit_requests'] = intval( $headers['x-ratelimit-limit-requests'] );
		}
		if ( isset( $headers['x-ratelimit-remaining-requests'] ) ) {
			$rate_limit_info['remaining_requests'] = intval( $headers['x-ratelimit-remaining-requests'] );
		}
		if ( isset( $headers['x-ratelimit-reset-requests'] ) ) {
			$reset_timestamp = $headers['x-ratelimit-reset-requests'];
			// Handle both timestamp and duration formats
			if ( strpos( $reset_timestamp, 's' ) !== false || strpos( $reset_timestamp, 'm' ) !== false ) {
				// Parse duration format (e.g., "5s", "2m30s")
				$seconds = $this->parse_duration_to_seconds( $reset_timestamp );
				$rate_limit_info['reset_requests'] = time() + $seconds;
				$rate_limit_info['reset_requests_in'] = $seconds;
			} else {
				$rate_limit_info['reset_requests'] = intval( $reset_timestamp );
				$rate_limit_info['reset_requests_in'] = max( 0, $rate_limit_info['reset_requests'] - time() );
			}
		}

		// Check for token-based rate limits
		if ( isset( $headers['x-ratelimit-limit-tokens'] ) ) {
			$rate_limit_info['limit_tokens'] = intval( $headers['x-ratelimit-limit-tokens'] );
		}
		if ( isset( $headers['x-ratelimit-remaining-tokens'] ) ) {
			$rate_limit_info['remaining_tokens'] = intval( $headers['x-ratelimit-remaining-tokens'] );
		}
		if ( isset( $headers['x-ratelimit-reset-tokens'] ) ) {
			$reset_timestamp = $headers['x-ratelimit-reset-tokens'];
			// Handle both timestamp and duration formats
			if ( strpos( $reset_timestamp, 's' ) !== false || strpos( $reset_timestamp, 'm' ) !== false ) {
				// Parse duration format (e.g., "5s", "2m30s")
				$seconds = $this->parse_duration_to_seconds( $reset_timestamp );
				$rate_limit_info['reset_tokens'] = time() + $seconds;
				$rate_limit_info['reset_tokens_in'] = $seconds;
			} else {
				$rate_limit_info['reset_tokens'] = intval( $reset_timestamp );
				$rate_limit_info['reset_tokens_in'] = max( 0, $rate_limit_info['reset_tokens'] - time() );
			}
		}

		// Store rate limit info for next request timing
		if ( ! empty( $rate_limit_info ) ) {
			set_transient( 'epkb_chatgpt_rate_limit', $rate_limit_info, 300 );
		}

		return $rate_limit_info;
	}

	/**
	 * Parse duration string to seconds
	 * Handles formats like "5s", "2m30s", "1h30m", etc.
	 *
	 * @param string $duration
	 * @return int Seconds
	 */
	private function parse_duration_to_seconds( $duration ) {
		$seconds = 0;

		// Match hours
		if ( preg_match( '/(\d+)h/i', $duration, $matches ) ) {
			$seconds += intval( $matches[1] ) * 3600;
		}

		// Match minutes
		if ( preg_match( '/(\d+)m/i', $duration, $matches ) ) {
			$seconds += intval( $matches[1] ) * 60;
		}

		// Match seconds
		if ( preg_match( '/(\d+)s/i', $duration, $matches ) ) {
			$seconds += intval( $matches[1] );
		}

		// If no units found, assume it's seconds
		if ( $seconds === 0 && is_numeric( $duration ) ) {
			$seconds = intval( $duration );
		}

		return $seconds;
	}

	/**
	 * Apply ChatGPT model parameters.
	 *
	 * Kept as a wrapper so older add-ons can still call the legacy client API.
	 *
	 * @param array $request
	 * @param string $model
	 * @param array $params
	 * @return array
	 */
	public static function apply_model_parameters( $request, $model, $params = array() ) {
		// TODO AI PRO LEGACY: Remove after add-ons call EPKB_AI_Provider::apply_model_parameters() directly.
		return EPKB_AI_Provider::apply_model_parameters( $request, $model, $params, EPKB_AI_Provider::PROVIDER_CHATGPT );
	}

	/**
	 * Get the default ChatGPT model.
	 *
	 * @return string
	 */
	public static function get_default_model() {
		// TODO AI PRO LEGACY: Remove after add-ons stop calling EPKB_ChatGPT_Client::get_default_model().
		return EPKB_AI_Provider::get_default_model( EPKB_AI_Provider::PROVIDER_CHATGPT );
	}

	/**
	 * Get API key from configuration
	 *
	 * @return string
	 */
	public static function get_api_key() {
		return EPKB_AI_Provider::get_api_key( EPKB_AI_Provider::PROVIDER_CHATGPT );
	}

	/**
	 * Validate presence of API key
	 *
	 * @return true|WP_Error
	 */
	private function check_api_key() {
		$api_key = self::get_api_key();
		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'ERROR: API key is not configured. Please configure your API key in the AI settings.', 'echo-knowledge-base' ) );
		}

		return true;
	}

	/**
	 * Test connection to ChatGPT API
	 *
	 * @return true|WP_Error True if connection is successful, WP_Error on failure
	 */
	public function test_connection() {
		// Try to list models as a simple test
		$response = $this->request( '/models', array(), 'GET' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Check if we got a valid response structure
		if ( ! isset( $response['data'] ) || ! is_array( $response['data'] ) ) {
			return new WP_Error( 'invalid_response', __( 'CHATGPT ERROR: Invalid response from ChatGPT API', 'echo-knowledge-base' ) );
		}

		return true;
	}

}
