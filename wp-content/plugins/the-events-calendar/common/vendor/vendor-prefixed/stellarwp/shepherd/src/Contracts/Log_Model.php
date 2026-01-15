<?php

/**
 * The Shepherd log model contract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Contracts
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Contracts;

use DateTimeInterface;
/**
 * The Shepherd log model contract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Contracts
 */
interface Log_Model extends Model
{
    /**
     * Sets the task ID.
     *
     * @since 0.0.1
     *
     * @param int $task_id The task ID.
     *
     * @return void The method does not return any value.
     */
    public function set_task_id(int $task_id): void;
    /**
     * Sets the date.
     *
     * @since 0.0.1
     *
     * @param DateTimeInterface $date The date.
     *
     * @return void The method does not return any value.
     */
    public function set_date(DateTimeInterface $date): void;
    /**
     * Sets the level.
     *
     * @since 0.0.1
     *
     * @param string $level The level.
     *
     * @return void The method does not return any value.
     */
    public function set_level(string $level): void;
    /**
     * Sets the type.
     *
     * @since 0.0.1
     *
     * @param string $type The type.
     *
     * @return void The method does not return any value.
     */
    public function set_type(string $type): void;
    /**
     * Sets the entry.
     *
     * @since 0.0.1
     *
     * @param string $entry The entry.
     *
     * @return void The method does not return any value.
     */
    public function set_entry(string $entry): void;
    /**
     * Gets the task ID.
     *
     * @since 0.0.1
     *
     * @return int The task ID.
     */
    public function get_task_id(): int;
    /**
     * Gets the date.
     *
     * @since 0.0.1
     *
     * @return DateTimeInterface The date.
     */
    public function get_date(): DateTimeInterface;
    /**
     * Gets the level.
     *
     * @since 0.0.1
     *
     * @return string The level.
     */
    public function get_level(): string;
    /**
     * Gets the type.
     *
     * @since 0.0.1
     *
     * @return string The type.
     */
    public function get_type(): string;
    /**
     * Gets the entry.
     *
     * @since 0.0.1
     *
     * @return string The entry.
     */
    public function get_entry(): string;
}