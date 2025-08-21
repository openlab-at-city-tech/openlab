<?php

namespace TEC\Common\StellarWP\Models\Repositories\Contracts;

use TEC\Common\StellarWP\Models\Contracts\Model;

interface Deletable {
	/**
	 * Inserts a model record.
	 *
	 * @since 1.0.0
	 *
	 * @param Model $model
	 *
	 * @return bool
	 */
	public function delete( Model $model ) : bool;
}
