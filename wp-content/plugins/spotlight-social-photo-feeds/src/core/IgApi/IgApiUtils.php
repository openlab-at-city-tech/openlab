<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheException as PsrCacheException;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
use RebelCode\Psr7\Request;
use RebelCode\Psr7\Uri;
use RuntimeException;

/**
 * Utilities for the API client.
 *
 * @since 0.1
 */
class IgApiUtils
{
    /**
     * Creates a URI from a string and an associative array of query values.
     *
     * @param string $uri   The URI string.
     * @param array  $query An associative array of key-value query params.
     *
     * @return UriInterface The created URI instance.
     */
    public static function createUri(string $uri, array $query = []): UriInterface
    {
        $base = new Uri($uri);

        return count($query) > 0
            ? Uri::withQueryValues($base, $query)
            : $base;
    }

    /**
     * Creates a simple request instance for a string URI and an associative array of query values.
     *
     * @param string $method The request method.
     * @param string $uri    The URI string.
     * @param array  $query  An associative array of key-value query params.
     *
     * @return RequestInterface The created request instance.
     */
    public static function createRequest(string $method, string $uri, array $query = []): RequestInterface
    {
        return new Request($method, static::createUri($uri, $query));
    }

    /**
     * Sends a request using the given client and handles exceptions.
     *
     * @param ClientInterface  $client  The HTTP client to send the request with.
     * @param RequestInterface $request The request to send using the client.
     *
     * @return ResponseInterface The response.
     *
     * @throws RuntimeException If a network problem occurred or got a 4xx or 5xx response.
     */
    public static function sendRequest(ClientInterface $client, RequestInterface $request): ResponseInterface
    {
        try {
            return $client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            static::handleException($e);
        }
    }

    /**
     * Handles an exception that is triggered when a request is sent.
     *
     * @param ClientExceptionInterface $e The exception to handle.
     *
     * @return never-returns
     *
     * @throws RuntimeException
     */
    public static function handleException(ClientExceptionInterface $e): void
    {
        $response = method_exists($e, 'getResponse')
            ? $e->getResponse()
            : null;
        $body = $response
            ? $response->getBody()
            : null;
        $decoded = ($body !== null)
            ? json_decode($body->getContents(), true) ?? []
            : [];

        $error = $decoded['error'] ?? $decoded;
        $message = $error['error_description'] ??
                   $error['error_message'] ??
                   $error['message'] ??
                   $e->getMessage();
        $code = $error['code'] ?? '?';
        $type = $error['type'] ?? 'Unknown';

        $fullMessage = sprintf('Error #%d [%s]: %s', $code, $type, $message);

        throw new RuntimeException("The Instagram API responded with an error: $fullMessage");
    }

    /**
     * Sends a request, or uses a cache.
     *
     * @since 0.1
     *
     * @param CacheInterface $cache     The cache instance.
     * @param string         $key       The key to read responses from or write new responses to.
     * @param callable       $getRemote The actual request dispatching function to invoke when the key is not in cache.
     *
     * @return array The parsed response.
     *
     * @throws CacheExceptionInterface
     */
    public static function getCachedResponse(CacheInterface $cache, string $key, callable $getRemote): array
    {
        try {
            if ($cache->has($key)) {
                return json_decode($cache->get($key), true);
            }
        } catch (PsrCacheException $e) {
            // Carry on with normal request
        }

        $response = ($getRemote)();

        $result = ($response instanceof ResponseInterface)
            ? static::parseResponse($response)
            : $response;

        $attempt = 1;

        do {
            try {
                $cache->set($key, json_encode($result), 1800);
                break;
            } catch (PsrCacheException $e) {
                // Do nothing
            }

            if (method_exists($e, 'getResponse')) {
                error_log($e->getMessage() . " (attempt $attempt of 3)");
            }

            $attempt++;
            if ($attempt > 3) {
                break;
            }
        } while (true);

        return $result;
    }

    /**
     * Parses an IG response, decoding JSON bodies or detecting errors, if any.
     *
     * @since 0.1
     *
     * @param ResponseInterface $response The response.
     *
     * @return array The decoded JSON response body.
     */
    public static function parseResponse(ResponseInterface $response): array
    {
        static::checkResponseStatus($response);

        $body = $response->getBody();
        $raw = $body ? $body->getContents() : null;
        $decoded = json_decode($raw, true);

        if ($decoded === false) {
            throw new RuntimeException("Received malformed response: $raw");
        }

        if (isset($decoded['error']) || isset($decoded['error_message'])) {
            $message = isset($decoded['error'])
                ? $decoded['error_description']
                : $decoded['error_message'];

            throw new RuntimeException("API error: $message");
        }

        return $decoded;
    }

    /**
     * Checks a response's status to determine if it's an erroneous response.
     *
     * @since 0.1
     *
     * @param ResponseInterface $response
     */
    public static function checkResponseStatus(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            $statusPhrase = $response->getReasonPhrase();
            throw new RuntimeException("API responded with $statusCode $statusPhrase");
        }
    }

    /**
     * Retrieves the fields available for users from the basic display API.
     *
     * @since 0.1
     *
     * @return string[]
     */
    public static function getBasicUserFields() : array
    {
        return [
            'id',
            'username',
            'media_count',
            'account_type',
        ];
    }

    /**
     * Retrieves all the fields available for users from the Graph API.
     *
     * @since 0.1
     *
     * @return string[]
     */
    public static function getGraphUserFields() : array
    {
        return [
            'id',
            'username',
            'name',
            'biography',
            'media_count',
            'followers_count',
            'follows_count',
            'profile_picture_url',
            'website',
        ];
    }

    /**
     * Retrieves the fields for media objects from a personal account.
     *
     * @since 0.5.1
     *
     * @param bool $isOwn True to get the fields for media that are owned by the personal account that is fetching them,
     *                    or false for media from other accounts.
     *
     * @return string[]
     */
    public static function getPersonalMediaFields(bool $isOwn = true): array
    {
        $fields = [
            'id',
            'username',
            'timestamp',
            'caption',
            'media_type',
            'media_url',
            'permalink',
            sprintf('children{%s}', implode(', ', static::getChildrenMediaFields($isOwn))),
        ];

        if ($isOwn) {
            $fields[] = 'thumbnail_url';
        }

        return $fields;
    }

    /**
     * Retrieves the fields for media objects from a business account.
     *
     * @since 0.5.1
     *
     * @param bool $isOwn True to get the fields for media that are owned by the business account that is fetching them,
     *                    or false for media from other accounts.
     *
     * @return string[]
     */
    public static function getBusinessMediaFields(bool $isOwn = true): array
    {
        $fields = static::getPersonalMediaFields($isOwn);
        $fields[] = 'like_count';
        $fields[] = 'comments_count';

        if ($isOwn) {
            $fields[] = 'media_product_type';
            $fields[] = 'shortcode';
            $fields[] = sprintf('comments.limit(15){%s}', implode(', ', static::getCommentFields()));
        }

        return $fields;
    }

    /**
     * Retrieves the fields for story media objects.
     *
     * @since 0.5.1
     *
     * @return string[]
     */
    public static function getStoryMediaFields(): array
    {
        $fields = static::getPersonalMediaFields();
        $fields[] = 'like_count';
        $fields[] = 'media_product_type';
        $fields[] = 'shortcode';

        return $fields;
    }

    /**
     * Retrieves the fields for media objects from hashtags.
     *
     * @since 0.1
     *
     * @return string[]
     */
    public static function getHashtagMediaFields() : array
    {
        $fields = [
            'id',
            'caption',
            'like_count',
            'comments_count',
            'media_type',
            'media_url',
            'permalink',
            'timestamp',
        ];

        $list = implode(',', static::getChildrenMediaFields(false));
        $fields[] = "children{{$list}}";

        return $fields;
    }

    /**
     * Retrieves the fields for children media objects.
     *
     * @since 0.1
     *
     * @param bool $isOwn True to get the fields for media that are owned by the account that is fetching the media,
     *                    or false for media from other accounts.
     *
     * @return string[]
     */
    public static function getChildrenMediaFields(bool $isOwn = true) : array
    {
        $fields = [
            'id',
            'media_url',
            'media_type',
            'permalink',
        ];

        if ($isOwn) {
            $fields[] = 'thumbnail_url';
        }

        return $fields;
    }

    /**
     * Retrieves the field for comment objects.
     *
     * @since 0.1
     *
     * @return string[]
     */
    public static function getCommentFields() : array
    {
        return [
            'id',
            'username',
            'text',
            'timestamp',
            'like_count',
        ];
    }
}
