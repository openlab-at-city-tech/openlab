<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Models\Repositories\Contracts;

use TEC\Common\StellarWP\Models\Contracts\Model;

interface Updatable {
	/**
	 * Inserts a model record.
	 *
	 * @since 1.0.0
	 *
	 * @param Model $model
	 *
	 * @return Model
	 */
	public function update( Model $model ) : Model;
}
