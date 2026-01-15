<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI Dashboard tab with React implementation
 */
class EPKB_AI_Dashboard_Tab {
	
	public function __construct() {
		add_action( 'wp_ajax_epkb_get_ai_status', array( $this, 'ajax_get_ai_status' ) );
		add_action( 'wp_ajax_epkb_vote_for_features', array( $this, 'ajax_vote_for_features' ) );
		add_action( 'wp_ajax_epkb_check_training_data_sync', array( $this, 'ajax_check_training_data_sync' ) );
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
		
		// Add current user info for prepopulating forms
		$current_user = wp_get_current_user();
		$config['current_user'] = array(
			'first_name' => $current_user->first_name ?: $current_user->display_name,
			'email' => $current_user->user_email,
			'site_url' => get_site_url()
		);
		
		// Do a quick status check for immediate display (no API calls)
		$quick_status = self::get_ai_status( true );
		$config['status'] = $quick_status;
		
		// Show dashboard content
		$config['dashboard_stats'] = self::get_dashboard_stats();
		$config['news'] = self::get_news_items();
		$config['upcoming_features'] = self::get_upcoming_features();
		$config['quick_links'] = self::get_setup_steps();
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
		$result['has_synced_data'] = EPKB_AI_Utilities::is_ai_chat_or_search_enabled() && EPKB_AI_Training_Data_DB::count_synced_data() > 0;

		wp_send_json_success( $result );
	}

	/**
	 * AJAX handler to vote for features
	 */
	public function ajax_vote_for_features() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action' );

		// Get the submitted data
		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$site_url = get_site_url(); // Always use the actual site URL
		$features = isset( $_POST['features'] ) ? array_map( 'sanitize_text_field', $_POST['features'] ) : array();
		$other_feature_text = isset( $_POST['other_feature_text'] ) ? sanitize_textarea_field( $_POST['other_feature_text'] ) : '';

		// Validate required fields - only features are required now
		if ( empty( $features ) ) {
			wp_send_json_error( __( 'Please select at least one feature.', 'echo-knowledge-base' ) );
		}

		// Validate email only if provided
		if ( ! empty( $email ) && ! is_email( $email ) ) {
			wp_send_json_error( __( 'Please provide a valid email address.', 'echo-knowledge-base' ) );
		}

		// Build feedback message
		$feedback_message = 'User voted for features: ' . implode( ', ', $features );
		if ( ! empty( $other_feature_text ) && in_array( 'other-feature', $features ) ) {
			$feedback_message .= "\nOther feature requested: " . $other_feature_text;
		}

		// send feedback to same endpoint as deactivation form
		$vote_data = array(
			'epkb_action'       => 'epkb_process_user_feedback',
			'feedback_type'     => 'feature_vote',
			'feedback_input'    => $feedback_message,
			'plugin_name'       => 'AI',
			'plugin_version'    => class_exists('Echo_Knowledge_Base') ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version'     => '',
			'wp_version'        => '',
			'theme_info'        => '',
			'contact_user'      => $email . ' - ' . $first_name,
			'first_name'        => $first_name,
			'email_subject'     => 'Feature Vote',
		);

		// Call the API
		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $vote_data, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $vote_data,
				'sslverify' => false
			)
		);

		// Check if the request was successful
		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to submit vote. Please try again.', 'echo-knowledge-base' ) ) );
		}

		wp_send_json_success( array( 'message' => __( 'Thank you for voting! Your feedback helps us prioritize future features.', 'echo-knowledge-base' ) ) );
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
		$encrypted_key = EPKB_AI_Config_Specs::get_unmasked_api_key();
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
	 * @param bool $quick_check If true, skip OpenAI connection test for faster loading
	 * @return array Status information
	 */
	private static function check_api_key( $quick_check = false ) {
		
		$encrypted_key = EPKB_AI_Config_Specs::get_unmasked_api_key();
		
		// Check if API key exists
		if ( empty( $encrypted_key ) ) {
			return array(
				'id' => 'api_key_missing',
				'status' => 'warning',
				'message' => __( 'OpenAI API key is not configured', 'echo-knowledge-base' ),
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
		if ( ! EPKB_AI_Validation::validate_api_key_format( $api_key ) ) {
			return array(
				'id' => 'api_key_invalid_format',
				'status' => 'warning',
				'message' => __( 'API key format is invalid', 'echo-knowledge-base' ),
				'action' => __( 'Check your API key format (should start with sk-)', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' )
			);
		}
		
		// Check with OpenAI
		$client = new EPKB_OpenAI_Client();
		$test_result = $client->test_connection();
		if ( is_wp_error( $test_result ) ) {
			return array(
				'id' => 'api_key_invalid_openai',
				'status' => 'warning',
				'message' => __( 'OpenAI does not recognize the API key', 'echo-knowledge-base' ),
				'action' => __( 'Verify your API key on OpenAI dashboard', 'echo-knowledge-base' ),
				'details' => $test_result->get_error_message(),
				'link' => 'https://platform.openai.com/api-keys'
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
				// Verify the store exists in OpenAI
				$handler = new EPKB_AI_OpenAI_Vector_Store();
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
				'message' => sprintf( 
					__( '%d vector store(s) are missing in OpenAI', 'echo-knowledge-base' ), 
					count( $missing_stores ) 
				),
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
				'message' => sprintf( 
					__( 'OpenAI rate limit active for %d more minutes', 'echo-knowledge-base' ), 
					$minutes_left 
				),
				'action' => __( 'Wait for rate limit to expire', 'echo-knowledge-base' ),
				'details' => __( 'Too many requests were sent to OpenAI', 'echo-knowledge-base' )
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
		
		// Always check training data stats (needed for setup steps)
		// Training Data Statistics
		$training_data_table = $wpdb->prefix . 'epkb_ai_training_data';
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $training_data_table ) ) === $training_data_table ) {
			$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$training_data_table}" );
			$synced_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$training_data_table} WHERE status = 'synced'" );
			$failed_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$training_data_table} WHERE status = 'failed'" );
			$last_sync = $wpdb->get_var( "SELECT MAX(last_synced) FROM {$training_data_table}" );
			
			$stats['training_data'] = array(
				'icon' => 'epkbfa epkbfa-database',
				'color' => '#4F46E5',
				'title' => __( 'Training Data', 'echo-knowledge-base' ),
				'value' => intval( $synced_items ),
				'total' => intval( $total_items ),
				'synced' => intval( $synced_items ),
				'failed' => intval( $failed_items ),
				'last_sync' => $last_sync,
				'description' => sprintf( __( '%d items synced', 'echo-knowledge-base' ), $synced_items )
			);
		} else {
			$stats['training_data'] = array(
				'icon' => 'epkbfa epkbfa-database',
				'color' => '#4F46E5',
				'title' => __( 'Training Data', 'echo-knowledge-base' ),
				'value' => 0,
				'total' => 0,
				'synced' => 0,
				'failed' => 0,
				'last_sync' => null,
				'description' => __( 'No training data synced yet', 'echo-knowledge-base' )
			);
		}
		
		// Chat Statistics
		$messages_table = $wpdb->prefix . 'epkb_ai_messages';
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $messages_table ) ) === $messages_table ) {
			$total_conversations = $wpdb->get_var( "SELECT COUNT(DISTINCT conversation_id) FROM {$messages_table}" );
			$total_messages = $wpdb->get_var( "SELECT COUNT(*) FROM {$messages_table}" );
			$today_conversations = $wpdb->get_var( $wpdb->prepare( 
				"SELECT COUNT(DISTINCT conversation_id) FROM {$messages_table} WHERE DATE(created) = %s",
				current_time( 'Y-m-d' )
			) );
			
			$stats['chat'] = array(
				'icon' => 'epkbfa epkbfa-comments',
				'color' => '#10B981',
				'title' => __( 'AI Chat', 'echo-knowledge-base' ),
				'value' => intval( $today_conversations ),
				'conversations' => intval( $total_conversations ),
				'messages' => intval( $total_messages ),
				'today' => intval( $today_conversations ),
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => sprintf( __( '%d total conversations', 'echo-knowledge-base' ), $total_conversations ),
				'show_as_main' => true
			);
		} else {
			$stats['chat'] = array(
				'icon' => 'epkbfa epkbfa-comments',
				'color' => '#10B981',
				'title' => __( 'AI Chat', 'echo-knowledge-base' ),
				'value' => 0,
				'conversations' => 0,
				'messages' => 0,
				'today' => 0,
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => __( '0 total conversations', 'echo-knowledge-base' ),
				'show_as_main' => true
			);
		}
		
		// Search Statistics
		$search_logs_table = $wpdb->prefix . 'epkb_ai_search_logs';
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $search_logs_table ) ) === $search_logs_table ) {
			$total_searches = $wpdb->get_var( "SELECT COUNT(*) FROM {$search_logs_table}" );
			$today_searches = $wpdb->get_var( $wpdb->prepare( 
				"SELECT COUNT(*) FROM {$search_logs_table} WHERE DATE(created) = %s",
				current_time( 'Y-m-d' )
			) );
			
			$stats['search'] = array(
				'icon' => 'epkbfa epkbfa-search',
				'color' => '#F59E0B',
				'title' => __( 'AI Search', 'echo-knowledge-base' ),
				'value' => intval( $today_searches ),
				'total' => intval( $total_searches ),
				'today' => intval( $today_searches ),
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => sprintf( __( '%d total searches', 'echo-knowledge-base' ), $total_searches ),
				'show_as_main' => true
			);
		} else {
			$stats['search'] = array(
				'icon' => 'epkbfa epkbfa-search',
				'color' => '#F59E0B',
				'title' => __( 'AI Search', 'echo-knowledge-base' ),
				'value' => 0,
				'total' => 0,
				'today' => 0,
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => __( '0 total searches', 'echo-knowledge-base' ),
				'show_as_main' => true
			);
		}
		
		// API Token Usage Statistics
		$api_usage = get_option( 'epkb_ai_api_usage', array() );
		$monthly_tokens = isset( $api_usage['monthly_tokens'] ) ? $api_usage['monthly_tokens'] : 0;
		$monthly_cost = isset( $api_usage['monthly_cost'] ) ? $api_usage['monthly_cost'] : 0;
		
		$stats['api_usage'] = array(
			'icon' => 'epkbfa epkbfa-line-chart',
			'color' => '#EF4444',
			'title' => __( 'API Token Usage', 'echo-knowledge-base' ),
			'value' => __( 'upcoming feature', 'echo-knowledge-base' ),
			'tokens' => $monthly_tokens,
			'cost' => number_format( $monthly_cost, 2 ),
			'description' => __( 'Token counter upcoming feature', 'echo-knowledge-base' ),
			'is_coming_soon' => true,
			'bottom_text' => sprintf( __( '%s tokens used', 'echo-knowledge-base' ), number_format( $monthly_tokens ) )
		);
		
		return $stats;
	}
	
	/**
	 * Get default statistics when AI is not enabled
	 *
	 * @return array
	 */
	private static function get_default_stats() {
		return array(
			'training_data' => array(
				'icon' => 'epkbfa epkbfa-database',
				'color' => '#4F46E5',
				'title' => __( 'Training Data', 'echo-knowledge-base' ),
				'value' => 0,
				'total' => 0,
				'synced' => 0,
				'failed' => 0,
				'last_sync' => null,
				'description' => __( 'Enable AI to sync training data', 'echo-knowledge-base' )
			),
			'chat' => array(
				'icon' => 'epkbfa epkbfa-comments',
				'color' => '#10B981',
				'title' => __( 'AI Chat', 'echo-knowledge-base' ),
				'value' => 0,
				'conversations' => 0,
				'messages' => 0,
				'today' => 0,
				'description' => __( 'Today\'s Activity', 'echo-knowledge-base' ),
				'bottom_text' => __( 'Enable AI to start conversations', 'echo-knowledge-base' ),
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
				'bottom_text' => __( 'Enable AI to track searches', 'echo-knowledge-base' ),
				'show_as_main' => true
			),
			'api_usage' => array(
				'icon' => 'epkbfa epkbfa-line-chart',
				'color' => '#EF4444',
				'title' => __( 'API Token Usage', 'echo-knowledge-base' ),
				'value' => __( 'upcoming feature', 'echo-knowledge-base' ),
				'tokens' => 0,
				'cost' => '0.00',
				'description' => __( 'Token counter upcoming feature', 'echo-knowledge-base' ),
				'is_coming_soon' => true,
				'bottom_text' => __( '0 tokens used', 'echo-knowledge-base' )
			)
		);
	}
	
	/**
	 * Get news items for the dashboard
	 *
	 * @return array
	 */
	private static function get_news_items() {
		return array(
			array(
				'date' => '2025-10-27',
				'type' => 'feature',
				'title' => __( 'AI Advanced Search', 'echo-knowledge-base' ),
				'description' => __( 'New AI-powered advanced search that provides intelligent answers to user queries with relevant articles and context.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=search' )
			),
			array(
				'date' => '2025-10-05',
				'type' => 'feature',
				'title' => __( 'AI Content Analysis Released', 'echo-knowledge-base' ),
				'description' => __( 'Advanced AI-powered content analysis is now available! Get insights on content gaps, readability scores, and tag usage to improve your knowledge base.', 'echo-knowledge-base' ),
				'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-content-analysis' )
			),
			array(
				'date' => '2025-09-21',
				'type' => 'feature',
				'title' => __( 'AI Chat Page Selection', 'echo-knowledge-base' ),
				'description' => __( 'Control where AI Chat appears - choose specific pages to display the chat widget for targeted user assistance.', 'echo-knowledge-base' ),
				'link' => null
			)
		);
	}
	
	/**
	 * Get upcoming features
	 *
	 * @return array
	 */
	private static function get_upcoming_features() {
		return array(
			array(
				'id' => 'ai-content-analysis',
				'icon' => 'epkbfa epkbfa-line-chart',
				'title' => __( 'AI Content Analysis (Knowledge Base Audit)', 'echo-knowledge-base' ),
				'description' => __( 'Advanced analysis with content gap alerts, outdated article flags, and pain-point analytics to focus documentation efforts.', 'echo-knowledge-base' ),
				'released' => true,
			),
			array(
				'id' => 'pdf-support',
				'icon' => 'epkbfa epkbfa-file-pdf-o',
				'title' => __( 'PDFs - Convert to Articles or Notes', 'echo-knowledge-base' ),
				'description' => __( 'Convert PDFs into knowledge base articles or notes and include them in AI training data.', 'echo-knowledge-base' ),
			),
			array(
				'id' => 'ai-enriched-search',
				'icon' => 'epkbfa epkbfa-search',
				'title' => __( 'AI-Generated Enriched Search Results', 'echo-knowledge-base' ),
				'description' => __( 'Search results with AI-generated snippets, glossary definitions, related articles, and relevant charts for more informative results.', 'echo-knowledge-base' ),
				'released' => true,
			),
			array(
				'id' => 'ai-human-handoff',
				'icon' => 'epkbfa epkbfa-group',
				'title' => __( 'AI Chat with Human Handoff', 'echo-knowledge-base' ),
				'description' => __( 'Seamless handover from AI chatbot to human agent with conversation context preserved for frustration-free support.', 'echo-knowledge-base' ),
			),
			array(
				'id' => 'related-articles-list',
				'icon' => 'epkbfa epkbfa-list',
				'title' => __( 'Related Articles List', 'echo-knowledge-base' ),
				'description' => __( 'Show articles closely connected to user\'s search or chat question for exploring relevant content without query refinement.', 'echo-knowledge-base' ),
			),
			array(
				'id' => 'ai-glossary-terms',
				'icon' => 'epkbfa epkbfa-book',
				'title' => __( 'AI-Generated Glossary Terms', 'echo-knowledge-base' ),
				'description' => __( 'Automatic suggestions and creation of glossary definitions with minimal manual effort for improved clarity.', 'echo-knowledge-base' ),
			),
			array(
				'id' => 'search-auto-suggest',
				'icon' => 'epkbfa epkbfa-magic',
				'title' => __( 'Search Auto-Suggest', 'echo-knowledge-base' ),
				'description' => __( 'Enhanced search bar with AI-powered auto-suggestions offering completions and popular queries for faster searching.', 'echo-knowledge-base' ),
			),
			array(
				'id' => 'other-feature',
				'icon' => 'epkbfa epkbfa-lightbulb-o',
				'title' => __( 'Other Feature Not Listed', 'echo-knowledge-base' ),
				'description' => __( 'Have a different feature in mind? Select this option and tell us what you would like to see added to the AI features.', 'echo-knowledge-base' ),
				'requires_input' => true
			),
		);
	}
	
	/**
	 * Get quick links for the dashboard (Setup Steps)
	 *
	 * @return array
	 */
	private static function get_setup_steps() {
		
		// Check current status to determine which step to highlight
		$encrypted_key = EPKB_AI_Config_Specs::get_unmasked_api_key();
		$disclaimer_accepted = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' );
		$ai_search_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' );
		$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled' );
		
		$step1_complete = ! empty( $encrypted_key ) && $disclaimer_accepted === 'on';
		$features_enabled = ( $ai_search_enabled !== 'off' || $ai_chat_enabled !== 'off' );
		
		// Check if training data is synced (for initial display)
		$has_synced_data = false;
		if ( EPKB_AI_Utilities::is_ai_chat_or_search_enabled() ) {
			$has_synced_data = EPKB_AI_Training_Data_DB::count_synced_data() > 0;
		}
		
		// Check if all steps are completed
		$all_steps_complete = $step1_complete && $features_enabled && $has_synced_data;
		
		$links = array(
			'all_completed' => $all_steps_complete,
			'steps' => array(
				array(
					'icon' => 'epkbfa epkbfa-key',
					'title' => __( 'Step 1: Configure API Key & Privacy', 'echo-knowledge-base' ),
					'description' => __( 'Add your OpenAI API key and accept the data privacy agreement.', 'echo-knowledge-base' ),
					'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' ),
					'external' => false,
					'completed' => $step1_complete
				),
				array(
					'icon' => 'epkbfa epkbfa-rocket',
					'title' => __( 'Step 2: Enable AI Chat and/or AI Search', 'echo-knowledge-base' ),
					'description' => __( 'Enable AI Search or Chat features for your website.', 'echo-knowledge-base' ),
					'link' => $features_enabled ? 
						admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=chat' ) :
						admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=search&sub_tab=settings' ),
					'external' => false,
					'disabled' => ! $step1_complete,
					'completed' => $features_enabled
				),
				array(
					'icon' => 'epkbfa epkbfa-database',
					'title' => __( 'Step 3: Add Training Data & Sync', 'echo-knowledge-base' ),
					'description' => __( 'Select content and sync it to OpenAI for AI processing.', 'echo-knowledge-base' ),
					'link' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' ),
					'external' => false,
					'disabled' => ! $features_enabled,
					'completed' => $has_synced_data
				)
			)
		);
		
		/* Temporarily hidden - backend help chat
		$links[] = array(
			'icon' => 'epkbfa epkbfa-life-ring',
			'title' => __( 'Need Help? Open AI Assistant', 'echo-knowledge-base' ),
			'description' => __( 'Click here to open the "Need Help?" dialog for instant AI assistance.', 'echo-knowledge-base' ),
			'link' => '#',
			'external' => false,
			'class' => 'epkb-ai-help-trigger',
			'onclick' => 'var button = document.querySelector("#epkb-admin-help-chat-root .epkb-help-chat-button"); if(button) { button.click(); } else { alert("AI Help is loading. Please try again in a moment."); } return false;'
		);
		*/
		
		return $links;
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