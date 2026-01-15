<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI Chat tab with React implementation
 */
class EPKB_AI_Chat_Tab {

	/**
	 * Constructor - register AJAX handlers
	 */
	public function __construct() {
		add_action( 'wp_ajax_epkb_ai_apply_chat_preset', array( __CLASS__, 'ajax_apply_chat_preset' ) );
	}

	/**
	 * Get the configuration for the Chat tab
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public static function get_tab_config() {

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		
		// Get default widget configuration
		$default_widget_config = EPKB_AI_Chat_Widget_Config_Specs::get_widget_config( 1 );

		return array(
			'tab_id' => 'chat',
			'title' => __( 'Chat', 'echo-knowledge-base' ),
			'sub_tabs' => self::get_sub_tabs_config(),
			'settings_sections' => self::get_settings_sections( $ai_config ),
			'ai_config' => $ai_config,
			'widget_config' => $default_widget_config,
			'all_widgets' => EPKB_AI_Chat_Widget_Config_Specs::get_all_widget_configs()
		);
	}

	/**
	 * Get sub-tabs configuration
	 *
	 * @return array
	 */
	private static function get_sub_tabs_config() {
		return array(
			'chat-history' => array(
				'id' => 'chat-history',
				'title' => __( 'Chat History', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-comments'
			),
			'chat-settings' => array(
				'id' => 'chat-settings',
				'title' => __( 'Settings', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-cogs'
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
		
		// Get preset options for chat
		$chat_presets = EPKB_OpenAI_Client::get_model_presets();
		$preset_options = array();
		foreach ( $chat_presets as $key => $preset ) {
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
		foreach ( $chat_presets as $key => $preset ) {
			if ( $key == 'custom' ) {
				continue; // Skip custom preset
			}

			$matches = true;

			// Check model (always present in non-custom presets)
			if ( isset( $preset['model'] ) && $preset['model'] != $ai_config['ai_chat_model'] ) {
				$matches = false;
			}

			// Check verbosity if present in preset (GPT-5 models)
			if ( $matches && isset( $preset['verbosity'] ) && $preset['verbosity'] != $ai_config['ai_chat_verbosity'] ) {
				$matches = false;
			}

			// Check reasoning if present in preset (GPT-5 models)
			if ( $matches && isset( $preset['reasoning'] ) && $preset['reasoning'] != $ai_config['ai_chat_reasoning'] ) {
				$matches = false;
			}

			// Check temperature ONLY if it's defined in the preset (GPT-4 models)
			if ( $matches && isset( $preset['temperature'] ) ) {
				if ( abs( floatval( $preset['temperature'] ) - floatval( $ai_config['ai_chat_temperature'] ) ) >= 0.01 ) {
					$matches = false;
				}
			}

			// Check max_output_tokens if present in preset (compare as integers)
			if ( $matches && isset( $preset['max_output_tokens'] ) && intval( $preset['max_output_tokens'] ) != intval( $ai_config['ai_chat_max_output_tokens'] ) ) {
				$matches = false;
			}

			// Check top_p ONLY if it's defined in the preset (GPT-4 models)
			if ( $matches && isset( $preset['top_p'] ) ) {
				if ( abs( floatval( $preset['top_p'] ) - floatval( $ai_config['ai_chat_top_p'] ) ) >= 0.01 ) {
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
			$default_spec = EPKB_AI_Config_Specs::get_default_value( 'ai_chat_model' );
			if ( $ai_config['ai_chat_model'] == 'gpt-5-nano' && 
				 $ai_config['ai_chat_verbosity'] == 'low' && 
				 $ai_config['ai_chat_reasoning'] == 'low' ) {
				$current_preset = 'fastest';
			}
		}
		
		// Build Custom Model Parameters fields (shown when preset = custom)
		// Send ALL parameters to JavaScript for dynamic switching
		$chat_model = isset( $ai_config['ai_chat_model'] ) ? $ai_config['ai_chat_model'] : EPKB_OpenAI_Client::DEFAULT_MODEL;
		$model_spec = EPKB_OpenAI_Client::get_models_and_default_params( $chat_model );
		$custom_param_fields = array();
		
		// Model selection
		$custom_param_fields['ai_chat_model'] = array(
			'type' => 'select',
			'label' => __( 'Chat Model', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_chat_model'],
			'options' => EPKB_AI_Config_Specs::get_field_options( 'ai_chat_model' )
		);
		
		// Include BOTH GPT-5 and GPT-4 parameters for dynamic JavaScript switching
		// GPT-5 parameters
		$custom_param_fields['ai_chat_verbosity'] = array(
			'type' => 'select',
			'label' => __( 'Verbosity', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_chat_verbosity'],
			'options' => array(
				'low' => __( 'Low', 'echo-knowledge-base' ),
				'medium' => __( 'Medium', 'echo-knowledge-base' ),
				'high' => __( 'High', 'echo-knowledge-base' ),
			),
			'description' => __( 'Controls response verbosity for GPT-5 models', 'echo-knowledge-base' ),
		);
		$custom_param_fields['ai_chat_reasoning'] = array(
			'type' => 'select',
			'label' => __( 'Reasoning', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_chat_reasoning'],
			'options' => array(
				'low' => __( 'Low', 'echo-knowledge-base' ),
				'medium' => __( 'Medium', 'echo-knowledge-base' ),
				'high' => __( 'High', 'echo-knowledge-base' ),
			),
			'description' => __( 'Controls reasoning depth for GPT-5 models', 'echo-knowledge-base' ),
		);
		
		// GPT-4 parameters  
		$custom_param_fields['ai_chat_temperature'] = array(
			'type' => 'number',
			'label' => __( 'Temperature', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_chat_temperature'],
			'min' => 0,
			'max' => 2,
			'step' => 0.1,
			'description' => __( 'Controls response creativity. Lower = more focused, Higher = more creative', 'echo-knowledge-base' )
		);
		$custom_param_fields['ai_chat_top_p'] = array(
			'type' => 'number',
			'label' => __( 'Top P', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_chat_top_p'],
			'min' => 0,
			'max' => 1,
			'step' => 0.1,
			'description' => __( 'Controls response diversity via nucleus sampling', 'echo-knowledge-base' )
		);
		
		// Max tokens for all models
		$max_limit = isset( $model_spec['max_output_tokens_limit'] ) ? $model_spec['max_output_tokens_limit'] : EPKB_OpenAI_Client::DEFAULT_MAX_OUTPUT_TOKENS;
		$custom_param_fields['ai_chat_max_output_tokens'] = array(
			'type' => 'number',
			'label' => __( 'Max Tokens', 'echo-knowledge-base' ),
			'value' => $ai_config['ai_chat_max_output_tokens'],
			'min' => 50,
			'max' => $max_limit,
			'description' => __( 'Maximum response length in tokens', 'echo-knowledge-base' )
		);

		return array(
			'chat_settings' => array(
				'id' => 'chat_settings',
				'title' => __( 'AI Chat Settings', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-comments',
				'sub_tab' => 'chat-settings',
				'fields' => array(
					'ai_chat_enabled' => array(
						'type' => 'radio',
						'label' => __( 'AI Chat Mode', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_chat_enabled'],
						'options' => array(
							'off'     => __( 'Off', 'echo-knowledge-base' ),
							'preview' => __( 'Preview (Admins only)', 'echo-knowledge-base' ),
							'on'      => __( 'On (Public)', 'echo-knowledge-base' )
						),
						'description' => __( 'Control AI Chat visibility: Off (disabled), Preview (admins only for testing), or On (public access)', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-chat-mode'
					),
					'ai_chat_instructions' => array(
						'type' => 'textarea',
						'label' => __( 'AI Chat Instructions', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_chat_instructions'],
						'description' => __( 'Warning: Modifying these instructions is challenging and can significantly impact AI performance. The AI is highly sensitive to instruction changes - even small modifications can cause unexpected behavior.', 'echo-knowledge-base' ),
						'rows' => 8,
						'default' => EPKB_AI_Config_Specs::get_default_value( 'ai_chat_instructions' ),
						'show_reset' => true
					)
				)
			),
			'display_settings' => self::get_display_settings_section( $ai_config ),
			'chat_behavior' => array(
				'id' => 'chat_behavior',
				'title' => __( 'AI Behavior', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-sliders',
				'sub_tab' => 'chat-settings',
				'fields' => array_merge(array(
					'ai_chat_preset' => array(
						'type' => 'select',
						'label' => __( 'Choose AI Behavior', 'echo-knowledge-base' ),
						'value' => $current_preset,
						'options' => $preset_options,
						'description' => $current_preset === 'custom' ?
							__( 'Custom model parameters are active. Adjust them below.', 'echo-knowledge-base' ) :
							__( 'Select an AI behavior preset that best fits your needs. To customize parameters, choose "Custom".', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-behavior-preset-select'
					)
				), $custom_param_fields)
			),
			'default_chat_widget' => self::get_widget_settings_section()
		);
	}

	/**
	 * Get widget settings section configuration
	 *
	 * @return array
	 */
	private static function get_widget_settings_section() {
		
		// Get the default widget configuration (always returns valid config for widget 1)
		$widget_config = EPKB_AI_Chat_Widget_Config_Specs::get_widget_config( 1 );
		
		return array(
			'id' => 'default_chat_widget',
			'title' => __( 'Chat Widget Appearance', 'echo-knowledge-base' ),
			'icon' => 'epkbfa epkbfa-paint-brush',
			'sub_tab' => 'chat-settings',
			'fields' => array(
				/* 'widget_enabled' => array(
					'type' => 'toggle',
					'label' => __( 'Enable This Widget', 'echo-knowledge-base' ),
					'value' => isset( $widget_config['widget_enabled'] ) ? $widget_config['widget_enabled'] : 'on',
					'description' => __( 'Enable or disable this chat widget', 'echo-knowledge-base' )
				), 
				'widget_name' => array(
					'type' => 'text',
					'label' => __( 'Widget Name', 'echo-knowledge-base' ),
					'value' => $widget_config['widget_name'],
					'description' => __( 'Internal name for this chat widget configuration', 'echo-knowledge-base' )
				), */
				
				// Text Customization
				'text_section' => array(
					'type' => 'section_header',
					'label' => __( 'Text Customization', 'echo-knowledge-base' ),
					'description' => __( 'Customize widget text and messages', 'echo-knowledge-base' )
				),
				'widget_header_title' => array(
					'type' => 'text',
					'label' => __( 'Widget Header Title', 'echo-knowledge-base' ),
					'value' => $widget_config['widget_header_title'],
					'description' => __( 'Title displayed in the chat widget header', 'echo-knowledge-base' )
				),
				'input_placeholder_text' => array(
					'type' => 'text',
					'label' => __( 'Input Placeholder', 'echo-knowledge-base' ),
					'value' => $widget_config['input_placeholder_text'],
					'description' => __( 'Placeholder text in the message input field', 'echo-knowledge-base' )
				),
				'welcome_message' => array(
					'type' => 'textarea',
					'label' => __( 'Welcome Message', 'echo-knowledge-base' ),
					'value' => $widget_config['welcome_message'],
					'description' => __( 'First message shown when chat opens', 'echo-knowledge-base' ),
					'rows' => 3
				),

				// Colors
				'launcher_background_color' => array(
					'type' => 'color',
					'label' => __( 'Launcher Color', 'echo-knowledge-base' ),
					'value' => $widget_config['launcher_background_color'],
					'description' => __( 'Background color of the floating chat button', 'echo-knowledge-base' )
				),
				'widget_header_background_color' => array(
					'type' => 'color',
					'label' => __( 'Widget Header Color', 'echo-knowledge-base' ),
					'value' => $widget_config['widget_header_background_color'],
					'description' => __( 'Background color of the chat widget header', 'echo-knowledge-base' )
				),
				
				// Error Messages
				/* 'errors_section' => array(
					'type' => 'section_header',
					'label' => __( 'Error Messages', 'echo-knowledge-base' ),
					'description' => __( 'Customize error messages shown to users', 'echo-knowledge-base' )
				),
				'error_generic_message' => array(
					'type' => 'text',
					'label' => __( 'Generic Error', 'echo-knowledge-base' ),
					'value' => $widget_config['error_generic_message']
				),
				'error_network_message' => array(
					'type' => 'text',
					'label' => __( 'Network Error', 'echo-knowledge-base' ),
					'value' => $widget_config['error_network_message']
				),
				'error_timeout_message' => array(
					'type' => 'text',
					'label' => __( 'Timeout Error', 'echo-knowledge-base' ),
					'value' => $widget_config['error_timeout_message']
				),
				'error_rate_limit_message' => array(
					'type' => 'text',
					'label' => __( 'Rate Limit Error', 'echo-knowledge-base' ),
					'value' => $widget_config['error_rate_limit_message']
				), */
				
				// Reset Button
				'reset_widget_settings' => array(
					'type' => 'action_button',
					'label' => __( 'Reset Widget Settings', 'echo-knowledge-base' ),
					'button_text' => __( 'Reset to Defaults', 'echo-knowledge-base' ),
					'button_class' => 'epkb-ai-reset-widget-settings',
					'confirm_message' => __( 'Are you sure you want to reset all widget settings to their default values?', 'echo-knowledge-base' ),
					'description' => __( 'Reset all chat widget appearance and text settings to default values', 'echo-knowledge-base' )
				)
			)
		);
	}

	/**
	 * Get display settings section configuration
	 *
	 * @param array $ai_config
	 * @return array
	 */
	private static function get_display_settings_section( $ai_config ) {

		// Get all training data collections for the dropdown
		$training_collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections();
		$collection_options = array();
		foreach ( $training_collections as $collection_id => $collection_config ) {
			$collection_name = isset( $collection_config['ai_training_data_store_name'] ) ? $collection_config['ai_training_data_store_name'] : sprintf( __( 'Collection %d', 'echo-knowledge-base' ), $collection_id );
			$collection_options[ $collection_id ] = $collection_name;
		}

		// Get Knowledge Base post types
		$kb_post_types = self::get_kb_post_types_for_display();

		// Build location tabs
		$location_tabs = array();
		for ( $i = 1; $i <= 5; $i++ ) {
			$suffix = $i === 1 ? '' : "_{$i}";
			$tab_id = "location-{$i}";
			$tab_label = sprintf( __( 'Location %d', 'echo-knowledge-base' ), $i );

			// Build fields for this location tab
			$tab_fields = array(
				// Collection selection
				"ai_chat_display_collection{$suffix}" => array(
					'type' => 'select',
					'label' => __( 'Training Data Collection', 'echo-knowledge-base' ),
					'value' => isset( $ai_config["ai_chat_display_collection{$suffix}"] ) ? $ai_config["ai_chat_display_collection{$suffix}"] : EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID,
					'options' => $collection_options,
					'description' => __( 'Select which Training Data Collection to use for pages matching the rules below.', 'echo-knowledge-base' ),
					'field_class' => 'epkb-ai-chat-collection-select'
				),

				// Page Rules
				"ai_chat_display_page_rules{$suffix}" => array(
					'type' => 'checkboxes',
					'label' => __( 'Page Types', 'echo-knowledge-base' ),
					'value' => isset( $ai_config["ai_chat_display_page_rules{$suffix}"] ) ? $ai_config["ai_chat_display_page_rules{$suffix}"] : array(),
					'options' => array(
						'posts'       => __( 'All Posts', 'echo-knowledge-base' ),
						'pages'       => __( 'All Pages', 'echo-knowledge-base' )
					),
					'field_class' => 'epkb-ai-chat-page-rules epkb-two-column-checkboxes'
				)
			);

			// Add Knowledge Bases if any exist
			if ( ! empty( $kb_post_types ) ) {
				$tab_fields["ai_chat_display_other_post_types{$suffix}"] = array(
					'type' => 'checkboxes',
					'label' => __( 'Knowledge Bases', 'echo-knowledge-base' ),
					'value' => isset( $ai_config["ai_chat_display_other_post_types{$suffix}"] ) ? $ai_config["ai_chat_display_other_post_types{$suffix}"] : array(),
					'options' => $kb_post_types,
					'field_class' => 'epkb-ai-chat-other-post-types epkb-two-column-checkboxes'
				);
			}

			// Add URL Patterns
			$tab_fields["ai_chat_display_url_patterns{$suffix}"] = array(
				'type' => 'textarea',
				'label' => __( 'URL Patterns', 'echo-knowledge-base' ),
				'value' => isset( $ai_config["ai_chat_display_url_patterns{$suffix}"] ) ? str_replace( ',', "\n", $ai_config["ai_chat_display_url_patterns{$suffix}"] ) : '',
				'placeholder' => '/' . __( 'sample-page', 'echo-knowledge-base' ) . "/\n/" . __( 'docs', 'echo-knowledge-base' ) . "/*\n/" . __( 'help', 'echo-knowledge-base' ) . '/*',
				'description' => __( 'Enter one URL pattern per line. Use * as wildcard', 'echo-knowledge-base' ),
				'rows' => 3,
				'field_class' => 'epkb-ai-chat-url-patterns'
			);

			$location_tabs[$tab_id] = array(
				'id' => $tab_id,
				'title' => $tab_label,
				'icon' => 'epkbfa epkbfa-map-marker',
				'fields' => $tab_fields
			);
		}

		// Build global fields (shown above tabs)
		$global_fields = array(
			'ai_chat_display_mode' => array(
				'type' => 'radio',
				'label' => __( 'Display Mode', 'echo-knowledge-base' ),
				'value' => $ai_config['ai_chat_display_mode'],
				'options' => array(
					'all_pages'      => __( 'Show Everywhere', 'echo-knowledge-base' ),
					'selected_only'  => __( 'Only Show On', 'echo-knowledge-base' ),
					'all_except'     => __( "Don't Show On", 'echo-knowledge-base' )
				),
				'description' => __( 'Choose one mode to control where the AI chat widget appears on your site. Then configure which Training Data Collection to use for each location below.', 'echo-knowledge-base' ),
				'field_class' => 'epkb-ai-chat-display-mode epkb-horizontal-radio'
			)
		);

		$global_fields['ai_chat_display_collection'] = array(
			'type' => 'select',
			'label' => __( 'Training Data Collection', 'echo-knowledge-base' ),
			'value' => isset( $ai_config['ai_chat_display_collection'] ) ? $ai_config['ai_chat_display_collection'] : EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID,
			'options' => $collection_options,
			'description' => __( 'Select which Training Data Collection to use for the AI chat widget.', 'echo-knowledge-base' ),
			'field_class' => 'epkb-ai-chat-collection-select',
			'hidden' => $ai_config['ai_chat_display_mode'] !== 'all_pages'
		);

		$global_fields['collection_tabs_description'] = array(
			'type' => 'html',
			'html' => '<div class="epkb-collection-tabs-description">' .
				'<p>' . esc_html__( 'Configure up to 5 different Training Data Collections for different pages. Location 1 has the highest priority and is checked first. If no match is found, Location 2 is checked, and so on.', 'echo-knowledge-base' ) . '</p>' .
				'</div>'
		);

		return array(
			'id' => 'display_settings',
			'title' => __( 'Display Settings', 'echo-knowledge-base' ),
			'icon' => 'epkbfa epkbfa-eye',
			'sub_tab' => 'chat-settings',
			'fields' => $global_fields,
			'location_tabs' => $location_tabs
		);
	}

	/**
	 * Get Knowledge Base post types for display rules
	 *
	 * @return array
	 */
	private static function get_kb_post_types_for_display() {
		$kb_post_types = array();
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();

		foreach ( $all_kb_configs as $kb_config ) {
			// Skip archived KBs
			if ( isset( $kb_config['status'] ) && $kb_config['status'] === EPKB_KB_Config_Specs::ARCHIVED ) {
				continue;
			}

			$kb_id = $kb_config['id'];
			$kb_post_type = EPKB_KB_Handler::get_post_type( $kb_id );
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : sprintf( __( 'Knowledge Base %d', 'echo-knowledge-base' ), $kb_id );
			$kb_post_types[ $kb_post_type ] = $kb_name;
		}

		return $kb_post_types;
	}

	/**
	 * AJAX handler to apply chat preset
	 */
	public static function ajax_apply_chat_preset() {
		
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
				'message' => __( 'Custom preset selected. Adjust model parameters below in Chat Settings.', 'echo-knowledge-base' )
			) );
			return;
		}
		
		// Get preset parameters
		$preset = EPKB_OpenAI_Client::get_preset_parameters( $preset_key, 'chat' );
		if ( ! $preset ) {
			wp_send_json_error( array( 'message' => __( 'Invalid preset configuration', 'echo-knowledge-base' ) ) );
			return;
		}
		
		// Apply preset parameters
		if ( isset( $preset['model'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_model', $preset['model'] );
		}
		if ( isset( $preset['verbosity'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_verbosity', $preset['verbosity'] );
		}
		if ( isset( $preset['reasoning'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_reasoning', $preset['reasoning'] );
		}
		if ( isset( $preset['temperature'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_temperature', $preset['temperature'] );
		}
		if ( isset( $preset['max_output_tokens'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_max_output_tokens', $preset['max_output_tokens'] );
		}
		if ( isset( $preset['top_p'] ) ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_top_p', $preset['top_p'] );
		}
		
		wp_send_json_success( array( 
			'message' => sprintf( 
				__( 'Applied "%s" preset for AI Chat', 'echo-knowledge-base' ), 
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
}
