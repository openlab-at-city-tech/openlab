<?php

namespace RebelCode\Atlas\QueryType;

use DomainException;
use InvalidArgumentException;
use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Expression\Term;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryCompiler;
use RebelCode\Atlas\QueryTypeInterface;
use RebelCode\Atlas\QueryUtils;
use Throwable;
use UnexpectedValueException;

/** @psalm-immutable */
class Insert implements QueryTypeInterface
{
    public const TABLE = 'table';
    public const COLUMNS = 'columns';
    public const VALUES = 'values';
    public const ON_DUPLICATE_KEY = 'on_duplicate';

    /** @inheritDoc */
    public function compile(Query $query): string
    {
        try {
            $table = QueryUtils::getTableName(self::TABLE, $query);

            $columns = $query->get(self::COLUMNS);
            $columnsStr = QueryCompiler::compileColumnList($columns);

            if (empty($columnsStr)) {
                throw new UnexpectedValueException('Column list cannot be empty');
            }

            /** @var array $columns */
            $numColumns = count($columns);
            $values = $query->get(self::VALUES);
            $valuesStr = self::compileInsertValues($values, $numColumns);

            $result = "INSERT INTO `$table` ({$columnsStr}) VALUES {$valuesStr}";

            $assignList = $query->get(self::ON_DUPLICATE_KEY);
            if (!empty($assignList)) {
                $result .= ' ON DUPLICATE KEY ' . QueryCompiler::compileAssignmentList('UPDATE', $assignList);
            }

            return $result;
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile INSERT query - ' . $e->getMessage(), $query, $e);
        }
    }

    /**
     * Compiles the VALUES fragment of the INSERT query.
     *
     * @psalm-mutation-free
     *
     * @param mixed $values A list of records, where each record is a list that contains the record's values.
     * @param int $numColumns The number of columns that the records should have.
     * @return string
     */
    public static function compileInsertValues($values, int $numColumns): string
    {
        /** @var array<array<string,mixed>> $values */
        if (empty($values) || !is_array($values)) {
            throw new InvalidArgumentException('VALUES list is empty or not an array');
        }

        $valuesList = [];
        foreach ($values as $i => $record) {
            if (empty($record) || !is_array($record)) {
                throw new DomainException("Value set #$i is not an array or is empty");
            } else {
                $numValues = count($record);
                if ($numValues !== $numColumns) {
                    throw new DomainException(
                        "Value set #$i has $numValues values, should have $numColumns"
                    );
                } else {
                    $recordValues = [];
                    foreach ($record as $value) {
                        $recordValues[] = Term::create($value)->toString();
                    }

                    $valuesList[] = '(' . implode(', ', $recordValues) . ')';
                }
            }
        }

        return implode(', ', $valuesList);
    }
}
