<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

/**
 * Represents a credit refund write request payload.
 *
 * @phpstan-type RefundPayload array{
 *     license_key: string,
 *     domain: string,
 *     product_slug: string,
 *     credit_type: string,
 *     pool_id: int,
 *     credits_refunded: int
 * }
 */
final class Refund
{
	/**
	 * License key to refund credits against.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Site domain receiving the refund.
	 *
	 * @example example.com
	 */
	public string $domain;

	/**
	 * Product identifier responsible for the refund.
	 *
	 * @example plugin-pro
	 */
	public string $productSlug;

	/**
	 * Credit type being refunded.
	 *
	 * @example ai
	 */
	public string $creditType;

	/**
	 * Pool ID the refund should be applied to.
	 *
	 * @example 42
	 */
	public int $poolId;

	/**
	 * Number of credits to refund.
	 *
	 * @example 5
	 */
	public int $creditsRefunded;

	/**
	 * Idempotency key forwarded as the X-Idempotency-Key header.
	 *
	 * @example request_456
	 */
	public string $idempotencyKey;

	public function __construct(
		string $licenseKey,
		string $domain,
		string $productSlug,
		string $creditType,
		int $poolId,
		int $creditsRefunded,
		string $idempotencyKey
	) {
		$this->licenseKey      = $licenseKey;
		$this->domain          = $domain;
		$this->productSlug     = $productSlug;
		$this->creditType      = $creditType;
		$this->poolId          = $poolId;
		$this->creditsRefunded = $creditsRefunded;
		$this->idempotencyKey  = $idempotencyKey;
	}

	/**
	 * @return RefundPayload
	 */
	public function toArray(): array {
		return [
			'license_key'      => $this->licenseKey,
			'domain'           => $this->domain,
			'product_slug'     => $this->productSlug,
			'credit_type'      => $this->creditType,
			'pool_id'          => $this->poolId,
			'credits_refunded' => $this->creditsRefunded,
		];
	}
}
