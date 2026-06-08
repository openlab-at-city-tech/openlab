<?php

namespace RebelCode\Atlas\Query;

use RebelCode\Atlas\Expression\ExprInterface;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryType\Update;

/** @psalm-immutable */
class UpdateQuery extends Query
{
    public function table(string $table): self
    {
        return $this->withAddedData([Update::TABLE => $table]);
    }

    public function set(array $set): self
    {
        return $this->withAddedData([Update::SET => $set]);
    }

    public function where(?ExprInterface $expr): self
    {
        return $this->withAddedData([Update::WHERE => $expr]);
    }

    public function orderBy(array $order): self
    {
        return $this->withAddedData([Update::ORDER => $order]);
    }

    public function limit(?int $limit): self
    {
        return $this->withAddedData([Update::LIMIT => $limit]);
    }
}
