<?php

/**
 * Log errors into a database table for later analysis
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Logging {

	const LOGGING_OPTION_NAME =  'epkb_error_log';
	const MAX_NOF_LOGS_STORED = 30;

	private static $report_on_error = true;

	/**
	 * Add a new log entry to the log stored in the WP options table
	 *
	 * @param $error_message
	 * @param string $param1 - error OR parameter
	 * @param null $param2 - if param1 is parameter then this is error and other way around
	 */
	public static function add_log( $error_message, $param1='', $param2=null ) {
		global $eckb_kb_id;

		// do not log anything if not in the back-end or not logged in as an admin
		if ( ! self::can_log_message() ) {
			// FUTURE if needed for early logging: self::store_message( $error_message, $param1, $param2 );
			return;
		}

		// switch $variable and $wp_error if caller switched them by mistake
		$wp_error = is_wp_error( $param2 ) ? $param2 : ( is_wp_error( $param1 ) ? $param1 : null );
		$variable = is_wp_error( $param1 ) ? ( is_wp_error( $param2 ) ? '[]' : $param2 ) : $param1;
		$variable = EPKB_Utilities::get_variable_string( $variable );

		// prepare error message
		$error_message .= ' ' . $variable;

		if ( $wp_error instanceof  WP_Error ) {
			$error_message .= ' WP Error: ' . $wp_error->get_error_message();
		}

		$error_message = trim( sanitize_text_field( $error_message ) );
		if ( empty($error_message) ) {
			return;
		}

		// retrieve current logs
		$error_log = self::get_logs();

		// prepare error message
		$error_message = EPKB_Utilities::substr( $error_message, 0, 3000);
		$serialized_error_message = serialize( $error_message ); //serialize(base64_encode( $error_message ) );
		$unserialized_error_message = unserialize( $serialized_error_message ); //base64_decode(unserialize( $serialized_error_message ) );
		if ( $unserialized_error_message != $error_message ) {
			$error_message = "can't serialize error message:" . preg_replace('/[^A-Za-z0-9\-]/', '.', $error_message);
		}

		// prepare error stack trace
		$stack_trace = self::generateStackTrace();

		$kb_id = isset($eckb_kb_id) && ! empty($eckb_kb_id) ? $eckb_kb_id : '';
		$kb_id = EPKB_Utilities::is_multiple_kbs_enabled() ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		// add new error log entry but remove oldest one if more than max
		$error_log[] = array( 'plugin' => 'EPKB', 'kb' => $kb_id, 'date' => date("Y-m-d H:i:s"), 'message' => $error_message, 'trace' => $stack_trace );

		if ( count($error_log) > self::MAX_NOF_LOGS_STORED ) {
			array_shift($error_log);
		}

		// save the error log
		update_option( self::LOGGING_OPTION_NAME, $error_log );
	}

	/**
	 * Get stored logs
	 *
	 * @return array return logs or false if logs cannot be serialized
	 */
	public static function get_logs() {
		$logs = get_option( self::LOGGING_OPTION_NAME, array() );
		$logs = is_array($logs) ? $logs : array();
		return $logs;
	}

	/**
	 * Remove stored logs
	 */
	public static function reset_logs() {
		delete_option( self::LOGGING_OPTION_NAME );
	}

	/**
	 * Do not log anything if not in the back-end or not logged in as an admin
	 *
	 * @return bool
	 */
	private static function can_log_message() {

		// we cannot log too early
		if ( ! function_exists('wp_get_current_user') ) {
			return false;
		}

		// sometimes we expect errors
		if ( ! self::$report_on_error ) {
			return false;
		}

		$is_debug_on = get_transient( EPKB_Debug_Controller::EPKB_DEBUG );
        return  ! empty($is_debug_on) && current_user_can( 'manage_options' );
	}

	public static function disable_logging() {
		self::$report_on_error = false;
	}

	public static function enable_logging() {
		self::$report_on_error = true;
	}

	public static function generateStackTrace()	{
		$msg = "\tStack Trace:\n";
		$stackMsg = "";

		foreach( debug_backtrace() as $trace ) {

			$file = isset($trace['file']) ? $trace['file'] : '';
			$file = EPKB_Utilities::substr($file, 1, 500);

			$function = isset($trace['function']) ? $trace['function'] : '[unknown]';
			$class = isset($trace['class']) ? $trace['class'] . $trace['type'] : '';
			$line_number = isset($trace['line']) ? $trace['line'] : '-';
			$line = $file . '[' . $line_number . '] - ' . $class . $function ;

			if ( strpos($line, 'generateStackTrace') !== false || strpos($line, 'add_log_now') !== false) {
				continue;
			}
			
			// add hooks names 
			if ( isset($trace['class']) && isset($trace['function']) && $trace['class'] != 'WP_Hook' && strpos($line, 'class-wp-hook.php') !== false) {
				//  we run some class with function via hook and want to find the function that was called from some class 
				$r_class = new ReflectionClass($trace['class']);

				if ( ! function_exists($trace['function']) ) {
					continue;
				}

				$method = $r_class->getMethod($trace['function']);
				$line = $method->getFileName() . ' [' . $method->getStartLine() . "] - " . $function;
				
			} else if ( strpos($line, 'admin-ajax.php') !== false && ! empty($trace['args']) && is_string( $trace['args'][0] ) ) {
				// add ajax start point 
				$line .= ' (' . $trace['args'][0] . ')';
			}
			$stackMsg .= "\t" . $line . "\n";
		}

		$stackMsg = empty($stackMsg) ? '' : $msg . $stackMsg;
		$stackMsg = str_replace('\\', '/', $stackMsg);
		$stackMsg = EPKB_Utilities::substr( $stackMsg, 0, 2000);

		$serialized_stackMsg = serialize( $stackMsg ); //serialize(base64_encode( $stackMsg ) );
		$unserialized_stackMsg = unserialize( $serialized_stackMsg ); //base64_decode(unserialize( $serialized_stackMsg ) );
		if ($unserialized_stackMsg != $stackMsg) {
			$stackMsg = "can't serialize stacktrace:" . preg_replace('/[^A-Za-z0-9\-]/', '.', $stackMsg);
		}

		return $stackMsg;
	}
}