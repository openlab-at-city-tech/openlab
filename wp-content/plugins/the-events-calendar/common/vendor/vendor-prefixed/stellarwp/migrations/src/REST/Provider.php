<?php

/**
 * REST Service Provider.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\REST;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\REST;

use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\Migrations\Config;
use WP_REST_Server;
/**
 * REST Service Provider.
 *
 * Registers REST API routes.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\REST;
 */
class Provider extends Provider_Abstract
{
    /**
     * The API version.
     *
     * @since 0.0.1
     *
     * @var string
     */
    public const VERSION = 'v1';
    /**
     * Register the REST routes.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    /**
     * Get the REST API namespace.
     *
     * @since 0.0.1
     *
     * @return string The namespace (e.g., "tec/v1").
     */
    public static function get_namespace(): string
    {
        $prefix = strtolower(Config::get_hook_prefix());
        $prefix = str_replace(['_', ' '], '-', $prefix);
        return $prefix . '/migrations/' . self::VERSION;
    }
    /**
     * Register REST API routes.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register_routes(): void
    {
        $namespace = self::get_namespace();
        $endpoints = $this->container->get(Endpoints::class);
        // List migrations.
        register_rest_route($namespace, '/migrations', [['methods' => WP_REST_Server::READABLE, 'callback' => [$endpoints, 'list'], 'permission_callback' => [$endpoints, 'can_manage_migrations'], 'args' => ['tags' => ['description' => __('Filter migrations by tags (comma-separated).', 'stellarwp-migrations'), 'type' => 'string', 'default' => '']]]]);
        // Run a migration.
        register_rest_route($namespace, '/migrations/(?P<migration_id>[a-zA-Z0-9_-]+)/run', [['methods' => WP_REST_Server::CREATABLE, 'callback' => [$endpoints, 'run'], 'permission_callback' => [$endpoints, 'can_manage_migrations'], 'args' => ['migration_id' => ['description' => __('The migration ID to run.', 'stellarwp-migrations'), 'type' => 'string', 'required' => true], 'from-batch' => ['description' => __('The batch number to start from.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => 1], 'to-batch' => ['description' => __('The batch number to end at.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => null], 'batch-size' => ['description' => __('The number of items per batch.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => null]]]]);
        // Rollback a migration.
        register_rest_route($namespace, '/migrations/(?P<migration_id>[a-zA-Z0-9_-]+)/rollback', [['methods' => WP_REST_Server::CREATABLE, 'callback' => [$endpoints, 'rollback'], 'permission_callback' => [$endpoints, 'can_manage_migrations'], 'args' => ['migration_id' => ['description' => __('The migration ID to rollback.', 'stellarwp-migrations'), 'type' => 'string', 'required' => true], 'from-batch' => ['description' => __('The batch number to start from.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => 1], 'to-batch' => ['description' => __('The batch number to end at.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => null], 'batch-size' => ['description' => __('The number of items per batch.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => null]]]]);
        // List executions for a migration.
        register_rest_route($namespace, '/migrations/(?P<migration_id>[a-zA-Z0-9_-]+)/executions', [['methods' => WP_REST_Server::READABLE, 'callback' => [$endpoints, 'executions'], 'permission_callback' => [$endpoints, 'can_manage_migrations'], 'args' => ['migration_id' => ['description' => __('The migration ID to list executions for.', 'stellarwp-migrations'), 'type' => 'string', 'required' => true]]]]);
        // List logs for an execution.
        register_rest_route($namespace, '/executions/(?P<execution_id>\d+)/logs', [['methods' => WP_REST_Server::READABLE, 'callback' => [$endpoints, 'logs'], 'permission_callback' => [$endpoints, 'can_manage_migrations'], 'args' => ['execution_id' => ['description' => __('The execution ID to list logs for.', 'stellarwp-migrations'), 'type' => 'integer', 'required' => true], 'type' => ['description' => __('Filter logs by type (comma-separated).', 'stellarwp-migrations'), 'type' => 'string', 'default' => ''], 'not-type' => ['description' => __('Exclude logs by type (comma-separated).', 'stellarwp-migrations'), 'type' => 'string', 'default' => ''], 'search' => ['description' => __('Search term to filter logs.', 'stellarwp-migrations'), 'type' => 'string', 'default' => ''], 'limit' => ['description' => __('Limit the number of results.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => 100], 'offset' => ['description' => __('Offset the results.', 'stellarwp-migrations'), 'type' => 'integer', 'default' => 0], 'order' => ['description' => __('Order direction (ASC or DESC).', 'stellarwp-migrations'), 'type' => 'string', 'default' => 'DESC', 'enum' => ['ASC', 'DESC']], 'order-by' => ['description' => __('Column to order by.', 'stellarwp-migrations'), 'type' => 'string', 'default' => 'created_at', 'enum' => ['id', 'type', 'created_at']]]]]);
    }
}