<?php

namespace RebelCode\Spotlight\Instagram\Engine\Importer;

use RebelCode\Iris\Fetcher\FetchQuery;
use RebelCode\Iris\Importer\ImportScheduler;
use RebelCode\Spotlight\Instagram\Wp\CronJob;

class WpCronScheduler implements ImportScheduler
{
    /** @var string */
    protected $cronHook;

    /** @var int */
    protected $delay;

    /** @var int */
    protected $maxRunTime;

    /** Constructor */
    public function __construct(string $cronHook, int $delay, int $maxRunTime)
    {
        $this->cronHook = $cronHook;
        $this->delay = $delay;
        $this->maxRunTime = $maxRunTime;
    }

    /** @inheritDoc */
    public function getMaxRunTime(FetchQuery $query): int
    {
        return $this->maxRunTime;
    }

    /** @inheritDoc */
    public function scheduleBatch(FetchQuery $query, callable $callback): bool
    {
        $job = new CronJob($this->cronHook, [$query]);
        CronJob::schedule($job, time() + $this->delay);

        return true;
    }
}
