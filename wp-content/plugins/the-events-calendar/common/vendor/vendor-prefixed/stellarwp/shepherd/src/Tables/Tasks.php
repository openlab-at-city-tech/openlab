<?php

/**
 * The Tasks table schema.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Shepherd\Tables;
 */
namespace TEC\Common\StellarWP\Shepherd\Tables;

use TEC\Common\StellarWP\Shepherd\Abstracts\Table_Abstract;
use TEC\Common\StellarWP\Shepherd\Contracts\Task;
use TEC\Common\StellarWP\Schema\Collections\Column_Collection;
use TEC\Common\StellarWP\Schema\Columns\ID;
use TEC\Common\StellarWP\Schema\Columns\Referenced_ID;
use TEC\Common\StellarWP\Schema\Columns\String_Column;
use TEC\Common\StellarWP\Schema\Columns\Text_Column;
use TEC\Common\StellarWP\Schema\Columns\Integer_Column;
use TEC\Common\StellarWP\Schema\Tables\Table_Schema;
use InvalidArgumentException;
/**
 * Tasks table schema.
 *
 * @since 0.0.1
 * @since 0.0.8 Updated to be compatible with the updated contract.
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tables;
 */
class Tasks extends Table_Abstract
{
    /**
     * The schema version.
     *
     * @since 0.0.1
     * @since 0.0.3 Updated to 0.0.2.
     * @since 0.0.7 Updated to 0.0.3 to fix typo in the version string.
     *
     * @var string
     */
    const SCHEMA_VERSION = '0.0.3';
    /**
     * The base table name, without the table prefix.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $base_table_name = 'shepherd_%s_tasks';
    /**
     * The table group.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $group = 'stellarwp_shepherd';
    /**
     * The slug used to identify the custom table.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $schema_slug = 'stellarwp-shepherd-%s-tasks';
    /**
     * The field that uniquely identifies a row in the table.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $uid_column = 'id';
    /**
     * Gets the schema history for the table.
     *
     * @since 0.0.8
     *
     * @return array<string, callable> The schema history for the table.
     */
    public static function get_schema_history(): array
    {
        return [self::SCHEMA_VERSION => [self::class, 'get_schema_version_0_0_3']];
    }
    /**
     * Gets the schema for version 0.0.3.
     *
     * @since 0.0.8
     *
     * @return Table_Schema The schema for version 0.0.3.
     */
    public static function get_schema_version_0_0_3(): Table_Schema
    {
        $columns = new Column_Collection();
        $columns[] = new ID('id');
        $columns[] = new Referenced_ID('action_id');
        $columns[] = (new String_Column('class_hash'))->set_length(191)->set_is_index(true);
        $columns[] = (new String_Column('args_hash'))->set_length(191)->set_is_index(true);
        $columns[] = (new Text_Column('data'))->set_nullable(true);
        $columns[] = (new Integer_Column('current_try'))->set_length(20)->set_signed(false)->set_default(0);
        return new Table_Schema(self::table_name(true), $columns);
    }
    /**
     * Gets a task by its action ID.
     *
     * @since 0.0.1
     *
     * @param int $action_id  The action ID.
     *
     * @return ?Task The task, or null if not found.
     *
     * @throws InvalidArgumentException If the task class does not implement the Task interface.
     */
    public static function get_by_action_id(int $action_id): ?Task
    {
        /** @var Task|null */
        return self::get_first_by('action_id', $action_id);
    }
    /**
     * Gets a task by its arguments hash.
     *
     * @since 0.0.1
     *
     * @param string $args_hash The arguments hash.
     *
     * @return Task[] The tasks, or an empty array if no tasks are found.
     */
    public static function get_by_args_hash(string $args_hash): array
    {
        /** @var Task[]|null */
        return self::get_all_by('args_hash', $args_hash);
    }
    /**
     * Gets a task from an array.
     *
     * @since 0.0.1
     *
     * @param array<string, mixed> $task_array The task array.
     *
     * @return Task The task.
     *
     * @throws InvalidArgumentException If the task class does not exist or does not implement the Task interface.
     */
    public static function transform_from_array(array $task_array): Task
    {
        $task_data = json_decode($task_array['data'] ?? '[]', true);
        $task_class = $task_data['task_class'] ?? '';
        if (!$task_class || !class_exists($task_class)) {
            throw new InvalidArgumentException('The task class does not exist.');
        }
        $task = new $task_class(...$task_data['args'] ?? []);
        if (!$task instanceof Task) {
            throw new InvalidArgumentException('The task class does not implement the Task interface.');
        }
        $task->set_id($task_array[self::$uid_column]);
        $task->set_action_id($task_array['action_id']);
        $task->set_current_try($task_array['current_try']);
        return $task;
    }
}