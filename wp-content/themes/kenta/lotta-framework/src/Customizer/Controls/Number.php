<?php

namespace LottaFramework\Customizer\Controls;

class Number extends Slider {

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->setDefaultUnit( false );
	}

	public function getType(): string {
		return 'lotta-number';
	}

	/**
	 * Select control when focus
	 *
	 * @return Number
	 */
	public function selectOnFocus() {
		return $this->setOption( 'select_on_focus', true );
	}
}