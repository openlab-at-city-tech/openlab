<?php
/**
 * The API implemented by all subscribers.
 *
 * @package TEC\Common\StellarWP\Telemetry\Contracts
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Telemetry\Contracts;

/**
 * Interface Subscriber_Interface
 *
 * @package TEC\Common\StellarWP\Telemetry\Contracts
 */
interface Subscriber_Interface {

	/**
	 * Register action/filter listeners to hook into WordPress
	 *
	 * @return void
	 */
	public function register();

}
