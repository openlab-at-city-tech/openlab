<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Placeholder extends Control {

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->displayNone();
	}

	public function getType(): string {
		return 'lotta-placeholder';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'primitive' ];
	}

	public function setDefaultBorder( $width, $style, $color, $hover = '' ) {
		$default = array_merge( $this->params['default'] ?? [], [
			'style' => $style,
			'width' => $width,
			'color' => $color,
			'hover' => $hover,
		] );

		return $this->setDefaultValue( $default );
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

	public function setSpacing( array $args = [], $default = '0px' ) {
		return $this->setDefaultValue( wp_parse_args( $args, [
			'top'    => $default,
			'bottom' => $default,
			'left'   => $default,
			'right'  => $default,
			'linked' => true
		] ) );
	}

	public function addColor( $id, $default = '' ) {
		$defaultParam        = $this->params['default'] ?? [];
		$defaultParam[ $id ] = $default;

		return $this->setDefaultValue( $defaultParam );
	}

	/**
	 * Set current color as css var in customizer
	 *
	 * @param $selector
	 * @param $maps
	 *
	 * @return Placeholder
	 *
	 * @since v2.0.15
	 */
	public function setCustomizerColors( $selector, $maps ) {
		return $this->setOption( 'set_customizer_colors', [
			'selector' => $selector,
			'maps'     => $maps,
		] );
	}
}