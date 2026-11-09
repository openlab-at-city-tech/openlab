<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Token;

/**
 * Represents a token revocation request payload.
 *
 * @phpstan-type RevokeTokenPayload array{
 *     license_key: string,
 *     domain: string
 * }
 */
final class Revoke
{
	public string $licenseKey;

	public string $domain;

	public function __construct(string $licenseKey, string $domain) {
		$this->licenseKey = $licenseKey;
		$this->domain     = $domain;
	}

	/**
	 * @return RevokeTokenPayload
	 */
	public function toArray(): array {
		return [
			'license_key' => $this->licenseKey,
			'domain'      => $this->domain,
		];
	}
}
