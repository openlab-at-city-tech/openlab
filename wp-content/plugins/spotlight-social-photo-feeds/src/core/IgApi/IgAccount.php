<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

/**
 * Represents an Instagram account; a tuple of an {@link IgUser} and an {@link AccessToken}.
 *
 * @since 0.1
 */
class IgAccount
{
    /**
     * @since 0.1
     *
     * @var IgUser
     */
    public $user;

    /**
     * @since 0.1
     *
     * @var AccessToken
     */
    public $accessToken;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param IgUser      $user        The user instance.
     * @param AccessToken $accessToken The access token.
     */
    public function __construct(IgUser $user, AccessToken $accessToken)
    {
        $this->user = $user;
        $this->accessToken = $accessToken;
    }

    /**
     * Retrieves the user for this account.
     *
     * @since 0.1
     *
     * @return IgUser
     */
    public function getUser() : IgUser
    {
        return $this->user;
    }

    /**
     * Retrieves the access token for this account.
     *
     * @since 0.1
     *
     * @return AccessToken
     */
    public function getAccessToken() : AccessToken
    {
        return $this->accessToken;
    }
}
