<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Fetcher;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\Catalog;
use RebelCode\Iris\Fetcher\FetchResult;

class NullCatalog implements Catalog
{
    public function query(Source $source, ?string $cursor = null, ?int $count = null): FetchResult
    {
        return new FetchResult([], $source);
    }
}
