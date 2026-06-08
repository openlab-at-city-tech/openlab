<?php

declare(strict_types=1);

namespace RebelCode\Iris\Importer;

use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Fetcher\FetchResult;

interface ImportScheduler
{
    /**
     * Returns the number of seconds that the PHP process is allowed to run for while importing a batch.
     */
    public function getMaxRunTime(FetchQuery $query): int;

    /**
     * Schedules an event for importing the items obtained from a given query.
     *
     * @param FetchQuery $query The query to use in the scheduled event to fetch the items to import.
     * @param callable $callback The function that should be called by the scheduled event. It has no parameters and
     *                           does not return any value.
     *
     * @return bool True if the event was scheduled, false if not.
     */
    public function scheduleBatch(FetchQuery $query, callable $callback): bool;
}
