<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http;

use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use RebelCode\Psr7\UriResolver;

/**
 * The PSR-18 client implementation.
 *
 * @psalm-import-type WpHandlerOptions from WpHandler
 */
class WpClient implements ClientInterface
{
    /** @var HandlerInterface */
    protected $handler;

    /** @var UriInterface|null */
    protected $baseUri;

    /**
     * Constructor.
     *
     * @param HandlerInterface  $handler The handler to use for dispatching requests and receiving responses.
     * @param UriInterface|null $baseUri Optional base URI for all relative requests sent using this client.
     *
     * @throws InvalidArgumentException If the "base_uri" option is present and is not a valid URI.
     */
    public function __construct(HandlerInterface $handler, ?UriInterface $baseUri = null)
    {
        $this->handler = $handler;
        $this->baseUri = $baseUri;
    }

    /** @inheritDoc */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->handler->handle($this->prepareRequest($request));
    }

    /**
     * Prepares the request before handing off to the handler.
     *
     * @param RequestInterface $request The request to prepare.
     *
     * @return RequestInterface The prepared requested.
     */
    protected function prepareRequest(RequestInterface $request): RequestInterface
    {
        if ($this->baseUri !== null) {
            $preserveHost = $request->hasHeader('Host');
            $requestUri = $request->getUri();
            $resolvedUri = UriResolver::resolve($this->baseUri, $requestUri);

            $request = $request->withUri($resolvedUri, $preserveHost);
        }

        return $request;
    }

    /**
     * Creates a client with the default configuration.
     *
     * @param UriInterface|null      $baseUri Optional base URI for all relative requests sent using this client.
     * @param array<string, mixed>   $options Optional array of options for the underlying {@link WpHandler}.
     *
     * @psalm-param WpHandlerOptions $options
     *
     * @return WpClient The created client instance.
     */
    public static function createDefault(?UriInterface $baseUri = null, array $options = []): WpClient
    {
        return new self(HandlerStack::createDefault($options), $baseUri);
    }
}
