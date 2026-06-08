<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\WordPress\Http\Middleware\HttpErrorsToExceptions;
use RebelCode\WordPress\Http\Middleware\PrepareBody;

/**
 * A handler implementation that composes a base handler and a set of middleware handlers.
 *
 * @psalm-type        MiddlewareFactory = callable(HandlerInterface): Middleware
 * @psalm-import-type WpHandlerOptions from WpHandler
 */
class HandlerStack implements HandlerInterface
{
    /** @var HandlerInterface */
    protected $handler;

    /**
     * @var callable[]
     *
     * @psalm-var MiddlewareFactory[]
     */
    protected $middlewares;

    /** @var HandlerInterface|null */
    protected $cache = null;

    /**
     * Constructor.
     *
     * @param HandlerInterface          $handler     The main handler that handles the transport of the request.
     * @param callable[]                $middlewares Optional list of middleware factory functions, each taking a
     *                                               handler instance as argument and returning the middleware handler.
     *                                               The middleware handlers will be consumed in the order given.
     *
     * @psalm-param MiddlewareFactory[] $middlewares
     */
    public function __construct(HandlerInterface $handler, array $middlewares = [])
    {
        $this->handler = $handler;
        $this->middlewares = $middlewares;
    }

    /** @inheritDoc */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $this->cache = $this->cache ?? $this->resolveStack();

        return $this->cache->handle($request);
    }

    /**
     * Creates a handler stack with the default configuration.
     *
     * @param array<string, mixed>   $options Optional array of options for the underlying {@link WpHandler}.
     *
     * @psalm-param WpHandlerOptions $options
     *
     * @return HandlerStack The created handler stack instance.
     */
    public static function createDefault(array $options = []): HandlerStack
    {
        return new HandlerStack(
            new WpHandler($options),
            [
                Middleware::factory(PrepareBody::class),
                Middleware::factory(HttpErrorsToExceptions::class),
            ]
        );
    }

    /**
     * Resolves the stack of middlewares into a single handler instance.
     *
     * @return HandlerInterface The resolved handler instance.
     */
    protected function resolveStack(): HandlerInterface
    {
        return array_reduce(array_reverse($this->middlewares), [$this, 'createMiddleware'], $this->handler);
    }

    /**
     * Creates a middleware instance using a factory.
     *
     * Used in {@link HandlerStack::resolveStack()} as a callback for {@link array_reduce()}.
     *
     * @param HandlerInterface  $handler The handler to pass to the factory.
     * @param MiddlewareFactory $factory The middleware factory function.
     *
     * @return HandlerInterface The middleware with the applied handler.
     */
    protected function createMiddleware(HandlerInterface $handler, callable $factory): HandlerInterface
    {
        return $factory($handler);
    }
}
