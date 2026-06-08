<?php

namespace RebelCode\Spotlight\Instagram\RestApi\Auth;

use RebelCode\Spotlight\Instagram\RestApi\AuthGuardInterface;
use WP_REST_Request;

/**
 * A REST API auth handler that checks if the request is sent by a logged in user that has a specific capability.
 *
 * @since 0.1
 */
class AuthUserCapability implements AuthGuardInterface
{
    /**
     * @since 0.1
     *
     * @var string
     */
    protected $capability;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $capability The required user capability.
     */
    public function __construct(string $capability)
    {
        $this->capability = $capability;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getAuthErrors(WP_REST_Request $request) : array
    {
        $userId = get_current_user_id();

        if ($userId === 0) {
            return ['You must be logged in'];
        }

        if (!user_can($userId, $this->capability)) {
            return ['You do not have permission to complete this action'];
        }

        return [];
    }
}
