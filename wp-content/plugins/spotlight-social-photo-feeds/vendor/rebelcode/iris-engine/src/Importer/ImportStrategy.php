<?php

declare(strict_types=1);

namespace RebelCode\Iris\Importer;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Fetcher\FetchResult;

interface ImportStrategy
{
    /**
     * Creates the fetch query for the first batch for a source.
     *
     * @param Source $source The source for which to create the import batch.
     *
     * @return FetchQuery|null The fetch query for the first batch, or null if no batch can be created for this source.
     */
    public function createFirstBatch(Source $source): ?FetchQuery;

    /**
     * Creates the fetch query for the next batch.
     *
     * @param FetchQuery $query The query that was just fetched.
     * @param FetchResult $result The result of importing the batch for the $query argument.
     *
     * @return FetchQuery|null The fetch query for the next batch, or null if no more batches can/should be fetched.
     */
    public function createNextBatch(FetchQuery $query, FetchResult $result): ?FetchQuery;
}
