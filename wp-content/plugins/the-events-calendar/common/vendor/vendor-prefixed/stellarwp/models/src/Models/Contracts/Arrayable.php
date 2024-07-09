<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Models\Contracts;

/**
 * @since 1.0.0
 */
interface Arrayable {
	/**
	 * Get the instance as an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function toArray() : array;
}
