<?php

namespace TEC\Common\StellarWP\Models\Repositories;

use TEC\Common\StellarWP\Models\Contracts\Model;
use TEC\Common\StellarWP\Models\ModelQueryBuilder;

/**
 * @template M of Model
 */
abstract class Repository {
	/**
	 * Prepare a query builder for the repository.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelQueryBuilder<M>
	 */
	abstract function prepareQuery() : ModelQueryBuilder;
}
