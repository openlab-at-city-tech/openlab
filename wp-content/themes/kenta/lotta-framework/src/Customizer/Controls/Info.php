<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Utils;

class Info extends Control {

	public function __construct( $id = null ) {
		parent::__construct( $id ?? Utils::rand_key() );

		$this->hideLabel();
		$this->setDefaultValue( [] );
		$this->alignLeft();
		$this->showBackground();
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'lotta-info';
	}

	/**
	 * @return string
	 */
	public function getSanitize() {
		return '__return_false';
	}

	/**
	 * @param $value
	 *
	 * @return Info
	 */
	public function setInfo( $value ) {
		return $this->setOption( 'info', $value );
	}

	public function hideBackground() {
		return $this->setOption( 'background', false );
	}

	public function showBackground() {
		return $this->setOption( 'background', true );
	}

	public function alignLeft() {
		return $this->setOption( 'align', 'left' );
	}

	public function alignCenter() {
		return $this->setOption( 'align', 'center' );
	}

	public function alignRight() {
		return $this->setOption( 'align', 'right' );
	}
}