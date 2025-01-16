<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Customizer\Sanitizes;
use LottaFramework\Utils;

class Section extends ContainerControl {

	/**
	 * @param null $id
	 */
	public function __construct( $id = null ) {
		if ( $id === null ) {
			$id = Utils::rand_key();
		}

		parent::__construct( $id );

		$this->setDefaultValue( 'yes' );
		$this->setControls( [] );
		$this->showLabel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-section';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'checkbox' ];
	}

	/**
	 * Enable switch
	 *
	 * @return Section
	 */
	public function enableSwitch( $default = true ) {
		$this->setOption( 'switch', true );

		if ( $default ) {
			return $this->setDefaultValue( 'yes' );
		}

		return $this->setDefaultValue( 'no' );
	}

	/**
	 * Get sub controls path
	 *
	 * @return array
	 */
	public function getSubControlsPath(): array {
		return [ 'controls' => false ];
	}

	/**
	 * @param $controls
	 *
	 * @return Section
	 */
	public function setControls( $controls ) {
		return $this->setOption( 'controls', $this->parseControls( $controls ) );
	}

	public function keepMarginAbove() {
		return $this->setOption( 'marginTop', true );
	}

	public function keepMarginBelow() {
		return $this->setOption( 'marginBottom', true );
	}
}