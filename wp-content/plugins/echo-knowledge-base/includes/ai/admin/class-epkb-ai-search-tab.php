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
		$ai_config['ai_search_mode'] = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_mode', 'simple_search' );

		$config = array(
			'tab_id' => 'search',
			'title' => __( 'Search', 'echo-knowledge-base' ),
			'sub_tabs' => self::get_sub_tabs_config(),
			'settings_sections' => self::get_settings_sections( $ai_config ),
			'ai_config' => $ai_config,
			'collection_issues' => self::get_collection_issues(),
			'setup_steps' => EPKB_AI_Admin_Page::get_setup_steps_for_tab( 'search' )
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

		$has_ai_features_pro = EPKB_Utilities::is_ai_features_pro_enabled();
		$search_mode_options = array(
			'simple_search' => __( 'Simple Search', 'echo-knowledge-base' ),
		);

		if ( $has_ai_features_pro ) {
			$search_mode_options['smart_search'] = __( 'Smart Search', 'echo-knowledge-base' );
		}

		$preset_options = EPKB_AI_Provider::get_preset_options( 'search' );

		// Build layout preset options
		$layout_preset_options = array();
		foreach ( self::get_search_results_presets() as $key => $preset ) {
			$layout_preset_options[$key] = $preset['name'] . ' - ' . $preset['description'];
		}
		$layout_preset_options['custom'] = __( 'Custom', 'echo-knowledge-base' ) . ' - ' . __( 'Configure your own layout', 'echo-knowledge-base' );

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
						'options' => $search_mode_options,
						'description' => __( 'Choose which AI search experience to display: Ask AI shows a simple Q&A button/interface, Search Results shows an advanced multi-column results layout', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-search-mode'
					),
					'ai_search_integration_options' => array(
						'type' => 'html',
						'html' => self::get_integration_options_html(),
						'field_class' => 'epkb-ai-mode-smart_search'
					),
					'ai_search_immediate_query' => array(
						'type' => 'checkbox',
						'label' => __( 'Immediate AI Query', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_immediate_query'],
						'description' => __( 'When enabled, AI will automatically query when a search is submitted instead of showing "Ask AI?" button', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-immediate-query epkb-ai-mode-simple_search'
					),
					'ai_show_sources' => array(
						'type' => 'checkbox',
						'label' => __( 'Show Source References', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_show_sources'],
						'description' => __( 'Display links to source articles that were used to generate the AI answer', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-show-sources'
					),
					'ai_search_ask_button_text' => array(
						'type' => 'text',
						'label' => __( 'AI Search Button Text', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_ask_button_text'],
						'description' => __( 'Text displayed on the AI search button', 'echo-knowledge-base' ),
						'placeholder' => __( 'Ask AI?', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-button-text epkb-ai-mode-simple_search'
					),
					'ai_search_results_articles_count_simple' => array(
						'type' => 'number',
						'label' => __( 'Number of Matching Articles', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_articles_count'],
						'description' => __( 'Number of articles to display in Matching Articles section', 'echo-knowledge-base' ),
						'min' => 1,
						'max' => 20,
						'field_class' => 'epkb-ai-mode-simple_search'
					),
					'ai_search_results_continue_in_chat' => array(
						'type' => 'checkbox',
						'label' => __( 'Show "Continue in AI Chat" Button', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_continue_in_chat'],
						'description' => __( 'Show a button on the AI Answer that opens the AI Chat widget with the search query pre-filled, so users can ask follow-up questions. Only appears when AI Chat is enabled on the current page.', 'echo-knowledge-base' ),
						'field_class' => 'epkb-ai-mode-simple_search'
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
				)
			),

			'ai_setup' => array(
				'id' => 'ai_setup',
				'title' => __( 'AI Setup', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-magic',
				'sub_tab' => 'search-settings',
				'fields' => array(
					'kb_collection_mapping' => array(
						'type' => 'kb_collection_mapping',
						'label' => __( 'Select Training Data for Each KB Search', 'echo-knowledge-base' ),
						'kb_mappings' => self::get_kb_collection_mappings(),
						'collection_options' => EPKB_AI_Training_Data_Config_Specs::get_active_provider_collection_options(),
						'description' => __( 'Select which Training Data Collection each Knowledge Base should use for AI Search.', 'echo-knowledge-base' )
					),
						'ai_search_preset' => array(
							'type' => 'select',
							'label' => __( 'Choose AI Behavior', 'echo-knowledge-base' ),
							'value' => EPKB_AI_Provider::get_feature_preset( 'search', $ai_config ),
							'options' => $preset_options,
							'description' => __( 'Choose whether AI Search should prioritize speed, balance, or answer quality.', 'echo-knowledge-base' ),
							'field_class' => 'epkb-ai-behavior-preset-select' . ( ! $has_ai_features_pro ? ' epkb-ai-mode-simple_search' : '' )
						)
					)
				),

			'search_results_columns' => array(
				'id' => 'search_results_columns',
				'title' => __( 'Columns and Sections', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-columns',
				'sub_tab' => 'search-settings',
				'fields' => $has_ai_features_pro ? array(
					'ai_search_results_layout_preset' => array(
						'type' => 'select',
						'label' => __( 'Choose Layout Preset', 'echo-knowledge-base' ),
						'value' => 'custom',
						'options' => $layout_preset_options,
						'description' => __( 'Select a preset or customize settings below.', 'echo-knowledge-base' ),
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
					),
					'ai_search_results_articles_count' => array(
						'type' => 'number',
						'label' => __( 'Number of Matching Articles', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_search_results_articles_count'],
						'description' => __( 'Number of articles to display in Matching Articles section', 'echo-knowledge-base' ),
						'min' => 1,
						'max' => 20,
						'field_class' => 'epkb-ai-mode-advanced_search'
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
			),

			'search_results_section_prompts' => array(
				'id' => 'search_results_section_prompts',
				'title' => __( 'Section Prompts', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-edit',
				'sub_tab' => 'search-settings',
				'fields' => array(
					'section_prompt_editor' => array(
						'type' => 'section_prompt_editor',
						'label' => __( 'Section Prompt Editor', 'echo-knowledge-base' ),
						'description' => __( 'Select a section to customize its AI prompt. The default prompt is shown as placeholder.', 'echo-knowledge-base' ),
						'sections' => self::get_sections_with_prompts(),
						'prompts' => array(
							'tips' => $ai_config['ai_search_results_tips_prompt'],
							'steps' => $ai_config['ai_search_results_steps_prompt'],
							'glossary_terms' => $ai_config['ai_search_results_glossary_prompt'],
							'you_can_also_ask' => $ai_config['ai_search_results_you_can_also_ask_prompt'],
							'tasks_list' => $ai_config['ai_search_results_tasks_list_prompt'],
							'related_keywords' => $ai_config['ai_search_results_related_keywords_prompt']
						),
						'default_prompts' => self::get_default_section_prompts(),
						'field_class' => 'epkb-ai-mode-advanced_search'
					)
				)
			)

		);

		// Hide pro-only sections when ai-features-pro is not installed
		if ( ! $has_ai_features_pro ) {
			unset( $sections['search_results_columns'] );
			unset( $sections['search_results_column_sections'] );
			unset( $sections['search_results_sections'] );
			unset( $sections['search_results_section_prompts'] );
		}

		return $sections;
	}

	/**
	 * AJAX handler to apply search preset
	 */
	public static function ajax_apply_search_preset() {
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_ai_feature' );

		$preset_key = EPKB_Utilities::post( 'preset', '', false );
		if ( empty( $preset_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid preset selected', 'echo-knowledge-base' ) ) );
			return;
		}

		$result = EPKB_AI_Provider::apply_preset( $preset_key, 'search' );
		wp_send_json_success( $result );
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
			if ( $column_number == 1 ) {
				return __( 'Left Column', 'echo-knowledge-base' );
			} elseif ( $column_number == 2 ) {
				return __( 'Right Column', 'echo-knowledge-base' );
			}
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
		// translators: %d is the column number
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
	 * Get sections that have configurable prompts
	 *
	 * @return array
	 */
	private static function get_sections_with_prompts() {
		return array(
			'tips' => __( 'Helpful Tips', 'echo-knowledge-base' ),
			'steps' => __( 'Step-by-Step Instructions', 'echo-knowledge-base' ),
			'glossary_terms' => __( 'Glossary Terms', 'echo-knowledge-base' ),
			'you_can_also_ask' => __( 'Related Questions', 'echo-knowledge-base' ),
			'tasks_list' => __( 'Tasks List', 'echo-knowledge-base' ),
			'related_keywords' => __( 'Related Keywords', 'echo-knowledge-base' )
		);
	}

	/**
	 * Get default prompts for each section (retrieved from ai-features-pro via filter)
	 *
	 * @return array
	 */
	private static function get_default_section_prompts() {
		return apply_filters( 'epkb_ai_search_section_default_prompts', array(
			'tips' => '',
			'steps' => '',
			'glossary_terms' => '',
			'you_can_also_ask' => '',
			'tasks_list' => '',
			'related_keywords' => ''
		) );
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
			'title' => __( 'AI Smart Search', 'echo-knowledge-base' ),
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
			'discount_coupon' => EPKB_AI_PRO_Features_Tab::get_discount_coupon(),
			'return_html' => true
		) );
	}

	/**
	 * Identify issues with AI Search training data collections
	 *
	 * @return array
	 */
	private static function get_collection_issues() {
		$issues = array();
		$kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		if ( is_wp_error( $kb_configs ) ) {
			return $issues;
		}
		$checked_collections = array();

		foreach ( $kb_configs as $kb_config ) {
			// Skip archived KBs
			if ( isset( $kb_config['status'] ) && $kb_config['status'] === EPKB_KB_Config_Specs::ARCHIVED ) {
				continue;
			}

			$collection_id = isset( $kb_config['kb_ai_collection_id'] ) ? absint( $kb_config['kb_ai_collection_id'] ) : 0;
			if ( $collection_id === 0 || isset( $checked_collections[ $collection_id ] ) ) {
				continue;
			}

			$checked_collections[ $collection_id ] = true;
			// translators: %d is the Knowledge Base ID
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : sprintf( __( 'Knowledge Base %d', 'echo-knowledge-base' ), $kb_config['id'] );

			// Check for provider mismatch first for clearer messaging
			$provider_mismatch = EPKB_AI_Training_Data_Config_Specs::get_active_and_selected_provider_if_mismatched( $collection_id );
			if ( $provider_mismatch !== null ) {
				$issues[] = array(
					'collection_id'   => $collection_id,
					'collection_name' => '', // do not repeat self::get_collection_label( $collection_id ),
					'kb_name'         => $kb_name,
					// translators: %1$s is collection name, %2$s is collection provider, %3$s is active provider
					'message'         => sprintf(
						__( '%1$s uses %2$s but the active provider is %3$s. Switch providers or select a different collection.', 'echo-knowledge-base' ),
						self::get_collection_label( $collection_id ),
						$provider_mismatch['collection_provider'],
						$provider_mismatch['active_provider']
					)
				);
				continue;
			}

			$validation_result = EPKB_AI_Validation::validate_collection_has_vector_store( $collection_id );
			if ( is_wp_error( $validation_result ) ) {
				$issues[] = array(
					'collection_id'   => $collection_id,
					'collection_name' => self::get_collection_label( $collection_id ),
					'kb_name'         => $kb_name,
					'message'         => $validation_result->get_error_message()
				);
			}
		}

		return $issues;
	}

	/**
	 * Get a readable label for a collection
	 *
	 * @param int $collection_id
	 * @return string
	 */
	private static function get_collection_label( $collection_id ) {
		$collection = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );

		if ( ! is_wp_error( $collection ) && ! empty( $collection['ai_training_data_store_name'] ) ) {
			return $collection['ai_training_data_store_name'];
		}

		return EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id );
	}

	/**
	 * Get the info box for Search Settings sub-tab
	 *
	 * @return string
	 */
	private static function get_search_settings_info_box() {

		// use single quotes in shortcode to avoid JSON issues!
		return "<div class='epkb-notification-box-middle epkb-notification-box-middle--info'>" .
					"<div class='epkb-notification-box-middle__icon'>" .
						"<div class='epkb-notification-box-middle__icon__inner epkbfa epkbfa-info-circle'></div>" .
					"</div>" .
					"<div class='epkb-notification-box-middle__body'>" .
						"<h4 class='epkb-notification-box-middle__body__title'>" . esc_html__( 'Training Data Collection Required', 'echo-knowledge-base' ) . "</h4>" .
						"<p class='epkb-notification-box-middle__body__desc'>" . esc_html__( 'To use AI Search, you need to select a Training Data Collection in your KB Search configuration.', 'echo-knowledge-base' ) . "</p>" .
					"</div>" .
				"</div>";
	}

	/**
	 * Get the HTML for integration options when Advanced Search is selected
	 *
	 * @return string
	 */
	private static function get_integration_options_html() {

		$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;

		// Get current collection ID from the first KB config (usually main KB)
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		$collection_id = ! is_wp_error( $kb_config ) && ! empty( $kb_config['kb_ai_collection_id'] ) ? $kb_config['kb_ai_collection_id'] : 0;

		// Build shortcode with collection id if available (use single quotes for JSON compatibility)
		$shortcode = '[ai-smart-search';
		if ( $collection_id > 0 ) {
			$shortcode .= " kb_ai_collection_id='" . $collection_id . "'";
		}
		$shortcode .= ']';

		$copy_box = EPKB_HTML_Elements::get_copy_to_clipboard_box( $shortcode );

		// use single quotes in shortcode to avoid JSON issues!
		return "<div class='epkb-ai-integration-option'>" .
					"<div class='epkb-ai-integration-option__icon'>" .
						"<span class='epkbfa epkbfa-code'></span>" .
					"</div>" .
					"<div class='epkb-ai-integration-option__content'>" .
						"<div class='epkb-ai-integration-option__title'>" . esc_html__( 'Add AI Smart Search to non-KB Pages', 'echo-knowledge-base' ) . ' (' . esc_html__( 'Optional', 'echo-knowledge-base' ) . ")</div>" .
						"<div class='epkb-ai-integration-option__desc'>" . esc_html__( 'Add this shortcode to any page or post to display the AI Smart Search.', 'echo-knowledge-base' ) . "</div>" .
						$copy_box .
					"</div>" .
				"</div>";
	}

	/**
	 * Get KB to Collection mapping data for the settings UI
	 *
	 * @return array Array of KB mappings with id, name, and collection_id
	 */
	private static function get_kb_collection_mappings() {
		$mappings = array();

		$kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		if ( is_wp_error( $kb_configs ) ) {
			return $mappings;
		}

		foreach ( $kb_configs as $kb_config ) {
			// Skip archived KBs
			if ( isset( $kb_config['status'] ) && $kb_config['status'] === EPKB_KB_Config_Specs::ARCHIVED ) {
				continue;
			}

			$kb_id = isset( $kb_config['id'] ) ? absint( $kb_config['id'] ) : 0;
			if ( $kb_id === 0 ) {
				continue;
			}

			// translators: %d is the Knowledge Base ID
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : sprintf( __( 'Knowledge Base %d', 'echo-knowledge-base' ), $kb_id );
			$collection_id = isset( $kb_config['kb_ai_collection_id'] ) ? absint( $kb_config['kb_ai_collection_id'] ) : 0;

			$mappings[] = array(
				'kb_id' => $kb_id,
				'kb_name' => $kb_name,
				'collection_id' => $collection_id
			);
		}

		return $mappings;
	}
}
