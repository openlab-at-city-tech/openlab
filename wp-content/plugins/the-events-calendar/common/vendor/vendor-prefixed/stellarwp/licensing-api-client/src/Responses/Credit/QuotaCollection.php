<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\SiteQuota;

/**
 * Represents a collection of site quotas.
 *
 * @implements Response<array{
 *     quotas: list<array{
 *         domain: string,
 *         credit_type: string,
 *         quota: ?int,
 *         period: string,
 *         first_period_start: ?string,
 *         is_blocked: bool,
 *         is_uncapped: bool
 *     }>
 * }>
 */
final class QuotaCollection implements Response
{
	/**
	 * @var list<SiteQuota>
	 */
	public array $quotas;

	/**
	 * @param list<SiteQuota> $quotas
	 */
	private function __construct(array $quotas) {
		$this->quotas = $quotas;
	}

	/**
	 * @param array{
	 *     quotas: list<array{
	 *         domain: string,
	 *         credit_type: string,
	 *         quota: ?int,
	 *         period: string,
	 *         first_period_start: ?string,
	 *         is_blocked: bool,
	 *         is_uncapped: bool
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(array_map(
			static fn (array $quota): SiteQuota => SiteQuota::from($quota),
			$attributes['quotas']
		));
	}

	/**
	 * @return array{
	 *     quotas: list<array{
	 *         domain: string,
	 *         credit_type: string,
	 *         quota: ?int,
	 *         period: string,
	 *         first_period_start: ?string,
	 *         is_blocked: bool,
	 *         is_uncapped: bool
	 *     }>
	 * }
	 */
	public function toArray(): array {
		return [
			'quotas' => array_map(
				static fn (SiteQuota $quota): array => $quota->toArray(),
				$this->quotas
			),
		];
	}
}
