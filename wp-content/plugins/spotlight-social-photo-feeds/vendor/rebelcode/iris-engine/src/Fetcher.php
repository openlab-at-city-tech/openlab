<?php

declare(strict_types=1);

namespace RebelCode\Iris;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Exception\FetchException;
use RebelCode\Iris\Exception\InvalidSourceException;
use RebelCode\Iris\Fetcher\FetchResult;
use RebelCode\Iris\Fetcher\FetchStrategy;

class Fetcher
{
    /** @var FetchStrategy */
    protected $strategy;

    /**
     * Constructor.
     */
    public function __construct(FetchStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @psalm-mutation-free
     *
     * @throws FetchException
     * @throws InvalidSourceException
     */
    public function fetch(Source $source, ?string $cursor = null, ?int $count = null): FetchResult
    {
        $catalog = $this->strategy->getCatalog($source);

        if ($catalog === null) {
            throw new InvalidSourceException("No catalog found for source of type \"{$source->type}\"", $source);
        }

        return $catalog->query($source, $cursor, $count);
    }
}
