<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * If user is deactivating plugin, find out why
 */
class EPKB_Deactivate_Feedback {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_feedback_dialog_scripts' ] );
		add_action( 'wp_ajax_epkb_deactivate_feedback', [ $this, 'ajax_epkb_deactivate_feedback' ] );
	}

	/**
	 * Enqueue feedback dialog scripts.
	 */
	public function enqueue_feedback_dialog_scripts() {
		add_action( 'admin_footer', [ $this, 'output_deactivate_feedback_dialog' ] );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'epkb-admin-feedback', Echo_Knowledge_Base::$plugin_url . 'js/admin-feedback' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
		wp_register_style( 'epkb-admin-feedback-style', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-feedback' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

		wp_enqueue_script( 'epkb-admin-feedback' );
		wp_enqueue_style( 'epkb-admin-feedback-style' );
	}

	/**
	 * Display a dialog box to ask the user why they deactivated the KB.
	 */
	public function output_deactivate_feedback_dialog() {   ?>
        <div class="epkb-deactivate-modal" id="epkb-deactivate-modal" style="display:none;">
            <div class="epkb-deactivate-modal-wrap">
                <form id="epkb-deactivate-feedback-dialog-form" method="post">
                    <div class="epkb-deactivate-modal-header">
                        <h3><?php esc_html_e( 'Quick Feedback', 'echo-knowledge-base' ); ?></h3>
                    </div>
                    <div class="epkb-deactivate-modal-body">
                        <div class="epkb-deactivate-modal-reason-input-wrap">
	                        <h4><?php esc_html_e( 'Please tell us what happened. Thank you!', 'echo-knowledge-base' ); ?></h4>
                            <textarea class="epkb-deactivate-feedback-text" name="epkb_deactivate_feedback"></textarea>
                        </div>
                    </div>

                    <div class="epkb-deactivate-modal-footer">
	                    <button class="epkb-deactivate-submit-modal"><?php echo esc_html__( 'Deactivate', 'echo-knowledge-base' ); ?></button>
	                    <button class="epkb-deactivate-button-secondary epkb-deactivate-cancel-modal"><?php echo esc_html__( 'Cancel', 'echo-knowledge-base' ); ?></button>
	                    <input type="hidden" name="action" value="epkb_deactivate_feedback" />  <?php
                        wp_nonce_field( '_epkb_deactivate_feedback_nonce' );    ?>
                    </div>
                </form>
            </div>
        </div>  <?php
	}

	/**
	 * Send the user feedback when KB is deactivated.
	 */
	public function ajax_epkb_deactivate_feedback() {

		$wpnonce_value = EPKB_Utilities::post( '_wpnonce' );
		if ( empty( $wpnonce_value ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wpnonce_value ) ), '_epkb_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		}

		// send email only if feedback is provided
		$feedback = EPKB_Utilities::post( 'epkb_deactivate_feedback' );
		if ( empty( $feedback ) ) {
			return;
		}

		// retrieve current user
		$user = EPKB_Utilities::get_current_user();
		$first_name = empty( $user ) ? 'Unknown' : ( empty( $user->user_firstname ) ? $user->display_name : $user->user_firstname );
		$contact_email = empty( $user ) ? 'N/A' : $user->user_email;

		// send feedback
		$api_params = array(
			'epkb_action'       => 'epkb_process_user_feedback',
			'feedback_type'     => get_transient( '_epkb_plugin_activated' ) ? 'Recently activated' : 'Some time ago',
			'feedback_input'    => $feedback,
			'plugin_name'       => 'KB',
			'plugin_version'    => class_exists('Echo_Knowledge_Base') ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version'     => '',
			'wp_version'        => '',
			'theme_info'        => '',
			'contact_user'      => $contact_email . ' - ' . $first_name,
			'first_name'        => $first_name,
		);

		// Call the API
		wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,
				'sslverify' => false
			)
		);

		wp_send_json_success();
	}

	private function get_deactivate_reasons( $type ) {

		switch ( $type ) {
		   case 1:
		   	    $deactivate_reasons = [
			        'missing_feature'                => [
				        'title'             => esc_html__( 'I cannot find a feature', 'echo-knowledge-base' ),
				        'icon'              => 'epkbfa epkbfa-puzzle-piece',
				        'input_placeholder' => esc_html__( 'Please tell us what is missing', 'echo-knowledge-base' ),
				        'contact_email'     => [
                            'title'    => esc_html__( 'Let us help you find the feature. Please provide your contact email:', 'echo-knowledge-base' ),
                            'required' => false,
                        ],
			        ],
			        'couldnt_get_the_plugin_to_work' => [
				        'title'             => esc_html__( 'I couldn\'t get the plugin to work', 'echo-knowledge-base' ),
				        'icon'              => 'epkbfa epkbfa-question-circle-o',
				        'input_placeholder' => esc_html__( 'Please share the reason', 'echo-knowledge-base' ),
				        'contact_email'     => [
					        'title'    => esc_html__( 'Sorry to hear that. Let us help you. Please provide your contact email:', 'echo-knowledge-base' ),
					        'required' => false,
				        ],
			        ],
			        'bug_issue'                      => [
				        'title'             => esc_html__( 'Bug Issue', 'echo-knowledge-base' ),
				        'icon'              => 'epkbfa epkbfa-bug',
				        'input_placeholder' => esc_html__( 'Please describe the bug', 'echo-knowledge-base' ),
				        'contact_email'     => [
					        'title'    => esc_html__( 'We can fix the bug right away. Please provide your contact email:', 'echo-knowledge-base' ),
					        'required' => true,
				        ]
			        ],
			        'other'                          => [
				        'title'             => esc_html__( 'Other', 'echo-knowledge-base' ),
				        'icon'              => 'epkbfa epkbfa-ellipsis-h',
				        'input_placeholder' => esc_html__( 'Please share the reason', 'echo-knowledge-base' ),
				        'contact_email'     => [
					        'title'    => esc_html__( 'Can we talk to you about reason for removing the plugin?', 'echo-knowledge-base' ),
					        'required' => false,
				        ]
			        ],
			   ];
			   break;
		    case 2:
			default:
				$deactivate_reasons = [
					'no_longer_needed' => [
						'title'             => esc_html__( 'I no longer need the plugin', 'echo-knowledge-base' ),
						'icon'              => 'epkbfa epkbfa-question-circle-o',
						'custom_content'    => esc_html__( 'Thanks for using our products and have a great week', 'echo-knowledge-base' ) . '!',
						'input_placeholder' => '',
					],
					'missing_feature'  => [
						'title'             => esc_html__( 'I cannot find a feature', 'echo-knowledge-base' ),
						'icon'              => 'epkbfa epkbfa-puzzle-piece',
						'input_placeholder' => esc_html__( 'Please tell us what is missing', 'echo-knowledge-base' ),
						'contact_email'     => [
							'title'    => esc_html__( 'Let us help you find the feature. Please provide your contact email:', 'echo-knowledge-base' ),
							'required' => false,
						],
					],
					'bug_issue'                      => [
						'title'             => esc_html__( 'Bug Issue', 'echo-knowledge-base' ),
						'icon'              => 'epkbfa epkbfa-bug',
						'input_placeholder' => esc_html__( 'Please describe the bug', 'echo-knowledge-base' ),
						'contact_email'     => [
							'title'    => esc_html__( 'We can fix the bug right away. Please provide your contact email:', 'echo-knowledge-base' ),
							'required' => true,
						]
					],
					'other'            => [
						'title'             => esc_html__( 'Other', 'echo-knowledge-base' ),
						'icon'              => 'epkbfa epkbfa-ellipsis-h',
						'input_placeholder' => esc_html__( 'Please share the reason', 'echo-knowledge-base' ),
						'contact_email'     => [
							'title'    => esc_html__( 'Can we talk to you about reason to remove the plugin?', 'echo-knowledge-base' ),
							'required' => false,
						]
					]
			   ];
			   break;
	   }

		return $deactivate_reasons;
	}
}
