<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents the product catalog response payload.
 *
 * @phpstan-type ActivationDomainPayload array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool,
 *     is_production: bool
 * }
 * @phpstan-type ActivationDomainsPayload array<string, ActivationDomainPayload>
 *
 * @implements Response<array{
 *     products: list<array{
 *         product_slug: string,
 *         tier: string,
 *         status: string,
 *         expires: string,
 *         capabilities: list<string>,
 *         activations: array{
 *             site_limit: int,
 *             active_count: int,
 *             over_limit: bool,
 *             excess_activations: int,
 *             domains: ActivationDomainsPayload
 *         },
 *         activated_here?: bool,
 *         validation_status?: string,
 *         is_valid?: bool
 *     }>
 * }>
 */
final class Catalog implements Response
{
	public CatalogProductCollection $products;

	private function __construct(CatalogProductCollection $products) {
		$this->products = $products;
	}

	/**
	 * @param array{
	 *     products: list<array{
	 *         product_slug: string,
	 *         tier: string,
	 *         status: string,
	 *         expires: string,
	 *         capabilities: list<string>,
	 *         activations: array{
	 *             site_limit: int,
	 *             active_count: int,
	 *             over_limit: bool,
	 *             excess_activations: int,
	 *             domains: ActivationDomainsPayload
	 *         },
	 *         activated_here?: bool,
	 *         validation_status?: string,
	 *         is_valid?: bool
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(CatalogProductCollection::from($attributes['products']));
	}

	public function toArray(): array {
		return [
			'products' => $this->products->toArray(),
		];
	}
}
