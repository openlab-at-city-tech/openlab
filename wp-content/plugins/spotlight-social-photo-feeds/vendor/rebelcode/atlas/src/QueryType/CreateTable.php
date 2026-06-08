<?php

namespace RebelCode\Atlas\QueryType;

use InvalidArgumentException;
use RebelCode\Atlas\Exception\QueryCompileException;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryTypeInterface;
use RebelCode\Atlas\QueryUtils;
use RebelCode\Atlas\Schema;
use RebelCode\Atlas\Schema\ForeignKey;
use Throwable;
use UnexpectedValueException;

/** @psalm-immutable */
class CreateTable implements QueryTypeInterface
{
    /** @var string */
    public const IF_NOT_EXISTS = 'if_not_exists';
    /** @var string */
    public const NAME = 'name';
    /** @var string */
    public const SCHEMA = 'schema';
    /** @var string */
    public const COLLATE = 'collate';

    /** @inheritDoc */
    public function compile(Query $query): string
    {
        try {
            $tableName = QueryUtils::getTableName(self::NAME, $query);
            $schema = $query->get(self::SCHEMA);

            if (!$schema instanceof Schema) {
                throw new InvalidArgumentException('Table schema is not valid');
            }

            $ifNotExists = $query->get(self::IF_NOT_EXISTS);
            $command = 'CREATE TABLE' . ($ifNotExists ? ' IF NOT EXISTS' : '');
            $schemaStr = $this->compileSchema($schema);

            if (empty($schemaStr)) {
                throw new UnexpectedValueException('A table schema is required');
            }

            $result = "$command `$tableName` (\n  $schemaStr\n)";

            /** @var string|null $collate */
            $collate = $query->get(self::COLLATE);
            if ($collate !== null) {
                $result .= ' COLLATE ' . $collate;
            }

            return $result;
        } catch (Throwable $e) {
            throw new QueryCompileException('Cannot compile CREATE TABLE query - ' . $e->getMessage(), $query, $e);
        }
    }

    /**
     * Compiles the schema for a table.
     *
     * @psalm-mutation-free
     *
     * @param Schema $schema The table schema.
     * @return string
     */
    protected function compileSchema(Schema $schema): string
    {
        $lines = [];

        foreach ($schema->getColumns() as $name => $column) {
            $parts = ["`$name`", $column->getType()];

            $defaultVal = $column->getDefaultValue();
            if ($defaultVal !== null) {
                $parts[] = "DEFAULT $defaultVal";
            } else {
                $parts[] = $column->getIsNullable() ? 'NULL' : 'NOT NULL';
            }

            if ($column->getIsAutoInc()) {
                $parts[] = 'AUTO_INCREMENT';
            }

            $lines[] = implode(' ', $parts);
        }

        foreach ($schema->getKeys() as $name => $key) {
            $type = $key->isPrimary() ? "PRIMARY KEY" : "UNIQUE";

            $columns = $key->getColumns();
            $columnsStr = implode('`, `', $columns);

            $lines[] = "CONSTRAINT `$name` $type (`$columnsStr`)";
        }

        foreach ($schema->getForeignKeys() as $name => $foreignKey) {
            $mappings = $foreignKey->getMappings();
            $foreignTable = $foreignKey->getForeignTable();
            $updateRule = $foreignKey->getUpdateRule();
            $deleteRule = $foreignKey->getDeleteRule();

            $tableColumns = implode('`, `', array_keys($mappings));
            $foreignColumns = implode('`, `', array_values($mappings));

            $constraint = "CONSTRAINT `$name` FOREIGN KEY (`$tableColumns`) REFERENCES `$foreignTable` (`$foreignColumns`)";

            if ($updateRule !== ForeignKey::RESTRICT) {
                $constraint .= " ON UPDATE " . $updateRule;
            }

            if ($deleteRule !== ForeignKey::RESTRICT) {
                $constraint .= ' ON DELETE ' . $deleteRule;
            }

            $lines[] = $constraint;
        }

        return implode(",\n  ", $lines);
    }
}
