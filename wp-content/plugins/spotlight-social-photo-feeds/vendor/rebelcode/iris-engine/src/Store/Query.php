<?php

declare(strict_types=1);

namespace RebelCode\Iris\Store;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Store\Query\Condition;
use RebelCode\Iris\Store\Query\Order;

class Query
{
    /** @var Source[] */
    public $sources;

    /** @var Order|null */
    public $order;

    /** @var Condition|null */
    public $condition;

    /** @var int|null */
    public $count;

    /** @var int */
    public $offset;

    /**
     * Constructor.
     *
     * @param Source[] $sources The sources to query.
     * @param Order|null $order The order of the items.
     * @param Condition|null $condition The condition for the query.
     * @param int|null $count The maximum number of items to retrieve.
     * @param int $offset The number of items to offset by.
     */
    public function __construct(
        array $sources,
        ?Order $order = null,
        ?Condition $condition = null,
        ?int $count = null,
        int $offset = 0
    ) {
        $this->sources = $sources;
        $this->order = $order;
        $this->condition = $condition;
        $this->count = $count;
        $this->offset = $offset;
    }
}
