<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

use Psr\Http\Client\ClientInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;

class IgGraphApiClient
{
    public const API_URI = 'https://graph.facebook.com';
    public const TOKEN_EXPIRY = 60 * 24 * 3600;
    public const TOP_MEDIA = 'top_media';
    public const RECENT_MEDIA = 'recent_media';

    /**
     * @since 0.1
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * @since 0.1
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param ClientInterface $client
     * @param CacheInterface  $cache
     */
    public function __construct(
        ClientInterface $client,
        CacheInterface $cache
    ) {
        $this->client = $client;
        $this->cache = $cache;
    }

    /**
     * Retrieves the Instagram Business account associated with a given Facebook page.
     *
     * @since 0.1
     *
     * @param string      $pageId      The ID of the Facebook page.
     * @param AccessToken $accessToken The access token for the Facebook page.
     *
     * @return IgAccount|null The associated Instagram Business account, or null if the page has no associated account.
     */
    public function getAccountForPage(string $pageId, AccessToken $accessToken): ?IgAccount
    {
        // Get the info for the Facebook page
        $request = IgApiUtils::createRequest('GET', static::API_URI . "/{$pageId}", [
            'fields' => 'instagram_business_account,access_token',
            'access_token' => $accessToken->code,

        ]);
        $response = IgApiUtils::sendRequest($this->client, $request);
        $body = IgApiUtils::parseResponse($response);

        if (!isset($body['instagram_business_account'])) {
            return null;
        }

        $userId = $body['instagram_business_account']['id'];
        $userToken = $body['access_token'];

        // Get the user info
        $request = IgApiUtils::createRequest('GET', static::API_URI . "/{$userId}", [
            'fields' => implode(',', IgApiUtils::getGraphUserFields()),
            'access_token' => $userToken,
        ]);
        $response = IgApiUtils::sendRequest($this->client, $request);

        $userData = IgApiUtils::parseResponse($response);
        $userData['account_type'] = IgUser::TYPE_BUSINESS;

        $user = IgUser::create($userData);
        $token = new AccessToken($userToken, time() + static::TOKEN_EXPIRY);

        return new IgAccount($user, $token);
    }

    /**
     * Retrieves the Instagram Business account associated with a given user ID and access token.
     *
     * @since 0.2
     *
     * @param string      $userId      The ID of the Instagram user.
     * @param AccessToken $accessToken The access token for the account.
     *
     * @return IgAccount|null The Instagram Business account, or null if no account was found for the given user ID
     *                        and access token.
     */
    public function getAccountForUser(string $userId, AccessToken $accessToken): ?IgAccount
    {
        // Get the user info
        $request = IgApiUtils::createRequest('GET', static::API_URI . "/{$userId}", [
            'fields' => implode(',', IgApiUtils::getGraphUserFields()),
            'access_token' => $accessToken->code,
        ]);
        $response = IgApiUtils::sendRequest($this->client, $request);

        $userData = IgApiUtils::parseResponse($response);
        $userData['account_type'] = IgUser::TYPE_BUSINESS;

        $user = IgUser::create($userData);

        return new IgAccount($user, $accessToken);
    }
}
