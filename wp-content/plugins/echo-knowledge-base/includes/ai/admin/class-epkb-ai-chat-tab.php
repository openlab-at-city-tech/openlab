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

		$has_ai_features_pro = EPKB_Utilities::is_ai_features_pro_enabled();
		if ( ! $has_ai_features_pro ) {
			$ai_config['ai_chat_handoff_enabled'] = 'off';
		}

		// Get default widget configuration
		$default_widget_config = EPKB_AI_Chat_Widget_Config_Specs::get_widget_config( 1 );

		return array(
			'tab_id' => 'chat',
			'title' => __( 'Chat', 'echo-knowledge-base' ),
			'sub_tabs' => self::get_sub_tabs_config(),
			'settings_sections' => self::get_settings_sections( $ai_config ),
			'ai_config' => $ai_config,
			'is_ai_features_pro_enabled' => $has_ai_features_pro,
			'collection_issues' => self::get_collection_issues( $ai_config ),
			'widget_config' => $default_widget_config,
			'all_widgets' => EPKB_AI_Chat_Widget_Config_Specs::get_all_widget_configs(),
			'setup_steps' => EPKB_AI_Admin_Page::get_setup_steps_for_tab( 'chat' )
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

		$preset_options = EPKB_AI_Provider::get_preset_options( 'chat' );

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
					),
						'ai_chat_preset' => array(
							'type' => 'select',
							'label' => __( 'Choose AI Behavior', 'echo-knowledge-base' ),
							'value' => EPKB_AI_Provider::get_feature_preset( 'chat', $ai_config ),
							'options' => $preset_options,
							'description' => __( 'Choose whether AI Chat should prioritize speed, balance, or answer quality.', 'echo-knowledge-base' ),
							'field_class' => 'epkb-ai-behavior-preset-select'
						),
					'ai_show_sources' => array(
						'type' => 'checkbox',
						'label' => __( 'Show Source References', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_show_sources'],
							'description' => __( 'Display links to source articles that were used to generate the AI answer', 'echo-knowledge-base' ),
							'field_class' => 'epkb-ai-show-sources'
						)
						)
					),
			'display_settings' => self::get_display_settings_section( $ai_config ),
			'handoff_settings' => self::get_handoff_settings_section( $ai_config ),
			'default_chat_widget' => self::get_widget_settings_section()
		);
	}

	/**
	 * Find collection issues for selected AI Chat collections
	 *
	 * @param array $ai_config
	 * @return array
	 */
	private static function get_collection_issues( $ai_config ) {
		$issues = array();

		$collection_fields = array(
			'ai_chat_display_collection',
			'ai_chat_display_collection_2',
			'ai_chat_display_collection_3',
			'ai_chat_display_collection_4',
			'ai_chat_display_collection_5'
		);

		$checked_collections = array();

		foreach ( $collection_fields as $field_name ) {
			if ( empty( $ai_config[ $field_name ] ) ) {
				continue;
			}

			$collection_id = absint( $ai_config[ $field_name ] );
			if ( $collection_id === 0 || isset( $checked_collections[ $collection_id ] ) ) {
				continue;
			}

			$checked_collections[ $collection_id ] = true;

			$validation_result = EPKB_AI_Validation::validate_collection_has_vector_store( $collection_id, 'chat' );
			if ( is_wp_error( $validation_result ) ) {
				$issues[] = array(
					'collection_id'   => $collection_id,
					'collection_name' => self::get_collection_label( $collection_id ),
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
				'send_button_background_color' => array(
					'type' => 'color',
					'label' => __( 'Send Button Color', 'echo-knowledge-base' ),
					'value' => $widget_config['send_button_background_color'],
					'description' => __( 'Background color of the send message button', 'echo-knowledge-base' )
				),
				'new_button_background_color' => array(
					'type' => 'color',
					'label' => __( 'New Conversation Button Color', 'echo-knowledge-base' ),
					'value' => $widget_config['new_button_background_color'],
					'description' => __( 'Background color of the new conversation button', 'echo-knowledge-base' )
				),
				'user_message_background_color' => array(
					'type' => 'color',
					'label' => __( 'User Message Background Color', 'echo-knowledge-base' ),
					'value' => $widget_config['user_message_background_color'],
					'description' => __( 'Background color of user question message bubbles', 'echo-knowledge-base' )
				),
				'ai_message_background_color' => array(
					'type' => 'color',
					'label' => __( 'AI Message Background Color', 'echo-knowledge-base' ),
					'value' => $widget_config['ai_message_background_color'],
					'description' => __( 'Background color of AI response message bubbles', 'echo-knowledge-base' )
				),
				
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

		// Get training data collections for the dropdown (active provider only)
		$collection_options = EPKB_AI_Training_Data_Config_Specs::get_active_provider_collection_options();

		// Get Knowledge Base post types
		$kb_post_types = self::get_kb_post_types_for_display();

		$has_ai_features_pro = EPKB_Utilities::is_ai_features_pro_enabled();
		$wp_roles_options = self::get_wp_roles_for_display();

		// Build location tabs
		$location_tabs = array();
		for ( $i = 1; $i <= 5; $i++ ) {
			$suffix = $i === 1 ? '' : "_{$i}";
			$tab_id = "location-{$i}";
			// translators: %d is the location number
			$tab_label = sprintf( __( 'Location %d', 'echo-knowledge-base' ), $i );

			// Build fields for this location tab
			$tab_fields = array(
				// Collection selection
				"ai_chat_display_collection{$suffix}" => array(
					'type' => 'select',
					'label' => __( 'Training Data Collection', 'echo-knowledge-base' ),
					'value' => isset( $ai_config["ai_chat_display_collection{$suffix}"] ) ? $ai_config["ai_chat_display_collection{$suffix}"] : 0,
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

			// Access Control
			$tab_fields["ai_chat_access_mode{$suffix}"] = array(
				'type' => 'radio',
				'label' => __( 'Access Control', 'echo-knowledge-base' ),
				'value' => isset( $ai_config["ai_chat_access_mode{$suffix}"] ) ? $ai_config["ai_chat_access_mode{$suffix}"] : 'all',
				'options' => array(
					'all'       => __( 'Everyone', 'echo-knowledge-base' ),
					'logged_in' => __( 'Logged-in Users Only', 'echo-knowledge-base' ),
					'wp_role'   => __( 'Specific WordPress Roles', 'echo-knowledge-base' )
				),
				'pro_feature' => ! $has_ai_features_pro,
			);

			$tab_fields["ai_chat_access_roles{$suffix}"] = array(
				'type' => 'checkboxes',
				'label' => __( 'Allowed Roles', 'echo-knowledge-base' ),
				'value' => isset( $ai_config["ai_chat_access_roles{$suffix}"] ) ? $ai_config["ai_chat_access_roles{$suffix}"] : array(),
				'options' => $wp_roles_options,
				'pro_feature' => ! $has_ai_features_pro,
				'field_class' => 'epkb-ai-chat-access-roles',
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
			'value' => isset( $ai_config['ai_chat_display_collection'] ) ? $ai_config['ai_chat_display_collection'] : 0,
			'options' => $collection_options,
			'description' => __( 'Select which Training Data Collection to use for the AI chat widget.', 'echo-knowledge-base' ),
			'field_class' => 'epkb-ai-chat-collection-select',
			'hidden' => $ai_config['ai_chat_display_mode'] !== 'all_pages'
		);

		$global_fields['ai_chat_access_mode'] = array(
			'type' => 'radio',
			'label' => __( 'Access Control', 'echo-knowledge-base' ),
			'value' => isset( $ai_config['ai_chat_access_mode'] ) ? $ai_config['ai_chat_access_mode'] : 'all',
			'options' => array(
				'all'       => __( 'Everyone', 'echo-knowledge-base' ),
				'logged_in' => __( 'Logged-in Users Only', 'echo-knowledge-base' ),
				'wp_role'   => __( 'Specific WordPress Roles', 'echo-knowledge-base' )
			),
			'pro_feature' => ! $has_ai_features_pro,
			'hidden' => $ai_config['ai_chat_display_mode'] !== 'all_pages'
		);

		$global_fields['ai_chat_access_roles'] = array(
			'type' => 'checkboxes',
			'label' => __( 'Allowed Roles', 'echo-knowledge-base' ),
			'value' => isset( $ai_config['ai_chat_access_roles'] ) ? $ai_config['ai_chat_access_roles'] : array(),
			'options' => $wp_roles_options,
			'pro_feature' => ! $has_ai_features_pro,
			'hidden' => $ai_config['ai_chat_display_mode'] !== 'all_pages',
			'field_class' => 'epkb-ai-chat-access-roles',
		);

		$global_fields['collection_tabs_description'] = array(
			'type' => 'html',
			'html' => "<div class='epkb-collection-tabs-description'>" . '<p>' .
							esc_html__( 'Configure up to 5 different Training Data Collections for different pages. Location 1 has the highest priority and is checked first. If no match is found, Location 2 is checked, and so on.', 'echo-knowledge-base' ) . '</p>' .
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
	 * Get handoff settings section configuration
	 *
	 * @param array $ai_config
	 * @return array
	 */
	private static function get_handoff_settings_section( $ai_config ) {
		$has_ai_features_pro = EPKB_Utilities::is_ai_features_pro_enabled();

		if ( ! $has_ai_features_pro ) {
			return array(
				'id' => 'handoff_settings',
				'title' => __( 'Human Handoff', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-user',
				'sub_tab' => 'chat-settings',
				'fields' => array(
					'ai_chat_handoff_pro_ad' => array(
						'type' => 'html',
						'html' => self::get_handoff_pro_feature_ad()
					)
				)
			);
		}

		return array(
			'id' => 'handoff_settings',
			'title' => __( 'Human Handoff', 'echo-knowledge-base' ),
			'icon' => 'epkbfa epkbfa-user',
			'sub_tab' => 'chat-settings',
			'fields' => array(
				'ai_chat_feedback_enabled' => array(
					'type' => 'toggle',
					'label' => __( 'Enable Feedback Buttons', 'echo-knowledge-base' ),
					'value' => $ai_config['ai_chat_feedback_enabled'],
					'description' => __( 'Show thumbs up/down feedback options below AI responses.', 'echo-knowledge-base' )
				),
				'ai_chat_feedback_with_handoff' => array(
					'type' => 'toggle',
					'label' => __( 'Enable Negative Feedback Actions', 'echo-knowledge-base' ),
					'value' => $ai_config['ai_chat_feedback_with_handoff'],
					'description' => __( 'Show follow-up actions (Try a different approach, Talk to a human) after a thumbs-down response.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_enabled' => array(
					'type' => 'toggle',
					'label' => __( 'Enable Human Handoff', 'echo-knowledge-base' ),
					'value' => $ai_config['ai_chat_handoff_enabled'],
					'description' => __( 'Allow visitors to request a human agent from the AI chat widget.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_phone_enabled' => array(
					'type' => 'toggle',
					'label' => __( 'Enable Phone Field', 'echo-knowledge-base' ),
					'value' => $ai_config['ai_chat_handoff_phone_enabled'],
					'description' => __( 'Show an optional phone field on the handoff form.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_keywords' => array(
					'type' => 'textarea',
					'label' => __( 'Human Handoff Triggers', 'echo-knowledge-base' ),
					'value' => isset( $ai_config['ai_chat_handoff_keywords'] ) ? str_replace( ',', "\n", $ai_config['ai_chat_handoff_keywords'] ) : '',
					'description' => __( 'Keywords or phrases that trigger an agent handoff.', 'echo-knowledge-base' ),
					'rows' => 4
				),
				'ai_chat_handoff_button_display' => array(
					'type' => 'select',
					'label' => __( 'Human Handoff Button Visibility', 'echo-knowledge-base' ),
					'value' => isset( $ai_config['ai_chat_handoff_button_display'] ) ? $ai_config['ai_chat_handoff_button_display'] : 'always',
					'options' => array(
						'always'              => __( 'Always show', 'echo-knowledge-base' ),
						'after_first_response' => __( 'After first response', 'echo-knowledge-base' ),
						'after_keyword'        => __( 'After keyword trigger', 'echo-knowledge-base' )
					),
					'description' => __( 'Control when the Contact an Agent button appears in the chat window.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_button_text' => array(
					'type' => 'text',
					'label' => __( 'Human Handoff Button Text', 'echo-knowledge-base' ),
					'value' => isset( $ai_config['ai_chat_handoff_button_text'] ) ? $ai_config['ai_chat_handoff_button_text'] : __( 'Contact an Agent', 'echo-knowledge-base' ),
					'description' => __( 'Text displayed on the handoff button in the chat UI.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_destination_email' => array(
					'type' => 'text',
					'label' => __( 'Destination Email', 'echo-knowledge-base' ),
					'value' => isset( $ai_config['ai_chat_handoff_destination_email'] ) ? $ai_config['ai_chat_handoff_destination_email'] : '',
					'description' => __( 'Leave blank to use the site admin email address.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_heading' => array(
					'type' => 'text',
					'label' => __( 'Human Handoff Form Heading', 'echo-knowledge-base' ),
					'value' => isset( $ai_config['ai_chat_handoff_heading'] ) ? $ai_config['ai_chat_handoff_heading'] : __( 'Contact an Agent', 'echo-knowledge-base' ),
					'description' => __( 'Heading displayed above the handoff form.', 'echo-knowledge-base' )
				),
				'ai_chat_handoff_consent_text' => array(
					'type' => 'textarea',
					'label' => __( 'Consent Text', 'echo-knowledge-base' ),
					'value' => isset( $ai_config['ai_chat_handoff_consent_text'] ) ? $ai_config['ai_chat_handoff_consent_text'] : '',
					'description' => __( 'Displayed below the handoff form before submission.', 'echo-knowledge-base' ),
					'rows' => 3
				),
			)
		);
	}

	/**
	 * Get Pro Feature Ad for Human Handoff
	 *
	 * @return string
	 */
	private static function get_handoff_pro_feature_ad() {
		return EPKB_HTML_Forms::pro_feature_ad_box( array(
			'id' => 'epkb-ai-chat-handoff-ad',
			'class' => 'epkb-ai-chat-pro-ad',
			'title' => __( 'AI Chat Human Handoff', 'echo-knowledge-base' ),
			'desc' => __( 'Let visitors request a human agent and hand off complex questions to your team.', 'echo-knowledge-base' ),
			'list' => array(
				__( 'Offer a Contact an Agent button in the chat window', 'echo-knowledge-base' ),
				__( 'Trigger handoff on keywords or thumbs-down feedback', 'echo-knowledge-base' ),
				__( 'Collect name, email, and message with the chat transcript', 'echo-knowledge-base' ),
				__( 'Send handoff requests to your support team', 'echo-knowledge-base' )
			),
			'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
			'btn_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/ai-features/',
			'btn_text_2' => __( 'Try AI Chat with Agent Handoff', 'echo-knowledge-base' ),
			'btn_url_2' => 'https://www.echoknowledgebase.com/',
			'discount_coupon' => EPKB_AI_PRO_Features_Tab::get_discount_coupon(),
			'return_html' => true
		) );
	}

	/**
	 * Get all WordPress roles for access control checkboxes
	 *
	 * @return array
	 */
	private static function get_wp_roles_for_display() {
		$roles = wp_roles()->get_names();
		$options = array();
		foreach ( $roles as $role_key => $role_name ) {
			if ( $role_key === 'administrator' ) {
				continue; // Admins always have access
			}
			$options[ $role_key ] = translate_user_role( $role_name );
		}
		return $options;
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
			// translators: %d is the Knowledge Base ID number
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : sprintf( __( 'Knowledge Base %d', 'echo-knowledge-base' ), $kb_id );
			$kb_post_types[ $kb_post_type ] = $kb_name;
		}

		return $kb_post_types;
	}

	/**
	 * AJAX handler to apply chat preset
	 */
	public static function ajax_apply_chat_preset() {
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_ai_feature' );

		$preset_key = EPKB_Utilities::post( 'preset', '', false );
		if ( empty( $preset_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid preset selected', 'echo-knowledge-base' ) ) );
			return;
		}

		$result = EPKB_AI_Provider::apply_preset( $preset_key, 'chat' );
		wp_send_json_success( $result );
	}

}
