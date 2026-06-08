<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\WordPress\Http\Exception\BadResponseException;
use RebelCode\WordPress\Http\Middleware;

/**
 * A middleware handler that throws exceptions for responses with 4xx or 5xx status codes.
 */
class HttpErrorsToExceptions extends Middleware
{
    /**
     * @inheritDoc
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $response = $this->next($request);
        $code = $response->getStatusCode();

        if ($code < 400) {
            return $response;
        }

        throw BadResponseException::create($request, $response);
    }
}
