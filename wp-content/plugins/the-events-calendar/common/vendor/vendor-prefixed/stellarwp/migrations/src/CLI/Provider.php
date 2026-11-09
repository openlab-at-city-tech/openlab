<?php

/**
 * CLI Service Provider.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\CLI;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\CLI;

use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\Migrations\Config;
use WP_CLI;
/**
 * CLI Service Provider.
 *
 * Registers WP-CLI commands.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\CLI;
 */
class Provider extends Provider_Abstract
{
    /**
     * Register the CLI commands.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register(): void
    {
        if (!(defined('WP_CLI') && WP_CLI)) {
            return;
        }
        $prefix = strtolower(Config::get_hook_prefix());
        $prefix = str_replace(['_', ' '], '-', $prefix);
        WP_CLI::add_command("{$prefix} migrations", Commands::class);
    }
}