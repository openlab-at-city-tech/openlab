<?php

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;
use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\DB\QueryBuilder\QueryBuilder;
use TEC\Common\StellarWP\DB\QueryBuilder\Clauses\RawSQL;
use TEC\Common\StellarWP\Models\Model;

/**
 * @since 1.2.2  improve model generic
 * @since 1.0.0
 *
 * @template M of Model
 */
class ModelQueryBuilder extends QueryBuilder {
	/**
	 * @var class-string<M>
	 */
	protected $model;

	/**
	 * @param class-string<M> $modelClass
	 */
	public function __construct( string $modelClass ) {
		if ( ! is_subclass_of( $modelClass, Model::class ) ) {
			throw new InvalidArgumentException( "$modelClass must be an instance of " . Model::class );
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

		return +parent::get()->count;
	}

	/**
	 * Get row
	 *
	 * @since 1.0.0
	 *
	 * @param string $output
	 *
	 * @return M|null
	 */
	public function get( $output = OBJECT ): ?Model {
		$row = DB::get_row( $this->getSQL(), OBJECT );

		if ( ! $row ) {
			return null;
		}

		return $this->getRowAsModel( $row );
	}

	/**
	 * Get results
	 *
	 * @since 1.0.0
	 *
	 * @return M[]|null
	 */
	public function getAll( $output = OBJECT ) : ?array {
		$results = DB::get_results( $this->getSQL(), OBJECT );

		if ( ! $results ) {
			return null;
		}

		if ( isset( $this->model ) ) {
			return $this->getAllAsModel( $results );
		}

		return $results;
	}

	/**
	 * Get row as model
	 *
	 * @since 1.0.0
	 *
	 * @param object|null $row
	 *
	 * @return M|null
	 */
	protected function getRowAsModel( $row ) {
		$model = $this->model;

		if ( ! method_exists( $model, 'fromQueryBuilderObject' ) ) {
			throw new InvalidArgumentException( "fromQueryBuilderObject missing from $model" );
		}

		return $model::fromQueryBuilderObject( $row );
	}

	/**
	 * Get results as models
	 *
	 * @since 1.0.0
	 *
	 * @param object[] $results
	 *
	 * @return M[]|null
	 */
	protected function getAllAsModel( array $results ) {
		/** @var Contracts\ModelCrud $model */
		$model = $this->model;

		if ( ! method_exists( $model, 'fromQueryBuilderObject' ) ) {
			throw new InvalidArgumentException( "fromQueryBuilderObject missing from $model" );
		}

		return array_map( static function( $object ) use ( $model ) {
			return $model::fromQueryBuilderObject( $object );
		}, $results );
	}
}
