<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a successful credit refund response.
 *
 * @implements Response<array{
 *     credits_refunded: int,
 *     pool_remaining: int,
 *     site_remaining: int|null,
 *     pool_breakdown: array<array-key, int>
 * }>
 */
final class Refund implements Response
{
	public int $creditsRefunded;

	public int $poolRemaining;

	public ?int $siteRemaining;

	/**
	 * @var array<int, int>
	 */
	public array $poolBreakdown;

	/**
	 * @param array<int, int> $poolBreakdown
	 */
	private function __construct(int $creditsRefunded, int $poolRemaining, ?int $siteRemaining, array $poolBreakdown) {
		$this->creditsRefunded = $creditsRefunded;
		$this->poolRemaining   = $poolRemaining;
		$this->siteRemaining   = $siteRemaining;
		$this->poolBreakdown   = $poolBreakdown;
	}

	/**
	 * @param array{
	 *     credits_refunded: int,
	 *     pool_remaining: int,
	 *     site_remaining: int|null,
	 *     pool_breakdown: array<array-key, int>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$poolBreakdown = [];

		foreach ($attributes['pool_breakdown'] as $poolId => $creditsRefunded) {
			$poolBreakdown[(int) $poolId] = $creditsRefunded;
		}

		return new self(
			$attributes['credits_refunded'],
			$attributes['pool_remaining'],
			$attributes['site_remaining'],
			$poolBreakdown
		);
	}

	/**
	 * @return array{
	 *     credits_refunded: int,
	 *     pool_remaining: int,
	 *     site_remaining: int|null,
	 *     pool_breakdown: array<int, int>
	 * }
	 */
	public function toArray(): array {
		return [
			'credits_refunded' => $this->creditsRefunded,
			'pool_remaining'   => $this->poolRemaining,
			'site_remaining'   => $this->siteRemaining,
			'pool_breakdown'   => $this->poolBreakdown,
		];
	}
}
