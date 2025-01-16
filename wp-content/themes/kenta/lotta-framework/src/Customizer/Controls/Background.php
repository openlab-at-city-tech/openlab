<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Background extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-background';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'background' ];
	}
}