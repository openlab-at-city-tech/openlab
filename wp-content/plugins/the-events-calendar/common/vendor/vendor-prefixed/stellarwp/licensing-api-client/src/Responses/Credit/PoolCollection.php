<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\CreditPool;

/**
 * Represents a keyed collection of managed credit pools.
 *
 * @implements Response<array{
 *     pools: array<int, array{
 *         pool_id: int,
 *         credit_type: string,
 *         credits_total: int,
 *         credits_used: int,
 *         overage_limit: ?int,
 *         priority: int,
 *         period: string,
 *         first_period_start: ?string,
 *         expires_at: ?string,
 *         is_expired: bool
 *     }>
 * }>
 */
final class PoolCollection implements Response
{
	/**
	 * @var array<int, CreditPool>
	 */
	public array $pools;

	/**
	 * @param array<int, CreditPool> $pools
	 */
	private function __construct(array $pools) {
		$this->pools = $pools;
	}

	/**
	 * @param array{
	 *     pools: array<int|string, array{
	 *         pool_id: int,
	 *         credit_type: string,
	 *         credits_total: int,
	 *         credits_used: int,
	 *         overage_limit: ?int,
	 *         priority: int,
	 *         period: string,
	 *         first_period_start: ?string,
	 *         expires_at: ?string,
	 *         is_expired: bool
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$pools = [];

		foreach ($attributes['pools'] as $poolId => $pool) {
			$pools[(int) $poolId] = CreditPool::from($pool);
		}

		return new self($pools);
	}

	/**
	 * @return array{
	 *     pools: array<int, array{
	 *         pool_id: int,
	 *         credit_type: string,
	 *         credits_total: int,
	 *         credits_used: int,
	 *         overage_limit: ?int,
	 *         priority: int,
	 *         period: string,
	 *         first_period_start: ?string,
	 *         expires_at: ?string,
	 *         is_expired: bool
	 *     }>
	 * }
	 */
	public function toArray(): array {
		$pools = array_map(static fn ($pool) => $pool->toArray(), $this->pools);

		return [
			'pools' => $pools,
		];
	}
}
