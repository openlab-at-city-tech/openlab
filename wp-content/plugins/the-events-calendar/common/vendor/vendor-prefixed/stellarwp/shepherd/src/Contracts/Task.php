<?php

/**
 * Shepherd's task contract.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Contracts;

/**
 * Shepherd's task contract.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
interface Task extends Task_Model
{
    /**
     * Processes the task.
     *
     * @since 0.0.1
     */
    public function process(): void;
    /**
     * Gets the task's group.
     *
     * @since 0.0.1
     *
     * @return string The task's group.
     */
    public function get_group(): string;
    /**
     * Gets the task's priority.
     *
     * @since 0.0.1
     *
     * @return int The task's priority.
     */
    public function get_priority(): int;
    /**
     * Gets the maximum number of retries.
     *
     * 0 means the task is not retryable, while less than 0 means the task is retryable indefinitely.
     *
     * @since 0.0.1
     *
     * @return int The maximum number of retries.
     */
    public function get_max_retries(): int;
    /**
     * Gets the task's retry delay.
     *
     * @since 0.0.1
     *
     * @return int The task's retry delay in seconds.
     */
    public function get_retry_delay(): int;
}