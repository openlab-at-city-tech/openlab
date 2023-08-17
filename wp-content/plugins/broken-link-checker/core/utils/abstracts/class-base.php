<?php
/**
 * File Description:
 * Base abstract class to be inherited by other classes
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Utils\Abstracts
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Utils\Abstracts;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Base
 *
 * @package WPMUDEV_BLC\Core\Utils\Abstracts
 */
abstract class Base extends Singleton {
	/**
	 * Getter method.
	 *
	 * Allows access to extended site properties.
	 *
	 * @param string $key Property to get.
	 *
	 * @return mixed Value of the property. Null if not available.
	 * @since 2.0.0
	 */
	public function __get( $key ) {
		// If set, get it.
		if ( isset( $this->{$key} ) ) {
			return $this->{$key};
		}

		return null;
	}

	/**
	 * Setter method.
	 *
	 * Set property and values to class.
	 *
	 * @param string $key Property to set.
	 * @param mixed  $value Value to assign to the property.
	 *
	 * @since 2.0.0
	 */
	public function __set( $key, $value ) {
		$this->{$key} = $value;
	}
}
