<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\BalanceEntry;

/**
 * Represents the credit balance response across one or more credit types.
 *
 * @implements Response<array{
 *     credits: list<array{
 *         credit_type: string,
 *         remaining: int,
 *         site_quota: int|null,
 *         site_used: int,
 *         site_remaining: int,
 *         aggregate_total: int,
 *         aggregate_used: int,
 *         aggregate_remaining: int,
 *         aggregate_overage: int,
 *         pools: list<array{
 *             pool_id: int,
 *             pool_remaining: int,
 *             priority: int,
 *             period: string,
 *             resets_on: string|null,
 *             expires_at: string|null,
 *             credits_total?: int,
 *             credits_used?: int,
 *             overage?: int,
 *             overage_limit?: int|null
 *         }>
 *     }>
 * }>
 */
final class BalanceCollection implements Response
{
	/** @var list<BalanceEntry> */
	public array $credits;

	/**
	 * @param list<BalanceEntry> $credits
	 */
	private function __construct(array $credits) {
		$this->credits = $credits;
	}

	/**
	 * @param array{
	 *     credits: list<array{
	 *         credit_type: string,
	 *         remaining: int,
	 *         site_quota: int|null,
	 *         site_used: int,
	 *         site_remaining: int,
	 *         aggregate_total: int,
	 *         aggregate_used: int,
	 *         aggregate_remaining: int,
	 *         aggregate_overage: int,
	 *         pools: list<array{
	 *             pool_id: int,
	 *             pool_remaining: int,
	 *             priority: int,
	 *             period: string,
	 *             resets_on: string|null,
	 *             expires_at: string|null,
	 *             credits_total?: int,
	 *             credits_used?: int,
	 *             overage?: int,
	 *             overage_limit?: int|null
	 *         }>
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(array_map(
			static fn (array $credit): BalanceEntry => BalanceEntry::from($credit),
			$attributes['credits']
		));
	}

	public function toArray(): array {
		return [
			'credits' => array_map(
				static fn (BalanceEntry $credit): array => $credit->toArray(),
				$this->credits
			),
		];
	}
}
