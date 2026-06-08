<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Fetcher;

use Psr\Http\Client\ClientInterface;
use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Exception\FetchException;
use RebelCode\Iris\Fetcher\Catalog;
use RebelCode\Iris\Fetcher\FetchResult;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\UserSource;
use RebelCode\Spotlight\Instagram\IgApi\IgApiUtils;
use RebelCode\Spotlight\Instagram\IgApi\IgUser;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\Wp\PostType;

class AccountPostsCatalog implements Catalog
{
    const DEFAULT_LIMIT = 50;

    /** @var ClientInterface */
    protected $client;

    /** @var PostType */
    protected $accounts;

    /** @var Catalog|null */
    protected $storyCatalog;

    /** Constructor */
    public function __construct(ClientInterface $client, PostType $accounts, ?Catalog $storyCatalog = null)
    {
        $this->client = $client;
        $this->accounts = $accounts;
        $this->storyCatalog = $storyCatalog;
    }

    public function query(Source $source, ?string $cursor = null, ?int $count = null): FetchResult
    {
        if ($source->type !== UserSource::TYPE_PERSONAL && $source->type !== UserSource::TYPE_BUSINESS) {
            return new FetchResult([], $source, null, null, null, [
                'Invalid source type',
            ]);
        }

        $username = $source->id;
        $account = AccountPostType::getByUsername($this->accounts, $username);

        if ($account === null) {
            throw new FetchException("Account \"@{$username}\" does not exist", null, $source, $cursor);
        }

        $userId = $account->user->id;
        $accessToken = $account->accessToken;
        $isBusiness = $account->getUser()->getType() === IgUser::TYPE_BUSINESS;

        $baseUrl = $isBusiness ? IgPostsCatalog::GRAPH_API_URL : IgPostsCatalog::BASIC_API_URL;
        $url = "{$baseUrl}/{$userId}/media";
        $limit = $count ?? static::DEFAULT_LIMIT;

        $fields = $isBusiness
            ? IgApiUtils::getBusinessMediaFields(true)
            : IgApiUtils::getPersonalMediaFields(true);

        $args = [
            'access_token' => $accessToken->code,
            'fields' => implode(',', $fields),
        ];

        $result = IgPostsCatalog::requestItems($this->client, $source, $cursor, $limit, $url, $args);
        $result = new FetchResult(
            $result->items,
            $source,
            $account->getUser()->getMediaCount(),
            $result->nextCursor,
            $result->prevCursor,
            $result->errors
        );

        // Add stories if it's a business account and we have a story catalog
        if ($isBusiness && $this->storyCatalog) {
            $storiesResult = $this->storyCatalog->query($source);

            $result = new FetchResult(
                array_merge($storiesResult->items, $result->items),
                $result->source,
                $result->catalogSize,
                $result->nextCursor,
                $result->prevCursor,
                $result->errors
            );
        }

        return $result;
    }
}
