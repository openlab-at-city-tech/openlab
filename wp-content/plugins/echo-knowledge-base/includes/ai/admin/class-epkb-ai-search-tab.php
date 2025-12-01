<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI Search tab
 */
class EPKB_AI_Search_Tab {

	/**
	 * Constructor - register AJAX handlers
	 */
	public function __construct() {
		add_action( 'wp_ajax_epkb_ai_apply_search_preset', array( __CLASS__, 'ajax_apply_search_preset' ) );
	}

	/**
	 * Get the configuration for the Search tab
	 *
	 * @return array
	 */
	public static function get_tab_config() {

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$has_ai_features_pro = EPKB_Utilities::is_ai_features_pro_enabled();

		$config = array(
			'tab_id' => 'search',
			'title' => __( 'Search', 'echo-knowledge-base' ),
			'sub_tabs' => self::get_sub_tabs_config(),
			'settings_sections' => self::get_settings_sections( $ai_config ),
			'ai_config' => $ai_config
		);

		// Add PRO feature ad HTML when ai-features-pro is not installed
		if ( ! $has_ai_features_pro ) {
			$config['ai_pro_ad_html'] = self::get_pro_feature_ad();
		}

		return $config;
	}

	/**
	 * Get sub-tabs configuration
	 *
	 * @return array
	 */
	private static function get_sub_tabs_config() {
		return array(
			'search-history' => array(
				'id' => 'search-history',
				'title' => __( 'Search History', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-search'
			),
			'search-settings' => array(
				'id' => 'search-settings',
				'title' => __( 'Search Settings', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-list-alt'
			)
		);
	}

	/**
	 * Get settings sections configuration
	 *
	 * @param array $ai_config
	 * @return array
	 */
	private static function get_settings_sections( $ai_config ) {

		// Check if ai-features-pro is installed
		$has_ai_features_pro = EPKB_Utilities::is_ai_features_pro_enabled();

		// Get preset options for search
		$search_presets = EPKB_OpenAI_Client::get_model_presets( 'search' );
		$preset_options = array();
		foreach ( $search_presets as $key => $preset ) {
			// Add (default) to the fastest preset
			if ( $key === 'fastest' ) {
				$preset_options[$key] = $preset['label'] . ': ' . $preset['description'] . ' ' . __( '(default)', 'echo-knowledge-base' );
			} else {
				$preset_options[$key] = $preset['label'] . ': ' . $preset['description'];
			}
		}
		
		// Determine current preset based on settings
		$current_preset = 'custom';
		
		// Check if current settings match any preset
		foreach ( $search_presets as $key => $preset ) {

			if ( $key == 'custom' ) {
				continue; // Skip custom preset
			}

			$matches = true;

			// Check model (always present in non-custom presets)
			if ( isset( $preset['model'] ) && $preset['model'] != $ai_config['ai_search_model'] ) {
				$matches = false;
			}

			// Check verbosity if present in preset (GPT-5 models)
			if ( $matches && isset( $preset['verbosity'] ) && $preset['verbosity'] != $ai_config['ai_search_verbosity'] ) {
				$matches = false;
			}

			// Check reasoning if present in preset (GPT-5 models)
			if ( $matches && isset( $preset['reasoning'] ) && $preset['reasoning'] != $ai_config['ai_search_reasoning'] ) {
				$matches = false;
			}

			// Check temperature ONLY if it's defined in the preset (GPT-4 models)
			if ( $matches && isset( $preset['temperature'] ) ) {
				if ( abs( floatval( $preset['temperature'] ) - floatval( $ai_config['ai_search_temperature'] ) ) >= 0.01 ) {
					$matches = false;
				}
			}

			// Check max_output_tokens if present in preset (compare as integers)
			if ( $matches && isset( $preset['max_output_tokens'] ) && intval( $preset['max_output_tokens'] ) != intval( $ai_config['ai_search_max_output_tokens'] ) ) {
				$matches = false;
			}

			// Check top_p ONLY if it's defined in the preset (GPT-4 models)
			if ( $matches && isset( $preset['top_p'] ) ) {
				if ( abs( floatval( $preset['top_p'] ) - floatval( $ai_config['ai_search_top_p'] ) ) >= 0.01 ) {
					$matches = false;
				}
			}

			if ( $matches ) {
				$current_preset = $key;
				break;
			}
		}
		
		// Default to fastest preset if settings match the default configuration
		if ( $current_preset == 'custom' ) {
			if ( $ai_config['ai_search_model'] == 'gpt-5-nano' && 
				 $ai_config['ai_search_verbosity'] == 'low' && 
				 $ai_config['ai_search_reasoning'] == 'low' ) {
				$current_preset = 'fastest';
			}
		}
		
		// Build Custom Model Parameters fields (shown when preset = custom)
		// Send ALL parameters to JavaScript for dynamic switching
		$search_model = isset( $ai_config['ai_search_model'] ) ? $ai_config['ai_search_model'] : EPKB_OpenAI_Client::DEFAULT_MODEL;
		$model_spec = EPKB_OpenAI_Client::get_models_and_default_params( $search_model );
		$custom_param_fields = array();
		
		// Model selection
		$custom_param_fields['ai_search_model'] = array(
			'type' => 'select',
			'label' => __( 'Search Model', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_search_model'],
			'options' => EPKB_AI_Config_Specs::get_field_options( 'ai_search_model' )
		);
		
		// Include BOTH GPT-5 and GPT-4 parameters for dynamic JavaScript switching
		// GPT-5 parameters
		$custom_param_fields['ai_search_verbosity'] = array(
			'type' => 'select',
			'label' => __( 'Verbosity', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_search_verbosity'],
			'options' => array(
				'low' => __( 'Low', 'echo-knowledge-base' ),
				'medium' => __( 'Medium', 'echo-knowledge-base' ),
				'high' => __( 'High', 'echo-knowledge-base' ),
			),
			'description' => __( 'Controls search result verbosity', 'echo-knowledge-base' ),
		);
		$custom_param_fields['ai_search_reasoning'] = array(
			'type' => 'select',
			'label' => __( 'Reasoning', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_search_reasoning'],
			'options' => array(
				'low' => __( 'Low', 'echo-knowledge-base' ),
				'medium' => __( 'Medium', 'echo-knowledge-base' ),
				'high' => __( 'High', 'echo-knowledge-base' ),
			),
			'description' => __( 'Controls search reasoning depth', 'echo-knowledge-base' ),
		);
		
		// GPT-4 parameters
		$custom_param_fields['ai_search_temperature'] = array(
			'type' => 'number',
			'label' => __( 'Temperature', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_search_temperature'],
			'min' => 0,
			'max' => 1,
			'step' => 0.1,
			'description' => __( 'Lower values for more accurate search results', 'echo-knowledge-base' )
		);
		$custom_param_fields['ai_search_top_p'] = array(
			'type' => 'number',
			'label' => __( 'Top P', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_search_top_p'],
			'min' => 0,
			'max' => 1,
			'step' => 0.1,
			'description' => __( 'Controls result diversity', 'echo-knowledge-base' )
		);
		
		// Max tokens for all models
		$max_limit = isset( $model_spec['max_output_tokens_limit'] ) ? $model_spec['max_output_tokens_limit'] : EPKB_OpenAI_Client::DEFAULT_MAX_OUTPUT_TOKENS;
		$custom_param_fields['ai_search_max_output_tokens'] = array(
			'type' => 'number',
			'label' => __( 'Max Tokens', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_search_max_output_tokens'],
			'min' => 50,
			'max' => $max_limit,
			'description' => __( 'Maximum search result length in tokens', 'echo-knowledge-base' )
		);

		// Get layout presets and build options
		$layout_presets = self::get_search_results_presets();
		$layout_preset_options = array();
		foreach ( $layout_presets as $key => $preset ) {
			$layout_preset_options[$key] = $preset['name'] . ' - ' . $preset['description'];
		}
		$layout_preset_options['custom'] = __( 'Custom', 'echo-knowledge-base' ) . ' - ' . __( 'Configure your own layout', 'echo-knowledge-base' );

		// Determine current layout preset based on settings
		$current_layout_preset = 'custom';
		foreach ( $layout_presets as $key => $preset ) {
			$matches = true;
			foreach ( $preset['settings'] as $setting_key => $setting_value ) {
				if ( $ai_config[$setting_key] != $setting_value ) {
					$matches = false;
					break;
				}
			}
			if ( $matches ) {
				$current_layout_preset = $key;
				break;
			}
		}

		$sections = array(
			'search_results_general' => array(
				'id' => 'search_results_general',
				'title' => __( 'General Settings', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-cog',
				'sub_tab' => 'search-settings',
				'fields' => array(
					'ai_search_enabled' => array(
						'type' => 'radio',
						'label' => __( 'Enable AI Search', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_enabled'],
						'options' => array(
							'off'     => __( 'Off', 'echo-knowledge-base' ),
							'preview' => __( 'Preview (Admins only)', 'echo-knowledge-base' ),
							'on'      => __( 'On (Public)', 'echo-knowledge-base' )
						),
						'description' => __( 'Control AI Search visibility: Off (disabled), Preview (admins only for testing), or On (public access)', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-enabled'
					),
					'ai_search_mode' => array(
						'type' => 'radio',
						'label' => __( 'AI Search Display Mode', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_mode'],
						'options' => array(
							'simple_search'   => __( 'Simple Search Results', 'echo-knowledge-base' ),
							'advanced_search' => __( 'Advanced Search Results', 'echo-knowledge-base' )
						),
						'description' => __( 'Choose which AI search experience to display: Ask AI shows a simple Q&A button/interface, Search Results shows an advanced multi-column results layout', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-mode'
					),
					'ai_advanced_search_shortcode' => array(
						'type' => 'html',
						'html' => self::get_ai_advanced_search_shortcode_box(),
						'field_class' => 'epkb-ai-mode-advanced_search'
					),
					'ai_search_immediate_query' => array(
						'type' => 'checkbox',
						'label' => __( 'Immediate AI Query', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_immediate_query'],
						'description' => __( 'When enabled, AI will automatically query when a search is submitted instead of showing "Ask AI?" button', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-immediate-query epkb-ai-mode-simple_search'
					),
					'ai_search_ask_button_text' => array(
						'type' => 'text',
						'label' => __( 'AI Search Button Text', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_ask_button_text'],
						'description' => __( 'Text displayed on the AI search button', 'echo-knowledge-base' ),
						'placeholder' => __( 'Ask AI?', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-button-text epkb-ai-mode-simple_search'
					),
					'ai_search_instructions' => array(
						'type' => 'textarea',
						'label' => __( 'AI Search Instructions', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_instructions'],
						'description' => __( 'Warning: Modifying these instructions is challenging and can significantly impact AI performance. The AI is highly sensitive to instruction changes - even small modifications can cause unexpected behavior.', 'echo-knowledge-base' ),
						'rows' => 8,
						'default' => EPKB_AI_Config_Specs::get_default_value( 'ai_search_instructions' ),
						'show_reset' => true,
						'field_class' => 'epkb-ai-mode-simple_search'
					),
					'ai_search_preset' => array(
						'type' => 'select',
						'label' => __( 'Choose AI Behavior', 'echo-knowledge-base' ),
						'value' => $current_preset,
						'options' => $preset_options,
						'description' => $current_preset === 'custom' ?
							__( 'Custom model parameters are active. Adjust them in Search Settings tab.', 'echo-knowledge-base' ) :
							__( 'Select an AI behavior preset that best fits your needs. To customize parameters, choose "Custom".', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-behavior-preset-select' . ( ! $has_ai_features_pro ? ' epkb-ai-mode-simple_search' : '' )
					)
				)
			),

			'search_results_columns' => array(
				'id' => 'search_results_columns',
				'title' => __( 'Column Configuration', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-columns',
				'sub_tab' => 'search-settings',
				'fields' => $has_ai_features_pro ? array(
					'ai_search_results_layout_preset' => array(
						'type' => 'select',
						'label' => __( 'Choose Layout Preset', 'echo-knowledge-base' ),
						'value' => $current_layout_preset,
						'options' => $layout_preset_options,
						'description' => $current_layout_preset === 'custom' ?
							__( 'Custom layout is active. Adjust settings below to customize your search results layout.', 'echo-knowledge-base' ) :
							__( 'Select a layout preset that best fits your needs. To customize individual settings, choose "Custom".', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-layout-preset-select epkb-ai-mode-advanced_search'
					),
					'ai_search_results_width' => array(
						'type' => 'text',
						'label' => __( 'Results Width', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_width'],
						'description' => __( 'Width of the search results container (e.g., 60%, 800px)', 'echo-knowledge-base' ),
						'placeholder' => '60%',
						'field_class' => 'epkb-ai-mode-advanced_search'
					),
					'ai_search_results_separator' => array(
						'type' => 'select',
						'label' => __( 'Section Separator', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_separator'],
						'options' => array(
							'none' => __( 'None', 'echo-knowledge-base' ),
							'shaded-box' => __( 'Shaded Box', 'echo-knowledge-base' ),
							'line' => __( 'Line Separator', 'echo-knowledge-base' )
						),
						'description' => __( 'Visual separator between sections', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-mode-advanced_search'
					),
					'ai_search_results_num_columns' => array(
						'type' => 'select',
						'label' => __( 'Number of Columns', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_num_columns'],
						'options' => array(
							'1' => __( '1 Column', 'echo-knowledge-base' ),
							'2' => __( '2 Columns', 'echo-knowledge-base' ),
							'3' => __( '3 Columns', 'echo-knowledge-base' )
						),
						'description' => __( 'Number of columns to display search results', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-results-num-columns epkb-ai-mode-advanced_search'
					),
					'ai_search_results_column_widths' => array(
						'type' => 'select',
						'label' => __( 'Column Widths', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_column_widths'],
						'options' => self::get_column_width_options( $ai_config['ai_search_results_num_columns'] ),
						'description' => __( 'Width distribution across columns', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-results-column-widths epkb-ai-mode-advanced_search'
					)
				) : array(
					'ai_pro_ad' => array(
						'type' => 'html',
						'html' => self::get_pro_feature_ad()
					)
				)
			),

			'search_results_column_sections' => array(
				'id' => 'search_results_column_sections',
				'title' => __( 'Column Sections', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-th-list',
				'sub_tab' => 'search-settings',
				'fields' => array(
					'ai_search_results_column_1_sections' => array(
						'type' => 'sections_manager',
						'label' => self::get_column_label( 1, $ai_config['ai_search_results_num_columns'] ),
						'value' => $ai_config['ai_search_results_column_1_sections'],
						'column_number' => 1,
						'available_sections' => self::get_available_sections(),
						'description' => __( 'Configure sections for this column', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-mode-advanced_search'
					),
					'ai_search_results_column_2_sections' => array(
						'type' => 'sections_manager',
						'label' => self::get_column_label( 2, $ai_config['ai_search_results_num_columns'] ),
						'value' => $ai_config['ai_search_results_column_2_sections'],
						'column_number' => 2,
						'available_sections' => self::get_available_sections(),
						'description' => __( 'Configure sections for this column', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-results-column-2 epkb-ai-mode-advanced_search'
					),
					'ai_search_results_column_3_sections' => array(
						'type' => 'sections_manager',
						'label' => self::get_column_label( 3, $ai_config['ai_search_results_num_columns'] ),
						'value' => $ai_config['ai_search_results_column_3_sections'],
						'column_number' => 3,
						'available_sections' => self::get_available_sections(),
						'description' => __( 'Configure sections for this column', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-results-column-3 epkb-ai-mode-advanced_search'
					)
				)
			),

			'search_results_sections' => array(
				'id' => 'search_results_sections',
				'title' => __( 'Section Labels Configuration', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-list',
				'sub_tab' => 'search-settings',
				'fields' => array(
					'ai_search_results_matching_articles_name' => array(
						'type' => 'text',
						'label' => __( 'Matching Articles - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_matching_articles_name'],
						'description' => __( 'Display a list of articles matching the search query', 'echo-knowledge-base' )
					),
					'ai_search_results_ai_answer_name' => array(
						'type' => 'text',
						'label' => __( 'AI Answer - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_ai_answer_name'],
						'description' => __( 'Display AI-generated answer to the search query', 'echo-knowledge-base' )
					),
					'ai_search_results_glossary_name' => array(
						'type' => 'text',
						'label' => __( 'Glossary Terms - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_glossary_name'],
						'description' => __( 'Display terminology and abbreviations related to the search', 'echo-knowledge-base' )
					),
					'ai_search_results_tips_name' => array(
						'type' => 'text',
						'label' => __( 'Helpful Tips - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_tips_name'],
						'description' => __( 'Display helpful tips related to the search query', 'echo-knowledge-base' )
					),
					'ai_search_results_steps_name' => array(
						'type' => 'text',
						'label' => __( 'Step-by-Step Instructions - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_steps_name'],
						'description' => __( 'Display step-by-step instructions related to the search', 'echo-knowledge-base' )
					),
					'ai_search_results_tasks_list_name' => array(
						'type' => 'text',
						'label' => __( 'Tasks List - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_tasks_list_name'],
						'description' => __( 'Display a list of tasks related to the search query', 'echo-knowledge-base' )
					),
					'ai_search_results_you_can_also_ask_name' => array(
						'type' => 'text',
						'label' => __( 'Related Questions - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_you_can_also_ask_name'],
						'description' => __( 'Display suggested follow-up questions', 'echo-knowledge-base' )
					),
					'ai_search_results_related_keywords_name' => array(
						'type' => 'text',
						'label' => __( 'Related Keywords - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_related_keywords_name'],
						'description' => __( 'Display keywords related to the search query', 'echo-knowledge-base' )
					),
					/* Disabled for now
					'ai_search_results_custom_prompt_name' => array(
						'type' => 'text',
						'label' => __( 'Custom Section - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_custom_prompt_name'],
						'description' => __( 'Display response from a custom AI prompt', 'echo-knowledge-base' )
					),
					'ai_search_results_custom_prompt_text' => array(
						'type' => 'textarea',
						'label' => __( 'Custom Prompt - Prompt Text', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_custom_prompt_text'],
						'description' => __( 'Enter the custom prompt that AI will use to generate a response', 'echo-knowledge-base' ),
						'rows' => 5
					),
					*/
					'ai_search_results_feedback_name' => array(
						'type' => 'text',
						'label' => __( 'Feedback - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_feedback_name'],
						'description' => __( 'Display feedback options (vote up/down)', 'echo-knowledge-base' )
					),
					'ai_search_results_contact_us_name' => array(
						'type' => 'text',
						'label' => __( 'Contact Us - Section Name', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_contact_us_name'],
						'description' => __( 'Display contact options for additional help', 'echo-knowledge-base' )
					),
					'ai_search_results_contact_support_button_text' => array(
						'type' => 'text',
						'label' => __( 'Contact Us - Button Text', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_contact_support_button_text'],
						'description' => __( 'Text to display on the contact support button', 'echo-knowledge-base' ),
						'placeholder' => __( 'Contact Support', 'echo-knowledge-base' )
					),
					'ai_search_results_contact_support_email' => array(
						'type' => 'text',
						'label' => __( 'Contact Us - Destination Email', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_contact_support_email'],
						'description' => __( 'Email address where contact requests will be sent', 'echo-knowledge-base' ),
						'placeholder' => 'support@example.com'
					)
				)
			)

		);

		// Hide pro-only sections when ai-features-pro is not installed
		if ( ! $has_ai_features_pro ) {
			unset( $sections['search_results_columns'] );
			unset( $sections['search_results_column_sections'] );
			unset( $sections['search_results_sections'] );
		}

		return $sections;
	}

	/**
	 * AJAX handler to apply search preset
	 */
	public static function ajax_apply_search_preset() {
		
		// Verify nonce and permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_ai_feature' );
		
		$preset_key = EPKB_Utilities::post( 'preset', '', false );
		
		if ( empty( $preset_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid preset selected', 'echo-knowledge-base' ) ) );
			return;
		}
		
		// Handle custom preset - no changes needed
		if ( $preset_key === 'custom' ) {
			wp_send_json_success( array(
				'message' => __( 'Custom preset selected. Adjust model parameters below in Search Settings.', 'echo-knowledge-base' )
			) );
			return;
		}
		
		// Get preset parameters
		$preset = EPKB_OpenAI_Client::get_preset_parameters( $preset_key, 'search' );
		if ( ! $preset ) {
			wp_send_json_error( array( 'message' => __( 'Invalid preset configuration', 'echo-knowledge-base' ) ) );
			return;
		}
		
		// Apply preset parameters
		if ( isset( $preset['model'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_model', $preset['model'] );
		}
		if ( isset( $preset['verbosity'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_verbosity', $preset['verbosity'] );
		}
		if ( isset( $preset['reasoning'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_reasoning', $preset['reasoning'] );
		}
		if ( isset( $preset['temperature'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_temperature', $preset['temperature'] );
		}
		if ( isset( $preset['max_output_tokens'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_max_output_tokens', $preset['max_output_tokens'] );
		}
		if ( isset( $preset['top_p'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_top_p', $preset['top_p'] );
		}
		
		wp_send_json_success( array( 
			'message' => sprintf( 
				__( 'Applied "%s" preset for AI Search', 'echo-knowledge-base' ), 
				$preset['label'] 
			),
			'applied_settings' => array(
				'model' => isset( $preset['model'] ) ? $preset['model'] : null,
				'verbosity' => isset( $preset['verbosity'] ) ? $preset['verbosity'] : null,
				'reasoning' => isset( $preset['reasoning'] ) ? $preset['reasoning'] : null,
				'temperature' => isset( $preset['temperature'] ) ? $preset['temperature'] : null,
				'max_output_tokens' => isset( $preset['max_output_tokens'] ) ? $preset['max_output_tokens'] : null,
				'top_p' => isset( $preset['top_p'] ) ? $preset['top_p'] : null
			)
		) );
	}

	/**
	 * Get column label based on column number and total columns
	 *
	 * @param int $column_number
	 * @param string $num_columns
	 * @return string
	 */
	private static function get_column_label( $column_number, $num_columns ) {
		// For 1 column
		if ( $num_columns == '1' ) {
			return __( 'Single Column', 'echo-knowledge-base' );
		}

		// For 2 columns
		if ( $num_columns == '2' ) {
			return $column_number == 1 ? __( 'Left Column', 'echo-knowledge-base' ) : __( 'Right Column', 'echo-knowledge-base' );
		}

		// For 3 columns
		if ( $num_columns == '3' ) {
			if ( $column_number == 1 ) {
				return __( 'Left Column', 'echo-knowledge-base' );
			} elseif ( $column_number == 2 ) {
				return __( 'Middle Column', 'echo-knowledge-base' );
			} else {
				return __( 'Right Column', 'echo-knowledge-base' );
			}
		}

		// Fallback
		return sprintf( __( 'Column %d', 'echo-knowledge-base' ), $column_number );
	}

	/**
	 * Get column width options based on number of columns
	 *
	 * @param string $num_columns
	 * @return array
	 */
	private static function get_column_width_options( $num_columns ) {
		switch ( $num_columns ) {
			case '1':
				return array( '100' => __( 'Full Width', 'echo-knowledge-base' ) );
			case '2':
				return array(
					'25-75' => '25% / 75%',
					'30-70' => '30% / 70%',
					'35-65' => '35% / 65%',
					'50-50' => '50% / 50%',
					'65-35' => '65% / 35%',
					'70-30' => '70% / 30%',
					'75-25' => '75% / 25%'
				);
			case '3':
				return array(
					'25-50-25' => '25% / 50% / 25%',
					'30-40-30' => '30% / 40% / 30%',
					'35-30-35' => '35% / 30% / 35%'
				);
			default:
				return array();
		}
	}

	/**
	 * Get all available sections
	 *
	 * @return array
	 */
	private static function get_available_sections() {
		return array(
			'matching_articles' => __( 'Matching Articles', 'echo-knowledge-base' ),
			'ai_answer' => __( 'Answer', 'echo-knowledge-base' ),
			'glossary_terms' => __( 'Glossary Terms', 'echo-knowledge-base' ),
			'tips' => __( 'Helpful Tips', 'echo-knowledge-base' ),
			'steps' => __( 'Step-by-Step Instructions', 'echo-knowledge-base' ),
			'tasks_list' => __( 'Tasks List', 'echo-knowledge-base' ),
			'you_can_also_ask' => __( 'Related Questions', 'echo-knowledge-base' ),
			'related_keywords' => __( 'Related Keywords', 'echo-knowledge-base' ),
			// 'custom_prompt' => __( 'Custom Section', 'echo-knowledge-base' ), // Disabled for now
			'feedback' => __( 'Feedback', 'echo-knowledge-base' ),
			'contact_us' => __( 'Contact Us', 'echo-knowledge-base' )
		);
	}

	/**
	 * Get predefined search results layout presets
	 *
	 * @return array
	 */
	public static function get_search_results_presets() {
		return array(
			'simple' => array(
				'name' => __( 'Simple Single Column', 'echo-knowledge-base' ),
				'description' => __( 'Clean single column layout with essential sections', 'echo-knowledge-base' ),
				'settings' => array(
					'ai_search_results_num_columns' => '1',
					'ai_search_results_column_widths' => '100',
					'ai_search_results_column_1_sections' => array( 'ai_answer', 'matching_articles', 'tasks_list', 'steps', 'you_can_also_ask', 'feedback' ),
					'ai_search_results_column_2_sections' => array(),
					'ai_search_results_column_3_sections' => array(),
					'ai_search_results_separator' => 'shaded-box'
				)
			),
			'sidebar' => array(
				'name' => __( 'Main Content with Sidebar', 'echo-knowledge-base' ),
				'description' => __( 'Main content column with helpful sidebar on the right', 'echo-knowledge-base' ),
				'settings' => array(
					'ai_search_results_num_columns' => '2',
					'ai_search_results_column_widths' => '65-35',
					'ai_search_results_column_1_sections' => array( 'ai_answer', 'matching_articles', 'feedback' ),
					'ai_search_results_column_2_sections' => array( 'you_can_also_ask', 'related_keywords', 'tips', 'contact_us' ),
					'ai_search_results_column_3_sections' => array(),
					'ai_search_results_separator' => 'line'
				)
			),
			'complete' => array(
				'name' => __( 'Complete Three Column', 'echo-knowledge-base' ),
				'description' => __( 'Full layout with task guidance, main content, and helpful resources', 'echo-knowledge-base' ),
				'settings' => array(
					'ai_search_results_num_columns' => '3',
					'ai_search_results_column_widths' => '25-50-25',
					'ai_search_results_column_1_sections' => array( 'tasks_list', 'steps', 'tips' ),
					'ai_search_results_column_2_sections' => array( 'ai_answer', 'matching_articles' ),
					'ai_search_results_column_3_sections' => array( 'you_can_also_ask', 'related_keywords', 'glossary_terms', 'feedback', 'contact_us' ),
					'ai_search_results_separator' => 'line'
				)
			),
			'faq' => array(
				'name' => __( 'FAQ Style', 'echo-knowledge-base' ),
				'description' => __( 'Question and answer focused layout', 'echo-knowledge-base' ),
				'settings' => array(
					'ai_search_results_num_columns' => '2',
					'ai_search_results_column_widths' => '50-50',
					'ai_search_results_column_1_sections' => array( 'ai_answer', 'you_can_also_ask', 'feedback' ),
					'ai_search_results_column_2_sections' => array( 'related_keywords', 'matching_articles', 'contact_us' ),
					'ai_search_results_column_3_sections' => array(),
					'ai_search_results_separator' => 'shaded-box'
				)
			),
			'tutorial' => array(
				'name' => __( 'Tutorial Mode', 'echo-knowledge-base' ),
				'description' => __( 'Step-by-step guidance with detailed instructions', 'echo-knowledge-base' ),
				'settings' => array(
					'ai_search_results_num_columns' => '2',
					'ai_search_results_column_widths' => '30-70',
					'ai_search_results_column_1_sections' => array( 'steps', 'tasks_list', 'tips' ),
					'ai_search_results_column_2_sections' => array( 'ai_answer', 'matching_articles', 'feedback', 'contact_us' ),
					'ai_search_results_column_3_sections' => array(),
					'ai_search_results_separator' => 'line'
				)
			)
		);
	}

	/**
	 * Get Pro Feature Ad for Column Configuration
	 *
	 * @return string
	 */
	public static function get_pro_feature_ad() {
		return EPKB_HTML_Forms::pro_feature_ad_box( array(
			'id' => 'epkb-ai-search-column-config-ad',
			'class' => 'epkb-ai-search-pro-ad',
			//'layout' => 'horizontal',
			'title' => __( 'AI Advanced Search', 'echo-knowledge-base' ),
			'desc' => __( 'A first-of-its-kind, multi-panel search experience. We pioneered a results layout that runs multiple AI prompts in parallel to surface not just an answer or article list, but complementary sections like Tips, Glossary, Related Questions, and more—so users get clarity faster.', 'echo-knowledge-base' ),
			'list' => array(
				__( 'Configure 1–3 columns with adjustable widths and assign sections to each column', 'echo-knowledge-base' ),
				__( '<strong>AI Answer</strong> - generates concise, accurate responses based on your knowledge base', 'echo-knowledge-base' ),
				__( '<strong>Matching Articles</strong> - displays relevant articles from search results', 'echo-knowledge-base' ),
				__( '<strong>Related Questions</strong> - suggests follow-up questions users can explore', 'echo-knowledge-base' ),
				__( '<strong>Related Keywords</strong> - offers keywords to refine and update the search', 'echo-knowledge-base' ),
				__( '<strong>Help Tips</strong> - provides actionable guidance to troubleshoot common issues', 'echo-knowledge-base' ),
				__( '<strong>Glossary Terms</strong> - defines key terminology for better understanding', 'echo-knowledge-base' ),
				__( '<strong>Feedback</strong> - lets users rate results to improve content and AI behavior', 'echo-knowledge-base' ),
				__( '<strong>Contact Us</strong> - presents a contact form when users need additional help', 'echo-knowledge-base' )
			),
			'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
			'btn_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/ai-features/',
			'btn_text_2' => __( 'See It In Action', 'echo-knowledge-base' ),
			'btn_url_2' => 'https://contentdisplay.wpengine.com/knowledge-base/',
			'return_html' => true
		) );
	}

	/**
	 * Get AI Advanced Search Shortcode Box
	 *
	 * @return string
	 */
	private static function get_ai_advanced_search_shortcode_box() {
		$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		return EPKB_Shortcodes::get_copy_box( 'ai-advanced-search', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) );
	}

}
