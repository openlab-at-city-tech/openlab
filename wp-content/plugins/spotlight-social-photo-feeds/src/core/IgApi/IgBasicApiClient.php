<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

use Exception;
use Psr\Http\Client\ClientInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;

/**
 * API client for the Instagram Basic Display API.
 *
 * @since 0.1
 */
class IgBasicApiClient
{
    /**
     * The base URI to the Instagram Basic Display API.
     *
     * @since 0.1
     */
    const BASE_URL = 'https://graph.instagram.com';

    /**
     * The API client driver.
     *
     * @since 0.1
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * The cache to use for caching responses.
     *
     * @since 0.1
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Whether or not to use the legacy Instagram API to compensate for data that is omitted by the Basic Display API.
     *
     * @since 0.1
     *
     * @var bool
     */
    protected $legacyComp;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param ClientInterface $client     The client driver to use for sending requests.
     * @param CacheInterface  $cache      The cache to use for caching responses.
     * @param bool            $legacyComp If true, the legacy Instagram API will be used to compensate for data that is
     *                                    omitted by the Basic Display API.
     */
    public function __construct(
        ClientInterface $client,
        CacheInterface $cache,
        bool $legacyComp = false
    ) {
        $this->client = $client;
        $this->cache = $cache;
        $this->legacyComp = $legacyComp;
    }

    /**
     * Retrieves information about the user with whom the access token is associated with.
     *
     * @since 0.1
     *
     * @param AccessToken $accessToken The access token.
     *
     * @return IgUser The user associated with the given access token.
     */
    public function getTokenUser(AccessToken $accessToken) : IgUser
    {
        $request = IgApiUtils::createRequest('GET', static::BASE_URL . '/me', [
            'fields' => implode(',', IgApiUtils::getBasicUserFields()),
            'access_token' => $accessToken->code,
        ]);

        $response = IgApiUtils::sendRequest($this->client, $request);

        // Make sure the account is marked as PERSONAL, even if Instagram treats it as a BUSINESS account.
        // Otherwise, media fetching in IgApiClient will use the Graph API instead of the BasicDisplay API, which will
        // fail (access tokens cannot be used across the two APIs).
        $body = IgApiUtils::parseResponse($response);
        $body['account_type'] = 'PERSONAL';

        return $this->createUserFromResponse($body);
    }

    /**
     * Refreshes a long-lived access token.
     *
     * @since 0.1
     *
     * @param AccessToken $accessToken The access token to refresh.
     *
     * @return AccessToken The new access token.
     */
    public function refreshAccessToken(AccessToken $accessToken) : AccessToken
    {
        $request = IgApiUtils::createRequest('GET', static::BASE_URL . '/refresh_access_token', [
            'grant_type' => 'ig_refresh_token',
            'access_token' => $accessToken->code,
        ]);

        $response = IgApiUtils::sendRequest($this->client, $request);

        $body = IgApiUtils::parseResponse($response);
        $code = $body['access_token'];
        $expiry = time() + intval($body['expires_in']);

        return new AccessToken($code, $expiry);
    }

    /**
     * Creates an IgUser instance from a response.
     *
     * @since 0.1
     *
     * @param array $data The user response data.
     *
     * @return IgUser The created user instance.
     */
    protected function createUserFromResponse(array $data)
    {
        $data = $this->populateMissingUserInfo($data);

        return IgUser::create($data);
    }

    /**
     * Attempts to populate missing user info from the legacy API.
     *
     * @since 0.1
     *
     * @param array $data The user data.
     *
     * @return array The user data, populated with additional info from the legacy API if successful.
     */
    protected function populateMissingUserInfo(array $data)
    {
        if (!$this->legacyComp || !isset($data['username'])) {
            return $data;
        }

        $username = $data['username'];

        try {
            $getRemote = function () use ($username) {
                $request = IgApiUtils::createRequest('GET', "https://instagram.com/{$username}?__a=1");

                return IgApiUtils::sendRequest($this->client, $request);
            };

            $info = IgApiUtils::getCachedResponse($this->cache, "legacy_p_{$username}", $getRemote);
        } catch (Exception $exception) {
            return $data;
        }

        if (!isset($info['graphql']['user'])) {
            return $data;
        }

        $legacy = $info['graphql']['user'];

        $data['profile_picture_url'] = $legacy['profile_pic_url'] ?? "";
        $data['biography'] = $legacy['biography'] ?? "";
        $data['followers_count'] = $legacy['edge_followed_by']['count'] ?? 0;

        return $data;
    }
}
