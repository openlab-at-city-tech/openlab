<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\Psr7\MimeType;
use RebelCode\Psr7\Utils;
use RebelCode\WordPress\Http\Middleware;

/**
 * Prepares requests that contain a body, adding the Content-Length and Content-Type headers.
 */
class PrepareBody extends Middleware
{
    /** @inheritDoc */
    public function handle(RequestInterface $request): ResponseInterface
    {
        // Don't do anything if the request has no body
        if ($request->getBody()->getSize() === 0) {
            return $this->next($request);
        }

        $modify = [];

        // Add a default content-type if possible.
        if (!$request->hasHeader('Content-Type')) {
            if ($uri = $request->getBody()->getMetadata('uri')) {
                if (is_string($uri) && $type = MimeType::fromFilename($uri)) {
                    $modify['set_headers']['Content-Type'] = $type;
                }
            }
        }

        // Add a default content-length or transfer-encoding header.
        if (!$request->hasHeader('Content-Length') && !$request->hasHeader('Transfer-Encoding')) {
            $size = $request->getBody()->getSize();
            if ($size !== null) {
                $modify['set_headers']['Content-Length'] = $size;
            } else {
                $modify['set_headers']['Transfer-Encoding'] = 'chunked';
            }
        }

        return $this->next(Utils::modifyRequest($request, $modify));
    }
}
