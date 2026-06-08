<?php

declare(strict_types=1);

namespace RebelCode\Iris;

use RebelCode\Iris\Aggregator\AggregateResult;
use RebelCode\Iris\Aggregator\AggregationStrategy;
use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Exception\StoreException;
use RebelCode\Iris\Store\Query;

class Aggregator
{
    /** @var Store */
    protected $store;

    /** @var AggregationStrategy */
    protected $strategy;

    /**
     * Constructor.
     */
    public function __construct(Store $store, AggregationStrategy $strategy)
    {
        $this->store = $store;
        $this->strategy = $strategy;
    }

    /**
     * @throws StoreException
     */
    public function aggregate(Feed $feed, ?int $count = null, int $offset = 0): AggregateResult
    {
        $query = $this->strategy->getFeedQuery($feed, $count, $offset);

        if ($query === null) {
            return new AggregateResult([], 0, 0, 0);
        }

        $manualPagination = $this->strategy->doManualPagination($feed, $query);

        $storeQuery = $manualPagination
            ? new Query($query->sources, $query->order, $query->condition)
            : $query;

        $items = $this->store->query($storeQuery);
        $this->removeDuplicates($items);

        $storeTotal = count($items);

        $preProcessors = $this->strategy->getPreProcessors($feed, $query);
        foreach ($preProcessors as $processor) {
            $processor->process($items, $feed, $query);
        }

        $preTotal = count($items);

        $postProcessors = $this->strategy->getPostProcessors($feed, $query);
        foreach ($postProcessors as $processor) {
            $processor->process($items, $feed, $query);
        }

        $postTotal = count($items);

        if ($manualPagination) {
            /** @psalm-suppress RedundantConditionGivenDocblockType, DocblockTypeContradiction */
            $items = array_slice($items, $query->offset ?? 0, $query->count);
        } else {
            // Make sure that the list of items is not greater than the query's count after post-processing
            $count = max(0, $query->count ?? 0);
            if ($count > 0) {
                $items = array_slice($items, 0, $count);
            }
        }

        return new AggregateResult($items, $storeTotal, $preTotal, $postTotal);
    }

    /**
     * Removes duplicate items that share the same ID.
     *
     * Note: this method takes the item list by reference for performance reasons.
     *
     * @param Item[] $items The list of items.
     */
    protected function removeDuplicates(array &$items): void
    {
        $unique = [];
        foreach ($items as $item) {
            $unique[$item->id] = $item;
        }
        $items = array_values($unique);
    }
}
