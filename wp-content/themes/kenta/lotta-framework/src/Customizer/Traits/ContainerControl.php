<?php

namespace LottaFramework\Customizer\Traits;

use LottaFramework\Customizer\Control;

trait ContainerControl {

	use Settings;

	/**
	 * Get controls as array data
	 *
	 * @param $controls
	 * @param bool $register
	 *
	 * @return array
	 */
	protected function parseControls( $controls, $register = false ) {
		$arr = [];

		foreach ( $controls as $control ) {
			if ( $control instanceof Control ) {
				$arr[] = $control->toArray();
			} else if ( is_array( $control ) ) {
				$arr[] = $control;
			}

			if ( $register ) {
				$this->register( $control, true );
			}
		}

		return $arr;
	}

	/**
	 * @return array
	 */
	public function getDefaults() {
		$defaults = [];

		foreach ( $this->_settings as $id => $setting ) {
			$defaults[ $id ] = $setting['default'] ?? null;
		}

		return $defaults;
	}
}