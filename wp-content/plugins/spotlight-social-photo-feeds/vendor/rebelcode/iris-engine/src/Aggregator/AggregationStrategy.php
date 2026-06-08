<?php

declare(strict_types=1);

namespace RebelCode\Iris\Aggregator;

use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Store\Query;

interface AggregationStrategy
{
    /**
     * Retrieves the query that the aggregator will use to obtain the items from the store.
     */
    public function getFeedQuery(Feed $feed, ?int $count = null, int $offset = 0): ?Query;

    /**
     * Retrieves the pre-processors to use for a given feed.
     *
     * Pre-processors will make modifications to the items before they become part of the aggregation result.
     * These modifications will also affect the result's {@link AggregateResult::$preTotal} count.
     *
     * It is recommended to use pre-processors to perform manipulations that rely on the feed. That is, given the same
     * feed, the same pre-processing is applied to the items regardless of the query.
     *
     * @return ItemProcessor[]
     */
    public function getPreProcessors(Feed $feed, Query $query): array;

    /**
     * Retrieves the post-processors to use for a given feed.
     *
     * Post-processors will make modifications to the list of items before they become part of the aggregation result,
     * but after the pre-processors have made already made their modifications. Post-processors will also affect the
     * result's {@link AggregateResult::$postTotal} count.
     *
     * It is recommended to use post-processors to perform manipulations that rely on the query, rather than the feed.
     * That is, the same post-processing is applied to the items regardless of the feed.
     *
     * @return ItemProcessor[]
     */
    public function getPostProcessors(Feed $feed, Query $query): array;

    /**
     * Whether the aggregator should apply pagination manually after post-processing.
     *
     * This is useful if the consumer is unable to perform all the necessary filtering using the store query which
     * is return from {@link AggregationStrategy::getFeedQuery()}. In those situations, the consumer may need to
     * perform programmatic filtering on the list of items using pre-processors or post-processors. Such consumers
     * can return true from this method to tell the aggregator to fetch all the items from the store, then apply
     * pagination manually using the query's count and offset.
     *
     * Note that in these case, the query sent to the store will have a null count and a zero offset, but the queries
     * provided to the strategy's methods will have the proper count and offset values.
     *
     * @return bool If true, the aggregator will manually apply pagination to the list of items. If false, no pagination
     *              will be applied by the aggregator.
     */
    public function doManualPagination(Feed $feed, Query $query): bool;
}
