<?php

namespace RebelCode\Atlas\QueryType;

use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryCompiler;
use RebelCode\Atlas\QueryTypeInterface;
use Throwable;

/** @psalm-immutable */
class Delete implements QueryTypeInterface
{
    public const FROM = 'table';
    public const WHERE = 'where';
    public const LIMIT = 'limit';
    public const ORDER = 'order';

    public function compile(Query $query): string
    {
        try {
            $table = $query->get(self::FROM);

            $where = QueryCompiler::compileWhere($query->get(self::WHERE));
            $hasWhere = !empty($where);

            $result = [
                'DELETE',
                QueryCompiler::compileFrom($table),
                $where,
                $hasWhere ? QueryCompiler::compileOrder($query->get(self::ORDER)) : null,
                QueryCompiler::compileLimit($query->get(self::LIMIT)),
            ];

            return implode(' ', array_filter($result));
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile DELETE query - ' . $e->getMessage(), $query, $e);
        }
    }
}
