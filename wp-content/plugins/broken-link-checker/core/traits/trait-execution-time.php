<?php
/**
 * Methods for obeying system max execution time.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Traits
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Traits;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Enqueue
 *
 * @package WPMUDEV_BLC\Core\Traits
 */
trait Execution_Time {
	/**
	 * Holds the microtime that timer started on.
	 *
	 * @var null
	 */
	protected static $execution_start_time = null;

	/**
	 * Starts the timer. Required to make sure execution doesn't go above max execution time.
	 *
	 * @return void
	 */
	public function start_timer() {
		if ( empty( self::$execution_start_time ) ) {
			self::$execution_start_time = microtime( true );
		}
	}

	/**
	 * Returns the time lapsed since timer started.
	 *
	 * @return float
	 */
	public function runtime() {
		//return microtime( true ) - (float) self::$execution_start_time;
		return timer_stop();
	}

	/**
	 * Returns true if runtime has passed max execution time.
	 *
	 * @return bool
	 */
	public function runtime_passed_limit() {
		return $this->runtime() >= $this->max_execution_time();
	}

	/**
	 * Max execution time for process. Default is one third of system max_execution_time.
	 *
	 * @return mixed
	 */
	public function max_execution_time() {
		$max_execution_time = intval( ini_get( 'max_execution_time' ) );

		return intval(
			apply_filters(
				'wpmudev_blc_max_execution_time',
				! empty( $max_execution_time ) ? ( 0.75 * $max_execution_time ) : 15,
				$max_execution_time
			)
		);
	}
}
