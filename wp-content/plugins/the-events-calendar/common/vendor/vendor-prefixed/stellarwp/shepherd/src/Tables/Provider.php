<?php

/**
 * Shepherd Tables Service Provider
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tables;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Tables;

use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\Schema\Register;
use TEC\Common\StellarWP\Shepherd\Contracts\Logger;
use TEC\Common\StellarWP\DB\Database\Exceptions\DatabaseQueryException;
use TEC\Common\StellarWP\Shepherd\Config;
/**
 * Shepherd Tables Service Provider
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tables;
 */
class Provider extends Provider_Abstract
{
    /**
     * Tables to register.
     *
     * @var array<int, class-string>
     */
    private array $tables = [Tasks::class, Task_Logs::class];
    /**
     * Registers the service provider bindings.
     *
     * @since 0.0.1
     * @since 0.0.7 Updated to catch DatabaseQueryException.
     *
     * @return void The method does not return any value.
     */
    public function register(): void
    {
        // Bind after all tables are registered.
        $this->container->singleton(Utility\Safe_Dynamic_Prefix::class);
        $this->container->get(Utility\Safe_Dynamic_Prefix::class)->calculate_longest_table_name($this->tables);
        $prefix = Config::get_hook_prefix();
        try {
            Register::table(Tasks::class);
            if ($this->container->get(Logger::class)->uses_own_table()) {
                Register::table(Task_Logs::class);
            }
            /**
             * Fires an action when the Shepherd tables are registered.
             *
             * @since 0.0.7
             */
            do_action("shepherd_{$prefix}_tables_registered");
        } catch (DatabaseQueryException $e) {
            /**
             * Fires an action when an error or exception happens in the context of Shepherd tables implementation AND the server runs PHP 7.0+.
             *
             * @since 0.0.7
             *
             * @param DatabaseQueryException $e The thrown error.
             */
            do_action("shepherd_{$prefix}_tables_error", $e);
        }
    }
}