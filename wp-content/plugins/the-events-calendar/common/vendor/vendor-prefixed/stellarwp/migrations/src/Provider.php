<?php

/**
 * Main Migrations Service Provider.
 *
 * @package \StellarWP\Migrations
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations;

use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Utilities\Logger;
use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\Shepherd\Provider as Shepherd_Provider;
use TEC\Common\StellarWP\Shepherd\Config as Shepherd_Config;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Tasks\Clear_Logs;
use TEC\Common\StellarWP\Migrations\Tables\Provider as Tables_Provider;
use TEC\Common\StellarWP\Migrations\CLI\Provider as CLI_Provider;
use TEC\Common\StellarWP\Migrations\REST\Provider as REST_Provider;
use TEC\Common\StellarWP\Migrations\Admin\Provider as Admin_Provider;
use TEC\Common\StellarWP\Migrations\Admin\UI;
use TEC\Common\StellarWP\Migrations\Admin\Assets;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Enums\Operation;
use TEC\Common\StellarWP\Migrations\Exceptions\ApiMethodException;
use TEC\Common\StellarWP\Migrations\Traits\API_Methods;
use function TEC\Common\StellarWP\Shepherd\shepherd;
/**
 * Main service provider for the Migrations library.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Migrations
 */
class Provider extends Provider_Abstract
{
    use API_Methods;
    /**
     * Whether the provider has been registered.
     *
     * @since 0.0.1
     *
     * @var bool
     */
    private static bool $registered = false;
    /**
     * The version of the plugin.
     *
     * @since 0.0.1
     *
     * @var string
     */
    public const VERSION = '0.0.1';
    /**
     * Register the service provider.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register(): void
    {
        if (Shepherd_Provider::is_registered()) {
            $this->register_migrations();
            return;
        }
        $prefix = Config::get_hook_prefix();
        Shepherd_Config::set_container($this->container);
        Shepherd_Config::set_hook_prefix($prefix);
        if (has_action("shepherd_{$prefix}_tables_registered", [$this, 'register_migrations'])) {
            return;
        }
        add_action("shepherd_{$prefix}_tables_registered", [$this, 'register_migrations']);
        $this->container->get(Shepherd_Provider::class)->register();
    }
    /**
     * Register migrations.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register_migrations(): void
    {
        if (self::is_registered()) {
            return;
        }
        $prefix = Config::get_hook_prefix();
        if (!did_action("shepherd_{$prefix}_tables_registered") && !doing_action("shepherd_{$prefix}_tables_registered")) {
            add_action("shepherd_{$prefix}_tables_registered", [$this, 'register_migrations']);
            return;
        }
        self::$registered = true;
        require_once __DIR__ . '/functions.php';
        add_action("stellarwp_migrations_{$prefix}_tables_registered", [$this, 'on_migrations_schema_up']);
        $this->container->singleton(Registry::class);
        $this->container->singleton(UI::class);
        $this->container->singleton(Assets::class);
        $this->container->singleton(Admin_Provider::class);
        $tables_provider = $this->container->get(Tables_Provider::class);
        $tables_provider->register();
        if (is_admin()) {
            $this->container->get(Admin_Provider::class)->register();
        }
    }
    /**
     * Called when the migrations schema is up.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function on_migrations_schema_up(): void
    {
        $prefix = Config::get_hook_prefix();
        $this->container->get(CLI_Provider::class)->register();
        $this->container->get(REST_Provider::class)->register();
        // During WP-CLI execution, we don't need to schedule migrations, we'll run them directly.
        if (defined('WP_CLI') && WP_CLI) {
            return;
        }
        add_action('shutdown', [$this, 'trigger_migrations_scheduling_action'], 100);
        add_action("stellarwp_migrations_{$prefix}_schedule_migrations", [$this, 'schedule_migrations']);
        // Schedule the clear old logs task to run daily.
        add_action('shutdown', [$this, 'dispatch_clear_logs_task'], 100);
    }
    /**
     * Check if the provider is registered.
     *
     * @since 0.0.1
     *
     * @return bool
     */
    public static function is_registered(): bool
    {
        return self::$registered;
    }
    /**
     * Reset registration status. Primarily for testing.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$registered = false;
        Shepherd_Config::reset();
    }
    /**
     * Trigger the migrations scheduling action.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function trigger_migrations_scheduling_action(): void
    {
        $prefix = Config::get_hook_prefix();
        /**
         * Fires when the migrations scheduling action is triggered.
         *
         * @since 0.0.1
         */
        do_action("stellarwp_migrations_{$prefix}_schedule_migrations");
    }
    /**
     * Schedule migrations.
     *
     * @since 0.0.1
     *
     * @throws ApiMethodException If the migration execution cannot be inserted.
     *
     * @return void
     */
    public function schedule_migrations(): void
    {
        $prefix = Config::get_hook_prefix();
        /**
         * Filters whether migrations should be automatically scheduled for this prefix.
         *
         * @since 0.0.1
         *
         * @param bool $automatic_schedule Whether the migration should be automatically scheduled. Default is true.
         *
         * @return bool Whether the migration should be automatically scheduled.
         */
        if (!apply_filters("stellarwp_migrations_{$prefix}_automatic_schedule", true)) {
            return;
        }
        /**
         * Fires before all migrations are automatically scheduled.
         *
         * @since 0.0.1
         */
        do_action("stellarwp_migrations_{$prefix}_pre_schedule_migrations");
        $migrations_registry = $this->container->get(Registry::class);
        /** @var Migration $migration */
        foreach ($migrations_registry as $migration_id => $migration) {
            if (!$migration->is_applicable()) {
                continue;
            }
            if (!$migration->can_run()) {
                continue;
            }
            // Check if there is already an execution for this migration.
            $existing_execution = Migration_Executions::get_first_by('migration_id', $migration_id);
            if ($existing_execution) {
                continue;
                // skip the automatic scheduling for this migration.
            }
            $result = $this->schedule($migration, Operation::UP());
            // Log the migration scheduling.
            $logger = new Logger($result['execution_id']);
            $logger->info(sprintf('Migration "%s" scheduled for execution.', $migration_id), ['batch' => $result['from_batch'], 'batch_size' => $result['batch_size'], 'items_total' => $migration->get_total_items(Operation::UP()), 'extra_args' => $migration->get_up_extra_args_for_batch($result['from_batch'], $result['batch_size'])]);
        }
        /**
         * Fires after all migrations are automatically scheduled.
         *
         * @since 0.0.1
         */
        do_action("stellarwp_migrations_{$prefix}_post_schedule_migrations");
    }
    /**
     * Dispatch the clear logs task.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function dispatch_clear_logs_task(): void
    {
        shepherd()->dispatch(new Clear_Logs(), DAY_IN_SECONDS);
    }
}