<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;

/**
 * Represents a credit pool update request payload.
 *
 * @phpstan-type UpdatePoolPayload array{
 *     license_key: string,
 *     pool_id: int,
 *     credits_total?: int,
 *     overage_limit?: int,
 *     priority?: int,
 *     expires_at?: string,
 *     metadata?: array<string, mixed>
 * }
 */
final class UpdatePool
{
	use InteractsWithDateTime;

	/**
	 * License key that owns the credit pool.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Credit pool identifier to update.
	 *
	 * @example 42
	 */
	public int $poolId;

	/**
	 * Updated total credits for the pool.
	 *
	 * @example 1500
	 */
	public ?int $creditsTotal;

	/**
	 * Updated overage allowance after the pool is depleted.
	 *
	 * @example 250
	 */
	public ?int $overageLimit;

	/**
	 * Updated pool priority. Lower values are consumed first.
	 *
	 * @example 10
	 */
	public ?int $priority;

	/**
	 * Updated absolute pool expiration date in the caller's timezone.
	 *
	 * @example 2026-04-01T00:00:00Z
	 */
	public ?DateTimeImmutable $expiresAt;

	/**
	 * Arbitrary pool metadata forwarded to the API.
	 *
	 * @var array<string, mixed>|null
	 *
	 * @example {"source":"portal"}
	 */
	public ?array $metadata;

	/**
	 * @param array<string, mixed>|null $metadata
	 */
	public function __construct(
		string $licenseKey,
		int $poolId,
		?int $creditsTotal = null,
		?int $overageLimit = null,
		?int $priority = null,
		?DateTimeImmutable $expiresAt = null,
		?array $metadata = null
	) {
		$this->licenseKey   = $licenseKey;
		$this->poolId       = $poolId;
		$this->creditsTotal = $creditsTotal;
		$this->overageLimit = $overageLimit;
		$this->priority     = $priority;
		$this->expiresAt    = $expiresAt;
		$this->metadata     = $metadata;
	}

	/**
	 * @return UpdatePoolPayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key'   => $this->licenseKey,
			'pool_id'       => $this->poolId,
			'credits_total' => $this->creditsTotal,
			'overage_limit' => $this->overageLimit,
			'priority'      => $this->priority,
			'expires_at'    => $this->expiresAt ? $this->formatDateTime($this->expiresAt) : null,
			'metadata'      => $this->metadata,
		], static fn ($value): bool => $value !== null);
	}
}
