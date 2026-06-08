<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

/**
 * Represents an API access token for an Instagram account.
 *
 * @since 0.1
 */
class AccessToken
{
    /**
     * @since 0.1
     *
     * @var string
     */
    public $code;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $expires;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $code    The access token code.
     * @param int    $expires The timestamp for when the access code expires.
     */
    public function __construct(string $code, int $expires)
    {
        $this->code = $code;
        $this->expires = $expires;
    }

    /**
     * Retrieves the access token code.
     *
     * @since 0.1
     *
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * Retrieves the timestamp for when the access token expires.
     * @since 0.1
     *
     * @return int
     */
    public function getExpires() : int
    {
        return $this->expires;
    }

    /**
     * Converts the access token instance into a string equivalent to its code.
     *
     * @since 0.1
     *
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
