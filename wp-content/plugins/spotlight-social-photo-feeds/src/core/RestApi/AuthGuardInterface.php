<?php

namespace RebelCode\Spotlight\Instagram\RestApi;

use WP_REST_Request;

/**
 * Represents an authorization guard for the REST API.
 *
 * Objects that implement this interface are used to determine if a client is authorized to carry out a specific
 * request.
 *
 * @since 0.1
 */
interface AuthGuardInterface
{
    /**
     * Retrieves any
     *
     * @since 0.1
     *
     * @param WP_REST_Request $request The request.
     *
     * @return array|null Any authorization error messages. An empty array signifies that the request is authorized.
     */
    public function getAuthErrors(WP_REST_Request $request) : array;
}
