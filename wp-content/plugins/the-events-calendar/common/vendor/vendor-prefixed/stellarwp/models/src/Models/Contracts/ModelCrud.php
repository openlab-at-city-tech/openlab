<?php

namespace TEC\Common\StellarWP\Models\Contracts;

use TEC\Common\StellarWP\Models\ModelQueryBuilder;

/**
 * @since 1.0.0
 */
interface ModelCrud {
	/**
	 * @since 1.0.0
	 *
	 * @param int $id
	 *
	 * @return Model
	 */
	public static function find( $id );

	/**
	 * @since 1.0.0
	 *
	 * @param array $attributes
	 *
	 * @return Model
	 */
	public static function create( array $attributes );

	/**
	 * @since 1.0.0
	 *
	 * @return Model
	 */
	public function save();

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function delete() : bool;

	/**
	 * @since 1.0.0
	 *
	 * @return ModelQueryBuilder
	 */
	public static function query();
}
