<?php

/**
 * Migrations Tables Service Provider
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Tables
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Tables;

use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\Schema\Register;
use TEC\Common\StellarWP\DB\Database\Exceptions\DatabaseQueryException;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Shepherd\Tables\Utility\Safe_Dynamic_Prefix;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Logs;
/**
 * Migrations Tables Service Provider
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Tables
 */
class Provider extends Provider_Abstract
{
    /**
     * Tables to register.
     *
     * @var array<int, class-string>
     */
    private array $tables = [Migration_Logs::class, Migration_Executions::class];
    /**
     * Registers the service provider bindings.
     *
     * @since 0.0.1
     *
     * @return void The method does not return any value.
     */
    public function register(): void
    {
        $prefix = Config::get_hook_prefix();
        // Bind after all tables are registered.
        $implementation_id = self::get_safe_dynamic_prefix_implementation_id();
        $this->container->singleton($implementation_id, Safe_Dynamic_Prefix::class);
        /** @var Safe_Dynamic_Prefix $safe_dynamic_prefix */
        $safe_dynamic_prefix = $this->container->get($implementation_id);
        $safe_dynamic_prefix->calculate_longest_table_name($this->tables);
        try {
            Register::table(Migration_Logs::class);
            Register::table(Migration_Executions::class);
            /**
             * Fires an action when the Shepherd tables are registered.
             *
             * @since 0.0.7
             */
            do_action("stellarwp_migrations_{$prefix}_tables_registered");
        } catch (DatabaseQueryException $e) {
            /**
             * Fires an action when an error or exception happens in the context of Shepherd tables implementation AND the server runs PHP 7.0+.
             *
             * @since 0.0.7
             *
             * @param DatabaseQueryException $e The thrown error.
             */
            do_action("stellarwp_migrations_{$prefix}_tables_error", $e);
        }
    }
    /**
     * Gets the implementation ID for the safe dynamic prefix.
     *
     * @since 0.0.1
     *
     * @return string The implementation ID.
     */
    public static function get_safe_dynamic_prefix_implementation_id(): string
    {
        $prefix = Config::get_hook_prefix();
        return "stellarwp_migrations_{$prefix}_safe_dynamic_prefix";
    }
}