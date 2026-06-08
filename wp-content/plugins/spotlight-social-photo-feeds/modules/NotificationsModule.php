<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\ServiceList;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Notifications\NotificationStore;

/**
 * The module that adds notification functionality to the plugin.
 *
 * @since 0.2
 */
class NotificationsModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    public function getFactories(): array
    {
        return [
            // The notification store
            'store' => new Constructor(NotificationStore::class, ['providers']),

            // The notification providers
            'providers' => new ServiceList([]),
        ];
    }
}
