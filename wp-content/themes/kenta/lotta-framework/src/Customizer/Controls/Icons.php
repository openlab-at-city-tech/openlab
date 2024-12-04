<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

class Icons extends Control {

	public function __construct( string $id ) {
		parent::__construct( $id );

		$this->displayInline();
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'lotta-icons';
	}

	/**
	 * @return array
	 */
	public function getSanitize() {
		return [ Sanitizes::class, 'icons' ];
	}

	/**
	 * @param $libraries
	 *
	 * @return Icons
	 */
	public function setLibraries( $libraries ) {
		return $this->setOption( 'libraries', $libraries );
	}
}