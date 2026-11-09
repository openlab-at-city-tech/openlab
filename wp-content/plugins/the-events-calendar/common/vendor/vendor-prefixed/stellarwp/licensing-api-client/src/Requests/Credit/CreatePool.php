<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;

/**
 * Represents a credit pool creation request payload.
 *
 * @phpstan-type CreatePoolPayload array{
 *     license_key: string,
 *     credit_type: string,
 *     credits_total: int,
 *     period: string,
 *     overage_limit?: int,
 *     priority: int,
 *     first_period_start?: string,
 *     expires_at?: string,
 *     metadata?: array<string, mixed>
 * }
 */
final class CreatePool
{
	use InteractsWithDateTime;

	/**
	 * License key that owns the credit pool.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Credit type tracked by this pool.
	 *
	 * @example ai
	 */
	public string $creditType;

	/**
	 * Total credits allocated to the pool.
	 *
	 * @example 1000
	 */
	public int $creditsTotal;

	/**
	 * Reset cadence for the pool.
	 *
	 * @example month
	 */
	public string $period;

	/**
	 * Optional overage allowance after the pool is depleted.
	 *
	 * @example 100
	 */
	public ?int $overageLimit;

	/**
	 * Pool consumption priority. Lower values are consumed first.
	 *
	 * @example 25
	 */
	public int $priority;

	/**
	 * First billing period start in the caller's timezone.
	 *
	 * @example 2026-03-01T00:00:00Z
	 */
	public ?DateTimeImmutable $firstPeriodStart;

	/**
	 * Absolute pool expiration date in the caller's timezone.
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
		string $creditType,
		int $creditsTotal,
		string $period,
		?int $overageLimit = null,
		int $priority = 50,
		?DateTimeImmutable $firstPeriodStart = null,
		?DateTimeImmutable $expiresAt = null,
		?array $metadata = null
	) {
		$this->licenseKey       = $licenseKey;
		$this->creditType       = $creditType;
		$this->creditsTotal     = $creditsTotal;
		$this->period           = $period;
		$this->overageLimit     = $overageLimit;
		$this->priority         = $priority;
		$this->firstPeriodStart = $firstPeriodStart;
		$this->expiresAt        = $expiresAt;
		$this->metadata         = $metadata;
	}

	/**
	 * @return CreatePoolPayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key'        => $this->licenseKey,
			'credit_type'        => $this->creditType,
			'credits_total'      => $this->creditsTotal,
			'period'             => $this->period,
			'overage_limit'      => $this->overageLimit,
			'priority'           => $this->priority,
			'first_period_start' => $this->firstPeriodStart ? $this->formatDateTime($this->firstPeriodStart) : null,
			'expires_at'         => $this->expiresAt ? $this->formatDateTime($this->expiresAt) : null,
			'metadata'           => $this->metadata,
		], static fn ($value): bool => $value !== null);
	}
}
