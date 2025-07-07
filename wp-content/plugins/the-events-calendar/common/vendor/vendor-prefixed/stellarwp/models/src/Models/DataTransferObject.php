<?php

namespace TEC\Common\StellarWP\Models;

abstract class DataTransferObject {
	/**
	 * Convert data from a query result object to a Model.
	 *
	 * @since 1.0.0
	 *
	 * @param $object
	 *
	 * @return self
	 */
	abstract public static function fromObject( $object );

	/**
	 * Convert data from this object to a Model.
	 *
	 * @since 1.0.0
	 *
	 * @return Model
	 */
	abstract public function toModel() : Model;
}
