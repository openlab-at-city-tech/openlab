<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Customizer\Sanitizes;

class Layers extends ContainerControl {

	public function __construct( $id ) {
		parent::__construct( $id );

		$this->showLabel();
	}

	public function getType(): string {
		return 'lotta-layers';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'layers' ];
	}

	/**
	 * Get sub controls path
	 *
	 * @return array
	 */
	public function getSubControlsPath(): array {
		return [ 'layers.[].controls' => true ];
	}

	/**
	 * @return Layers
	 */
	public function isDynamic() {
		return $this->setOption( 'dynamic', true );
	}

	/**
	 * Add layer
	 *
	 * @param $id
	 * @param $label
	 * @param array $controls
	 *
	 * @return Layers
	 */
	public function addLayer( $id, $label, array $controls = [] ) {
		$layers        = $this->options['layers'] ?? [];
		$layers[ $id ] = [
			'label'    => $label,
			'controls' => $this->parseControls( $controls ),
		];

		return $this->setLayers( $layers );
	}

	/**
	 * @param array $layers
	 *
	 * @return Layers
	 */
	public function setLayers( array $layers ) {
		return $this->setOption( 'layers', $layers );
	}
}