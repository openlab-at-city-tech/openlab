<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * OpenAI utility class
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_OpenAI {

	/**
	 * API URL base: the base URL part for API end-points.
	 */
	const API_V1_URL = 'https://api.openai.com/v1';

	/**
	 * OPTIONAL API parameter: The maximum number of tokens to generate in the completion.
	 * The token count of your prompt plus max_tokens cannot exceed the model's context length.
	 * Most models have a context length of 2048 tokens (except for the newest models, which support 4096).
	 * For more details see the documentation at https://platform.openai.com/docs/api-reference/completions/create#completions/create-max_tokens
	 *
	 * Default: 16
	 * @var int
	 */
	// private $max_tokens;

	/**
	 * OPTIONAL API parameter: What sampling temperature to use.
	 * Higher values means the model will take more risks.
	 * Try 0.9 for more creative applications, and 0 (argmax sampling) for ones with a well-defined answer.
	 * We generally recommend altering this or top_p but not both.
	 * For more details see the documentation at https://platform.openai.com/docs/api-reference/completions/create#completions/create-temperature
	 *
	 * Default: 1
	 * @var float
	 */
	// private $temperature;

	/**
	 * OPTIONAL API parameter: How many completions to generate for each prompt.
	 * Note: Because this parameter generates many completions, it can quickly consume your token quota.
	 * Use carefully and ensure that you have reasonable settings for max_tokens and stop.
	 * For more details see the documentation at https://platform.openai.com/docs/api-reference/completions/create#completions/create-n
	 *
	 * Default: 1
	 * @var int
	 */
	// private $n;

	/**
	 * REQUIRED API parameter: The instruction that tells the model how to edit the prompt.
	 *
	 * @var string
	 */
	// private $instruction;

	/**
	 * Text message containing error description returned from API.
	 * Empty string on success API response.
	 *
	 * @var string
	 */
	public $error_message = '';

	/**
	 * Indicates whether the current error is OpenAI error
	 *
	 * @var bool
	 */
	public $is_openai_error = false;

	/**
	 * Number of tokens used for latest API request
	 *
	 * @var string
	 */
	public $tokens_used = 0;

	/**
	 * Create completion
	 * More Info: https://beta.openai.com/docs/api-reference/completions
	 *
	 * @param $prompt
	 * @param $temperature
	 * @return string
	 */
	public function complete( $prompt, $temperature ) {

		$choices = $this->make_api_request( self::API_V1_URL . '/completions', array(
			'model'         => 'text-davinci-003',
			'prompt'        => trim( $prompt ),
			'max_tokens'    => 2048, //(int)$this->global_config['openai_max_tokens'],
			'temperature'   => $temperature,
			'n'             => 1,
		) );

		return count( $choices ) > 0 ? trim( $choices[0]['text'] ) : '';
	}

	/**
	 * Create chat completion
	 * More Info: https://platform.openai.com/docs/api-reference/chat
	 *
	 * @param $messages
	 * @param $temperature
	 * @return string
	 */
	public function chat_complete( $messages, $temperature ) {

		$choices = $this->make_api_request( self::API_V1_URL . '/chat/completions', array(
			'model'         => 'gpt-3.5-turbo',
			'messages'      => $messages,
			'max_tokens'    => 2048, // NOTE: it supports 4096 max, but the limit is exceeded for some reason when we use it; (int)$this->global_config['openai_max_tokens'], // max 4096 supported by 'gpt-3.5-turbo' model
			'temperature'   => $temperature,
			'n'             => 1,
		) );

		return count( $choices ) > 0 ? trim( $choices[0]['message']['content'] ) : '';
	}

	/**
	 * Edit input text
	 * More Info: https://platform.openai.com/docs/api-reference/edits
	 *
	 * @param $prompt
	 * @param $input
	 * @param $temperature
	 * @return string
	 */
	public function edit( $prompt, $input, $temperature ) {

		$choices = $this->make_api_request( self::API_V1_URL . '/edits', array(
			'model'         => 'text-davinci-003',
			'instruction'   => trim( $prompt ),
			'input'         => trim( $input ),
			'temperature'   => $temperature,
			'n'             => 1,
		) );

		return count( $choices ) > 0 ? trim( $choices[0]['text'] ) : '';
	}

	/**
	 * Make API request
	 *
	 * @param $url
	 * @param $args
	 * @return array
	 */
	private function make_api_request( $url, $args ) {

		// reset error message
		$this->error_message = '';
		$this->is_openai_error = false;

		// validate API key
		$api_key = self::get_openai_api_key();
		if ( is_wp_error( $api_key ) ) {
			$error_message = $api_key->get_error_message();
			EPKB_Logging::add_log( 'Cannot retrieve API key. Error details: ' . $error_message );
			$this->error_message = EPKB_Utilities::report_generic_error( 602, $error_message );
			return [];
		} else if ( empty( $api_key ) || ! is_string( $api_key ) ) {
			$openai_settings_capability = EPKB_Admin_UI_Access::get_admin_capability();
			if ( current_user_can( $openai_settings_capability ) ) {
				$this->error_message = sprintf( esc_html__( 'Please enter your OpenAI API key in the %s AI Settings %s', 'echo-knowledge-base' ),  '<a href="#" class="epkb-ai-help-sidebar__open-settings-tab-btn">', '</a>' );
			} else {
				$this->error_message = esc_html__( 'You have no API key.', 'echo-knowledge-base' );
			}
			return [];
		}

		$http_result = wp_remote_post(
			$url,
			array(
				'headers'   => array(
					'Content-Type' => 'application/json',
					'Authorization' => 'Bearer ' . sanitize_text_field( $api_key ),
				),
				'body'      => wp_json_encode( $this->ai_sanitize_args( $args ) ),
				'timeout'   => 120, // some of OpenAI API requests can take up to a few minutes
			)
		);

		// check for WP error
		if ( is_wp_error( $http_result ) ) {
			$error_message = $http_result->get_error_message();
			EPKB_Logging::add_log( 'WP error on OpenAI API response. Error details: ' . $error_message );
			$this->error_message = EPKB_Utilities::report_generic_error( 602, $error_message );
			return [];
		}

		if ( empty( $http_result['body'] ) ) {
			EPKB_Logging::add_log( 'Empty body on OpenAI API response.' );
			$this->error_message = EPKB_Utilities::report_generic_error( 603 );
			return [];
		}

		// retrieve API response
		$api_result = json_decode( $http_result['body'], true );

		// validate decoded JSON
		if ( empty( $api_result ) ) {
			EPKB_Logging::add_log( 'Unable to decode JSON from OpenAI API response.' );
			$this->error_message = EPKB_Utilities::report_generic_error( 604 );
			return [];
		}

		// validate HTTP code - for detailed description about each response code look to https://beta.openai.com/docs/guides/error-codes/api-errors
		if ( $http_result['response']['code'] != 200 ) {
			EPKB_Logging::add_log( 'OpenAI API request failed. HTTP response code: ' . $http_result['response']['code'] . '. API error message: ' . $api_result['error']['message'] );
			$this->error_message = esc_html__( 'OpenAI reported the following error' , 'echo-knowledge-base' ) . ': ' . $api_result['error']['message'];
			$this->is_openai_error = true;
			return [];
		}

		// save tokens usage
		$this->tokens_used = $api_result['usage']['total_tokens'];

		return is_array( $api_result['choices'] ) ? $api_result['choices'] : [];
	}

	/**
	* Sanitize AI call arguments
	*
	* @param $args
	* @return array
	*/
	private function ai_sanitize_args( $args = [] ) {
		if ( empty( $args ) ) {
			return $args;
		}

		foreach ($args as $key => $arg) {
			if ( is_array( $arg ) ) {
				$args[$key] = $this->ai_sanitize_args( $arg );
				continue;
			}

			switch ( $key ) {
				case 'n':
				case 'max_tokens':
					$args[$key] = absint( $arg );
					break;
				case 'temperature':
					$args[$key] = floatval( $arg );
					break;
				case 'content':
					$args[$key] = sanitize_textarea_field( $arg );
					break;
				case 'model':
				case 'role':
					default:
					$args[$key] = sanitize_text_field( $arg );
					break;
			}
		}

		return $args;
	}

	public static function get_openai_api_key() {

		$old_api_key = EPKB_Utilities::get_wp_option( 'epkb_openai_api_key', '', false, true );
		$new_api_key = EPKB_Utilities::get_wp_option( 'epkb_openai_key', '', false, true );

		if ( ! is_wp_error( $new_api_key ) && ! empty( $new_api_key ) ) {
			$api_key = EPKB_Utilities::decrypt_data( $new_api_key );
			$api_key = $api_key ?: '';

		} else if ( ! is_wp_error( $old_api_key ) && ! empty( $old_api_key ) ) {
			$api_key = $old_api_key;
			$result = self::save_openai_api_key( $api_key );
			if ( ! is_wp_error( $result ) ) {
				delete_option( 'epkb_openai_api_key' );
			}

		} else {
			$api_key = '';
		}

		return $api_key;
	}

	public static function save_openai_api_key( $openai_api_key ) {

		$api_key = EPKB_Utilities::encrypt_data( $openai_api_key );
		$result = EPKB_Utilities::save_wp_option( 'epkb_openai_key', $api_key );

		return $result;
	}
}
