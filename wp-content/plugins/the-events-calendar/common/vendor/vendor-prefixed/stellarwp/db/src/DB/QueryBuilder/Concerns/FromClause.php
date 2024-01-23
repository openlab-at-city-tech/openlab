<?php
/**
 * @license GPL-2.0
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\DB\QueryBuilder\Concerns;

use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\DB\QueryBuilder\Clauses\From;
use TEC\Common\StellarWP\DB\QueryBuilder\Clauses\RawSQL;

/**
 * @since 1.0.0
 */
trait FromClause {
	/**
	 * @var From[]
	 */
	protected $froms = [];

	/**
	 * @param  string|RawSQL  $table
	 * @param  string|null  $alias
	 *
	 * @return $this
	 */
	public function from( $table, $alias = '' ) {
		$this->froms[] = new From( $table, $alias );

		return $this;
	}

	/**
	 * @return array|string[]
	 */
	protected function getFromSQL() {
		if ( empty( $this->froms ) ) {
			return [];
		}

		return [
			'FROM ' . implode(
				', ',
				array_map( function ( From $from ) {
					if ( $from->alias ) {
						return DB::prepare(
							'%1s AS %2s',
							$from->table,
							$from->alias
						);
					}

					return DB::prepare( '%1s', $from->table );
				}, $this->froms )
			)
		];
	}
}
