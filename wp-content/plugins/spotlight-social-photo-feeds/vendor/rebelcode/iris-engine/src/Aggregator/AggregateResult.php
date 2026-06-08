<?php

declare(strict_types=1);

namespace RebelCode\Iris\Aggregator;

use RebelCode\Iris\Data\Item;

class AggregateResult
{
    /** @var Item[] The aggregated items after all aggregator processors are run. */
    public $items;

    /** @var int The total number of aggregated items from the store, before any processors are run. */
    public $storeTotal;

    /** @var int The total number of aggregated items after pre-processors are run. */
    public $preTotal;

    /** @var int The total number of aggregated items after post-processors are run. */
    public $postTotal;

    /**
     * Constructor.
     *
     * @param Item[] $items The aggregated items after all aggregator processors are run.
     * @param int $storeTotal The total number of aggregated items from the store, before any processors are run.
     * @param int $preTotal The total number of aggregated items after pre-processors are run.
     * @param int $postTotal The total number of aggregated items after post-processors are run.
     */
    public function __construct(array $items, int $storeTotal, int $preTotal, int $postTotal)
    {
        $this->items = $items;
        $this->storeTotal = $storeTotal;
        $this->preTotal = $preTotal;
        $this->postTotal = $postTotal;
    }
}
