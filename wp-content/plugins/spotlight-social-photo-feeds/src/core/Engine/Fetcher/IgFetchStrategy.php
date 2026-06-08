<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Fetcher;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\Catalog;
use RebelCode\Iris\Fetcher\FetchStrategy;

class IgFetchStrategy implements FetchStrategy
{
    /** @var array<string, Catalog> */
    protected $catalogMap;

    /** @var Catalog|null */
    protected $fallback;

    /**
     * Constructor.
     *
     * @param array<string, Catalog> $sourceCatalogMap
     * @param Catalog|null $fallback
     */
    public function __construct(array $sourceCatalogMap, ?Catalog $fallback = null)
    {
        $this->catalogMap = $sourceCatalogMap;
        $this->fallback = $fallback;
    }

    public function getCatalog(Source $source): ?Catalog
    {
        return $this->catalogMap[$source->type] ?? $this->fallback;
    }
}
