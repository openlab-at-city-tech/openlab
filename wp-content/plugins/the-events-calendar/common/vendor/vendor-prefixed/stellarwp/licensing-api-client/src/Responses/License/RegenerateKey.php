<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a regenerate key response.
 *
 * @implements Response<array{license_key: string}>
 */
final class RegenerateKey implements Response
{
	public string $licenseKey;

	private function __construct(string $licenseKey) {
		$this->licenseKey = $licenseKey;
	}

	/**
	 * @param array{license_key: string} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['license_key']);
	}

	/**
	 * @return array{license_key: string}
	 */
	public function toArray(): array {
		return [
			'license_key' => $this->licenseKey,
		];
	}
}
