<?php

namespace RebelCode\Spotlight\Instagram\Notifications;

use Psr\Http\Client\ClientInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
use RebelCode\Psr7\Request;
use Throwable;

/**
 * Provides notifications for news fetched from the Spotlight server.
 *
 * @since 0.2
 */
class NewsNotificationProvider implements NotificationProvider
{
    /**
     * The cache key where the cached news is stored.
     *
     * @since 0.2
     */
    const CACHE_KEY = 'news.remote';

    /**
     * @since 0.2
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * @since 0.2
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @since 0.2
     *
     * @param ClientInterface $client The HTTP client to use for sending requests.
     * @param CacheInterface  $cache  The cache to use for caching news from the remote server.
     */
    public function __construct(ClientInterface $client, CacheInterface $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     *
     * @since 0.2
     */
    public function getNotifications() : array
    {
        try {
            $fetched = false;

            if ($this->cache->has(static::CACHE_KEY)) {
                $raw = $this->cache->get(static::CACHE_KEY);
            } else {
                $request = new Request('GET', '');
                $response = $this->client->sendRequest($request);
                $body = $response ? $response->getBody() : null;
                $raw = $body ? $body->getContents() : null;
                $fetched = true;
            }

            $decoded = json_decode($raw);

            if ($fetched && $decoded !== null) {
                $this->cache->set(static::CACHE_KEY, $raw);
            }
        } catch (Throwable $exception) {
            // If anything goes wrong, just return no notifications.
            // Notifications aren't mission critical, so we can get away with this.
            return [];
        }

        $notifications = [];
        foreach ($decoded as $data) {
            $notifications[] = new Notification(
                $data->id,
                $data->title,
                $data->message,
                $data->date
            );
        }

        return $notifications;
    }
}
