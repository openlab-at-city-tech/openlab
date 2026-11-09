<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents one managed credit pool entry.
 *
 * @implements Response<array{
 *     pool_id: int,
 *     credit_type: string,
 *     credits_total: int,
 *     credits_used: int,
 *     overage_limit: ?int,
 *     priority: int,
 *     period: string,
 *     first_period_start: ?string,
 *     expires_at: ?string,
 *     is_expired: bool
 * }>
 */
final class CreditPool implements Response
{
	use InteractsWithDateTime;

	public int $poolId;

	public string $creditType;

	public int $creditsTotal;

	public int $creditsUsed;

	public ?int $overageLimit;

	public int $priority;

	public string $period;

	public ?DateTimeImmutable $firstPeriodStart;

	public ?DateTimeImmutable $expiresAt;

	public bool $isExpired;

	private function __construct(
		int $poolId,
		string $creditType,
		int $creditsTotal,
		int $creditsUsed,
		?int $overageLimit,
		int $priority,
		string $period,
		?DateTimeImmutable $firstPeriodStart,
		?DateTimeImmutable $expiresAt,
		bool $isExpired
	) {
		$this->poolId           = $poolId;
		$this->creditType       = $creditType;
		$this->creditsTotal     = $creditsTotal;
		$this->creditsUsed      = $creditsUsed;
		$this->overageLimit     = $overageLimit;
		$this->priority         = $priority;
		$this->period           = $period;
		$this->firstPeriodStart = $firstPeriodStart;
		$this->expiresAt        = $expiresAt;
		$this->isExpired        = $isExpired;
	}

	/**
	 * @param array{
	 *     pool_id: int,
	 *     credit_type: string,
	 *     credits_total: int,
	 *     credits_used: int,
	 *     overage_limit: ?int,
	 *     priority: int,
	 *     period: string,
	 *     first_period_start: ?string,
	 *     expires_at: ?string,
	 *     is_expired: bool
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['pool_id'],
			$attributes['credit_type'],
			$attributes['credits_total'],
			$attributes['credits_used'],
			$attributes['overage_limit'],
			$attributes['priority'],
			$attributes['period'],
			self::parseNullableDateTime($attributes['first_period_start']),
			self::parseNullableDateTime($attributes['expires_at']),
			$attributes['is_expired']
		);
	}

	/**
	 * @return array{
	 *     pool_id: int,
	 *     credit_type: string,
	 *     credits_total: int,
	 *     credits_used: int,
	 *     overage_limit: ?int,
	 *     priority: int,
	 *     period: string,
	 *     first_period_start: ?string,
	 *     expires_at: ?string,
	 *     is_expired: bool
	 * }
	 */
	public function toArray(): array {
		return [
			'pool_id'            => $this->poolId,
			'credit_type'        => $this->creditType,
			'credits_total'      => $this->creditsTotal,
			'credits_used'       => $this->creditsUsed,
			'overage_limit'      => $this->overageLimit,
			'priority'           => $this->priority,
			'period'             => $this->period,
			'first_period_start' => $this->firstPeriodStart ? $this->formatDateTime($this->firstPeriodStart) : null,
			'expires_at'         => $this->expiresAt ? $this->formatDateTime($this->expiresAt) : null,
			'is_expired'         => $this->isExpired,
		];
	}
}
