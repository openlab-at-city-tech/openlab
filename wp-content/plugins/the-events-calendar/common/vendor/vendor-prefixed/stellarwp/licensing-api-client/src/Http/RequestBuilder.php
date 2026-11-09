<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

use JsonException;
use TEC\Common\LiquidWeb\LicensingApiClient\Value\AuthToken;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Builds PSR-7 requests from SDK configuration and endpoint input.
 *
 * @phpstan-type QueryScalar string|int|float|bool|null
 * @phpstan-type QueryList list<QueryScalar>
 * @phpstan-type QueryValue QueryScalar|QueryList
 * @phpstan-type HeaderValue string|int|float|bool
 * @phpstan-type JsonScalar string|int|float|bool|null
 * @phpstan-type JsonCollection array<array-key, JsonScalar|array<array-key, JsonScalar|array<array-key, JsonScalar|null>|null>|null>
 * @phpstan-type JsonObject array<string, JsonScalar|JsonCollection|null>
 */
final class RequestBuilder
{
	private RequestFactoryInterface $requestFactory;

	private StreamFactoryInterface $streamFactory;

	public function __construct(
		RequestFactoryInterface $requestFactory,
		StreamFactoryInterface $streamFactory
	) {
		$this->requestFactory = $requestFactory;
		$this->streamFactory  = $streamFactory;
	}

	/**
	 * @param array<string, QueryValue> $query
	 * @param JsonObject|null           $body
	 * @param array<string, HeaderValue> $headers
	 *
	 * @throws JsonException
	 */
	public function build(
		string $method,
		ApiUri $uri,
		array $query = [],
		?array $body = null,
		?AuthToken $token = null,
		array $headers = []
	): RequestInterface {
		$request = $this->requestFactory->createRequest($method, $this->buildUri($uri, $query));

		return $this->finalizeRequest($request, $body, $token, $headers);
	}

	/**
	 * @param JsonObject|null            $body
	 * @param array<string, HeaderValue> $headers
	 *
	 * @throws JsonException
	 */
	private function finalizeRequest(
		RequestInterface $request,
		?array $body = null,
		?AuthToken $token = null,
		array $headers = []
	): RequestInterface {

		if ($token !== null) {
			$request = $request->withHeader('X-LWS-Token', $token->get());
		}

		foreach ($headers as $name => $value) {
			$request = $request->withHeader($name, (string) $value);
		}

		if ($body !== null) {
			$request = $request->withHeader('Content-Type', 'application/json');
			$request = $request->withBody(
				$this->streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR))
			);
		}

		return $request;
	}

	/**
	 * @param array<string, QueryValue> $query
	 */
	private function buildUri(ApiUri $uri, array $query): string {
		$uri = $uri->get();

		$queryString = $this->buildQueryString($query);

		if ($queryString === '') {
			return $uri;
		}

		return $uri . (strpos($uri, '?') === false ? '?' : '&') . $queryString;
	}

	/**
	 * @param array<string, QueryValue> $query
	 */
	private function buildQueryString(array $query): string {
		$pairs = [];

		foreach ($query as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $item) {
					if ($item === null) {
						continue;
					}

					$pairs[] = rawurlencode($key . '[]') . '=' . rawurlencode((string) $item);
				}

				continue;
			}

			if ($value === null) {
				continue;
			}

			$pairs[] = rawurlencode($key) . '=' . rawurlencode((string) $value);
		}

		return implode('&', $pairs);
	}
}
