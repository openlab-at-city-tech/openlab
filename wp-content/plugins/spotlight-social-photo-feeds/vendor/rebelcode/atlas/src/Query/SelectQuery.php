<?php

namespace RebelCode\Atlas\Query;

use RebelCode\Atlas\Expression\ExprInterface;
use RebelCode\Atlas\Group;
use RebelCode\Atlas\Order;
use RebelCode\Atlas\Query;
use RebelCode\Atlas\QueryType\Select;

/** @psalm-immutable */
class SelectQuery extends Query
{
    /** @param string|mixed $from */
    public function from($from): self
    {
        return $this->withAddedData([Select::FROM => $from]);
    }

    public function columns(array $columns): self
    {
        return $this->withAddedData([Select::COLUMNS => $columns]);
    }

    public function where(?ExprInterface $expr): self
    {
        return $this->withAddedData([Select::WHERE => $expr]);
    }

    /** @param Group[] $groups */
    public function groupBy(array $groups): self
    {
        return $this->withAddedData([Select::GROUP => $groups]);
    }

    public function having(?ExprInterface $expr): self
    {
        return $this->withAddedData([Select::HAVING => $expr]);
    }

    /** @param Order[] $order */
    public function orderBy(array $order): self
    {
        return $this->withAddedData([Select::ORDER => $order]);
    }

    public function limit(?int $limit): self
    {
        return $this->withAddedData([Select::LIMIT => $limit]);
    }

    public function offset(?int $offset): self
    {
        return $this->withAddedData([Select::OFFSET => $offset]);
    }
}
