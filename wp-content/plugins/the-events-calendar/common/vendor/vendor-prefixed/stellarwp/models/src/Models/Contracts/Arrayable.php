<?php

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
	 * @return array<string,mixed>
	 */
	public function toArray() : array;
}
