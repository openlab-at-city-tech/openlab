<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Filters extends Control {

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->disableByDefault();
	}

	public function getType(): string {
		return 'lotta-css-filters';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'filters' ];
	}

	public function enableByDefault() {
		return $this->setDefaultValue( [ 'enable' => 'yes' ] );
	}

	public function disableByDefault() {
		return $this->setDefaultValue( [ 'enable' => 'no' ] );
	}
}
