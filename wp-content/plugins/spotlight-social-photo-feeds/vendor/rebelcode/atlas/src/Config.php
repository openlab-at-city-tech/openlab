<?php

namespace RebelCode\Atlas;

use RebelCode\Atlas\QueryType\CreateIndex;
use RebelCode\Atlas\QueryType\CreateTable;
use RebelCode\Atlas\QueryType\Delete;
use RebelCode\Atlas\QueryType\DropTable;
use RebelCode\Atlas\QueryType\Insert;
use RebelCode\Atlas\QueryType\Select;
use RebelCode\Atlas\QueryType\Update;

/** @psalm-immutable */
class Config
{
    /** @var array<string, QueryTypeInterface> */
    protected $queryTypes;

    /** @param array<string, QueryTypeInterface> $types */
    public function __construct(array $types)
    {
        $this->queryTypes = $types;
    }

    /** @return array<string, QueryTypeInterface> */
    public function getQueryTypes(): array
    {
        return $this->queryTypes;
    }

    /** @psalm-mutation-free */
    public function getQueryType(string $key): ?QueryTypeInterface
    {
        return $this->queryTypes[$key] ?? null;
    }

    /** @param array<string, QueryTypeInterface> $overrides */
    public function withOverrides(array $overrides): self
    {
        return new self(array_merge($this->queryTypes, $overrides));
    }

    public static function createDefault(): self
    {
        return new self([
            QueryType::CREATE_TABLE => new CreateTable(),
            QueryType::CREATE_INDEX => new CreateIndex(),
            QueryType::DROP_TABLE => new DropTable(),
            QueryType::SELECT => new Select(),
            QueryType::INSERT => new Insert(),
            QueryType::UPDATE => new Update(),
            QueryType::DELETE => new Delete(),
        ]);
    }
}
