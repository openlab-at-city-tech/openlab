<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class ColorPalettes extends Control {

	public function __construct( $id, $colors = [] ) {
		parent::__construct( $id );

		$this->setOption( 'labels', $colors );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-color-palettes';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'palettes' ];
	}

	/**
	 * Add new palette
	 *
	 * @param $id
	 * @param $palette
	 *
	 * @return ColorPalettes
	 */
	public function addPalette( $id, $palette ) {
		$palettes        = $this->options['palettes'] ?? [];
		$palettes[ $id ] = $palette;

		return $this->setOption( 'palettes', $palettes );
	}

	/**
	 * @param $id
	 * @param $maps
	 *
	 * @return ColorPalettes
	 *
	 * @since v2.0.15
	 */
	public function setColor( $id, $map ) {
		$maps        = $this->options['maps'] ?? [];
		$maps[ $id ] = $map;

		return $this->setOption( 'maps', $maps );
	}
}