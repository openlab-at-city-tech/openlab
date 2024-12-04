<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;

class TextArea extends Control {

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->setFiledAttr( [ 'rows' => 4 ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-text-area';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return 'sanitize_text_field';
	}

	/**
	 * @param $attr
	 *
	 * @return TextArea
	 */
	public function setFiledAttr( $attr ) {
		return $this->setOption( 'field_attr', $attr );
	}
}