<?php /** @noinspection PhpUndefinedMethodInspection */

/**
 * AI Provider helper
 *
 * Centralizes provider selection and provider-specific utilities
 * so features can switch between ChatGPT and Gemini without duplicating logic.
 */
class EPKB_AI_Provider {

	const PROVIDER_CHATGPT = 'chatgpt';	// do not modify
	const PROVIDER_GEMINI = 'gemini';	// do not modify
	const FASTEST_PRESET = 'fastest';
	const BALANCED_PRESET = 'balanced';
	const SMARTEST_PRESET = 'smartest';

	private static $cached_active_provider = null;

	/**
	 * List of supported providers.
	 *
	 * @return array
	 */
	public static function get_supported_providers() {
		return array( self::PROVIDER_CHATGPT, self::PROVIDER_GEMINI );
	}

	/**
	 * Reset the cached active provider so the next lookup re-reads config.
	 */
	public static function clear_cache() {
		self::$cached_active_provider = null;
	}

	/**
	 * Normalize provider slug
	 *
	 * @param string $provider
	 * @return string
	 */
	public static function normalize_provider( $provider ) {

		$provider = strtolower( trim( (string) $provider ) );
		// TODO AI PRO LEGACY: Remove after old AI PRO and stored configs no longer use the legacy "openai" provider slug.
		if ( $provider === 'openai' ) {
			return self::PROVIDER_CHATGPT;
		}

		return in_array( $provider, self::get_supported_providers(), true ) ? $provider : self::PROVIDER_GEMINI;
	}

	/**
	 * Get active provider using current configuration
	 *
	 * @param array|null $config Optional config array to avoid recursive lookups
	 * @return string
	 */
	public static function get_active_provider( $config = null ) {

		if ( $config !== null ) {
			return self::normalize_provider( isset( $config['ai_provider'] ) ? $config['ai_provider'] : '' );
		}

		if ( self::$cached_active_provider !== null ) {
			return self::$cached_active_provider;
		}

		$config = EPKB_AI_Config_Specs::get_ai_config();
		self::$cached_active_provider = self::normalize_provider( isset( $config['ai_provider'] ) ? $config['ai_provider'] : '' );

		return self::$cached_active_provider;
	}

	/**
	 * Human readable label for provider
	 *
	 * @param string|null $provider
	 * @return string
	 */
	public static function get_provider_label( $provider = null ) {
		$provider = $provider ?: self::get_active_provider();

		return $provider === self::PROVIDER_GEMINI ? 'Google Gemini' : 'OpenAI ChatGPT';
	}

	/**
	 * Get user-facing provider options for select fields.
	 *
	 * @return array
	 */
	public static function get_provider_options() {
		return array(
			self::PROVIDER_GEMINI => self::get_provider_label( self::PROVIDER_GEMINI ),
			self::PROVIDER_CHATGPT => self::get_provider_label( self::PROVIDER_CHATGPT ),
		);
	}

	/**
	 * Get AI client for provider
	 *
	 * @param string|null $provider
	 * @return EPKB_ChatGPT_Client|EPKB_Gemini_Client
	 */
	public static function get_client( $provider = null ) {
		$provider = $provider ?: self::get_active_provider();

		return $provider === self::PROVIDER_GEMINI ? new EPKB_Gemini_Client() : new EPKB_ChatGPT_Client();
	}

	/**
	 * Get vector store handler for provider
	 *
	 * @param string|null $provider
	 * @return EPKB_AI_ChatGPT_Vector_Store|EPKB_AI_Gemini_Vector_Store
	 */
	public static function get_vector_store_handler( $provider = null ) {
		$provider = $provider ?: self::get_active_provider();

		return $provider === self::PROVIDER_GEMINI ? new EPKB_AI_Gemini_Vector_Store() : new EPKB_AI_ChatGPT_Vector_Store();
	}

	/**
	 * Get model catalog class for provider.
	 *
	 * @param string|null $provider
	 * @return string
	 */
	private static function get_catalog_class( $provider = null ) {
		$provider = $provider ? self::normalize_provider( $provider ) : self::get_active_provider();
		return $provider === self::PROVIDER_GEMINI ? 'EPKB_Gemini_Model_Catalog' : 'EPKB_ChatGPT_Model_Catalog';
	}

	/**
	 * Get models and defaults for provider
	 *
	 * @param string|null $model_name
	 * @param string|null $provider
	 * @return array
	 */
	public static function get_models_and_default_params( $model_name = null, $provider = null ) {
		$catalog_class = self::get_catalog_class( $provider );
		return $catalog_class::get_models( $model_name );
	}

	/**
	 * Resolve a provider model name, including legacy aliases.
	 *
	 * @param string $model
	 * @param string|null $provider
	 * @return string
	 */
	public static function resolve_model_name( $model, $provider = null ) {
		$catalog_class = self::get_catalog_class( $provider );
		return $catalog_class::resolve_model_name( $model );
	}

	/**
	 * Get user-facing preset definitions.
	 *
	 * @return array
	 */
	private static function get_preset_definitions() {
		return array(
			self::FASTEST_PRESET   => array(
				'label'       => __( 'Fastest', 'echo-knowledge-base' ),
				'description' => __( 'Fastest responses for lightweight questions.', 'echo-knowledge-base' ),
			),
			self::BALANCED_PRESET => array(
				'label'       => __( 'Balanced', 'echo-knowledge-base' ),
				'description' => __( 'Best default for most sites.', 'echo-knowledge-base' ),
			),
			self::SMARTEST_PRESET => array(
				'label'       => __( 'Smartest', 'echo-knowledge-base' ),
				'description' => __( 'Smarter than Balanced for harder questions while keeping response times practical.', 'echo-knowledge-base' ),
			),
		);
	}

	/**
	 * Get the stored preset field name for a feature.
	 *
	 * @param string $use_case
	 * @return string
	 */
	private static function get_preset_field_name( $use_case ) {
		if ( $use_case === 'chat' ) {
			return 'ai_chat_preset';
		}

		if ( $use_case === 'search' ) {
			return 'ai_search_preset';
		}

		return '';
	}

	/**
	 * Get the default preset for one use case.
	 *
	 * @param string $use_case
	 * @return string
	 */
	private static function get_default_preset_for_use_case( $use_case ) {

		if ( $use_case === 'content_analysis' ) {
			return self::FASTEST_PRESET;
		}

		return self::BALANCED_PRESET;
	}

	/**
	 * Get runtime preset profiles for one provider.
	 *
	 * Preset profiles decouple user-facing behavior from provider model IDs so
	 * multiple presets can share the same underlying model with different defaults.
	 *
	 * @param string $provider
	 * @return array
	 */
	private static function get_preset_profiles( $provider ) {

		$provider = self::normalize_provider( $provider );

		if ( $provider === self::PROVIDER_CHATGPT ) {
			return array(
				self::FASTEST_PRESET => array(
					'model' => 'gpt-5.4-mini',
				),
				self::BALANCED_PRESET => array(
					'model' => 'gpt-5.4',
				),
				self::SMARTEST_PRESET => array(
					'model'  => 'gpt-5.4',
					'params' => array(
						'reasoning'         => 'high',
						'verbosity'         => 'medium',
						'max_output_tokens' => 3500,
					),
				),
			);
		}

		return array(
			self::FASTEST_PRESET => array(
				'model' => 'gemini-2.5-flash',
			),
			self::BALANCED_PRESET => array(
				'model' => 'gemini-3-flash-preview',
			),
			self::SMARTEST_PRESET => array(
				'model'  => 'gemini-3-flash-preview',
				'params' => array(
					'thinking_level'    => 'high',
					'max_output_tokens' => 4096,
				),
			),
		);
	}

	/**
	 * Map legacy stored model IDs to stable preset behavior.
	 *
	 * This preserves the intent of old saved "pro" selections without keeping
	 * the retired model definitions active in the runtime catalog.
	 *
	 * @param string $provider
	 * @return array
	 */
	private static function get_legacy_model_preset_map( $provider ) {

		$provider = self::normalize_provider( $provider );

		if ( $provider === self::PROVIDER_CHATGPT ) {
			return array(
				'gpt-5.2-pro' => self::SMARTEST_PRESET,
				'gpt-5.4-pro' => self::SMARTEST_PRESET,
			);
		}

		return array(
			'gemini-2.5-pro'        => self::SMARTEST_PRESET,
			'gemini-3-pro-preview'  => self::SMARTEST_PRESET,
			'gemini-3.1-pro'        => self::SMARTEST_PRESET,
			'gemini-3.1-pro-preview' => self::SMARTEST_PRESET,
		);
	}

	/**
	 * Normalize a stored preset key to one of the supported values.
	 *
	 * @param string $preset_key
	 * @return string
	 */
	public static function normalize_preset_key( $preset_key ) {

		$preset_key = strtolower( trim( (string) $preset_key ) );
		if ( $preset_key === '' || $preset_key === 'custom' ) {
			return self::BALANCED_PRESET;
		}

		$preset_definitions = self::get_preset_definitions();
		return isset( $preset_definitions[ $preset_key ] ) ? $preset_key : self::BALANCED_PRESET;
	}

	/**
	 * Get the active preset for a feature.
	 *
	 * @param string $use_case
	 * @param array|null $config
	 * @param string|null $provider
	 * @return string
	 */
	public static function get_feature_preset( $use_case = 'chat', $config = null, $provider = null ) {

		if ( $config === null ) {
			$config = EPKB_AI_Config_Specs::get_ai_config();
		}

		$provider = $provider
			? self::normalize_provider( $provider )
			: ( ! empty( $config['ai_provider'] ) ? self::normalize_provider( $config['ai_provider'] ) : self::get_active_provider() );
		$preset_field = self::get_preset_field_name( $use_case );
		if ( $preset_field !== '' && ! empty( $config[ $preset_field ] ) ) {
			return self::normalize_preset_key( $config[ $preset_field ] );
		}

		return self::get_default_preset_for_use_case( $use_case );
	}

	/**
	 * Build the resolved preset-to-model map for one provider.
	 *
	 * @param string $provider
	 * @return array
	 */
	private static function get_runtime_preset_models( $provider ) {

		$provider = self::normalize_provider( $provider );
		$preset_profiles = self::get_preset_profiles( $provider );
		$model_map = array();

		foreach ( $preset_profiles as $preset_key => $preset_profile ) {
			if ( empty( $preset_profile['model'] ) ) {
				continue;
			}

			$model_map[ $preset_key ] = self::resolve_model_name( $preset_profile['model'], $provider );
		}

		if ( empty( $model_map[ self::BALANCED_PRESET ] ) ) {
			$model_map[ self::BALANCED_PRESET ] = self::get_default_model( $provider );
		}

		if ( empty( $model_map[ self::FASTEST_PRESET ] ) ) {
			$model_map[ self::FASTEST_PRESET ] = $model_map[ self::BALANCED_PRESET ];
		}

		if ( empty( $model_map[ self::SMARTEST_PRESET ] ) ) {
			$model_map[ self::SMARTEST_PRESET ] = $model_map[ self::BALANCED_PRESET ];
		}

		return $model_map;
	}

	/**
	 * Apply use-case-specific parameter overrides.
	 *
	 * @param string $use_case
	 * @param array $params
	 * @param array $model_spec
	 * @return array
	 */
	private static function apply_use_case_runtime_overrides( $use_case, $params, $model_spec ) {

		$max_limit = isset( $model_spec['max_output_tokens_limit'] ) ? intval( $model_spec['max_output_tokens_limit'] ) : 16384;

		if ( $use_case === 'quiz' ) {
			if ( ! empty( $model_spec['supports_temperature'] ) ) {
				$params['temperature'] = 0.2;
			} else {
				if ( ! empty( $model_spec['supports_reasoning'] ) ) {
					$params['reasoning'] = 'low';
				}
				if ( ! empty( $model_spec['supports_verbosity'] ) ) {
					$params['verbosity'] = 'low';
				}
			}
			$params['max_output_tokens'] = min( 9000, $max_limit );
		}

		if ( $use_case === 'content_analysis' ) {
			$params['max_output_tokens'] = min( 12000, $max_limit );
		}

		if ( $use_case === 'pdf' ) {
			$params['max_output_tokens'] = $max_limit;
		}

		return $params;
	}

	/**
	 * Build one internal runtime profile.
	 *
	 * @param string $use_case
	 * @param string $provider
	 * @param string|null $preset_key
	 * @return array
	 */
	private static function build_runtime_profile( $use_case, $provider, $preset_key = null ) {

		$provider = self::normalize_provider( $provider );
		$preset_key = self::normalize_preset_key( $preset_key );
		$preset_profiles = self::get_preset_profiles( $provider );
		$model_map = self::get_runtime_preset_models( $provider );
		if ( empty( $model_map[ $preset_key ] ) ) {
			$preset_key = self::BALANCED_PRESET;
		}

		$model = empty( $model_map[ $preset_key ] ) ? self::get_default_model( $provider ) : $model_map[ $preset_key ];
		$preset_profile = empty( $preset_profiles[ $preset_key ] ) ? array() : $preset_profiles[ $preset_key ];
		$model_spec = self::get_models_and_default_params( $model, $provider );
		$params = empty( $model_spec['default_params'] ) ? array() : $model_spec['default_params'];
		$preset_params = empty( $preset_profile['params'] ) || ! is_array( $preset_profile['params'] ) ? array() : $preset_profile['params'];
		$params = array_merge( $params, $preset_params );
		$params = self::apply_use_case_runtime_overrides( $use_case, $params, $model_spec );

		return array(
			'preset' => $preset_key,
			'model'  => $model,
			'params' => $params,
		);
	}

	/**
	 * Get the resolved runtime profile for a feature.
	 *
	 * @param string $use_case
	 * @param string|null $provider
	 * @param array|null $config
	 * @return array
	 */
	public static function get_runtime_profile( $use_case = 'chat', $provider = null, $config = null ) {

		$provider = $provider
			? self::normalize_provider( $provider )
			: ( ! empty( $config['ai_provider'] ) ? self::normalize_provider( $config['ai_provider'] ) : self::get_active_provider() );
		$preset_key = self::get_feature_preset( $use_case, $config, $provider );
		return self::build_runtime_profile( $use_case, $provider, $preset_key );
	}

	/**
	 * Get model presets from internal runtime profiles.
	 *
	 * @param string $use_case
	 * @param string|null $provider
	 * @return array
	 */
	public static function get_model_presets( $use_case = 'chat', $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		$presets = array();

		foreach ( self::get_preset_definitions() as $preset_key => $preset_definition ) {
			$profile = self::build_runtime_profile( $use_case, $provider, $preset_key );
			$preset = array(
				'label'       => $preset_definition['label'],
				'description' => $preset_definition['description'],
				'model'       => $profile['model'],
			);

			foreach ( $profile['params'] as $param => $value ) {
				$preset[ $param ] = $value;
			}

			$presets[ $preset_key ] = $preset;
		}

		return $presets;
	}

	/**
	 * Get preset parameters
	 *
	 * @param string $preset_key
	 * @param string $use_case
	 * @param string|null $provider
	 * @return array
	 */
	public static function get_preset_parameters( $preset_key, $use_case = 'chat', $provider = null ) {
		$presets = self::get_model_presets( $use_case, $provider );
		$preset_key = self::normalize_preset_key( $preset_key );
		return isset( $presets[ $preset_key ] ) ? $presets[ $preset_key ] : reset( $presets );
	}

	/**
	 * Get the preset key for a given model
	 *
	 * Legacy pro models map to Smartest explicitly. Active shared models resolve to
	 * the first matching preset profile, which keeps the balanced model pinned to
	 * Balanced during reverse migration.
	 *
	 * @param string $model Model ID
	 * @param string|null $provider
	 * @return string
	 */
	public static function get_preset_key_for_model( $model, $provider = null ) {

		$model = trim( (string) $model );
		if ( $model === '' ) {
			return self::BALANCED_PRESET;
		}

		$provider = $provider ? self::normalize_provider( $provider ) : self::get_active_provider();
		$legacy_model_preset_map = self::get_legacy_model_preset_map( $provider );
		if ( isset( $legacy_model_preset_map[ $model ] ) ) {
			return $legacy_model_preset_map[ $model ];
		}

		$model = self::resolve_model_name( $model, $provider );
		foreach ( self::get_preset_profiles( $provider ) as $preset_key => $preset_profile ) {
			if ( empty( $preset_profile['model'] ) ) {
				continue;
			}

			if ( self::resolve_model_name( $preset_profile['model'], $provider ) === $model ) {
				return $preset_key;
			}
		}

		return self::BALANCED_PRESET;
	}

	/**
	 * Normalize AI config and migrate legacy stored model values to presets.
	 *
	 * @param array $config
	 * @return array
	 */
	public static function migrate_ai_config( $config ) {

		if ( empty( $config ) || ! is_array( $config ) ) {
			return $config;
		}

		$provider = self::get_active_provider( $config );
		$config['ai_provider'] = $provider;

		if ( empty( $config['ai_chat_preset'] ) ) {
			$provider_model_field = 'ai_' . $provider . '_chat_model';
			$legacy_model = ! empty( $config[ $provider_model_field ] ) ? $config[ $provider_model_field ] : ( ! empty( $config['ai_chat_model'] ) ? $config['ai_chat_model'] : '' );

			if ( $legacy_model !== '' ) {
				$config['ai_chat_preset'] = self::get_preset_key_for_model( $legacy_model, $provider );
			}
		}

		$config['ai_chat_preset'] = self::get_feature_preset( 'chat', $config, $provider );

		if ( empty( $config['ai_search_preset'] ) ) {
			$provider_model_field = 'ai_' . $provider . '_search_model';
			$legacy_model = ! empty( $config[ $provider_model_field ] ) ? $config[ $provider_model_field ] : ( ! empty( $config['ai_search_model'] ) ? $config['ai_search_model'] : '' );

			if ( $legacy_model !== '' ) {
				$config['ai_search_preset'] = self::get_preset_key_for_model( $legacy_model, $provider );
			}
		}

		$config['ai_search_preset'] = self::get_feature_preset( 'search', $config, $provider );

		unset( $config['ai_chat_model'], $config['ai_search_model'] );

		foreach ( self::get_supported_providers() as $supported_provider ) {
			unset( $config[ 'ai_' . $supported_provider . '_chat_model' ], $config[ 'ai_' . $supported_provider . '_search_model' ] );
		}

		return $config;
	}

	/**
	 * Apply model parameters for provider
	 *
	 * @param array $request
	 * @param string $model
	 * @param array $params
	 * @param string|null $provider
	 * @return array
	 */
	public static function apply_model_parameters( $request, $model, $params = array(), $provider = null ) {
		$catalog_class = self::get_catalog_class( $provider );
		return $catalog_class::apply_model_parameters( $request, $model, $params );
	}

	/**
	 * Get default model for provider
	 *
	 * @param string|null $provider
	 * @return string
	 */
	public static function get_default_model( $provider = null ) {
		$catalog_class = self::get_catalog_class( $provider );
		return $catalog_class::get_default_model();
	}

	/**
	 * Send one prompt request for the resolved use-case runtime profile.
	 *
	 * @param string $prompt
	 * @param string $instructions
	 * @param string $purpose
	 * @param string $use_case
	 * @param string|null $provider
	 * @param array $attachment
	 * @param array $model_params
	 * @param array $response_format
	 * @return string|WP_Error
	 */
	public static function send_prompt_request( $prompt, $instructions, $purpose = 'general', $use_case = 'chat', $provider = null, $attachment = array(), $model_params = array(), $response_format = array() ) {

		$provider = $provider ? self::normalize_provider( $provider ) : self::get_active_provider();
		$client = self::get_client( $provider );
		$runtime_profile = self::get_runtime_profile( $use_case, $provider );
		$model = $runtime_profile['model'];
		$model_params = array_merge(
			empty( $runtime_profile['params'] ) ? array() : $runtime_profile['params'],
			is_array( $model_params ) ? $model_params : array()
		);

		$uploaded_attachment = array();
		if ( ! empty( $attachment['file_content'] ) ) {
			$uploaded_attachment = self::upload_prompt_attachment(
				$attachment['file_content'],
				isset( $attachment['file_name'] ) ? $attachment['file_name'] : 'document',
				isset( $attachment['mime_type'] ) ? $attachment['mime_type'] : 'application/octet-stream',
				$provider
			);
			if ( is_wp_error( $uploaded_attachment ) ) {
				return $uploaded_attachment;
			}
		}

		try {
			if ( $provider === self::PROVIDER_GEMINI ) {
				$parts = array( array( 'text' => $prompt ) );
				if ( ! empty( $uploaded_attachment ) ) {
					$parts[] = self::build_prompt_attachment_content( $uploaded_attachment, $provider );
				}

				$request = array(
					'contents' => array(
						array( 'parts' => $parts )
					),
					'system_instruction' => array(
						'parts' => array(
							array( 'text' => $instructions )
						)
					)
				);
				$request = self::apply_model_parameters( $request, $model, $model_params, $provider );
				$response = $client->request( '/models/' . $model . ':generateContent', $request, 'POST', $purpose );
			} else {
				$content = empty( $uploaded_attachment )
					? $prompt
					: array(
						array( 'type' => 'input_text', 'text' => $prompt ),
						self::build_prompt_attachment_content( $uploaded_attachment, $provider ),
					);

				$request = array(
					'model'        => $model,
					'instructions' => $instructions,
					'input'        => array(
						array(
							'role'    => 'user',
							'content' => $content,
						)
					)
				);
				$request = self::apply_model_parameters( $request, $model, $model_params, $provider );
				$request = self::apply_response_format( $request, $response_format, $provider );
				$response = $client->request( '/responses', $request, 'POST', $purpose );
			}
		} finally {
			if ( ! empty( $uploaded_attachment ) ) {
				self::delete_prompt_attachment( $uploaded_attachment, $provider );
			}
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return self::extract_response_content( $response, $provider );
	}

	/**
	 * Apply one optional response format to the provider request.
	 *
	 * Only enforced for ChatGPT (Responses API json_schema). Gemini and Claude ignore the
	 * hint and rely on prompt compliance plus the JSON recovery parser in the utilities layer.
	 *
	 * @param array $request
	 * @param array $response_format
	 * @param string|null $provider
	 * @return array
	 */
	private static function apply_response_format( $request, $response_format, $provider = null ) {
		$provider = $provider ? self::normalize_provider( $provider ) : self::get_active_provider();
		if ( $provider !== self::PROVIDER_CHATGPT || empty( $response_format['type'] ) ) {
			return $request;
		}

		if ( ! isset( $request['text'] ) || ! is_array( $request['text'] ) ) {
			$request['text'] = array();
		}

		if ( $response_format['type'] === 'json_schema' ) {
			if ( empty( $response_format['name'] ) || empty( $response_format['schema'] ) || ! is_array( $response_format['schema'] ) ) {
				return $request;
			}

			$request['text']['format'] = array(
				'type'   => 'json_schema',
				'name'   => preg_replace( '/[^a-zA-Z0-9_-]/', '_', $response_format['name'] ),
				'strict' => ! isset( $response_format['strict'] ) || ! empty( $response_format['strict'] ),
				'schema' => $response_format['schema'],
			);
		}

		return $request;
	}

	/**
	 * Get decrypted API key for provider
	 *
	 * @param string|null $provider Provider (defaults to active)
	 * @return string Decrypted API key or empty string
	 */
	public static function get_api_key( $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		$encrypted = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( $provider );
		if ( empty( $encrypted ) ) {
			return '';
		}
		$decrypted = EPKB_Utilities::decrypt_data( $encrypted );
		return $decrypted !== false ? $decrypted : '';
	}

	/**
	 * Get chat model for provider
	 *
	 * @param string|null $provider
	 * @return string Valid model ID
	 */
	public static function get_chat_model( $provider = null ) {
		$runtime_profile = self::get_runtime_profile( 'chat', $provider );
		return $runtime_profile['model'];
	}

	/**
	 * Get search model for provider
	 *
	 * @param string|null $provider
	 * @return string Valid model ID
	 */
	public static function get_search_model( $provider = null ) {
		$runtime_profile = self::get_runtime_profile( 'search', $provider );
		return $runtime_profile['model'];
	}

	/**
	 * Get the config field name for API key
	 *
	 * @param string|null $provider
	 * @return string
	 */
	public static function get_api_key_field( $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		return $provider === self::PROVIDER_GEMINI ? 'ai_gemini_key' : 'ai_chatgpt_key';
	}

	/**
	 * Get default max output tokens for provider
	 *
	 * @param string|null $provider
	 * @return int
	 */
	public static function get_default_max_output_tokens( $provider = null ) {
		$catalog_class = self::get_catalog_class( $provider );
		return $catalog_class::get_default_max_output_tokens();
	}

	/**
	 * Get default max results for retrieval tools
	 *
	 * @param string|null $provider
	 * @return int
	 */
	public static function get_default_max_results( $provider = null ) {
		$catalog_class = self::get_catalog_class( $provider );
		return $catalog_class::get_default_max_results();
	}

	/**
	 * Get max upload size according to provider
	 *
	 * @param string|null $provider
	 * @return int
	 */
	public static function get_max_file_size( $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		return $provider === self::PROVIDER_GEMINI ? EPKB_Gemini_Client::MAX_FILE_SIZE : EPKB_ChatGPT_Client::MAX_FILE_SIZE;
	}

	/**
	 * Get translated select options for a model parameter.
	 *
	 * @param string $parameter
	 * @param string|null $model
	 * @param string|null $provider
	 * @return array
	 */
	public static function get_parameter_options( $parameter, $model = null, $provider = null ) {

		$parameter_labels = array(
			'verbosity' => array(
				'low'    => __( 'Low', 'echo-knowledge-base' ),
				'medium' => __( 'Medium', 'echo-knowledge-base' ),
				'high'   => __( 'High', 'echo-knowledge-base' ),
			),
			'reasoning' => array(
				'none'   => __( 'None', 'echo-knowledge-base' ),
				'low'    => __( 'Low', 'echo-knowledge-base' ),
				'medium' => __( 'Medium', 'echo-knowledge-base' ),
				'high'   => __( 'High', 'echo-knowledge-base' ),
				'xhigh'  => __( 'Extra High', 'echo-knowledge-base' ),
			),
			'thinking_level' => array(
				'low'  => __( 'Low', 'echo-knowledge-base' ),
				'high' => __( 'High', 'echo-knowledge-base' ),
			),
		);

		if ( empty( $parameter_labels[ $parameter ] ) ) {
			return array();
		}

		$provider = $provider ?: self::get_active_provider();
		$model = $model ? self::resolve_model_name( $model, $provider ) : self::get_default_model( $provider );
		$model_spec = self::get_models_and_default_params( $model, $provider );
		if ( empty( $model_spec['parameters'] ) || ! in_array( $parameter, $model_spec['parameters'], true ) ) {
			return array();
		}
		$option_key = $parameter . '_options';
		$option_values = empty( $model_spec[ $option_key ] ) ? array_keys( $parameter_labels[ $parameter ] ) : $model_spec[ $option_key ];
		$options = array();

		foreach ( $option_values as $option_value ) {
			if ( isset( $parameter_labels[ $parameter ][ $option_value ] ) ) {
				$options[ $option_value ] = $parameter_labels[ $parameter ][ $option_value ];
			}
		}

		return $options;
	}

	/**
	 * Upload a temporary file for direct prompt input.
	 *
	 * @param string $file_content Raw file bytes.
	 * @param string $file_name Original file name.
	 * @param string $mime_type MIME type.
	 * @param string|null $provider Provider override.
	 * @return array|WP_Error Normalized attachment reference.
	 */
	public static function upload_prompt_attachment( $file_content, $file_name, $mime_type, $provider = null ) {

		if ( ! is_string( $file_content ) || $file_content === '' ) {
			return new WP_Error( 'empty_file', __( 'File content is empty.', 'echo-knowledge-base' ) );
		}

		$provider = $provider ? self::normalize_provider( $provider ) : self::get_active_provider();
		$file_name = sanitize_file_name( $file_name );
		$file_name = empty( $file_name ) ? 'document' : $file_name;
		$mime_type = empty( $mime_type ) ? 'application/octet-stream' : $mime_type;

		if ( $provider === self::PROVIDER_GEMINI ) {
			return self::upload_gemini_prompt_attachment( $file_content, $file_name, $mime_type );
		}

		return self::upload_chatgpt_prompt_attachment( $file_content, $file_name, $mime_type );
	}

	/**
	 * Build provider-native request content for an uploaded prompt attachment.
	 *
	 * @param array $attachment Normalized attachment reference.
	 * @param string|null $provider
	 * @return array
	 */
	public static function build_prompt_attachment_content( $attachment, $provider = null ) {

		$provider = $provider ?: ( ! empty( $attachment['provider'] ) ? $attachment['provider'] : self::get_active_provider() );

		if ( $provider === self::PROVIDER_GEMINI ) {
			return array(
				'fileData' => array(
					'mimeType' => $attachment['mime_type'],
					'fileUri'  => $attachment['file_uri'],
				)
			);
		}

		return array(
			'type'    => 'input_file',
			'file_id' => $attachment['file_id'],
		);
	}

	/**
	 * Delete a temporary prompt attachment.
	 *
	 * @param array $attachment Normalized attachment reference.
	 * @param string|null $provider
	 * @return bool
	 */
	public static function delete_prompt_attachment( $attachment, $provider = null ) {

		$provider = $provider ?: ( ! empty( $attachment['provider'] ) ? $attachment['provider'] : self::get_active_provider() );

		if ( $provider === self::PROVIDER_GEMINI ) {
			return self::delete_gemini_prompt_attachment( $attachment );
		}

		return self::delete_chatgpt_prompt_attachment( $attachment );
	}

	/**
	 * Upload a temporary prompt attachment to OpenAI.
	 *
	 * @param string $file_content Raw file bytes.
	 * @param string $file_name Original file name.
	 * @param string $mime_type MIME type.
	 * @return array|WP_Error
	 */
	private static function upload_chatgpt_prompt_attachment( $file_content, $file_name, $mime_type ) {

		$client = new EPKB_ChatGPT_Client();
		$response = $client->request( '/files', array(
			'file_name'         => $file_name,
			'file_content'      => $file_content,
			'file_purpose'      => 'user_data',
			'file_content_type' => $mime_type,
		), 'POST', 'file_storage_upload' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( empty( $response['id'] ) ) {
			return new WP_Error( 'file_upload_failed', __( 'Failed to upload file to AI storage.', 'echo-knowledge-base' ) );
		}

		return array(
			'provider'  => self::PROVIDER_CHATGPT,
			'file_id'   => $response['id'],
			'mime_type' => ! empty( $response['mime_type'] ) ? $response['mime_type'] : $mime_type,
			'file_name' => $file_name,
		);
	}

	/**
	 * Upload a temporary prompt attachment to Gemini.
	 *
	 * @param string $file_content Raw file bytes.
	 * @param string $file_name Original file name.
	 * @param string $mime_type MIME type.
	 * @return array|WP_Error
	 */
	private static function upload_gemini_prompt_attachment( $file_content, $file_name, $mime_type ) {

		$client = new EPKB_Gemini_Client();
		$response = $client->upload_prompt_file( $file_content, array( 'displayName' => $file_name ), $mime_type );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$file = isset( $response['file'] ) && is_array( $response['file'] ) ? $response['file'] : $response;
		$file = self::wait_for_gemini_prompt_attachment( $client, $file );
		if ( is_wp_error( $file ) ) {
			return $file;
		}

		if ( empty( $file['name'] ) || empty( $file['uri'] ) ) {
			return new WP_Error( 'file_upload_failed', __( 'Failed to upload file to AI storage.', 'echo-knowledge-base' ) );
		}

		return array(
			'provider'      => self::PROVIDER_GEMINI,
			'resource_name' => $file['name'],
			'file_uri'      => $file['uri'],
			'mime_type'     => ! empty( $file['mimeType'] ) ? $file['mimeType'] : $mime_type,
			'file_name'     => $file_name,
		);
	}

	/**
	 * Wait until Gemini marks an uploaded file as ready for prompt usage.
	 *
	 * @param EPKB_Gemini_Client $client
	 * @param array $file Uploaded Gemini File resource.
	 * @return array|WP_Error
	 */
	private static function wait_for_gemini_prompt_attachment( $client, $file ) {

		if ( empty( $file['name'] ) ) {
			return new WP_Error( 'file_upload_failed', __( 'Failed to upload file to AI storage.', 'echo-knowledge-base' ) );
		}

		$state = ! empty( $file['state'] ) ? strtoupper( $file['state'] ) : '';
		if ( $state === '' || $state === 'ACTIVE' ) {
			return $file;
		}

		if ( $state === 'FAILED' ) {
			return self::get_gemini_prompt_attachment_error( $file );
		}

		for ( $attempt = 0; $attempt < 30; $attempt++ ) {
			EPKB_AI_Utilities::safe_sleep( 2 );

			$latest = $client->get_file( $file['name'] );
			if ( is_wp_error( $latest ) ) {
				return $latest;
			}

			$file = isset( $latest['file'] ) && is_array( $latest['file'] ) ? $latest['file'] : $latest;
			$state = ! empty( $file['state'] ) ? strtoupper( $file['state'] ) : '';

			if ( $state === '' || $state === 'ACTIVE' ) {
				return $file;
			}

			if ( $state === 'FAILED' ) {
				return self::get_gemini_prompt_attachment_error( $file );
			}
		}

		return new WP_Error( 'file_processing_timeout', __( 'AI provider is still processing the uploaded file. Please try again.', 'echo-knowledge-base' ) );
	}

	/**
	 * Build a Gemini processing error from a File resource.
	 *
	 * @param array $file Gemini File resource.
	 * @return WP_Error
	 */
	private static function get_gemini_prompt_attachment_error( $file ) {

		$message = __( 'AI provider failed to process the uploaded file.', 'echo-knowledge-base' );
		if ( ! empty( $file['error']['message'] ) ) {
			$message = 'AI ERROR: ' . $file['error']['message'];
		}

		return new WP_Error( 'file_processing_failed', $message );
	}

	/**
	 * Delete a temporary OpenAI prompt attachment.
	 *
	 * @param array $attachment Normalized attachment reference.
	 * @return bool
	 */
	private static function delete_chatgpt_prompt_attachment( $attachment ) {

		if ( empty( $attachment['file_id'] ) ) {
			return true;
		}

		$client = new EPKB_ChatGPT_Client();
		$response = $client->request( '/files/' . rawurlencode( $attachment['file_id'] ), array(), 'DELETE', 'file_storage' );
		if ( is_wp_error( $response ) && $response->get_error_code() !== 'not_found' ) {
			EPKB_AI_Log::add_log( 'Failed to delete temporary AI prompt file', array(
				'provider' => self::PROVIDER_CHATGPT,
				'file_id'  => $attachment['file_id'],
				'error'    => $response->get_error_message(),
			) );
			return false;
		}

		return true;
	}

	/**
	 * Delete a temporary Gemini prompt attachment.
	 *
	 * @param array $attachment Normalized attachment reference.
	 * @return bool
	 */
	private static function delete_gemini_prompt_attachment( $attachment ) {

		if ( empty( $attachment['resource_name'] ) ) {
			return true;
		}

		$client = new EPKB_Gemini_Client();
		$response = $client->delete_file( $attachment['resource_name'] );
		if ( is_wp_error( $response ) && $response->get_error_code() !== 'not_found' ) {
			EPKB_AI_Log::add_log( 'Failed to delete temporary AI prompt file', array(
				'provider'      => self::PROVIDER_GEMINI,
				'resource_name' => $attachment['resource_name'],
				'error'         => $response->get_error_message(),
			) );
			return false;
		}

		return true;
	}

	/**
	 * Extract response content for provider
	 *
	 * @param array $response
	 * @param string|null $provider
	 * @return string
	 */
	public static function extract_response_content( $response, $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		if ( $provider === self::PROVIDER_GEMINI ) {
			return self::extract_gemini_response_content( $response );
		}

		return self::extract_chatgpt_response_content( $response );
	}

	/**
	 * Extract content from ChatGPT-style Responses API payload
	 *
	 * @param array $response
	 * @return string
	 */
	private static function extract_chatgpt_response_content( $response ) {
		if ( ! empty( $response['output_text'] ) && is_string( $response['output_text'] ) ) {
			return EPKB_AI_Utilities::convert_kb_article_references_to_links( $response['output_text'] );
		}

		if ( empty( $response['output'] ) || ! is_array( $response['output'] ) ) {
			return '';
		}

		$content_fragments = array();
		$fallback_content = '';

		foreach ( $response['output'] as $output_item ) {
			if ( empty( $output_item['content'] ) || ! is_array( $output_item['content'] ) ) {
				continue;
			}

			foreach ( $output_item['content'] as $content_item ) {
				$text_fragment = self::extract_chatgpt_content_text_fragment( $content_item );
				if ( $text_fragment !== '' ) {
					$content_fragments[] = $text_fragment;
					continue;
				}

				if ( $fallback_content === '' ) {
					$fallback_content = self::stringify_chatgpt_content_fragment( $content_item );
				}
			}
		}

		$content = empty( $content_fragments ) ? $fallback_content : implode( '', $content_fragments );

		// Convert kb_article patterns to links with article names
		// This is done in the ChatGPT handler since it created these file names
		return EPKB_AI_Utilities::convert_kb_article_references_to_links( $content );
	}

	/**
	 * Extract a text fragment from one ChatGPT Responses API content item.
	 *
	 * @param mixed $content_item
	 * @return string
	 */
	private static function extract_chatgpt_content_text_fragment( $content_item ) {
		if ( is_string( $content_item ) ) {
			return $content_item;
		}

		if ( ! is_array( $content_item ) ) {
			return '';
		}

		return isset( $content_item['text'] ) && is_string( $content_item['text'] ) ? $content_item['text'] : '';
	}

	/**
	 * Convert one ChatGPT Responses API content item to a string as a compatibility fallback.
	 *
	 * @param mixed $content_item
	 * @return string
	 */
	private static function stringify_chatgpt_content_fragment( $content_item ) {
		if ( is_string( $content_item ) ) {
			return $content_item;
		}

		if ( is_array( $content_item ) || is_object( $content_item ) ) {
			return wp_json_encode( $content_item );
		}

		return '';
	}

	/**
	 * Extract content from Gemini generateContent response
	 *
	 * @param array $response
	 * @return string
	 */
	private static function extract_gemini_response_content( $response ) {
		if ( empty( $response['candidates'] ) || ! is_array( $response['candidates'] ) ) {
			return '';
		}

		$candidate = $response['candidates'][0];
		if ( empty( $candidate['content']['parts'] ) || ! is_array( $candidate['content']['parts'] ) ) {
			return '';
		}

		$content = '';
		foreach ( $candidate['content']['parts'] as $part ) {
			if ( is_array( $part ) && isset( $part['text'] ) && $part['text'] !== '' ) {
				$content = $part['text'];
				break;
			}
		}

		if ( $content === '' ) {
			$first_part = $candidate['content']['parts'][0];
			$content = is_string( $first_part ) ? $first_part : wp_json_encode( $first_part );
		}

		// Convert kb_article patterns to links with article names
		// This is done in the ChatGPT handler since it created these file names
		return EPKB_AI_Utilities::convert_kb_article_references_to_links( $content );
	}

	/**
	 * Extract source references from provider response metadata
	 *
	 * @param array $response Raw API response
	 * @param string|null $provider Provider (defaults to active)
	 * @return array Array of source articles with post_id, title, url
	 */
	public static function extract_response_sources( $response, $provider = null ) {
		$provider = $provider ?: self::get_active_provider();

		if ( $provider === self::PROVIDER_GEMINI ) {
			return EPKB_AI_Utilities::extract_sources_from_grounding_metadata( $response );
		}

		return EPKB_AI_Utilities::extract_sources_from_chatgpt_annotations( $response );
	}

	/**
	 * Get preset options formatted for select fields
	 *
	 * @param string $use_case 'chat' or 'search'
	 * @param string|null $provider
	 * @return array
	 */
	public static function get_preset_options( $use_case = 'chat', $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		$presets = self::get_model_presets( $use_case, $provider );
		$default_preset_key = self::get_default_preset_for_use_case( $use_case );
		$options = array();

		foreach ( $presets as $key => $preset ) {
			if ( $key === $default_preset_key ) {
				$options[ $key ] = $preset['label'] . ' - ' . $preset['description'] . ' ' . __( '(default)', 'echo-knowledge-base' );
			} else {
				$options[ $key ] = $preset['label'] . ' - ' . $preset['description'];
			}
		}

		return $options;
	}

	/**
	 * Apply preset settings for a use case
	 *
	 * @param string $preset_key
	 * @param string $use_case 'chat' or 'search'
	 * @param string|null $provider
	 * @return array Applied settings info
	 */
	public static function apply_preset( $preset_key, $use_case = 'chat', $provider = null ) {
		$provider = $provider ?: self::get_active_provider();
		$preset_key = self::normalize_preset_key( $preset_key );
		$preset = self::get_preset_parameters( $preset_key, $use_case, $provider );
		$preset_field = self::get_preset_field_name( $use_case );

		if ( $preset_field !== '' ) {
			EPKB_AI_Config_Specs::update_ai_config_value( $preset_field, $preset_key );
		}

		// translators: %s is the preset name
		return array(
			'message' => sprintf( __( 'Applied "%s" preset', 'echo-knowledge-base' ), $preset['label'] ),
			'applied_settings' => array( 'preset' => $preset_key )
		);
	}
}
