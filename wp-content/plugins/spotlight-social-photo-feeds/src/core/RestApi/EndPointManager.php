<?php

namespace RebelCode\Spotlight\Instagram\RestApi;

use WP_Error;
use WP_REST_Request;

/**
 * A REST API endpoint manager.
 *
 * @since 0.1
 */
class EndPointManager
{
    /**
     * The namespace.
     *
     * @since 0.1
     *
     * @var string
     */
    protected $namespace;

    /**
     * The REST API endpoints.
     *
     * @since 0.1
     *
     * @var EndPoint[]
     */
    protected $endPoints;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string     $namespace The namespace.
     * @param EndPoint[] $endPoints The REST API endpoints.
     */
    public function __construct($namespace, $endPoints)
    {
        $this->namespace = $namespace;
        $this->endPoints = $endPoints;
    }

    /**
     * Registers the routes and endpoints with WordPress.
     *
     * @since 0.1
     */
    public function register()
    {
        foreach ($this->endPoints as $endPoint) {
            $route = $endPoint->getRoute();
            $methods = $endPoint->getMethods();
            $handler = $endPoint->getHandler();
            $authFn = $endPoint->getAuthHandler();

            register_rest_route($this->namespace, $route, [
                'methods' => $methods,
                'callback' => $handler,
                'permission_callback' => $this->getPermissionCallback($authFn),
            ]);
        }
    }

    /**
     * Retrieves the permissions callback for an auth guard.
     *
     * @since 0.1
     *
     * @param AuthGuardInterface|null $auth The auth guard instance, if any.
     *
     * @return callable The callback.
     */
    protected function getPermissionCallback(?AuthGuardInterface $auth = null)
    {
        if ($auth === null) {
            return '__return_true';
        }

        return function (WP_REST_Request $request) use ($auth) {
            $errors = $auth->getAuthErrors($request);

            if (count($errors) === 0) {
                return true;
            }

            return new WP_Error('unauthorized', 'Unauthorized', [
                'status' => 401,
                'reasons' => $errors,
            ]);
        };
    }
}
