<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Slider extends Control {

	public function getType(): string {
		return 'lotta-slider';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'slider' ];
	}

	/**
	 * Set the min value
	 *
	 * @param $min
	 *
	 * @return Slider
	 */
	public function setMin( $min ) {
		return $this->setOption( 'min', $min );
	}

	/**
	 * Set the max
	 *
	 * @param $max
	 *
	 * @return Slider
	 */
	public function setMax( $max ) {
		return $this->setOption( 'max', $max );
	}

	/**
	 * Set the decimals
	 *
	 * @param $decimals
	 *
	 * @return Slider
	 */
	public function setDecimals( $decimals ) {
		return $this->setOption( 'decimals', $decimals );
	}

	/**
	 * Set slider unit
	 *
	 * @param $unit
	 *
	 * @return Slider
	 */
	public function setDefaultUnit( $unit ) {
		return $this->setOption( 'defaultUnit', $unit );
	}

	/**
	 * Set slider units
	 *
	 * @param array $units
	 *
	 * @return Slider
	 */
	public function setUnits( array $units ) {
		return $this->setOption( 'units', $units );
	}
}