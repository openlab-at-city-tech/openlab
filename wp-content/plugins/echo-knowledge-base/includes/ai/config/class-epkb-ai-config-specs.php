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
	 * Clear cached AI configuration plus any derived caches.
	 */
	public static function clear_cache() {
		parent::clear_cache();
		EPKB_AI_Provider::clear_cache();
	}

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

		// translators: %s is the AI refusal prompt message
		$default_instructions = sprintf( __( 'You may ONLY answer using information from the vector store. Do not mention references, documents, files, or sources. Do not reveal retrieval, guess, speculate, or use outside knowledge. If no relevant information is found, reply exactly: %s If relevant information is found, you may give structured explanations, including comparisons, pros and cons, or decision factors, but only if they are in the data. Answer only what the data supports; when unsure, leave it out.', 'echo-knowledge-base' ), self::get_ai_refusal_prompt() );

		$ai_specs = array(

			/***  AI General Settings ***/
			'ai_disclaimer_accepted' => array(
				'name'      => 'ai_disclaimer_accepted',
				'type'      => EPKB_Input_Filter::CHECKBOX,
				'default'   => 'off'
			),
			'ai_key' => array(	// TODO legacy remove in April 2026
				'name'        => 'ai_key',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'         => 20,
				'max'         => 2500
			),
			'ai_chatgpt_key' => array(
				'name'        => 'ai_chatgpt_key',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'         => 20,
				'max'         => 2500
			),
			'ai_gemini_key' => array(
				'name'        => 'ai_gemini_key',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'         => 20,
				'max'         => 2500
			),
			'ai_provider' => array(
				'name'    => 'ai_provider',
				'type'    => EPKB_Input_Filter::SELECTION,
				'options' => EPKB_AI_Provider::get_provider_options(),
				'default' => EPKB_AI_Provider::PROVIDER_GEMINI
			),

			'ai_organization_id' => array(
				'name'        => 'ai_organization_id',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'min'		  => 3,
				'max'  => 256
			),
			'ai_show_sources' => array(
				'name'        => 'ai_show_sources',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
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
			'ai_chat_preset' => array(
				'name'        => 'ai_chat_preset',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => EPKB_AI_Provider::BALANCED_PRESET,
				'min'         => 0,
				'max'         => 50
			),
				'ai_chat_widgets' => array(
					'name'        => 'ai_chat_widgets',
					'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
					'default'     => array( 1 ),
				),
				'ai_chat_instructions' => array(
					'name'        => 'ai_chat_instructions',
					'type'        => EPKB_Input_Filter::AI_PROMPT,
					'default'     => $default_instructions,
					'min'         => 0,
					'max'         => 10000
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

			// AI Chat - Collection 1 Display Rules
			'ai_chat_display_collection' => array(
				'name'        => 'ai_chat_display_collection',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 999
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

			// AI Chat - Collection 2 Display Rules
			'ai_chat_display_collection_2' => array(
				'name'        => 'ai_chat_display_collection_2',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0,
				'min'         => 0,
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
				'default'     => 0,
				'min'         => 0,
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
				'default'     => 0,
				'min'         => 0,
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
				'default'     => 0,
				'min'         => 0,
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

			/***  AI Chat Access Control Settings ***/
			'ai_chat_access_mode' => array(
				'name' => 'ai_chat_access_mode', 'type' => EPKB_Input_Filter::SELECTION,
				'options' => array( 'all' => 'Everyone', 'logged_in' => 'Logged-in Users Only', 'wp_role' => 'Specific WordPress Roles' ),
				'default' => 'all'
			),
			'ai_chat_access_roles' => array(
				'name' => 'ai_chat_access_roles', 'type' => EPKB_Input_Filter::INTERNAL_ARRAY, 'default' => array()
			),
			'ai_chat_access_mode_2' => array(
				'name' => 'ai_chat_access_mode_2', 'type' => EPKB_Input_Filter::SELECTION,
				'options' => array( 'all' => 'Everyone', 'logged_in' => 'Logged-in Users Only', 'wp_role' => 'Specific WordPress Roles' ),
				'default' => 'all'
			),
			'ai_chat_access_roles_2' => array(
				'name' => 'ai_chat_access_roles_2', 'type' => EPKB_Input_Filter::INTERNAL_ARRAY, 'default' => array()
			),
			'ai_chat_access_mode_3' => array(
				'name' => 'ai_chat_access_mode_3', 'type' => EPKB_Input_Filter::SELECTION,
				'options' => array( 'all' => 'Everyone', 'logged_in' => 'Logged-in Users Only', 'wp_role' => 'Specific WordPress Roles' ),
				'default' => 'all'
			),
			'ai_chat_access_roles_3' => array(
				'name' => 'ai_chat_access_roles_3', 'type' => EPKB_Input_Filter::INTERNAL_ARRAY, 'default' => array()
			),
			'ai_chat_access_mode_4' => array(
				'name' => 'ai_chat_access_mode_4', 'type' => EPKB_Input_Filter::SELECTION,
				'options' => array( 'all' => 'Everyone', 'logged_in' => 'Logged-in Users Only', 'wp_role' => 'Specific WordPress Roles' ),
				'default' => 'all'
			),
			'ai_chat_access_roles_4' => array(
				'name' => 'ai_chat_access_roles_4', 'type' => EPKB_Input_Filter::INTERNAL_ARRAY, 'default' => array()
			),
			'ai_chat_access_mode_5' => array(
				'name' => 'ai_chat_access_mode_5', 'type' => EPKB_Input_Filter::SELECTION,
				'options' => array( 'all' => 'Everyone', 'logged_in' => 'Logged-in Users Only', 'wp_role' => 'Specific WordPress Roles' ),
				'default' => 'all'
			),
			'ai_chat_access_roles_5' => array(
				'name' => 'ai_chat_access_roles_5', 'type' => EPKB_Input_Filter::INTERNAL_ARRAY, 'default' => array()
			),

			/***  AI Chat Handoff Settings ***/
			'ai_chat_feedback_enabled' => array(
				'name'        => 'ai_chat_feedback_enabled',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'ai_chat_feedback_with_handoff' => array(
				'name'        => 'ai_chat_feedback_with_handoff',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'ai_chat_handoff_enabled' => array(
				'name'        => 'ai_chat_handoff_enabled',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'ai_chat_handoff_phone_enabled' => array(
				'name'        => 'ai_chat_handoff_phone_enabled',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'ai_chat_handoff_method' => array(
				'name'        => 'ai_chat_handoff_method',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'email' => __( 'Email (Contact Form)', 'echo-knowledge-base' )
				),
				'default'     => 'email'
			),
			'ai_chat_handoff_button_display' => array(
				'name'        => 'ai_chat_handoff_button_display',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'always'              => __( 'Always show', 'echo-knowledge-base' ),
					'after_first_response' => __( 'After first response', 'echo-knowledge-base' ),
					'after_keyword'        => __( 'After keyword trigger', 'echo-knowledge-base' )
				),
				'default'     => 'always'
			),
			'ai_chat_handoff_button_text' => array(
				'name'        => 'ai_chat_handoff_button_text',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact an Agent', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_chat_handoff_heading' => array(
				'name'        => 'ai_chat_handoff_heading',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact an Agent', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			'ai_chat_handoff_keywords' => array(
				'name'        => 'ai_chat_handoff_keywords',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => 'human,agent,representative,support,customer support,live agent,real person,talk to a human,contact support',
				'min'         => 0,
				'max'         => 1000
			),
			'ai_chat_handoff_destination_email' => array(
				'name'        => 'ai_chat_handoff_destination_email',
				'type'        => EPKB_Input_Filter::EMAIL,
				'default'     => '',
				'min'         => 0,
				'max'         => 100
			),
			'ai_chat_handoff_consent_text' => array(
				'name'        => 'ai_chat_handoff_consent_text',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => __( 'By submitting this form, you agree that your contact details and chat transcript will be shared with our support team.', 'echo-knowledge-base' ),
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
			'ai_search_preset' => array(
				'name'        => 'ai_search_preset',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => EPKB_AI_Provider::BALANCED_PRESET,
				'min'         => 0,
				'max'         => 50
			),
			'ai_search_mode' => array(
				'name'        => 'ai_search_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'simple_search' => __( 'Simple Search', 'echo-knowledge-base' ),
					'smart_search'  => __( 'Smart Search', 'echo-knowledge-base' )
				),
				'default'     => 'simple_search'
			),

				/**   AI Search - Ask AI */
			'ai_search_instructions' => array(
				'name'        => 'ai_search_instructions',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
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
			'ai_search_results_articles_count' => array(
				'name'        => 'ai_search_results_articles_count',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 5,
				'min'         => 1,
				'max'         => 20
			),
			'ai_search_results_continue_in_chat' => array(
				'name'        => 'ai_search_results_continue_in_chat',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
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
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),

			/***  AI Search Results - Section Prompts ***/
			'ai_search_results_tips_prompt' => array(
				'name'        => 'ai_search_results_tips_prompt',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),
			'ai_search_results_steps_prompt' => array(
				'name'        => 'ai_search_results_steps_prompt',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),
			'ai_search_results_glossary_prompt' => array(
				'name'        => 'ai_search_results_glossary_prompt',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),
			'ai_search_results_you_can_also_ask_prompt' => array(
				'name'        => 'ai_search_results_you_can_also_ask_prompt',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),
			'ai_search_results_tasks_list_prompt' => array(
				'name'        => 'ai_search_results_tasks_list_prompt',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
				'default'     => '',
				'min'         => 0,
				'max'         => 10000
			),
			'ai_search_results_related_keywords_prompt' => array(
				'name'        => 'ai_search_results_related_keywords_prompt',
				'type'        => EPKB_Input_Filter::AI_PROMPT,
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
	 *
	 * @param string $field_name Configuration field name
	 * @param mixed $default Default value if not found
	 * @return mixed
	 */
	public static function get_ai_config_value( $field_name, $default = null ) {

		if ( in_array( $field_name, array( 'ai_chat_handoff_enabled', 'ai_chat_handoff_phone_enabled', 'ai_chat_feedback_enabled', 'ai_chat_feedback_with_handoff' ), true ) && ! EPKB_Utilities::is_ai_features_pro_enabled() ) {
			return 'off';
		}

		if ( $field_name === 'ai_search_mode' && ! EPKB_Utilities::is_ai_features_pro_enabled() ) {
			return 'simple_search';
		}

		$config = static::get_ai_config();

		// If field exists in config, return it
		if ( isset( $config[ $field_name ] ) ) {

			// Mask API keys for security - only internal methods should access the real value
			if ( in_array( $field_name, array( 'ai_chatgpt_key', 'ai_gemini_key' ), true ) && ! empty( $config[ $field_name ] ) ) {
				return '********';
			}

			return $config[ $field_name ];
		}

		// If no default was supplied, get default from field specifications
		if ( $default === null ) {
			return static::get_field_default( $field_name );
		}

		return $default;
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
	 * Update AI configuration in database
	 *
	 * @param array $new_config New configuration values
	 * @return array|WP_Error Updated configuration or error
	 */
	public static function update_ai_config( $original_config, $new_config ) {

		$original_config['ai_provider'] = EPKB_AI_Provider::normalize_provider( isset( $original_config['ai_provider'] ) ? $original_config['ai_provider'] : '' );
		if ( isset( $new_config['ai_provider'] ) ) {
			$new_config['ai_provider'] = EPKB_AI_Provider::normalize_provider( $new_config['ai_provider'] );
		}

		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() && isset( $new_config['ai_search_mode'] ) && $new_config['ai_search_mode'] === 'smart_search' ) {
			return new WP_Error( 'validation_failed', __( 'Smart Search requires AI Features PRO.', 'echo-knowledge-base' ) );
		}

		// If provider is changing, reset collection settings that belong to the now-inactive provider
		if ( isset( $new_config['ai_provider'] ) && $new_config['ai_provider'] !== $original_config['ai_provider'] ) {
			$new_config = self::reset_inactive_provider_collections( $original_config, $new_config );
		}

		$new_config = EPKB_AI_Provider::migrate_ai_config( array_merge( $original_config, $new_config ) );

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
	 * Get the unmasked API key for a specific provider - for internal use only
	 *
	 * @param string $provider Provider constant (chatgpt or gemini)
	 * @return string Encrypted API key value
	 */
	public static function get_unmasked_api_key_for_provider( $provider ) {
		$key_field = $provider === EPKB_AI_Provider::PROVIDER_GEMINI ? 'ai_gemini_key' : 'ai_chatgpt_key';

		// Bypass get_ai_config() which masks the keys - read directly from database
		$config = get_option( self::OPTION_NAME, array() );

		return isset( $config[$key_field] ) ? $config[$key_field] : '';
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

	/**
	 * Reset collection settings that belong to the now-inactive provider when provider changes
	 *
	 * @param array $original_config Original configuration before the update
	 * @param array $new_config New configuration being saved
	 * @return array Updated new_config with inactive provider collections reset to 0
	 */
	private static function reset_inactive_provider_collections( $original_config, $new_config ) {
		$old_provider = $original_config['ai_provider'];

		// Get all collections to determine which belong to which provider
		$all_collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections( false, false );
		if ( is_wp_error( $all_collections ) || empty( $all_collections ) ) {
			return $new_config;
		}

		// Collection settings that may need to be reset
		$collection_fields = array(
			'ai_chat_display_collection',
			'ai_chat_display_collection_2',
			'ai_chat_display_collection_3',
			'ai_chat_display_collection_4',
			'ai_chat_display_collection_5'
		);

		// Check each collection field
		foreach ( $collection_fields as $field ) {
			// Get the current value from new_config if set, otherwise from original_config
			$collection_id = isset( $new_config[$field] ) ? absint( $new_config[$field] ) : absint( $original_config[$field] ?? 0 );

			if ( empty( $collection_id ) ) {
				continue;
			}

			// Check if this collection belongs to the old (now inactive) provider
			if ( ! isset( $all_collections[$collection_id] ) ) {
				continue;
			}

			$collection_provider = self::get_collection_provider_for_provider_switch( $all_collections[$collection_id] );
			if ( empty( $collection_provider ) ) {
				continue;
			}

			// If collection belongs to the old provider, reset it to 0
			if ( $collection_provider === $old_provider ) {
				$new_config[$field] = 0;
			}
		}

		// Reset kb_ai_collection_id in any KB config whose selected collection belongs to the old provider
		$kb_config_obj = new EPKB_KB_Config_DB();
		$all_kb_configs = $kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $kb_id => $kb_config ) {
			$kb_collection_id = isset( $kb_config['kb_ai_collection_id'] ) ? absint( $kb_config['kb_ai_collection_id'] ) : 0;
			if ( empty( $kb_collection_id ) || ! isset( $all_collections[$kb_collection_id] ) ) {
				continue;
			}

			$kb_collection_provider = self::get_collection_provider_for_provider_switch( $all_collections[$kb_collection_id] );
			if ( empty( $kb_collection_provider ) ) {
				continue;
			}

			if ( $kb_collection_provider !== $old_provider ) {
				continue;
			}

			$kb_config['kb_ai_collection_id'] = 0;
			$update_result = $kb_config_obj->update_kb_configuration( $kb_id, $kb_config );
			if ( is_wp_error( $update_result ) ) {
				EPKB_AI_Log::add_log( 'Failed to reset kb_ai_collection_id on provider switch - KB ID: ' . $kb_id . ' - Collection ID: ' . $kb_collection_id . ' - ' . $update_result->get_error_message() );
			}
		}

		return $new_config;
	}

	/**
	 * Resolve one collection provider for provider-switch cleanup.
	 *
	 * @param array $collection_config
	 * @return string
	 */
	private static function get_collection_provider_for_provider_switch( $collection_config ) {
		if ( empty( $collection_config['ai_training_data_provider'] ) ) {
			return '';
		}

		return EPKB_AI_Provider::normalize_provider( $collection_config['ai_training_data_provider'] );
	}
}
