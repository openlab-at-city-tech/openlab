<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Listing\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\ValueObjects\ImportedAlias;

/**
 * Represents a single license in a cursor-paginated listing.
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
 *     license_key: string,
 *     identity_id: string,
 *     status: string,
 *     created_at: string,
 *     updated_at: string,
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
 *         }
 *     }>,
 *     aliases: list<array{alias_key: string, product_slug: string|null}>
 * }>
 */
final class LicenseListItem implements Response
{
	use InteractsWithDateTime;

	public string $licenseKey;

	public string $identityId;

	public string $status;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $updatedAt;

	public ListedProductCollection $products;

	/**
	 * @var ImportedAlias[]
	 */
	public array $aliases;

	/**
	 * @param ImportedAlias[] $aliases
	 */
	private function __construct(
		string $licenseKey,
		string $identityId,
		string $status,
		DateTimeImmutable $createdAt,
		DateTimeImmutable $updatedAt,
		ListedProductCollection $products,
		array $aliases
	) {
		$this->licenseKey = $licenseKey;
		$this->identityId = $identityId;
		$this->status     = $status;
		$this->createdAt  = $createdAt;
		$this->updatedAt  = $updatedAt;
		$this->products   = $products;
		$this->aliases    = $aliases;
	}

	/**
	 * @param array{
	 *     license_key: string,
	 *     identity_id: string,
	 *     status: string,
	 *     created_at: string,
	 *     updated_at: string,
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
	 *         }
	 *     }>,
	 *     aliases: list<array{alias_key: string, product_slug?: string|null}>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['license_key'],
			$attributes['identity_id'],
			$attributes['status'],
			self::parseDateTime($attributes['created_at']),
			self::parseDateTime($attributes['updated_at']),
			ListedProductCollection::from($attributes['products']),
			array_map(
				static fn (array $alias): ImportedAlias => ImportedAlias::from($alias),
				$attributes['aliases']
			)
		);
	}

	/**
	 * @return array{
	 *     license_key: string,
	 *     identity_id: string,
	 *     status: string,
	 *     created_at: string,
	 *     updated_at: string,
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
	 *         }
	 *     }>,
	 *     aliases: list<array{alias_key: string, product_slug: string|null}>
	 * }
	 */
	public function toArray(): array {
		return [
			'license_key' => $this->licenseKey,
			'identity_id' => $this->identityId,
			'status'      => $this->status,
			'created_at'  => $this->formatDateTime($this->createdAt),
			'updated_at'  => $this->formatDateTime($this->updatedAt),
			'products'    => $this->products->toArray(),
			'aliases'     => array_map(
				static fn (ImportedAlias $alias): array => $alias->toArray(),
				$this->aliases
			),
		];
	}
}
