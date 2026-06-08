<?php

namespace RebelCode\Atlas\QueryType;

use DomainException;
use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Order;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryTypeInterface;
use RebelCode\Atlas\QueryUtils;
use RebelCode\Atlas\Schema\Index;
use Throwable;

/** @psalm-immutable */
class CreateIndex implements QueryTypeInterface
{
    public const TABLE = 'table';
    public const NAME = 'name';
    public const INDEX = 'index';

    public function compile(Query $query): string
    {
        try {
            $table = QueryUtils::getTableName(self::TABLE, $query);

            $index = $query->get(self::INDEX);
            if (!$index instanceof Index) {
                throw new DomainException('The index was not specified or is not a valid `Index` instance');
            }

            $name = $query->get(self::NAME);
            $name = is_string($name) ? trim($name) : $name;
            if (empty($name) || !is_string($name)) {
                throw new DomainException('The index name was not specified or is not a valid string');
            }

            $columns = $index->getColumns();
            if (empty($columns)) {
                throw new DomainException('The column list is empty');
            }

            $columnList = [];
            foreach ($index->getColumns() as $col => $order) {
                $order = $order ?? Order::ASC;
                $columnList[] = "`$col` $order";
            }

            $columnStr = implode(', ', $columnList);
            $command = $index->isUnique() ? 'CREATE UNIQUE INDEX' : 'CREATE INDEX';

            return "$command `$name` ON `$table` ($columnStr)";
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile CREATE INDEX query - ' . $e->getMessage(), $query, $e);
        }
    }
}
