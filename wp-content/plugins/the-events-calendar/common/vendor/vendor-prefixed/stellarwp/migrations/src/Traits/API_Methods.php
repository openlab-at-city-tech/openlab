<?php

/**
 * API Abstract Trait for Migrations.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Traits
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Traits;

use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Registry;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Enums\Operation;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Logs;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Exceptions\ApiMethodException;
use TEC\Common\StellarWP\Migrations\Models\Execution;
use TEC\Common\StellarWP\Migrations\Tasks\Execute;
use DateTimeInterface;
use TEC\Common\MyCLabs\Enum\Enum;
use function TEC\Common\StellarWP\Shepherd\shepherd;
/**
 * API Methods Trait for Migrations.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Traits
 */
trait API_Methods
{
    /**
     * List registered migrations.
     *
     * @since 0.0.1
     *
     * @param string $tags_string The tags to list migrations for.
     *
     * @return array<string, array<string, mixed>> The list of migrations.
     */
    protected function get_list(string $tags_string = ''): array
    {
        $tags = array_filter(explode(',', $tags_string));
        $container = Config::get_container();
        $registry = $container->get(Registry::class);
        if (!empty($tags)) {
            $registry = $registry->filter(static fn(?Migration $migration): bool => $migration && !empty(array_intersect($tags, $migration->get_tags())));
        }
        $items = $registry->all();
        if (empty($items)) {
            return [];
        }
        $migrations_as_arrays = [];
        /** @var string $migration_id */
        foreach ($items as $migration_id => $migration) {
            $migrations_as_arrays[$migration_id] = array_merge(['id' => $migration_id], $migration->to_array());
        }
        return $migrations_as_arrays;
    }
    /**
     * List logs for a specific migration.
     *
     * @since 0.0.1
     *
     * @param int    $execution_id The execution ID to list logs for.
     * @param string $types        The types to list logs for.
     * @param string $not_types    The types to not list logs for.
     * @param int    $limit        The limit of logs to list.
     * @param int    $offset       The offset of logs to list.
     * @param string $order        The order of logs to list.
     * @param string $order_by     The column to order logs by.
     * @param string $search       The search term to list logs for.
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws ApiMethodException If invalid arguments are provided.
     */
    public function get_logs(int $execution_id, string $types = '', string $not_types = '', int $limit = 100, int $offset = 0, string $order = 'DESC', string $order_by = 'created_at', string $search = ''): array
    {
        $execution = Migration_Executions::get_by_id($execution_id);
        if (!$execution || !$execution instanceof Execution) {
            throw new ApiMethodException("Execution with ID '{$execution_id}' not found.");
        }
        $migration_id = $execution->get_migration_id();
        $container = Config::get_container();
        $registry = $container->get(Registry::class);
        $migration = $registry->get($migration_id);
        if (!$migration) {
            throw new ApiMethodException("The migration associated with execution '{$execution_id}' is no longer available.");
        }
        // Validate order direction.
        if (!in_array($order, ['ASC', 'DESC'], true)) {
            throw new ApiMethodException('Invalid order direction. Use ASC or DESC.');
        }
        // Validate order-by column.
        $allowed_order_by = ['id', 'type', 'created_at'];
        if (!in_array($order_by, $allowed_order_by, true)) {
            throw new ApiMethodException(sprintf('Invalid order-by column. Allowed: %s', implode(', ', $allowed_order_by)));
        }
        $arguments = ['offset' => $offset, 'orderby' => $order_by, 'order' => $order, 'query_operator' => 'AND', 'migration_execution_id' => ['column' => 'migration_execution_id', 'value' => $execution_id, 'operator' => '=']];
        if ($search) {
            $arguments['term'] = $search;
        }
        if ($types && $not_types) {
            throw new ApiMethodException('Cannot filter by type and not-type at the same time. Use one or the other.');
        }
        if ($types) {
            $types = explode(',', $types);
            $arguments['type'] = ['query_operator' => 'OR'];
            foreach ($types as $type) {
                $arguments['type'][] = ['column' => 'type', 'value' => $type, 'operator' => '='];
            }
        }
        if ($not_types) {
            $not_types = explode(',', $not_types);
            $arguments['not_type'] = ['query_operator' => 'AND'];
            foreach ($not_types as $type) {
                $arguments['not_type'][] = ['column' => 'type', 'value' => $type, 'operator' => '!='];
            }
        }
        /** @var array<string, array<string, mixed>> $logs */
        $logs = Migration_Logs::paginate($arguments, $limit);
        if (empty($logs)) {
            return [];
        }
        return $logs;
    }
    /**
     * List executions.
     *
     * @since 0.0.1
     *
     * @param string $migration_id The migration ID to list executions for.
     *
     * @return array<string, Execution>
     */
    public function get_executions(string $migration_id): array
    {
        return Migration_Executions::get_all_by('migration_id', $migration_id);
    }
    /**
     * Normalize items to be displayed in the CLI.
     *
     * @since 0.0.1
     *
     * @param array<int|string, array<string, mixed>> $items  The items to normalize.
     * @param string                                  $format The format to normalize the items to.
     *
     * @return array<int|string, array<string, mixed>> The normalized items.
     */
    protected function normalize_items(array $items, string $format): array
    {
        foreach ($items as $offset => $item) {
            if (!is_array($item)) {
                continue;
            }
            foreach ($item as $column => $value) {
                if (is_array($value)) {
                    $normalized = $this->normalize_array_value($value, $format);
                    $items[$offset][$column] = 'table' === $format ? implode(', ', $normalized) : $normalized;
                    continue;
                }
                if ($value instanceof Enum) {
                    $items[$offset][$column] = $value->getValue();
                    continue;
                }
                if (is_bool($value)) {
                    if ('table' === $format) {
                        $items[$offset][$column] = $value ? 'true' : 'false';
                    } elseif ('csv' === $format) {
                        $items[$offset][$column] = (int) $value;
                    } else {
                        $items[$offset][$column] = $value;
                    }
                    continue;
                }
                if ($value instanceof DateTimeInterface) {
                    $items[$offset][$column] = $value->format(DateTimeInterface::ATOM);
                    continue;
                }
            }
        }
        return $items;
    }
    /**
     * Normalize an array value for CLI display.
     *
     * Handles nested arrays by converting objects (Enums, DateTimes) to their string representations.
     *
     * @since 0.0.1
     *
     * @param array<mixed> $value  The array value to normalize.
     * @param string       $format The format to normalize to.
     *
     * @return array<mixed> The normalized array.
     */
    private function normalize_array_value(array $value, string $format): array
    {
        $normalized = [];
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $normalized[$key] = $this->normalize_array_value($item, $format);
            } elseif ($item instanceof Enum) {
                $normalized[$key] = $item->getValue();
            } elseif ($item instanceof DateTimeInterface) {
                $normalized[$key] = $item->format(DateTimeInterface::ATOM);
            } elseif (is_bool($item)) {
                if ('table' === $format) {
                    $normalized[$key] = $item ? 'true' : 'false';
                } elseif ('csv' === $format) {
                    $normalized[$key] = (int) $item;
                } else {
                    $normalized[$key] = $item;
                }
            } else {
                $normalized[$key] = $item;
            }
        }
        return $normalized;
    }
    /**
     * Schedule a migration for execution.
     *
     * Creates an execution record and dispatches batch tasks via Shepherd.
     *
     * @since 0.0.1
     *
     * @param Migration $migration           The migration instance to schedule.
     * @param Operation $operation           The operation to run (UP or DOWN).
     * @param int       $from_batch          The starting batch number. Default 1.
     * @param int|null  $to_batch            The ending batch number. Default null (same as from_batch).
     * @param int|null  $batch_size          The batch size. Default null (uses migration default).
     * @param int|null  $parent_execution_id The parent execution ID when scheduling an automatic rollback. Default null.
     *
     * @throws ApiMethodException If the execution record cannot be inserted.
     *
     * @return array{execution_id: int, from_batch: int, to_batch: int, batch_size: int} The scheduling details.
     */
    public function schedule(Migration $migration, Operation $operation, int $from_batch = 1, ?int $to_batch = null, ?int $batch_size = null, ?int $parent_execution_id = null): array
    {
        $batch_size ??= $migration->get_default_batch_size();
        $total_batches = max(1, $migration->get_total_batches($batch_size, $operation));
        $to_batch ??= $from_batch;
        // Ensure batch bounds are valid.
        $from_batch = max(1, $from_batch);
        $to_batch = min($to_batch, $total_batches);
        $insert_data = ['migration_id' => $migration->get_id(), 'status' => Status::SCHEDULED()->getValue(), 'items_total' => $migration->get_total_items($operation), 'items_processed' => 0];
        if (null !== $parent_execution_id) {
            $insert_data['parent_execution_id'] = $parent_execution_id;
        }
        $insert_status = Migration_Executions::insert($insert_data);
        if (!$insert_status) {
            throw new ApiMethodException(sprintf(
                /* translators: %s is the migration ID */
                __('Failed to insert migration execution for migration "%s"', 'stellarwp-migrations'),
                $migration->get_id()
            ));
        }
        $execution_id = DB::last_insert_id();
        $extra_args_method = 'get_' . $operation->getValue() . '_extra_args_for_batch';
        $prefix = Config::get_hook_prefix();
        /**
         * Fires before a migration is scheduled.
         *
         * @since 0.0.1
         *
         * @param Migration $migration  The migration instance being scheduled.
         * @param Operation $operation  The operation to run (UP or DOWN).
         * @param int       $from_batch The starting batch number.
         * @param int       $to_batch   The ending batch number.
         */
        do_action("stellarwp_migrations_{$prefix}_pre_schedule_migration", $migration, $operation, $from_batch, $to_batch);
        for ($batch_number = $from_batch; $batch_number <= $to_batch; $batch_number++) {
            $extra_args = $migration->{$extra_args_method}($batch_number, $batch_size);
            shepherd()->dispatch(new Execute($operation->getValue(), $migration->get_id(), $batch_number, $batch_size, $execution_id, ...$extra_args));
        }
        /**
         * Fires after a migration is scheduled.
         *
         * @since 0.0.1
         *
         * @param Migration $migration    The migration instance that was scheduled.
         * @param Operation $operation    The operation to run (UP or DOWN).
         * @param int       $execution_id The execution record ID.
         * @param int       $from_batch   The starting batch number.
         * @param int       $to_batch     The ending batch number.
         */
        do_action("stellarwp_migrations_{$prefix}_post_schedule_migration", $migration, $operation, $execution_id, $from_batch, $to_batch);
        return ['execution_id' => $execution_id, 'from_batch' => $from_batch, 'to_batch' => $to_batch, 'batch_size' => $batch_size];
    }
    /**
     * Get the migrations registry.
     *
     * @since 0.0.1
     *
     * @return Registry The migrations registry.
     */
    public function get_registry(): Registry
    {
        return Config::get_container()->get(Registry::class);
    }
}