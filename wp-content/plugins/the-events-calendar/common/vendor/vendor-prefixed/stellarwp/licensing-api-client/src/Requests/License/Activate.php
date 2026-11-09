<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License;

/**
 * Represents a license activation request payload.
 *
 * @phpstan-type ActivatePayload array{
 *     license_key: string,
 *     product_slug: string,
 *     tier: string,
 *     domain: string
 * }
 */
final class Activate
{
	/**
	 * License key being activated.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Product identifier to activate.
	 *
	 * @example plugin-pro
	 */
	public string $productSlug;

	/**
	 * Entitlement tier being activated.
	 *
	 * @example pro
	 */
	public string $tier;

	/**
	 * Site domain where the license is being activated.
	 *
	 * @example example.com
	 */
	public string $domain;

	public function __construct(string $licenseKey, string $productSlug, string $tier, string $domain) {
		$this->licenseKey  = $licenseKey;
		$this->productSlug = $productSlug;
		$this->tier        = $tier;
		$this->domain      = $domain;
	}

	/**
	 * @return ActivatePayload
	 */
	public function toArray(): array {
		return [
			'license_key'  => $this->licenseKey,
			'product_slug' => $this->productSlug,
			'tier'         => $this->tier,
			'domain'       => $this->domain,
		];
	}
}
