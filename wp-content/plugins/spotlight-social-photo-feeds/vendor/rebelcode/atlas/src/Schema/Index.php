<?php

namespace RebelCode\Atlas\Schema;

use RebelCode\Atlas\Order;

/** @psalm-immutable */
class Index
{
    /** @var bool */
    protected $isUnique;

    /**
     * @var array<string,string|null>
     * @psalm-var array<string, Order::*|null>
     */
    protected $columns;

    /**
     * Constructor.
     *
     * @param bool $isUnique Whether the index is unique.
     * @param array<string, string|null> $columns A mapping of column names to their respective sorting. The sorting
     *                                           should be one of the constants in {@link Order}.
     *
     * @psalm-param array<string, Order::*|null> $columns
     */
    public function __construct(bool $isUnique, array $columns)
    {
        $this->isUnique = $isUnique;
        $this->columns = $columns;
    }

    /** @return bool */
    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    /**
     * @return array<string, string|null>
     * @psalm-return array<string, Order::*|null>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
