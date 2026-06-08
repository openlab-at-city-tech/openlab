<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use RebelCode\WordPress\Http\WpClient;
use RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\SilentPool;
use RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\CachePool;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\IgApi\IgGraphApiClient;
use RebelCode\Spotlight\Instagram\IgApi\IgBasicApiClient;
use RebelCode\Spotlight\Instagram\IgApi\IgApiClient;
use RebelCode\Spotlight\Instagram\Actions\AuthCallbackListener;
use Psr\Container\ContainerInterface;
use Dhii\Services\Factory;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\ServiceList;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Alias;

/**
 * The module that contains all functionality related to Instagram's APIs.
 *
 * @since 0.1
 */
class InstagramModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            //==========================================================================
            // HTTP CLIENT
            //==========================================================================

            // The HTTP client instance
            'client' => new Factory(['client/timeout'], function ($timeout) {
                return WpClient::createDefault(null, [
                    'timeout' => $timeout,
                ]);
            }),

            // The default timeout for the HTTP client
            'client/timeout' => new Value(20),

            // Middleware for the Guzzle client
            'client/middlewares' => new ServiceList([]),

            //==========================================================================
            // API CACHE
            //==========================================================================

            // The cache pool instance
            'cache/pool' => new Factory(['@wp/db', 'cache/pool/key', 'cache/pool/default', 'cache/pool/silent'],
                function ($wpdb, $key, $default, $silent) {
                    $pool = new CachePool($wpdb, $key, $default);

                    return $silent ? new SilentPool($pool) : $pool;
                }
            ),

            // The time-to-live for the cache (1 hour)
            'cache/ttl' => new Value(3600),

            // The key for the cache pool
            'cache/pool/key' => new Value('sli_api'),

            // The default value for the cache pool - allows false-negative detection
            'cache/pool/default' => new StringService(uniqid('{key}'), [
                'key' => 'cache/pool/key',
            ]),

            // Whether the cache pool is silent (does not throw errors)
            'cache/pool/silent' => new Value(true),

            //==========================================================================
            // API CLIENT
            //==========================================================================

            // The auth state, which is passed back by IG/FB APIs
            'api/state' => new Factory([], function () {
                $nonce = wp_create_nonce(AuthCallbackListener::NONCE_ACTION);
                return urlencode(
                    json_encode([
                        // For the auth server to redirect back to this site
                        'site' => admin_url() . 'admin.php?nonce=' . urlencode($nonce),
                        // The version of the auth server to use
                        'version' => 2,
                    ])
                );
            }),

            // The driver for clients, responsible for dispatching requests and receiving responses
            'api/driver' => new Alias('ig/client'),

            // The API client that combines the Basic Display API and Graph API clients
            'api/client' => new Constructor(IgApiClient::class, [
                'api/basic/client',
                'api/graph/client',
            ]),

            // Listens to requests from the auth server to save accounts into the DB
            'api/auth/listener' => new Constructor(AuthCallbackListener::class, [
                'api/client',
                '@accounts/cpt',
                'api/graph/auth_url',
            ]),

            //==========================================================================
            // BASIC DISPLAY API
            //==========================================================================

            // The URL to the auth dialog for the Basic Display API
            'api/basic/auth_url' => new Factory(['api/state'], function ($state) {
                return "https://auth2.spotlightwp.com/dialog/personal?state={$state}";
            }),

            // The basic display API client
            'api/basic/client' => new Constructor(IgBasicApiClient::class, [
                'api/driver',
                'cache/pool',
                'api/basic/legacy_compensation',
            ]),

            // Whether or not to use the legacy API to compensate for data that is missing from the Basic Display API
            'api/basic/legacy_compensation' => new Value(false),

            //==========================================================================
            // GRAPH API
            //==========================================================================

            // The URL to auth dialog for the Graph API
            'api/graph/auth_url' => new Factory(['api/state'], function ($state) {
                return "https://auth.spotlightwp.com/dialog/business?state={$state}";
            }),

            // The Graph API client
            'api/graph/client' => new Constructor(IgGraphApiClient::class, [
                'api/driver',
                'cache/pool',
            ]),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c): void
    {
        // Listen for requests from the auth server to insert connected accounts into the DB
        add_action('admin_init', $c->get('api/auth/listener'));
    }
}
