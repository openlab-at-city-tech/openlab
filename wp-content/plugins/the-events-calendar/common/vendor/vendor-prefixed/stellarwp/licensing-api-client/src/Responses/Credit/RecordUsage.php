<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a successful credit usage response.
 *
 * @implements Response<array{
 *     credits_used: int,
 *     pool_remaining: int,
 *     site_remaining: int|null,
 *     pool_breakdown: array<array-key, int>
 * }>
 */
final class RecordUsage implements Response
{
	public int $creditsUsed;

	public int $poolRemaining;

	public ?int $siteRemaining;

	/**
	 * @var array<int, int>
	 */
	public array $poolBreakdown;

	/**
	 * @param array<int, int> $poolBreakdown
	 */
	private function __construct(int $creditsUsed, int $poolRemaining, ?int $siteRemaining, array $poolBreakdown) {
		$this->creditsUsed   = $creditsUsed;
		$this->poolRemaining = $poolRemaining;
		$this->siteRemaining = $siteRemaining;
		$this->poolBreakdown = $poolBreakdown;
	}

	/**
	 * @param array{
	 *     credits_used: int,
	 *     pool_remaining: int,
	 *     site_remaining: int|null,
	 *     pool_breakdown: array<array-key, int>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$poolBreakdown = [];

		foreach ($attributes['pool_breakdown'] as $poolId => $creditsUsed) {
			$poolBreakdown[(int) $poolId] = $creditsUsed;
		}

		return new self(
			$attributes['credits_used'],
			$attributes['pool_remaining'],
			$attributes['site_remaining'],
			$poolBreakdown
		);
	}

	/**
	 * @return array{
	 *     credits_used: int,
	 *     pool_remaining: int,
	 *     site_remaining: int|null,
	 *     pool_breakdown: array<int, int>
	 * }
	 */
	public function toArray(): array {
		return [
			'credits_used'   => $this->creditsUsed,
			'pool_remaining' => $this->poolRemaining,
			'site_remaining' => $this->siteRemaining,
			'pool_breakdown' => $this->poolBreakdown,
		];
	}
}
