<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Entitlement;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a cancelled entitlement response payload.
 *
 * @implements Response<array{
 *     product_slug: string,
 *     tier: string,
 *     status: string
 * }>
 */
final class Cancel implements Response
{
	public string $productSlug;

	public string $tier;

	public string $status;

	private function __construct(string $productSlug, string $tier, string $status) {
		$this->productSlug = $productSlug;
		$this->tier        = $tier;
		$this->status      = $status;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     tier: string,
	 *     status: string
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['product_slug'],
			$attributes['tier'],
			$attributes['status']
		);
	}

	public function toArray(): array {
		return [
			'product_slug' => $this->productSlug,
			'tier'         => $this->tier,
			'status'       => $this->status,
		];
	}
}
