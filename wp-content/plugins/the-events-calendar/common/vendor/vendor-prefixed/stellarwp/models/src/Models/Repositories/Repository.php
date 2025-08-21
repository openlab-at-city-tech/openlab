<?php

namespace TEC\Common\StellarWP\Models\Repositories;

use TEC\Common\StellarWP\Models\ModelQueryBuilder;

abstract class Repository {
	/**
	 * Prepare a query builder for the repository.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelQueryBuilder
	 */
	abstract function prepareQuery() : ModelQueryBuilder;
}
