<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Utils;

class Collapse extends ContainerControl {

	public function __construct( $id = null ) {
		parent::__construct( $id ?? Utils::rand_key() );

		$this->solidStyle();
	}

	public function getType(): string {
		return 'lotta-collapse';
	}

	public function solidStyle() {
		return $this->setOption( 'style', 'solid' );
	}

	public function ghostStyle() {
		return $this->setOption( 'style', 'ghost' );
	}

	public function openByDefault() {
		return $this->setOption( 'open', true );
	}

	public function closeByDefault() {
		return $this->setOption( 'open', false );
	}

	/**
	 * Get sub controls path
	 *
	 * @return array
	 */
	public function getSubControlsPath(): array {
		return [ 'controls' => false ];
	}

	public function setControls( $controls ) {
		return $this->setOption( 'controls', $this->parseControls( $controls ) );
	}
}