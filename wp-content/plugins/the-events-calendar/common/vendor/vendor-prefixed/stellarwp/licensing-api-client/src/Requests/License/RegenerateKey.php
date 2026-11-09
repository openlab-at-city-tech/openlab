<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License;

/**
 * Represents a regenerate-key request payload.
 *
 * @phpstan-type RegenerateKeyPayload array{
 *     identity_id: string
 * }
 */
final class RegenerateKey
{
	/**
	 * Identity identifier whose license key should be regenerated.
	 *
	 * @example identity_123
	 */
	public string $identityId;

	public function __construct(string $identityId) {
		$this->identityId = $identityId;
	}

	/**
	 * @return RegenerateKeyPayload
	 */
	public function toArray(): array {
		return [
			'identity_id' => $this->identityId,
		];
	}
}
