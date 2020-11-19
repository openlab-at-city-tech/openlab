<?php

namespace Advanced_Sidebar_Menu\Traits;

/**
 * Trait Singleton
 *
 * @author Mat Lipe
 * @since  7.7.0
 */
trait Singleton {

	/**
	 * Instance of this class for use as singleton
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Tracks if init has been called.
	 *
	 * @since 8.0.1
	 *
	 * @var bool
	 */
	protected static $inited = false;


	/**
	 * Create the instance of the class
	 *
	 * @static
	 * @return void
	 */
	public static function init() {
		static::$instance = static::instance();
		if ( method_exists( static::$instance, 'hook' ) ) {
			static::$instance->hook();
		}
		static::$inited = true;
	}


	/**
	 * Call this method as many times as needed and the
	 * class will only init() one time.
	 *
	 * @static
	 *
	 * @since 8.0.1
	 *
	 * @return void
	 */
	public static function init_once() {
		if ( ! static::$inited ) {
			static::init();
		}
	}


	/**
	 * Get (and instantiate, if necessary) the instance of the
	 * class
	 *
	 * @static
	 * @return static
	 */
	public static function instance() {
		if ( ! is_a( static::$instance, __CLASS__ ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}
}
