<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;
use LottaFramework\Facades\AsyncCss;

class ColorPicker extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function __construct( $id ) {
		parent::__construct( $id );

		$this->enableAlpha();
	}

	public function getType(): string {
		return 'lotta-color-picker';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'rgba_color_collect' ];
	}

	public function disableAlpha() {
		return $this->setOption( 'alpha', false );
	}

	public function enableAlpha() {
		return $this->setOption( 'alpha', true );
	}

	public function computedValue() {
		return $this->setOption( 'computed', true );
	}

	public function setSwatches( $swatches ) {
		return $this->setOption( 'swatches', $swatches );
	}

	public function addColor( $id, $title, $default = '' ) {
		$defaultParam = $this->params['default'] ?? [];
		$colorsOption = $this->options['colors'] ?? [];

		$defaultParam[ $id ] = $default;
		$colorsOption[]      = [
			'title' => $title,
			'id'    => $id,
		];

		return $this->setOption( 'colors', $colorsOption )
		            ->setDefaultValue( $defaultParam );
	}

	/**
	 * Set current color as css var in customizer
	 *
	 * @param $selector
	 * @param $maps
	 *
	 * @return ColorPicker
	 *
	 * @since v2.0.15
	 */
	public function setCustomizerColors( $selector, $maps ) {
		return $this->setOption( 'set_customizer_colors', [
			'selector' => $selector,
			'maps'     => $maps,
		] );
	}

	/**
	 * Shortcut for async colors value
	 *
	 * @param $selector
	 * @param $maps
	 *
	 * @return ColorPicker
	 */
	public function asyncColors( $selector, $maps ) {
		$css = AsyncCss::colors( $maps );

		return $this->asyncCss( $selector, $css );
	}
}