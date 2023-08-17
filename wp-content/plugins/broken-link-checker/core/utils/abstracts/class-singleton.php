<?php
/**
 * Singleton class for all classes.
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
 * Class Singleton
 *
 * @package WPMUDEV_BLC\Core\Utils\Abstracts
 */
abstract class Singleton {

	/**
	 * Singleton constructor.
	 *
	 * Protect the class from being initiated multiple times.
	 *
	 * @param array $props Optional properties array.
	 *
	 * @since 2.0.0
	 */
	protected function __construct( $props = array() ) {
		// Protect class from being initiated multiple times.
	}

	/**
	 * Instance obtaining method.
	 *
	 * @return static Called class instance.
	 * @since 2.0.0
	 */
	public static function instance() {
		static $instances = array();

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();
		}

		return $instances[ $called_class_name ];
	}
}
