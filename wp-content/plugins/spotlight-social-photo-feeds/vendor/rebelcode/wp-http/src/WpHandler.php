<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\Psr7\Response;

use function wp_remote_request;

/**
 * A handler that uses the WordPress HTTP API to dispatch requests.
 *
 * @see https://developer.wordpress.org/reference/functions/wp_remote_request/
 *
 * @psalm-type WpHandlerOptions = array{
 *  timeout?: int,
 *  redirection?: int,
 *  user-agent?: string,
 *  reject_unsafe_urls?: bool,
 *  blocking?: bool,
 *  compress?: bool,
 *  decompress?: bool,
 *  sslverify?: bool,
 *  sslcertificates?: string,
 *  stream?: bool,
 *  filename?: string,
 *  limit_response_size?: int,
 * }
 */
class WpHandler implements HandlerInterface
{
    /**
     * @var array<string, mixed>
     *
     * @psalm-var WpHandlerOptions
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param array<string, mixed>   $options
     *
     * @psalm-param WpHandlerOptions $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $uri = (string) $request->getUri();
        $args = $this->prepareArgs($request);
        $httpVer = $request->getProtocolVersion();

        $responseData = wp_remote_request($uri, $args);

        $code = wp_remote_retrieve_response_code($responseData);
        $code = is_numeric($code) ? (int) $code : 400;
        $reason = wp_remote_retrieve_response_message($responseData);
        $headers = wp_remote_retrieve_headers($responseData);
        $headers = is_array($headers) ? $headers : iterator_to_array($headers);
        $body = wp_remote_retrieve_body($responseData);

        return new Response($code, $headers, $body, $httpVer, $reason);
    }

    /**
     * Prepares the args array for a specific request. The result can be used with WordPress' remote functions.
     *
     * @param RequestInterface $request The request.
     *
     * @return array<string, mixed> The prepared args array.
     *
     * @psalm-return WpHandlerOptions
     */
    protected function prepareArgs(RequestInterface $request): array
    {
        return array_merge($this->options, [
            'method' => $request->getMethod(),
            'httpversion' => $request->getProtocolVersion(),
            'headers' => $this->prepareHeaders($request),
            'body' => (string) $request->getBody(),
        ]);
    }

    /**
     * Transforms a request's headers into the format expected by WordPress' remote functions.
     *
     * @param RequestInterface $request The request.
     *
     * @return array<string, string> The prepared headers array.
     */
    protected function prepareHeaders(RequestInterface $request): array
    {
        $headers = [];

        foreach ($request->getHeaders() as $header => $values) {
            $headers[$header] = $request->getHeaderLine($header);
        }

        return $headers;
    }
}
