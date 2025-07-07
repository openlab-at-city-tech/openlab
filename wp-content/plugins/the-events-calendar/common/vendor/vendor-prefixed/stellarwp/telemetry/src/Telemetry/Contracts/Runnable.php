<?php
/**
 * Provides an API for all classes that are runnable.
 *
 * @since 1.0.0
 *
 * @package TEC\Common\StellarWP\Telemetry\Contracts
 */

namespace TEC\Common\StellarWP\Telemetry\Contracts;

/**
 * Provides an API for all classes that are runnable.
 *
 * @since 1.0.0
 *
 * @package TEC\Common\StellarWP\Telemetry\Contracts
 */
interface Runnable {
	/**
	 * Run the intended action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run();
}
