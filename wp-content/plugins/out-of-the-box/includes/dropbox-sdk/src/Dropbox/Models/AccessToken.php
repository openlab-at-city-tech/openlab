<?php

namespace Kunnu\Dropbox\Models;

class AccessToken extends BaseModel
{
    /**
     * Access Token.
     *
     * @var string
     */
    protected $token;

    /**
     * Token Type.
     *
     * @var string
     */
    protected $tokenType;

    /**
     * User ID.
     *
     * @var string
     */
    protected $uid;

    /**
     * Account ID.
     *
     * @var string
     */
    protected $accountId;

    /**
     * Scope.
     *
     * @var string
     */
    protected $scope;

    /**
     * Refresh Token.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * Expires.
     *
     * @var string
     */
    protected $expiresIn;

    /**
     * Created.
     *
     * @var string
     */
    protected $created;

    /**
     * Create a new AccessToken instance.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->token = $this->getDataProperty('access_token');
        $this->tokenType = $this->getDataProperty('tokenType');
        $this->uid = $this->getDataProperty('uid');
        $this->accountId = $this->getDataProperty('accountId');
        $this->scope = $this->getDataProperty('scope');
        $this->refreshToken = $this->getDataProperty('refresh_token');
        $this->expiresIn = $this->getDataProperty('expires_in');
        $this->created = $this->getDataProperty('created');

        if (empty($this->refreshToken)) {
            // Long lived Tokens don't have a refresh token and don't expire
            $this->expiresIn = -1;
        }

        if (empty($this->created)) {
            $this->created = time();
        }
    }

    /**
     * Get Access Token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get Token Type.
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Get User ID.
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Get Account ID.
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Get created.
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get expires.
     *
     * @return string
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Get refresh Token.
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Get scope.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set expires.
     *
     * @param string $expiresIn expires
     *
     * @return self
     */
    public function setExpiresIn(string $expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Set access Token.
     *
     * @param string $token access Token
     *
     * @return self
     */
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Set created.
     *
     * @param string $created created
     *
     * @return self
     */
    public function setCreated(string $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set refresh Token.
     *
     * @param string $refreshToken refresh Token
     *
     * @return self
     */
    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }
}
