<?php

namespace Kunnu\Dropbox;

class DropboxApp
{
    /**
     * The Client ID of the App.
     *
     * @see https://www.dropbox.com/developers/apps
     *
     * @var string
     */
    protected $clientId;

    /**
     * The Client Secret of the App.
     *
     * @see https://www.dropbox.com/developers/apps
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The Access Token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Create a new Dropbox instance.
     *
     * @param string $clientId     Application Client ID
     * @param string $clientSecret Application Client Secret
     * @param string $accessToken  Access Token
     */
    public function __construct($clientId, $clientSecret, $accessToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
    }

    /**
     * Get the App Client ID.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the App Client Secret.
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Get the Access Token.
     *
     * @return \Kunnu\Dropbox\Models\AccessToken|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Update the Access Token.
     *
     * @param null|\Kunnu\Dropbox\Models\AccessToken $access_token
     *
     * @return string
     */
    public function setAccessToken($access_token)
    {
        $this->accessToken = $access_token;
    }
}
