<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Traits\RadioControl;

class ImageRadio extends Control {

	use RadioControl;

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-image-radio';
	}

	public function inlineChoices() {
		return $this->setOption( 'inline', true );
	}
}