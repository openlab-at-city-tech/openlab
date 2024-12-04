<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;

class Text extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-text';
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