<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Spacing extends Control {

	public function getType(): string {
		return 'lotta-spacing';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'spacing' ];
	}

	public function setSpacing( array $args = [], $default = '0px' ) {
		return $this->setDefaultValue( wp_parse_args( $args, [
			'top'    => $default,
			'bottom' => $default,
			'left'   => $default,
			'right'  => $default,
			'linked' => true
		] ) );
	}

	public function setDisabled( $disabled ) {
		$_disabled = $this->options['disabled'] = [];

		foreach ( $disabled as $item ) {
			$_disabled[ $item ] = true;
		}

		return $this->setOption( 'disabled', $_disabled );
	}

	public function allowAutoToggle() {
		return $this->setOption( 'autoToggle', true );
	}
}
