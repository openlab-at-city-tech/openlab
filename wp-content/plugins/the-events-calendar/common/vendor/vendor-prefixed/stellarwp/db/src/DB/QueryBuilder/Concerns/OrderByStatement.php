<?php
/**
 * @license GPL-2.0
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\DB\QueryBuilder\Concerns;

use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\DB\QueryBuilder\Clauses\OrderBy;

/**
 * @since 1.0.0
 */
trait OrderByStatement {
	/**
	 * @var OrderBy[]
	 */
	protected $orderBys = [];

	/**
	 * @param  string  $column
	 * @param  string  $direction  ASC|DESC
	 *
	 * @return $this
	 */
	public function orderBy( $column, $direction = 'ASC' ) {
		$this->orderBys[] = new OrderBy( $column, $direction );

		return $this;
	}

	/**
	 * @return array|string[]
	 */
	protected function getOrderBySQL() {
		if ( empty( $this->orderBys ) ) {
			return [];
		}

		$orderBys = implode(
			', ',
			array_map( function ( OrderBy $order ) {
				return DB::prepare( '%1s %2s', $order->column, $order->direction );
			}, $this->orderBys )
		);


		return [ 'ORDER BY ' . $orderBys ];
	}
}
