<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Token;

/**
 * Represents a token creation request payload.
 *
 * @phpstan-type CreateTokenPayload array{
 *     license_key: string,
 *     domain: string
 * }
 */
final class Create
{
	public string $licenseKey;

	public string $domain;

	public function __construct(string $licenseKey, string $domain) {
		$this->licenseKey = $licenseKey;
		$this->domain     = $domain;
	}

	/**
	 * @return CreateTokenPayload
	 */
	public function toArray(): array {
		return [
			'license_key' => $this->licenseKey,
			'domain'      => $this->domain,
		];
	}
}
