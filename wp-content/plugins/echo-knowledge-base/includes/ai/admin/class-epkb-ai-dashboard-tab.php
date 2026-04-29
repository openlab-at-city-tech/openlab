<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI Dashboard tab with React implementation
 */
class EPKB_AI_Dashboard_Tab {
	
	public function __construct() {
		add_action( 'wp_ajax_epkb_get_ai_status', array( $this, 'ajax_get_ai_status' ) );
add_action( 'wp_ajax_epkb_check_training_data_sync', array( $this, 'ajax_check_training_data_sync' ) );
		add_action( 'wp_ajax_epkb_submit_empty_content_report', array( $this, 'ajax_submit_empty_content_report' ) );
	}

	/**
	 * Get the configuration for the Dashboard tab
	 * This will be used by React to render the tab content
	 *
	 * @return array
	 */
	public static function get_tab_config() {
		$config = array(
			'tab_id' => 'dashboard',
			'title' => __( 'Dashboard', 'echo-knowledge-base' ),
			'load_status_async' => true
		);
		
		// Do a quick status check for immediate display (no API calls)
		$quick_status = self::get_ai_status( true );
		$config['status'] = $quick_status;
		
		$config['is_ai_enabled'] = EPKB_AI_Utilities::is_ai_chat_or_search_enabled();

		// Show dashboard content
		$config['dashboard_stats'] = self::get_dashboard_stats();
		$config['news'] = self::get_news_items();
		$config['ai_features'] = self::get_ai_features();
		$config['tools_link'] = self::get_tools_link();
		
		return $config;
	}

	/**
	 * AJAX handler to get AI status
	 */
	public function ajax_get_ai_status() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action' );

		// Check for force refresh parameter
		$force_refresh = isset( $_POST['force_refresh'] ) && $_POST['force_refresh'] === 'true';
		
		// Try to get cached status first (cache for 30 seconds to avoid repeated checks)
		$cache_key = 'epkb_ai_dashboard_status';
		$cached_status = get_transient( $cache_key );
		
		if ( ! $force_refresh && $cached_status !== false ) {
			wp_send_json_success( $cached_status );
			return;
		}

		// Use quick check by default, full check only on force refresh
		$quick_check = ! $force_refresh;
		$status = self::get_ai_status( $quick_check );
		
		// Cache the status for 30 seconds
		set_transient( $cache_key, $status, 30 );

		wp_send_json_success( $status );
	}

	/**
	 * AJAX handler to check training data sync status
	 */
	public function ajax_check_training_data_sync() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action' );

		// Check if training data table exists and has synced data
		$provider_collections = EPKB_AI_Training_Data_Config_Specs::get_collection_ids_by_provider();
		$has_provider_data = ! empty( $provider_collections ) && EPKB_AI_Training_Data_DB::count_synced_data( $provider_collections ) > 0;
		$result['has_synced_data'] = EPKB_AI_Utilities::is_ai_chat_or_search_enabled() && $has_provider_data;

		wp_send_json_success( $result );
	}

	/**
	 * AJAX handler for submitting empty content error reports
	 */
	public function ajax_submit_empty_content_report() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action' );

		$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$error_type = isset( $_POST['error_type'] ) ? sanitize_text_field( $_POST['error_type'] ) : '';

		if ( ! empty( $email ) && ! is_email( $email ) ) {
			wp_send_json_error( __( 'Please provide a valid email address.', 'echo-knowledge-base' ) );
		}

		// Build feedback message with post details
		$post = get_post( $post_id );
		$post_title = $post ? $post->post_title : 'N/A';
		$original_html = $post ? mb_substr( $post->post_content, 0, 5000 ) : 'N/A';

		$feedback_message = "Empty Content Report\n";
		$feedback_message .= "Error Type: " . $error_type . "\n";
		$feedback_message .= "Post ID: " . $post_id . "\n";
		$feedback_message .= "Post Title: " . $post_title . "\n";
		$feedback_message .= "Original HTML:\n" . $original_html;

		$report_data = array(
			'epkb_action'       => 'epkb_process_user_feedback',
			'feedback_type'     => 'empty_content_report',
			'feedback_input'    => $feedback_message,
			'plugin_name'       => 'AI',
			'plugin_version'    => class_exists( 'Echo_Knowledge_Base' ) ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version'     => '',
			'wp_version'        => '',
			'theme_info'        => '',
			'contact_user'      => $email,
			'first_name'        => '',
			'email_subject'     => 'Empty Content Report',
		);

		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $report_data, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $report_data,
				'sslverify' => false
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to submit report. Please try again.', 'echo-knowledge-base' ) ) );
		}

		wp_send_json_success( array( 'message' => __( 'Thank you! Your report has been submitted successfully.', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Get comprehensive AI status
	 *
	 * @param bool $quick_check If true, perform quick checks without API calls
	 * @return array Status information with issues and warnings
	 */
	private static function get_ai_status( $quick_check = true ) {

		$status = array(
			'issues' => array(),
			'warnings' => array(),
			'info' => array(),
			'checks' => array()
		);
		
		// Only check if user has used AI if AI is enabled (to avoid DB errors)
		$status['show_get_started'] = !EPKB_AI_Utilities::is_ai_chat_or_search_enabled() || !EPKB_AI_Messages_DB::has_user_used_ai();
		
		// Check if this is initial setup (no API key and no disclaimer accepted)
		$encrypted_key = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( EPKB_AI_Provider::get_active_provider() );
		$disclaimer_accepted = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' );
		$status['is_initial_setup'] = empty( $encrypted_key ) && $disclaimer_accepted !== 'on';
		
		// 1. Check API Key - use quick check for dashboard loading
		$api_key_status = self::check_api_key( $quick_check );
		$status['checks']['api_key'] = $api_key_status;
		if ( $api_key_status['status'] === 'error' ) {
			$status['issues'][] = $api_key_status;
		} elseif ( $api_key_status['status'] === 'warning' ) {
			$status['warnings'][] = $api_key_status;
		}
		
		// 2. Check Vector Store - skip for quick check to avoid API calls
		if ( ! $quick_check ) {
			$vector_store_status = self::check_vector_store();
			$status['checks']['vector_store'] = $vector_store_status;
			if ( $vector_store_status['status'] === 'error' ) {
				$status['issues'][] = $vector_store_status;
			} elseif ( $vector_store_status['status'] === 'warning' ) {
				$status['warnings'][] = $vector_store_status;
			}
		}
		
		// 3. Check Disclaimer Agreement
		$disclaimer_status = self::check_disclaimer();
		$status['checks']['disclaimer'] = $disclaimer_status;
		if ( $disclaimer_status['status'] === 'error' ) {
			$status['issues'][] = $disclaimer_status;
		}
		
		// 4. Check AI Tables only if AI is enabled (skip DB checks otherwise)
		if ( EPKB_AI_Utilities::is_ai_chat_or_search_enabled() ) {
			$tables_status = self::check_ai_tables();
			$status['checks']['tables'] = $tables_status;
			if ( $tables_status['status'] === 'error' ) {
				$status['issues'][] = $tables_status;
			}
		}
		
		// 5. Check AI Configuration
		$config_status = self::check_ai_configuration();
		$status['checks']['configuration'] = $config_status;
		if ( $config_status['status'] === 'error' ) {
			$status['issues'][] = $config_status;
		} elseif ( $config_status['status'] === 'warning' ) {
			$status['warnings'][] = $config_status;
		}
		
		// 6. Check REST API
		$rest_status = self::check_rest_api();
		$status['checks']['rest_api'] = $rest_status;
		if ( $rest_status['status'] === 'error' ) {
			$status['issues'][] = $rest_status;
		}
		
		// 8. Additional System Checks - only if AI is enabled
		if ( EPKB_AI_Utilities::is_ai_chat_or_search_enabled() ) {
			$system_checks = self::check_system_requirements();
			foreach ( $system_checks as $check ) {
				$status['checks'][$check['id']] = $check;
				if ( $check['status'] === 'error' ) {
					$status['issues'][] = $check;
				} elseif ( $check['status'] === 'warning' ) {
					$status['warnings'][] = $check;
				} elseif ( $check['status'] === 'info' ) {
					$status['info'][] = $check;
				}
			}
		}
		
		// Calculate overall status
		if ( ! empty( $status['issues'] ) ) {
			$status['overall'] = 'error';
		} elseif ( ! empty( $status['warnings'] ) ) {
			$status['overall'] = 'warning';
		} else {
			$status['overall'] = 'success';
		}
		
		return $status;
	}
	
	/**
	 * Check API Key validity
	 *
	 * @param bool $quick_check If true, skip live provider connection test for faster loading
	 * @return array Status information
	 */
	private static function check_api_key( $quick_check = false ) {

		$provider = EPKB_AI_Provider::get_active_provider();
		$encrypted_key = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( $provider );
		$provider_label = EPKB_AI_Provider::get_provider_label( $provider );

		// Check if API key exists
		if ( empty( $encrypted_key ) ) {
			return array(
				'id' => 'api_key_missing',
				'status' => 'warning',
				'message' => __( 'API key is not configured', 'echo-knowledge-base' ),
				'action' => __( 'Add your API key in General Settings', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' ),
				'is_setup_step' => true
			);
		}
		
		// For quick check, just verify the key exists and has basic format
		if ( $quick_check ) {
			// Simple format check without decryption
			return array(
				'id' => 'api_key_valid',
				'status' => 'success',
				'message' => __( 'API key is configured', 'echo-knowledge-base' )
			);
		}
		
		// Decrypt the API key for validation
		$api_key = EPKB_Utilities::decrypt_data( $encrypted_key );
		if ( $api_key === false ) {
			return array(
				'id' => 'api_key_decrypt_failed',
				'status' => 'warning',
				'message' => __( 'Failed to decrypt API key', 'echo-knowledge-base' ),
				'action' => __( 'Re-enter your API key in General Settings', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' )
			);
		}
		
		// Validate API key format
		if ( ! EPKB_AI_Validation::validate_api_key_format( $api_key, $provider ) ) {
			return array(
				'id' => 'api_key_invalid_format',
				'status' => 'warning',
				'message' => __( 'API key format is invalid', 'echo-knowledge-base' ),
				'action' => __( 'Check your API key format', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' )
			);
		}
		
		// Check with active provider
		$client = EPKB_AI_Provider::get_client();
		$test_result = $client->test_connection();
		if ( is_wp_error( $test_result ) ) {
			return array(
				'id' => 'api_key_not_found',
				'status' => 'warning',
				// translators: %s is the AI provider name (e.g., "OpenAI")
				'message' => sprintf( __( '%s does not recognize the API key', 'echo-knowledge-base' ), $provider_label ),
				'action' => __( 'Verify your API key with your provider', 'echo-knowledge-base' ),
				'details' => $test_result->get_error_message(),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' )
			);
		}

		return array(
			'id' => 'api_key_valid',
			'status' => 'success',
			'message' => __( 'API key is valid', 'echo-knowledge-base' )
		);
	}
	
	/**
	 * Check Vector Store existence
	 *
	 * @return array Status information
	 */
	private static function check_vector_store() {
		
		// Get all training data collections
		$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections();
		$missing_stores = array();
		
		foreach ( $collections as $collection_id => $collection ) {
			if ( ! empty( $collection['ai_training_data_store_id'] ) ) {
				// Verify the store exists in the configured provider
				$handler = EPKB_AI_Provider::get_vector_store_handler();
				$store_info = $handler->get_vector_store_info_by_collection_id( $collection_id );
				if ( is_wp_error( $store_info ) ) {
					$missing_stores[] = array(
						'collection_id' => $collection_id,
						'store_id' => $collection['ai_training_data_store_id'],
						'collection_name' => $collection['ai_training_data_store_name']
					);
				}
			}
		}
		
		if ( ! empty( $missing_stores ) ) {
			return array(
				'id' => 'vector_store_missing',
				'status' => 'warning',
				'message' => __( 'vector store is missing', 'echo-knowledge-base' ),
				'action' => __( 'Re-sync your training data to create new vector stores', 'echo-knowledge-base' ),
				'details' => $missing_stores,
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' )
			);
		}
		
		return array(
			'id' => 'vector_store_valid',
			'status' => 'success',
			'message' => __( 'All vector stores are valid', 'echo-knowledge-base' )
		);
	}
	
	/**
	 * Check disclaimer agreement
	 *
	 * @return array Status information
	 */
	private static function check_disclaimer() {
		
		$disclaimer_accepted = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' );
		if ( $disclaimer_accepted !== 'on' ) {
			return array(
				'id' => 'disclaimer_not_accepted',
				'status' => 'warning',
				'message' => __( 'Data privacy agreement needed', 'echo-knowledge-base' ),
				'action' => __( 'Review and accept the data privacy terms', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' ),
				'is_setup_step' => true
			);
		}
		
		return array(
			'id' => 'disclaimer_accepted',
			'status' => 'success',
			'message' => __( 'Disclaimer has been accepted', 'echo-knowledge-base' )
		);
	}
	
	/**
	 * Check AI database tables
	 *
	 * @return array Status information
	 */
	private static function check_ai_tables() {

		global $wpdb;
		$missing_tables = array();
		
		// List of required AI tables
		$required_tables = array(
			$wpdb->prefix . 'epkb_ai_training_data' => __( 'Training Data', 'echo-knowledge-base' ),
			$wpdb->prefix . 'epkb_ai_messages' => __( 'Chat Messages', 'echo-knowledge-base' )
		);
		
		foreach ( $required_tables as $table_name => $table_label ) {
			$table_exists = $wpdb->get_var( $wpdb->prepare( 
				"SHOW TABLES LIKE %s", 
				$table_name 
			) );
			
			if ( $table_exists !== $table_name ) {
				$missing_tables[] = $table_label;
			}
		}
		
		if ( ! empty( $missing_tables ) ) {
			return array(
				'id' => 'ai_tables_missing',
				'status' => 'warning',
				'message' => __( 'Database setup required', 'echo-knowledge-base' ),
				// translators: %s is the list of missing database table names
				'action' => sprintf(
					__( 'Please deactivate and reactivate the plugin to create the necessary database tables (%s)', 'echo-knowledge-base' ),
					implode( ', ', $missing_tables )
				),
				'details' => $missing_tables,
				'is_setup_step' => true
			);
		}
		
		return array(
			'id' => 'ai_tables_valid',
			'status' => 'success',
			'message' => __( 'All AI database tables exist', 'echo-knowledge-base' )
		);
	}
	
	/**
	 * Check AI configuration
	 *
	 * @return array Status information
	 */
	private static function check_ai_configuration() {
		
		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		if ( empty( $ai_config ) || ! is_array( $ai_config ) ) {
			return array(
				'id' => 'ai_config_missing',
				'status' => 'warning',
				'message' => __( 'AI configuration is missing', 'echo-knowledge-base' ),
				'action' => __( 'Contact support - configuration needs to be initialized', 'echo-knowledge-base' )
			);
		}
		
		// Check if any AI features are enabled
		if ( $ai_config['ai_chat_enabled'] == 'off' && $ai_config['ai_search_enabled'] == 'off' ) {
			return array(
				'id' => 'ai_features_disabled',
				'status' => 'warning',
				'message' => __( 'No AI features are enabled', 'echo-knowledge-base' ),
				'action' => __( 'Enable AI Chat or AI Search to use AI features', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' )
			);
		}
		
		return array(
			'id' => 'ai_config_valid',
			'status' => 'success',
			'message' => __( 'AI configuration is valid', 'echo-knowledge-base' )
		);
	}
	
	/**
	 * Check REST API availability
	 *
	 * @return array Status information
	 */
	private static function check_rest_api() {
		
		// Check if REST API is disabled via filter
		if ( apply_filters( 'rest_enabled', true ) === false ) {
			return array(
				'id' => 'rest_api_disabled_filter',
				'status' => 'warning',
				'message' => __( 'REST API is disabled by a filter', 'echo-knowledge-base' ),
				'action' => __( 'Remove any filters disabling the REST API', 'echo-knowledge-base' ),
				'details' => __( 'AI features require REST API to be enabled', 'echo-knowledge-base' )
			);
		}
		
		// Check if REST API routes are available
		$rest_url = get_rest_url();
		if ( empty( $rest_url ) ) {
			return array(
				'id' => 'rest_api_url_missing',
				'status' => 'warning',
				'message' => __( 'REST API URL is not available', 'echo-knowledge-base' ),
				'action' => __( 'Check permalink settings and server configuration', 'echo-knowledge-base' )
			);
		}
		
		// Check if our custom REST endpoints are registered
		$routes = rest_get_server()->get_routes();
		$our_namespace = '/epkb-ai/v1';
		$has_our_routes = false;
		
		foreach ( $routes as $route => $data ) {
			if ( strpos( $route, $our_namespace ) === 0 ) {
				$has_our_routes = true;
				break;
			}
		}
		
		if ( ! $has_our_routes ) {
			return array(
				'id' => 'rest_api_routes_missing',
				'status' => 'warning',
				'message' => __( 'AI REST API routes are not registered', 'echo-knowledge-base' ),
				'action' => __( 'Deactivate and reactivate the plugin', 'echo-knowledge-base' )
			);
		}
		
		return array(
			'id' => 'rest_api_valid',
			'status' => 'success',
			'message' => __( 'REST API is enabled and working', 'echo-knowledge-base' )
		);
	}
	
	
	/**
	 * Check system requirements and additional issues
	 *
	 * @return array Array of status checks
	 */
	private static function check_system_requirements() {
		
		$checks = array();
		
		// Check PHP version
		if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {
			$checks[] = array(
				'id' => 'php_version',
				'status' => 'warning',
				// translators: %s is the PHP version number
				'message' => sprintf( __( 'PHP version %s is too old', 'echo-knowledge-base' ), PHP_VERSION ),
				'action' => __( 'Upgrade to PHP 7.2 or higher', 'echo-knowledge-base' )
			);
		}

		// Check WordPress version
		global $wp_version;
		if ( version_compare( $wp_version, '5.3', '<' ) ) {
			$checks[] = array(
				'id' => 'wp_version',
				'status' => 'warning',
				// translators: %s is the WordPress version number
				'message' => sprintf( __( 'WordPress %s may have compatibility issues', 'echo-knowledge-base' ), $wp_version ),
				'action' => __( 'Update WordPress to 5.3 or higher', 'echo-knowledge-base' )
			);
		}
		
		// Check SSL
		if ( ! is_ssl() && ! defined( 'WP_DEBUG' ) ) {
			$checks[] = array(
				'id' => 'ssl_missing',
				'status' => 'warning',
				'message' => __( 'Site is not using SSL/HTTPS', 'echo-knowledge-base' ),
				'action' => __( 'Enable SSL for secure API communication', 'echo-knowledge-base' ),
				'details' => __( 'AI features work best with SSL enabled', 'echo-knowledge-base' )
			);
		}
		
		// Check memory limit
		$memory_limit = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
		if ( $memory_limit < 128 * MB_IN_BYTES ) {
			$checks[] = array(
				'id' => 'memory_limit',
				'status' => 'warning',
				'message' => __( 'PHP memory limit is low', 'echo-knowledge-base' ),
				'action' => __( 'Increase memory_limit to at least 128M', 'echo-knowledge-base' ),
				// translators: %s is the current PHP memory limit
				'details' => sprintf( __( 'Current limit: %s', 'echo-knowledge-base' ), size_format( $memory_limit ) )
			);
		}
		
		/** @disregard P1011 Check cron status */
		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			$auto_sync = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_auto_sync_enabled' );
			if ( $auto_sync === 'on' ) {
				$checks[] = array(
					'id' => 'cron_disabled',
					'status' => 'warning',
					'message' => __( 'WP Cron is disabled', 'echo-knowledge-base' ),
					'action' => __( 'Auto-sync requires WP Cron or external cron', 'echo-knowledge-base' ),
					'details' => __( 'Set up external cron or enable WP Cron', 'echo-knowledge-base' )
				);
			}
		}
		
		// Check for conflicting plugins
		$conflicting = self::check_conflicting_plugins();
		if ( ! empty( $conflicting ) ) {
			$checks[] = array(
				'id' => 'conflicting_plugins',
				'status' => 'warning',
				'message' => __( 'Potentially conflicting plugins detected', 'echo-knowledge-base' ),
				'action' => __( 'Test AI features with these plugins disabled', 'echo-knowledge-base' ),
				'details' => implode( ', ', $conflicting )
			);
		}
		
		// Check sync status
		$sync_status = self::check_sync_status();
		if ( $sync_status !== null ) {
			$checks[] = $sync_status;
		}
		
		// Check rate limiting
		$rate_limit_status = self::check_rate_limiting();
		if ( $rate_limit_status !== null ) {
			$checks[] = $rate_limit_status;
		}
		
		return $checks;
	}
	
	/**
	 * Check for conflicting plugins
	 *
	 * @return array List of potentially conflicting plugins
	 */
	private static function check_conflicting_plugins() {
		
		$conflicting = array();
		$active_plugins = get_option( 'active_plugins', array() );
		
		// Known plugins that might conflict
		$potential_conflicts = array(
			'disable-json-api/disable-json-api.php' => 'Disable JSON API',
			'disable-rest-api/disable-rest-api.php' => 'Disable REST API',
			'wp-rest-api-controller/wp-rest-api-controller.php' => 'WP REST API Controller',
			'jwt-authentication-for-wp-rest-api/jwt-auth.php' => 'JWT Authentication'
		);
		
		foreach ( $potential_conflicts as $plugin => $name ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				$conflicting[] = $name;
			}
		}
		
		return $conflicting;
	}
	
	/**
	 * Check sync status and identify issues
	 *
	 * @return array|null Status information or null if no issues
	 */
	private static function check_sync_status() {
		
		// Check if there's a stuck sync
		$sync_lock = get_transient( 'epkb_ai_sync_lock' );
		if ( $sync_lock !== false ) {
			$lock_time = get_option( 'epkb_ai_sync_lock_time', 0 );
			if ( $lock_time && ( time() - $lock_time ) > 3600 ) {
				return array(
					'id' => 'sync_stuck',
					'status' => 'warning',
					'message' => __( 'AI sync appears to be stuck', 'echo-knowledge-base' ),
					'action' => __( 'Clear sync lock in Tools tab', 'echo-knowledge-base' ),
					'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=tools' )
				);
			}
		}
		
		// Check last sync time
		$last_sync = get_option( 'epkb_ai_last_sync_completed', 0 );
		if ( $last_sync > 0 ) {
			$days_since_sync = ( time() - $last_sync ) / DAY_IN_SECONDS;
			if ( $days_since_sync > 30 ) {
				return array(
					'id' => 'sync_outdated',
					'status' => 'info',
					// translators: %d is the number of days since last sync
					'message' => sprintf(
						__( 'Last sync was %d days ago', 'echo-knowledge-base' ),
						round( $days_since_sync )
					),
					'action' => __( 'Consider syncing your training data', 'echo-knowledge-base' ),
					'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' )
				);
			}
		}
		
		return null;
	}
	
	/**
	 * Check rate limiting status
	 *
	 * @return array|null Status information or null if no issues
	 */
	private static function check_rate_limiting() {
		
		// Check if rate limited
		$rate_limit_until = get_transient( 'epkb_ai_rate_limit_until' );
		if ( $rate_limit_until !== false && $rate_limit_until > time() ) {
			$minutes_left = ceil( ( $rate_limit_until - time() ) / 60 );
			return array(
				'id' => 'rate_limited',
				'status' => 'warning',
				// translators: %d is the number of minutes until rate limit expires
				'message' => sprintf(
					__( 'OpenAI rate limit active for %d more minutes', 'echo-knowledge-base' ),
					$minutes_left
				),
				'action' => __( 'Wait for rate limit to expire', 'echo-knowledge-base' ),
				'details' => __( 'Too many requests were sent to AI', 'echo-knowledge-base' )
			);
		}
		
		return null;
	}

	
	/**
	 * Get dashboard statistics
	 *
	 * @return array
	 */
	private static function get_dashboard_stats() {
		global $wpdb;

		$stats = array();

		// Training data stats (internal use for sync_status)
		$training_data_table = $wpdb->prefix . 'epkb_ai_training_data';
		$provider_collections = EPKB_AI_Training_Data_Config_Specs::get_collection_ids_by_provider();
		$training_data = array( 'synced' => 0, 'pending' => 0, 'last_sync' => null );

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $training_data_table ) ) === $training_data_table && ! empty( $provider_collections ) ) {
			$placeholders = implode( ', ', array_fill( 0, count( $provider_collections ), '%d' ) );
			$where = $wpdb->prepare( "collection_id IN ( {$placeholders} )", $provider_collections );

			$training_data['synced'] = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$training_data_table} WHERE {$where} AND status IN ('added','updated')" ) );
			$training_data['pending'] = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$training_data_table} WHERE {$where} AND status IN ('pending','adding','updating','outdated')" ) );
			$training_data['last_sync'] = $wpdb->get_var( "SELECT MAX(last_synced) FROM {$training_data_table} WHERE {$where}" );
		}

		// Monthly date range
		$month_start = current_time( 'Y-m-01' );
		$month_end = current_time( 'Y-m-t' );

		// Chat Statistics
		$messages_table = $wpdb->prefix . 'epkb_ai_messages';
		$monthly_conversations = 0;
		$total_conversations = 0;
		$today_conversations = 0;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $messages_table ) ) === $messages_table ) {
			$total_conversations = intval( $wpdb->get_var( "SELECT COUNT(DISTINCT conversation_id) FROM {$messages_table}" ) );
			$today_conversations = intval( $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(DISTINCT conversation_id) FROM {$messages_table} WHERE DATE(created) = %s",
				current_time( 'Y-m-d' )
			) ) );
			$monthly_conversations = intval( $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(DISTINCT conversation_id) FROM {$messages_table} WHERE DATE(created) BETWEEN %s AND %s",
				$month_start, $month_end
			) ) );
		}

		// Search Statistics
		$search_logs_table = $wpdb->prefix . 'epkb_ai_search_logs';
		$monthly_searches = 0;
		$total_searches = 0;
		$today_searches = 0;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $search_logs_table ) ) === $search_logs_table ) {
			$total_searches = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$search_logs_table}" ) );
			$today_searches = intval( $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$search_logs_table} WHERE DATE(created) = %s",
				current_time( 'Y-m-d' )
			) ) );
			$monthly_searches = intval( $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$search_logs_table} WHERE DATE(created) BETWEEN %s AND %s",
				$month_start, $month_end
			) ) );
		}

		// Monthly AI Activity (first stat)
		$monthly_total = $monthly_conversations + $monthly_searches;
		$month_name = date_i18n( 'F', current_time( 'timestamp' ) );

		$stats['monthly_activity'] = array(
			'icon' => 'epkbfa epkbfa-bar-chart',
			'color' => '#4F46E5',
			'title' => __( 'AI Activity', 'echo-knowledge-base' ),
			'value' => $monthly_total,
			'monthly_conversations' => $monthly_conversations,
			'monthly_searches' => $monthly_searches,
			'description' => $month_name,
			// translators: %1$d is number of chats, %2$d is number of searches
			'bottom_text' => sprintf( __( '%1$d chats, %2$d searches', 'echo-knowledge-base' ), $monthly_conversations, $monthly_searches ),
			'show_as_main' => true
		);

		// Chat Statistics
		$stats['chat'] = array(
			'icon' => 'epkbfa epkbfa-comments',
			'color' => '#10B981',
			'title' => __( 'AI Chat', 'echo-knowledge-base' ),
			'value' => $today_conversations,
			'conversations' => $total_conversations,
			'today' => $today_conversations,
			'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
			// translators: %d is the total number of conversations
			'bottom_text' => sprintf( __( '%d total conversations', 'echo-knowledge-base' ), $total_conversations ),
			'show_as_main' => true
		);

		// Search Statistics
		$stats['search'] = array(
			'icon' => 'epkbfa epkbfa-search',
			'color' => '#F59E0B',
			'title' => __( 'AI Search', 'echo-knowledge-base' ),
			'value' => $today_searches,
			'total' => $total_searches,
			'today' => $today_searches,
			'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
			// translators: %d is the total number of searches
			'bottom_text' => sprintf( __( '%d total searches', 'echo-knowledge-base' ), $total_searches ),
			'show_as_main' => true
		);

		// Last Sync Status
		$last_sync_time = $training_data['last_sync'] ? strtotime( $training_data['last_sync'] ) : 0;
		$synced_count = $training_data['synced'];
		$pending_count = $training_data['pending'];
		$auto_sync_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_auto_sync_enabled' ) === 'on';

		if ( $last_sync_time > 0 ) {
			$time_diff = human_time_diff( $last_sync_time, current_time( 'timestamp' ) );
			// translators: %s is a human-readable time difference (e.g., "2 hours")
			$sync_value = sprintf( __( '%s ago', 'echo-knowledge-base' ), $time_diff );
		} else {
			$sync_value = __( 'Never', 'echo-knowledge-base' );
		}

		// Show pending count if there are items needing sync, otherwise show synced count
		if ( $pending_count > 0 ) {
			// translators: %d is the number of items pending sync
			$sync_description = sprintf( __( '%d items need syncing', 'echo-knowledge-base' ), $pending_count );
		} elseif ( $synced_count > 0 ) {
			$sync_description = __( 'All items synced', 'echo-knowledge-base' );
		} else {
			$sync_description = __( 'No sync completed yet', 'echo-knowledge-base' );
		}

		$stats['sync_status'] = array(
			'icon' => 'epkbfa epkbfa-refresh',
			'color' => '#8B5CF6',
			'title' => __( 'Last Sync', 'echo-knowledge-base' ),
			'value' => $sync_value,
			'last_sync_time' => $last_sync_time,
			'synced_count' => $synced_count,
			'pending_count' => $pending_count,
			'auto_sync' => $auto_sync_enabled,
			'description' => $sync_description,
			'bottom_text' => $auto_sync_enabled ? __( 'Auto-sync enabled', 'echo-knowledge-base' ) : __( 'Manual sync only', 'echo-knowledge-base' ),
			'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' ),
			'show_as_main' => true
		);

		return $stats;
	}
	
	/**
	 * Get default statistics when AI is not enabled
	 *
	 * @return array
	 */
	private static function get_default_stats() {
		$month_name = date_i18n( 'F', current_time( 'timestamp' ) );

		return array(
			'monthly_activity' => array(
				'icon' => 'epkbfa epkbfa-bar-chart',
				'color' => '#4F46E5',
				'title' => __( 'AI Activity', 'echo-knowledge-base' ),
				'value' => 0,
				'monthly_conversations' => 0,
				'monthly_searches' => 0,
				'description' => $month_name,
				'bottom_text' => __( '0 chats, 0 searches', 'echo-knowledge-base' ),
				'show_as_main' => true
			),
			'chat' => array(
				'icon' => 'epkbfa epkbfa-comments',
				'color' => '#10B981',
				'title' => __( 'AI Chat', 'echo-knowledge-base' ),
				'value' => 0,
				'conversations' => 0,
				'today' => 0,
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => __( '0 total conversations', 'echo-knowledge-base' ),
				'show_as_main' => true
			),
			'search' => array(
				'icon' => 'epkbfa epkbfa-search',
				'color' => '#F59E0B',
				'title' => __( 'AI Search', 'echo-knowledge-base' ),
				'value' => 0,
				'total' => 0,
				'today' => 0,
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => __( '0 total searches', 'echo-knowledge-base' ),
				'show_as_main' => true
			),
			'sync_status' => array(
				'icon' => 'epkbfa epkbfa-refresh',
				'color' => '#8B5CF6',
				'title' => __( 'Last Sync', 'echo-knowledge-base' ),
				'value' => __( 'Never', 'echo-knowledge-base' ),
				'last_sync_time' => 0,
				'synced_count' => 0,
				'pending_count' => 0,
				'auto_sync' => false,
				'description' => __( 'No sync completed yet', 'echo-knowledge-base' ),
				'bottom_text' => __( 'Enable AI to sync data', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' ),
				'show_as_main' => true
			)
		);
	}
	
	/**
	 * Get news items for the dashboard
	 *
	 * @return array
	 */
	private static function get_news_items() {
		$quiz_demo_url = EPKB_Quizzes_Utilities::get_demo_quiz_url();

		return array(
			array(
				'date' => '2026-03-29',
				'type' => 'feature',
				'title' => __( 'AI-Generated Quizzes', 'echo-knowledge-base' ),
				'description' => __( 'Generate quizzes from KB articles with AI and publish them below article content.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-quizzes' ),
				'link_label' => __( 'Open Quizzes', 'echo-knowledge-base' ),
				'secondary_link' => $quiz_demo_url,
				'secondary_link_label' => __( 'See Demo Quiz', 'echo-knowledge-base' )
			),
			array(
				'date' => '2026-03-09',
				'type' => 'feature',
				'title' => __( 'PDF to Articles (PRO)', 'echo-knowledge-base' ),
				'description' => __( 'Upload PDF files and convert them into KB articles with AI-powered formatting. Import documentation, manuals, and guides directly into your knowledge base.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' )
			),
			array(
				'date' => '2026-03-09',
				'type' => 'feature',
				'title' => __( 'PDF Uploads for AI Data Collections (PRO)', 'echo-knowledge-base' ),
				'description' => __( 'Upload PDF documents directly into AI Data Collections. Your AI Chat and Search will use the PDF content to provide accurate answers.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' )
			),
			array(
				'date' => '2026-03-01',
				'type' => 'feature',
				'title' => __( 'AI Chat Access Control (PRO)', 'echo-knowledge-base' ),
				'description' => __( 'Control who can use AI Chat: allow everyone, logged-in users only, or specific WordPress roles. Set different access rules for each chat location.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=chat' )
			),
			array(
				'date' => '2026-02-21',
				'type' => 'feature',
				'title' => __( 'Glossary', 'echo-knowledge-base' ),
				'description' => __( 'Add glossary terms with definitions that are automatically highlighted in articles with interactive tooltips.', 'echo-knowledge-base' ),
				'link' => null
			),
			array(
				'date' => '2026-02-21',
				'type' => 'feature',
				'title' => __( 'PDF to Notes (PRO)', 'echo-knowledge-base' ),
				'description' => __( 'Upload PDF files and convert them into AI training notes to expand your AI knowledge beyond KB articles.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' )
			),
		);
	}
	
	/**
	 * Get AI features for dashboard display
	 *
	 * @return array
	 */
	private static function get_ai_features() {
		$has_ai_features_pro = EPKB_AI_Utilities::is_ai_features_pro_enabled();
		$quiz_demo_url = EPKB_Quizzes_Utilities::get_demo_quiz_url();

		return array(
			array_merge( array(
				'id'            => 'ai-chat',
				'icon'          => 'epkbfa epkbfa-comments',
				'title'         => __( 'AI Chat', 'echo-knowledge-base' ),
				'is_pro'        => false,
				'is_free'       => true,
				'features'      => array(
					__( 'Answer visitor questions instantly', 'echo-knowledge-base' ),
					__( 'Use synced KB articles as training data', 'echo-knowledge-base' ),
					__( 'Preview chat before enabling it publicly', 'echo-knowledge-base' ),
					__( 'Customize widget display and behavior', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( self::get_ai_chat_settings_admin_url(), $has_ai_features_pro, true ) ),
			array_merge( array(
				'id'            => 'ai-search',
				'icon'          => 'epkbfa epkbfa-search',
				'title'         => __( 'AI Search', 'echo-knowledge-base' ),
				'is_pro'        => false,
				'is_free'       => true,
				'features'      => array(
					__( 'Show AI-powered answers from KB content', 'echo-knowledge-base' ),
					__( 'Use synced KB articles as training data', 'echo-knowledge-base' ),
					__( 'Preview search before enabling it publicly', 'echo-knowledge-base' ),
					__( 'Configure search mode and result display', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( self::get_ai_search_settings_admin_url(), $has_ai_features_pro, true ) ),
			array_merge( array(
				'id'            => 'ai-tag-suggestions',
				'icon'          => 'epkbfa epkbfa-tags',
				'title'         => __( 'AI Tag Suggestions', 'echo-knowledge-base' ),
				'is_pro'        => false,
				'is_free'       => true,
				'features'      => array(
					__( 'AI-powered tag recommendations', 'echo-knowledge-base' ),
					__( 'Broad and specific tag categories', 'echo-knowledge-base' ),
					__( 'SEO-aware suggestions', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=content-analysis' ), $has_ai_features_pro, true ) ),
			array_merge( array(
				'id'            => 'ai-quizzes',
				'icon'          => 'epkbfa epkbfa-graduation-cap',
				'title'         => __( 'AI-Generated Quizzes', 'echo-knowledge-base' ),
				'is_pro'        => ! $has_ai_features_pro,
				'features'      => array(
					__( 'Generate quizzes from KB articles', 'echo-knowledge-base' ),
					__( 'Custom questions, answers, and explanations', 'echo-knowledge-base' ),
					__( 'Step-by-step tutorials', 'echo-knowledge-base' ),
					__( 'Show quizzes below article content', 'echo-knowledge-base' ),
				),
				'secondary_link'       => $quiz_demo_url,
				'secondary_link_label' => __( 'See Demo Quiz', 'echo-knowledge-base' ),
			), EPKB_Quizzes_Utilities::is_feature_enabled()
				? self::get_ai_feature_action_links( admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-quizzes' ), $has_ai_features_pro )
				: array(
					'settings_link'     => '',
					'doc_link'          => $has_ai_features_pro ? '' : self::get_ai_pro_features_admin_url(),
					'doc_link_external' => false,
					'enable_action'     => $has_ai_features_pro ? 'epkb_enable_quizzes' : '',
					'enable_label'      => __( 'Enable Quizzes', 'echo-knowledge-base' ),
					'enable_done_url'   => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-quizzes' ),
				)
			),
			array_merge( array(
				'id'            => 'ai-human-handoff',
				'icon'          => 'epkbfa epkbfa-group',
				'title'         => __( 'Human Handoff', 'echo-knowledge-base' ),
				'is_pro'        => ! $has_ai_features_pro,
				'features'      => array(
					__( 'Seamless AI-to-human escalation', 'echo-knowledge-base' ),
					__( 'Conversation context preserved', 'echo-knowledge-base' ),
					__( 'Email and ticket integration', 'echo-knowledge-base' ),
					__( 'Keyword-triggered handoff', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( self::get_ai_chat_settings_admin_url(), $has_ai_features_pro ) ),
			array_merge( array(
				'id'            => 'pdf-to-articles',
				'icon'          => 'epkbfa epkbfa-file-pdf-o',
				'title'         => __( 'PDF to Articles', 'echo-knowledge-base' ),
				'is_pro'        => ! $has_ai_features_pro,
				'features'      => array(
					__( 'Convert PDFs to KB articles', 'echo-knowledge-base' ),
					__( 'AI-powered formatting', 'echo-knowledge-base' ),
					__( 'Preserve document structure', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( self::get_notes_and_pdfs_admin_url(), $has_ai_features_pro ) ),
			array_merge( array(
				'id'            => 'pdf-upload',
				'icon'          => 'epkbfa epkbfa-cloud-upload',
				'title'         => __( 'PDF Upload for AI Training', 'echo-knowledge-base' ),
				'is_pro'        => ! $has_ai_features_pro,
				'features'      => array(
					__( 'Upload PDFs as AI training data', 'echo-knowledge-base' ),
					__( 'AI-powered text extraction', 'echo-knowledge-base' ),
					__( 'Multiple data collections', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( self::get_notes_and_pdfs_admin_url(), $has_ai_features_pro ) ),
			array_merge( array(
				'id'            => 'ai-glossary-generation',
				'icon'          => 'epkbfa epkbfa-book',
				'title'         => __( 'AI Glossary Generation', 'echo-knowledge-base' ),
				'is_pro'        => ! $has_ai_features_pro,
				'features'      => array(
					__( 'Auto-scan articles for terms', 'echo-knowledge-base' ),
					__( 'AI-generated definitions', 'echo-knowledge-base' ),
					__( 'Review and approve workflow', 'echo-knowledge-base' ),
					__( 'Tooltip auto-highlighting', 'echo-knowledge-base' ),
				),
			), self::get_ai_feature_action_links( self::get_glossary_admin_url( '#glossary-ai-generate' ), $has_ai_features_pro ) ),
		);
	}

	/**
	 * Get action links for an AI feature card.
	 *
	 * @param string $settings_link Feature settings URL.
	 * @param bool   $has_ai_features_pro Whether AI Features PRO is active.
	 * @param bool   $is_free Whether the feature is part of the free core set.
	 * @return array
	 */
	private static function get_ai_feature_action_links( $settings_link, $has_ai_features_pro, $is_free = false ) {
		if ( $is_free ) {
			return array(
				'settings_link'     => $settings_link,
				'doc_link'          => '',
				'doc_link_external' => false,
			);
		}

		return array(
			'settings_link'     => $has_ai_features_pro ? $settings_link : '',
			'doc_link'          => $has_ai_features_pro ? '' : self::get_ai_pro_features_admin_url(),
			'doc_link_external' => false,
		);
	}

	/**
	 * Get AI PRO Features admin URL.
	 *
	 * @return string
	 */
	private static function get_ai_pro_features_admin_url() {
		return admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=pro-features' );
	}

	/**
	 * Get AI Chat settings URL.
	 *
	 * @return string
	 */
	private static function get_ai_chat_settings_admin_url() {
		return admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=chat&active_sub_tab=chat-settings' );
	}

	/**
	 * Get AI Search settings URL.
	 *
	 * @return string
	 */
	private static function get_ai_search_settings_admin_url() {
		return admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=search&active_sub_tab=search-settings' );
	}

	/**
	 * Get Notes and PDFs admin URL.
	 *
	 * @return string
	 */
	private static function get_notes_and_pdfs_admin_url() {
		return admin_url( 'admin.php?page=aipro-all-notes' );
	}

	/**
	 * Get Glossary admin URL.
	 *
	 * @param string $hash Optional hash for a Glossary tab.
	 * @return string
	 */
	private static function get_glossary_admin_url( $hash = '' ) {
		return admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-glossary' ) . $hash;
	}

	/**
	 * Get tools link for dashboard
	 *
	 * @return array
	 */
	private static function get_tools_link() {
		return array(
			'icon' => 'epkbfa epkbfa-wrench',
			'title' => __( 'Advanced Tools & Debug', 'echo-knowledge-base' ),
			'description' => __( 'Access debug information, sync status, and advanced AI management tools.', 'echo-knowledge-base' ),
			'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=tools' ),
			'external' => false,
			'is_tools_link' => true
		);
	}
}
