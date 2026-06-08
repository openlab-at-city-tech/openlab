<?php

namespace RebelCode\Atlas\QueryType;

use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryTypeInterface;
use RebelCode\Atlas\QueryUtils;
use Throwable;

/** @psalm-immutable */
class DropTable implements QueryTypeInterface
{
    public const TABLE = 'table';
    public const IF_EXISTS = 'if_exists';
    public const CASCADE = 'cascade';

    /** @inheritDoc */
    public function compile(Query $query): string
    {
        try {
            $table = QueryUtils::getTableName(self::TABLE, $query);

            $result = 'DROP TABLE';
            if ($query->get(self::IF_EXISTS, false)) {
                $result .= ' IF EXISTS';
            }

            $result .= " `$table`";

            if ($query->get(self::CASCADE, false)) {
                $result .= ' CASCADE';
            }

            return $result;
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile DROP TABLE query - ' . $e->getMessage(), $query, $e);
        }
    }
}
