<?php

/**
 * The Shepherd loggable trait.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Traits;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Traits;

use TEC\Common\StellarWP\Shepherd\Config;
use TEC\Common\StellarWP\Shepherd\Contracts\Logger;
use TEC\Common\Psr\Log\LogLevel;
/**
 * The Shepherd loggable trait.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Traits;
 */
trait Loggable
{
    /**
     * The logger.
     *
     * @since 0.0.1
     *
     * @var Logger|null
     */
    private ?Logger $logger = null;
    /**
     * Gets the logger.
     *
     * @since 0.0.1
     *
     * @return Logger The logger.
     */
    private function get_logger(): Logger
    {
        if (!$this->logger) {
            $this->logger = Config::get_container()->get(Logger::class);
        }
        return $this->logger;
    }
    /**
     * Logs a message.
     *
     * @since 0.0.1
     *
     * @param string $level   The log level.
     * @param string $type    The log type.
     * @param int    $task_id The task ID.
     * @param string $message The message to log.
     * @param array  $data    The data to log.
     *
     * @return void The method does not return any value.
     */
    public function log(string $level, string $type, int $task_id, string $message, array $data = []): void
    {
        $data['task_id'] = $task_id;
        $data['type'] = $type;
        $this->get_logger()->log($level, $message, $data);
    }
    /**
     * Logs a created message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_created(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d created.', $task_id);
        $this->log(LogLevel::INFO, 'created', $task_id, $message, $data);
    }
    /**
     * Logs a starting message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_starting(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d starting.', $task_id);
        $this->log(LogLevel::INFO, 'started', $task_id, $message, $data);
    }
    /**
     * Logs a finished message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_finished(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d finished.', $task_id);
        $this->log(LogLevel::INFO, 'finished', $task_id, $message, $data);
    }
    /**
     * Logs a failed message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_failed(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d failed.', $task_id);
        $this->log(LogLevel::ERROR, 'failed', $task_id, $message, $data);
    }
    /**
     * Logs a rescheduled message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_rescheduled(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d rescheduled.', $task_id);
        $this->log(LogLevel::NOTICE, 'rescheduled', $task_id, $message, $data);
    }
    /**
     * Logs a cancelled message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_cancelled(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d cancelled.', $task_id);
        $this->log(LogLevel::NOTICE, 'cancelled', $task_id, $message, $data);
    }
    /**
     * Logs a retrying message.
     *
     * @since 0.0.1
     *
     * @param int    $task_id The task ID.
     * @param array  $data    The data to log.
     * @param string $message The message to log.
     *
     * @return void The method does not return any value.
     */
    public function log_retrying(int $task_id, array $data = [], string $message = ''): void
    {
        $message = $message ?: sprintf('Task %d retrying.', $task_id);
        $this->log(LogLevel::INFO, 'retrying', $task_id, $message, $data);
    }
}