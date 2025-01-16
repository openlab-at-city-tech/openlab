<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class ImageUploader extends Control {

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string {
		return 'lotta-image-uploader';
	}

	/**
	 * @return string[]
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'image_uploader' ];
	}

	/**
	 * Enable position picker
	 *
	 * @return ImageUploader
	 */
	public function enablePositionPicker() {
		return $this->setOption( 'positionPicker', true );
	}

}