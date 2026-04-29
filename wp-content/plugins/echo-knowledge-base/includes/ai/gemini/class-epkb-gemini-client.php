<?php defined( 'ABSPATH' ) || exit();

/**
 * Gemini API Client
 *
 * Handles HTTP communication with Google Gemini API endpoints.
 * Implements retry logic, rate limiting, and error handling.
 */
class EPKB_Gemini_Client {

	const API_BASE_URL = 'https://generativelanguage.googleapis.com';
	const API_VERSION = 'v1beta';
	const DEFAULT_MAX_RETRIES = 3;
	const MAX_FILE_SIZE = 51380224; // 49 MB.

	/**
	 * Make a request to the Gemini API with automatic retry logic
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

		$headers = $this->build_headers();

		$args = array(
			'method'  => $method,
			'headers' => $headers,
			'timeout' => EPKB_AI_Utilities::get_timeout_for_purpose( $purpose ),
			'sslverify' => true
		);

		if ( ! empty( $data ) ) {
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
	 * Upload file using Google's resumable upload protocol
	 *
	 * @param string $endpoint Upload endpoint (e.g., /fileSearchStores/{id}:uploadToFileSearchStore)
	 * @param string $file_content File content to upload
	 * @param array $metadata Metadata including displayName
	 * @param string $mime_type MIME type (default: text/plain)
	 * @return array|WP_Error Response data or error
	 */
	public function upload_file_resumable( $endpoint, $file_content, $metadata = array(), $mime_type = 'text/plain' ) {

		$api_key_check = $this->check_api_key();
		if ( is_wp_error( $api_key_check ) ) {
			return $api_key_check;
		}

		$content_length = strlen( $file_content );

		// Step 1: Initiate resumable upload
		$init_headers = array(
			'Content-Type'                     => 'application/json',
			'x-goog-api-key'                   => self::get_api_key(),
			'X-Goog-Upload-Protocol'           => 'resumable',
			'X-Goog-Upload-Command'            => 'start',
			'X-Goog-Upload-Header-Content-Length' => $content_length,
			'X-Goog-Upload-Header-Content-Type'   => $mime_type,
			'User-Agent'                       => 'Echo-Knowledge-Base/' . \Echo_Knowledge_Base::$version
		);

		$init_body = array();
		if ( ! empty( $metadata['displayName'] ) ) {
			$init_body['displayName'] = $metadata['displayName'];
		}
		if ( ! empty( $metadata['customMetadata'] ) ) {
			$init_body['customMetadata'] = $metadata['customMetadata'];
		}

		return $this->execute_resumable_upload( '/upload/' . self::API_VERSION . $endpoint, $file_content, $init_headers, $init_body );
	}

	/**
	 * Upload a file to Gemini Files API for direct prompt usage.
	 *
	 * @param string $file_content Raw file content.
	 * @param array $metadata Metadata including displayName.
	 * @param string $mime_type MIME type.
	 * @return array|WP_Error
	 */
	public function upload_prompt_file( $file_content, $metadata = array(), $mime_type = 'application/octet-stream' ) {

		$api_key_check = $this->check_api_key();
		if ( is_wp_error( $api_key_check ) ) {
			return $api_key_check;
		}

		$content_length = strlen( $file_content );
		$init_headers = array(
			'Content-Type'                        => 'application/json',
			'x-goog-api-key'                      => self::get_api_key(),
			'X-Goog-Upload-Protocol'              => 'resumable',
			'X-Goog-Upload-Command'               => 'start',
			'X-Goog-Upload-Header-Content-Length' => $content_length,
			'X-Goog-Upload-Header-Content-Type'   => $mime_type,
			'User-Agent'                          => 'Echo-Knowledge-Base/' . \Echo_Knowledge_Base::$version
		);

		$init_body = array();
		if ( ! empty( $metadata['displayName'] ) ) {
			$init_body['file'] = array( 'display_name' => $metadata['displayName'] );
		}

		return $this->execute_resumable_upload( '/upload/' . self::API_VERSION . '/files', $file_content, $init_headers, $init_body );
	}

	/**
	 * Get a Gemini File resource.
	 *
	 * @param string $file_name Resource name like files/abc123.
	 * @return array|WP_Error
	 */
	public function get_file( $file_name ) {

		if ( empty( $file_name ) ) {
			return new WP_Error( 'missing_file_name', __( 'File name is required.', 'echo-knowledge-base' ) );
		}

		return $this->request( '/' . ltrim( $file_name, '/' ), array(), 'GET', 'file_storage' );
	}

	/**
	 * Delete a Gemini File resource.
	 *
	 * @param string $file_name Resource name like files/abc123.
	 * @return array|WP_Error
	 */
	public function delete_file( $file_name ) {

		if ( empty( $file_name ) ) {
			return new WP_Error( 'missing_file_name', __( 'File name is required.', 'echo-knowledge-base' ) );
		}

		return $this->request( '/' . ltrim( $file_name, '/' ), array(), 'DELETE', 'file_storage' );
	}

	/**
	 * Execute Gemini resumable upload flow.
	 *
	 * @param string $upload_path API path beginning with /upload/.
	 * @param string $file_content Raw file content.
	 * @param array $init_headers Headers for the initiation request.
	 * @param array $init_body Optional initiation metadata.
	 * @return array|WP_Error
	 */
	private function execute_resumable_upload( $upload_path, $file_content, $init_headers, $init_body = array() ) {

		$init_response = wp_remote_post( self::API_BASE_URL . $upload_path, array(
			'headers'   => $init_headers,
			'body'      => ! empty( $init_body ) ? wp_json_encode( $init_body ) : '',
			'timeout'   => EPKB_AI_Utilities::get_timeout_for_purpose( 'file_upload' ),
			'sslverify' => true
		) );

		if ( is_wp_error( $init_response ) ) {
			EPKB_AI_Log::add_log( 'Resumable upload initiation failed', array( 'error' => $init_response->get_error_message() ) );
			return $init_response;
		}

		$init_status = wp_remote_retrieve_response_code( $init_response );
		if ( $init_status < 200 || $init_status >= 300 ) {
			$body = wp_remote_retrieve_body( $init_response );
			EPKB_AI_Log::add_log( 'Resumable upload initiation failed', array( 'status' => $init_status, 'body' => $body ) );
			return new WP_Error( 'upload_init_failed', __( 'Failed to initiate file upload', 'echo-knowledge-base' ), array( 'status_code' => $init_status, 'raw_body' => $body ) );
		}

		$headers = wp_remote_retrieve_headers( $init_response );
		$upload_url = isset( $headers['x-goog-upload-url'] ) ? $headers['x-goog-upload-url'] : '';
		if ( empty( $upload_url ) ) {
			EPKB_AI_Log::add_log( 'Resumable upload URL not found in response headers' );
			return new WP_Error( 'upload_url_missing', __( 'Upload URL not found in response', 'echo-knowledge-base' ) );
		}

		$upload_headers = array(
			'Content-Length'        => strlen( $file_content ),
			'X-Goog-Upload-Offset'  => '0',
			'X-Goog-Upload-Command' => 'upload, finalize'
		);

		$upload_response = wp_remote_post( $upload_url, array(
			'headers'   => $upload_headers,
			'body'      => $file_content,
			'timeout'   => EPKB_AI_Utilities::get_timeout_for_purpose( 'file_upload' ),
			'sslverify' => true
		) );

		if ( is_wp_error( $upload_response ) ) {
			EPKB_AI_Log::add_log( 'File upload failed', array( 'error' => $upload_response->get_error_message() ) );
			return $upload_response;
		}

		return $this->parse_response( $upload_response );
	}

	/**
	 * Build request headers
	 * @return array
	 */
	private function build_headers() {
		
		return array(
			'Content-Type'  => 'application/json',
			'x-goog-api-key' => self::get_api_key(),
			'User-Agent'    => 'Echo-Knowledge-Base/' . \Echo_Knowledge_Base::$version
		);
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
		$retry_after = wp_remote_retrieve_header( $response, 'retry-after' );

		// Handle WP_Error response
		if ( is_wp_error( $response ) ) {
			$error_data = $this->build_error_data( $status_code, $response_message, $body, '', array(), array( 'retry_after' => $retry_after ) );
			$response->add_data( $error_data );
			return $response;
		}

		// Try to decode JSON response
		$data = json_decode( $body, true );
		$is_json = json_last_error() === JSON_ERROR_NONE;

		// Handle success responses
		if ( $status_code >= 200 && $status_code < 300 ) {
			if ( ! $is_json ) {
				$error_data = $this->build_error_data( $status_code, $response_message, $body );
				return new WP_Error( 'invalid_json', 'AI ERROR: Invalid JSON in success response', $error_data );
			}
			return $data;
		}
		
		// Extract error message and code from JSON if available
		$error_message = '';
		$error_status = '';
		$error_code = '';
		if ( $is_json && isset( $data['error'] ) ) {
			if ( isset( $data['error']['message'] ) ) {
				$error_message = 'AI ERROR: ' . $data['error']['message'];
			} elseif ( is_string( $data['error'] ) ) {
				$error_message = 'AI ERROR: ' . $data['error'];
			}

			if ( isset( $data['error']['status'] ) ) {
				$error_status = strtolower( $data['error']['status'] );
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

		$normalized_error_code = ! empty( $error_status ) ? $error_status : ( $error_code !== '' ? strtolower( (string) $error_code ) : '' );
		$additional_data = array();
		if ( ! empty( $retry_after ) ) {
			$additional_data['retry_after'] = $retry_after;
		}
		if ( ! empty( $error_status ) ) {
			$additional_data['error_status'] = $error_status;
		}

		$error_data = $this->build_error_data( $status_code, $response_message, $body, $normalized_error_code, array(), $additional_data );
		$wp_error_code = $this->map_gemini_error_code( $status_code, $normalized_error_code, $error_message );

		return new WP_Error( $wp_error_code, $error_message, $error_data );
	}

	/**
	 * Map Gemini error status/HTTP code to internal error codes based on API return codes
	 *
	 * @param int $status_code
	 * @param string $error_status
	 * @param string $error_message
	 * @return string
	 */
	private function map_gemini_error_code( $status_code, $error_status, $error_message ) {

		switch ( $error_status ) {
			case 'invalid_argument':
			case 'failed_precondition':
			case 'out_of_range':
				return 'bad_request';

			case 'unauthenticated':
				return 'invalid_api_key';

			case 'permission_denied':
				return 'authentication_failed';

			case 'not_found':
				return 'not_found';

			case 'already_exists':
			case 'aborted':
				return 'version_conflict';

			case 'resource_exhausted':
				return stripos( $error_message, 'quota' ) !== false ? 'insufficient_quota' : 'rate_limit_exceeded';

			case 'deadline_exceeded':
				return 'timeout';

			case 'unavailable':
				return 'service_unavailable';

			case 'internal':
			case 'data_loss':
			case 'unknown':
				return 'server_error';

			case 'unimplemented':
			case 'cancelled':
				return 'api_error';
		}

		switch ( $status_code ) {
			case 400:
				return 'bad_request';

			case 401:
			case 403:
				return 'authentication_failed';

			case 404:
				return 'not_found';

			case 408:
			case 504:
				return 'timeout';

			case 409:
				return 'version_conflict';

			case 429:
				return 'rate_limit_exceeded';

			case 500:
			case 502:
				return 'server_error';

			case 503:
				return 'service_unavailable';
		}

		return 'api_error';
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
	 * Get API key from configuration
	 *
	 * @return string
	 */
	public static function get_api_key() {
		return EPKB_AI_Provider::get_api_key( EPKB_AI_Provider::PROVIDER_GEMINI );
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
	 * Test connection to Gemini API by listing models
	 *
	 * @return true|WP_Error True if connection is successful, WP_Error on failure
	 */
	public function test_connection() {
		// Try to list models as a simple test
		$response = $this->request( '/models', array(), 'GET' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['models'] ) || ! is_array( $response['models'] ) ) {
			return new WP_Error( 'invalid_response', __( 'GEMINI ERROR: Invalid response from Gemini API', 'echo-knowledge-base' ) );
		}

		return true;
	}

	/**
	 * Generate content with file search grounding (RAG)
	 *
	 * Uses the Gemini generateContent endpoint with file_search tool for
	 * retrieval-augmented generation from documents in a File Search Store.
	 *
	 * @param string $model Model name (e.g., 'gemini-2.5-flash')
	 * @param string $prompt User prompt/question
	 * @param string $store_id File Search Store ID
	 * @param array $options Additional options (system_instruction, model_parameters, etc.)
	 * @return array|WP_Error Response data or error
	 */
	public function generate_content_with_file_search( $model, $prompt, $store_id, $options = array() ) {

		if ( empty( $model ) || empty( $prompt ) || empty( $store_id ) ) {
			return new WP_Error( 'missing_params', __( 'Model, prompt, and store ID are required', 'echo-knowledge-base' ) );
		}

		// Ensure store_id has the full resource name format
		$store_resource_name = strpos( $store_id, 'fileSearchStores/' ) === 0 ? $store_id : 'fileSearchStores/' . $store_id;

		// Build contents array - include conversation history if provided
		$contents = $this->build_contents_with_history( $prompt, $options );

		// Build the request body for generateContent with file_search tool
		$request_body = array(
			'contents' => $contents,
			'tools' => array(
				array(
					'file_search' => array(
						'file_search_store_names' => array( $store_resource_name )
					)
				)
			)
		);

		// Add system instruction if provided
		if ( ! empty( $options['system_instruction'] ) ) {
			$request_body['system_instruction'] = array(
				'parts' => array(
					array( 'text' => $options['system_instruction'] )
				)
			);
		}

		$model_parameters = isset( $options['model_parameters'] ) && is_array( $options['model_parameters'] ) ? $options['model_parameters'] : array();
		$request_body = EPKB_Gemini_Model_Catalog::apply_model_parameters( $request_body, $model, $model_parameters );

		// Make request to generateContent endpoint
		$endpoint = '/models/' . $model . ':generateContent';
		return $this->request( $endpoint, $request_body, 'POST', 'search' );
	}

	/**
	 * Extract a Gemini thought signature from the first candidate.
	 *
	 * @param array $response
	 * @return string
	 */
	public static function extract_thought_signature( $response ) {

		if ( empty( $response['candidates'][0]['content']['parts'] ) || ! is_array( $response['candidates'][0]['content']['parts'] ) ) {
			return '';
		}

		$thought_signature = '';
		foreach ( $response['candidates'][0]['content']['parts'] as $part ) {
			if ( empty( $part['thoughtSignature'] ) || ! is_string( $part['thoughtSignature'] ) ) {
				continue;
			}

			$thought_signature = $part['thoughtSignature'];
		}

		return $thought_signature;
	}

	/**
	 * Extract the raw response parts from the first candidate.
	 *
	 * @param array $response
	 * @return array
	 */
	public static function extract_response_parts( $response ) {

		if ( empty( $response['candidates'][0]['content']['parts'] ) || ! is_array( $response['candidates'][0]['content']['parts'] ) ) {
			return array();
		}

		return $response['candidates'][0]['content']['parts'];
	}

	/**
	 * Build contents array with conversation history for multi-turn conversations
	 *
	 * Gemini API requires all previous messages to be included in the contents array
	 * for multi-turn conversations (unlike ChatGPT which uses previous_response_id).
	 *
	 * @param string $current_prompt Current user message
	 * @param array $options Options array containing 'conversation_history' key
	 * @return array Contents array for Gemini API
	 */
	private function build_contents_with_history( $current_prompt, $options = array() ) {

		$contents = array();
		$conversation_history = isset( $options['conversation_history'] ) ? $options['conversation_history'] : array();

		// Add previous messages from conversation history
		if ( ! empty( $conversation_history ) ) {
			foreach ( $conversation_history as $message ) {
				if ( empty( $message['content'] ) ) {
					continue;
				}

				// Skip error stubs from failed prior turns so the model is not told it previously said the fallback text
				if ( ! empty( $message['metadata']['error'] ) ) {
					continue;
				}

				// Map 'assistant' role to 'model' for Gemini API
				$role = isset( $message['role'] ) && $message['role'] === 'assistant' ? 'model' : 'user';

				$parts = array(
					array( 'text' => $message['content'] )
				);
				if ( $role === 'model' ) {
					$stored_parts = empty( $message['metadata']['gemini_parts'] ) || ! is_array( $message['metadata']['gemini_parts'] ) ? array() : $message['metadata']['gemini_parts'];
					if ( ! empty( $stored_parts ) ) {
						$parts = $stored_parts;
					} else {
						$thought_signature = empty( $message['metadata']['thought_signature'] ) ? '' : strval( $message['metadata']['thought_signature'] );
						if ( ! empty( $thought_signature ) ) {
							$parts[0]['thoughtSignature'] = $thought_signature;
						}
					}
				}

				$contents[] = array(
					'role' => $role,
					'parts' => $parts
				);
			}
		}

		// Add current user message
		$contents[] = array(
			'role' => 'user',
			'parts' => array(
				array( 'text' => $current_prompt )
			)
		);

		return $contents;
	}
}
