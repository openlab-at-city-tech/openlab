<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Actions;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Importer;
use RebelCode\Spotlight\Instagram\ErrorLog;
use Throwable;

class BatchHandler
{
    /** @var Importer */
    protected $importer;

    /**
     * Constructor.
     *
     * @param Importer $importer The importer to use to import the batch.
     */
    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * Handles a batch cron job.
     *
     * @param mixed $arg The cron argument.
     */
    public function __invoke($arg): void
    {
        try {
            $query = $this->prepareQuery($arg);

            $this->importer->importBatch($query);
        } catch (Throwable $e) {
            ErrorLog::exception($e);
        }
    }

    /**
     * Prepares the query.
     *
     * @param mixed $arg The argument.
     * @return FetchQuery|null The query, or null if the argument is invalid.
     */
    protected function prepareQuery($arg): ?FetchQuery
    {
        if ($arg instanceof FetchQuery) {
            return $arg;
        } elseif (is_array($arg)) {
            $source = $this->prepareSource($arg['source'] ?? null);

            if ($source === null) {
                return null;
            }

            $cursor = $arg['cursor'] ?? null;
            $count = $arg['count'] ?? null;
            $accrual = $arg['accrual'] ?? 0;

            return new FetchQuery($source, $cursor, $count, $accrual);
        } else {
            ErrorLog::message("Invalid cron argument: " . Types::getType($arg));
            return null;
        }
    }

    /**
     * Prepares the query source.
     *
     * @param mixed $arg The argument.
     * @return Source|null The source, or null if the argument is invalid.
     */
    protected function prepareSource($arg): ?Source
    {
        if ($arg instanceof Source) {
            return $arg;
        } elseif (is_string($arg)) {
            return Source::fromString($arg);
        } elseif (is_array($arg)) {
            $id = $arg['id'] ?? '';
            $type = $arg['type'] ?? '';
            $data = $arg['data'] ?? [];
            return new Source($id, $type, $data);
        } else {
            ErrorLog::message("Invalid query source in cron argument: " . Types::getType($arg));
            return null;
        }
    }
}
