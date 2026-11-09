<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects\LicenseSummary;

/**
 * Represents the batch license validation response payload.
 *
 * @implements Response<array{
 *     license: array{license_key: string, status: string}|null,
 *     domain: string,
 *     is_production: bool,
 *     products: list<array{
 *         product_slug: string,
 *         status: string,
 *         is_valid: bool,
 *         entitlement: array{
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             available: int,
 *             over_limit: bool,
 *             excess_activations: int,
 *             expiration_date: string,
 *             status: string,
 *             capabilities: list<string>
 *         }|null,
 *         activation: array{
 *             domain: string,
 *             activated_at: string
 *         }|null,
 *         available_entitlements: list<array{
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             available: int,
 *             over_limit: bool,
 *             excess_activations: int,
 *             capabilities: list<string>,
 *             status: string,
 *             expires: string
 *         }>
 *     }>
 * }>
 */
final class Validate implements Response
{
	public ?LicenseSummary $license;

	public string $domain;

	public bool $isProduction;

	public ValidatedProductCollection $products;

	/**
	 */
	private function __construct(
		?LicenseSummary $license,
		string $domain,
		bool $isProduction,
		ValidatedProductCollection $products
	) {
		$this->license      = $license;
		$this->domain       = $domain;
		$this->isProduction = $isProduction;
		$this->products     = $products;
	}

	/**
	 * @param array{
	 *     license: array{license_key: string, status: string}|null,
	 *     domain: string,
	 *     is_production: bool,
	 *     products: list<array{
	 *         product_slug: string,
	 *         status: string,
	 *         is_valid: bool,
	 *         entitlement: array{
	 *             tier: string,
	 *             site_limit: int,
	 *             active_count: int,
	 *             available: int,
	 *             over_limit: bool,
	 *             excess_activations: int,
	 *             expiration_date: string,
	 *             status: string,
	 *             capabilities: list<string>
	 *         }|null,
	 *         activation: array{
	 *             domain: string,
	 *             activated_at: string
	 *         }|null,
	 *         available_entitlements?: list<array{
	 *             tier: string,
	 *             site_limit: int,
	 *             active_count: int,
	 *             available: int,
	 *             over_limit: bool,
	 *             excess_activations: int,
	 *             capabilities: list<string>,
	 *             status: string,
	 *             expires: string
	 *         }>
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$license = $attributes['license'] ?? null;

		return new self(
			$license ? LicenseSummary::from($license) : null,
			$attributes['domain'],
			$attributes['is_production'],
			ValidatedProductCollection::from($attributes['products'])
		);
	}

	public function toArray(): array {
		return [
			'license'       => $this->license ? $this->license->toArray() : null,
			'domain'        => $this->domain,
			'is_production' => $this->isProduction,
			'products'      => $this->products->toArray(),
		];
	}
}
