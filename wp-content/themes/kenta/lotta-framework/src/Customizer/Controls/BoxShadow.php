<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class BoxShadow extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-box-shadow';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'shadow' ];
	}

	public function setDefaultShadow( $color = 'rgba(0, 0, 0, 0.1)', $horizontal = '0px', $vertical = '0px', $blur = '0px', $spread = '0px', $enable = true ) {
		return $this->setDefaultValue( [
			'enable'     => $enable ? 'yes' : 'no',
			'horizontal' => $horizontal,
			'vertical'   => $vertical,
			'blur'       => $blur,
			'spread'     => $spread,
			'color'      => $color,
		] );
	}
}