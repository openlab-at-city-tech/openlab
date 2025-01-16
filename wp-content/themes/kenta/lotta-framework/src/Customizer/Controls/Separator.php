<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Utils;

class Separator extends Control {

	public function __construct( $id = null ) {
		parent::__construct( $id ?? Utils::rand_key() );

		$this->setDefaultValue( [] );
		$this->hideLabel();
	}

	public function getType(): string {
		return 'lotta-separator';
	}

	public function getSanitize() {
		return '__return_false';
	}

	public function setStyle( $style ) {
		return $this->setOption( 'style', $style );
	}

	public function solidStyle() {
		return $this->setStyle( 'solid' );
	}

	public function dashedStyle() {
		return $this->setStyle( 'dashed' );
	}

	public function dottedStyle() {
		return $this->setStyle( 'dotted' );
	}

	public function setSize( $size ) {
		return $this->setOption( 'size', $size );
	}

	public function setSpacing( $spacing ) {
		return $this->setOption( 'spacing', $spacing );
	}
}