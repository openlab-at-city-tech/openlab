<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

/**
 * Legacy image collection data type.
 */
class LegacyImageCollection implements \ArrayAccess {

	/**
	 * Container array.
	 *
	 * @var array
	 */
	public $container = [];

	/**
	 * Galleries array.
	 *
	 * @var array
	 */
	public $galleries = [];

	/**
	 * Checks if an offset exists.
	 *
	 * @param $offset
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->container[ $offset ] );
	}

	/**
	 * Gets the value at an offset.
	 *
	 * @param $offset
	 * @return mixed|null
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return isset( $this->container[ $offset ] ) ? $this->container[ $offset ] : null;
	}

	/**
	 * Sets the value at an offset.
	 *
	 * @param $offset
	 * @param $value
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( is_object( $value ) ) {
			$value->container = $this;
		}

		if ( is_null( $offset ) ) {
			$this->container[] = $value;
		} else {
			$this->container[ $offset ] = $value;
		}
	}

	/**
	 * Unsets the value at an offset.
	 *
	 * @param $offset
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->container[ $offset ] );
	}

	/**
	 * Retrieves and caches a gallery mapper instance for this gallery id
	 *
	 * @param int $gallery_id Gallery ID
	 * @return mixed
	 */
	public function get_gallery( $gallery_id ) {
		if ( ! isset( $this->galleries[ $gallery_id ] ) || is_null( $this->galleries[ $gallery_id ] ) ) {
			$this->galleries[ $gallery_id ] = GalleryMapper::get_instance();
		}

		return $this->galleries[ $gallery_id ];
	}
}
