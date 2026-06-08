<?php

declare(strict_types=1);

namespace RebelCode\Iris\Aggregator;

use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Store\Query;

interface ItemProcessor
{
    /**
     * @param Item[] $items
     */
    public function process(array &$items, Feed $feed, Query $query): void;
}
