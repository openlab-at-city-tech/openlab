<?php

namespace LottaFramework\Customizer\Traits;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

trait RadioControl {

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'select' ];
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
}