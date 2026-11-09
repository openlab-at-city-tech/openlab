<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http\Factories;

use TEC\Common\LiquidWeb\LicensingApiClient\Config;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\ApiUri;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\ApiVersion;

/**
 * Builds fully resolved licensing API URIs from endpoints and trusted pagination links.
 */
final class ApiUriFactory
{
	private Config $config;

	private ApiVersion $defaultVersion;

	public function __construct(Config $config, ApiVersion $defaultVersion) {
		$this->config         = $config;
		$this->defaultVersion = $defaultVersion;
	}

	public function make(string $path): ApiUri {
		return $this->makeWithVersion($path, $this->defaultVersion);
	}

	public function makeWithVersion(string $path, ApiVersion $version): ApiUri {
		return new ApiUri(
			rtrim($this->config->baseUri, '/')
			. $this->config->apiRootPath()
			. '/'
			. $this->config->restNamespace
			. '/'
			. $version->get()
			. '/'
			. ltrim($path, '/')
		);
	}

	/**
	 * @throws UnexpectedResponseException
	 */
	public function fromPaginationLink(string $uri): ApiUri {
		$target = parse_url($uri);
		$base   = parse_url($this->config->baseUri);

		if (! is_array($target) || ! is_array($base)) {
			throw new UnexpectedResponseException('Unexpected pagination link.');
		}

		$targetScheme = $this->requireString($target, 'scheme');
		$baseScheme   = $this->requireString($base, 'scheme');
		$targetHost   = $this->requireString($target, 'host');
		$baseHost     = $this->requireString($base, 'host');
		$targetPort   = $target['port'] ?? null;
		$basePort     = $base['port'] ?? null;
		$targetPath   = $this->requireString($target, 'path');

		if (
			strtolower($targetScheme) !== strtolower($baseScheme)
			|| strtolower($targetHost) !== strtolower($baseHost)
			|| $targetPort !== $basePort
		) {
			throw new UnexpectedResponseException('Unexpected pagination link origin.');
		}

		if (strpos($targetPath, $this->config->apiRootPath() . '/' . $this->config->restNamespace . '/') !== 0) {
			throw new UnexpectedResponseException('Unexpected pagination link path.');
		}

		return new ApiUri($uri);
	}

	/**
	 * @param array<string, mixed> $parts
	 *
	 * @throws UnexpectedResponseException
	 */
	private function requireString(array $parts, string $key): string {
		$value = $parts[$key] ?? null;

		if (! is_string($value)) {
			throw new UnexpectedResponseException('Unexpected pagination link.');
		}

		return $value;
	}
}
