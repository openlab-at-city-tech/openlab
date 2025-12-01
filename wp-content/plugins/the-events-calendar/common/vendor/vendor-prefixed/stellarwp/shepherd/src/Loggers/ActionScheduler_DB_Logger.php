<?php

/**
 * Shepherd's Action Scheduler DB Logger
 *
 * @package \TEC\Common\StellarWP\Shepherd\Loggers
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Loggers;

use TEC\Common\StellarWP\Shepherd\Contracts\Logger as LoggerContract;
use TEC\Common\Psr\Log\LoggerTrait;
use TEC\Common\Psr\Log\InvalidArgumentException;
use TEC\Common\StellarWP\Shepherd\Log;
use TEC\Common\StellarWP\Shepherd\Tables\AS_Logs;
/**
 * Shepherd DB Logger
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Loggers
 */
class ActionScheduler_DB_Logger implements LoggerContract
{
    use LoggerTrait;
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
        return AS_Logs::get_by_task_id($task_id);
    }
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level   The log level.
     * @param string  $message The log message.
     * @param mixed[] $context The log context.
     *
     * @return void
     *
     * @throws InvalidArgumentException If the log level is invalid.
     */
    public function log($level, $message, array $context = [])
    {
        if (!in_array($level, Log::VALID_LEVELS, true)) {
            throw new InvalidArgumentException('Invalid log level.');
        }
        if (!isset($context['task_id'])) {
            throw new InvalidArgumentException('Task ID is required.');
        }
        if (!isset($context['type'])) {
            throw new InvalidArgumentException('Type is required.');
        }
        if (!isset($context['action_id'])) {
            throw new InvalidArgumentException('Action ID is required.');
        }
        $log = new Log();
        $log->set_task_id($context['task_id']);
        $log->set_level($level);
        $log->set_type($context['type']);
        $log->set_action_id($context['action_id']);
        unset($context['task_id'], $context['type'], $context['action_id']);
        $log->set_entry(wp_json_encode(['message' => $message, 'context' => $context]));
        $log->save();
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
        return true;
    }
}