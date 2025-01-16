<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Utils;

class CallToAction extends Control {

	public function __construct( $id = null ) {
		parent::__construct( $id ?? Utils::rand_key() );

		$this->hideLabel();
		$this->displayAsLink();
		$this->setDefaultValue( [] );
	}

	public function getType(): string {
		return 'lotta-cta';
	}

	public function getSanitize() {
		return '__return_false';
	}

	public function expandCustomize( $path ) {
		$this->setOption( 'cta', 'customize' );
		$this->setOption( 'target', $path );

		return $this;
	}

	public function linkTo( $url ) {
		$this->setOption( 'cta', 'url' );
		$this->setOption( 'target', $url );

		return $this;
	}

	public function displayAsLink() {
		return $this->setOption( 'style', 'link' );
	}

	public function displayAsButton() {
		return $this->setOption( 'style', 'button' );
	}
}
