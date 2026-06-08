<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Fetcher;

use Exception;
use Psr\Http\Client\ClientInterface;
use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\FetchResult;
use RebelCode\Psr7\Request;
use RebelCode\Psr7\Uri;
use RebelCode\Spotlight\Instagram\IgApi\IgApiUtils;
use RebelCode\Spotlight\Instagram\Utils\Arrays;

abstract class IgPostsCatalog
{
    const BASIC_API_URL = 'https://graph.instagram.com';
    const GRAPH_API_URL = 'https://graph.facebook.com';

    public static function requestItems(
        ClientInterface $client,
        Source $source,
        ?string $cursor,
        ?int $count,
        string $url,
        array $args
    ): FetchResult {
        if ($cursor) {
            $args['after'] = $cursor;
        }

        if ($count) {
            $args['limit'] = $count;
        }

        $items = [];
        $errors = [];
        $nextCursor = null;
        $prevCursor = null;

        try {
            $response = static::requestRaw($client, $url, $args);

            $items = $response['data'] ?? [];
            $items = Arrays::map($items, function (array $data) use ($source) {
                return new Item($data['id'], null, [$source], $data);
            });

            if (!empty($response['paging']['next'] ?? null)) {
                $nextCursor = $response['paging']['cursors']['after'] ?? null;
            }

            if (!empty($response['paging']['previous'] ?? null)) {
                $prevCursor = $response['paging']['cursors']['before'] ?? null;
            }
        } catch (Exception $ex) {
            $errors[] = $ex->getMessage();
        }

        return new FetchResult(
            $items,
            $source,
            count($items),
            $nextCursor,
            $prevCursor,
            $errors
        );
    }

    public static function requestRaw(
        ClientInterface $client,
        string $url,
        array $query = []
    ): array {
        // Merge the query into the URL
        $uri = Uri::withQueryValues(new Uri($url), $query);

        // Create and send the request
        $request = new Request('GET', $uri);
        $response = IgApiUtils::sendRequest($client, $request);

        // Parse the body of the response
        return IgApiUtils::parseResponse($response);
    }
}
