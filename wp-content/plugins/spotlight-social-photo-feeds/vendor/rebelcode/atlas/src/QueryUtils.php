<?php

namespace RebelCode\Atlas;

use DomainException;

class QueryUtils
{
    /**
     * Used by {@link QueryTypeInterface} instances to obtain the table name from a query. If the table name is missing
     * or is invalid, an exception is thrown.
     *
     * @psalm-mutation-free
     */
    public static function getTableName(string $key, Query $query): string
    {
        /** @var string|mixed $table */
        $table = $query->get($key);
        $table = is_string($table) ? trim($table) : $table;

        if (!is_string($table) || empty($table)) {
            throw new DomainException('The table name was not specified or is not a valid string');
        }

        return $table;
    }
}
