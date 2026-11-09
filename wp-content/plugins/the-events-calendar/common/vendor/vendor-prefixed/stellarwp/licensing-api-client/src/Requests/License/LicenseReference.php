<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License;

use InvalidArgumentException;

/**
 * Represents a license reference identified by license key or identity ID.
 *
 * @phpstan-type LicenseReferencePayload array{
 *     license_key?: string,
 *     identity_id?: string
 * }
 */
final class LicenseReference
{
	/**
	 * License key identifier.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public ?string $licenseKey;

	/**
	 * Customer identity identifier.
	 *
	 * @example identity_123
	 */
	public ?string $identityId;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(?string $licenseKey = null, ?string $identityId = null) {
		if ($licenseKey === null && $identityId === null) {
			throw new InvalidArgumentException('Either licenseKey or identityId is required.');
		}

		$this->licenseKey = $licenseKey;
		$this->identityId = $identityId;
	}

	/**
	 * @return LicenseReferencePayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key' => $this->licenseKey,
			'identity_id' => $this->identityId,
		], static fn ($value): bool => $value !== null);
	}
}
