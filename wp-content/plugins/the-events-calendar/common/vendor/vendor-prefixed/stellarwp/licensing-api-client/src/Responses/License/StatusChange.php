<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a license status change response.
 *
 * @implements Response<array{license_key: string, status: string}>
 */
final class StatusChange implements Response
{
	public string $licenseKey;

	public string $status;

	private function __construct(string $licenseKey, string $status) {
		$this->licenseKey = $licenseKey;
		$this->status     = $status;
	}

	/**
	 * @param array{license_key: string, status: string} $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['license_key'],
			$attributes['status']
		);
	}

	/**
	 * @return array{license_key: string, status: string}
	 */
	public function toArray(): array {
		return [
			'license_key' => $this->licenseKey,
			'status'      => $this->status,
		];
	}
}
