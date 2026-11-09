<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

/**
 * Represents a fully resolved licensing API URI.
 */
final class ApiUri
{
	private string $uri;

	public function __construct(string $uri) {
		$this->uri = $uri;
	}

	public function get(): string {
		return $this->uri;
	}
}
