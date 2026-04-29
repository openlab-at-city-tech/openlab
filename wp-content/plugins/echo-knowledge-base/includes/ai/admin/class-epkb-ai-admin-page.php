<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI Chat admin page
 */
class EPKB_AI_Admin_Page {

	public function __construct() {
		// Initialize tabs to register AJAX handlers
		new EPKB_AI_Tools_Tab();
		new EPKB_AI_Dashboard_Tab();
	}

	// top tabs - base configuration
	private $top_tabs = array(
		'dashboard' => array(
			'title' => 'Dashboard',
			'icon' => 'epkbfa epkbfa-tachometer',
			'class' => 'EPKB_AI_Dashboard_Tab',
			'requires_ai' => false
		),
		'chat' => array(
			'title' => 'Chat',
			'icon' => 'epkbfa epkbfa-comments',
			'class' => 'EPKB_AI_Chat_Tab',
			'requires_ai' => false
		),
		'search' => array(
			'title' => 'Search',
			'icon' => 'epkbfa epkbfa-search',
			'class' => 'EPKB_AI_Search_Tab',
			'requires_ai' => false
		),
		'training-data' => array(
			'title' => 'Training Data',
			'icon' => 'epkbfa epkbfa-database',
			'class' => 'EPKB_AI_Training_Data_Tab',
			// Training Data requires AI to be enabled
			'requires_ai' => true
		),
		'content-analysis' => array(
			'title' => 'Content Analysis',
			'icon' => 'epkbfa epkbfa-line-chart',
			'class' => 'EPKB_AI_Content_Analysis_Page',
			'requires_ai' => true,
			'has_sub_tabs' => true,
		),
		'general-settings' => array(
			'title' => 'General Settings',
			'icon' => 'epkbfa epkbfa-cogs',
			'class' => 'EPKB_AI_General_Settings_Tab',
			'requires_ai' => false
		),
		'pro-features' => array(
			'title' => 'PRO Features',
			'icon' => 'epkbfa epkbfa-star',
			'class' => 'EPKB_AI_PRO_Features_Tab',
			'requires_ai' => false
		),
		'tools' => array(
			'title' => 'Tools',
			'icon' => 'epkbfa epkbfa-wrench',
			'class' => 'EPKB_AI_Tools_Tab',
			'requires_ai' => true,
			'has_sub_tabs' => true,
			'hidden' => true  // Hide from main navigation but keep accessible via direct URL
		),
	);

	// Sub-tabs configuration
	private $sub_tabs = array();

	/**
	 * Get sub-tabs configuration with translations
	 *
	 * @return array
	 */
	private function get_sub_tabs() {

		if ( empty( $this->sub_tabs ) ) {
			$this->sub_tabs = array(
				'tools' => array(
					'debug' => array(
						'id' => 'debug',
						'title' => __( 'Debug Information', 'echo-knowledge-base' ),
						'icon' => 'epkbfa epkbfa-bug'
					),
				)
			);
		}
		
		return $this->sub_tabs;
	}

	/**
	 * Get tabs array with dynamic ordering based on settings
	 *
	 * @return array
	 */
	private function get_ordered_tabs() {
		$tabs = $this->top_tabs;
		
		// Hide PRO Features tab if AI Features Pro plugin is active
		if ( EPKB_AI_Utilities::is_ai_features_pro_enabled() && ! defined( 'ECHO_WP_RELEASE_VERSION' ) ) {
			unset( $tabs['pro-features'] );
		}
		
		// Remove hidden tabs from navigation display
		foreach ( $tabs as $key => $tab ) {
			if ( ! empty( $tab['hidden'] ) ) {
				unset( $tabs[$key] );
			}
		}
		
		// Always keep dashboard first, but prioritize general-settings as second if not configured
		if ( ! EPKB_AI_General_Settings_Tab::are_settings_configured() ) {
			// Extract dashboard and general-settings
			$dashboard = $tabs['dashboard'];
			$general_settings = $tabs['general-settings'];
			unset( $tabs['dashboard'] );
			unset( $tabs['general-settings'] );
			
			// Reconstruct with dashboard first, general-settings second, then the rest
			$tabs = array( 'dashboard' => $dashboard, 'general-settings' => $general_settings ) + $tabs;
		}
		
		return $tabs;
	}

	/**
	 * Display the admin page
	 */
	public function display_page() {

		EPKB_Core_Utilities::display_missing_css_message();

		// Get ordered tabs for display
		$tabs = $this->get_ordered_tabs();
		
		// Get current tab - check against all tabs including hidden ones
		$active_tab = EPKB_Utilities::get( 'active_tab', 'dashboard' );
		$active_tab = isset( $this->top_tabs[$active_tab] ) ? $active_tab : 'dashboard';

		// Pre-calculate show_get_started flag for immediate display
		// Only check if AI is enabled to avoid DB errors
		$show_get_started = !EPKB_AI_Utilities::is_ai_chat_or_search_enabled() || !EPKB_AI_Messages_DB::has_user_used_ai();

		$react_data = array(
			'active_tab' => $active_tab,
			'tabs' => array_values( array_map( function( $key, $tab ) {
				$tab_data = array(
					'key' => $key,
					'title' => $this->get_translated_tab_title( $key ),
					'icon' => $tab['icon'],
					'requires_ai' => $this->tab_requires_ai( (array)$tab )
				);
				
				// Mark dashboard tab to check for issues in background
				if ( $key === 'dashboard' ) {
					$tab_data['check_status'] = true;
				}
				
				// Add sub-tabs indicator if present
				if ( ! empty( $tab['has_sub_tabs'] ) ) {
					$tab_data['has_sub_tabs'] = true;
				}
				
				return $tab_data;
			}, array_keys( $tabs ), $tabs ) ),
			'sub_tabs' => $this->get_sub_tabs(),  // Sub-tabs configuration
			'ai_enabled' => EPKB_AI_Utilities::is_ai_configured(),  // Current AI status
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'rest_url' => esc_url_raw( rest_url() ),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( '_wpnonce_epkb_ajax_action' ),
			'i18n' => $this->get_i18n_strings(),
			'tabs_data' => $this->get_all_tabs_data( $active_tab ),  // Pre-load tab settings
			'are_settings_configured' => EPKB_AI_General_Settings_Tab::are_settings_configured(),
			'show_get_started' => $show_get_started,  // Pre-calculated for immediate display
			'discount_coupon' => EPKB_AI_PRO_Features_Tab::get_discount_coupon()
		);

		// Start the page output
		echo '<div class="wrap" id="epkb-admin-ai-page-wrap">'; ?>

		<h1></h1> <!-- This is here for WP admin consistency -->

		<?php EPKB_AI_Utilities::display_ai_pro_version_mismatch_notice(); ?>

		<div class="epkb-wrap">
			<div id="epkb-ai-admin-react-root" class="epkb-ai-config-page-react" data-epkb-ai-settings='<?php echo esc_attr( wp_json_encode( $react_data ) ); ?>'>
				<!-- Initial loading spinner - will be replaced when React mounts -->
				<div class="epkb-ai-loading-container" id="epkb-ai-initial-loader">
					<div class="epkb-loading-spinner"></div>
					<div class="epkb-ai-loading"><?php echo esc_html__( 'Loading AI Page...', 'echo-knowledge-base' ); ?></div>
				</div>
			</div>
		</div>		<?php

		echo '</div>';
	}

	/**
	 * Get translated tab title - uses literal strings for proper i18n extraction
	 *
	 * @param string $key Tab key
	 * @return string Translated title
	 */
	private function get_translated_tab_title( $key ) {
		$titles = array(
			'dashboard'        => __( 'Dashboard', 'echo-knowledge-base' ),
			'chat'             => __( 'Chat', 'echo-knowledge-base' ),
			'search'           => __( 'Search', 'echo-knowledge-base' ),
			'training-data'      => __( 'Training Data', 'echo-knowledge-base' ),
			'content-analysis'   => __( 'Content Analysis', 'echo-knowledge-base' ),
			'general-settings'   => __( 'General Settings', 'echo-knowledge-base' ),
			'pro-features'     => __( 'PRO Features', 'echo-knowledge-base' ),
			'tools'            => __( 'Tools', 'echo-knowledge-base' ),
		);
		return isset( $titles[$key] ) ? $titles[$key] : $key;
	}

	/**
	 * Get internationalization strings for React
	 *
	 * @return array
	 */
	private function get_i18n_strings() {
		return array(
			'save' => __( 'Save', 'echo-knowledge-base' ),
			'saving' => __( 'Saving...', 'echo-knowledge-base' ),
			'saved' => __( 'Saved!', 'echo-knowledge-base' ),
			'error' => __( 'Error', 'echo-knowledge-base' ),
			'success' => __( 'Success', 'echo-knowledge-base' ),
			'loading' => __( 'Loading...', 'echo-knowledge-base' ),
			'confirm' => __( 'Are you sure?', 'echo-knowledge-base' ),
			'yes' => __( 'Yes', 'echo-knowledge-base' ),
			'no' => __( 'No', 'echo-knowledge-base' ),
			'cancel' => __( 'Cancel', 'echo-knowledge-base' ),
			'ok' => __( 'OK', 'echo-knowledge-base' ),
			'reset_logs' => __( 'Reset Logs', 'echo-knowledge-base' ),
			'reset_logs_confirm' => __( 'Are you sure you want to reset all AI logs?', 'echo-knowledge-base' ),
			'logs_reset_success' => __( 'AI logs have been reset successfully.', 'echo-knowledge-base' ),
			'settings_saved' => __( 'Settings saved successfully.', 'echo-knowledge-base' ),
			'settings_save_error' => __( 'Failed to save settings. Please try again.', 'echo-knowledge-base' ),
			'disclaimer_required' => __( 'Data Privacy Agreement Required', 'echo-knowledge-base' ),
			'disclaimer_message' => __( 'To use AI features, you must accept our data privacy agreement. This ensures you understand how your data will be processed by AI services.', 'echo-knowledge-base' ),
			'go_to_settings' => __( 'Go to General Settings', 'echo-knowledge-base' ),
			'privacy_policy' => __( 'View Privacy Policy', 'echo-knowledge-base' ),
			'content_analysis' => __( 'Content Analysis', 'echo-knowledge-base' ),
			'analyze' => __( 'Analyze', 'echo-knowledge-base' ),
			'improve' => __( 'Improve', 'echo-knowledge-base' ),
			'analyzing' => __( 'Analyzing...', 'echo-knowledge-base' ),
			'analysis_complete' => __( 'Analysis complete!', 'echo-knowledge-base' ),
			'analysis_failed' => __( 'Analysis failed. Please try again.', 'echo-knowledge-base' ),
			'ai_disabled_message' => __( 'AI features are not configured. Please add your API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' ),
			'go_to_ai_settings' => __( 'Go to AI Settings', 'echo-knowledge-base' ),
			'upload_pdfs_to_ai_store' => __( 'Upload PDFs to AI Store', 'echo-knowledge-base' ),
			'no_pdfs_uploaded_yet' => __( 'No PDFs uploaded yet', 'echo-knowledge-base' ),
			'demo_analytics_badge' => __( 'Demo', 'echo-knowledge-base' ),
			'demo_analytics_message' => __( 'This is demo analytics data for demonstration purposes.', 'echo-knowledge-base' ),
			'demo_analytics_notice' => __( 'Demo Analytics', 'echo-knowledge-base' ),
		);
	}

	/**
	 * Get all tabs data at once for initial page load
	 *
	 * @param string $active_tab
	 * @return array
	 */
	private function get_all_tabs_data( $active_tab ) {
		$tabs_data = array();
		// Use top_tabs instead of get_ordered_tabs() to include hidden tabs
		$tabs = $this->top_tabs;
		
		foreach ( $tabs as $tab_key => $tab ) {
			// Skip loading dashboard data unless it is the active tab
			if ( $tab_key === 'dashboard' && $active_tab !== 'dashboard' ) {
				continue;
			}

			$tabs_data[$tab_key] = $this->get_tab_config( $tab_key, $tab );
		}
		
		return $tabs_data;
	}

	/**
	 * Get the tab configuration with AI enabled check
	 *
	 * @param string $tab_key
	 * @param array $tab
	 * @return array
	 */
	private function get_tab_config( $tab_key, $tab ) {
		// Check if this tab requires AI to be enabled
		if ( $this->tab_requires_ai( $tab ) && ! EPKB_AI_Utilities::is_ai_configured() ) {
			return $this->get_ai_disabled_config( $tab_key, $tab['title'] );
		}

		// Get the tab configuration from the tab class
		$config = call_user_func( array( $tab['class'], 'get_tab_config' ) );

		// Add sub-tabs information if this tab has sub-tabs
		$sub_tabs = $this->get_sub_tabs();
		if ( ! empty( $tab['has_sub_tabs'] ) && isset( $sub_tabs[$tab_key] ) ) {
			$config['sub_tabs'] = $sub_tabs[$tab_key];
		}

		// Add active_sub_tab from URL if present (for chat and search tabs)
		if ( in_array( $tab_key, array( 'chat', 'search' ) ) ) {
			$active_sub_tab = EPKB_Utilities::get( 'active_sub_tab', '' );
			if ( ! empty( $active_sub_tab ) ) {
				$config['active_sub_tab'] = $active_sub_tab;
			}
		}

		return $config;
	}

	/**
	 * Check if a tab requires AI to be enabled
	 *
	 * @param array $tab
	 * @return bool
	 */
	private function tab_requires_ai( $tab ) {
		return isset( $tab['requires_ai'] ) && $tab['requires_ai'];
	}

	/**
	 * Get configuration when AI is disabled
	 *
	 * @param string $tab_key
	 * @param string $tab_title
	 * @return array
	 */
	private function get_ai_disabled_config( $tab_key, $tab_title ) {
		// Base config (avoid repeating the full array)
		$base = array(
			'tab_id' => $tab_key,
			'title' => $tab_title,
			'ai_disabled' => true,
			'message' => __( 'AI Features Required', 'echo-knowledge-base' ),
		);

		// Custom instructions per tab
		$instructions = __( 'To use AI features, please configure your API key and accept the data privacy agreement in General Settings, then enable AI Search or AI Chat.', 'echo-knowledge-base' );
		if ( $tab_key === 'training-data' ) {
			$instructions = __( 'To use Training Data, please enable either AI Chat or AI Search in their respective tabs.', 'echo-knowledge-base' );
		} elseif ( $tab_key === 'content-analysis' ) {
			$instructions = __( 'To use Content Analysis, please configure your API key and accept the data privacy agreement in General Settings.', 'echo-knowledge-base' );
		}

		$base['instructions'] = $instructions;
		return $base;
	}

	/**
	 * Get the active Training Data collection ID from the current request.
	 *
	 * @param array $collections Available collections keyed by collection ID.
	 * @return int
	 */
	private static function get_requested_training_data_collection_id( $collections ) {

		if ( empty( $collections ) || ! is_array( $collections ) ) {
			return 0;
		}

		if ( ! empty( $_GET['active_sub_tab'] ) ) {
			$active_sub_tab = sanitize_text_field( wp_unslash( $_GET['active_sub_tab'] ) );
			if ( preg_match( '/^collection-(\d+)$/', $active_sub_tab, $matches ) ) {
				$collection_id = absint( $matches[1] );
				if ( isset( $collections[ $collection_id ] ) ) {
					return $collection_id;
				}
			}
		}

		$collection_ids = array_keys( $collections );
		return (int) reset( $collection_ids );
	}

	/**
	 * Get setup steps configuration for a specific tab
	 *
	 * @param string $tab_id Tab identifier: 'chat', 'search', or 'general-settings'
	 * @return array Setup steps configuration with steps array, all_completed flag, and doc_link
	 */
	public static function get_setup_steps_for_tab( $tab_id ) {

		// Get current status values
		$encrypted_key = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( EPKB_AI_Provider::get_active_provider() );
		$disclaimer_accepted = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' ) === 'on';
		$ai_search_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' ) !== 'off';
		$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled' ) !== 'off';

		// Check if at least one collection has data synced to vector store for the current provider
		$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections();
		$collection_ids_with_store = array();
		$active_training_collection_id = 0;
		$active_collection_has_data = false;
		$active_collection_has_synced_data = false;
		if ( ! is_wp_error( $collections ) ) {
			foreach ( $collections as $collection_id => $collection ) {
				if ( ! empty( $collection['ai_training_data_store_id'] ) ) {
					$collection_ids_with_store[] = $collection_id;
				}
			}

			$active_training_collection_id = self::get_requested_training_data_collection_id( $collections );
			if ( ! empty( $active_training_collection_id ) ) {
				$training_data_db = new EPKB_AI_Training_Data_DB();
				$active_collection_stats = $training_data_db->get_status_statistics( $active_training_collection_id );
				$active_collection_has_data = ! empty( $active_collection_stats['total'] );
				$active_collection_has_synced_data = ! empty( $active_collection_stats['synced'] );
			}
		}
		$has_synced_data = ! empty( $collection_ids_with_store ) && EPKB_AI_Training_Data_DB::count_synced_data( $collection_ids_with_store ) > 0;

		$step1_complete = ! empty( $encrypted_key ) && $disclaimer_accepted;
		$admin_url_base = admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features' );

		// Build steps based on tab
		switch ( $tab_id ) {

			case 'chat':
				// Check if a training data collection is selected for chat
				$ai_config = EPKB_AI_Config_Specs::get_ai_config();
				$chat_collection_selected = ! empty( $ai_config['ai_chat_display_collection'] );

				// Hide setup steps if all conditions are met: training data synced, collection selected, and chat enabled
				if ( $has_synced_data && $chat_collection_selected && $ai_chat_enabled ) {
					return array(
						'all_completed' => true,
						'steps'         => array(),
						'doc_link'      => ''
					);
				}

				$steps = array(
					array(
						'title'       => __( 'Add Training Data', 'echo-knowledge-base' ),
						'description' => __( 'Select content and sync it to AI storage.', 'echo-knowledge-base' ),
						'completed'   => $has_synced_data,
						'disabled'    => ! $step1_complete,
						'link'        => $admin_url_base . '&active_tab=training-data',
						'link_tab'    => 'training-data',
						'doc_link'    => 'https://www.echoknowledgebase.com/documentation/step-2-add-ai-training-data/'
					),
					array(
						'title'            => __( 'Configure AI Chat', 'echo-knowledge-base' ),
						'description'      => __( 'Set up display mode, select training data, and enable AI Chat.', 'echo-knowledge-base' ),
						'completed'        => $ai_chat_enabled && $chat_collection_selected,
						'disabled'         => ! $has_synced_data,
						'pointer_sub_tab'  => 'chat-settings',
						'pointer_sequence' => array(
							array(
								'target'  => '.epkb-ai-chat-display-mode',
								'title'   => __( 'Display Mode', 'echo-knowledge-base' ),
								'content' => '<ul style="margin: 10px 0 10px 20px; list-style: disc;">' .
									'<li><strong>' . __( 'Show Everywhere', 'echo-knowledge-base' ) . '</strong> - ' . __( 'Chat widget appears on all pages', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( 'Only Show On', 'echo-knowledge-base' ) . '</strong> - ' . __( 'Chat widget only appears on selected pages', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( "Don't Show On", 'echo-knowledge-base' ) . '</strong> - ' . __( 'Chat widget appears everywhere except selected pages', 'echo-knowledge-base' ) . '</li>' .
								'</ul>'
							),
							array(
								'target'  => '.epkb-ai-chat-collection-select',
								'title'   => __( 'Training Data Collection', 'echo-knowledge-base' ),
								'content' => __( 'Select the training data collection that AI Chat will use to answer questions.', 'echo-knowledge-base' )
							),
							array(
								'target'  => '.epkb-ai-chat-mode',
								'title'   => __( 'AI Chat Mode', 'echo-knowledge-base' ),
								'content' => '<ul style="margin: 10px 0 10px 20px; list-style: disc;">' .
									'<li><strong>' . __( 'Off', 'echo-knowledge-base' ) . '</strong> - ' . __( 'AI Chat is disabled', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( 'Preview', 'echo-knowledge-base' ) . '</strong> - ' . __( 'Only administrators can see and test AI Chat', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( 'On', 'echo-knowledge-base' ) . '</strong> - ' . __( 'AI Chat is publicly available to all visitors', 'echo-knowledge-base' ) . '</li>' .
								'</ul>'
							),
							array(
								'target'  => '.epkb-ai-sub-tabs-save-button',
								'title'   => __( 'Save Settings', 'echo-knowledge-base' ),
								'content' => __( 'Click Save Settings to apply your changes. After saving, you can view the AI Chat on your website.', 'echo-knowledge-base' )
							)
						)
					)
				);
				$doc_link = 'https://www.echoknowledgebase.com/documentation/step-3b-enable-ai-chat/';
				break;

			case 'search':
				// Hide setup steps if all conditions are met: training data synced and search enabled
				if ( $has_synced_data && $ai_search_enabled ) {
					return array(
						'all_completed' => true,
						'steps'         => array(),
						'doc_link'      => ''
					);
				}

				$steps = array(
					array(
						'title'       => __( 'Add Training Data', 'echo-knowledge-base' ),
						'description' => __( 'Select content and sync it to AI storage.', 'echo-knowledge-base' ),
						'completed'   => $has_synced_data,
						'disabled'    => ! $step1_complete,
						'link'        => $admin_url_base . '&active_tab=training-data',
						'link_tab'    => 'training-data',
						'doc_link'    => 'https://www.echoknowledgebase.com/documentation/step-2-add-ai-training-data/'
					),
					array(
						'title'            => __( 'Configure AI Search', 'echo-knowledge-base' ),
						'description'      => __( 'Select training data and enable AI Search for your visitors.', 'echo-knowledge-base' ),
						'completed'        => $ai_search_enabled,
						'disabled'         => ! $has_synced_data,
						'pointer_sub_tab'  => 'search-settings',
						'pointer_sequence' => array(
							array(
								'target'  => '.epkb-ai-field-kb-collection-mapping',
								'title'   => __( 'Select Training Data for Each KB', 'echo-knowledge-base' ),
								'content' => __( 'Choose which Training Data Collection each Knowledge Base should use for AI Search results.', 'echo-knowledge-base' )
							),
							array(
								'target'  => '.epkb-ai-search-enabled',
								'title'   => __( 'Enable AI Search', 'echo-knowledge-base' ),
								'content' => '<ul style="margin: 10px 0 10px 20px; list-style: disc;">' .
									'<li><strong>' . __( 'Off', 'echo-knowledge-base' ) . '</strong> - ' . __( 'AI Search is disabled', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( 'Preview', 'echo-knowledge-base' ) . '</strong> - ' . __( 'Only administrators can see and test AI Search', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( 'On', 'echo-knowledge-base' ) . '</strong> - ' . __( 'AI Search is publicly available to all visitors', 'echo-knowledge-base' ) . '</li>' .
								'</ul>'
							),
							array(
								'target'  => '.epkb-ai-search-mode',
								'title'   => __( 'AI Search Display Mode', 'echo-knowledge-base' ),
								'content' => '<ul style="margin: 10px 0 10px 20px; list-style: disc;">' .
									'<li><strong>' . __( 'Simple Search', 'echo-knowledge-base' ) . '</strong> - ' . __( 'Shows a simple "Ask AI?" button for quick Q&A', 'echo-knowledge-base' ) . '</li>' .
									'<li><strong>' . __( 'Smart Search', 'echo-knowledge-base' ) . '</strong> - ' . __( 'Shows an advanced multi-column results layout with AI answers, matching articles, and more', 'echo-knowledge-base' ) . '</li>' .
								'</ul>'
							),
							array(
								'target'  => '.epkb-ai-sub-tabs-save-button',
								'title'   => __( 'Save Settings', 'echo-knowledge-base' ),
								'content' => __( 'Click Save Settings to apply your changes. After saving, you can view AI Search on your website.', 'echo-knowledge-base' )
							)
						)
					)
				);
				$doc_link = 'https://www.echoknowledgebase.com/documentation/step-3-enable-ai-search/';
				break;

			case 'general-settings':
				$active_provider = EPKB_AI_Provider::get_active_provider();
				$api_key_field_id = $active_provider === EPKB_AI_Provider::PROVIDER_GEMINI
					? '#epkb-ai-api_settings-ai_gemini_key'
					: '#epkb-ai-api_settings-ai_chatgpt_key';

				$steps = array(
					array(
							'title'          => __( 'Choose AI Provider', 'echo-knowledge-base' ),
							'description'    => __( 'Select Google Gemini or OpenAI ChatGPT as your AI provider.', 'echo-knowledge-base' ),
							'completed'      => true,  // Always completed since a provider is always selected
							'doc_link'       => 'https://www.echoknowledgebase.com/documentation/setup-ai-provider-and-key/',
							'pointer_target' => '#epkb-ai-api_settings-ai_provider',
							'pointer_title'  => __( 'AI Provider Selection', 'echo-knowledge-base' ),
							'pointer_content' => __( 'Google Gemini is recommended for its speed and cost-effectiveness. Select your preferred AI provider here.', 'echo-knowledge-base' )
					),
					array(
							'title'          => __( 'Enter API Key', 'echo-knowledge-base' ),
							'description'    => __( 'Add your API key for the selected provider.', 'echo-knowledge-base' ),
							'completed'      => ! empty( $encrypted_key ),
							'doc_link'       => 'https://www.echoknowledgebase.com/documentation/setup-ai-provider-and-key/',
							'pointer_target' => $api_key_field_id,
							'pointer_title'  => __( 'API Key', 'echo-knowledge-base' ),
							'pointer_content' => __( 'Enter your API key here. You can get a free API key from Google AI Studio for Gemini or directly from OpenAI.', 'echo-knowledge-base' )
						),
					array(
						'title'          => __( 'Accept Privacy Terms', 'echo-knowledge-base' ),
						'description'    => __( 'Read and accept the data privacy agreement.', 'echo-knowledge-base' ),
						'completed'      => $disclaimer_accepted,
						'disabled'       => empty( $encrypted_key ),
						'doc_link'       => 'https://www.echoknowledgebase.com/documentation/setup-ai-provider-and-key/',
						'pointer_target' => '.epkb-ai-disclaimer-text',
						'pointer_title'  => __( 'Privacy Agreement', 'echo-knowledge-base' ),
						'pointer_content' => __( 'Review the privacy disclaimer and check the box to accept it before using AI features.', 'echo-knowledge-base' )
					)
				);
				$doc_link = 'https://www.echoknowledgebase.com/documentation/setup-ai-provider-and-key/';
				break;

			case 'training-data':
				// Hide setup steps if active collection has synced data
				if ( $active_collection_has_synced_data ) {
					return array(
						'all_completed' => true,
						'steps'         => array(),
						'doc_link'      => ''
					);
				}

				$has_collection = ! is_wp_error( $collections ) && count( $collections ) > 0;

				$steps = array(
					array(
						'title'           => __( 'Add New Collection', 'echo-knowledge-base' ),
						'description'     => __( 'Create a collection to organize your training data.', 'echo-knowledge-base' ),
						'completed'       => $has_collection,
						'disabled'        => ! $step1_complete,
						'doc_link'        => 'https://www.echoknowledgebase.com/documentation/step-2-add-ai-training-data/',
						'pointer_target'  => '.epkb-ai-sub-tab-add-new',
						'pointer_title'   => __( 'Add New Collection', 'echo-knowledge-base' ),
						'pointer_content' => __( 'Click here to create a new Training Data Collection. Collections let you organize different sets of content to train AI for specific purposes.', 'echo-knowledge-base' )
					),
					array(
						'title'           => __( 'Choose Training Data', 'echo-knowledge-base' ),
						'description'     => __( 'Select which content to include in your collection.', 'echo-knowledge-base' ),
						'completed'       => $active_collection_has_data,
						'disabled'        => ! $has_collection,
						'doc_link'        => 'https://www.echoknowledgebase.com/documentation/step-2-add-ai-training-data/',
						'pointer_target'  => '.epkb-ai-action-choose-data',
						'pointer_title'   => __( 'Choose Training Data', 'echo-knowledge-base' ),
						'pointer_content' => __( 'Click this button to select the content you want to train AI with. You can include Knowledge Base articles, FAQs, pages, and custom content.', 'echo-knowledge-base' )
					),
					array(
						'title'           => __( 'Send Data to AI', 'echo-knowledge-base' ),
						'description'     => __( 'Sync your training data to the AI provider.', 'echo-knowledge-base' ),
						'completed'       => $active_collection_has_synced_data,
						'disabled'        => ! $active_collection_has_data,
						'doc_link'        => 'https://www.echoknowledgebase.com/documentation/step-2-add-ai-training-data/',
						'pointer_target'  => '.epkb-ai-action-sync-data',
						'pointer_title'   => __( 'Send Data to AI', 'echo-knowledge-base' ),
						'pointer_content' => __( 'Click this button to sync your training data. Select items in the table, then send them to your AI provider to enable AI Search and AI Chat.', 'echo-knowledge-base' )
					)
				);
				$doc_link = 'https://www.echoknowledgebase.com/documentation/step-2-add-ai-training-data/';
				break;

			default:
				return array(
					'all_completed' => true,
					'steps'         => array(),
					'doc_link'      => ''
				);
		}

		// Check if all steps are completed
		$all_completed = true;
		foreach ( $steps as $step ) {
			if ( empty( $step['completed'] ) ) {
				$all_completed = false;
				break;
			}
		}

		return array(
			'all_completed' => $all_completed,
			'steps'         => $steps,
			'doc_link'      => $doc_link
		);
	}
}
