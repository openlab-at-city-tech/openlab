<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class FileUploader extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-file-uploader';
	}

	/**
	 * @return string[]
	 */
	public function getSanitize() {
		return [ $this, 'sanitizeCallback' ];
	}

	/**
	 * Sanitize callback for repeater control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public function sanitizeCallback( $input, $args ) {

		$result = [];
		foreach ( $input as $item ) {
			$result[] = Sanitizes::attachment_info( $item );
		}

		return $result;
	}

	/**
	 * Set media type
	 *
	 * @param $media_type
	 *
	 * @return FileUploader
	 */
	public function setMediaType( $media_type ) {
		return $this->setOption( 'mediaType', $media_type );
	}

	/**
	 * Enable multiple select
	 *
	 * @param bool $multiple
	 *
	 * @return FileUploader
	 */
	public function enableMultiple( $multiple = true ) {
		return $this->setOption( 'multiple', $multiple );
	}
}