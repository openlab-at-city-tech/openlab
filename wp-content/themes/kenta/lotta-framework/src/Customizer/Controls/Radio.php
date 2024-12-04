<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Traits\RadioControl;

class Radio extends Control {

	use RadioControl;

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->setColumns( 2 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-radio';
	}

	public function buttonsGroupView() {
		$this->setColumns( 0 );

		return $this->setOption( 'view', 'buttons' );
	}

	public function radioInputView() {
		return $this->setOption( 'view', 'radio' );
	}
}