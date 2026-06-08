<?php

namespace RebelCode\Atlas\QueryType;

use DomainException;
use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryCompiler;
use RebelCode\Atlas\QueryTypeInterface;
use Throwable;

/** @psalm-immutable */
class Select implements QueryTypeInterface
{
    public const FROM = 'table';
    public const COLUMNS = 'columns';
    public const WHERE = 'where';
    public const GROUP = 'group';
    public const HAVING = 'having';
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';
    public const ORDER = 'order';

    public function compile(Query $query): string
    {
        try {
            $from = $query->get(self::FROM);
            $columns = $query->get(self::COLUMNS, ['*']);
            $where = $query->get(self::WHERE);
            $group = $query->get(self::GROUP);
            $having = $query->get(self::HAVING);
            $limit = $query->get(self::LIMIT);
            $offset = $query->get(self::OFFSET);
            $order = $query->get(self::ORDER, []);

            $fromStr = QueryCompiler::compileFrom($from, null, true);
            if (empty($fromStr)) {
                throw new DomainException('The query source is missing or is invalid');
            }

            $result = [
                'SELECT',
                QueryCompiler::compileColumnList($columns, true),
                QueryCompiler::compileFrom($from, null, true),
                QueryCompiler::compileWhere($where),
                QueryCompiler::compileGroupBy($group),
                QueryCompiler::compileHaving($having),
                QueryCompiler::compileOrder($order),
                QueryCompiler::compileLimit($limit),
                QueryCompiler::compileOffset($offset),
            ];

            return implode(' ', array_filter($result));
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile SELECT query - ' . $e->getMessage(), $query, $e);
        }
    }
}
