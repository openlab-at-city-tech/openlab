<?php

/**
 * The Shepherd task model abstract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Abstracts;

use TEC\Common\StellarWP\Shepherd\Contracts\Task_Model;
use RuntimeException;
use TEC\Common\StellarWP\Shepherd\Tables\Tasks as Tasks_Table;
use TEC\Common\StellarWP\Shepherd\Config;
use TEC\Common\StellarWP\Shepherd\Contracts\Task;
use TEC\Common\StellarWP\Shepherd\Action_Scheduler_Methods;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskAlreadyExistsException;
use TEC\Common\StellarWP\Shepherd\Tasks\Herding;
/**
 * The Shepherd task model abstract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts;
 */
abstract class Task_Model_Abstract extends Model_Abstract implements Task_Model
{
    /**
     * The table interface for the task.
     *
     * @since 0.0.1
     *
     * @var string
     */
    public const TABLE_INTERFACE = Tasks_Table::class;
    /**
     * The task's action ID.
     *
     * @since 0.0.1
     *
     * @var int
     */
    private int $action_id = 0;
    /**
     * The task's class hash.
     *
     * @since 0.0.1
     *
     * @var string
     */
    private string $class_hash;
    /**
     * The task's arguments hash.
     *
     * @since 0.0.1
     *
     * @var string
     */
    private string $args_hash;
    /**
     * The task's data.
     *
     * @since 0.0.1
     *
     * @var string
     */
    private string $data;
    /**
     * The task's current try.
     *
     * We start at 1 because the first try is the initial try.
     *
     * @since 0.0.1
     *
     * @var int
     */
    private int $current_try = 0;
    /**
     * The task's constructor arguments.
     *
     * @since 0.0.1
     *
     * @var array<mixed>
     */
    private array $args;
    /**
     * Sets the task's action ID.
     *
     * @since 0.0.1
     *
     * @param int $action_id The task's action ID.
     */
    public function set_action_id(int $action_id): void
    {
        $this->action_id = $action_id;
    }
    /**
     * Sets the task's class hash.
     *
     * @since 0.0.1
     */
    public function set_class_hash(): void
    {
        $this->class_hash = md5(static::class);
    }
    /**
     * Sets the task's arguments hash.
     *
     * @since 0.0.1
     *
     * @throws RuntimeException If the task prefix is longer than 15 characters.
     */
    public function set_args_hash(): void
    {
        $task_prefix = $this->get_task_prefix();
        if (strlen($task_prefix) > 15) {
            throw new RuntimeException('The task prefix must be a maximum of 15 characters.');
        }
        $this->args_hash = $task_prefix . md5(wp_json_encode(array_merge([static::class], $this->args)));
    }
    /**
     * Sets the task's data.
     *
     * @since 0.0.1
     */
    public function set_data(): void
    {
        $this->data = wp_json_encode(['args' => $this->args, 'task_class' => static::class]);
    }
    /**
     * Sets the task's current try.
     *
     * @since 0.0.1
     *
     * @param int $current_try The task's current try.
     */
    public function set_current_try(int $current_try): void
    {
        $this->current_try = $current_try;
    }
    /**
     * Sets the task's arguments.
     *
     * @since 0.0.1
     *
     * @param array $args The task's arguments.
     */
    public function set_args(array $args): void
    {
        $this->args = $args;
        $this->set_args_hash();
    }
    /**
     * Gets the task's action ID.
     *
     * @since 0.0.1
     *
     * @return int The task's action ID.
     */
    public function get_action_id(): int
    {
        return $this->action_id;
    }
    /**
     * Gets the task's class hash.
     *
     * @since 0.0.1
     *
     * @return string The task's class hash.
     */
    public function get_class_hash(): string
    {
        return $this->class_hash;
    }
    /**
     * Gets the task's arguments hash.
     *
     * @since 0.0.1
     *
     * @return string The task's arguments hash.
     */
    public function get_args_hash(): string
    {
        return $this->args_hash;
    }
    /**
     * Gets the task's data.
     *
     * @since 0.0.1
     *
     * @return string The task's data.
     */
    public function get_data(): string
    {
        return $this->data;
    }
    /**
     * Gets the task's current try.
     *
     * @since 0.0.1
     *
     * @return int The task's current try.
     */
    public function get_current_try(): int
    {
        return $this->current_try;
    }
    /**
     * Gets the task's arguments.
     *
     * @since 0.0.1
     *
     * @return array The task's arguments.
     */
    public function get_args(): array
    {
        return $this->args;
    }
    /**
     * Gets the table interface for the task.
     *
     * @since 0.0.1
     *
     * @return Table_Abstract The table interface.
     */
    public function get_table_interface(): Table_Abstract
    {
        return Config::get_container()->get(static::TABLE_INTERFACE);
    }
    /**
     * Saves the task.
     *
     * @since 0.0.1
     * @since 0.0.8 Updated to delete stale tasks from the database.
     *
     * @return int The id of the saved task.
     *
     * @throws ShepherdTaskAlreadyExistsException If multiple tasks are found with the same arguments hash.
     * @throws RuntimeException                   If multiple tasks are found with the same arguments hash.
     */
    public function save(): int
    {
        $task_id = parent::save();
        $table_interface = Config::get_container()->get(static::TABLE_INTERFACE);
        $tasks = $table_interface::get_by_args_hash($this->get_args_hash());
        if (count($tasks) === 1) {
            return $task_id;
        }
        $action_ids = array_map(fn(Task $task) => $task->get_action_id(), $tasks);
        [$pending_actions, $non_pending_actions] = Action_Scheduler_Methods::get_pending_and_non_pending_actions_by_ids($action_ids);
        $stale_task_ids = array_map(fn(Task $task) => $task->get_id(), array_filter($tasks, fn(Task $task) => in_array($task->get_action_id(), array_keys($non_pending_actions), true)));
        if (!empty($stale_task_ids)) {
            Herding::delete_data_of_tasks($stale_task_ids);
        }
        if (count($pending_actions) > 1) {
            throw new RuntimeException(esc_html_x('Multiple tasks found with the same arguments hash.', 'This error is thrown when multiple tasks are found with the same arguments hash while they are also pending.', 'stellarwp-shepherd'));
        }
        $number_of_actions = count($action_ids);
        $number_of_unique_actions = count(array_unique($action_ids));
        if ($number_of_actions !== $number_of_unique_actions) {
            throw new ShepherdTaskAlreadyExistsException(esc_html_x('Multiple tasks found with the same arguments hash.', 'This error is thrown when multiple tasks are found with the same arguments hash.', 'stellarwp-shepherd'));
        }
        return $task_id;
    }
}