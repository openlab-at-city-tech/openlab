<?php

declare(strict_types=1);

namespace RebelCode\Iris;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Exception\ConversionException;
use RebelCode\Iris\Exception\FetchException;
use RebelCode\Iris\Exception\InvalidSourceException;
use RebelCode\Iris\Exception\StoreException;
use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Fetcher\FetchResult;
use RebelCode\Iris\Importer\ImportedBatch;
use RebelCode\Iris\Importer\ImportStrategy;
use RebelCode\Iris\Utils\Marker;
use RebelCode\Iris\Importer\ImportScheduler;

class Importer
{
    /** @var Engine */
    protected $engine;

    /** @var ImportStrategy */
    protected $strategy;

    /** @var ImportScheduler */
    protected $scheduler;

    /** @var Marker */
    protected $lock;

    /** @var Marker */
    protected $interrupt;

    /**
     * Constructor.
     *
     * @param Engine $engine The engine instance.
     * @param ImportStrategy $strategy The import strategy to use.
     * @param ImportScheduler $scheduler The scheduler to use, for batching.
     * @param Marker $lock A lock mutex, to protect against concurrent imports.
     * @param Marker $interrupt An interrupt marker, to allow 3rd party code to interrupt the import process.
     */
    public function __construct(
        Engine $engine,
        ImportStrategy $strategy,
        ImportScheduler $scheduler,
        Marker $lock,
        Marker $interrupt
    ) {
        $this->engine = $engine;
        $this->strategy = $strategy;
        $this->scheduler = $scheduler;
        $this->lock = $lock;
        $this->interrupt = $interrupt;
    }

    /**
     * Imports batches of items for a list of sources.
     *
     * @param Source[] $sources The sources for which to import items.
     */
    public function importForSources(array $sources): ImportedBatch
    {
        $result = new ImportedBatch([], [], false);

        foreach ($sources as $source) {
            $query = $this->strategy->createFirstBatch($source);
            if ($query !== null) {
                $batch = $this->importBatch($query);
                $result = $result->mergeWith($batch);
            }
        }

        return $result;
    }

    /**
     * Imports a batch ot items.
     *
     * @param FetchQuery $query The fetch query for the items to import.
     *
     * @return ImportedBatch The imported batch as a result of importing the items fetched from the given query.
     *
     * @throws InvalidSourceException
     * @throws FetchException
     * @throws ConversionException
     * @throws StoreException
     */
    public function importBatch(FetchQuery $query): ImportedBatch
    {
        // Stop if the lock marker is set
        if ($this->lock->isSet()) {
            return new ImportedBatch([], [], false);
        }

        // Create the lock marker
        $this->lock->create();

        // When execution ends, delete the lock marker
        register_shutdown_function(function (): void {
            $this->lock->delete();
        });

        try {
            set_time_limit($this->scheduler->getMaxRunTime($query));

            // Run the import
            $fetchResult = $this->engine->fetch($query);
            $insertedItems = $this->engine->getStore()->insertMultiple($fetchResult->items);

            $result = new FetchResult(
                $insertedItems,
                $fetchResult->source,
                $fetchResult->catalogSize,
                $fetchResult->nextCursor,
                $fetchResult->prevCursor,
                $fetchResult->errors
            );

            if (!$this->interrupt->isSet()) {
                // Schedule the next batch if not interrupted and a new batch was created successfully
                $nextQuery = $this->strategy->createNextBatch($query, $result);
                $hasNext = $nextQuery !== null;

                if ($hasNext) {
                    $hasNext = $this->scheduler->scheduleBatch($nextQuery, function () use ($nextQuery): void {
                        $this->importBatch($nextQuery);
                    });
                }
            } else {
                $hasNext = false;
            }
        } finally {
            // Remove the lock and interrupt marker
            $this->lock->delete();
            $this->interrupt->delete();
        }

        return new ImportedBatch($result->items, $result->errors, $hasNext);
    }
}
