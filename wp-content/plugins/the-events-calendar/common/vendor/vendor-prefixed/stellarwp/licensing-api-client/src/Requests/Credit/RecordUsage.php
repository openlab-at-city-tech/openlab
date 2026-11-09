<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

/**
 * Represents a credit usage write request payload.
 *
 * @phpstan-type RecordUsagePayload array{
 *     license_key: string,
 *     domain: string,
 *     product_slug: string,
 *     credit_type: string,
 *     credits_used: int
 * }
 */
final class RecordUsage
{
	/**
	 * License key to record usage against.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Site domain consuming the credits.
	 *
	 * @example example.com
	 */
	public string $domain;

	/**
	 * Product identifier responsible for the usage.
	 *
	 * @example plugin-pro
	 */
	public string $productSlug;

	/**
	 * Credit type being consumed.
	 *
	 * @example ai
	 */
	public string $creditType;

	/**
	 * Number of credits to consume.
	 *
	 * @example 10
	 */
	public int $creditsUsed;

	/**
	 * Idempotency key forwarded as the X-Idempotency-Key header.
	 *
	 * @example request_123
	 */
	public string $idempotencyKey;

	public function __construct(
		string $licenseKey,
		string $domain,
		string $productSlug,
		string $creditType,
		int $creditsUsed,
		string $idempotencyKey
	) {
		$this->licenseKey     = $licenseKey;
		$this->domain         = $domain;
		$this->productSlug    = $productSlug;
		$this->creditType     = $creditType;
		$this->creditsUsed    = $creditsUsed;
		$this->idempotencyKey = $idempotencyKey;
	}

	/**
	 * @return RecordUsagePayload
	 */
	public function toArray(): array {
		return [
			'license_key'  => $this->licenseKey,
			'domain'       => $this->domain,
			'product_slug' => $this->productSlug,
			'credit_type'  => $this->creditType,
			'credits_used' => $this->creditsUsed,
		];
	}
}
