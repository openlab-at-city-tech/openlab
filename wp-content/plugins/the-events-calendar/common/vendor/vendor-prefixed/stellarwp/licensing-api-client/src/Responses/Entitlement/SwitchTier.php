<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Entitlement;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents an entitlement tier switch response payload.
 *
 * @implements Response<array{
 *     product_slug: string,
 *     from_tier: string,
 *     to_tier: string,
 *     status: string,
 *     site_limit: int,
 *     active_count: int,
 *     over_limit: bool
 * }>
 */
final class SwitchTier implements Response
{
	public string $productSlug;

	public string $fromTier;

	public string $toTier;

	public string $status;

	public int $siteLimit;

	public int $activeCount;

	public bool $overLimit;

	private function __construct(
		string $productSlug,
		string $fromTier,
		string $toTier,
		string $status,
		int $siteLimit,
		int $activeCount,
		bool $overLimit
	) {
		$this->productSlug = $productSlug;
		$this->fromTier    = $fromTier;
		$this->toTier      = $toTier;
		$this->status      = $status;
		$this->siteLimit   = $siteLimit;
		$this->activeCount = $activeCount;
		$this->overLimit   = $overLimit;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     from_tier: string,
	 *     to_tier: string,
	 *     status: string,
	 *     site_limit: int,
	 *     active_count: int,
	 *     over_limit: bool
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['product_slug'],
			$attributes['from_tier'],
			$attributes['to_tier'],
			$attributes['status'],
			$attributes['site_limit'],
			$attributes['active_count'],
			$attributes['over_limit']
		);
	}

	public function toArray(): array {
		return [
			'product_slug' => $this->productSlug,
			'from_tier'    => $this->fromTier,
			'to_tier'      => $this->toTier,
			'status'       => $this->status,
			'site_limit'   => $this->siteLimit,
			'active_count' => $this->activeCount,
			'over_limit'   => $this->overLimit,
		];
	}
}
