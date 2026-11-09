<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

use InvalidArgumentException;

/**
 * Represents an API version segment used when building endpoint URLs.
 */
final class ApiVersion
{
	public const V1 = 'v1';

	private string $version;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $version) {
		$version = trim($version, '/');

		if ($version === '') {
			throw new InvalidArgumentException('API version cannot be empty.');
		}

		$this->version = $version;
	}

	public static function default(): self {
		return new self(self::V1);
	}

	public function get(): string {
		return $this->version;
	}

	public function __toString(): string {
		return $this->version;
	}
}
