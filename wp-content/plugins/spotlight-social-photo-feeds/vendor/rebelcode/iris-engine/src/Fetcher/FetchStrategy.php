<?php

declare(strict_types=1);

namespace RebelCode\Iris\Fetcher;

use RebelCode\Iris\Data\Source;

interface FetchStrategy
{
    /**
     * Retrieves the resource from which items for a specific source should be fetched from.
     *
     * @psalm-mutation-free
     */
    public function getCatalog(Source $source): ?Catalog;
}
