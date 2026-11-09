<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Entitlement;

use TEC\Common\LiquidWeb\LicensingApiClient\Requests\Entitlement\ValueObjects\UpsertProduct;

/**
 * Represents an upsert entitlements request payload.
 *
 * @phpstan-import-type UpsertProductPayload from UpsertProduct
 * @phpstan-type UpsertPayload array{
 *     identity_id: string,
 *     license_key?: string,
 *     products: list<UpsertProductPayload>
 * }
 */
final class Upsert
{
	public string $identityId;

	public ?string $licenseKey;

	/**
	 * @var UpsertProduct[]
	 */
	public array $products;

	/**
	 * @param UpsertProduct[] $products
	 */
	public function __construct(string $identityId, array $products, ?string $licenseKey = null) {
		$this->identityId = $identityId;
		$this->products   = $products;
		$this->licenseKey = $licenseKey;
	}

	/**
	 * @return UpsertPayload
	 */
	public function toArray(): array {
		return array_filter([
			'identity_id' => $this->identityId,
			'license_key' => $this->licenseKey,
			'products'    => array_map(
				static fn (UpsertProduct $product): array => $product->toArray(),
				$this->products
			),
		], static fn ($value): bool => $value !== null);
	}
}
