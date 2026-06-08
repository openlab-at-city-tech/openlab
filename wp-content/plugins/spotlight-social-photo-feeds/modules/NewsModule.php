<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheException;
use RebelCode\Psr7\Uri;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Notifications\NewsNotificationProvider;
use RebelCode\WordPress\Http\WpClient;
use wpdb;
use RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\CachePool;

/**
 * The module that adds functionality for showing news from the Spotlight server in the plugin's UI.
 *
 * @since 0.2
 */
class NewsModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    public function run(ContainerInterface $c): void
    {
        add_action('spotlight/instagram/rest_api/clear_cache', function () use ($c) {
            /** @var $cache CachePool */
            $cache = $c->get('cache');
            try {
                $cache->clear();
            } catch (CacheException $e) {
                // Fail silently
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    public function getFactories(): array
    {
        return [
            // The HTTP client to use to fetch news
            'client' => new Factory(['client/base_url', 'client/options'], function ($url, $options) {
                return WpClient::createDefault(new Uri($url), $options);
            }),

            // The base URL for the HTTP client
            'client/base_url' => new StringService('{0}/news', ['@saas/server/base_url']),

            // The options for the HTTP client
            'client/options' => new Value(['timeout' => 10]),

            // The cache where to store cached responses from the server (15 minute TTL)
            'cache' => new Factory(['@wp/db',], function (wpdb $wpdb) {
                return new CachePool($wpdb, 'sli_news', uniqid('sli_news'), 15 * 60);
            }),

            // The notification provider
            'provider' => new Constructor(NewsNotificationProvider::class, ['client', 'cache']),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    public function getExtensions(): array
    {
        return [
            // Register the provider
            'notifications/providers' => new ArrayExtension(['provider']),
        ];
    }
}
