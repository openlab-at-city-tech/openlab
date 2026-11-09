<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License;

/**
 * Represents a license activation deletion request payload.
 *
 * @phpstan-type DeleteActivationPayload array{
 *     license_key: string,
 *     product_slug: string,
 *     domain: string
 * }
 */
final class DeleteActivation
{
	/**
	 * License key whose activation is being deleted.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Product identifier whose activation is being deleted.
	 *
	 * @example plugin-pro
	 */
	public string $productSlug;

	/**
	 * Site domain whose activation is being deleted.
	 *
	 * @example example.com
	 */
	public string $domain;

	public function __construct(
		string $licenseKey,
		string $productSlug,
		string $domain
	) {
		$this->licenseKey  = $licenseKey;
		$this->productSlug = $productSlug;
		$this->domain      = $domain;
	}

	/**
	 * @return DeleteActivationPayload
	 */
	public function toArray(): array {
		return [
			'license_key'  => $this->licenseKey,
			'product_slug' => $this->productSlug,
			'domain'       => $this->domain,
		];
	}
}
