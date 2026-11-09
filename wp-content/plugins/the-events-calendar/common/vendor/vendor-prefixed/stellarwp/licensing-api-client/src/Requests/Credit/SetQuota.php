<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;

/**
 * Represents a site quota write request payload.
 *
 * `quota` semantics:
 * - `null` means uncapped and is omitted from the outgoing payload
 * - `0` means blocked
 * - a positive integer means a fixed quota
 *
 * `firstPeriodStart` is normalized to the API's canonical UTC datetime format.
 *
 * @phpstan-type SetQuotaPayload array{
 *     license_key: string,
 *     domain: string,
 *     credit_type: string,
 *     quota?: int,
 *     period: string,
 *     first_period_start?: string,
 *     metadata?: array<string, mixed>
 * }
 */
final class SetQuota
{
	use InteractsWithDateTime;

	/**
	 * License key that owns the quota.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Site domain the quota applies to.
	 *
	 * @example example.com
	 */
	public string $domain;

	/**
	 * Credit type identifier.
	 *
	 * @example ai
	 */
	public string $creditType;

	/**
	 * Quota amount for the domain.
	 *
	 * @example null Uncapped
	 * @example 0 Blocked
	 * @example 100 Fixed quota
	 */
	public ?int $quota;

	/**
	 * Quota period strategy.
	 *
	 * @example lifetime
	 * @example month
	 * @example week
	 */
	public string $period;

	/**
	 * UTC anchor for period calculations.
	 *
	 * @example new DateTimeImmutable('2026-03-01T00:00:00Z')
	 */
	public ?DateTimeImmutable $firstPeriodStart;

	/**
	 * Structured metadata passed through to the API.
	 *
	 * @var array<string, mixed>|null
	 *
	 * @example ['source' => 'portal']
	 */
	public ?array $metadata;

	/**
	 * @param array<string, mixed>|null $metadata
	 */
	public function __construct(
		string $licenseKey,
		string $domain,
		string $creditType,
		?int $quota = null,
		string $period = 'lifetime',
		?DateTimeImmutable $firstPeriodStart = null,
		?array $metadata = null
	) {
		$this->licenseKey       = $licenseKey;
		$this->domain           = $domain;
		$this->creditType       = $creditType;
		$this->quota            = $quota;
		$this->period           = $period;
		$this->firstPeriodStart = $firstPeriodStart;
		$this->metadata         = $metadata;
	}

	/**
	 * @return SetQuotaPayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key'        => $this->licenseKey,
			'domain'             => $this->domain,
			'credit_type'        => $this->creditType,
			'quota'              => $this->quota,
			'period'             => $this->period,
			'first_period_start' => $this->firstPeriodStart ? $this->formatDateTime($this->firstPeriodStart) : null,
			'metadata'           => $this->metadata,
		], static fn ($value): bool => $value !== null);
	}
}
