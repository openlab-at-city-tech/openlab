<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class MultiSelect extends Control {

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->setColumns( 2 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-multi-select';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'multiSelect' ];
	}

	/**
	 * Alias for set choices
	 *
	 * @param $choices
	 *
	 * @return Control
	 */
	public function setChoices( $choices ) {
		return $this->setParam( 'choices', $choices );
	}

	/**
	 * Alias for set columns
	 *
	 * @param $columns
	 *
	 * @return Control
	 */
	public function setColumns( $columns ) {
		$attr = [];

		if ( isset( $this->options['attr'] ) && is_array( $this->options['attr'] ) ) {
			$attr = $this->options['attr'];
		}

		$attr['data-columns'] = $columns;

		return $this->setOption( 'attr', $attr );
	}

	public function buttonsGroupView() {
		$this->setColumns( 0 );

		return $this->setOption( 'view', 'buttons' );
	}

	public function checkboxView() {
		return $this->setOption( 'view', 'checkbox' );
	}
}