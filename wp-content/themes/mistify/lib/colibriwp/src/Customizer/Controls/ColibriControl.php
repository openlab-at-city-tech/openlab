<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Translations;
use WP_Customize_Control;
use WP_Customize_Manager;
use WP_Error;

class ColibriControl extends WP_Customize_Control {

	const DEFAULT_COLIBRI_TAB = 'content';
	const STYLE_COLIBRI_TAB   = 'style';

	protected $colibri_tab = self::DEFAULT_COLIBRI_TAB;
	protected $default     = '';

	private $extra_json_params = array();

	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$exclude_keys = array_merge(
			array_keys( get_object_vars( $this ) ),
			array(
				'transport',
				'colibri_selective_refresh_key',
				'colibri_selective_refresh_class',
				'css_output',
			)
		);

		$args_keys         = array_keys( $args );
		$extra_json_params = array_diff( $args_keys, $exclude_keys );

		foreach ( $extra_json_params as $param ) {
			$this->extra_json_params[ $param ] = $args[ $param ];
		}

	}

	/**
	 * Default sanitization function for Colibri Controls.
	 * This is added to force a sanitization implementation for each Colibri Control
	 *
	 * @param $value
	 * @param $control_data
	 *
	 * @param string       $default
	 *
	 * @return mixed
	 */
	public static function sanitize( $value, $control_data, $default = '' ) {
		return new WP_Error(
			'colibri_undefined_sanitize_function_for_control',
			Translations::get( 'undefined_sanitize_function_for_control', array( $control_data['type'] ) )
		);
	}

	public function json() {
		$json      = parent::json();
		$json_data = $this->extra_json_params;

		$json['choices']     = $this->choices;
		$json['colibri_tab'] = $this->colibri_tab;

		return array_merge( $json, $json_data );
	}

	protected function hasParam( $name, $in_extra = true ) {
		if ( property_exists( $this, $name ) ) {
			return true;
		}

		if ( $in_extra && array_key_exists( $name, $this->extra_json_params ) ) {
			return true;
		}

		return false;
	}

	protected function getParam( $name, $default = null, $in_extra = true ) {
		if ( property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		if ( $in_extra && array_key_exists( $name, $this->getExtraJsonParams() ) ) {
			return $this->getExtraJsonParams()[ $name ];
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getExtraJsonParams() {
		return $this->extra_json_params;
	}

	protected function getProps( $props = array() ) {
		$props = is_array( $props ) ? $props : array( $props );
		$props = array_flip( $props );

		foreach ( $props as $key => $prop ) {
			$props[ $key ] = null;

			if ( property_exists( $this, $key ) ) {
				$props[ $key ] = $this->$key;
			}
		}

		return $props;
	}
}
