<?php

namespace TEC\Common\StellarWP\Models\Repositories\Contracts;

use TEC\Common\StellarWP\Models\Contracts\Model;

interface Insertable {
	/**
	 * Inserts a model record.
	 *
	 * @since 1.0.0
	 *
	 * @param Model $model
	 *
	 * @return Model
	 */
	public function insert( Model $model ) : Model;
}
