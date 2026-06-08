<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheException;
use RebelCode\Psr7\Uri;
use RebelCode\Spotlight\Instagram\Feeds\Preview\FeedPreviewProvider;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\WordPress\Http\WpClient;
use wpdb;
use RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\CachePool;

class PreviewModule extends Module
{
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

    public function getFactories(): array
    {
        return [
            // Client
            'client' => new Factory(['client/base_url', 'client/options'], function ($url, $options) {
                return WpClient::createDefault(new Uri($url), $options);
            }),
            'client/base_url' => new StringService('{0}/preview', ['@saas/server/base_url']),
            'client/options' => new Value(['timeout' => 10]),

            // Cache
            'cache/key' => new Value('preview.remote'),
            'cache' => new Factory(['@wp/db',], function (wpdb $wpdb) {
                return new CachePool($wpdb, 'sli_preview', uniqid('sli_preview'), 86400);
            }),
            // Provider
            'provider' => new Constructor(FeedPreviewProvider::class, ['client', 'cache/key', 'cache']),
        ];
    }

    public function getExtensions(): array
    {
        return [
            'ui/l10n/admin-common' => new Extension(['provider'], function ($l10n, $provider) {
                $l10n['preview'] = $provider->get();

                return $l10n;
            }),
        ];
    }
}
