<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http;

use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A middleware is a type of handler that is aware of another handler, referred to as the "next" handler.
 *
 * The middleware can delegate the actual handling to the next handler, but is free to perform any operations before
 * or after the delegation. In this sense, a middleware is a type of decorator for another handler.
 *
 * @psalm-import-type MiddlewareFactory from HandlerStack
 */
abstract class Middleware implements HandlerInterface
{
    /** @var HandlerInterface|null */
    private $handler = null;

    /**
     * Constructor.
     *
     * @param HandlerInterface $handler The handler that the middleware will delegate to.
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Invokes the internal handler's {@link HandlerInterface::handle()} method.
     *
     * @param RequestInterface $request The request to handle.
     *
     * @return ResponseInterface The response returned by the handler.
     */
    final protected function next(RequestInterface $request): ResponseInterface
    {
        if ($this->handler === null) {
            throw new LogicException('Next handler in ' . get_class() . ' is null');
        }

        return $this->handler->handle($request);
    }

    /**
     * Create a factory for middlewares that accept the handler as the first constructor argument.
     *
     * @param string  $className The class name of the middleware for which the factory will create and instance of.
     * @param mixed[] $args      Optional additional constructor arguments.
     *
     * @return callable The created middleware factory function.
     *
     * @psalm-return MiddlewareFactory
     *
     * @psalm-suppress LessSpecificReturnStatement, MoreSpecificReturnType
     */
    public static function factory(string $className, array $args = []): callable
    {
        return function (HandlerInterface $handler) use ($className, $args): Middleware {
            return new $className($handler, ...$args);
        };
    }
}
