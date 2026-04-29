<?php defined( 'ABSPATH' ) || exit();

/**
 * Gemini model catalog.
 *
 * Centralizes supported Gemini model metadata, defaults, legacy mappings,
 * and request parameter formatting.
 */
class EPKB_Gemini_Model_Catalog implements EPKB_AI_Model_Catalog_Interface {

	const DEFAULT_MODEL = 'gemini-3-flash-preview';
	const DEFAULT_MAX_OUTPUT_TOKENS = 8192;
	const DEFAULT_MAX_NUM_RESULTS = 10;

	const DEPRECATED_MODELS = array(
		'gemini-2.5-flash-lite'         => 'gemini-2.5-flash',
		'gemini-2.5-pro'                => 'gemini-3-flash-preview',
		'gemini-3-pro-preview'          => 'gemini-3-flash-preview',
		'gemini-3.1-pro'                => 'gemini-3-flash-preview',
		'gemini-3.1-pro-preview'        => 'gemini-3-flash-preview',
		'gemini-3.1-flash-lite-preview' => 'gemini-3-flash-preview',
	);

	/**
	 * Get the provider default model.
	 *
	 * @return string
	 */
	public static function get_default_model() {
		return self::DEFAULT_MODEL;
	}

	/**
	 * Get provider default max output tokens.
	 *
	 * @return int
	 */
	public static function get_default_max_output_tokens() {
		return self::DEFAULT_MAX_OUTPUT_TOKENS;
	}

	/**
	 * Get provider default max retrieval results.
	 *
	 * @return int
	 */
	public static function get_default_max_results() {
		return self::DEFAULT_MAX_NUM_RESULTS;
	}

	/**
	 * Get model definitions or one resolved model definition.
	 *
	 * @param string|null $model_name
	 * @return array
	 */
	public static function get_models( $model_name = null ) {

		$models = array(
			'gemini-2.5-flash' => array(
				'name'                       => 'Gemini 2.5 Flash',
				'type'                       => 'gemini',
				'description'                => __( 'Fast, cost-effective Gemini model for general-purpose use.', 'echo-knowledge-base' ),
				'default_params'             => array(
					'temperature'       => 1.0,
					'top_p'             => 0.95,
					'thinking_level'    => 'low',
					'max_output_tokens' => self::DEFAULT_MAX_OUTPUT_TOKENS
				),
				'supports_temperature'       => true,
				'supports_top_p'             => true,
				'supports_thinking_level'    => true,
				'supports_max_output_tokens' => true,
				'max_output_tokens_limit'    => 65536,
				'thinking_config_format'     => 'budget',
				'thinking_budget_map'        => array(
					'low'  => 2048,
					'high' => 8192
				),
				'thinking_level_options'     => array( 'low', 'high' ),
				'parameters'                 => array( 'temperature', 'top_p', 'thinking_level', 'max_output_tokens' )
			),
			'gemini-3-flash-preview' => array(
				'name'                       => 'Gemini 3 Flash',
				'type'                       => 'gemini',
				'description'                => __( 'Default balanced Gemini model for chat and search.', 'echo-knowledge-base' ),
				'default_params'             => array(
					'temperature'       => 1.0,
					'top_p'             => 0.95,
					'thinking_level'    => 'low',
					'max_output_tokens' => self::DEFAULT_MAX_OUTPUT_TOKENS
				),
				'supports_temperature'       => true,
				'supports_top_p'             => true,
				'supports_thinking_level'    => true,
				'supports_max_output_tokens' => true,
				'max_output_tokens_limit'    => 65536,
				'thinking_config_format'     => 'level',
				'thinking_level_options'     => array( 'low', 'high' ),
				'parameters'                 => array( 'temperature', 'top_p', 'thinking_level', 'max_output_tokens' )
			),
		);

		if ( empty( $model_name ) ) {
			return $models;
		}

		$model_name = self::resolve_model_name( $model_name );

		return isset( $models[ $model_name ] ) ? $models[ $model_name ] : $models[ self::DEFAULT_MODEL ];
	}

	/**
	 * Resolve a stored or legacy model name to a valid catalog entry.
	 *
	 * @param string $model_name
	 * @return string
	 */
	public static function resolve_model_name( $model_name ) {

		$model_name = trim( (string) $model_name );
		if ( $model_name === '' ) {
			return self::DEFAULT_MODEL;
		}

		$seen = array();
		while ( isset( self::DEPRECATED_MODELS[ $model_name ] ) && ! isset( $seen[ $model_name ] ) ) {
			$seen[ $model_name ] = true;
			$model_name = self::DEPRECATED_MODELS[ $model_name ];
		}

		$models = self::get_models();

		return isset( $models[ $model_name ] ) ? $model_name : self::DEFAULT_MODEL;
	}

	/**
	 * Normalize model parameters according to model capabilities.
	 *
	 * @param string $model
	 * @param array $params
	 * @return array
	 */
	public static function normalize_parameters( $model, $params = array() ) {

		$model = self::resolve_model_name( $model );
		$model_spec = self::get_models( $model );
		$defaults = empty( $model_spec['default_params'] ) ? array() : $model_spec['default_params'];
		$params = is_array( $params ) ? $params : array();
		$normalized = $defaults;

		if ( ! empty( $model_spec['supports_temperature'] ) && isset( $params['temperature'] ) ) {
			$temperature = floatval( $params['temperature'] );
			if ( $temperature >= 0.0 && $temperature <= 2.0 ) {
				$normalized['temperature'] = $temperature;
			}
		}

		if ( ! empty( $model_spec['supports_top_p'] ) && isset( $params['top_p'] ) ) {
			$top_p = floatval( $params['top_p'] );
			if ( $top_p >= 0.0 && $top_p <= 1.0 ) {
				$normalized['top_p'] = $top_p;
			}
		}

		if ( ! empty( $model_spec['supports_thinking_level'] ) && isset( $params['thinking_level'] ) ) {
			$thinking_level = strval( $params['thinking_level'] );
			$options = empty( $model_spec['thinking_level_options'] ) ? array() : $model_spec['thinking_level_options'];
			if ( in_array( $thinking_level, $options, true ) ) {
				$normalized['thinking_level'] = $thinking_level;
			}
		}

		if ( ! empty( $model_spec['supports_max_output_tokens'] ) && isset( $params['max_output_tokens'] ) ) {
			$max_output_tokens = intval( $params['max_output_tokens'] );
			$max_limit = empty( $model_spec['max_output_tokens_limit'] ) ? self::DEFAULT_MAX_OUTPUT_TOKENS : intval( $model_spec['max_output_tokens_limit'] );
			if ( $max_output_tokens > 0 && $max_output_tokens <= $max_limit ) {
				$normalized['max_output_tokens'] = $max_output_tokens;
			}
		}

		return $normalized;
	}

	/**
	 * Apply model parameters to a Gemini generation request.
	 *
	 * @param array $request
	 * @param string $model
	 * @param array $params
	 * @return array
	 */
	public static function apply_model_parameters( $request, $model, $params = array() ) {

		$model = self::resolve_model_name( $model );
		$model_spec = self::get_models( $model );
		$params = self::normalize_parameters( $model, $params );
		$generation_config = isset( $request['generationConfig'] ) ? $request['generationConfig'] : array();

		if ( ! empty( $model_spec['supports_temperature'] ) && isset( $params['temperature'] ) ) {
			$generation_config['temperature'] = floatval( $params['temperature'] );
		}

		if ( ! empty( $model_spec['supports_top_p'] ) && isset( $params['top_p'] ) ) {
			$generation_config['topP'] = floatval( $params['top_p'] );
		}

		if ( ! empty( $model_spec['supports_max_output_tokens'] ) && isset( $params['max_output_tokens'] ) ) {
			$generation_config['maxOutputTokens'] = intval( $params['max_output_tokens'] );
		}

		if ( ! empty( $model_spec['supports_thinking_level'] ) && ! empty( $params['thinking_level'] ) ) {
			$thinking_config = self::get_thinking_config( $model, $params['thinking_level'] );
			if ( ! empty( $thinking_config ) ) {
				$generation_config['thinkingConfig'] = $thinking_config;
			}
		}

		if ( ! empty( $generation_config ) ) {
			$request['generationConfig'] = $generation_config;
		}

		return $request;
	}

	/**
	 * Build Gemini thinking config from the model's declared contract.
	 *
	 * @param string $model
	 * @param string $level
	 * @return array
	 */
	public static function get_thinking_config( $model, $level ) {

		$model = self::resolve_model_name( $model );
		$model_spec = self::get_models( $model );
		$level = strtolower( trim( (string) $level ) );
		$options = empty( $model_spec['thinking_level_options'] ) ? array( 'low', 'high' ) : $model_spec['thinking_level_options'];

		if ( ! in_array( $level, $options, true ) ) {
			return array();
		}

		if ( ! empty( $model_spec['thinking_config_format'] ) && $model_spec['thinking_config_format'] === 'level' ) {
			return array( 'thinkingLevel' => strtoupper( $level ) );
		}

		if ( empty( $model_spec['thinking_config_format'] ) || $model_spec['thinking_config_format'] !== 'budget' ) {
			return array();
		}

		$budget_map = empty( $model_spec['thinking_budget_map'] ) ? array() : $model_spec['thinking_budget_map'];
		if ( empty( $budget_map[ $level ] ) ) {
			return array();
		}

		return array( 'thinkingBudget' => intval( $budget_map[ $level ] ) );
	}
}
