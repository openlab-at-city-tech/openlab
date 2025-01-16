<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Utils;

class Condition extends ContainerControl {

	/**
	 * @param null $id
	 * @param array $condition
	 * @param array $controls
	 * @param array $reverseControls
	 */
	public function __construct( $id = null, array $condition = [], array $controls = [], array $reverseControls = [] ) {
		parent::__construct( $id !== null ? $id : Utils::rand_key() );

		$this->setCondition( $condition );
		$this->setControls( $controls );
		$this->setReverseControls( $reverseControls );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-condition';
	}

	public function getSubControlsPath(): array {
		return [ 'controls' => false, 'reverseControls' => false ];
	}

	public function reverse() {
		return $this->setOption( 'reverse', true );
	}

	public function setCondition( $condition ) {
		return $this->setOption( 'condition', $condition );
	}

	public function setControls( $controls ) {
		return $this->setOption( 'controls', $this->parseControls( $controls ) );
	}

	public function setReverseControls( $controls ) {
		return $this->setOption( 'reverseControls', $this->parseControls( $controls ) );
	}
}