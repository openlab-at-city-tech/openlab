<?php

namespace RebelCode\Spotlight\Instagram\Engine\Importer;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Fetcher\FetchResult;
use RebelCode\Iris\Importer\ImportStrategy;
use RebelCode\Iris\Store;
use RebelCode\Iris\Store\Query\Order;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\HashtagSource;
use RebelCode\Spotlight\Instagram\PostTypes\MediaPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Utils\Functions;

class IgImportStrategy implements ImportStrategy
{
    /** @var Store */
    protected $store;

    /** @var ConfigEntry */
    protected $limitCfg;

    /** @var int */
    protected $batchSize;

    /** @var int */
    protected $maxHashtagItems;

    /** Constructor */
    public function __construct(Store $store, ConfigEntry $limitCfg, int $batchSize, int $maxHashtagItems)
    {
        $this->store = $store;
        $this->limitCfg = $limitCfg;
        $this->batchSize = $batchSize;
        $this->maxHashtagItems = $maxHashtagItems;
    }

    /** @inheritDoc */
    public function createFirstBatch(Source $source): ?FetchQuery
    {
        $limit = intval($this->limitCfg->getValue());

        $numToFetch = empty($limit)
            ? $this->batchSize
            : min($this->batchSize, $limit);

        return new FetchQuery($source, null, $numToFetch);
    }

    /** @inheritDoc */
    public function createNextBatch(FetchQuery $query, FetchResult $result): ?FetchQuery
    {
        $source = $query->source;
        $numItems = count($this->store->getForSources([$source]));
        $limit = intval($this->limitCfg->getValue());

        // Truncate excess items
        if (!empty($limit) && ($numItems >= $limit)) {
            if ($numItems > $limit) {
                // Query for all items, offsetting by the limit to skip the items to keep
                $query = new Store\Query([$source], new Order(Order::DESC, MediaPostType::TIMESTAMP), null, null, $limit);

                // Get IDs of the items to delete
                $toDelete = $this->store->query($query);
                $deleteIds = Arrays::map($toDelete, Functions::property('localId'));

                // Delete them
                $this->store->deleteMultiple($deleteIds);
            }

            // Do not create a new batch
            return null;
        }

        // Create query for next batch
        $nextQuery = $query->forNextBatch($result);

        // If importing for a hashtag source, make sure we don't import more than the hard-coded limit
        if (HashtagSource::isHashtagSource($nextQuery->source->type ?? '')) {
            // Calculate the new count for the new query
            $numCanFetch = max(0, $this->maxHashtagItems - $nextQuery->accrual);
            $newCount = min($numCanFetch, $nextQuery->count);

            // If zero or less, we cannot import anymore
            if ($newCount <= 0) {
                $nextQuery = null;
            } else {
                $nextQuery = new FetchQuery($nextQuery->source, $nextQuery->cursor, $newCount, $nextQuery->accrual);
            }
        }

        return $nextQuery;
    }
}
