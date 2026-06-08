<?php

namespace RebelCode\Spotlight\Instagram\Wp;

use WP_REST_Request;

/**
 * Extension class for the vanilla WordPress {@link WP_REST_Request} class.
 *
 * @since 0.2.3
 */
class RestRequest
{
    /**
     * Checks if a request has a parameter.
     *
     * If the {@link WP_REST_Request::has_param} method is available (WordPress v5.3 or later), this function will
     * simply forward the call to that method. Otherwise, a normal array key existence check on the entire list of
     * parameters is performed. The latter is most noticeably slower for params at the end of the request, since the
     * WordPress 5.3 implementation can terminate early once it has found a match.
     *
     * @since 0.2.3
     *
     * @param string $key The key of the param to check for.
     *
     * @return bool True if the param exists in the request, false if not.
     */
    public static function has_param(WP_REST_Request $request, $key)
    {
        return method_exists($request, 'has_param')
            ? $request->has_param($key)
            : array_key_exists($key, $request->get_params());
    }
}
