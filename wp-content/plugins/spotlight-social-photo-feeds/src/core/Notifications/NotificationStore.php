<?php

namespace RebelCode\Spotlight\Instagram\Notifications;

/**
 * A composite notification provider that acts as the root notification store for the plugin.
 *
 * @since 0.2
 */
class NotificationStore implements NotificationProvider
{
    /**
     * @since 0.2
     *
     * @var NotificationProvider[]
     */
    protected $providers;

    /**
     * Constructor.
     *
     * @since 0.2
     *
     * @param NotificationProvider[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    public function getNotifications() : array
    {
        $all = [];

        foreach ($this->providers as $provider) {
            $all = array_merge($all, $provider->getNotifications());
        }

        return $all;
    }
}
