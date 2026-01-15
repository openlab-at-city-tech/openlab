<?php

/**
 * AI Error Codes
 * 
 * Provides a centralized system for error codes and their corresponding messages
 * to avoid storing redundant generic messages in the database.
 */
class EPKB_AI_Log {

	const EPKB_AI_LOGS_OPTION_NAME = 'epkb_ai_logs';

	/**
	 * Add a log entry
	 *
	 * @param WP_Error|string $message The log message or WP_Error object
	 * @param array $context The log context - key-value pairs: message, error_code, error_data, request_endpoint, model, elapsed_seconds, timeout_seconds, attempt
	 */
	 public static function add_log( $message, $context = array() ) {

		// Security: Only log if user has appropriate permissions or it's a system event
		if ( !self::should_log() ) {
			return;
		}

		$max_logs = 50; // FIFO - when limit reached, oldest log is removed
		$max_message_length = 800;
		$max_context_size = 2000; // Increased to capture full OpenAI error responses

		// Handle WP_Error objects
		if ( is_wp_error( $message ) ) {
			// If context is not an array, make it an array
			if ( !is_array( $context ) ) {
				$context = array();
			}

			// Extract error details
			$error_message = $message->get_error_message();
			$error_code = $message->get_error_code();
			$error_data = $message->get_error_data();

			// Add error details to context
			$context['error_code'] = $error_code;
			if ( !empty( $error_data ) ) {
				$context['error_data'] = $error_data;
			}

			// Use error message as the main message
			$message = $error_message;
		}

		// Strip tags but keep raw characters for logging (escaping happens on display)
		$message = wp_strip_all_tags( $message );

		// Truncate message if too long
		if ( strlen( $message ) > $max_message_length ) {
			$message = substr( $message, 0, $max_message_length - 3 ) . '...';
		}

		// Sanitize and limit context
		$context = self::sanitize_log_context( $context, $max_context_size );

		// Get existing logs
		$logs = get_option( self::EPKB_AI_LOGS_OPTION_NAME, array() );

		// Create log entry with minimal information
		$log_entry = array(
			'timestamp' => gmdate( 'Y-m-d H:i:s' ),
			'date' => current_time( 'Y-m-d' ),
			'message' => $message,
			'context' => $context,
			'hash' => substr( md5( $message . serialize( $context ) ), 0, 8 ) // For deduplication
		);

		// Check for sequential duplicate (same hash as the last log entry)
		if ( !empty( $logs ) ) {
			$last_log = end( $logs );
			if ( isset( $last_log['hash'] ) && $last_log['hash'] === $log_entry['hash'] ) {
				array_pop( $logs ); // Remove the older duplicate
			}
		}

		// Add new log entry
		$logs[] = $log_entry;

		// Enforce FIFO: keep only the most recent logs
		if ( count( $logs ) > $max_logs ) {
			$logs = array_slice( $logs, -$max_logs );
		}

		// Update option with autoload disabled for performance
		update_option( self::EPKB_AI_LOGS_OPTION_NAME, $logs, false );
	}

	/**
	 * Get logs formatted for display in admin interface
	 *
	 * IMPORTANT: This method returns sanitized but NOT escaped data.
	 * Callers MUST escape log messages and context values using esc_html()
	 * or appropriate escaping functions before outputting to HTML.
	 *
	 * @return array Array of log entries with sanitized data
	 */
	public static function get_logs_for_display() {

		if ( !current_user_can( 'manage_options' ) ) {
			return array();
		}

		$logs = get_option( self::EPKB_AI_LOGS_OPTION_NAME, array() );

		// Additional filtering for display
		return array_map( function ( $log ) {
			// Ensure no sensitive data is exposed
			if ( isset( $log['context'] ) && is_array( $log['context'] ) ) {
				$log['context'] = self::sanitize_log_context( $log['context'], 2000 );
			}
			return $log;
		}, $logs );
	}

	private static function should_log() {

		// Check if AI debug mode is enabled
		$ai_debug_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_tools_debug_enabled', 'off' );
		if ( $ai_debug_enabled === 'on' ) {
			return true;
		}

		// Only log if debugging is enabled or user is admin
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return true;
		}

		// Check if user has admin capabilities
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Sanitize log context to remove sensitive information
	 *
	 * @param array $context
	 * @param int $max_size
	 * @return array
	 */
	private static function sanitize_log_context( $context, $max_size ) {
		if ( !is_array( $context ) ) {
			return array();
		}

		// Remove sensitive keys
		$sensitive_keys = array(
			'password', 'pass', 'pwd', 'secret', 'token', 'key', 'api_key',
			'auth', 'authorization', 'cookie', 'session', 'nonce', 'salt'
		);

		// Special keys that should preserve more data for debugging
		$debug_keys = array( 'raw_response_body', 'raw_body' );

		// Keys that should preserve array data instead of converting to string
		$preserve_array_keys = array( 'error_data' );

		$sanitized = array();
		foreach ( $context as $key => $value ) {
			// Skip sensitive keys
			$lower_key = strtolower( $key );
			$skip = false;
			foreach ( $sensitive_keys as $sensitive ) {
				if ( strpos( $lower_key, $sensitive ) !== false ) {
					$sanitized[ $key ] = '[REDACTED]';
					$skip = true;
					break;
				}
			}

			if ( $skip ) {
				continue;
			}

			// Sanitize values
			if ( is_string( $value ) ) {
				// Allow debug keys to have more data (up to 1000 chars for full error responses)
				$max_length = in_array( $key, $debug_keys, true ) ? 1000 : 100;
				$sanitized[ $key ] = wp_strip_all_tags( substr( $value, 0, $max_length ) );
			} elseif ( is_numeric( $value ) ) {
				$sanitized[ $key ] = $value;
			} elseif ( is_bool( $value ) ) {
				$sanitized[ $key ] = $value;
			} elseif ( is_array( $value ) ) {
				// Preserve array data for specific keys
				if ( in_array( $key, $preserve_array_keys, true ) ) {
					$sanitized[ $key ] = self::sanitize_array_recursive( $value, 3 ); // Max depth of 3
				} else {
					$sanitized[ $key ] = '[Array with ' . count( $value ) . ' items]';
				}
			} else {
				$sanitized[ $key ] = '[' . gettype( $value ) . ']';
			}
		}

		// Check serialized size
		$serialized = serialize( $sanitized );
		if ( strlen( $serialized ) > $max_size ) {
			// Truncate to most important keys, including debug keys for OpenAI errors
			$important_keys = array( 'error_code', 'status', 'user_id', 'action', 'raw_response_body', 'response_code', 'request_endpoint' );
			$truncated = array();
			foreach ( $important_keys as $key ) {
				if ( isset( $sanitized[ $key ] ) ) {
					$truncated[ $key ] = $sanitized[ $key ];
				}
			}
			$truncated['truncated'] = true;
			return $truncated;
		}

		return $sanitized;
	}

	public static function reset_logs() {

		if ( !current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Delete log option
		delete_option( self::EPKB_AI_LOGS_OPTION_NAME );

		return true;
	}

	/**
	 * Get user-friendly error message with optional details for admins
	 * 
	 * @param string|WP_Error $error Error code or WP_Error object
	 * @return string User-friendly error message
	 */
	public static function get_user_friendly_message( $error ) {

		if ( is_wp_error( $error ) ) {
			$error_code = $error->get_error_code();
			$error_message = $error->get_error_message();
			$error_data = $error->get_error_data();
		} else {
			$error_code = $error;
			$error_message = 'Unknown error';
			$error_data = null;
		}
		
		switch ( $error_code ) {
			case 'authentication_failed':
				$friendly_message = __( 'Authentication failed. Please check your OpenAI API key in the General Settings.', 'echo-knowledge-base' );
				break;
				
			case 'rate_limit_exceeded':
				$retry_after = isset( $error_data['retry_after'] ) ? $error_data['retry_after'] : null;
				if ( $retry_after ) {
					$friendly_message = sprintf( 
						__( 'Rate limit exceeded. Please try again in %s seconds.', 'echo-knowledge-base' ),
						$retry_after
					);
				} else {
					$friendly_message = __( 'Rate limit exceeded. Please try again in a few minutes.', 'echo-knowledge-base' );
				}
				break;
				
			case 'server_error':
				$friendly_message = __( 'The AI service is temporarily unavailable. Please try again later.', 'echo-knowledge-base' );
				break;
				
			case 'timeout':
			case 'http_request_timeout':
				$friendly_message = __( 'The request timed out. This might be due to network issues or a long-running operation. Please try again.', 'echo-knowledge-base' );
				break;
				
			case 'content_too_large':
			case 'file_too_large':
				$friendly_message = __( 'The content is too large to process. Please try with smaller content.', 'echo-knowledge-base' );
				break;
				
			case 'vector_store_not_found':
				$friendly_message = __( 'The training data store was not found. Please check your Training Data settings.', 'echo-knowledge-base' );
				break;
				
			case 'invalid_api_key':
				$friendly_message = __( 'Invalid OpenAI API key. Please check your API key in the General Settings.', 'echo-knowledge-base' );
				break;
				
			case 'insufficient_quota':
				$friendly_message = __( 'Your OpenAI account has insufficient credits. Please check your OpenAI account billing.', 'echo-knowledge-base' );
				break;
				
			case 'user_state_changed':
				$friendly_message = __( 'Your login status has changed. Please start a new conversation.', 'echo-knowledge-base' );
				break;
				
			case 'user_mismatch':
				$friendly_message = __( 'You are not authorized to continue this chat session.', 'echo-knowledge-base' );
				break;
				
			case 'rest_cookie_invalid_nonce':
				$friendly_message = __( 'Your session has expired. Please refresh the page to continue.', 'echo-knowledge-base' );
				break;
				
			case 'validation_failed':
				// For validation errors, use the original error message as-is
				$friendly_message = $error_message;
				break;
				
			default:
				// For unknown errors, provide a generic message
				if ( strpos( $error_message, 'Invalid API key' ) !== false ) {
					$friendly_message = __( 'Invalid OpenAI API key. Please check your API key in the General Settings.', 'echo-knowledge-base' );
				} elseif ( strpos( $error_message, 'quota' ) !== false || strpos( $error_message, 'billing' ) !== false ) {
					$friendly_message = __( 'OpenAI account issue. Please check your OpenAI account status and billing.', 'echo-knowledge-base' );
				} else {
					$friendly_message = __( 'An error occurred while processing your request. Please try again.', 'echo-knowledge-base' );
				}
		}
		
		// Add technical details for admins (except for validation errors which already have clear messages)
		if ( current_user_can( 'manage_options' ) && $error_code !== 'validation_failed' ) {
			$technical_details = '';
			
			// Add error code if available
			if ( $error_code && $error_code !== 'unknown_error' ) {
				$technical_details .= sprintf( ' [Error Code: %s]', $error_code );
			}
			
			// Add original error message if different from friendly message
			if ( $error_message && $error_message !== $friendly_message && $error_message !== 'Unknown error' ) {
				$technical_details .= sprintf( ' [Details: %s]', esc_html( $error_message ) );
			}
			
			if ( $technical_details ) {
				$friendly_message .= $technical_details;
			}
		}
		
		return $friendly_message;
	}


	/*******************************************************************
	 * Utility functions
	 *******************************************************************/
	/**
	 * Recursively sanitize array values
	 *
	 * @param array $array Array to sanitize
	 * @param int $max_depth Maximum recursion depth
	 * @param int $current_depth Current recursion depth
	 * @return array|string Sanitized array or placeholder string if max depth exceeded
	 */
	private static function sanitize_array_recursive( $array, $max_depth = 3, $current_depth = 0 ) {
		if ( $current_depth >= $max_depth ) {
			return '[Array depth limit reached]';
		}

		if ( !is_array( $array ) ) {
			return $array;
		}

		$sanitized = array();
		foreach ( $array as $key => $value ) {
			if ( is_string( $value ) ) {
				$sanitized[ $key ] = wp_strip_all_tags( substr( $value, 0, 200 ) );
			} elseif ( is_numeric( $value ) || is_bool( $value ) ) {
				$sanitized[ $key ] = $value;
			} elseif ( is_array( $value ) ) {
				$sanitized[ $key ] = self::sanitize_array_recursive( $value, $max_depth, $current_depth + 1 );
			} elseif ( is_null( $value ) ) {
				$sanitized[ $key ] = null;
			} else {
				$sanitized[ $key ] = '[' . gettype( $value ) . ']';
			}
		}

		return $sanitized;
	}

	/**
	 * Map error to internal code and message
	 *
	 * Centralizes error mapping logic for consistent error handling.
	 * Takes a WP_Error or string error and returns the appropriate internal
	 * error code and message for storage.
	 *
	 * @param WP_Error $wp_error Error object or message
	 * @return array Array with 'code' and 'message' keys
	 */
	public static function map_error_to_internal_code( $wp_error ) {

		$error_message = $wp_error->get_error_message();
		$error_data = $wp_error->get_error_data();
		$error_code = empty( $error_data['response']['code'] ) ? 500 : $error_data['response']['code'];

		// Try to map to internal error code
		$internal_code = $wp_error->get_error_code();

		// If we have a valid internal code, use it instead of the full message except for validation errors where details are important
		if ( $internal_code && strpos( $internal_code, '_' ) !== false ) {
			if ( $internal_code === 'validation_failed' ) {
				// keep the detailed message
				$error_message = $error_message ?: $internal_code;
			} else {
				$error_message = $internal_code;
			}
		}
		if ( empty( $error_message ) ) {
			// Check if we have an internal error code for this HTTP status
			$status_code = self::get_code_for_http_status( $error_code );
			if ( $status_code ) {
				$error_message = $status_code;
			}
		}
		
		return array(
			'code' => $error_code,
			'message' => $error_message
		);
	}

	/**
	 * Normalize error message to use error codes when appropriate
	 *
	 * @param string $message
	 * @return string Error code or truncated message
	 */
	public static function normalize_error_message( $message ) {

		// Check if it's already an error code
		if ( strpos( $message, '_' ) !== false && strlen( $message ) < 30 ) {
			return $message;
		}

		// Truncate long messages
		if ( strlen( $message ) > 200 ) {
			$message = substr( $message, 0, 197 ) . '...';
		}

		return $message;
	}

	/**
	 * Process a WP_Error object and extract error data
	 *
	 * @param WP_Error $wp_error WP_Error object to process
	 * @param int $default_http_status_code Default status code if none is found
	 * @return array Array containing error data and status code
	 */
	public static function rest_process_wp_error( $wp_error, $default_http_status_code = 200 ) {
		if ( ! ( $wp_error instanceof WP_Error ) ) {
			return array(
				'data' => array(),
				'status' => $default_http_status_code
			);
		}

		$error_code = $wp_error->get_error_code();
		$error_data = $wp_error->get_error_data( $error_code );
		$http_status_code = $default_http_status_code;

		// Extract status from error data if available
		if ( is_array( $error_data ) && isset( $error_data['status'] ) ) {
			$http_status_code = (int) $error_data['status'];
		} elseif ( is_array( $error_data ) && isset( $error_data['status_code'] ) ) {
			$http_status_code = (int) $error_data['status_code'];
		} elseif ( is_array( $error_data ) && isset( $error_data['response']['code'] ) ) {
			$http_status_code = (int) $error_data['response']['code'];
		} elseif ( $default_http_status_code === 200 ) {
			// If no status provided and default is still 200, use appropriate error status
			$http_status_code = self::get_error_status_code( $error_code );
		}

		// Get all error messages and combine them
		$error_messages = implode( '; ', $wp_error->get_error_messages( $error_code ) );

		// Log the error for debugging
		EPKB_AI_Log::add_log( $wp_error, array(
			'endpoint' => isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : 'Unknown',
			'method' => isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'Unknown',
			'context' => 'REST API Error'
		) );

		// Get user-friendly message
		$friendly_message = EPKB_AI_Log::get_user_friendly_message( $wp_error );

		// For certain errors, provide different messages for admins vs regular users
		$user_message = $friendly_message;
		if ( $error_code === 'insufficient_quota' && ! current_user_can( 'manage_options' ) ) {
			// Show generic message to regular users/guests for quota errors
			$user_message = __( 'The service is temporarily unavailable. Please try again later.', 'echo-knowledge-base' );
		}

		// Build error response data with comprehensive error information
		$error_response = array(
			'success' => false,
			'status' => 'error',
			'error' => $error_code,
			'message' => $user_message,  // Use the appropriate user message
			'user_message' => $user_message,  // Explicit user message
			'admin_message' => '',                // Will be set below for admins
			'is_retryable' => self::is_retryable_error( $error_code, $http_status_code ),
			'error_type' => self::get_error_type( $error_code, $http_status_code )
		);

		// Include technical details for admins
		if ( current_user_can( 'manage_options' ) ) {
			// Build admin message with all technical details
			$admin_message_parts = array();

			// For quota errors, start with the friendly message for clarity
			if ( $error_code === 'insufficient_quota' ) {
				$admin_message_parts[] = $friendly_message;
			}

			// Add error type and status code
			if ( $http_status_code !== 200 ) {
				$admin_message_parts[] = sprintf( 'HTTP %d', $http_status_code );
			}

			// Add error code
			if ( $error_code && $error_code !== 'unknown_error' ) {
				$admin_message_parts[] = sprintf( 'Code: %s', $error_code );
			}

			// Add original error message (if different from friendly message and not already added for quota)
			$original_message = $error_messages ?: $wp_error->get_error_message();
			if ( $original_message && $original_message !== $friendly_message && $error_code !== 'insufficient_quota' ) {
				$admin_message_parts[] = $original_message;
			}

			$error_response['admin_message'] = implode( ' | ', $admin_message_parts );
		}

		// Include additional error data (like retry_after for rate limits)
		if ( is_array( $error_data ) ) {
			// Extract specific fields that client might need
			if ( isset( $error_data['retry_after'] ) ) {
				$error_response['retry_after'] = $error_data['retry_after'];
			}
			
			// Exclude internal status fields from details
			unset( $error_data['status'] );
			unset( $error_data['status_code'] );
			unset( $error_data['response'] );
			
			// Include remaining error data as details
			if ( ! empty( $error_data ) ) {
				$error_response['details'] = $error_data;
			}
		}

		return array(
			'data' => $error_response,
			'status' => $http_status_code
		);
	}

	/**
	 * Get appropriate HTTP status code for error code
	 *
	 * @param string $error_code
	 * @return int
	 */
	public static function get_error_status_code( $error_code ) {
		$status_map = array(
			'invalid_input'       => 400,
			'validation_failed'   => 400,
			'message_too_long'    => 400,
			'invalid_idempotency_key' => 400,
			'empty_message'       => 400,
			'invalid_content'     => 400,
			'conversation_limit_reached' => 400,
			'authentication_failed' => 401,
			'invalid_session'     => 401,
			'no_session'          => 401,
			'login_required'      => 401,
			'unauthorized'        => 403,
			'access_denied'       => 403,
			'ai_disabled'         => 403,
			'ai_chat_disabled'    => 403,
			'ai_search_disabled'  => 403,
			'not_found'           => 404,
			'conversation_not_found' => 404,
			'expired'             => 410,
			'conversation_expired' => 410,
			'rate_limit_exceeded' => 429,
			'user_rate_limit'     => 429,
			'global_rate_limit'   => 429,
			'insufficient_quota'  => 429,
			'version_conflict'    => 409,
			'server_error'        => 500,
			'db_error'           => 500,
			'save_failed'        => 500,
			'insert_failed'      => 500,
			'unexpected_error'   => 500,
			'service_unavailable' => 503,
			'empty_response'     => 503,
		);

		return isset( $status_map[ $error_code ] ) ? $status_map[ $error_code ] : 500;
	}


	/**
	 * Get error code for HTTP status
	 *
	 * @param int $http_status
	 * @return string|null
	 */
	public static function get_code_for_http_status( $http_status ) {
		$http_status_map = array(
			401 => 'authentication_failed',
			403 => 'authentication_failed',
			429 => 'rate_limit_exceeded',  // Default for 429; actual API error code takes precedence
			404 => 'not_found',
			400 => 'bad_request',
			408 => 'timeout',
			500 => 'server_error',
			502 => 'server_error',
			503 => 'server_error',
		);

		return isset( $http_status_map[ $http_status ] ) ? $http_status_map[ $http_status ] : null;
	}

	/**
	 * Determine if an error is retryable
	 *
	 * @param string $error_code
	 * @param int $http_status
	 * @return bool
	 */
	public static function is_retryable_error( $error_code, $http_status = null ) {
		// Network and server errors are generally retryable
		$retryable_codes = array(
			'server_error',
			'network_error',
			'timeout',
			'http_request_failed',
			'connection_error',
			'service_unavailable',
			'empty_response',
			'rate_limit_exceeded'  // Rate limits are retryable after appropriate delay
		);
		
		if ( in_array( $error_code, $retryable_codes, true ) ) {
			return true;
		}
		
		// Check by HTTP status code
		if ( $http_status !== null ) {
			// 5xx errors are generally retryable (server errors)
			if ( $http_status >= 500 && $http_status < 600 ) {
				return true;
			}
			// 408 Request Timeout is retryable
			if ( $http_status === 408 ) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Get error type based on error code and status
	 *
	 * @param string $error_code
	 * @param int $http_status
	 * @return string
	 */
	public static function get_error_type( $error_code, $http_status = null ) {
		// Map error codes to types
		$error_type_map = array(
			// Authentication errors
			'authentication_failed' => 'authentication',
			'invalid_session' => 'authentication',
			'no_session' => 'authentication',
			'login_required' => 'authentication',
			'rest_cookie_invalid_nonce' => 'authentication',
			
			// Authorization errors
			'unauthorized' => 'authorization',
			'access_denied' => 'authorization',
			'ai_disabled' => 'authorization',
			'ai_chat_disabled' => 'authorization',
			'ai_search_disabled' => 'authorization',
			
			// Rate limit errors
			'rate_limit_exceeded' => 'rate_limit',
			'user_rate_limit' => 'rate_limit',
			'global_rate_limit' => 'rate_limit',
			
			// Not found errors
			'not_found' => 'not_found',
			'conversation_not_found' => 'not_found',
			'vector_store_not_found' => 'not_found',
			
			// Timeout errors
			'timeout' => 'timeout',
			'http_request_timeout' => 'timeout',
			
			// Network errors
			'network_error' => 'network',
			'connection_error' => 'network',
			'http_request_failed' => 'network',
			
			// Server errors
			'server_error' => 'server_error',
			'db_error' => 'server_error',
			'save_failed' => 'server_error',
			'insert_failed' => 'server_error',
			'unexpected_error' => 'server_error',
			'service_unavailable' => 'server_error',
			'empty_response' => 'server_error',
			
			// Content errors
			'content_too_large' => 'content_error',
			'file_too_large' => 'content_error',
			'message_too_long' => 'content_error',
			'invalid_content' => 'content_error',
			'empty_message' => 'content_error',
			
			// Other errors
			'invalid_api_key' => 'configuration',
			'insufficient_quota' => 'quota',
			'version_conflict' => 'conflict',
			'expired' => 'expired',
			'conversation_expired' => 'expired',
			'conversation_limit_reached' => 'limit_reached'
		);
		
		// Check by error code first
		if ( isset( $error_type_map[ $error_code ] ) ) {
			return $error_type_map[ $error_code ];
		}
		
		// Fallback to HTTP status code
		if ( $http_status !== null ) {
			if ( $http_status === 401 || $http_status === 403 ) {
				return 'authentication';
			} elseif ( $http_status === 429 ) {
				return 'rate_limit';
			} elseif ( $http_status === 404 ) {
				return 'not_found';
			} elseif ( $http_status === 408 ) {
				return 'timeout';
			} elseif ( $http_status >= 500 && $http_status < 600 ) {
				return 'server_error';
			} elseif ( $http_status >= 400 && $http_status < 500 ) {
				return 'client_error';
			}
		}
		
		return 'unknown';
	}


}