<?php

namespace RebelCode\Atlas\Query;

use RebelCode\Atlas\Expression\ExprInterface;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryType\Delete;

/** @psalm-immutable */
class DeleteQuery extends Query
{
    public function from(string $table): self
    {
        return $this->withAddedData([Delete::FROM => $table]);
    }

    public function where(?ExprInterface $expr): self
    {
        return $this->withAddedData([Delete::WHERE => $expr]);
    }

    public function orderBy(array $order): self
    {
        return $this->withAddedData([Delete::ORDER => $order]);
    }

    public function limit(?int $limit): self
    {
        return $this->withAddedData([Delete::LIMIT => $limit]);
    }
}
