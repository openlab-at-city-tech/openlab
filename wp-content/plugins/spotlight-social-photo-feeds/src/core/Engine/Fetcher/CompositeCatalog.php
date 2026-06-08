<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Fetcher;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\Catalog;
use RebelCode\Iris\Fetcher\FetchResult;

/**
 * A catalog implementation that provides items from multiple other catalogs.
 */
class CompositeCatalog implements Catalog
{
    /** @var Catalog[] */
    protected $catalogs;

    /**
     * Constructor.
     *
     * @param Catalog[] $catalogs
     */
    public function __construct(array $catalogs)
    {
        $this->catalogs = $catalogs;
    }

    /** @inheritDoc */
    public function query(Source $source, ?string $cursor = null, ?int $count = null): FetchResult
    {
        $result = new FetchResult([], $source, 0);

        foreach ($this->catalogs as $catalog) {
            $subResult = $catalog->query($source, $cursor, $count);
            $result = new FetchResult(
                array_merge($result->items, $subResult->items),
                $source,
                $result->catalogSize + ($subResult->catalogSize ?? 0),
                $result->nextCursor ?? $subResult->nextCursor,
                $result->prevCursor ?? $subResult->prevCursor,
                array_merge($result->errors, $subResult->errors)
            );
        }

        return $result;
    }
}
