<?php

namespace Imagely\NGG\DataTypes;

/**
 * Lightbox data type.
 */
class Lightbox {

	/**
	 * Lightbox ID.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Lightbox name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Lightbox title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Lightbox code.
	 *
	 * @var string
	 */
	public $code = '';

	/**
	 * Lightbox values.
	 *
	 * @var array
	 */
	public $values = [];

	/**
	 * Lightbox scripts.
	 *
	 * @var array
	 */
	public $scripts = [];

	/**
	 * Lightbox styles.
	 *
	 * @var array
	 */
	public $styles = [];

	/**
	 * Whether albums are supported.
	 *
	 * @var bool
	 */
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
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		return ! in_array( $displayed_gallery->source, [ 'album', 'albums' ] ) || isset( $displayed_gallery->display_settings['open_gallery_in_lightbox'] );
	}
}
