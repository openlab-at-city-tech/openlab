<?php
declare( strict_types=1 );

namespace TEC\Common\StellarWP\Models\Contracts;

use TEC\Common\StellarWP\Models\ModelQueryBuilder;

/**
 * @since 2.0.0 renamed from ModelCrud, added strict return types.
 * @since 1.0.0
 */
interface ModelPersistable extends Model {
	/**
	 * @since 1.0.0
	 *
	 * @param int $id
	 *
	 * @return ?Model
	 */
	public static function find( $id ): ?Model;

	/**
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $attributes
	 *
	 * @return Model
	 */
	public static function create( array $attributes ): Model;

	/**
	 * @since 1.0.0
	 *
	 * @return Model
	 */
	public function save(): Model;

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function delete() : bool;

	/**
	 * @since 1.0.0
	 *
	 * @return ModelQueryBuilder<static>
	 */
	public static function query(): ModelQueryBuilder;
}
