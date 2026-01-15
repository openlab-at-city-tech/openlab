<?php

/**
 * Shepherd's main service provider.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd;

use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\Shepherd\Tables\Provider as Tables_Provider;
use TEC\Common\StellarWP\Schema\Config as Schema_Config;
use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\Shepherd\Contracts\Logger;
use TEC\Common\StellarWP\Shepherd\Tables\Task_Logs;
use TEC\Common\StellarWP\Shepherd\Tables\Tasks;
use RuntimeException;
/**
 * Main Service Provider
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd;
 */
class Provider extends Provider_Abstract
{
    /**
     * The version of the plugin.
     *
     * @since 0.0.1
     *
     * @var string
     */
    public const VERSION = '0.0.1';
    /**
     * The hook prefix.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static string $hook_prefix;
    /**
     * Whether the provider has been registered.
     *
     * @since 0.0.1
     *
     * @var bool
     */
    private static bool $has_registered = false;
    /**
     * Registers Shepherd's specific providers and starts core functionality
     *
     * @since 0.0.1
     * @since 0.0.7 Updated to register the regulator after the tables are registered successfully.
     *
     * @return void The method does not return any value.
     */
    public function register(): void
    {
        if (self::is_registered()) {
            return;
        }
        $this->require_action_scheduler();
        Schema_Config::set_container(Config::get_container());
        Schema_Config::set_db(DB::class);
        // Manually require functions.php since it's not autoloaded for Strauss compatibility.
        require_once __DIR__ . '/functions.php';
        $this->container->singleton(Logger::class, Config::get_logger());
        $this->container->singleton(Tables_Provider::class);
        $this->container->singleton(Regulator::class);
        $prefix = Config::get_hook_prefix();
        add_action("shepherd_{$prefix}_tables_registered", [$this, 'register_regulator']);
        if (!has_action("shepherd_{$prefix}_tables_error")) {
            _doing_it_wrong(__METHOD__, esc_html__('Your software should be handling the case where Shepherd tables are not registered successfully and notify your end users about it.', 'stellarwp-shepherd'), '0.0.7');
        }
        $this->container->get(Tables_Provider::class)->register();
        add_action('action_scheduler_deleted_action', [$this, 'delete_tasks_on_action_deletion']);
        self::$has_registered = true;
    }
    /**
     * Registers the regulator.
     *
     * @since 0.0.7
     *
     * @return void
     */
    public function register_regulator(): void
    {
        $this->container->get(Regulator::class)->register();
    }
    /**
     * Requires Action Scheduler.
     *
     * @since 0.0.1
     * @since 0.0.2 Look into multiple places for the action scheduler main file.
     *
     * @return void
     *
     * @throws RuntimeException If Action Scheduler is not found.
     */
    private function require_action_scheduler(): void
    {
        // This is true when we are not running as a Composer package.
        if (file_exists(__DIR__ . '/../vendor/woocommerce/action-scheduler/action-scheduler.php')) {
            require_once __DIR__ . '/../vendor/woocommerce/action-scheduler/action-scheduler.php';
            return;
        }
        // This is true when we are running as a Composer package.
        if (file_exists(__DIR__ . '/../../../woocommerce/action-scheduler/action-scheduler.php')) {
            require_once __DIR__ . '/../../../woocommerce/action-scheduler/action-scheduler.php';
            return;
        }
        // This is true when we are running as a Composer package but prefixed by Strauss or Mozart or similar.
        if (file_exists(__DIR__ . '/../../../../woocommerce/action-scheduler/action-scheduler.php')) {
            require_once __DIR__ . '/../../../../woocommerce/action-scheduler/action-scheduler.php';
            return;
        }
        throw new RuntimeException('Action Scheduler not found');
    }
    /**
     * Resets the registered state.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$has_registered = false;
    }
    /**
     * Checks if Shepherd is registered.
     *
     * @since 0.0.1
     *
     * @return bool
     */
    public static function is_registered(): bool
    {
        return self::$has_registered;
    }
    /**
     * Deletes tasks on action deletion.
     *
     * @since 0.0.1
     * @since 0.0.8 Check that the DB Logger is used before trying to delete task logs from there.
     *
     * @param int $action_id The action ID.
     */
    public function delete_tasks_on_action_deletion(int $action_id): void
    {
        $task_ids = DB::get_col(DB::prepare('SELECT DISTINCT(%i) FROM %i WHERE %i = %d', Tasks::uid_column(), Tasks::table_name(), 'action_id', $action_id));
        if (empty($task_ids)) {
            return;
        }
        $task_ids = implode(',', array_unique(array_map('intval', $task_ids)));
        if ($this->container->get(Logger::class)->uses_own_table()) {
            DB::query(DB::prepare("DELETE FROM %i WHERE %i IN ({$task_ids})", Task_Logs::table_name(), 'task_id'));
        }
        DB::query(DB::prepare("DELETE FROM %i WHERE %i IN ({$task_ids})", Tasks::table_name(), Tasks::uid_column()));
    }
}