<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Border extends Control {

	public function getType(): string {
		return 'lotta-border';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'border' ];
	}

	public function enableHoverColor() {
		return $this->setOption( 'enableHover', true );
	}

	public function setDefaultBorder( $width, $style, $color, $hover = '', $inherit = false ) {
		$default = array_merge( $this->params['default'] ?? [], [
			'style'   => $style,
			'width'   => $width,
			'color'   => $color,
			'hover'   => $hover,
			'inherit' => $inherit,
		] );

		return $this->setDefaultValue( $default );
	}

	public function inheritByDefault() {
		$default = $this->params['default'] ?? [];

		$default['inherit'] = true;

		return $this->setDefaultValue( $default );
	}
}