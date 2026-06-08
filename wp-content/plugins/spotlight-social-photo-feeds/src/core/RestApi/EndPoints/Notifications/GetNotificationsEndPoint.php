<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Notifications;

use RebelCode\Spotlight\Instagram\Notifications\Notification;
use RebelCode\Spotlight\Instagram\Notifications\NotificationProvider;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the endpoint that provides plugin notifications.
 *
 * @since 0.2
 */
class GetNotificationsEndPoint extends AbstractEndpointHandler
{
    /**
     * @since 0.2
     *
     * @var NotificationProvider
     */
    protected $provider;

    /**
     * Constructor.
     *
     * @since 0.2
     *
     * @param NotificationProvider $provider
     */
    public function __construct(NotificationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    protected function handle(WP_REST_Request $request)
    {
        $notifications = $this->provider->getNotifications();
        $result = Arrays::map($notifications, function (Notification $notification) {
            return [
                'id' => $notification->getId(),
                'title' => $notification->getTitle(),
                'content' => $notification->getContent(),
                'date' => $notification->getDate(),
            ];
        });

        return new WP_REST_Response($result);
    }
}
