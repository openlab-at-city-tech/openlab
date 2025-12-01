<?php

/**
 * Shepherd Null Logger
 *
 * @package \TEC\Common\StellarWP\Shepherd\Loggers
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Loggers;

use TEC\Common\StellarWP\Shepherd\Contracts\Logger as LoggerContract;
use TEC\Common\Psr\Log\NullLogger;
use TEC\Common\StellarWP\Shepherd\Log;
/**
 * Shepherd Null Logger
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Loggers
 */
class Null_Logger extends NullLogger implements LoggerContract
{
    /**
     * Retrieves the logs for a given task.
     *
     * @since 0.0.1
     *
     * @param int $task_id The ID of the task.
     *
     * @return Log[] The logs for the task.
     */
    public function retrieve_logs(int $task_id): array
    {
        return [];
    }
    /**
     * Indicates if the logger uses its own table.
     *
     * @since 0.0.8
     *
     * @return bool
     */
    public function uses_own_table(): bool
    {
        return false;
    }
    /**
     * Indicates if the logger uses the Action Scheduler table.
     *
     * @since 0.0.8
     *
     * @return bool
     */
    public function uses_as_table(): bool
    {
        return false;
    }
}