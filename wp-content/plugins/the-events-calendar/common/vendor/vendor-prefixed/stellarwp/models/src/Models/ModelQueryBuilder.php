<?php

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;
use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\DB\QueryBuilder\QueryBuilder;
use TEC\Common\StellarWP\DB\QueryBuilder\Clauses\RawSQL;
use TEC\Common\StellarWP\Models\Contracts\Model;
/**
 * @since 1.2.2  improve model generic
 * @since 1.0.0
 *
 * @template M of Model
 */
class ModelQueryBuilder extends QueryBuilder {
	public const MODEL = 'model';

	/**
	 * @var class-string<M>
	 */
	protected $model;

	/**
	 * @param class-string<M> $modelClass
	 */
	public function __construct( string $modelClass ) {
		if ( ! is_subclass_of( $modelClass, Model::class ) ) {
			throw new InvalidArgumentException( "$modelClass must implement " . Model::class );
		}

		$this->model = $modelClass;
	}

	/**
	 * Returns the number of rows returned by a query
	 *
	 * @since 1.0.0
	 *
	 * @param null|string $column
	 */
	public function count( $column = null ) : int {
		$column = ( ! $column || $column === '*' ) ? '1' : trim( $column );

		if ( '1' === $column ) {
			$this->selects = [];
		}
		$this->selects[] = new RawSQL( 'SELECT COUNT(%1s) AS count', $column );

		/** @var object{count:numeric-string} $result */
		$result = parent::get();
		return (int) $result->count;
	}

	/**
	 * Get row
	 *
	 * @since 1.0.0
	 *
	 * @param string $output
	 *
	 * @return M|array<string,mixed>|object|null
	 */
	public function get( $output = self::MODEL ) {
		if ( $output !== self::MODEL ) {
			/** @var array<string,mixed>|object|null */
			return parent::get( $output );
		}

		$row = DB::get_row( $this->getSQL() );

		if ( ! $row ) {
			return null;
		}

		/** @var array<string,mixed>|object $row */
		return $this->model::fromData( $row );
	}

	/**
	 * Get results
	 *
	 * @since 1.0.0
	 *
	 * @return list<M|array<string,mixed>|object>|null
	 */
	public function getAll( $output = self::MODEL ) : ?array {
		if ( $output !== self::MODEL ) {
			/** @var list<array<string,mixed>|object>|null */
			return parent::getAll( $output );
		}

		/** @var list<object> */
		$results = DB::get_results( $this->getSQL() );

		if ( ! $results ) {
			return null;
		}

		/** @var list<M> */
		return array_map( [ $this->model, 'fromData' ], $results );
	}
}
