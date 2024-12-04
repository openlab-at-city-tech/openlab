<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;

class Editor extends Control {

	public function __construct( $id ) {
		parent::__construct( $id );

		$this->showQuicktags();
	}

	public function getType(): string {
		return 'lotta-editor';
	}

	public function getSanitize() {
		return 'wp_kses_post';
	}

	/**
	 * @param $attr
	 * @param $value
	 *
	 * @return Editor
	 */
	public function setTinymce( $attr, $value ) {
		$tinymce          = $this->options['tinymce'] ?? [];
		$tinymce[ $attr ] = $value;

		return $this->setOption( 'tinymce', $tinymce );
	}

	/**
	 * @param $value
	 *
	 * @return Editor
	 */
	public function setToolbar1( $value ) {
		return $this->setTinymce( 'toolbar1', $value );
	}

	/**
	 * @param $value
	 *
	 * @return Editor
	 */
	public function setToolbar2( $value ) {
		return $this->setTinymce( 'toolbar2', $value );
	}

	/**
	 * @return Editor
	 */
	public function hideMediaButtons() {
		return $this->setOption( 'mediaButtons', false );
	}

	/**
	 * @return Editor
	 */
	public function showMediaButtons() {
		return $this->setOption( 'mediaButtons', true );
	}

	/**
	 * @return Editor
	 */
	public function hideQuicktags() {
		return $this->setOption( 'quicktags', false );
	}

	/**
	 * @return Editor
	 */
	public function showQuicktags() {
		return $this->setOption( 'quicktags', true );
	}
}