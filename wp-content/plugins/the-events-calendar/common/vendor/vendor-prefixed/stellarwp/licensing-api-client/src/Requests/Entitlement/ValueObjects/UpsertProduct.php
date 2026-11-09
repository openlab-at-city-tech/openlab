<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Entitlement\ValueObjects;

/**
 * Represents one entitlement mutation inside an upsert request.
 *
 * @phpstan-type MetadataValue string|int|float|bool|null|array<int, string|int|float|bool|null>
 * @phpstan-type MetadataMap array<string, MetadataValue>
 * @phpstan-type UpsertProductPayload array{
 *     product_slug: string,
 *     tier?: string,
 *     site_limit?: int,
 *     expiration_date?: string,
 *     purchase_date?: string,
 *     status?: string,
 *     grants?: MetadataMap,
 *     metadata?: MetadataMap
 * }
 */
final class UpsertProduct
{
	public string $productSlug;

	public ?string $tier;

	/**
	 * Maximum allowed site activations for this entitlement.
	 */
	public ?int $siteLimit;

	/**
	 * Entitlement expiration date in a format accepted by the API, including RFC3339 UTC `Z`, RFC3339 with an explicit offset, or MySQL DATETIME.
	 */
	public ?string $expirationDate;

	/**
	 * Purchase date in a format accepted by the API, including RFC3339 UTC `Z`, RFC3339 with an explicit offset, or MySQL DATETIME.
	 */
	public ?string $purchaseDate;

	/**
	 * Target entitlement status, e.g. `active`, `suspended`, or `cancelled`.
	 */
	public ?string $status;

	/**
	 * Grants payload stored by the API, typically including a `capabilities` list.
	 *
	 * @var MetadataMap|null
	 */
	public ?array $grants;

	/**
	 * Arbitrary metadata persisted with the entitlement.
	 *
	 * @var MetadataMap|null
	 */
	public ?array $metadata;

	/**
	 * @param MetadataMap|null $grants
	 * @param MetadataMap|null $metadata
	 */
	public function __construct(
		string $productSlug,
		?string $tier = null,
		?int $siteLimit = null,
		?string $expirationDate = null,
		?string $purchaseDate = null,
		?string $status = null,
		?array $grants = null,
		?array $metadata = null
	) {
		$this->productSlug    = $productSlug;
		$this->tier           = $tier;
		$this->siteLimit      = $siteLimit;
		$this->expirationDate = $expirationDate;
		$this->purchaseDate   = $purchaseDate;
		$this->status         = $status;
		$this->grants         = $grants;
		$this->metadata       = $metadata;
	}

	/**
	 * @return UpsertProductPayload
	 */
	public function toArray(): array {
		return array_filter([
			'product_slug'    => $this->productSlug,
			'tier'            => $this->tier,
			'site_limit'      => $this->siteLimit,
			'expiration_date' => $this->expirationDate,
			'purchase_date'   => $this->purchaseDate,
			'status'          => $this->status,
			'grants'          => $this->grants,
			'metadata'        => $this->metadata,
		], static fn ($value): bool => $value !== null);
	}
}
