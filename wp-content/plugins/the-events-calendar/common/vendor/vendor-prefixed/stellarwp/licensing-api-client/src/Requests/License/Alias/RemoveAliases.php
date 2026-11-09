<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias;

use InvalidArgumentException;

/**
 * Represents an alias removal request payload.
 *
 * @phpstan-type RemoveAliasesPayload array{
 *     license_key?: string,
 *     identity_id?: string,
 *     alias_key?: string
 * }
 */
final class RemoveAliases
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
	 * Specific alias key to remove. Omit to remove all aliases.
	 *
	 * @example LEGACY-KEY-123
	 */
	public ?string $aliasKey;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(?string $licenseKey = null, ?string $identityId = null, ?string $aliasKey = null) {
		if ($licenseKey === null && $identityId === null) {
			throw new InvalidArgumentException('Either licenseKey or identityId is required.');
		}

		$this->licenseKey = $licenseKey;
		$this->identityId = $identityId;
		$this->aliasKey   = $aliasKey;
	}

	/**
	 * @return RemoveAliasesPayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key' => $this->licenseKey,
			'identity_id' => $this->identityId,
			'alias_key'   => $this->aliasKey,
		], static fn ($value): bool => $value !== null);
	}
}
