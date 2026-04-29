<?php defined( 'ABSPATH' ) || exit();

/**
 * ChatGPT model catalog.
 *
 * Centralizes supported OpenAI model metadata, defaults, legacy mappings,
 * and request parameter formatting.
 */
class EPKB_ChatGPT_Model_Catalog implements EPKB_AI_Model_Catalog_Interface {

	const DEFAULT_MODEL = 'gpt-5.4';
	const DEFAULT_MAX_OUTPUT_TOKENS = 5000;
	const DEFAULT_MAX_NUM_RESULTS = 10;

	const DEPRECATED_MODELS = array(
		'gpt-4.1-mini'       => 'gpt-5.4-mini',
		'gpt-5-nano'         => 'gpt-5.4-mini',
		'gpt-5'              => 'gpt-5.4',
		'gpt-5.1'            => 'gpt-5.4',
		'gpt-5.2'            => 'gpt-5.4',
		'gpt-5.2-chat-latest'=> 'gpt-5.4',
		'gpt-5.2-pro'        => 'gpt-5.4',
		'gpt-5.4-pro'        => 'gpt-5.4',
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
			'gpt-5.4' => array(
				'name'                       => 'GPT-5.4',
				'type'                       => 'gpt5',
				'description'                => __( 'Balanced frontier model for most AI chat and search workloads.', 'echo-knowledge-base' ),
				'default_params'             => array(
					'reasoning'         => 'medium',
					'verbosity'         => 'medium',
					'max_output_tokens' => self::DEFAULT_MAX_OUTPUT_TOKENS
				),
				'supports_temperature'       => false,
				'supports_top_p'             => false,
				'supports_verbosity'         => true,
				'supports_reasoning'         => true,
				'supports_max_output_tokens' => true,
				'max_output_tokens_limit'    => 16384,
				'verbosity_options'          => array( 'low', 'medium', 'high' ),
				'reasoning_options'          => array( 'none', 'low', 'medium', 'high', 'xhigh' ),
				'parameters'                 => array( 'verbosity', 'reasoning', 'max_output_tokens' )
			),
			'gpt-5.4-mini' => array(
				'name'                       => 'GPT-5.4 mini',
				'type'                       => 'gpt5',
				'description'                => __( 'Fastest low-cost GPT-5 option for lightweight responses.', 'echo-knowledge-base' ),
				'default_params'             => array(
					'reasoning'         => 'none',
					'verbosity'         => 'low',
					'max_output_tokens' => self::DEFAULT_MAX_OUTPUT_TOKENS
				),
				'supports_temperature'       => false,
				'supports_top_p'             => false,
				'supports_verbosity'         => true,
				'supports_reasoning'         => true,
				'supports_max_output_tokens' => true,
				'max_output_tokens_limit'    => 16384,
				'verbosity_options'          => array( 'low', 'medium', 'high' ),
				'reasoning_options'          => array( 'none', 'low', 'medium', 'high', 'xhigh' ),
				'parameters'                 => array( 'verbosity', 'reasoning', 'max_output_tokens' )
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
		$use_temperature = false;

		if ( ! empty( $model_spec['supports_temperature'] ) ) {
			if ( isset( $params['temperature'] ) ) {
				$temperature = floatval( $params['temperature'] );
				if ( $temperature >= 0.0 && $temperature <= 2.0 ) {
					$normalized['temperature'] = $temperature;
					unset( $normalized['top_p'] );
					$use_temperature = true;
				}
			}
		}

		if ( ! empty( $model_spec['supports_top_p'] ) && isset( $params['top_p'] ) && ! $use_temperature ) {
			$top_p = floatval( $params['top_p'] );
			if ( $top_p >= 0.0 && $top_p <= 1.0 ) {
				$normalized['top_p'] = $top_p;
				unset( $normalized['temperature'] );
			}
		}

		if ( ! empty( $model_spec['supports_verbosity'] ) && isset( $params['verbosity'] ) ) {
			$verbosity = strval( $params['verbosity'] );
			$options = empty( $model_spec['verbosity_options'] ) ? array() : $model_spec['verbosity_options'];
			if ( in_array( $verbosity, $options, true ) ) {
				$normalized['verbosity'] = $verbosity;
			}
		}

		if ( ! empty( $model_spec['supports_reasoning'] ) && isset( $params['reasoning'] ) ) {
			$reasoning = strval( $params['reasoning'] );
			$options = empty( $model_spec['reasoning_options'] ) ? array() : $model_spec['reasoning_options'];
			if ( in_array( $reasoning, $options, true ) ) {
				$normalized['reasoning'] = $reasoning;
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
	 * Apply model parameters to a Responses API request.
	 *
	 * @param array $request
	 * @param string $model
	 * @param array $params
	 * @return array
	 */
	public static function apply_model_parameters( $request, $model, $params = array() ) {

		if ( empty( $model ) || ! is_string( $model ) ) {
			return $request;
		}

		$model = self::resolve_model_name( $model );
		$model_spec = self::get_models( $model );
		$params = self::normalize_parameters( $model, $params );
		$use_temperature = ! empty( $model_spec['supports_temperature'] ) && isset( $params['temperature'] );
		// TODO AI PRO LEGACY: Remove after all callers pass resolved model IDs before applying model parameters.
		$request['model'] = $model;

		if ( $use_temperature ) {
			$request['temperature'] = $params['temperature'];
		}

		if ( ! empty( $model_spec['supports_top_p'] ) && isset( $params['top_p'] ) && ! $use_temperature ) {
			$request['top_p'] = $params['top_p'];
		}

		if ( ! empty( $model_spec['supports_verbosity'] ) && ! empty( $params['verbosity'] ) ) {
			if ( ! isset( $request['text'] ) ) {
				$request['text'] = array();
			}
			$request['text']['verbosity'] = $params['verbosity'];
		}

		if ( ! empty( $model_spec['supports_reasoning'] ) && ! empty( $params['reasoning'] ) ) {
			$request['reasoning'] = array(
				'effort' => $params['reasoning']
			);
		}

		if ( ! empty( $model_spec['supports_max_output_tokens'] ) && isset( $params['max_output_tokens'] ) ) {
			$request['max_output_tokens'] = intval( $params['max_output_tokens'] );
		}

		return $request;
	}
}
