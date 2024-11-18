<?php

/**
 * Handle user submission from AI Help Sidebar
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_AI_Help_Sidebar_Ctrl {

	public function __construct() {
		add_action( 'wp_ajax_epkb_ai_request', array( $this, 'ai_request' ) );
		add_action( 'wp_ajax_nopriv_epkb_ai_request', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_ai_feedback', [ $this, 'ai_feedback' ] );
	}

	/**
	 * Handle all AI requests and run needed functions
	 */
	public function ai_request() {

		// verify nonce
		EPKB_Utilities::ajax_verify_nonce_and_capability_or_error_die( EPKB_Admin_UI_Access::get_contributor_capability() );

        // will die inside functions
		$ai_action = EPKB_Utilities::post( 'ai_action' );
		switch( $ai_action )  {
			case 'epkb_ai_improve_readability':
				$this->improve_readability();
				break;
			case 'epkb_airephrase':
				$this->rephrase();
				break;
			case 'epkb_ai_fix_spelling_and_grammar':
				$this->fix_spelling_and_grammar();
				break;
			case 'epkb_ai_generate_outline':
				$this->generate_outline();
				break;
			case 'epkb_ai_chat':
				$this->ai_chat();
				break;
			case 'epkb_ai_dismiss_main_intro':
				$this->dismiss_main_intro();
				break;
			case 'epkb_ai_save_settings':
				$this->save_settings();
				break;
			default:
				self::ajax_show_error_die( esc_html__( 'Unknown AI request', 'echo-knowledge-base' ) );
		}
	}

	/**
	 * Improve Readability
	 */
	private function improve_readability() {

		$input_text = EPKB_Utilities::post( 'input_text', '', 'wp_editor' );
		//$input_text = '"' . str_replace('"', "'", trim($input_text, '"')) . '"';

		$openai_handler = new EPKB_OpenAI();
		$fixed_input_text = $openai_handler->chat_complete(
			[
				[
					'role'      => 'user',
					'content'   => 'Improve the text in triple curly brackets to be easier to read or return the same text in triple curly brackets; return fixed text in triple curly brackets:' . '{{{' . $input_text . '}}}'
				]
			],
			0
		);

		// let user see detailed error message only if it was returned by OpenAI response so that user knows what is happening
		if ( ! empty( $openai_handler->error_message ) ) {
			if ( $openai_handler->is_openai_error ) {
				self::ajax_show_error_die( $openai_handler->error_message );
			} else {
				self::ajax_show_error_die(  esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (3355)' );
			}
		}

		preg_match_all( '/{{{(.*?)}}}/s', $fixed_input_text, $matches );
		$fixed_input_text = empty( $matches ) || count( $matches ) < 2 || empty( $matches[1] ) ? $input_text : implode( '', $matches[1] );

		if ( $fixed_input_text == $input_text ) {
			wp_die( wp_json_encode( array(
				'status'            => 'success',
				'message'           => esc_html__( 'No text change required.', 'echo-knowledge-base' ),
				'tokens_used'       => $openai_handler->tokens_used,
			) ) );
		}

		wp_die( wp_json_encode( array(
			'status'            => 'success',
			'message'           => esc_html__( 'AI Task Completed.', 'echo-knowledge-base' ),
			'fixed_input_text'  => $fixed_input_text,
			'tokens_used'       => $openai_handler->tokens_used,
		) ) );
	}

	/**
	 * Re-phrase
	 */
	private function rephrase() {
		$input_text = EPKB_Utilities::post( 'input_text', '', 'wp_editor' );
		//$input_text = '"' . str_replace('"', "'", trim($input_text, '"')) . '"';

		$openai_handler = new EPKB_OpenAI();
		$fixed_input_text = $openai_handler->chat_complete(
			[
				[
					'role'      => 'user',
					'content'   => 'Rephrase the text in triple curly brackets or return the same text in triple curly brackets; return fixed text in triple curly brackets:' . '{{{' . $input_text . '}}}'
				]
			],
			0
		);

		// let user see detailed error message only if it was returned by OpenAI response so that user knows what is happening
		if ( ! empty( $openai_handler->error_message ) ) {
			if ( $openai_handler->is_openai_error ) {
				self::ajax_show_error_die( $openai_handler->error_message );
			} else {
				self::ajax_show_error_die(  esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (2964)' );
			}
		}

		preg_match_all( '/{{{(.*?)}}}/s', $fixed_input_text, $matches );
		$fixed_input_text = empty( $matches ) || count( $matches ) < 2 || empty( $matches[1] ) ? $input_text : implode( '', $matches[1] );

		if ( $fixed_input_text == $input_text ) {
			wp_die( wp_json_encode( array(
				'status'            => 'success',
				'message'           => esc_html__( 'No text change required.', 'echo-knowledge-base' ),
				'tokens_used'       => $openai_handler->tokens_used,
			) ) );
		}

		wp_die( wp_json_encode( array(
			'status'            => 'success',
			'message'           => esc_html__( 'AI Task Completed.', 'echo-knowledge-base' ),
			'fixed_input_text'  => $fixed_input_text,
			'tokens_used'       => $openai_handler->tokens_used,
		) ) );
	}

	/**
	 * Fix Spelling and Grammar
	 */
	private function fix_spelling_and_grammar() {

		$input_text = EPKB_Utilities::post( 'input_text', '', 'wp_editor' );
        //$input_text = '"' . str_replace('"', "'", trim($input_text, '"')) . '"';

		$openai_handler = new EPKB_OpenAI();
		$fixed_input_text = $openai_handler->chat_complete(
			[
				[
					'role'      => 'user',
					'content'   => 'Fix spelling and grammar of the text in triple curly brackets or return the same text in triple curly brackets; return fixed text in triple curly brackets:' . '{{{' . $input_text . '}}}'
				]
			],
			0
		);

		// let user see detailed error message only if it was returned by OpenAI response so that user knows what is happening
		if ( ! empty( $openai_handler->error_message ) ) {
			if ( $openai_handler->is_openai_error ) {
				self::ajax_show_error_die( $openai_handler->error_message );
			} else {
				self::ajax_show_error_die(  esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (2934)' );
			}
		}

		preg_match_all( '/{{{(.*?)}}}/s', $fixed_input_text, $matches );
		$fixed_input_text = empty( $matches ) || count( $matches ) < 2 || empty( $matches[1] ) ? $input_text : implode( '', $matches[1] );

		if ( $fixed_input_text == $input_text ) {
			wp_die( wp_json_encode( array(
				'status'            => 'success',
				'message'           => esc_html__( 'No text change required.', 'echo-knowledge-base' ),
				'tokens_used'       => $openai_handler->tokens_used,
			) ) );
		}

		wp_die( wp_json_encode( array(
			'status'            => 'success',
			'message'           => esc_html__( 'AI Task Completed.', 'echo-knowledge-base' ),
			'fixed_input_text'  => $fixed_input_text,
			'tokens_used'       => $openai_handler->tokens_used,
		) ) );
	}

	/**
	 * Generate Outline
	 */
	private function generate_outline() {
		$input_text = EPKB_Utilities::post( 'input_text', '', 'text-area' );

		$openai_handler = new EPKB_OpenAI();
		$outline = $openai_handler->chat_complete(
			[
				[
					'role'      => 'user',
					'content'   => 'Create an outline for article that has this title: ' . $input_text
				]
			],
			0.5
		);

		// let user see error message from OpenAI API response
		if ( ! empty( $openai_handler->error_message ) ) {
			self::ajax_show_error_die( $openai_handler->error_message );
		}

		wp_die( wp_json_encode( array(
			'status'            => 'success',
			'message'           => esc_html__( 'Outline generated', 'echo-knowledge-base' ),
			'result'            => $outline,
			'tokens_used'       => $openai_handler->tokens_used,
		) ) );
	}

	/**
	 * Save Settings
	 */
	private function save_settings() {

		// API key
		$openai_api_key = EPKB_Utilities::post( 'openai_api_key' );

		if ( empty( $openai_api_key ) || strpos( $openai_api_key, '...' ) === false ) {
			$result = EPKB_OpenAI::save_openai_api_key( $openai_api_key );
			if ( is_wp_error( $result ) ) {
				self::ajax_show_error_die($result->get_error_message());
			}
		}

		$disable_openai = EPKB_Utilities::post( 'disable_openai', 'on' ) == 'on';

		$result = EPKB_Core_Utilities::update_kb_flag( 'disable_openai', $disable_openai );
		if ( is_wp_error( $result ) ) {
			self::ajax_show_error_die( $result->get_error_message() );
		}

		wp_die( wp_json_encode( array(
			'status'    => 'success',
			'message'   => esc_html__( 'Settings saved', 'echo-knowledge-base' ),
		) ) );
	}

	/**
	 * AI Chat
	 */
	private function ai_chat() {
		$input_text = EPKB_Utilities::post( 'input_text', '', 'text-area' );

		$openai_handler = new EPKB_OpenAI();
		$chat_answer = $openai_handler->chat_complete(
			[
				[
					'role'      => 'user',
					'content'   => $input_text
				]
			],
			0.5
		);

		// let user see error message from OpenAI API response
		if ( ! empty( $openai_handler->error_message ) ) {
			self::ajax_show_error_die( $openai_handler->error_message );
		}

		wp_die( wp_json_encode( array(
			'status'            => 'success',
			'message'           => 'success',
			'result'            => $chat_answer,
			'tokens_used'       => $openai_handler->tokens_used,
		) ) );
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 * Use this method for AI Help Sidebar messages as they have custom design
	 *
	 * @param $message
	 * @param string $title
	 * @param string $error_code
	 */
	private static function ajax_show_error_die( $message, $title = '', $error_code = '' ) {
		if ( defined('DOING_AJAX') ) {
			wp_die( wp_json_encode( array( 'error' => true, 'message' => self::notification_box_bottom( $message, $title, 'error' ), 'error_code' => $error_code ) ) );
		}
	}

	/**
	 * Show info or error message to the user
	 * Use this method for AI Help Sidebar messages as they have custom design
	 *
	 * @param $message
	 * @param string $title
	 * @param string $type
	 * @return string
	 */
	private static function notification_box_bottom( $message, $title='', $type='success' ) {

		$message = empty( $message ) ? '' : $message;

		return
			"<div class='eckb-bottom-notice-message'>
				<span class='eckb-bottom-notice-message-icon " . ( $type == 'success' ? 'ep_font_icon_checkmark' : 'epkbfa epkbfa-times-circle' ) . "'></span>
				<div class='contents'>
					<span class='" . esc_attr( $type ) . "'>" .
						( empty( $title ) ? '' : '<h4>' . esc_html( $title ) . '</h4>' ) . "
						<p> " . wp_kses_post( $message ) . "</p>
					</span>
				</div>
			</div>";
	}

	/**
	 * Dismiss main intro
	 */
	private function dismiss_main_intro() {

		EPKB_Core_Utilities::add_kb_flag( 'ai_dismiss_main_intro' );

		wp_die( wp_json_encode( array(
			'status'    => 'success',
			'message'   => 'success',
		) ) );
	}

	/**
	 * Handle feedback form submit
	 */
	function ai_feedback() {

		EPKB_Utilities::ajax_verify_nonce_and_capability_or_error_die();

		$reason_type ='KB AI FEEDBACK';
		$reason_input = EPKB_Utilities::post( 'feedback_text', '', 'text-area' );

		if ( empty( $reason_input ) ) {
			self::ajax_show_error_die( esc_html__( 'Please enter your feedback', 'echo-knowledge-base' ) );
		}

		// retrieve email
		$contact_email = EPKB_Utilities::post( 'feedback_email', '', 'email' );
		$contact_email = is_email( $contact_email ) ? $contact_email : 'feedback@ai.com';
		$contact_user = EPKB_Utilities::post( 'feedback_name' );

		if ( empty( $contact_user ) ) {
			$user = EPKB_Utilities::get_current_user();
			$contact_user = $user->first_name;
		}

		// send feedback
		$api_params = array(
			'epkb_action'       => 'epkb_process_user_feedback',
			'feedback_type'     => $reason_type,
			'feedback_input'    => $reason_input,
			'plugin_name'       => 'KB',
			'plugin_version'    => class_exists('Echo_Knowledge_Base') ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version'     => '',
			'contact_user'      => $contact_email . ' - ' . $contact_user
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

		wp_die( wp_json_encode( array(
			'status'            => 'success',
			'message'           => esc_html__( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ),
		) ) );
	}
}
