<?php defined( 'ABSPATH' ) || exit();

/**
 * AI Tools Tab
 * 
 * Provides tools for debugging and monitoring AI functionality
 */
class EPKB_AI_Tools_Tab {

	/**
	 * Constructor - register AJAX handlers
	 */
	public function __construct() {
		add_action( 'wp_ajax_epkb_ai_toggle_debug_mode', array( __CLASS__, 'ajax_toggle_debug_mode' ) );
		add_action( 'wp_ajax_epkb_ai_get_data_collections_info', array( __CLASS__, 'ajax_get_data_collections_info' ) );
		add_action( 'admin_init', array( __CLASS__, 'download_ai_debug_info' ) );
	}

	/**
	 * Get tab configuration
	 *
	 * @return array
	 */
	public static function get_tab_config() {

		// Get debug enabled status
		$debug_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_tools_debug_enabled', 'off' );
		
		// Check if localhost and enable by default if not set
		if ( !self::is_localhost() && $debug_enabled === 'off' ) {
			return [];
		}
		
		$config = array(
			'tab_id' => 'tools',
			'title' => __( 'Tools', 'echo-knowledge-base' ),
			'settings_sections' => self::get_settings_sections(),
			'active_sub_tab' => 'debug',
			// Additional data for React component
			'debug_enabled' => $debug_enabled,
			'system_info' => self::get_system_info(),
			'data_collections' => self::get_data_collections_info(),
			'nonce' => wp_create_nonce( 'epkb_ai_tools_debug' ),
			// Tuning sub-tab removed
		);
		
		return $config;
	}

	/**
	 * Get settings sections configuration
	 *
	 * @return array
	 */
	private static function get_settings_sections() {
		// Return empty array - all rendering is handled by React
		return array();
	}
	
	/**
	 * Check if running on localhost
	 *
	 * @return bool
	 */
	private static function is_localhost() {
		$whitelist = array(
			'127.0.0.1',
			'::1',
			'localhost'
		);
		
		// Check SERVER_ADDR
		if ( isset( $_SERVER['SERVER_ADDR'] ) && in_array( $_SERVER['SERVER_ADDR'], $whitelist ) ) {
			return true;
		}
		
		// Check REMOTE_ADDR (for development)
		if ( isset( $_SERVER['REMOTE_ADDR'] ) && in_array( $_SERVER['REMOTE_ADDR'], $whitelist ) ) {
			return true;
		}
		
		// Check HTTP_HOST
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$host = $_SERVER['HTTP_HOST'];
			if ( in_array( $host, $whitelist ) || 
				 strpos( $host, 'localhost' ) !== false || 
				 strpos( $host, '.local' ) !== false ||
				 strpos( $host, '.test' ) !== false ) {
				return true;
			}
		}
		
		// Check site URL
		$site_url = get_site_url();
		foreach ( $whitelist as $local_indicator ) {
			if ( strpos( $site_url, $local_indicator ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * AJAX handler to toggle debug mode
	 */
	public static function ajax_toggle_debug_mode() {

		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'echo-knowledge-base' ) ) );
			return;
		}
		
		$enabled = EPKB_Utilities::post( 'enabled', 'off' );
		$enabled = ( $enabled === 'on' ) ? 'on' : 'off';
		
		// Update the configuration
		$result = EPKB_AI_Config_Specs::update_config_value( 'ai_tools_debug_enabled', $enabled );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			return;
		}

		wp_send_json_success( array( 'message' => __( 'Debug mode updated successfully', 'echo-knowledge-base' ) ) );
	}
	
	/**
	 * Get system information
	 *
	 * @return array
	 */
	private static function get_system_info() {
		global $wp_version;
		
		$info = array();
		
		// WordPress info
		$info['wp_version'] = array(
			'label' => __( 'WordPress Version', 'echo-knowledge-base' ),
			'value' => $wp_version
		);
		
		// PHP info
		$info['php_version'] = array(
			'label' => __( 'PHP Version', 'echo-knowledge-base' ),
			'value' => PHP_VERSION,
			'class' => version_compare( PHP_VERSION, '7.4', '<' ) ? 'epkb-ai-warning' : ''
		);
		
		// Memory limit
		$memory_limit = ini_get( 'memory_limit' );
		$info['memory_limit'] = array(
			'label' => __( 'PHP Memory Limit', 'echo-knowledge-base' ),
			'value' => $memory_limit,
			'class' => ( intval( $memory_limit ) < 128 ) ? 'epkb-ai-warning' : ''
		);
		
		// Max execution time
		$max_exec_time = ini_get( 'max_execution_time' );
		$exec_time_warning = '';
		$exec_time_class = '';

		// Test if set_time_limit() works
		$original_time_limit = $max_exec_time;
		$can_change_time_limit = false;

		// Only test if not already unlimited (0)
		if ( $max_exec_time != 0 ) {
			$test_limit = (int) $max_exec_time + 1;
			@set_time_limit( $test_limit );
			$new_time_limit = ini_get( 'max_execution_time' );

			// Check if it changed
			if ( $new_time_limit == $test_limit || $new_time_limit == 0 ) {
				$can_change_time_limit = true;
				// Restore original value
				@set_time_limit( (int) $original_time_limit );
			}
		} else {
			$can_change_time_limit = true; // Unlimited is always OK
		}

		// Determine warning level
		if ( $max_exec_time == 0 ) {
			$exec_time_warning = ' (' . __( 'unlimited', 'echo-knowledge-base' ) . ')';
		} elseif ( $max_exec_time < 60 ) {
			if ( ! $can_change_time_limit ) {
				$exec_time_warning = ' - ' . __( 'WARNING: Too low for Content Analysis and cannot be changed', 'echo-knowledge-base' );
				$exec_time_class = 'epkb-ai-error';
			} else {
				$exec_time_warning = ' - ' . __( 'Low, but can be extended by plugin', 'echo-knowledge-base' );
				$exec_time_class = 'epkb-ai-warning';
			}
		} elseif ( ! $can_change_time_limit && $max_exec_time < 180 ) {
			$exec_time_warning = ' - ' . __( 'Cannot be changed, may timeout on large articles', 'echo-knowledge-base' );
			$exec_time_class = 'epkb-ai-warning';
		}

		$info['max_execution_time'] = array(
			'label' => __( 'Max Execution Time', 'echo-knowledge-base' ),
			'value' => $max_exec_time . ' seconds' . $exec_time_warning,
			'class' => $exec_time_class
		);

		// OpenAI API status
		$api_key = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_key' );
		$info['openai_api'] = array(
			'label' => __( 'OpenAI API Key', 'echo-knowledge-base' ),
			'value' => ! empty( $api_key ) ? __( 'Configured', 'echo-knowledge-base' ) : __( 'Not Configured', 'echo-knowledge-base' ),
			'class' => empty( $api_key ) ? 'epkb-ai-error' : 'epkb-ai-success'
		);
		
		// AI Features status
		$ai_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' ) === 'on';
		$info['ai_enabled'] = array(
			'label' => __( 'AI Features', 'echo-knowledge-base' ),
			'value' => $ai_enabled ? __( 'Enabled', 'echo-knowledge-base' ) : __( 'Disabled', 'echo-knowledge-base' ),
			'class' => $ai_enabled ? 'epkb-ai-success' : ''
		);
		
		// Chat status
		$chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled' );
		$info['ai_chat'] = array(
			'label' => __( 'AI Chat', 'echo-knowledge-base' ),
			'value' => $chat_enabled === 'on' ? __( 'Enabled', 'echo-knowledge-base' ) : __( 'Disabled', 'echo-knowledge-base' ),
			'class' => $chat_enabled === 'on' ? 'epkb-ai-success' : ''
		);
		
		// Search status
		$search_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' );
		$info['ai_search'] = array(
			'label' => __( 'AI Search', 'echo-knowledge-base' ),
			'value' => $search_enabled === 'on' ? __( 'Enabled', 'echo-knowledge-base' ) : __( 'Disabled', 'echo-knowledge-base' ),
			'class' => $search_enabled === 'on' ? 'epkb-ai-success' : ''
		);
		
		// Debug mode
		$info['debug_mode'] = array(
			'label' => __( 'WordPress Debug Mode', 'echo-knowledge-base' ),
			'value' => defined( 'WP_DEBUG' ) && WP_DEBUG ? __( 'Enabled', 'echo-knowledge-base' ) : __( 'Disabled', 'echo-knowledge-base' ),
			'class' => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'epkb-ai-warning' : ''
		);
		
		// Plugin version
		$info['plugin_version'] = array(
			'label' => __( 'Echo KB Version', 'echo-knowledge-base' ),
			'value' => Echo_Knowledge_Base::$version
		);
		
		// Server info
		$info['server_software'] = array(
			'label' => __( 'Server Software', 'echo-knowledge-base' ),
			'value' => $_SERVER['SERVER_SOFTWARE'] ?? __( 'Unknown', 'echo-knowledge-base' )
		);
		
		// Timezone
		$info['timezone'] = array(
			'label' => __( 'Timezone', 'echo-knowledge-base' ),
			'value' => wp_timezone_string()
		);
		
		// Current time
		$info['current_time'] = array(
			'label' => __( 'Current Time', 'echo-knowledge-base' ),
			'value' => current_time( 'mysql' )
		);
		
		// PHP Error log path
		$php_error_log_path = ini_get( 'error_log' );
		$info['php_error_log_path'] = array(
			'label' => __( 'PHP Error Log Path', 'echo-knowledge-base' ),
			'value' => ! empty( $php_error_log_path ) ? $php_error_log_path : __( 'Not configured', 'echo-knowledge-base' ),
			'class' => empty( $php_error_log_path ) ? 'epkb-ai-warning' : ''
		);
		
		// WordPress Error log path
		$wp_error_log_path = WP_CONTENT_DIR . '/debug.log';
		$wp_debug_enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$wp_debug_log_enabled = defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
		
		if ( $wp_debug_log_enabled ) {
			if ( file_exists( $wp_error_log_path ) ) {
				$info['wp_error_log_path'] = array(
					'label' => __( 'WP Error Log Path', 'echo-knowledge-base' ),
					'value' => $wp_error_log_path,
					'class' => ''
				);
			} else {
				$info['wp_error_log_path'] = array(
					'label' => __( 'WP Error Log Path', 'echo-knowledge-base' ),
					'value' => $wp_error_log_path . ' ' . __( '(file not found)', 'echo-knowledge-base' ),
					'class' => 'epkb-ai-warning'
				);
			}
		} else {
			$info['wp_error_log_path'] = array(
				'label' => __( 'WP Error Log Path', 'echo-knowledge-base' ),
				'value' => __( 'WP_DEBUG_LOG is disabled', 'echo-knowledge-base' ),
				'class' => 'epkb-ai-warning'
			);
		}
		
		return $info;
	}
	
	/**
	 * Get data collections information
	 *
	 * @return array
	 */
	private static function get_data_collections_info() {
		$collections_data = array();
		
		// Get all training data collections from specs
		$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections();
		if ( is_wp_error( $collections ) ) {
			return array();
		}
		
		$training_data_db = new EPKB_AI_Training_Data_DB( true );
		
		foreach ( $collections as $collection_id => $collection_config ) {
			// Get training data info from database
			$collection_info = $training_data_db->get_status_statistics( $collection_id );
			
			// Get vector store info from collection config
			$vector_store_id = isset( $collection_config['ai_training_data_store_id'] ) ? $collection_config['ai_training_data_store_id'] : '';
			$vector_store_status = ! empty( $vector_store_id ) ? 'created' : 'not_created';
			
			$collections_data[] = array(
				'id' => $collection_id,
				'name' => empty( $collection_config['ai_training_data_store_name'] ) ? EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id ) : $collection_config['ai_training_data_store_name'],
				'vector_store_id' => $vector_store_id,
				'vector_store_status' => $vector_store_status,
				'db_record_count' => $collection_info['total'] ?? 0,
				'status_counts' => array(
					'added' => $collection_info['added'] ?? 0,
					'updated' => $collection_info['updated'] ?? 0,
					'outdated' => $collection_info['outdated'] ?? 0,
					'error' => $collection_info['error'] ?? 0,
					'pending' => $collection_info['pending'] ?? 0
				)
			);
		}
		
		return $collections_data;
	}
	
	/**
	 * AJAX handler to get data collections info
	 */
	public static function ajax_get_data_collections_info() {
		
		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'echo-knowledge-base' ) ) );
			return;
		}
		
		$collections = self::get_data_collections_info();
		
		wp_send_json_success( array( 'collections' => $collections ) );
	}
	
	/**
	 * Generates AI Debug Info download file
	 */
	public static function download_ai_debug_info() {
		
		if ( EPKB_Utilities::post( 'action' ) != 'epkb_download_ai_debug_info' ) {
			return;
		}
		
		// Check nonce
		$wp_nonce = EPKB_Utilities::post( '_wpnonce' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), 'epkb_ai_tools_debug' ) ) {
			wp_die( esc_html__( 'You do not have permission to download debug info', 'echo-knowledge-base' ) );
		}
		
		// Security check
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_die( esc_html__( 'Access denied', 'echo-knowledge-base' ) );
		}
		
		nocache_headers();
		
		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename="epkb-ai-debug-' . date( 'Y-m-d' ) . '.json"' );
		
		// Get all debug data
		$system_info = self::get_system_info();
		$data_collections = self::get_data_collections_info();
		
		// Get AI config
		$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled' );
		$ai_search_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' );
		$ai_config = array(
			'ai_enabled' => EPKB_AI_Utilities::is_ai_configured(),
			'ai_chat_enabled' => $ai_chat_enabled != 'off',
			'ai_search_enabled' => $ai_search_enabled != 'off',
			'api_key_configured' => ! empty( EPKB_AI_Config_Specs::get_ai_config_value( 'ai_key' ) )
		);
		
		// Get AI logs
		$ai_logs = EPKB_AI_Log::get_logs_for_display();
		
		$debug_data = array(
			'generated_at' => current_time( 'c' ),
			'plugin_version' => Echo_Knowledge_Base::$version,
			'ai_configuration' => $ai_config,
			'data_collections' => $data_collections,
			'system_info' => array(),
			'recent_ai_logs' => array_slice( $ai_logs, -50 ) // Last 50 logs
		);
		
		// Convert system info to simple key-value pairs
		foreach ( $system_info as $key => $info ) {
			$debug_data['system_info'][$key] = $info['value'];
		}
		
		// Add error log paths explicitly if not already included
		if ( ! isset( $debug_data['system_info']['php_error_log_path'] ) ) {
			$debug_data['system_info']['php_error_log_path'] = ini_get( 'error_log' ) ?: 'Not configured';
		}
		if ( ! isset( $debug_data['system_info']['wp_error_log_path'] ) ) {
			$debug_data['system_info']['wp_error_log_path'] = WP_CONTENT_DIR . '/debug.log';
		}
		
		echo wp_json_encode( $debug_data, JSON_PRETTY_PRINT );
		
		die();
	}
}
