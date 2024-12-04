<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Toggle extends Control {

	public function getType(): string {
		return 'lotta-toggle';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'checkbox' ];
	}

	public function openByDefault() {
		return $this->setDefaultValue( 'yes' );
	}

	public function closeByDefault() {
		return $this->setDefaultValue( 'no' );
	}
}