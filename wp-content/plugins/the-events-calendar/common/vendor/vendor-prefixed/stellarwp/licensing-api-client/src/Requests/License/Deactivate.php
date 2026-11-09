<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License;

/**
 * Represents a license deactivation request payload.
 *
 * @phpstan-type DeactivatePayload array{
 *     license_key: string,
 *     product_slug: string,
 *     domain: string
 * }
 */
final class Deactivate
{
	/**
	 * License key being deactivated.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Product identifier to deactivate.
	 *
	 * @example plugin-pro
	 */
	public string $productSlug;

	/**
	 * Site domain where the license is being deactivated.
	 *
	 * @example example.com
	 */
	public string $domain;

	public function __construct(string $licenseKey, string $productSlug, string $domain) {
		$this->licenseKey  = $licenseKey;
		$this->productSlug = $productSlug;
		$this->domain      = $domain;
	}

	/**
	 * @return DeactivatePayload
	 */
	public function toArray(): array {
		return [
			'license_key'  => $this->licenseKey,
			'product_slug' => $this->productSlug,
			'domain'       => $this->domain,
		];
	}
}
