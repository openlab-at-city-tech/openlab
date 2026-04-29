<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI General Settings tab with React implementation
 */
class EPKB_AI_General_Settings_Tab {

	/**
	 * Get the configuration for the General Settings tab
	 *
	 * @return array
	 */
	public static function get_tab_config() {

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$ai_config['ai_provider'] = EPKB_AI_Provider::normalize_provider( isset( $ai_config['ai_provider'] ) ? $ai_config['ai_provider'] : '' );

		return array(
			'tab_id' => 'general-settings',
			'title' => __( 'General Settings', 'echo-knowledge-base' ),
			'settings_sections' => self::get_settings_sections( $ai_config ),
			'ai_config' => $ai_config,
			'has_kb_ai_collection' => self::any_kb_has_ai_collection(),
			'setup_steps' => EPKB_AI_Admin_Page::get_setup_steps_for_tab( 'general-settings' )
		);
	}

	/**
	 * Return true if at least one KB config has kb_ai_collection_id set.
	 *
	 * @return bool
	 */
	private static function any_kb_has_ai_collection() {
		$kb_config_obj = new EPKB_KB_Config_DB();
		foreach ( $kb_config_obj->get_kb_configs() as $kb_config ) {
			if ( ! empty( $kb_config['kb_ai_collection_id'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get settings sections configuration
	 *
	 * @param array $ai_config
	 * @return array
	 */
	private static function get_settings_sections( $ai_config ) {
		$provider = $ai_config['ai_provider'];
		$provider_options = EPKB_AI_Provider::get_provider_options();

		$sections = array(
			'api_settings' => array(
				'id' => 'api_settings',
				'title' => __( 'API Configuration', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-key',
				'fields' => array(
					'ai_provider' => array(
						'type' => 'select',
						'label' => __( 'AI Provider', 'echo-knowledge-base' ),
						'value' => $provider,
						'options' => $provider_options,
						'description' => __( 'Choose which provider to use for chat, search, and training data.', 'echo-knowledge-base' )
					),
					'ai_gemini_key' => array(
						'type' => 'password',
						'label' => __( 'Google Gemini API Key', 'echo-knowledge-base' ),
						'value' => empty( $ai_config['ai_gemini_key'] ) ? '' : '********',
						'description' => __( 'Enter your Google Gemini API key.', 'echo-knowledge-base' ),
						'placeholder' => 'AIza...',
						'required' => true,
						'dependency' => array(
							'field' => 'ai_provider',
							'value' => EPKB_AI_Provider::PROVIDER_GEMINI
						)
					),
					'ai_chatgpt_key' => array(
						'type' => 'password',
						'label' => __( 'OpenAI ChatGPT API Key', 'echo-knowledge-base' ),
						'value' => empty( $ai_config['ai_chatgpt_key'] ) ? '' : '********',
						'description' => __( 'Enter your OpenAI ChatGPT API key.', 'echo-knowledge-base' ),
						'placeholder' => 'sk-...',
						'required' => true,
						'dependency' => array(
							'field' => 'ai_provider',
							'value' => EPKB_AI_Provider::PROVIDER_CHATGPT
						)
					),
					'ai_organization_id' => array(
						'type' => 'text',
						'label' => __( 'OpenAI Organization ID', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_organization_id'],
						'description' => __( 'Optional: Enter your OpenAI Organization ID if you belong to multiple organizations', 'echo-knowledge-base' ),
						'placeholder' => 'org-...',
						'dependency' => array(
							'field' => 'ai_provider',
							'value' => EPKB_AI_Provider::PROVIDER_CHATGPT
						)
					)
				)
			),
			'data_privacy' => array(
				'id' => 'data_privacy',
				'title' => __( 'Data Privacy & Disclaimer', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-shield',
				'fields' => array(
					'ai_disclaimer_accepted' => array(
						'type' => 'checkbox',
						'label' => __( 'Data Privacy Acknowledgment', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_disclaimer_accepted'],
						'description' => self::get_disclaimer_text()
					)
				)
			)
		);

		if ( EPKB_AI_Utilities::is_ai_features_pro_enabled() ) {
			$sections['email_notifications'] = array(
				'id' => 'email_notifications',
				'title' => __( 'Email Notifications', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-envelope',
				'fields' => array(
					'ai_email_notifications_enabled' => array(
						'type' => 'checkbox',
						'label' => __( 'Enable Daily Email Summary', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_email_notifications_enabled'],
						'description' => __( 'Send a daily email summary of AI Chat and Search queries', 'echo-knowledge-base' )
					),
					'ai_email_notifications_recipient' => array(
						'type' => 'email',
						'label' => __( 'Recipient Email', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_email_notifications_recipient'],
						'description' => __( 'Leave empty to send to admin email', 'echo-knowledge-base' ),
						'placeholder' => get_option( 'admin_email' )
					),
					'ai_email_notifications_send_time' => array(
						'type' => 'time',
						'label' => __( 'Send Time', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_email_notifications_send_time'],
						// translators: %s is the site timezone string
						'description' => sprintf( __( 'Time in site timezone (%s)', 'echo-knowledge-base' ), wp_timezone_string() ),
						'placeholder' => '09:00'
					),
					'ai_email_notification_subject' => array(
						'type' => 'text',
						'label' => __( 'Email Subject', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_email_notification_subject'],
						'description' => __( 'Available tags: {site_name}', 'echo-knowledge-base' ),
						'placeholder' => 'Daily AI Activity Summary - {site_name}'
					),
					'test_email_button' => array(
						'type' => 'button',
						'label' => '',
						'button_text' => __( 'Send Test Email', 'echo-knowledge-base' ),
						'button_class' => 'epkb-ai-test-email-btn',
						'description' => __( 'Send a test email with current settings', 'echo-knowledge-base' )
					)
				)
			);
		}

		return $sections;
	}

	/**
	 * Check if general settings are configured so that we move the tab to the front if not
	 *
	 * @return bool
	 */
	public static function are_settings_configured() {
		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$provider = EPKB_AI_Provider::normalize_provider( isset( $ai_config['ai_provider'] ) ? $ai_config['ai_provider'] : '' );

		// Check if API key is set for the active provider and disclaimer accepted
		$api_key_field = EPKB_AI_Provider::get_api_key_field( $provider );
		$api_key_set = ! empty( $ai_config[$api_key_field] );
		$disclaimer_set = ! empty( $ai_config['ai_disclaimer_accepted'] ) && $ai_config['ai_disclaimer_accepted'] === 'on';

		return $api_key_set && $disclaimer_set;
	}

	/**
	 * Get the disclaimer text
	 *
	 * @return string
	 */
	private static function get_disclaimer_text() {

		$is_checked = checked( 'on', EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' ), false );

		return "<div class='epkb-ai-disclaimer-text'>" .
					"<p class='epkb-ai-disclaimer-text__intro'>" . esc_html__( 'Please read our AI features privacy and security disclaimer before enabling AI features.', 'echo-knowledge-base' ) . "</p>" .
					"<p class='epkb-ai-disclaimer-text__link'>" .
						"<a href='https://www.echoknowledgebase.com/privacy-security-disclaimer' target='_blank'>" .
							esc_html__( 'View Privacy & Security Disclaimer', 'echo-knowledge-base' ) . " →" .
						"</a>" .
					"</p>" .
					"<label class='epkb-ai-disclaimer-text__checkbox'>" .
						"<input type='checkbox' name='ai_disclaimer_accepted' value='on' " . $is_checked . ">" .
						esc_html__( 'I have read and accept the privacy & security disclaimer', 'echo-knowledge-base' ) .
					"</label>" .
				"</div>";
	}
}
