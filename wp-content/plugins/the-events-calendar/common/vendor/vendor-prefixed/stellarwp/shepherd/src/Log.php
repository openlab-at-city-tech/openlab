<?php

/**
 * The Shepherd log model abstract.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd;

use TEC\Common\StellarWP\Shepherd\Config;
use TEC\Common\StellarWP\Shepherd\Contracts\Log_Model;
use TEC\Common\StellarWP\Shepherd\Tables\Task_Logs as Task_Logs_Table;
use TEC\Common\StellarWP\Shepherd\Tables\AS_Logs as AS_Logs_Table;
use TEC\Common\StellarWP\Shepherd\Contracts\Logger;
use TEC\Common\StellarWP\Shepherd\Abstracts\Model_Abstract;
use DateTimeInterface;
use TEC\Common\StellarWP\Schema\Tables\Contracts\Table;
use TEC\Common\Psr\Log\LogLevel;
use InvalidArgumentException;
use DateTime;
use RuntimeException;
use TEC\Common\StellarWP\DB\DB;
/**
 * The Shepherd log model abstract.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
class Log extends Model_Abstract implements Log_Model
{
    /**
     * The table interface for the log.
     *
     * @since 0.0.1
     *
     * @var string
     */
    public const TABLE_INTERFACE = Task_Logs_Table::class;
    /**
     * The valid log levels.
     *
     * @since 0.0.1
     *
     * @var array<string>
     */
    public const VALID_LEVELS = [LogLevel::INFO, LogLevel::WARNING, LogLevel::ERROR, LogLevel::DEBUG, LogLevel::EMERGENCY, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::NOTICE];
    /**
     * The valid log types.
     *
     * @since 0.0.1
     *
     * @var array<string>
     */
    public const VALID_TYPES = ['created', 'started', 'finished', 'failed', 'rescheduled', 'cancelled', 'retrying'];
    /**
     * The task ID.
     *
     * @since 0.0.1
     *
     * @var int
     */
    protected int $task_id = 0;
    /**
     * The action ID.
     *
     * @since 0.0.1
     *
     * @var int
     */
    protected int $action_id = 0;
    /**
     * The date.
     *
     * @since 0.0.1
     *
     * @var DateTimeInterface
     */
    protected ?DateTimeInterface $date = null;
    /**
     * The level.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected string $level;
    /**
     * The type.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected string $type;
    /**
     * The entry.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected string $entry;
    /**
     * Sets the task ID.
     *
     * @since 0.0.1
     *
     * @param int $task_id The task ID.
     */
    public function set_task_id(int $task_id): void
    {
        $this->task_id = $task_id;
    }
    /**
     * Sets the action ID.
     *
     * @since 0.0.1
     *
     * @param int $action_id The action ID.
     */
    public function set_action_id(int $action_id): void
    {
        $this->action_id = $action_id;
    }
    /**
     * Sets the date.
     *
     * @since 0.0.1
     *
     * @param DateTimeInterface $date The date.
     */
    public function set_date(DateTimeInterface $date): void
    {
        $this->date = $date;
    }
    /**
     * Sets the level.
     *
     * @since 0.0.1
     *
     * @param string $level The level.
     *
     * @throws InvalidArgumentException If the log level is invalid.
     */
    public function set_level(string $level): void
    {
        if (!in_array($level, self::VALID_LEVELS, true)) {
            throw new InvalidArgumentException('Invalid log level.');
        }
        $this->level = $level;
    }
    /**
     * Sets the type.
     *
     * @since 0.0.1
     *
     * @param string $type The type.
     *
     * @throws InvalidArgumentException If the log type is invalid.
     */
    public function set_type(string $type): void
    {
        if (!in_array($type, self::VALID_TYPES, true)) {
            throw new InvalidArgumentException('Invalid log type.');
        }
        $this->type = $type;
    }
    /**
     * Sets the entry.
     *
     * @since 0.0.1
     *
     * @param string $entry The entry.
     */
    public function set_entry(string $entry): void
    {
        $this->entry = trim($entry);
    }
    /**
     * Gets the task ID.
     *
     * @since 0.0.1
     *
     * @return int The task ID.
     */
    public function get_task_id(): int
    {
        return $this->task_id;
    }
    /**
     * Gets the action ID.
     *
     * @since 0.0.1
     *
     * @return int The action ID.
     */
    public function get_action_id(): int
    {
        return $this->action_id;
    }
    /**
     * Gets the date.
     *
     * @since 0.0.1
     *
     * @return DateTimeInterface The date.
     */
    public function get_date(): DateTimeInterface
    {
        return $this->date ?? new DateTime();
    }
    /**
     * Gets the level.
     *
     * @since 0.0.1
     *
     * @return string The level.
     */
    public function get_level(): string
    {
        return $this->level;
    }
    /**
     * Gets the type.
     *
     * @since 0.0.1
     *
     * @return string The type.
     */
    public function get_type(): string
    {
        return $this->type;
    }
    /**
     * Gets the entry.
     *
     * @since 0.0.1
     *
     * @return string The entry.
     */
    public function get_entry(): string
    {
        return $this->entry;
    }
    /**
     * Gets the table interface for the log.
     *
     * @since 0.0.1
     * @since 0.0.8 Updated to return Table instead.
     *
     * @return Table The table interface.
     *
     * @throws RuntimeException If the log table interface is invalid.
     */
    public function get_table_interface(): Table
    {
        $logger = Config::get_container()->get(Logger::class);
        $table = null;
        if ($logger->uses_as_table()) {
            $table = AS_Logs_Table::class;
        }
        if ($logger->uses_own_table()) {
            $table = Task_Logs_Table::class;
        }
        $table = apply_filters('shepherd_' . Config::get_hook_prefix() . '_log_table_interface', $table, $logger);
        if (!is_string($table) || !class_exists($table)) {
            throw new RuntimeException('Invalid log table interface.');
        }
        return Config::get_container()->get($table);
    }
    /**
     * Converts the model to an array.
     *
     * @since 0.0.1
     *
     * @return array The model as an array.
     */
    public function to_array(): array
    {
        $table_interface = Task_Logs_Table::class;
        $columns = $table_interface::get_columns()->get_names();
        $model = [];
        foreach ($columns as $column) {
            $method = 'get_' . $column;
            $model[$column] = $this->{$method}();
        }
        $uid_column = $table_interface::uid_column();
        if (empty($model[$uid_column])) {
            unset($model[$uid_column]);
        }
        return $model;
    }
    /**
     * Converts the model to an array for saving.
     *
     * @since 0.0.1
     *
     * @return array The model as an array for saving.
     */
    public function to_array_for_save(): array
    {
        $model = $this->to_array();
        if ($this->get_table_interface() instanceof Task_Logs_Table) {
            return $model;
        }
        if (isset($model['date'])) {
            $model['log_date_gmt'] = $model['date'];
            unset($model['date']);
        }
        if (isset($model['id'])) {
            $model['log_id'] = $model['id'];
            unset($model['id']);
        }
        $model['message'] = 'shepherd_' . Config::get_hook_prefix() . '||' . $model['task_id'] . '||' . $model['type'] . '||' . $model['level'] . '||' . $model['entry'];
        unset($model['entry'], $model['task_id'], $model['type'], $model['level']);
        return $model;
    }
    /**
     * Saves the model.
     *
     * @since 0.0.1
     *
     * @return int The id of the saved model.
     *
     * @throws RuntimeException If the model fails to save.
     */
    public function save(): int
    {
        $table_interface = $this->get_table_interface();
        $result = $table_interface::upsert($this->to_array_for_save());
        if (!$result) {
            throw new RuntimeException('Failed to save the model.');
        }
        $id = $this->get_id();
        if (!$id) {
            $id = DB::last_insert_id();
            $this->set_id($id);
        }
        return $id;
    }
}