<?php

/**
 * Clear Logs Task.
 *
 * Shepherd task that clears old migration logs after a configurable retention period.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Tasks
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Tasks;

use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\DB\QueryBuilder\QueryBuilder;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Logs;
use TEC\Common\StellarWP\Shepherd\Abstracts\Task_Abstract;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskFailWithoutRetryException;
/**
 * Task to clear old migration logs.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Tasks
 */
class Clear_Logs extends Task_Abstract
{
    /**
     * Get the retention period in days for migration logs.
     *
     * @since 0.0.1
     *
     * @return int The retention period in days.
     */
    public static function get_retention_days(): int
    {
        $default_retention_days = 180;
        $prefix = Config::get_hook_prefix();
        /**
         * Filters the retention period in days for migration logs.
         *
         * @since 0.0.1
         *
         * @param int $retention_days The number of days to retain logs. Default is 180.
         *
         * @return int The retention period in days.
         */
        $retention_days = (int) apply_filters("stellarwp_migrations_{$prefix}_log_retention_days", $default_retention_days);
        // Fallback to the default retention days if the filtered value is invalid.
        return $retention_days >= 1 ? $retention_days : $default_retention_days;
    }
    /**
     * Process the task - clear old logs.
     *
     * @return void
     *
     * @throws ShepherdTaskFailWithoutRetryException If the cleanup fails.
     */
    public function process(): void
    {
        $retention_days = self::get_retention_days();
        // Calculate the cutoff date.
        $cutoff_date = gmdate('Y-m-d H:i:s', (int) strtotime("-{$retention_days} days"));
        // Delete all logs for executions older than the retention period.
        $deleted_count = DB::table(Migration_Logs::table_name(false))->where('migration_execution_id', function (QueryBuilder $builder) use ($cutoff_date) {
            $builder->select('id')->from(Migration_Executions::table_name(false))->where('end_date_gmt', $cutoff_date, '<');
        }, 'IN')->delete();
        if (false === $deleted_count) {
            throw new ShepherdTaskFailWithoutRetryException('Failed to delete old migration logs.');
        }
    }
    /**
     * Get the task prefix.
     *
     * @since 0.0.1
     *
     * @return string
     */
    public function get_task_prefix(): string
    {
        return 'mig_clear_logs_';
    }
    /**
     * Get the task's group.
     *
     * @since 0.0.1
     *
     * @return string
     */
    public function get_group(): string
    {
        return sprintf('%s_migrations', Config::get_hook_prefix());
    }
}