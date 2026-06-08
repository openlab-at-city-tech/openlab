<?php

namespace RebelCode\Atlas\Query;

use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryType\Insert;

/** @psalm-immutable */
class InsertQuery extends Query
{
    public function into(string $table): self
    {
        return $this->withAddedData([Insert::TABLE => $table]);
    }

    /** @param string[] $columns */
    public function columns(array $columns): self
    {
        return $this->withAddedData([Insert::COLUMNS => $columns]);
    }

    /**
     * @param array[] $values
     * @psalm-param array<string, mixed>[] $values
     */
    public function values(array $values): self
    {
        return $this->withAddedData([Insert::VALUES => $values]);
    }

    /** @param array<string, mixed> $assignList */
    public function onDuplicateKey(array $assignList): self
    {
        return $this->withAddedData([Insert::ON_DUPLICATE_KEY => $assignList]);
    }
}
