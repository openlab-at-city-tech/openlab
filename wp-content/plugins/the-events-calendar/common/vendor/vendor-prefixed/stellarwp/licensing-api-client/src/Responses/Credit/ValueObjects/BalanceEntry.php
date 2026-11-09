<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents the credit balance for a single credit type.
 *
 * @implements Response<array{
 *     credit_type: string,
 *     remaining: int,
 *     site_quota: int|null,
 *     site_used: int,
 *     site_remaining: int,
 *     aggregate_total: int,
 *     aggregate_used: int,
 *     aggregate_remaining: int,
 *     aggregate_overage: int,
 *     pools: list<array{
 *         pool_id: int,
 *         pool_remaining: int,
 *         priority: int,
 *         period: string,
 *         resets_on: string|null,
 *         expires_at: string|null,
 *         credits_total?: int,
 *         credits_used?: int,
 *         overage?: int,
 *         overage_limit?: int|null
 *     }>
 * }>
 */
final class BalanceEntry implements Response
{
	public string $creditType;

	public int $remaining;

	public ?int $siteQuota;

	public int $siteUsed;

	public int $siteRemaining;

	public int $aggregateTotal;

	public int $aggregateUsed;

	public int $aggregateRemaining;

	public int $aggregateOverage;

	/** @var list<PoolBalance> */
	public array $pools;

	/**
	 * @param list<PoolBalance> $pools
	 */
	private function __construct(
		string $creditType,
		int $remaining,
		?int $siteQuota,
		int $siteUsed,
		int $siteRemaining,
		int $aggregateTotal,
		int $aggregateUsed,
		int $aggregateRemaining,
		int $aggregateOverage,
		array $pools
	) {
		$this->creditType         = $creditType;
		$this->remaining          = $remaining;
		$this->siteQuota          = $siteQuota;
		$this->siteUsed           = $siteUsed;
		$this->siteRemaining      = $siteRemaining;
		$this->aggregateTotal     = $aggregateTotal;
		$this->aggregateUsed      = $aggregateUsed;
		$this->aggregateRemaining = $aggregateRemaining;
		$this->aggregateOverage   = $aggregateOverage;
		$this->pools              = $pools;
	}

	/**
	 * @param array{
	 *     credit_type: string,
	 *     remaining: int,
	 *     site_quota: int|null,
	 *     site_used: int,
	 *     site_remaining: int,
	 *     aggregate_total: int,
	 *     aggregate_used: int,
	 *     aggregate_remaining: int,
	 *     aggregate_overage: int,
	 *     pools: list<array{
	 *         pool_id: int,
	 *         pool_remaining: int,
	 *         priority: int,
	 *         period: string,
	 *         resets_on: string|null,
	 *         expires_at: string|null,
	 *         credits_total?: int,
	 *         credits_used?: int,
	 *         overage?: int,
	 *         overage_limit?: int|null
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['credit_type'],
			$attributes['remaining'],
			$attributes['site_quota'],
			$attributes['site_used'],
			$attributes['site_remaining'],
			$attributes['aggregate_total'],
			$attributes['aggregate_used'],
			$attributes['aggregate_remaining'],
			$attributes['aggregate_overage'],
			array_map(
				static fn (array $pool): PoolBalance => PoolBalance::from($pool),
				$attributes['pools']
			)
		);
	}

	public function toArray(): array {
		return [
			'credit_type'         => $this->creditType,
			'remaining'           => $this->remaining,
			'site_quota'          => $this->siteQuota,
			'site_used'           => $this->siteUsed,
			'site_remaining'      => $this->siteRemaining,
			'aggregate_total'     => $this->aggregateTotal,
			'aggregate_used'      => $this->aggregateUsed,
			'aggregate_remaining' => $this->aggregateRemaining,
			'aggregate_overage'   => $this->aggregateOverage,
			'pools'               => array_map(
				static fn (PoolBalance $pool): array => $pool->toArray(),
				$this->pools
			),
		];
	}
}
