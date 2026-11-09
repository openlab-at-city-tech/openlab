<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;

/**
 * Represents a credit pool delete request payload.
 *
 * @phpstan-type DeletePoolPayload array{
 *     license_key: string,
 *     pool_id: int,
 *     expires_at?: string
 * }
 */
final class DeletePool
{
	use InteractsWithDateTime;

	/**
	 * License key that owns the credit pool.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Credit pool identifier to delete.
	 *
	 * @example 42
	 */
	public int $poolId;

	/**
	 * Optional expiration timestamp to soft-delete the pool at a specific time.
	 *
	 * @example 2026-04-01T00:00:00Z
	 */
	public ?DateTimeImmutable $expiresAt;

	public function __construct(string $licenseKey, int $poolId, ?DateTimeImmutable $expiresAt = null) {
		$this->licenseKey = $licenseKey;
		$this->poolId     = $poolId;
		$this->expiresAt  = $expiresAt;
	}

	/**
	 * @return DeletePoolPayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key' => $this->licenseKey,
			'pool_id'     => $this->poolId,
			'expires_at'  => $this->expiresAt ? $this->formatDateTime($this->expiresAt) : null,
		], static fn ($value): bool => $value !== null);
	}
}
