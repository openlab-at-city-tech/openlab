<?php
/**
 * @license GPL-2.0
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\DB\QueryBuilder;

use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\Aggregate;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\CRUD;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\FromClause;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\GroupByStatement;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\HavingClause;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\JoinClause;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\LimitStatement;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\MetaQuery;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\OffsetStatement;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\OrderByStatement;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\SelectStatement;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\TablePrefix;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\UnionOperator;
use TEC\Common\StellarWP\DB\QueryBuilder\Concerns\WhereClause;

/**
 * @since 1.0.0
 */
class QueryBuilder {
	use Aggregate;
	use CRUD;
	use FromClause;
	use GroupByStatement;
	use HavingClause;
	use JoinClause;
	use LimitStatement;
	use MetaQuery;
	use OffsetStatement;
	use OrderByStatement;
	use SelectStatement;
	use TablePrefix;
	use UnionOperator;
	use WhereClause;

	/**
	 * @return string
	 */
	public function getSQL() {
		$sql = array_merge(
			$this->getSelectSQL(),
			$this->getFromSQL(),
			$this->getJoinSQL(),
			$this->getWhereSQL(),
			$this->getGroupBySQL(),
			$this->getHavingSQL(),
			$this->getOrderBySQL(),
			$this->getLimitSQL(),
			$this->getOffsetSQL(),
			$this->getUnionSQL()
		);

		// Trim double spaces added by DB::prepare
		return str_replace(
			[ '   ', '  ' ],
			' ',
			implode( ' ', $sql )
		);
	}
}
