<?php

namespace RebelCode\Spotlight\Instagram\RestApi\Auth;

use RebelCode\Spotlight\Instagram\RestApi\AuthGuardInterface;
use WP_REST_Request;

/**
 * A REST API auth handler implementation that verifies a token in the request.
 *
 * @since 0.3
 */
class AuthVerifyToken implements AuthGuardInterface
{
    /**
     * The token value.
     *
     * @since 0.3
     *
     * @var string
     */
    protected $token;

    /**
     * The name of the header from where the nonce value is read.
     *
     * @since 0.3
     *
     * @var string
     */
    protected $header;

    /**
     * Constructor.
     *
     * @since 0.3
     *
     * @param string $header The name of the header from where the nonce value is read.
     * @param string $token  The token value to validate against.
     */
    public function __construct($header, $token)
    {
        $this->header = $header;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getAuthErrors(WP_REST_Request $request) : array
    {
        $value = $request->get_header($this->header);

        return ($value !== $this->token)
            ? ['Invalid auth token. Please refresh the page.']
            : [];
    }
}
