<?php

/**
 * WP-CLI Commands for Migrations.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\CLI
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\CLI;

use WP_CLI;
use WP_CLI\Utils;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Registry;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Tasks\Execute;
use TEC\Common\StellarWP\Shepherd\Contracts\Task;
use TEC\Common\StellarWP\Migrations\Enums\Operation;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Utilities\Cast;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\Migrations\Exceptions\ApiMethodException;
use TEC\Common\StellarWP\Migrations\Traits\API_Methods;
use TEC\Common\StellarWP\Migrations\Models\Execution;
use function TEC\Common\StellarWP\Shepherd\shepherd;
use function WP_CLI\Utils\make_progress_bar;
/**
 * Manage database migrations.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\CLI
 *
 * ## EXAMPLES
 *
 *     # Run all pending migrations
 *     $ wp migrations run
 *
 *     # Run migrations for a specific plugin
 *     $ wp migrations run --plugin=tec
 *
 *     # Rollback last batch of migrations
 *     $ wp migrations rollback --plugin=tec
 *
 *     # Show migration status
 *     $ wp migrations status
 */
class Commands
{
    use API_Methods;
    /**
     * List registered migrations.
     *
     * @since 0.0.1
     *
     * ## OPTIONS
     *
     * [--tags=<tags>]
     * : The tags to list migrations for. If not specified, lists for all registered migrations.
     *
     * [--format=<format>]
     * : Output format. Options: table, json, csv, yaml. Default: table.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - csv
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     # List all registered migrations
     *     $ wp migrations list
     *
     *     # List migrations for a specific plugin
     *     $ wp migrations list --tags=tec,ld
     *
     * @subcommand list
     *
     * @param array<mixed>               $args       Positional arguments.
     * @param array<string, bool|string> $assoc_args Associative arguments.
     *
     * @return void
     */
    public function list(array $args, array $assoc_args): void
    {
        /** @var string $tags_string */
        $tags_string = $this->get_param($assoc_args, 'tags', '');
        $items = $this->get_list($tags_string);
        if (empty($items)) {
            $this->log('No migrations found.');
            return;
        }
        /** @var string $format */
        $format = $this->get_param($assoc_args, 'format', 'table');
        $this->display_items_in_format($items, ['id', 'label', 'description', 'tags', 'total_batches', 'can_run', 'is_applicable', 'status'], $format);
    }
    /**
     * Run a migration.
     *
     * @since 0.0.1
     *
     * ## OPTIONS
     *
     * <migration_id>
     * : The migration ID to run.
     *
     * [--from-batch=<batch>]
     * : The batch number to start from. If not specified, starts from the first batch.
     *
     * [--to-batch=<batch>]
     * : The batch number to end at. If not specified, ends at the last batch.
     *
     * [--batch-size=<batch-size>]
     * : The number of items to process per batch. If not specified, uses the migration's default batch size.
     *
     * [--dry-run]
     * : Show what would be run without actually running the migration.
     *
     * ## EXAMPLES
     *
     *     # Run a migration
     *     $ wp migrations run my_migration
     *
     *     # Run a migration for a specific batch range
     *     $ wp migrations run my_migration --from-batch=1 --to-batch=10
     *
     *     # Run a migration with a custom batch size
     *     $ wp migrations run my_migration --batch-size=10
     *
     *     # Preview what would be run (dry run)
     *     $ wp migrations run my_migration --dry-run
     *
     *     # Run a migration with all options combined
     *     $ wp migrations run my_migration --batch-size=10 --from-batch=1 --to-batch=10
     *
     * @subcommand run
     *
     * @param array<mixed>               $args       Positional arguments.
     * @param array<string, bool|string> $assoc_args Associative arguments.
     *
     * @return void
     */
    public function run(array $args, array $assoc_args): void
    {
        $this->run_operation(Operation::UP(), $args, $assoc_args);
    }
    /**
     * Rollback a migration.
     *
     * @since 0.0.1
     *
     * ## OPTIONS
     *
     * <migration_id>
     * : The migration ID to rollback.
     *
     * [--from-batch=<batch>]
     * : The batch number to start from. If not specified, starts from the first batch.
     *
     * [--to-batch=<batch>]
     * : The batch number to end at. If not specified, ends at the last batch.
     *
     * [--batch-size=<batch-size>]
     * : The number of items to process per batch. If not specified, uses the migration's default batch size.
     *
     * [--dry-run]
     * : Show what would be rolled back without actually running the rollback.
     *
     * ## EXAMPLES
     *
     *     # Rollback a migration
     *     $ wp migrations rollback my_migration
     *
     *     # Rollback a migration for a specific batch range
     *     $ wp migrations rollback my_migration --from-batch=1 --to-batch=10
     *
     *     # Rollback a migration with a custom batch size
     *     $ wp migrations rollback my_migration --batch-size=10
     *
     *     # Preview what would be rolled back (dry run)
     *     $ wp migrations rollback my_migration --dry-run
     *
     *     # Rollback a migration with all options combined
     *     $ wp migrations rollback my_migration --batch-size=10 --from-batch=1 --to-batch=10
     *
     * @subcommand rollback
     *
     * @param array<mixed>               $args       Positional arguments.
     * @param array<string, bool|string> $assoc_args Associative arguments.
     *
     * @return void
     */
    public function rollback(array $args, array $assoc_args): void
    {
        $this->run_operation(Operation::DOWN(), $args, $assoc_args);
    }
    /**
     * List logs for a specific migration.
     *
     * @since 0.0.1
     *
     * ## OPTIONS
     *
     * <execution_id>
     * : The execution ID to list logs for.
     *
     * [--type=<type>]
     * : Filter logs by log type (e.g., info, warning, error, debug). Accepts multiple types separated by commas.
     *
     * [--not-type=<not-type>]
     * : Filter logs by log type that is not the specified type (e.g., info, warning, error, debug). Accepts multiple types separated by commas.
     *
     * [--search=<search>]
     * : Filter logs by search term.
     *
     * [--limit=<limit>]
     * : Limit the number of results returned. Default is 100.
     *
     * [--offset=<offset>]
     * : Offset the results by the specified number of records. Default is 0.
     *
     * [--order=<order>]
     * : Order the results by ASC or DESC. Default is DESC.
     *
     * [--order-by=<order-by>]
     * : Order the results by the specified column. Default is created_at.
     *
     * [--format=<format>]
     * : Output format. Options: table, json, csv, yaml. Default: table.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - csv
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     # List logs for a specific execution
     *     $ wp migrations logs 123
     *
     *     # List logs in JSON format
     *     $ wp migrations logs 123 --format=json
     *
     *     # List only failed logs
     *     $ wp migrations logs 123 --type=error
     *
     *     # List the last 10 logs
     *     $ wp migrations logs 123 --limit=10
     *
     *     # List logs for a specific migration
     *     $ wp migrations logs 123 --search="failed to update record"
     *
     *     # List logs for a specific type
     *     $ wp migrations logs 123 --type=error
     *
     *     # List logs for a specific search term and not type
     *     $ wp migrations logs 123 --not-type=info --search="failed to update record"
     *
     *     # List logs for a specific search term and not multiple types
     *     $ wp migrations logs 123 --not-type=info,debug --search="failed to update record"
     *
     * @subcommand logs
     *
     * @param array<mixed>               $args       Positional arguments.
     * @param array<string, bool|string> $assoc_args Associative arguments.
     *
     * @return void
     */
    public function logs(array $args, array $assoc_args): void
    {
        $execution_id = $args[0] ?? null;
        if (!($execution_id && is_numeric($execution_id))) {
            $this->error('Execution ID is required.');
            return;
        }
        $execution_id = Cast::to_int($execution_id);
        /** @var string $format */
        $format = $this->get_param($assoc_args, 'format', 'table');
        /** @var string $types */
        $types = $this->get_param($assoc_args, 'type', '');
        /** @var string $not_types */
        $not_types = $this->get_param($assoc_args, 'not-type', '');
        $limit = Cast::to_int($this->get_param($assoc_args, 'limit', 100));
        $offset = Cast::to_int($this->get_param($assoc_args, 'offset', 0));
        $order = strtoupper(Cast::to_string($this->get_param($assoc_args, 'order', 'DESC')));
        /** @var string $order_by */
        $order_by = $this->get_param($assoc_args, 'order-by', 'created_at');
        /** @var string $search */
        $search = $this->get_param($assoc_args, 'search', '');
        try {
            $items = $this->get_logs($execution_id, $types, $not_types, $limit, $offset, $order, $order_by, $search);
            if (empty($items)) {
                $this->log("No logs found for execution '{$execution_id}'.");
                return;
            }
            $this->display_items_in_format($items, ['id', 'type', 'message', 'data', 'created_at'], $format);
        } catch (ApiMethodException $e) {
            $this->error($e->getMessage());
        }
    }
    /**
     * List executions.
     *
     * @since 0.0.1
     *
     * ## OPTIONS
     *
     * <migration_id>
     * : The migration ID to list executions for.
     *
     * [--format=<format>]
     * : Output format. Options: table, json, csv, yaml. Default: table.
     *
     * ## EXAMPLES
     *
     *     # List executions for a specific migration
     *     $ wp migrations executions my_migration
     *
     *     # List executions in JSON format
     *     $ wp migrations executions my_migration --format=json
     *
     * @subcommand executions
     *
     * @param array<mixed>               $args       Positional arguments.
     * @param array<string, bool|string> $assoc_args Associative arguments.
     *
     * @return void
     */
    public function executions(array $args, array $assoc_args): void
    {
        $migration_id = $args[0] ?? null;
        if (!$migration_id) {
            $this->error('Migration ID is required.');
            return;
        }
        /** @var string $format */
        $format = $this->get_param($assoc_args, 'format', 'table');
        $executions = $this->get_executions(Cast::to_string($migration_id));
        if (empty($executions)) {
            $this->log('No executions found.');
            return;
        }
        $executions = array_map(static fn(Execution $execution): array => $execution->to_array(), $executions);
        $this->display_items_in_format($executions, ['id', 'migration_id', 'start_date', 'end_date', 'status', 'items_total', 'items_processed', 'created_at'], $format);
    }
    /**
     * Run a migration operation.
     *
     * @since 0.0.1
     *
     * @param Operation                  $operation  The operation to run.
     * @param array<mixed>               $args       The arguments.
     * @param array<string, bool|string> $assoc_args The associative arguments.
     *
     * @return void
     */
    protected function run_operation(Operation $operation, array $args, array $assoc_args): void
    {
        $migration_id = $args[0] ?? null;
        if (!$migration_id) {
            $this->error('Migration ID is required.');
            return;
        }
        $container = Config::get_container();
        $registry = $container->get(Registry::class);
        /** @var string $migration_id */
        $migration = $registry->get($migration_id);
        if (!$migration) {
            $this->error("Migration with ID {$migration_id} not found.");
            return;
        }
        /** @var Migration $migration */
        $batch_size = Cast::to_int(Utils\get_flag_value($assoc_args, 'batch-size', $migration->get_default_batch_size()));
        if ($batch_size < 1) {
            WP_CLI::error('batch-size must be at least 1.');
        }
        $total_batches = $migration->get_total_batches($batch_size, $operation);
        $from_batch = Cast::to_int(Utils\get_flag_value($assoc_args, 'from-batch', 1));
        $to_batch = Cast::to_int(Utils\get_flag_value($assoc_args, 'to-batch', $total_batches));
        $from_batch = max(1, $from_batch);
        $to_batch = min($to_batch, $total_batches);
        if ($from_batch > $to_batch) {
            WP_CLI::error('from-batch cannot be greater than to-batch.');
        }
        $dry_run = isset($assoc_args['dry-run']);
        if ($dry_run) {
            $total_batches_to_run = $to_batch - $from_batch + 1;
            $operation_label = $operation->get_label();
            WP_CLI::log("Dry run: Would {$operation_label} migration '{$migration_id}'.");
            WP_CLI::log("  - Total items: {$migration->get_total_items($operation)}");
            WP_CLI::log("  - Batch size: {$batch_size}");
            WP_CLI::log("  - From batch: {$from_batch}");
            WP_CLI::log("  - To batch: {$to_batch}");
            WP_CLI::log("  - Total batches to run: {$total_batches_to_run}");
            return;
        }
        $insert_status = Migration_Executions::insert(['migration_id' => $migration_id, 'status' => Status::SCHEDULED()->getValue(), 'items_total' => $migration->get_total_items($operation), 'items_processed' => 0]);
        if (!$insert_status) {
            WP_CLI::error(sprintf(
                // translators: %1$s is the migration ID.
                __('Failed to insert migration execution for migration "%1$s"', 'stellarwp-migrations'),
                $migration_id
            ));
        }
        $execution_id = DB::last_insert_id();
        $tasks = [];
        for ($i = $from_batch; $i <= $to_batch; $i++) {
            $extra_args_method = 'get_' . $operation->getValue() . '_extra_args_for_batch';
            $extra_args = $migration->{$extra_args_method}($i, $batch_size);
            $task = new Execute($operation->getValue(), $migration_id, $i, $batch_size, $execution_id, ...$extra_args);
            $tasks[] = $task;
        }
        $batches = count($tasks);
        /** @var \cli\progress\Bar $progress_bar */
        $progress_bar = make_progress_bar("Running `{$batches}` batches for migration `{$migration_id}`. From batch `{$from_batch}` to batch `{$to_batch}` with a batch size of `{$batch_size}`.", $batches);
        $callables = ['before' => function (Task $task): void {
            $migration_id = Cast::to_string($task->get_args()[1]);
            $batch = Cast::to_int($task->get_args()[2]);
            WP_CLI::log("Running batch `{$batch}` for migration `{$migration_id}`.");
        }, 'after' => function () use ($progress_bar): void {
            $progress_bar->tick();
        }, 'always' => function () use ($progress_bar): void {
            $progress_bar->finish();
        }];
        shepherd()->run($tasks, $callables);
    }
    /**
     * Display items in the specified format.
     *
     * @since 0.0.1
     *
     * @param array<int|string, array<string, mixed>> $items   The items to display.
     * @param array<string>                           $columns The columns to display.
     * @param string                                  $format  The format to display the items in.
     *
     * @return void
     */
    protected function display_items_in_format(array $items, array $columns, string $format = 'table'): void
    {
        $items = $this->normalize_items($items, $format);
        Utils\format_items($format, $items, $columns);
    }
    /**
     * Get a parameter from the arguments.
     *
     * @since 0.0.1
     *
     * @param array<string, bool|string> $args          The arguments.
     * @param string                     $param_key     The parameter key.
     * @param mixed|null                 $default_value The default value.
     *
     * @return mixed The parameter value.
     */
    protected function get_param(array $args, string $param_key, $default_value = null)
    {
        return Utils\get_flag_value($args, $param_key, $default_value);
    }
    /**
     * Log a message.
     *
     * @since 0.0.1
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    protected function log(string $message): void
    {
        WP_CLI::log($message);
    }
    /**
     * Log an error message.
     *
     * @since 0.0.1
     *
     * @param string $message The error message to log.
     *
     * @return void
     */
    protected function error(string $message): void
    {
        WP_CLI::error($message);
    }
}