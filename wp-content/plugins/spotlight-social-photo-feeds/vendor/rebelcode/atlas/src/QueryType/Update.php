<?php

namespace RebelCode\Atlas\QueryType;

use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryCompiler;
use RebelCode\Atlas\QueryTypeInterface;
use RebelCode\Atlas\QueryUtils;
use Throwable;

/** @psalm-immutable */
class Update implements QueryTypeInterface
{
    public const TABLE = 'table';
    public const SET = 'set';
    public const WHERE = 'where';
    public const LIMIT = 'limit';
    public const ORDER = 'order';

    public function compile(Query $query): string
    {
        try {
            $table = QueryUtils::getTableName(self::TABLE, $query);
            $set = $query->get(self::SET);
            $where = $query->get(self::WHERE);
            $order = $query->get(self::ORDER, []);
            $limit = $query->get(self::LIMIT);

            $updateSet = QueryCompiler::compileAssignmentList('SET', $set);
            if (empty($updateSet)) {
                throw new \DomainException('UPDATE SET is missing');
            }

            $result = [
                "UPDATE `$table`",
                $updateSet,
                QueryCompiler::compileWhere($where),
                QueryCompiler::compileOrder($order),
                QueryCompiler::compileLimit($limit),
            ];

            return implode(' ', array_filter($result));
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile UPDATE query - ' . $e->getMessage(), $query, $e);
        }
    }
}
