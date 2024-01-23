<?php

namespace Imagely\NGG\DataTypes;

class Lightbox {

	public $id      = '';
	public $name    = '';
	public $title   = '';
	public $code    = '';
	public $values  = [];
	public $scripts = [];
	public $styles  = [];

	public $albums_supported = false;

	public function __construct( $id = '' ) {
		$this->id = $id;
	}

	/**
	 * Returns whether the lightbox supports displaying entities from the displayed gallery object.
	 * Most lightbox do not support displaying albums.
	 *
	 * @param DisplayedGallery $displayed_gallery
	 * @return bool
	 */
	public function is_supported( $displayed_gallery ) {
		return ! in_array( $displayed_gallery->source, [ 'album', 'albums' ] ) || isset( $displayed_gallery->display_settings['open_gallery_in_lightbox'] );
	}
}
