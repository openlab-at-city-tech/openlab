<?php

/**
 * OpenAI Handler
 * 
 * Provides high-level operations for OpenAI API including vector stores and files.
 * Wraps the OpenAI client and vector store service for use by the sync manager.
 */
class EPKB_AI_OpenAI_Handler {

	/**
	 * OpenAI client
	 * @var EPKB_OpenAI_Client
	 */
	private $client;

	public function __construct() {
		$this->client = new EPKB_OpenAI_Client();
	}

	/**
	 * Calculate exponential backoff delay
	 *
	 * @param int $retry_count Current retry attempt (0-based)
	 * @param int $base_delay Base delay in seconds
	 * @param int $max_delay Maximum delay in seconds
	 * @param WP_Error|null $error Optional error object that may contain retry-after header
	 * @return int Delay in seconds
	 */
	public static function calculate_backoff_delay( $retry_count, $base_delay = 1, $max_delay = 60, $error = null ) {
		$calculated_delay = 0;
		$server_hint_delay = 0;
		
		// Calculate exponential backoff with jitter
		$exponential_delay = $base_delay * pow( 2, $retry_count );
		// Add jitter (0-25% of delay)
		$jitter = $exponential_delay * ( mt_rand( 0, 25 ) / 100 );
		$calculated_delay = $exponential_delay + $jitter;
		
		// Check for Retry-After header in error data (highest priority)
		if ( is_wp_error( $error ) ) {
			$error_data = $error->get_error_data();
			if ( ! empty( $error_data['retry_after'] ) ) {
				// Retry-After can be seconds or HTTP date
				$retry_after = $error_data['retry_after'];
				if ( is_numeric( $retry_after ) ) {
					// It's seconds
					$server_hint_delay = intval( $retry_after );
				} else {
					// It's an HTTP date, parse it
					$retry_time = strtotime( $retry_after );
					if ( $retry_time !== false ) {
						$server_hint_delay = max( 0, $retry_time - time() );
					}
				}
			}
		}
		
		// Check for X-RateLimit-Reset from transient (second priority)
		if ( $server_hint_delay === 0 ) {
			$rate_limit_info = get_transient( 'epkb_openai_rate_limit' );
			if ( ! empty( $rate_limit_info['reset_in'] ) && $rate_limit_info['remaining'] === 0 ) {
				// Use actual reset time from OpenAI headers
				$server_hint_delay = $rate_limit_info['reset_in'] + 1;
			}
		}
		
		// Use the maximum of calculated backoff and server hint
		$delay = max( $calculated_delay, $server_hint_delay );
		
		// Cap at max delay
		return min( $delay, $max_delay );
	}
	
	/**
	 * Check if OpenAI API error is retryable
	 * 
	 * This determines whether the server should retry a failed OpenAI API request.
	 * Note: This is different from EPKB_AI_Log::is_retryable_error() which determines
	 * if the client should retry a request to our REST API.
	 *
	 * @param WP_Error $error
	 * @return bool
	 */
	public static function is_retryable_error( $error ) {
		if ( ! is_wp_error( $error ) ) {
			return false;
		}
		
		$error_code = $error->get_error_code();
		$error_data = $error->get_error_data();
		$http_code = null;
		
		// Extract HTTP status code if available
		if ( isset( $error_data['response']['code'] ) ) {
			$http_code = $error_data['response']['code'];
		}
		
		// Use centralized logic from EPKB_AI_Log for consistency
		// But apply OpenAI-specific rules
		$is_retryable = EPKB_AI_Log::is_retryable_error( $error_code, $http_code );
		
		// OpenAI-specific overrides:
		// - Don't retry 401/403 auth errors (API key issues)
		if ( $http_code === 401 || $http_code === 403 ) {
			return false;
		}

		if ( $error_code === 'json_encode_error' ) {
			return false;
		}

		// - Don't retry if execution_time_too_low (won't be fixed by retrying)
		if ( $error_code === 'execution_time_too_low' ) {
			return false;
		}

		// - Don't retry insufficient_quota errors (billing issues) or invalid API key
		if ( $error_code === 'insufficient_quota' || $error_code === 'invalid_api_key' ) {
			return false;
		}
		
		// - Don't retry incomplete responses (won't be fixed by retrying)
		if ( $error_code === 'response_incomplete' ) {
			return false;
		}
		
		return $is_retryable;
	}

	/**
	 * Call OpenAI API to rewrite content
	 *
	 * @param string $instructions Instructions for the AI to follow
	 * @param string $original_content Original content
	 * @return string|WP_Error Rewritten content or error
	 */
	public static function call_openai_for_rewrite( $instructions, $original_content ) {

		// Calculate max tokens based on input length
		$input_length = strlen( $original_content );
		if ( $input_length > 5000 ) {
			$max_output_tokens = 800;
		} elseif ( $input_length > 2000 ) {
			$max_output_tokens = 500;
		} else {
			$max_output_tokens = 300;
		}

		// Prepare the request for Responses API
		$request = array(
			'model' => EPKB_OpenAI_Client::DEFAULT_MODEL,
			'instructions' => $instructions,
			'input' => array(
				array(
					'role' => 'user',
					'content' => $original_content
				)
			)
		);

		// Apply model-specific parameters using the generic method
		$params = array(
			'temperature' => 0.3, // Low temperature for consistency
			'max_output_tokens' => $max_output_tokens
		);
		$request = EPKB_OpenAI_Client::apply_model_parameters( $request, EPKB_OpenAI_Client::DEFAULT_MODEL, $params );

		// Make the API call
		$client = new EPKB_OpenAI_Client();
		$response = $client->request( '/responses', $request );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Extract the content from response (Responses API structure)
		if ( empty( $response['output'] ) || ! is_array( $response['output'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from AI service', 'echo-knowledge-base' ) );
		}

		// Get the last output item
		$last_output = end( $response['output'] );
		if ( empty( $last_output['content'] ) || ! is_array( $last_output['content'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response content from AI service', 'echo-knowledge-base' ) );
		}

		// Get the first content item
		$content = $last_output['content'][0];

		// If content is an object with a 'text' property, extract it
		if ( is_array( $content ) && isset( $content['text'] ) ) {
			$content = $content['text'];
		}

		return trim( $content );
	}

	/**
	 * Convert kb_article file references to markdown links with article titles
	 * This converts the file names we generate when uploading to OpenAI back to readable links
	 *
	 * @param string $content Content that may contain kb_article references
	 * @return string Content with references converted to links
	 */
	public static function convert_kb_article_references_to_links( $content ) {

		// Remove the †turn0file2 pattern and similar artifacts only when kb_article_ is found
		// Pattern matches optional † followed by turn, number, file, number
		$content = preg_replace('/†?turn\d+file\d+/u', '', $content);

		// Quick check - if content doesn't contain kb_article_, no need to process
		if ( strpos( $content, 'kb_article_' ) === false ) {
			return $content;
		}

		// Pattern: kb_article_[postId]_[timestamp].txt
		// This matches the format we use in upload_file() method
		$pattern = '/kb_article_(\d+)_\d+\.txt/';

		// Find all matches
		if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$file_reference = $match[0];
				$post_id = $match[1];

				// Get the post to retrieve its title
				$post = get_post( $post_id );
				if ( $post ) {
					// Get the article URL
					$article_url = get_permalink( $post_id );
					// Get the article title
					$article_title = get_the_title( $post );

					// Create HTML link that opens in new tab
					// Using HTML directly since marked.js passes through HTML
					$link = '<a href="' . esc_url( $article_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $article_title ) . '</a>';

					// Replace the file reference with the link
					$content = str_replace( $file_reference, $link, $content );
				}
			}
		}

		return $content;
	}
}