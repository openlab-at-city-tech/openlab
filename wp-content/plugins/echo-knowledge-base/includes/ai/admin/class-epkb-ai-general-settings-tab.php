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

		return array(
			'tab_id' => 'general-settings',
			'title' => __( 'General Settings', 'echo-knowledge-base' ),
			'settings_sections' => self::get_settings_sections( $ai_config ),
			'ai_config' => $ai_config
		);
	}

	/**
	 * Get settings sections configuration
	 *
	 * @param array $ai_config
	 * @return array
	 */
	private static function get_settings_sections( $ai_config ) {
		$sections = array(
			'api_settings' => array(
				'id' => 'api_settings',
				'title' => __( 'API Configuration', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-key',
				'fields' => array(
					'ai_key' => array(
						'type' => 'password',
						'label' => __( 'OpenAI API Key', 'echo-knowledge-base' ),
						'value' => empty( $ai_config['ai_key'] ) ? '': '********',
						'description' => sprintf( __( 'Enter your OpenAI API key. You can find it at %s', 'echo-knowledge-base' ), '<a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a>' ),
						'placeholder' => 'sk-...',
						'required' => true
					),
					'ai_organization_id' => array(
						'type' => 'text',
						'label' => __( 'Organization ID', 'echo-knowledge-base' ),
						'value' => $ai_config['ai_organization_id'],
						'description' => __( 'Optional: Enter your OpenAI Organization ID if you belong to multiple organizations', 'echo-knowledge-base' ),
						'placeholder' => 'org-...'
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
		
		// Check if API key is set and disclaimer accepted
		$api_key_set = ! empty( $ai_config['ai_key'] );
		$disclaimer_set = ! empty( $ai_config['ai_disclaimer_accepted'] ) && $ai_config['ai_disclaimer_accepted'] === 'on';
		
		return $api_key_set && $disclaimer_set;
	}

	/**
	 * Get the disclaimer text
	 *
	 * @return string
	 */
	private static function get_disclaimer_text() {
			return sprintf(
		'<div class="epkb-ai-disclaimer-text">%s</div>',
		sprintf(
			'<p style="margin-bottom: 15px;">%s</p>
			<p style="margin-bottom: 20px;">
				<a href="%s" target="_blank" style="color: #2271b1; text-decoration: underline; font-weight: bold;">
					%s
				</a>
			</p>	
			<label style="font-weight: bold; display: flex; align-items: center;">
				<input type="checkbox" name="ai_disclaimer_accepted" value="on" %s style="margin-right: 8px;">
				%s
			</label>',
			__( 'Please read our AI features privacy and security disclaimer before enabling AI features.', 'echo-knowledge-base' ),
			'https://www.echoknowledgebase.com/privacy-security-disclaimer',
			__( 'View Privacy & Security Disclaimer', 'echo-knowledge-base' ) . ' →',
			checked( 'on', EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted' ), false ),
			__( 'I have read and accept the privacy & security disclaimer', 'echo-knowledge-base' )
		)
	);
	}
}