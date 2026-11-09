<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient;

use InvalidArgumentException;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\RetryPolicy;
use TEC\Common\LiquidWeb\LicensingApiClient\Value\AuthToken;

/**
 * Stores stable client configuration shared across API requests.
 */
final class Config
{
	public string $baseUri;

	public ?AuthToken $configuredToken;

	public string $userAgent;

	public RetryPolicy $retryPolicy;

	public string $restNamespace;

	public string $apiRoot;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		string $baseUri,
		?string $configuredToken = null,
		?string $userAgent = null,
		?RetryPolicy $retryPolicy = null,
		?string $restNamespace = null,
		?string $apiRoot = null
	) {
		$baseUri       = rtrim($baseUri, '/');
		$apiRoot       = trim(($apiRoot ?? 'api'), '/');
		$restNamespace = trim(($restNamespace ?? 'liquidweb'), '/');

		if ($baseUri === '') {
			throw new InvalidArgumentException('Base URI cannot be empty.');
		}

		if ($apiRoot === '') {
			throw new InvalidArgumentException('API root cannot be empty.');
		}

		if ($restNamespace === '') {
			throw new InvalidArgumentException('REST namespace cannot be empty.');
		}

		$this->baseUri         = $baseUri;
		$this->configuredToken = $configuredToken !== null ? new AuthToken($configuredToken) : null;
		$this->userAgent       = $userAgent !== null && $userAgent !== '' ? $userAgent : 'stellarwp/licensing-api-client';
		$this->apiRoot         = $apiRoot;
		$this->restNamespace   = $restNamespace;
		$this->retryPolicy     = $retryPolicy ?: RetryPolicy::default();
	}

	/**
	 * @param array{
	 *     base_uri?: non-empty-string,
	 *     configured_token?: non-empty-string|null,
	 *     user_agent?: non-empty-string|null,
	 *     retry_policy?: RetryPolicy|null,
	 *     rest_namespace?: non-empty-string|null,
	 *     api_root?: non-empty-string|null
	 * } $config
	 *
	 * @throws InvalidArgumentException
	 */
	public static function fromArray(array $config): self {
		return new self(
			$config['base_uri'] ?? '',
			$config['configured_token'] ?? null,
			$config['user_agent'] ?? null,
			$config['retry_policy'] ?? null,
			$config['rest_namespace'] ?? null,
			$config['api_root'] ?? null
		);
	}

	public function apiRootPath(): string {
		return '/' . $this->apiRoot;
	}
}
