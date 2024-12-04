<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Select extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-select';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'select' ];
	}

	/**
	 * Alias for set choices
	 *
	 * @param $choices
	 *
	 * @return Select
	 */
	public function setChoices( $choices ) {
		return $this->setParam( 'choices', apply_filters( $this->id . '_choices', $choices ) );
	}
}