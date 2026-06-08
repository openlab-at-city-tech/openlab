<?php

namespace RebelCode\Spotlight\Instagram\RestApi;

/**
 * A simple implementation of a REST API endpoint.
 *
 * @since 0.1
 */
class EndPoint
{
    /**
     * The endpoint's route.
     *
     * @since 0.1
     *
     * @var string
     */
    public $route;

    /**
     * The endpoint's accepted HTTP methods.
     *
     * @since 0.1
     *
     * @var string[]
     */
    public $methods;

    /**
     * The endpoint's handler.
     *
     * @since 0.1
     *
     * @var callable
     */
    public $handler;

    /**
     * The endpoint's authorization handler, if any.
     *
     * @since 0.1
     *
     * @var AuthGuardInterface|null
     */
    public $authHandler;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string                  $route       The route.
     * @param string[]                $methods     The accepted HTTP methods.
     * @param callable                $handler     The handler.
     * @param AuthGuardInterface|null $authHandler Optional authorization handler.
     */
    public function __construct($route, array $methods, callable $handler, ?AuthGuardInterface $authHandler = null)
    {
        $this->route = $route;
        $this->methods = $methods;
        $this->handler = $handler;
        $this->authHandler = $authHandler;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getAuthHandler()
    {
        return $this->authHandler;
    }
}
