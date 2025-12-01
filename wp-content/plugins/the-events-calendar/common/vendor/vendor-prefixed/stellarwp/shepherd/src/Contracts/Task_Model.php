<?php

/**
 * The Shepherd task model contract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Contracts;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Contracts;

/**
 * The Shepherd task model contract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Contracts;
 */
interface Task_Model extends Model
{
    /**
     * Sets the task's arguments.
     *
     * @since 0.0.1
     *
     * @param array $args The task's arguments.
     */
    public function set_args(array $args): void;
    /**
     * Sets the task's data.
     *
     * @since 0.0.1
     */
    public function set_data(): void;
    /**
     * Sets the task's arguments hash.
     *
     * @since 0.0.1
     */
    public function set_args_hash(): void;
    /**
     * Sets the task's class hash.
     *
     * @since 0.0.1
     */
    public function set_class_hash(): void;
    /**
     * Sets the task's action ID.
     *
     * @since 0.0.1
     *
     * @param int $action_id The task's action ID.
     */
    public function set_action_id(int $action_id): void;
    /**
     * Sets the task's current try.
     *
     * @since 0.0.1
     *
     * @param int $current_try The task's current try.
     */
    public function set_current_try(int $current_try): void;
    /**
     * Gets the task's current try.
     *
     * @since 0.0.1
     *
     * @return int The task's current try.
     */
    public function get_current_try(): int;
    /**
     * Gets the task's action ID.
     *
     * @since 0.0.1
     *
     * @return int The task's action ID.
     */
    public function get_action_id(): int;
    /**
     * Gets the task's arguments hash.
     *
     * @since 0.0.1
     *
     * @return string The task's arguments hash.
     */
    public function get_args_hash(): string;
    /**
     * Gets the task's class hash.
     *
     * @since 0.0.1
     *
     * @return string The task's class hash.
     */
    public function get_class_hash(): string;
    /**
     * Gets the task's data.
     *
     * @since 0.0.1
     *
     * @return string The task's data.
     */
    public function get_data(): string;
    /**
     * Gets the task's arguments.
     *
     * @since 0.0.1
     *
     * @return array The task's arguments.
     */
    public function get_args(): array;
    /**
     * Gets the task's hook prefix.
     *
     * SHOULD BE a maximum of 15 characters.
     *
     * @since 0.0.1
     *
     * @return string The task's prefix.
     */
    public function get_task_prefix(): string;
}