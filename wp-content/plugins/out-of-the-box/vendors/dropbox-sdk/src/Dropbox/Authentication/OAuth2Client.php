<?php

namespace TheLion\OutoftheBox\API\Dropbox\Authentication;

use TheLion\OutoftheBox\API\Dropbox\DropboxApp;
use TheLion\OutoftheBox\API\Dropbox\DropboxClient;
use TheLion\OutoftheBox\API\Dropbox\DropboxRequest;
use TheLion\OutoftheBox\API\Dropbox\Security\RandomStringGeneratorInterface;

class OAuth2Client
{
    /**
     * The Base URL.
     *
     * @const string
     */
    const BASE_URL = 'https://dropbox.com';

    /**
     * Auth Token URL.
     *
     * @const string
     */
    const AUTH_TOKEN_URL = 'https://api.dropboxapi.com/oauth2/token';

    /**
     * The Dropbox App.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\DropboxApp
     */
    protected $app;

    /**
     * The Dropbox Client.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\DropboxClient
     */
    protected $client;

    /**
     * Create a new DropboxApp instance.
     *
     * @param \TheLion\OutoftheBox\API\Dropbox\Security\RandomStringGeneratorInterface $randStrGenerator
     */
    public function __construct(DropboxApp $app, DropboxClient $client, RandomStringGeneratorInterface $randStrGenerator = null)
    {
        $this->app = $app;
        $this->client = $client;
        $this->randStrGenerator = $randStrGenerator;
    }

    /**
     * Get the Dropbox App.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxApp
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get the Dropbox Client.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get the OAuth2 Authorization URL.
     *
     * @param string $redirectUri Callback URL to redirect user after authorization.
     *                            If null is passed, redirect_uri will be omitted
     *                            from the url and the code will be presented directly
     *                            to the user.
     * @param string $state       CSRF Token
     * @param array  $params      Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#oauth2-authorize
     *
     * @return string
     */
    public function getAuthorizationUrl($redirectUri = null, $state = null, array $params = [])
    {
        //Request Parameters
        $params = array_merge([
            'client_id' => $this->getApp()->getClientId(),
            'response_type' => 'code',
            'state' => $state,
            'token_access_type' => 'offline',
        ], $params);

        if (!is_null($redirectUri)) {
            $params['redirect_uri'] = $redirectUri;
        }

        return $this->buildUrl('/oauth2/authorize', $params);
    }

    /**
     * Get Access Token.
     *
     * @param string $code        Authorization Code
     * @param string $redirectUri Redirect URI used while getAuthorizationUrl
     * @param string $grant_type  Grant Type ['authorization_code']
     *
     * @return array
     */
    public function getAccessToken($code, $redirectUri = null, $grant_type = 'authorization_code')
    {
        //Request Params
        $params = [
            'code' => $code,
            'grant_type' => $grant_type,
            'client_id' => $this->getApp()->getClientId(),
            'client_secret' => $this->getApp()->getClientSecret(),
            'redirect_uri' => $redirectUri,
        ];

        $params = http_build_query($params);

        $apiUrl = static::AUTH_TOKEN_URL;
        $uri = $apiUrl.'?'.$params;

        //Send Request through the DropboxClient
        //Fetch the Response (DropboxRawResponse)
        $response = $this->getClient()
            ->getHttpClient()
            ->send($uri, 'POST', null)
        ;

        //Fetch Response Body
        $body = $response->getBody();

        //Decode the Response body to associative array
        //and return
        return json_decode((string) $body, true);
    }

    /**
     * Refresh token.
     *
     * @param string $code        Authorization Code
     * @param string $redirectUri Redirect URI used while getAuthorizationUrl
     * @param string $grant_type  Grant Type ['authorization_code']
     *
     * @return array
     */
    public function refreshToken()
    {
        //Access Token (Should most probably be null)
        $accessToken = $this->getApp()->getAccessToken();

        //Request Params
        $params = [
            'refresh_token' => $accessToken->getRefreshToken(),
            'grant_type' => 'refresh_token',
            'client_id' => $this->getApp()->getClientId(),
            'client_secret' => $this->getApp()->getClientSecret(),
        ];

        $params = http_build_query($params);

        $apiUrl = static::AUTH_TOKEN_URL;
        $uri = $apiUrl.'?'.$params;

        //Send Request through the DropboxClient
        //Fetch the Response (DropboxRawResponse)
        $response = $this->getClient()
            ->getHttpClient()
            ->send($uri, 'POST', null)
        ;

        //Fetch Response Body
        $body = $response->getBody();

        //Decode the Response body to associative array
        //and return
        $token = json_decode((string) $body, true);

        $accessToken->setToken($token['access_token']);
        $accessToken->setExpiresIn($token['expires_in']);
        $accessToken->setCreated(time());

        if (!empty($token['refresh_token'])) {
            $accessToken->setRefreshToken($token['refresh_token']);
        }

        return $accessToken;
    }

    /**
     * Returns if the access_token is expired.
     *
     * @return bool returns True if the access_token is expired
     */
    public function isAccessTokenExpired()
    {
        $accessToken = $this->getApp()->getAccessToken();

        if ($accessToken->getExpiresIn() < 0) {
            return false;
        }

        // If the token is set to expire in the next 120 seconds.
        return ($accessToken->getCreated()
        + ($accessToken->getExpiresIn() - 120)) < time();
    }

    /**
     * Disables the access token.
     */
    public function revokeAccessToken()
    {
        //Access Token
        $accessToken = $this->getApp()->getAccessToken();

        //Request
        $request = new DropboxRequest('POST', '/auth/token/revoke', $accessToken->getToken());
        // Do not validate the response
        // since the /token/revoke endpoint
        // doesn't return anything in the response.
        // See: https://www.dropbox.com/developers/documentation/http/documentation#auth-token-revoke
        $request->setParams(['validateResponse' => false]);

        //Revoke Access Token
        $response = $this->getClient()->sendRequest($request);
    }

    /**
     * Build URL.
     *
     * @param string $endpoint
     * @param array  $params   Query Params
     *
     * @return string
     */
    protected function buildUrl($endpoint = '', array $params = [])
    {
        $queryParams = http_build_query($params);

        return static::BASE_URL.$endpoint.'?'.$queryParams;
    }
}
