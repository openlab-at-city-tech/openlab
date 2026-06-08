<?php

declare(strict_types=1);

namespace RebelCode\Iris;

use RebelCode\Iris\Exception\ConversionException;
use RebelCode\Iris\Exception\FetchException;
use RebelCode\Iris\Exception\InvalidSourceException;
use RebelCode\Iris\Exception\StoreException;
use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Fetcher\FetchResult;

class Engine
{
    /** @var Fetcher */
    protected $fetcher;

    /** @var Converter */
    protected $converter;

    /** @var Aggregator */
    protected $aggregator;

    /** @var Store */
    protected $store;

    /**
     * Constructor.
     */
    public function __construct(
        Fetcher $fetcher,
        Converter $converter,
        Aggregator $aggregator,
        Store $store
    ) {
        $this->fetcher = $fetcher;
        $this->converter = $converter;
        $this->aggregator = $aggregator;
        $this->store = $store;
    }

    public function getFetcher(): Fetcher
    {
        return $this->fetcher;
    }

    public function getConverter(): Converter
    {
        return $this->converter;
    }

    public function getAggregator(): Aggregator
    {
        return $this->aggregator;
    }

    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @throws InvalidSourceException
     * @throws FetchException
     * @throws ConversionException
     * @throws StoreException
     */
    public function fetch(FetchQuery $query): FetchResult
    {
        $result = $this->fetcher->fetch($query->source, $query->cursor, $query->count);
        $convItems = $this->converter->convertMultiple($result->items);

        return new FetchResult(
            $convItems,
            $result->source,
            $result->catalogSize,
            $result->nextCursor,
            $result->prevCursor,
            $result->errors
        );
    }

    /**
     * @throws ConversionException
     * @throws FetchException
     * @throws InvalidSourceException
     * @throws StoreException
     */
    public function import(FetchQuery $query): FetchResult
    {
        $result = $this->fetch($query);
        $items = $this->store->insertMultiple($result->items);

        return new FetchResult(
            $items,
            $result->source,
            $result->catalogSize,
            $result->nextCursor,
            $result->prevCursor,
            $result->errors
        );
    }
}
