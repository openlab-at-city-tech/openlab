<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Tags extends Control {

	public function getType(): string {
		return 'lotta-tags';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'tags' ];
	}

	public function enforceWhitelist() {
		return $this->setOption( 'enforceWhitelist', true );
	}

	public function setWhitelist( $whitelist ) {
		return $this->setOption( 'whitelist', $whitelist );
	}

	public function setChoices( $choices ) {
		$whitelist = [];

		foreach ( $choices as $id => $label ) {
			$whitelist[] = [
				'value' => $id,
				'label' => $label,
			];
		}

		return $this->setWhitelist( $whitelist );
	}
}
