<?php

namespace RebelCode\Spotlight\Instagram\Notifications;

/**
 * Interface for an object that provides notifications.
 *
 * @since 0.2
 */
interface NotificationProvider
{
    /**
     * Retrieves the notifications.
     *
     * @since 0.2
     *
     * @return Notification[]
     */
    public function getNotifications() : array;
}
