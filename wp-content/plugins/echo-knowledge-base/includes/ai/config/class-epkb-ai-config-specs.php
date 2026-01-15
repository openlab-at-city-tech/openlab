<?php defined( 'ABSPATH' ) || exit();

/**
 * AI Configuration Specifications
 * 
 * Defines all AI-related configuration settings with their specifications,
 * validation rules, and default values. This separates AI settings from 
 * the main KB configuration for better organization and performance.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_AI_Config_Specs extends EPKB_AI_Config_Base {

	const OPTION_NAME = 'epkb_ai_configuration';

	/**
	 * Get AI refusal message (translatable)
	 *
	 * @return string
	 */
	public static function get_ai_refusal_message() {
		return __( 'That is not something I can help with', 'echo-knowledge-base' );
	}

	/**
	 * Get AI refusal prompt (translatable)
	 *
	 * @return string
	 */
	public static function get_ai_refusal_prompt() {
		return __( 'That is not something I can help with. Please try a different question.', 'echo-knowledge-base' );
	}

	/**
	 * Get all AI configuration specifications
	 *
	 * @return array
	 */
	public static function get_config_fields_specifications() {

		// Get available models from OpenAI client
		$models_data = EPKB_OpenAI_Client::get_models_and_default_params();
		$ai_models = array();
		foreach ( $models_data as $model_key => $model_info ) {
			$ai_models[$model_key] = $model_info['name'];
		}
		
		// Get default model specs for default values
		$default_model_spec = EPKB_OpenAI_Client::get_models_and_default_params( EPKB_OpenAI_Client::DEFAULT_MODEL );
		$default_params = $default_model_spec['default_params'];

		$default_instructions = __( 'You may ONLY answer using information from the vector store. Do not mention references, documents, files, or sources. ' .
				'Do not reveal retrieval, guess, speculate, or use outside knowledge. If no relevant information is found, reply exactly:' . ' ' . self::get_ai_refusal_prompt() . ' ' .
				'If relevant information is found, you may give structured explanations, including comparisons, pros and cons, or decision factors, ' .
				'but only if they are in the data. Answer only what the data supports; when unsure, leave it out.', 'echo-knowledge-base' );

		$ai_specs = array(

			/***  AI General Settings ***/
			'ai_disclaimer_accepted' => array(
				'name'      => 'ai_disclaimer_accepted',
				'type'      => EPKB_Input_Filter::CHECKBOX,
				'default'   => 'off'
			),
			'ai_key' => array(
				'name'        => 'ai_key',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'         => 20,
				'max'         => 2500
			),
			'ai_organization_id' => array(
				'name'        => 'ai_organization_id',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'		  => 3,
				'max'  => 256
			),

			/***  AI Chat Settings ***/
			'ai_chat_enabled' => array(
				'name'        => 'ai_chat_enabled',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'off'     => 'Off', // do not translate - avoid early loading errors
					'preview' => __( 'Preview (Admins only)', 'echo-knowledge-base' ),
					'on'      => __( 'On (Public)', 'echo-knowledge-base' )
				),
				'default'     => 'off'
			),
			'ai_chat_widgets' => array(
				'name'        => 'ai_chat_widgets',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'default'     => array( 1 ),
			),
			'ai_chat_model' => array(
				'name'         => 'ai_chat_model',
				'type'         => EPKB_Input_Filter::SELECTION,
				'options'      => $ai_models,
				'default'      => EPKB_OpenAI_Client::DEFAULT_MODEL
			),
			'ai_chat_instructions' => array(
				'name'        => 'ai_chat_instructions',
				'type'        => EPKB_Input_Filter::WP_EDITOR,
				'default'     => $default_instructions,
				'min'         => 0,
				'max'         => 10000
			),
			// Chat-specific tuning parameters
			'ai_chat_temperature' => array(
				'name'        => 'ai_chat_temperature',
				'type'        => EPKB_Input_Filter::FLOAT_NUMBER,
				'default'     => isset( $default_params['temperature'] ) ? $default_params['temperature'] : 0.2,
				'min'         => 0.0,
				'max'         => 2.0
			),
			'ai_chat_top_p' => array(
				'name'        => 'ai_chat_top_p',
				'type'        => EPKB_Input_Filter::FLOAT_NUMBER,
				'default'     => isset( $default_params['top_p'] ) ? $default_params['top_p'] : 1.0,
				'min'         => 0.0,
				'max'         => 1.0
			),
			'ai_chat_max_output_tokens' => array(
				'name'        => 'ai_chat_max_output_tokens',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => isset( $default_params['max_output_tokens'] ) ? $default_params['max_output_tokens'] : EPKB_OpenAI_Client::DEFAULT_MAX_OUTPUT_TOKENS,
				'min'         => 500,
				'max'         => 16384
			),
			'ai_chat_verbosity' => array(
				'name'        => 'ai_chat_verbosity',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'low'    => __( 'Low', 'echo-knowledge-base' ),
					'medium' => __( 'Medium', 'echo-knowledge-base' ),
					'high'   => __( 'High', 'echo-knowledge-base' ),
				),
				'default'     => 'low'
			),
			'ai_chat_reasoning' => array(
				'name'        => 'ai_chat_reasoning',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'low'    => __( 'Low', 'echo-knowledge-base' ),
					'medium' => __( 'Medium', 'echo-knowledge-base' ),
					'high'   => __( 'High', 'echo-knowledge-base' ),
				),
				'default'     => 'low'
			),

			/***  AI Chat Display Settings ***/
			'ai_chat_display_mode' => array(
				'name'        => 'ai_chat_display_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'all_pages'      => __( 'Show Everywhere', 'echo-knowledge-base' ),
					'selected_only'  => __( 'Only Show On', 'echo-knowledge-base' ),
					'all_except'     => __( "Don't Show On", 'echo-knowledge-base' )
				),
				'default'     => 'all_pages'
			),
			'ai_chat_display_page_rules' => array(
				'name'        => 'ai_chat_display_page_rules',
				'type'        => EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT,
				'options'     => array(
					'posts'       => __( 'Posts', 'echo-knowledge-base' ),
					'pages'       => __( 'Pages', 'echo-knowledge-base' )
				),
				'default'     => array()
			),
			'ai_chat_display_other_post_types' => array(
				'name'        => 'ai_chat_display_other_post_types',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'default'     => array()
			),
			'ai_chat_display_url_patterns' => array(
				'name'        => 'ai_chat_display_url_patterns',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'         => 0,
				'max'         => 1000
			),

		// AI Chat - Collection 1 Display Rules
		'ai_chat_display_collection' => array(
			'name'        => 'ai_chat_display_collection',
			'type'        => EPKB_Input_Filter::NUMBER,
			'default'     => 1,
			'min'         => 1,
			'max'         => 999
		),
		
		// AI Chat - Collection 2 Display Rules
		'ai_chat_display_collection_2' => array(
			'name'        => 'ai_chat_display_collection_2',
			'type'        => EPKB_Input_Filter::NUMBER,
			'default'     => 1,
			'min'         => 1,
			'max'         => 999
		),
		'ai_chat_display_page_rules_2' => array(
			'name'        => 'ai_chat_display_page_rules_2',
			'type'        => EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT,
			'options'     => array(
				'posts'       => __( 'Posts', 'echo-knowledge-base' ),
				'pages'       => __( 'Pages', 'echo-knowledge-base' )
			),
			'default'     => array()
		),
		'ai_chat_display_other_post_types_2' => array(
			'name'        => 'ai_chat_display_other_post_types_2',
			'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
			'default'     => array()
		),
		'ai_chat_display_url_patterns_2' => array(
			'name'        => 'ai_chat_display_url_patterns_2',
			'type'        => EPKB_Input_Filter::TEXT,
			'default'     => '',
			'min'         => 0,
			'max'         => 1000
		),

		// AI Chat - Collection 3 Display Rules
		'ai_chat_display_collection_3' => array(
			'name'        => 'ai_chat_display_collection_3',
			'type'        => EPKB_Input_Filter::NUMBER,
			'default'     => 1,
			'min'         => 1,
			'max'         => 999
		),
		'ai_chat_display_page_rules_3' => array(
			'name'        => 'ai_chat_display_page_rules_3',
			'type'        => EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT,
			'options'     => array(
				'posts'       => __( 'Posts', 'echo-knowledge-base' ),
				'pages'       => __( 'Pages', 'echo-knowledge-base' )
			),
			'default'     => array()
		),
		'ai_chat_display_other_post_types_3' => array(
			'name'        => 'ai_chat_display_other_post_types_3',
			'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
			'default'     => array()
		),
		'ai_chat_display_url_patterns_3' => array(
			'name'        => 'ai_chat_display_url_patterns_3',
			'type'        => EPKB_Input_Filter::TEXT,
			'default'     => '',
			'min'         => 0,
			'max'         => 1000
		),

		// AI Chat - Collection 4 Display Rules
		'ai_chat_display_collection_4' => array(
			'name'        => 'ai_chat_display_collection_4',
			'type'        => EPKB_Input_Filter::NUMBER,
			'default'     => 1,
			'min'         => 1,
			'max'         => 999
		),
		'ai_chat_display_page_rules_4' => array(
			'name'        => 'ai_chat_display_page_rules_4',
			'type'        => EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT,
			'options'     => array(
				'posts'       => __( 'Posts', 'echo-knowledge-base' ),
				'pages'       => __( 'Pages', 'echo-knowledge-base' )
			),
			'default'     => array()
		),
		'ai_chat_display_other_post_types_4' => array(
			'name'        => 'ai_chat_display_other_post_types_4',
			'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
			'default'     => array()
		),
		'ai_chat_display_url_patterns_4' => array(
			'name'        => 'ai_chat_display_url_patterns_4',
			'type'        => EPKB_Input_Filter::TEXT,
			'default'     => '',
			'min'         => 0,
			'max'         => 1000
		),

		// AI Chat - Collection 5 Display Rules
		'ai_chat_display_collection_5' => array(
			'name'        => 'ai_chat_display_collection_5',
			'type'        => EPKB_Input_Filter::NUMBER,
			'default'     => 1,
			'min'         => 1,
			'max'         => 999
		),
		'ai_chat_display_page_rules_5' => array(
			'name'        => 'ai_chat_display_page_rules_5',
			'type'        => EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT,
			'options'     => array(
				'posts'       => __( 'Posts', 'echo-knowledge-base' ),
				'pages'       => __( 'Pages', 'echo-knowledge-base' )
			),
			'default'     => array()
		),
		'ai_chat_display_other_post_types_5' => array(
			'name'        => 'ai_chat_display_other_post_types_5',
			'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
			'default'     => array()
		),
		'ai_chat_display_url_patterns_5' => array(
			'name'        => 'ai_chat_display_url_patterns_5',
			'type'        => EPKB_Input_Filter::TEXT,
			'default'     => '',
			'min'         => 0,
			'max'         => 1000
		),

			/***  AI Sync Custom Settings ***/
			'ai_auto_sync_enabled' => array(
				'name'        => 'ai_auto_sync_enabled',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			/***  AI Search Settings ***/
			'ai_search_enabled' => array(
				'name'        => 'ai_search_enabled',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'off'     => 'Off', // do not translate - avoid early loading errors
					'preview' => __( 'Preview (Admins only)', 'echo-knowledge-base' ),
					'on'      => __( 'On (Public)', 'echo-knowledge-base' )
				),
				'default'     => 'off'
			),
			'ai_search_mode' => array(
				'name'        => 'ai_search_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'simple_search'   => __( 'Simple Search Results', 'echo-knowledge-base' ),
					'advanced_search' => __( 'Advanced Search Results', 'echo-knowledge-base' )
				),
				'default'     => 'simple_search'
			),

			/**   AI Search Model */
			'ai_search_model' => array(
				'name'        => 'ai_search_model',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => $ai_models,
				'default'     => EPKB_OpenAI_Client::DEFAULT_MODEL
			),
			'ai_search_temperature' => array(
				'name'        => 'ai_search_temperature',
				'type'        => EPKB_Input_Filter::FLOAT_NUMBER,
				'default'     => isset( $default_params['temperature'] ) ? $default_params['temperature'] : 0.2,
				'min'         => 0.0,
				'max'         => 2.0
			),
			'ai_search_top_p' => array(
				'name'        => 'ai_search_top_p',
				'type'        => EPKB_Input_Filter::FLOAT_NUMBER,
				'default'     => isset( $default_params['top_p'] ) ? $default_params['top_p'] : 1.0,
				'min'         => 0.0,
				'max'         => 1.0
			),
			'ai_search_max_output_tokens' => array(
				'name'        => 'ai_search_max_output_tokens',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => isset( $default_params['max_output_tokens'] ) ? $default_params['max_output_tokens'] : EPKB_OpenAI_Client::DEFAULT_MAX_OUTPUT_TOKENS,
				'min'         => 500,
				'max'         => 16384
			),
			'ai_search_verbosity' => array(
				'name'        => 'ai_search_verbosity',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'low'    => __( 'Low', 'echo-knowledge-base' ),
					'medium' => __( 'Medium', 'echo-knowledge-base' ),
					'high'   => __( 'High', 'echo-knowledge-base' ),
				),
				'default'     => 'low'
			),
			'ai_search_reasoning' => array(
				'name'        => 'ai_search_reasoning',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'low'    => __( 'Low', 'echo-knowledge-base' ),
					'medium' => __( 'Medium', 'echo-knowledge-base' ),
					'high'   => __( 'High', 'echo-knowledge-base' ),
				),
				'default'     => 'low'
			),

			/**   AI Search - Ask AI */
			'ai_search_instructions' => array(
				'name'        => 'ai_search_instructions',
				'type'        => EPKB_Input_Filter::WP_EDITOR,
				'default'     => $default_instructions,
				'min'         => 0,
				'max'         => 10000
			),
			'ai_search_immediate_query' => array(
				'name'        => 'ai_search_immediate_query',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'ai_search_ask_button_text' => array(
				'name'        => 'ai_search_ask_button_text',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Ask AI?', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 50
			),

			/***  AI Search Results ***/
			'ai_search_results_width' => array(
				'name'        => 'ai_search_results_width',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '60%',
				'min'         => 1,
				'max'         => 20
			),
			'ai_search_results_separator' => array(
				'name'        => 'ai_search_results_separator',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'   => __( 'None', 'echo-knowledge-base' ),
					'shaded-box' => __( 'Shaded Box', 'echo-knowledge-base' ),
					'line'   => __( 'Line Separator', 'echo-knowledge-base' )
				),
				'default'     => 'line'
			),
			'ai_search_results_num_columns' => array(
				'name'        => 'ai_search_results_num_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1' => __( '1 Column', 'echo-knowledge-base' ),
					'2' => __( '2 Columns', 'echo-knowledge-base' ),
					'3' => __( '3 Columns', 'echo-knowledge-base' )
				),
				'default'     => '2'
			),
			'ai_search_results_column_widths' => array(
				'name'        => 'ai_search_results_column_widths',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '65-35',
				'min'         => 1,
				'max'         => 20
			),
			'ai_search_results_column_1_sections' => array(
				'name'        => 'ai_search_results_column_1_sections',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'default'     => array( 'ai_answer', 'matching_articles', 'feedback' )
			),
			'ai_search_results_column_2_sections' => array(
				'name'        => 'ai_search_results_column_2_sections',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'default'     => array( 'you_can_also_ask', 'related_keywords', 'tips', 'contact_us' )
			),
			'ai_search_results_column_3_sections' => array(
				'name'        => 'ai_search_results_column_3_sections',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'default'     => array()
			),

			/***  AI Search Results - Section Names ***/
			'ai_search_results_matching_articles_name' => array(
				'name'        => 'ai_search_results_matching_articles_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Matching Articles', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_ai_answer_name' => array(
				'name'        => 'ai_search_results_ai_answer_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Answer', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_glossary_name' => array(
				'name'        => 'ai_search_results_glossary_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Glossary Terms', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_tips_name' => array(
				'name'        => 'ai_search_results_tips_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Helpful Tips', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_steps_name' => array(
				'name'        => 'ai_search_results_steps_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Step-by-Step Instructions', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_tasks_list_name' => array(
				'name'        => 'ai_search_results_tasks_list_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Tasks List', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_you_can_also_ask_name' => array(
				'name'        => 'ai_search_results_you_can_also_ask_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Related Questions', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_related_keywords_name' => array(
				'name'        => 'ai_search_results_related_keywords_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Related Keywords', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_custom_prompt_name' => array(
				'name'        => 'ai_search_results_custom_prompt_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Custom Section', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_feedback_name' => array(
				'name'        => 'ai_search_results_feedback_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Feedback', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_contact_us_name' => array(
				'name'        => 'ai_search_results_contact_us_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact Us', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_custom_prompt_text' => array(
				'name'        => 'ai_search_results_custom_prompt_text',
				'type'        => EPKB_Input_Filter::WP_EDITOR,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),

			/***  AI Search Results - Contact Support Settings ***/
			'ai_search_results_contact_support_button_text' => array(
				'name'        => 'ai_search_results_contact_support_button_text',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact Support', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_search_results_contact_support_email' => array(
				'name'        => 'ai_search_results_contact_support_email',
				'type'        => EPKB_Input_Filter::EMAIL,
				'default'     => '',
				'min'         => 0,
				'max'         => 100
			),

			/***  AI Email Notification Settings ***/
			'ai_email_notifications_enabled' => array(
			'name'        => 'ai_email_notifications_enabled',
			'type'        => EPKB_Input_Filter::CHECKBOX,
			'default'     => 'off'
		),
			'ai_email_notifications_send_time' => array(
			'name'        => 'ai_email_notifications_send_time',
			'type'        => EPKB_Input_Filter::TEXT,
			'default'     => '09:00',
			'min'         => 5,
			'max'         => 5
		),
			'ai_email_notifications_recipient' => array(
			'name'        => 'ai_email_notifications_recipient',
			'type'        => EPKB_Input_Filter::EMAIL,
			'default'     => '',
			'min'         => 0,
			'max'         => 100
		),
			'ai_email_notification_subject' => array(
			'name'        => 'ai_email_notification_subject',
			'type'        => EPKB_Input_Filter::TEXT,
			'default'     => __( 'Daily AI Activity Summary - {site_name}', 'echo-knowledge-base' ),
			'min'         => 5,
			'max'         => 200
		),

			/***  AI Debug Settings ***/
			'ai_tools_debug_enabled' => array(
			'name'        => 'ai_tools_debug_enabled',
			'type'        => EPKB_Input_Filter::CHECKBOX,
			'default'     => 'off'
		)
		);

		return $ai_specs;
	}

	/**
	 * Get a specific AI configuration value
	 * Wrapper method for backward compatibility
	 *
	 * @param string $field_name Configuration field name
	 * @param mixed $default Default value if not found
	 * @return mixed
	 */
	public static function get_ai_config_value( $field_name, $default = null ) {
		$value = parent::get_config_value( $field_name, $default );
		
		// Mask the API key for security - only internal methods should access the real value
		if ( $field_name === 'ai_key' && ! empty( $value ) ) {
			return '********';
		}
		
		return $value;
	}

	/**
	 * Get AI configuration from database
	 * Wrapper method for backward compatibility
	 *
	 * @return array
	 */
	public static function get_ai_config() {
		$config = parent::get_config();
		
		// Mask the API key for security - only the OpenAI client should access the real value
		if ( ! empty( $config['ai_key'] ) ) {
			$config['ai_key'] = '********';
		}
		
		return $config;
	}

	/**
	 * Get field options dynamically (for fields that need late loading)
	 * Overrides parent method to provide AI-specific options
	 *
	 * @param string $field_name
	 * @return array
	 */
	public static function get_field_options( $field_name ) {
		switch ( $field_name ) {
			case 'ai_training_data_store_post_types':
				return EPKB_AI_Utilities::get_available_post_types_for_ai();
			default:
				return parent::get_field_options( $field_name );
		}
	}

	/**
	 * Update a specific AI configuration value
	 * Wrapper method for backward compatibility
	 *
	 * @param string $field_name Configuration field name
	 * @param mixed $value New value
	 * @return bool|WP_Error
	 */
	public static function update_ai_config_value( $field_name, $value ) {
		$result = parent::update_config_value( $field_name, $value );
		
		// Clear the dashboard status cache when AI config is updated
		if ( ! is_wp_error( $result ) ) {
			delete_transient( 'epkb_ai_dashboard_status' );
		}
		
		return $result;
	}

	/**
	 * Update AI configuration in database
	 * Wrapper method for backward compatibility
	 *
	 * @param array $new_config New configuration values
	 * @return array|WP_Error Updated configuration or error
	 */
	public static function update_ai_config( $original_config, $new_config ) {

		// If user doesn't have ai-features-pro active, don't save advanced_search mode
		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() && isset( $new_config['ai_search_mode'] ) && $new_config['ai_search_mode'] === 'advanced_search' ) {
			return new WP_Error( 'epkb_ai_features_required', __( 'Advanced Search Results requires AI Features add-on to be installed and active.', 'echo-knowledge-base' ) );
		}

		$new_config = parent::update_config( $new_config );
		if ( is_wp_error( $new_config ) ) {
			return $new_config;
		}

		// Check if AI features are being enabled (from off to preview/on) and ensure DB tables exist
		$search_was_off = $original_config['ai_search_enabled'] == 'off';
		$search_enabled = empty( $new_config['ai_search_enabled'] ) ? $original_config['ai_search_enabled'] == 'on' : $new_config['ai_search_enabled'] == 'on';
		$chat_was_off = $original_config['ai_chat_enabled'] == 'off';
		$chat_enabled = empty( $new_config['ai_chat_enabled'] ) ? $original_config['ai_chat_enabled'] == 'on' : $new_config['ai_chat_enabled'] == 'on';

		// If either feature is being enabled from off state, ensure DB tables exist
		if ( ( $search_was_off && $search_enabled ) || ( $chat_was_off && $chat_enabled ) ) {
			// Force DB table creation by instantiating the DB classes
			new EPKB_AI_Training_Data_DB( true );
			new EPKB_AI_Messages_DB();
		}

		do_action( 'eckb_ai_config_updated', $original_config, $new_config );

		// Clear the dashboard status cache when AI config is updated
		delete_transient( 'epkb_ai_dashboard_status' );

		return $new_config;
	}
	
	/**
	 * Get all AI configuration specifications
	 * Wrapper method for backward compatibility
	 *
	 * @return array
	 */
	public static function get_ai_config_fields_specifications() {
		return self::get_config_fields_specifications();
	}
	
	/**
	 * Get the unmasked API key - for internal use only
	 * This method should only be used by the OpenAI client class
	 *
	 * @return string Encrypted API key value
	 */
	public static function get_unmasked_api_key() {
		// Get directly from parent to bypass masking
		return parent::get_config_value( 'ai_key', '' );
	}
	
	/**
	 * Get the default value for a specific field
	 *
	 * @param string $field_name The field name to get default value for
	 * @return mixed The default value or null if not found
	 */
	public static function get_default_value( $field_name ) {
		$specs = self::get_config_fields_specifications();
		return isset( $specs[$field_name]['default'] ) ? $specs[$field_name]['default'] : null;
	}
}
