<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Entitlement;

/**
 * Represents a switch entitlement tier request payload.
 *
 * @phpstan-type MetadataValue string|int|float|bool|null|array<int, string|int|float|bool|null>
 * @phpstan-type MetadataMap array<string, MetadataValue>
 * @phpstan-type SwitchTierPayload array{
 *     license_key: string,
 *     product_slug: string,
 *     from_tier: string,
 *     to_tier: string,
 *     site_limit?: int,
 *     expiration_date?: string,
 *     purchase_date?: string,
 *     grants?: MetadataMap,
 *     metadata?: MetadataMap
 * }
 */
final class SwitchTier
{
	public string $licenseKey;

	public string $productSlug;

	public string $fromTier;

	public string $toTier;

	public ?int $siteLimit;

	public ?string $expirationDate;

	public ?string $purchaseDate;

	/**
	 * @var MetadataMap|null
	 */
	public ?array $grants;

	/**
	 * @var MetadataMap|null
	 */
	public ?array $metadata;

	/**
	 * @param MetadataMap|null $grants
	 * @param MetadataMap|null $metadata
	 */
	public function __construct(
		string $licenseKey,
		string $productSlug,
		string $fromTier,
		string $toTier,
		?int $siteLimit = null,
		?string $expirationDate = null,
		?string $purchaseDate = null,
		?array $grants = null,
		?array $metadata = null
	) {
		$this->licenseKey     = $licenseKey;
		$this->productSlug    = $productSlug;
		$this->fromTier       = $fromTier;
		$this->toTier         = $toTier;
		$this->siteLimit      = $siteLimit;
		$this->expirationDate = $expirationDate;
		$this->purchaseDate   = $purchaseDate;
		$this->grants         = $grants;
		$this->metadata       = $metadata;
	}

	/**
	 * @return SwitchTierPayload
	 */
	public function toArray(): array {
		return array_filter([
			'license_key'     => $this->licenseKey,
			'product_slug'    => $this->productSlug,
			'from_tier'       => $this->fromTier,
			'to_tier'         => $this->toTier,
			'site_limit'      => $this->siteLimit,
			'expiration_date' => $this->expirationDate,
			'purchase_date'   => $this->purchaseDate,
			'grants'          => $this->grants,
			'metadata'        => $this->metadata,
		], static fn ($value): bool => $value !== null);
	}
}
