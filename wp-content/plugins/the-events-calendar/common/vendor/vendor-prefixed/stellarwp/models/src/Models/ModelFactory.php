<?php

namespace TEC\Common\StellarWP\Models;

use Exception;
use TEC\Common\StellarWP\DB\DB;

/**
 * @template M
 */
abstract class ModelFactory {
	/**
	 * @var class-string<M>
	 */
	protected $model;

	/**
	 * @var int The number of models to create.
	 */
	protected $count = 1;

	/**
	 * @since 1.0.0
	 *
	 * @param class-string<M> $model
	 *
	 * @return void
	 */
	public function __construct( $model ) {
		$this->model = $model;
	}

	/**
	 * Define the model's default state.
	 */
	abstract public function definition() : array;

	/**
	 * @since 1.0.0
	 *
	 * @param array $attributes
	 *
	 * @return M|M[]
	 */
	public function make( array $attributes = [] ) {
		$results = [];
		for ( $i = 0; $i < $this->count; $i++ ) {
			$instance = $this->makeInstance( $attributes );

			$results[] = $instance;
		}

		return $this->count === 1 ? $results[0] : $results;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param array $attributes
	 *
	 * @return M|M[]
	 * @throws Exception
	 */
	public function create( array $attributes = [] ) {
		$instances = $this->make( $attributes );
		$instances = is_array( $instances ) ? $instances : [ $instances ];

		DB::transaction( function() use ( $instances ) {
			foreach ( $instances as $instance ) {
				$instance->save();
			}
		} );

		return $this->count === 1 ? $instances[0] : $instances;
	}

	/**
	 * Creates an instance of the model from the attributes and definition.
	 *
	 * @since 1.0.0
	 *
	 * @return M
	 */
	protected function makeInstance( array $attributes ) {
		return new $this->model( array_merge( $this->definition(), $attributes ) );
	}

	/**
	 * Configure the factory.
	 *
	 * @since 1.0.0
	 */
	public function configure() : self {
		return $this;
	}

	/**
	 * Sets the number of models to generate.
	 *
	 * @since 1.0.0
	 */
	public function count( int $count ) : self {
		$this->count = $count;

		return $this;
	}
}
