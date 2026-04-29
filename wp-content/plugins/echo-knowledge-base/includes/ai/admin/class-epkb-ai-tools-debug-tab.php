<?php defined( 'ABSPATH' ) || exit();

/**
 * AI Tools Debug Sub Tab
 * 
 * Provides debug information including PHP error logs and AI logs
 */
class EPKB_AI_Tools_Debug_Tab {

	/**
	 * Constructor - register AJAX handlers
	 */
	public function __construct() {
		add_action( 'wp_ajax_epkb_ai_get_php_error_logs', array( __CLASS__, 'ajax_get_php_error_logs' ) );
		add_action( 'wp_ajax_epkb_ai_get_wp_error_logs', array( __CLASS__, 'ajax_get_wp_error_logs' ) );
		add_action( 'wp_ajax_epkb_ai_get_ai_logs', array( __CLASS__, 'ajax_get_ai_logs' ) );
		add_action( 'wp_ajax_epkb_ai_clear_ai_logs', array( __CLASS__, 'ajax_clear_ai_logs' ) );
		// Enqueue scripts hook removed - now handled by React component
	}

	
	/**
	 * AJAX handler to get PHP error logs
	 */
	public static function ajax_get_php_error_logs() {
		

		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'echo-knowledge-base' ) ) );
			return;
		}
		
		$filter = sanitize_text_field( $_POST['filter'] ?? '' );
		$logs = self::get_php_error_logs( $filter );
		
		wp_send_json_success( array( 'logs' => $logs ) );
	}
	
	/**
	 * AJAX handler to get WP error logs
	 */
	public static function ajax_get_wp_error_logs() {

		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'echo-knowledge-base' ) ) );
			return;
		}
		
		$filter = sanitize_text_field( $_POST['filter'] ?? '' );
		$logs = self::get_wp_error_logs( $filter );
		
		wp_send_json_success( array( 'logs' => $logs ) );
	}
	
	/**
	 * AJAX handler to get AI logs
	 */
	public static function ajax_get_ai_logs() {

		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'echo-knowledge-base' ) ) );
			return;
		}
		
		$filter = sanitize_text_field( $_POST['filter'] ?? '' );
		$logs = self::get_ai_logs( $filter );
		
		wp_send_json_success( array( 'logs' => $logs ) );
	}
	
	/**
	 * AJAX handler to clear AI logs
	 */
	public static function ajax_clear_ai_logs() {

		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'echo-knowledge-base' ) ) );
			return;
		}
		
		// Clear logs
		EPKB_AI_Log::reset_logs();
		
		wp_send_json_success( array( 'message' => __( 'AI logs cleared successfully', 'echo-knowledge-base' ) ) );
	}
	
	/**
	 * Get PHP error logs
	 *
	 * @param string $filter
	 * @return array
	 */
	private static function get_php_error_logs( $filter = '' ) {
		$logs = array();

		// Get the error log path from PHP configuration
		$error_log_path = ini_get( 'error_log' );

		// If no error_log is configured or it's syslog, we can't read it
		if ( empty( $error_log_path ) || $error_log_path === 'syslog' ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				'message' => __( 'PHP error log is not configured or is set to syslog. Check your PHP configuration.', 'echo-knowledge-base' )
			) );
		}

		// Check if the log file exists
		if ( ! file_exists( $error_log_path ) ) {
			$configured_path = ini_get( 'error_log' );
			// translators: %1$s is the expected error log path, %2$s is the configured PHP error_log path
			$message = sprintf(
				__( 'Error log file not found at: %1$s. PHP error_log is configured as: %2$s. Enable WP_DEBUG and WP_DEBUG_LOG in wp-config.php or check your PHP error_log configuration.', 'echo-knowledge-base' ),
				$error_log_path,
				! empty( $configured_path ) ? $configured_path : __( 'Not configured', 'echo-knowledge-base' )
			);
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				'message' => $message
			) );
		}

		if ( ! is_readable( $error_log_path ) ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'warning',
				'message' => __( 'Error log file exists but is not readable. Check file permissions.', 'echo-knowledge-base' )
			) );
		}

		// Check if file is empty
		$file_size = filesize( $error_log_path );
		if ( $file_size === 0 ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				'message' => __( 'Error log file is empty. No PHP errors have been logged.', 'echo-knowledge-base' )
			) );
		}

		// Read last 100 lines of error log
		$lines = self::tail( $error_log_path, 100 );

		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( empty( $line ) ) {
				continue;
			}

			// Parse log line
			$parsed = self::parse_php_log_line( $line );
			if ( ! $parsed ) {
				continue;
			}

			// Apply filter
			if ( $filter ) {
				if ( $filter === 'ai-related' && stripos( $line, 'epkb' ) === false && stripos( $line, 'ai' ) === false ) {
					continue;
				} elseif ( $filter !== 'ai-related' && $filter !== $parsed['level'] ) {
					continue;
				}
			}

			// Escape log data for display
			$parsed['timestamp'] = esc_html( $parsed['timestamp'] );
			$parsed['level'] = esc_html( $parsed['level'] );
			$parsed['message'] = esc_html( $parsed['message'] );

			$logs[] = $parsed;
		}

		// Return most recent first
		return array_reverse( $logs );
	}
	
	/**
	 * Get WordPress error logs
	 *
	 * @param string $filter
	 * @return array
	 */
	private static function get_wp_error_logs( $filter = '' ) {
		$logs = array();

		// Check if WP_DEBUG_LOG is enabled
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				'message' => __( 'WordPress debug logging is disabled. Enable WP_DEBUG and WP_DEBUG_LOG in wp-config.php to see WordPress errors.', 'echo-knowledge-base' )
			) );
		}

		// Get WordPress debug.log path
		$wp_error_log_path = WP_CONTENT_DIR . '/debug.log';

		// Check if the log file exists
		if ( ! file_exists( $wp_error_log_path ) ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				// translators: %s is the path to the WordPress debug log file
				'message' => sprintf(
					__( 'WordPress debug log file not found at: %s. The file will be created when WordPress encounters errors.', 'echo-knowledge-base' ),
					$wp_error_log_path
				)
			) );
		}

		if ( ! is_readable( $wp_error_log_path ) ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'warning',
				'message' => __( 'WordPress debug log file exists but is not readable. Check file permissions.', 'echo-knowledge-base' )
			) );
		}

		// Check if file is empty
		$file_size = filesize( $wp_error_log_path );
		if ( $file_size === 0 ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				'message' => __( 'WordPress debug log file is empty. No WordPress errors have been logged.', 'echo-knowledge-base' )
			) );
		}

		// Read last 100 lines of WordPress debug log
		$lines = self::tail( $wp_error_log_path, 100 );

		// If no lines were read, return message
		if ( empty( $lines ) ) {
			return array( array(
				'timestamp' => current_time( 'mysql' ),
				'level' => 'info',
				'message' => __( 'Could not read any lines from the WordPress debug log file.', 'echo-knowledge-base' )
			) );
		}

		$unparsed_lines = array(); // Track lines that couldn't be parsed

		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( empty( $line ) ) {
				continue;
			}

			// Parse log line
			$parsed = self::parse_php_log_line( $line );
			if ( ! $parsed ) {
				// If we can't parse it, add it as raw log entry with escaping
				$logs[] = array(
					'timestamp' => current_time( 'mysql' ),
					'level' => 'info',
					'message' => esc_html( $line )
				);
				continue;
			}

			// Apply filter
			if ( $filter ) {
				if ( $filter === 'ai-related' && stripos( $line, 'epkb' ) === false && stripos( $line, 'ai' ) === false ) {
					continue;
				} elseif ( $filter !== 'ai-related' && $filter !== $parsed['level'] ) {
					continue;
				}
			}

			// Escape log data for display
			$parsed['timestamp'] = esc_html( $parsed['timestamp'] );
			$parsed['level'] = esc_html( $parsed['level'] );
			$parsed['message'] = esc_html( $parsed['message'] );

			$logs[] = $parsed;
		}

		// Return most recent first
		return array_reverse( $logs );
	}
	
	/**
	 * Get AI logs
	 *
	 * @param string $filter
	 * @return array
	 */
	private static function get_ai_logs( $filter = '' ) {
		$logs = EPKB_AI_Log::get_logs_for_display();
		$formatted_logs = array();

		foreach ( $logs as $log ) {
			// Determine log level
			$level = 'info';
			if ( isset( $log['context']['error_code'] ) || stripos( $log['message'], 'error' ) !== false ) {
				$level = 'error';
			} elseif ( stripos( $log['message'], 'warning' ) !== false || stripos( $log['message'], 'retry' ) !== false ) {
				$level = 'warning';
			}

			// Apply filter
			if ( $filter && $filter !== $level ) {
				continue;
			}

			// Escape message for display
			$escaped_message = isset( $log['message'] ) ? esc_html( $log['message'] ) : '';

			// Context values are not escaped here because they're already sanitized
			// by EPKB_AI_Log and will be safely rendered by React in the frontend

			$formatted_logs[] = array(
				'timestamp' => isset( $log['timestamp'] ) ? esc_html( $log['timestamp'] ) : current_time( 'mysql' ),
				'level' => esc_html( $level ),
				'message' => $escaped_message,
				'context' => isset( $log['context'] ) && is_array( $log['context'] ) ? $log['context'] : array()
			);
		}

		// Return most recent first
		return array_reverse( $formatted_logs );
	}
	
	/**
	 * Check if running on localhost
	 *
	 * @return bool
	 */
	private static function is_localhost() {
		$site_url = get_site_url();
		return ( strpos( $site_url, 'localhost' ) !== false || 
			strpos( $site_url, '127.0.0.1' ) !== false ||
			strpos( $site_url, '.local' ) !== false ||
			strpos( $site_url, '.test' ) !== false );
	}
	
	/**
	 * Parse PHP log line
	 *
	 * @param string $line
	 * @return array|false
	 */
	private static function parse_php_log_line( $line ) {
		// Match standard PHP error format: [timestamp] PHP Level: message
		$pattern = '/^\[([^\]]+)\]\s+PHP\s+(\w+(?:\s+\w+)?):?\s+(.+)$/';
		if ( preg_match( $pattern, $line, $matches ) ) {
			$level = strtolower( $matches[2] );
			
			// Normalize level names
			if ( strpos( $level, 'fatal' ) !== false ) {
				$level = 'fatal';
			} elseif ( strpos( $level, 'warning' ) !== false ) {
				$level = 'warning';
			} elseif ( strpos( $level, 'notice' ) !== false ) {
				$level = 'notice';
			} elseif ( strpos( $level, 'deprecated' ) !== false ) {
				$level = 'deprecated';
			} else {
				$level = 'error';
			}
			
			return array(
				'timestamp' => $matches[1],
				'level' => $level,
				'message' => $matches[3]
			);
		}
		
		// Try alternative format without PHP prefix
		$pattern = '/^\[([^\]]+)\]\s+(.+)$/';
		if ( preg_match( $pattern, $line, $matches ) ) {
			return array(
				'timestamp' => $matches[1],
				'level' => 'info',
				'message' => $matches[2]
			);
		}
		
		return false;
	}
	
	/**
	 * Read last N lines from a file
	 *
	 * @param string $filepath
	 * @param int $lines
	 * @return array
	 */
	private static function tail( $filepath, $lines = 100 ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Reading log file for debugging, requires seek operations
		$handle = @fopen( $filepath, "r" );
		if ( ! $handle ) {
			return array();
		}
		
		// Get file size
		$file_size = filesize( $filepath );
		
		// If file is small, just read all lines
		if ( $file_size < 8192 ) { // 8KB
			$all_lines = file( $filepath, FILE_IGNORE_NEW_LINES );
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			// Remove completely empty lines but keep lines with whitespace
			$all_lines = array_filter( $all_lines, function( $line ) {
				return $line !== '';
			} );
			if ( count( $all_lines ) <= $lines ) {
				return $all_lines;
			}
			return array_slice( $all_lines, -$lines );
		}
		
		// For larger files, use the original method
		$linecounter = $lines;
		$pos = -2;
		$beginning = false;
		$text = array();
		
		// Start from a reasonable position (not too far back)
		$start_pos = min( $file_size, $lines * 200 ); // Assume average line length of 200 chars
		if ( $start_pos < $file_size ) {
			fseek( $handle, -$start_pos, SEEK_END );
		}
		
		while ( $linecounter > 0 ) {
			$t = " ";
			while ( $t != "\n" ) {
				if ( fseek( $handle, $pos, SEEK_END ) == -1 ) {
					$beginning = true;
					break;
				}
				$t = fgetc( $handle );
				$pos--;
			}
			$linecounter--;
			if ( $beginning ) {
				rewind( $handle );
			}
			$text[$lines - $linecounter - 1] = @fgets( $handle );
			if ( $beginning ) {
				break;
			}
		}
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		
		// Filter out only completely empty lines (not lines with just whitespace) and reverse
		$text = array_filter( $text, function( $line ) {
			return $line !== null && $line !== false && $line !== '';
		} );
		
		return array_reverse( $text );
	}
}