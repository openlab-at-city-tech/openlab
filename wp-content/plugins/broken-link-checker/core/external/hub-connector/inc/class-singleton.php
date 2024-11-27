<?php
/**
 * Trait Singleton
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

defined( 'WPINC' ) || die();

trait Singleton {

	/**
	 * Instance holder.
	 *
	 * @var static $instance
	 */
	private static $instance;

	/**
	 * Instance obtaining method.
	 *
	 * @since 1.0.0
	 *
	 * @return static Called class instance.
	 */
	public static function get() {
		$called_class_name = get_called_class();

		// Only if not already exist.
		if ( ! self::$instance instanceof $called_class_name ) {
			self::$instance = new $called_class_name();
		}

		return self::$instance;
	}
}
