<?php

namespace TEC\Common\StellarWP\Models\Contracts;

use TEC\Common\StellarWP\Models\ModelQueryBuilder;

/**
 * @since 1.0.0
 */
interface ModelFromQueryBuilderObject {
	/**
	 * @since 1.0.0
	 *
	 * @param $object
	 *
	 * @return Model
	 */
	public static function fromQueryBuilderObject( $object );
}
