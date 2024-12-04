<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\ConfigurableInterface;
use Exception;

class ActiveCallback {

	private $rules     = array();
	private $component = null;

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function applyRules() {
		$result = true;

		foreach ( (array) $this->rules as $rule ) {
			try {
				if ( ! $this->checkRule( $rule ) ) {
					$result = false;
					break;
				}
			} catch ( Exception $e ) {
				throw $e;
			}
		}

		return $result;
	}

	/**
	 * @param $rule
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function checkRule( $rule ) {
		$result = true;

		if ( ! is_array( $rule ) ) {
			throw new Exception( 'Invalid active callback rule' );
		}

		$rule = array_merge(
			array(
				'setting'  => '',
				'operator' => '=',
				'value'    => true,
				'function' => null,
			),
			$rule
		);

		if ( $rule['function'] ) {
			return call_user_func( $rule['function'] );
		}

		if ( empty( $rule['setting'] ) ) {
			return true;
		}

		$value         = $rule['value'];
		$setting_value = $this->getSettingValue( $rule['setting'] );

		switch ( $rule['operator'] ) {
			case '=':
			case '==':
				$result = ( $setting_value == $value );
				break;

			case '!=':
				$result = ( $setting_value != $value );
				break;

			case '===':
				$result = ( $setting_value === $value );
				break;

			case '!==':
				$result = ( $setting_value !== $value );
				break;
			/* greater */
			case '>':
				$result = ( $setting_value > $value );
				break;
			case '>=':
				$result = ( $setting_value >= $value );
				break;
			/* lower than */
			case '<':
				$result = ( $setting_value < $value );
				break;
			case '<=':
				$result = ( $setting_value <= $value );
				break;

			case 'in':
				if ( is_array( $setting_value ) ) {
					$result = in_array( $value, $setting_value );
				} else {
					if ( is_array( $value ) ) {
						$result = in_array( $setting_value, $value );
					} else {
						$result = false;
					}
				}

				break;

		}

		return $result;
	}

	private function getSettingValue( $setting ) {
		global $wp_customize;

		$value = '';

		if ( $wp_customize ) {
			if ( $wp_customize->get_setting( $setting ) ) {
				$value = $wp_customize->get_setting( $setting )->value();
			}
		} else {

			$default = false;
			if ( $this->component ) {
				/** @var ConfigurableInterface $component */
				$component = $this->component;
				$default   = $component::settingDefault( $setting );
			}

			$value = get_theme_mod( $setting, $default );

		}

		return ComponentBase::mabyDeserializeModValue( $value );
	}

	/**
	 * @param array $rules
	 *
	 * @return ActiveCallback
	 */
	public function setRules( $rules ) {
		$this->rules = $rules;

		return $this;
	}

	/**
	 * @param ConfigurableInterface $component
	 *
	 * @return ActiveCallback
	 */
	public function setComponent( $component ) {
		$this->component = $component;

		return $this;
	}

}
